<?php
namespace App\Controllers;

use App\Repositories\RentalRepository;
use App\Core\Interfaces\RequestInterface;
use App\Core\Response;
use App\Services\JwtService;
use App\Services\CookieAuthService;
use App\Services\RentalService;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\RentalAlreadyExistsException;
use App\Traits\JwtAuthenticationTrait;

class RentalController {
    use JwtAuthenticationTrait;
    
    private $rentalRepository;
    private $request;
    private $jwtService;
    private $cookieAuthService;
    private $rentalService; // Add service dependency

    public function __construct(RentalRepository $rentalRepository, RequestInterface $request, JwtService $jwtService, CookieAuthService $cookieAuthService, RentalService $rentalService) {
        $this->rentalRepository = $rentalRepository;
        $this->request = $request;        
        $this->jwtService = $jwtService;
        $this->cookieAuthService = $cookieAuthService;
        $this->rentalService = $rentalService;
    }

    protected function getRequest() {
        return $this->request;
    }

    public function getRentalById($id) {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $rental = $this->rentalService->getRentalById($id);
        if (empty($rental)) {
            return new Response(404, json_encode(['error' => 'Rental not found']));
        }
        return new Response(200, json_encode($rental[0]));
    }

    public function showRentalsPage() {
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) return $auth;
        $user = $this->cookieAuthService->getAuthenticatedUser();

        $page = (int)$this->request->getQueryParam('page', 1);
        $page = max(1, $page);
        $itemsPerPage = 10;

        $filters = [
            'status' => $this->request->getQueryParam('filter_status', ''),
            'brand' => $this->request->getQueryParam('filter_brand', ''),
            'rate' => $this->request->getQueryParam('filter_rate', '')
        ];

        $hasFilter = !empty($filters['status']) || !empty($filters['brand']) || !empty($filters['rate']);
        if ($hasFilter) {
            $result = $this->rentalRepository->getFilteredPaginated($filters, $page, $itemsPerPage);
        } else {
            $result = $this->rentalRepository->getAllPaginated($page, $itemsPerPage);
        }
        
        $rentals = $result['rentals'];
        $total = $result['total'];
        $totalPages = max(1, ceil($total / $itemsPerPage));

        $brands = $this->rentalRepository->getAllBrands();
        extract([
            'rentals' => $rentals,
            'page' => $page,
            'totalPages' => $totalPages,
            'user' => $user,
            'brands' => $brands
        ]);
        ob_start();
        include __DIR__ . '/../Views/rentals/dashboard.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
    
    public function showCreateForm($error = '', $success = '', $data = []) {
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) return $auth;
        ob_start();
        include __DIR__ . '/../Views/rentals/create.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function processCreate() {
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) return $auth;
        $data = $this->request->getBody();
        $error = '';
        $success = '';
        try {
            $this->rentalService->createRental($data);
            $success = 'Rental created successfully.';
            $data = [];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        return $this->showCreateForm($error, $success, $data);
    }

    public function showEditForm($id) {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $error = '';
        $success = '';
        $rental = null;
        $rental = $this->rentalService->getRentalById($id);

        if (!$rental) {
            return new Response(404, 'Rental not found', ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
        ob_start();
        include __DIR__ . '/../Views/rentals/edit.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function processUpdate($id) {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $data = $this->request->getBody();
        $error = '';
        $success = '';
        $rental = null;

        try {
            $success = $this->rentalService->updateRental($id, $data);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        $rental = $this->rentalService->getRentalById($id);

        ob_start();
        include __DIR__ . '/../Views/rentals/edit.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function showDeleteForm($id) {
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) return $auth;

        $error = '';
        $rental = null;
        try {
            $rental = $this->rentalService->getRentalById($id);
            if (!$rental) {
                $error = 'Rental not found.';
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        ob_start();
        include __DIR__ . '/../Views/rentals/delete.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function processDelete($id) {
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) return $auth;

        $error = '';
        $rental = null;
        try {
            $this->rentalService->deleteRental($id);
            return new Response(302, '', ['Location' => '/rentals']);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            // Try to fetch the rental again for the view (may be null)
            $rental = $this->rentalService->getRentalById($id);
        }
        ob_start();
        include __DIR__ . '/../Views/rentals/delete.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    private function preparePaginatedRentalsViewData(int $currentPage) {
        $user = $this->cookieAuthService->getAuthenticatedUser();
        $itemsPerPage = 10; 
        
        $result = $this->rentalRepository->getAllPaginated($currentPage, $itemsPerPage);
        $rentals = $result['rentals'];
        $total = $result['total'];
        $totalPages = max(1, ceil($total / $itemsPerPage));

        return [
            'rentals' => $rentals,
            'page' => $currentPage,
            'totalPages' => $totalPages,
            'user' => $user
        ];
    }
}

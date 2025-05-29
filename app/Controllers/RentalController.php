<?php
namespace App\Controllers;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\RequestInterface;
use App\Core\Response;
use App\Services\JwtService;
use App\Exceptions\InvalidCredentialsException;
use App\Traits\JwtAuthenticationTrait;

class RentalController {
    use JwtAuthenticationTrait;
    
    private $rentalRepository;
    private $request;
    private $jwtService;
    private $cookieAuthService;

    public function __construct(DataRepositoryInterface $rentalRepository, RequestInterface $request, JwtService $jwtService) {
        $this->rentalRepository = $rentalRepository;
        $this->request = $request;        
        $this->jwtService = $jwtService;
        $this->cookieAuthService = new \App\Services\CookieAuthService($jwtService);
    }

    private function preparePaginatedRentalsViewData(int $currentPage) {
        $user = $this->cookieAuthService->getAuthenticatedUser();
        $itemsPerPage = 10; // This could be a class constant or configurable
        
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

        public function getAllRentals() {
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) return $auth;
        $user = $this->cookieAuthService->getAuthenticatedUser(); // Get user data

        $page = (int)$this->request->getQueryParam('page', 1);
        $page = max(1, $page); // Ensure page is at least 1
        $itemsPerPage = 10;

        $result = $this->rentalRepository->getAllPaginated($page, $itemsPerPage);
        $rentals = $result['rentals'];
        $total = $result['total'];
        $totalPages = max(1, ceil($total / $itemsPerPage));

        ob_start();
        // Pass all necessary variables to the view
        include __DIR__ . '/../Views/rentals/dashboard.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function getRentalById($id) {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $rental = $this->rentalRepository->getById($id);
        if (empty($rental)) {
            return new Response(404, json_encode(['error' => 'Rental not found']));
        }
        return new Response(200, json_encode($rental[0]));
    }
    
    public function createRental() {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $data = $this->request->getBody();
        $this->rentalRepository->create($data);
        return new Response(201, json_encode(['message' => 'Rental created']));
    }

    public function updateRental($id) {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $data = $this->request->getBody();
        $this->rentalRepository->update($id, $data);
        return new Response(200, json_encode(['message' => 'Rental updated']));
    }

    public function deleteRental($id) {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $this->rentalRepository->delete($id);
        return new Response(204, '');
    }

    public function showRentalsPage() {
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) return $auth;
        $user = $this->cookieAuthService->getAuthenticatedUser(); // Get user data

        $page = (int)$this->request->getQueryParam('page', 1);
        $page = max(1, $page); // Ensure page is at least 1
        $itemsPerPage = 10;

        $result = $this->rentalRepository->getAllPaginated($page, $itemsPerPage);
        $rentals = $result['rentals'];
        $total = $result['total'];
        $totalPages = max(1, ceil($total / $itemsPerPage));

        ob_start();
        // Pass all necessary variables to the view
        include __DIR__ . '/../Views/rentals/dashboard.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
    
    public function showCreateForm() {
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) return $auth;
        $error = '';
        $success = '';
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
        // Basic validation
        if (empty($data['car_brand']) || empty($data['car_model']) || empty($data['car_license_plate']) || empty($data['car_daily_rate'])) {
            $error = 'All fields except status are required.';
        } else {
            try {
                $this->rentalRepository->create($data);
                // Redirect to rentals dashboard after successful creation
                return new Response(302, '', ['Location' => '/rentals']);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        ob_start();
        include __DIR__ . '/../Views/rentals/create.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}

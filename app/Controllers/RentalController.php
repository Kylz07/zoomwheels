<?php
namespace App\Controllers;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\RequestInterface;
use App\Core\Response;
use App\Services\JwtService;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\RentalAlreadyExistsException;
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
        $user = $this->cookieAuthService->getAuthenticatedUser();
        if (!$user) {
            return new Response(401, 'Unauthorized', ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
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
                $success = 'Rental created successfully.';
                $data = [];
            } catch (RentalAlreadyExistsException $e) {
                $error = $e->getMessage();
            } catch (\Exception $e) {
                $error = "Failed to create rental: " . $e->getMessage();
            }
        }
        ob_start();
        include __DIR__ . '/../Views/rentals/create.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function showEditForm($id) {
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) return $auth;
        $error = '';
        $success = '';
        $rental = null;
        $result = $this->rentalRepository->getById($id);
        if (empty($result)) {
            // Use the same error response pattern as getRentalById for consistency and testability
            return new Response(404, 'Rental not found', ['Content-Type' => 'text/plain; charset=UTF-8']);
        } else {
            $rental = $result[0];
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
        $updateData = [];
        if (isset($data['car_daily_rate'])) {
            $updateData['car_daily_rate'] = $data['car_daily_rate'];
        }
        if (isset($data['rental_status'])) {
            $updateData['rental_status'] = $data['rental_status'];
        }
        if (empty($updateData)) {
            $error = 'No valid fields provided for update.';
        } else {
            try {
                // Only attempt update if values are actually different from current
                $current = $this->rentalRepository->getById($id);
                if (!empty($current)) {
                    $currentRental = $current[0];
                    $changed = false;
                    foreach ($updateData as $field => $value) {
                        if (!isset($currentRental[$field]) || $currentRental[$field] != $value) {
                            $changed = true;
                            break;
                        }
                    }
                    if ($changed) {
                        $this->rentalRepository->update($id, $updateData);
                        $success = 'Rental updated successfully.';
                    } else {
                        $success = 'No changes detected.';
                    }
                } else {
                    $error = 'Rental not found.';
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        $result = $this->rentalRepository->getById($id);
        $rental = !empty($result) ? $result[0] : null;
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
        $result = $this->rentalRepository->getById($id);
        if (empty($result)) {
            return new Response(404, 'Rental not found', ['Content-Type' => 'text/plain; charset=UTF-8']);
        } else {
            $rental = $result[0];
        }
        ob_start();
        include __DIR__ . '/../Views/rentals/delete.php';
        $html = ob_get_clean();
        return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function processDelete($id) {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $error = '';
        $result = $this->rentalRepository->getById($id);
        if (empty($result)) {
            return new Response(404, 'Rental not found', ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
        try {
            $this->rentalRepository->delete($id); // Hard delete
            return new Response(302, '', ['Location' => '/rentals']);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $rental = $result[0];
            ob_start();
            include __DIR__ . '/../Views/rentals/delete.php';
            $html = ob_get_clean();
            return new Response(200, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
        }
    }
}

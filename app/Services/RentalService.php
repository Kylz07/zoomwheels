<?php
namespace App\Services;

use App\Repositories\RentalRepository;
use Exception;

class RentalService {
    private $rentalRepository;
    public function __construct(RentalRepository $rentalRepository) {
        $this->rentalRepository = $rentalRepository;
    }

    public function createRental($data) {
        // Validation
        if (empty($data['car_brand']) || empty($data['car_model']) || empty($data['car_license_plate']) || empty($data['car_daily_rate'])) {
            throw new Exception('All fields except status are required.');
        }
        // Uniqueness check
        $existing = $this->rentalRepository->getByLicensePlate($data['car_license_plate']);
        if (!empty($existing)) {
            throw new Exception('A rental with this license plate already exists.');
        }
        // Set default status if not provided
        if (empty($data['rental_status'])) {
            $data['rental_status'] = 'available';
        }
        // Call repository to insert
        return $this->rentalRepository->create($data);
    }
}
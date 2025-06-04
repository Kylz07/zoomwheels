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
        if (empty($data['car_brand']) || empty($data['car_model']) || empty($data['car_license_plate']) || empty($data['car_daily_rate'])) {
            throw new Exception('All fields except status are required.');
        }

        $existing = $this->rentalRepository->getByLicensePlate($data['car_license_plate']);
        if (!empty($existing)) {
            throw new Exception('A rental with this license plate already exists.');
        }

        if (empty($data['rental_status'])) {
            $data['rental_status'] = 'available';
        }

        return $this->rentalRepository->create($data);
    }

    public function getRentalById($id) {
        $result = $this->rentalRepository->getById($id);
        return $result[0] ?? null;
    }

    public function updateRental($id, $data) {
        $existing = $this->getRentalById($id);
        if (!$existing) {
            throw new Exception("Rental not found.");
        }

        $updatable = [
            'car_daily_rate' => $data['car_daily_rate'] ?? null,
            'rental_status' => $data['rental_status'] ?? null,
        ];

        $updateFields = [];

        foreach ($updatable as $field => $newValue) {
            if ($newValue !== null && $existing[$field] != $newValue) {
                $updateFields[$field] = $newValue;
            }
        }

        if (empty($updateFields)) {
            return 'No changes detected.';
        }

        $this->rentalRepository->update($id, $updateFields);
        return 'Rental updated successfully.';
    }

    public function deleteRental($id) {
        $rental = $this->getRentalById($id);
        if (empty($rental)) {
            throw new Exception('Rental not found.');
        }
        return $this->rentalRepository->delete($id);
    }
}
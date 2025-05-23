<?php
namespace App\Controllers;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\RequestInterface;
use App\Core\Response;

class RentalController {
    private $rentalRepository;
    private $request;

    public function __construct(DataRepositoryInterface $rentalRepository, RequestInterface $request) {
        $this->rentalRepository = $rentalRepository;
        $this->request = $request;
    }

    public function getAllRentals() {
        return new Response(200, json_encode($this->rentalRepository->getAll()));
    }

    public function getRentalById($id) {
        $rental = $this->rentalRepository->getById($id);
        if (empty($rental)) {
            return new Response(404, json_encode(['error' => 'Rental not found']));
        }
        return new Response(200, json_encode($rental[0]));
    }

    public function createRental() {
        $data = $this->request->getBody();
        $this->rentalRepository->create($data);
        return new Response(201, json_encode(['message' => 'Rental created']));
    }

    public function updateRental($id) {
        $data = $this->request->getBody();
        $this->rentalRepository->update($id, $data);
        return new Response(200, json_encode(['message' => 'Rental updated']));
    }

    public function deleteRental($id) {
        $this->rentalRepository->delete($id);
        return new Response(204, '');
    }
}

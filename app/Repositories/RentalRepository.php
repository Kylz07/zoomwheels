<?php
namespace App\Repositories;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\iDBFuncs;
use App\Core\Database;
use Exception;
use App\Exceptions\RentalAlreadyExistsException;

class RentalRepository implements DataRepositoryInterface {
    private $db; 
    private $database; 

    public function __construct(iDBFuncs $db, Database $database) {
        $this->db = $db;
        $this->database = $database;
    }    
    
    public function getAll() {
        return $this->database->query("SELECT * FROM rentals");
    }

    public function getById($id) {
        return $this->database->query("SELECT * FROM rentals WHERE rental_id = ?", [$id]);
    }

    public function create($data) {
        $car_brand = $data['car_brand'];
        $car_model = $data['car_model'];
        $car_license_plate = $data['car_license_plate'];
        $car_daily_rate = $data['car_daily_rate'];
        $rental_status = $data['rental_status'] ?? 'available';

        return $this->db->table('rentals')->insert([
            null, $car_brand, $car_model, $car_license_plate, $car_daily_rate, $rental_status
        ]);
    }

    public function update($id, $data) {
        return $this->db->table('rentals')->where('rental_id', $id)->update($data);
    }

    public function delete($id) {
        return $this->db->table('rentals')->where('rental_id', $id)->delete();
    }

    public function getAllBrands() {
        $result = $this->database->query("SELECT DISTINCT car_brand FROM rentals ORDER BY car_brand");
        return array_column($result, 'car_brand');
    }

    public function getByLicensePlate($licensePlate) {
        return $this->database->query("SELECT * FROM rentals WHERE car_license_plate = ? LIMIT 1", [$licensePlate]);
    }

    public function getAllPaginated($page = 1, $itemsPerPage = 10) {
        $offset = ($page - 1) * $itemsPerPage;
        // Inject as integers directly (safe, no user input)
        $sql = "SELECT * FROM rentals LIMIT $itemsPerPage OFFSET $offset";
        $rentals = $this->database->query($sql);
        $totalSql = "SELECT COUNT(*) as total FROM rentals";
        $totalResult = $this->database->query($totalSql);
        $total = $totalResult[0]['total'] ?? 0;
        return [
            'rentals' => $rentals,
            'total' => $total
        ];
    }

    public function getFilteredPaginated($filters, $page = 1, $itemsPerPage = 10) {
        $where = [];
        $params = [];
        if (!empty($filters['status'])) {
            $where[] = 'rental_status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['brand'])) {
            $where[] = 'car_brand = ?';
            $params[] = $filters['brand'];
        }
        if (!empty($filters['rate'])) {
            $where[] = 'car_daily_rate <= ?';
            $params[] = $filters['rate'];
        }
        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $offset = ($page - 1) * $itemsPerPage;
        $sql = "SELECT * FROM rentals $whereSql LIMIT $itemsPerPage OFFSET $offset";
        $rentals = $this->database->query($sql, $params);
        $countSql = "SELECT COUNT(*) as total FROM rentals $whereSql";
        $totalResult = $this->database->query($countSql, $params);
        $total = $totalResult[0]['total'] ?? 0;
        return [
            'rentals' => $rentals,
            'total' => $total
        ];
    }
}

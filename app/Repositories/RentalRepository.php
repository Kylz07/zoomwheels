<?php
namespace App\Repositories;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\iDBFuncs;
use App\Core\Database;
use Exception;

class RentalRepository implements DataRepositoryInterface {
    private $db; // DBORM instance
    private $database; // Database instance for direct queries

    // Updated constructor
    public function __construct(iDBFuncs $db, Database $database) {
        $this->db = $db;
        $this->database = $database;
    }    
    
    // Use Database.php for reads (avoids DBORM state issues)
    public function getAll() {
        // Ensures a fresh, correct query every time
        return $this->database->query("SELECT * FROM rentals");
    }

    public function getById($id) {
        // Ensures a fresh, correct query with parameters
        return $this->database->query("SELECT * FROM rentals WHERE rental_id = ?", [$id]);
    }

    // Use DBORM for writes (assuming these work correctly or are preferred)
    public function create($data) {
        $car_brand = $data['car_brand'] ?? null;
        $car_model = $data['car_model'] ?? null;
        $car_license_plate = $data['car_license_plate'] ?? null;
        $car_daily_rate = $data['car_daily_rate'] ?? null;
        $rental_status = $data['rental_status'] ?? 'available';        
        
        if ($car_brand && $car_model && $car_license_plate && $car_daily_rate) {
            // This uses the DBORM instance
            return $this->db->table('rentals')->insert([
                null, $car_brand, $car_model, $car_license_plate, $car_daily_rate, $rental_status
            ]);
        } else {
            throw new Exception("Missing required fields for rental creation.");
        }
    }

    public function update($id, $data) {
        $fields = [];
        if (isset($data['car_brand'])) $fields['car_brand'] = $data['car_brand'];
        if (isset($data['car_model'])) $fields['car_model'] = $data['car_model'];
        if (isset($data['car_license_plate'])) $fields['car_license_plate'] = $data['car_license_plate'];
        if (isset($data['car_daily_rate'])) $fields['car_daily_rate'] = $data['car_daily_rate'];
        if (isset($data['rental_status'])) $fields['rental_status'] = $data['rental_status'];
        
        if (count($fields) > 0) {
            // This uses the DBORM instance
            return $this->db->table('rentals')->where('rental_id', $id)->update($fields);
        } else {
            throw new Exception("No valid fields provided for update.");
        }
    }

    public function delete($id) {
        // This uses the DBORM instance
        return $this->db->table('rentals')->where('rental_id', $id)->delete();
    }
}

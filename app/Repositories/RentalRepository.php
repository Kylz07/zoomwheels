<?php
namespace App\Repositories;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\iDBFuncs;

class RentalRepository implements DataRepositoryInterface {
    private $db;

    public function __construct(iDBFuncs $db) {
        $this->db = $db;
    }    public function getAll() {
        return $this->db->table('rentals')->select()->from('rentals')->getAll();
    }

    public function getById($id) {
        return $this->db->table('rentals')->select()->from('rentals')->where('rental_id', $id)->get();
    }

    public function create($data) {
        $car_brand = $data['car_brand'] ?? null;
        $car_model = $data['car_model'] ?? null;
        $car_license_plate = $data['car_license_plate'] ?? null;
        $car_daily_rate = $data['car_daily_rate'] ?? null;
        $rental_status = $data['rental_status'] ?? 'available';        if ($car_brand && $car_model && $car_license_plate && $car_daily_rate) {
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
        if (count($fields) > 0) 
        {
            return $this->db->table('rentals')->where('rental_id', $id)->update($fields);
        } 
        else {
            throw new Exception("No valid fields provided for update.");
        }
    }

    public function delete($id) {
        return $this->db->table('rentals')->where('rental_id', $id)->delete();
    }
}

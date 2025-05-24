<?php
namespace App\Repositories;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\iDBFuncs;

class UserRepository implements DataRepositoryInterface {
    private $db;

    public function __construct(iDBFuncs $db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->table('users')->select()->getAll();
    }

    public function getById($id) {
        return $this->db->table('users')->select()->where('user_id', $id)->get();
    }

    public function create($data) {
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        $email = $data['email'] ?? null;
        $first_name = $data['first_name'] ?? null;
        $last_name = $data['last_name'] ?? null;

        if ($username && $password && $email && $first_name && $last_name) {
            return $this->db->table('users')->insert([
                null, $username, $password, $email, $first_name, $last_name
            ]);
        } else {
            throw new Exception("Missing required fields for user creation.");
        }
    }

    public function update($id, $data) {
        $fields = [];
        if (isset($data['username'])) $fields['username'] = $data['username'];
        if (isset($data['password'])) $fields['password'] = $data['password'];
        if (isset($data['email'])) $fields['email'] = $data['email'];
        if (isset($data['first_name'])) $fields['first_name'] = $data['first_name'];
        if (isset($data['last_name'])) $fields['last_name'] = $data['last_name'];
        if (count($fields) > 0) 
        {
            return $this->db->table('users')->where('user_id', $id)->update($fields);
        } 
        else {
            throw new Exception("No valid fields provided for update.");
        }
    }

    public function delete($id) {
        return $this->db->table('users')->where('user_id', $id)->delete();
    }
}
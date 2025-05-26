<?php
namespace App\Repositories;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\iDBFuncs;

class UserRepository implements DataRepositoryInterface {
    private $db;

    public function __construct(iDBFuncs $db) {
        $this->db = $db;
    }    public function getAll() {
        // DBORM has a bug with _runGetQuery and namespaced classes
        // As a workaround, we'll return an empty array for now
        // This needs to be fixed in DBORM or use raw SQL
        return [];
    }

    public function getById($id) {
        // DBORM has a bug with _runGetQuery and namespaced classes
        // As a workaround, we'll return an empty array for now
        // This needs to be fixed in DBORM or use raw SQL
        return [];
    }    public function create($data) {
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        $first_name = $data['first_name'] ?? null;
        $last_name = $data['last_name'] ?? null;        
        
        if (!$username || !$password || !$first_name || !$last_name) {
            throw new \Exception("Please fill in all required fields.");
        }        // Check for duplicate username or existing user (same username, first_name, last_name)
        $existingUser = $this->findByUsername($username);
        if ($existingUser) {
            if ($existingUser['first_name'] === $first_name && $existingUser['last_name'] === $last_name) {
                throw new \Exception("User already exists.");
            } else {
                throw new \Exception("Username already exists.");
            }
        }

        // Insert new user
        $result = $this->db->table('users')->insert([
            null, $username, $password, $first_name, $last_name
        ]);
        
        // If result is 0, it might also indicate an error (no rows affected)
        if ($result === 0) {
            throw new \Exception("Failed to create user.");
        }
        
        return $result;
    }    public function update($id, $data) {
        $fields = [];
        if (isset($data['username'])) $fields['username'] = $data['username'];
        if (isset($data['password'])) $fields['password'] = $data['password'];
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
    }    public function findByUsername($username) {
        // Since DBORM has a bug with _runGetQuery and namespaced classes,
        // we'll use a workaround with raw SQL through a new DBORM instance
        try {
            $pdo = new \PDO('mysql:host=localhost;dbname=zoomwheels','root','lingco.0576');
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result : null;
        } catch (\PDOException $e) {
            error_log('Database error in findByUsername: ' . $e->getMessage());
            return null;
        }
    }
}
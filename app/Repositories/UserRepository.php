<?php
namespace App\Repositories;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\iDBFuncs;
use App\Core\Database;
use Exception;
use App\Exceptions\UserAlreadyExistsException;

class UserRepository implements DataRepositoryInterface {
    private $db;
    private $database; // Not used in this class, but can be useful for raw queries

    public function __construct(iDBFuncs $db, Database $database) {
        $this->db = $db;
        $this->database = $database;
    }    
    
    public function getAll() {
        return $this->database->query("SELECT * FROM users");
    }

    public function getById($id) {
        return $this->database->query("SELECT * FROM users WHERE user_id = ?", [$id]);
    }    

    public function delete($id) {
        return $this->db->table('users')->where('user_id', $id)->delete();
    }
    
    public function create($data) {
        $username = $data['username'];
        $password = $data['password'];
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];

        $existingUser = $this->findByUsername($username);
        if ($existingUser) {
            $isSameName = $existingUser['first_name'] === $first_name && $existingUser['last_name'] === $last_name;
            $message = $isSameName ? "User already exists." : "Username already exists.";
            throw new UserAlreadyExistsException($message);
        }

        $result = $this->db->table('users')->insert([
            null, $username, $password, $first_name, $last_name
        ]);
        
        if ($result === 0) {
            throw new \Exception("Failed to create user.");
        }
        
        return $result;
    }    
    
    public function update($id, $data) {
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
    
    public function findByUsername($username) {
        try {
            $result = $this->database->query("SELECT * FROM users WHERE username = ? LIMIT 1", [$username]);
            return !empty($result) ? $result[0] : null;
        } catch (\PDOException $e) {
            error_log('Database error in findByUsername: ' . $e->getMessage());
            return null;
        }
    }
}
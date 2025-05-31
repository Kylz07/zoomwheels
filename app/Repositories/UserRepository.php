<?php
namespace App\Repositories;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\iDBFuncs;
use App\Exceptions\UserAlreadyExistsException;

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

    public function delete($id) {
        return $this->db->table('users')->where('user_id', $id)->delete();
    }    
    
    public function findByUsername($username) {
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
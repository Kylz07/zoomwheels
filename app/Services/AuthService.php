<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\InvalidCredentialsException;

class AuthService {
    private $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user with validation and password hashing
     * @throws \Exception on validation or duplicate errors
     */
    public function register($data) {
        // Validate required fields
        $username = isset($data['username']) ? trim($data['username']) : '';
        $password = isset($data['password']) ? $data['password'] : '';
        $confirm_password = isset($data['confirm_password']) ? $data['confirm_password'] : '';
        $first_name = isset($data['first_name']) ? trim($data['first_name']) : '';
        $last_name = isset($data['last_name']) ? trim($data['last_name']) : '';

        if (!$username || !$password || !$confirm_password || !$first_name || !$last_name) {
            throw new \Exception('Please fill in all required fields.');
        }
        if (strlen($password) < 3) {
            throw new \Exception('Password must be at least 3 characters.');
        }
        if ($password !== $confirm_password) {
            throw new \Exception('Passwords do not match.');
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Attempt to create user (UserRepository will check for duplicates)
        try {
            $this->userRepository->create([
                'username' => $username,
                'password' => $hashedPassword,
                'first_name' => $first_name,
                'last_name' => $last_name
            ]);
        } catch (UserAlreadyExistsException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    /**
     * Login a user: validate input, check credentials, return user data or throw exception
     */
    public function login($data) {
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        if (empty($username) || empty($password)) {
            throw new InvalidCredentialsException('Username and password are required.');
        }
        $user = $this->userRepository->findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            throw new InvalidCredentialsException('Invalid username or password.');
        }
        return $user;
    }
}

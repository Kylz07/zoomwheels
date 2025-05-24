<?php
namespace App\Controllers;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\RequestInterface;
use App\Core\Response;
use App\Repositories\UserRepository;

class AuthController {
    private $userRepository;
    private $request;

    public function __construct(UserRepository $userRepository, RequestInterface $request) {
        $this->userRepository = $userRepository;
        $this->request = $request;
    }

    public function showRegisterForm($error = '', $success = '') {
        // Render the registration view with error/success messages
        include __DIR__ . '/../../public/register.php';
    }

    public function register() {
        $data = $this->request->getBody();
        $error = '';
        $success = '';
        // Server-side validation
        if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['confirm_password'])) {
            $error = 'All fields are required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } elseif (strlen($data['password']) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $error = 'Passwords do not match.';
        }
        if ($error) {
            $this->showRegisterForm($error);
            return;
        }
        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        try {
            $this->userRepository->create([
                'username' => $data['username'],
                'password' => $hashedPassword,
                'email' => $data['email'],
                'first_name' => $data['first_name'] ?? '',
                'last_name' => $data['last_name'] ?? ''
            ]);
            $success = 'Registration successful!';
            $this->showRegisterForm('', $success);
        } catch (\Exception $e) {
            $this->showRegisterForm('Registration failed: ' . $e->getMessage());
        }
    }
}

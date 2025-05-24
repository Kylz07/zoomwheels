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

    public function showRegisterForm($error = '', $success = '', $status = 200) {
        if (!isset($error)) $error = '';
        if (!isset($success)) $success = '';
        ob_start();
        include __DIR__ . '/../Views/users/register.php';
        $html = ob_get_clean();
        return new Response($status, $html);
    }

    public function register() {
        $data = $this->request->getBody();
        $error = '';
        $success = '';
        $status = 200;
        // Sanitize and validate input
        $username = isset($data['username']) ? trim($data['username']) : '';
        $email = isset($data['email']) ? filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL) : '';
        $password = isset($data['password']) ? $data['password'] : '';
        $confirm_password = isset($data['confirm_password']) ? $data['confirm_password'] : '';
        $first_name = isset($data['first_name']) ? trim($data['first_name']) : '';
        $last_name = isset($data['last_name']) ? trim($data['last_name']) : '';

        // Validation
        if (!$username || !$email || !$password || !$confirm_password || !$first_name || !$last_name) {
            $error = 'All fields are required.';
            $status = 400;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
            $status = 400;
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
            $status = 400;
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
            $status = 400;
        }
        if ($error) {
            // Error takes priority, no success message
            return $this->showRegisterForm($error, '', $status);
        }
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $this->userRepository->create([
                'username' => $username,
                'password' => $hashedPassword,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name
            ]);
            $success = 'Registration successful!';
            $status = 201;
            return $this->showRegisterForm('', $success, $status);
        } catch (\Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            $error = 'Registration failed. Please try again later.';
            $status = 500;
            return $this->showRegisterForm($error, '', $status);
        }
    }
}

<?php
namespace App\Controllers;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\RequestInterface;
use App\Core\Response;
use App\Repositories\UserRepository;

class AuthController {
    private $userRepository;
    private $request;
    private $session;

    public function __construct(UserRepository $userRepository, RequestInterface $request) {
        $this->userRepository = $userRepository;
        $this->request = $request;
        $this->session = new \App\Core\Session();
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
        $password = isset($data['password']) ? $data['password'] : '';
        $confirm_password = isset($data['confirm_password']) ? $data['confirm_password'] : '';
        $first_name = isset($data['first_name']) ? trim($data['first_name']) : '';
        $last_name = isset($data['last_name']) ? trim($data['last_name']) : '';

        // Validation for password length and password match
        if (strlen($password) < 3) {
            $error = 'Password must be at least 3 characters.';
            $status = 400;
            return $this->showRegisterForm($error, '', $status);
        }

        if ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
            $status = 400;
            return $this->showRegisterForm($error, '', $status);
        }

        // Hash and insert
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);        
        try {
            $this->userRepository->create([
                'username' => $username,
                'password' => $hashedPassword,
                'first_name' => $first_name,
                'last_name' => $last_name
            ]);
            $success = 'Registration successful!';
            $status = 201;
            return $this->showRegisterForm('', $success, $status);
        } catch (\Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            $error = $e->getMessage(); // Show specific error (e.g., duplicate)
            $status = 400;
            return $this->showRegisterForm($error, '', $status);
        }
    }

    public function showLoginForm($error = '', $status = 200) {
        if (!isset($error)) $error = '';
        ob_start();
        include __DIR__ . '/../Views/users/login.php';
        $html = ob_get_clean();
        return new Response($status, $html);
    }

    public function login() {
        $data = $this->request->getBody();
        $error = '';
        $status = 200;

        // Sanitize and validate input
        $username = isset($data['username']) ? trim($data['username']) : '';
        $password = isset($data['password']) ? $data['password'] : '';

        // Validate required fields
        if (empty($username) || empty($password)) {
            $error = 'Username and password are required.';
            $status = 400;
            return $this->showLoginForm($error, $status);
        }

        try {
            // Find user by username
            $user = $this->userRepository->findByUsername($username);
            
            if (!$user) {
                $error = 'Invalid username or password.';
                $status = 401;
                return $this->showLoginForm($error, $status);
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                $error = 'Invalid username or password.';
                $status = 401;
                return $this->showLoginForm($error, $status);
            }

            // Login successful - set session
            $this->session->setUser($user);
            $this->session->regenerate(); // Security: regenerate session ID

            // Redirect to dashboard
            return $this->redirectToDashboard();

        } catch (\Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            $error = 'An error occurred during login. Please try again.';
            $status = 500;
            return $this->showLoginForm($error, $status);
        }
    }

    public function logout() {
        $this->session->destroy();
        
        // Redirect to login form with success message
        ob_start();
        $success = 'You have been logged out successfully.';
        include __DIR__ . '/../Views/users/login.php';
        $html = ob_get_clean();
        return new Response(200, $html);
    }

    public function showDashboard() {
        // Check if user is logged in
        if (!$this->session->isLoggedIn()) {
            return $this->redirectToLogin('Please log in to access the dashboard.');
        }        $user = [
            'username' => $this->session->getUsername(),
            'first_name' => $this->session->get('first_name'),
            'last_name' => $this->session->get('last_name')
        ];

        ob_start();
        include __DIR__ . '/../Views/users/dashboard.php';
        $html = ob_get_clean();
        return new Response(200, $html);
    }

    private function redirectToDashboard() {
        // For now, we'll return the dashboard directly
        // In a real application, you might use header redirects
        return $this->showDashboard();
    }

    private function redirectToLogin($message = '') {
        return $this->showLoginForm($message, 401);
    }

    public function requireAuth() {
        if (!$this->session->isLoggedIn()) {
            return $this->redirectToLogin('Authentication required.');
        }
        return null; // Continue with request
    }

}

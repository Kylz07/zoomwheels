<?php
namespace App\Controllers;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\RequestInterface;
use App\Core\Response;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Exceptions\UserAlreadyExistsException;
use App\Exceptions\InvalidCredentialsException;
use App\Services\JwtService;
use App\Services\CookieAuthService;
use App\Traits\JwtAuthenticationTrait;

class AuthController {
    use JwtAuthenticationTrait;

    private $userRepository;
    private $request;
    private $cookieAuthService;
    private $authService;
    private $jwtService;

    public function __construct(UserRepository $userRepository, RequestInterface $request) {
        $this->userRepository = $userRepository;
        $this->request = $request;
        $this->authService = new AuthService($userRepository);
        $this->jwtService = new JwtService($userRepository);
        $this->cookieAuthService = new CookieAuthService($this->jwtService, null); // SessionService removed
    }    
    
    public function showRegisterForm($error = '', $success = '', $status = 200) {
        ob_start();
        include __DIR__ . '/../Views/users/register.php';
        $html = ob_get_clean();

        return new Response($status, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function register() {
        $data = $this->request->getBody();    

        try {
            $this->authService->register($data);
            $success = 'Registration successful! You can now log in';
            $status = 201;
            return $this->showRegisterForm('', $success, $status);
        } 
        catch (UserAlreadyExistsException $e) {
            $error = $e->getMessage();
            $status = 409;
            return $this->showRegisterForm($error, '', $status);
        } 
        catch (\Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            $error = $e->getMessage();
            $status = 400;
            return $this->showRegisterForm($error, '', $status);
        }
    }    
    
    public function showLoginForm($error = '', $status = 200) {
        ob_start();
        include __DIR__ . '/../Views/users/login.php';
        $html = ob_get_clean();
        return new Response($status, $html, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function login() {
        $data = $this->request->getBody();

        try {
            $user = $this->authService->login($data);
            $token = $this->jwtService->generateToken($user);

            $loginResponse = new Response(302, '', ['Location' => '/dashboard']);
            $loginResponse->addCookie(
                $this->jwtService->getCookieName(),
                $token,
                $this->jwtService->getCookieOptions()
            );
            return $loginResponse;
        } 
        catch (InvalidCredentialsException $e) {
            error_log($e->getMessage());
            return $this->showLoginForm('Invalid username or password.', 401);
        } 
        catch (\Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            return $this->showLoginForm('Login failed. Please try again.', 400);
        }
    }

    public function logout() {
        // Clear the JWT cookie and redirect to login
        $logoutResponse = new Response(302, '', ['Location' => '/login']);
        $logoutResponse->addCookie($this->jwtService->getCookieName(), '', $this->jwtService->getCookieOptions(true));
        return $logoutResponse;
    }
    
    public function showDashboard() {
        if (!$this->cookieAuthService->isAuthenticated()) {
            return $this->redirectToLogin('Can\'t access dashboard. Please log in first');
        }
        return new Response(200, '', ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    private function redirectToDashboard() {
        return $this->showDashboard();
    }

    private function redirectToLogin($message = '') {
        return $this->showLoginForm($message, 401);
    }    
    
    public function requireAuth() {
        // Use cookie-only JWT auth for web routes
        $auth = $this->requireJwtAuthCookieOnly();
        if ($auth) {
            return $this->redirectToLogin('Authentication required.');
        }
        return null;
    }

    /**
     * API endpoint to check authentication status
     */
    public function checkAuth() {
        if ($this->cookieAuthService->isAuthenticated()) {
            $user = $this->cookieAuthService->getAuthenticatedUser();
            return new Response(200, json_encode([
                'authenticated' => true,
                'user' => $user
            ]), ['Content-Type' => 'application/json']);
        } 
        else {
            return new Response(401, json_encode([
                'authenticated' => false,
                'message' => 'Not authenticated'
            ]), ['Content-Type' => 'application/json']);
        }
    }

}

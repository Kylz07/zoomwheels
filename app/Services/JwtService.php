<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Exceptions\InvalidCredentialsException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService {
    private $secret;
    private $algo;
    private $userRepository;
    private $cookieName;
    private $tokenExpiry;
    private $issuer;
    private $audience;

    public function __construct(UserRepository $userRepository, $config = null) {
        $this->userRepository = $userRepository;
        
        // Load configuration
        if ($config === null) {
            $config = include __DIR__ . '/../../config/init.php';
        }
        
        $this->secret = $config['jwt']['secret_key'] ?? 'your-secret-key';
        $this->algo = $config['jwt']['algorithm'] ?? 'HS256';
        $this->tokenExpiry = $config['jwt']['expiry_seconds'] ?? 3600;
        $this->issuer = $config['jwt']['issuer'] ?? 'ZoomwheelsApp';
        $this->audience = $config['jwt']['audience'] ?? 'ZoomwheelsAppUsers';
        $this->cookieName = 'zoomwheels_auth_token';
    }    public function generateToken($user) {
        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'sub' => $user['user_id'],
            'username' => $user['username'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'iat' => time(),
            'exp' => time() + $this->tokenExpiry
        ];
        return JWT::encode($payload, $this->secret, $this->algo);
    }

    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algo));
            // Optionally, check user existence
            $user = $this->userRepository->findByUsername($decoded->username);
            if (!$user) {
                throw new InvalidCredentialsException('User not found.');
            }
            return $decoded;
        } catch (\Exception $e) {
            throw new InvalidCredentialsException('Invalid or expired token.');
        }
    }

    public function getTokenFromCookie() {
        return $_COOKIE[$this->cookieName] ?? null;
    }

    public function validateTokenFromCookie() {
        $token = $this->getTokenFromCookie();
        if (!$token) {
            throw new InvalidCredentialsException('Authentication token not found.');
        }
        return $this->validateToken($token);
    }

    public function getCookieOptions($isRemoval = false) {
        $expiry = $isRemoval ? time() - 3600 : time() + $this->tokenExpiry;
        
        return [
            'expires' => $expiry,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ];
    }

    public function getCookieName() {
        return $this->cookieName;
    }
}

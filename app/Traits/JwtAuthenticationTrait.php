<?php
namespace App\Traits;

use App\Core\Response;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\WebAuthenticationRequiredException;

trait JwtAuthenticationTrait {
    private function requireJwtAuth() {
        // Try cookie-based authentication first
        try {
            $this->jwtService->validateTokenFromCookie();
            return null; // Authentication successful
        } catch (InvalidCredentialsException $e) {
            // Fall back to Authorization header for API compatibility
            $headers = getallheaders();
             // If no Authorization header and it's a web request, throw web exception
            if (!isset($headers['Authorization']) && $this->isWebRequest()) {
                throw new WebAuthenticationRequiredException($this->getWebAuthMessage($e->getMessage()));
            }
            
            if (!isset($headers['Authorization'])) {
                $response = new Response(401, json_encode(['error' => 'Authentication required']), ['Content-Type' => 'application/json']);
                return $response;
            }

            $authHeader = $headers['Authorization'];
            if (strpos($authHeader, 'Bearer ') !== 0) {
                $response = new Response(401, json_encode(['error' => 'Invalid Authorization header format']), ['Content-Type' => 'application/json']);
                return $response;
            }
            
            $token = substr($authHeader, 7);
            try {
                $this->jwtService->validateToken($token);
                return null; // Authentication successful
            } catch (InvalidCredentialsException $e) {
                $response = new Response(401, json_encode(['error' => $e->getMessage()]), ['Content-Type' => 'application/json']);
                return $response;
            }
        }
    }

    private function requireJwtAuthCookieOnly() {
        try {
            $this->jwtService->validateTokenFromCookie();
            return null; // Authentication successful
        } catch (InvalidCredentialsException $e) {
            // For cookie-only auth, if it's a web request, throw web exception
            if ($this->isWebRequest()) {
                throw new WebAuthenticationRequiredException($this->getWebAuthMessage($e->getMessage()));
            }

            $response = new Response(401, json_encode(['error' => $e->getMessage()]), ['Content-Type' => 'application/json']);
            return $response;
        }
    }

    /**
     * Check if current request is from a web browser
     * @return bool
     */
    private function isWebRequest() {
        // Access the request object through the controller that uses this trait
        return method_exists($this, 'getRequest') ? $this->getRequest()->isWebRequest() : true;
    }

    private function getWebAuthMessage($originalMessage) {
        switch ($originalMessage) {
            case 'Authentication token not found.':
                return 'Access denied. Please login to continue.';
            case 'User not found.':
                return 'Session invalid. Please login again.';
            case 'Invalid or expired token.':
                return 'Your session has expired. Please login again.';
            default:
                return 'Authentication required. Please login.';
        }
    }
}

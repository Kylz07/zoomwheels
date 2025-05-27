<?php
namespace App\Traits;

use App\Core\Response;
use App\Exceptions\InvalidCredentialsException;

trait JwtAuthenticationTrait {
    private function requireJwtAuth() {
        // Try cookie-based authentication first
        try {
            $this->jwtService->validateTokenFromCookie();
            return null; // Authentication successful
        } catch (InvalidCredentialsException $e) {
            // Fall back to Authorization header for API compatibility
            $headers = getallheaders();
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
            $response = new Response(401, json_encode(['error' => $e->getMessage()]), ['Content-Type' => 'application/json']);
            return $response;
        }
    }
}

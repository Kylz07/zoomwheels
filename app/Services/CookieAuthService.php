<?php
namespace App\Services;

use App\Services\JwtService;
use App\Exceptions\InvalidCredentialsException;

class CookieAuthService {
    private $jwtService;

    public function __construct(JwtService $jwtService) {
        $this->jwtService = $jwtService;
    }

    /**
     * Check if user is authenticated via JWT cookie only
     */
    public function isAuthenticated() {
        try {
            $this->jwtService->validateTokenFromCookie();
            return true;
        } catch (InvalidCredentialsException $e) {
            return false;
        }
    }

    /**
     * Get authenticated user data from JWT cookie only
     */
    public function getAuthenticatedUser() {
        try {
            $tokenData = $this->jwtService->validateTokenFromCookie();
            return [
                'user_id' => $tokenData->sub,
                'username' => $tokenData->username,
                'first_name' => $tokenData->first_name,
                'last_name' => $tokenData->last_name
            ];
        } catch (InvalidCredentialsException $e) {
            return null;
        }
    }

    /**
     * No session to sync from JWT
     */
    public function syncSessionFromJwt() {
        return true;
    }

    /**
     * No session to clear, only JWT cookie is handled by controller
     */
    public function clearAuthentication() {
        // No-op
    }
}

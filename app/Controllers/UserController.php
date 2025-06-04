<?php

namespace App\Controllers;

use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\RequestInterface;
use App\Core\Response;
use App\Services\JwtService;
use App\Exceptions\InvalidCredentialsException;
use App\Traits\JwtAuthenticationTrait;

class UserController {
    use JwtAuthenticationTrait;
    
    private $userRepository;
    private $request;
    private $jwtService;

    public function __construct(DataRepositoryInterface $userRepository, RequestInterface $request, JwtService $jwtService) {
        $this->userRepository = $userRepository;        
        $this->request = $request;
        $this->jwtService = $jwtService;
    }

    protected function getRequest() {
        return $this->request;
    }

    public function getAllUsers() {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        return new Response(200, json_encode($this->userRepository->getAll()));
    }
    public function getUserById($id) {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $user = $this->userRepository->getById($id);
        if (empty($user)) {
            return new Response(404, json_encode(['error' => 'User not found']));
        }
        return new Response(200, json_encode($user[0]));
    }
    
    public function deleteUser($id) {
        $auth = $this->requireJwtAuth();
        if ($auth) return $auth;
        $this->userRepository->delete($id);
        return new Response(204, '');
    }
}
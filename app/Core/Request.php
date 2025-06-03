<?php
namespace App\Core;

use App\Core\Interfaces\RequestInterface;

class Request implements RequestInterface {
    public function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPath(): string {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return rtrim($path, '/');
    }

    public function getBody(): array {
        $data = [];
        if ($this->getMethod() === 'POST' || $this->getMethod() === 'PUT') {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (stripos($contentType, 'application/json') === 0) {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!is_array($data)) {
                    $data = [];
                }
            } else {
                // For form submissions (x-www-form-urlencoded or multipart/form-data)
                $data = $_POST;
            }
        }
        return is_array($data) ? $data : [];
    }

    public function getQueryParam(string $key, $default = null) {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public function isApiRequest() {
        // Check Accept header for JSON preference
        $acceptHeader = $this->getHeader('Accept') ?? '';
        if (strpos($acceptHeader, 'application/json') !== false) {
            return true;
        }
        
        // Check if Authorization header is present (typical for API)
        if ($this->getHeader('Authorization')) {
            return true;
        }

            // Check User-Agent for typical API clients
        $userAgent = $this->getHeader('User-Agent') ?? '';
        $apiUserAgents = ['curl', 'postman', 'insomnia', 'httpie', 'python-requests'];
        
        foreach ($apiUserAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }
        
        return false;
    }

    public function isWebRequest() {
        return !$this->isApiRequest();
    }

    private function getHeader($name) {
        $headers = getallheaders();
        return $headers[$name] ?? null;
    }
}
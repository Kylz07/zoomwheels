<?php
namespace App\Core;

class Response {
    private $statusCode;
    private $body;
    private $headers;
    private $cookies;

    public function __construct($statusCode, $body, $headers = [], $cookies = []) {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
        $this->cookies = $cookies;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function getBody() {
        return $this->body;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function getCookies() {
        return $this->cookies;
    }

    public function addHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    public function addCookie($name, $value, $options = []) {
        $this->cookies[$name] = array_merge([
            'value' => $value,
            'expires' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ], $options);
    }    public function send() {
        // Auto-detect content type if not set
        if (!isset($this->headers['Content-Type'])) {
            if (stripos($this->body, '<!DOCTYPE html>') === 0 || stripos($this->body, '<html') !== false) {
                $this->headers['Content-Type'] = 'text/html; charset=UTF-8';
            } elseif ($this->isJson($this->body)) {
                $this->headers['Content-Type'] = 'application/json';
            } else {
                $this->headers['Content-Type'] = 'text/plain';
            }
        }

        // Set headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Set cookies
        foreach ($this->cookies as $name => $options) {
            setcookie(
                $name,
                $options['value'],
                [
                    'expires' => $options['expires'],
                    'path' => $options['path'],
                    'domain' => $options['domain'],
                    'secure' => $options['secure'],
                    'httponly' => $options['httponly'],
                    'samesite' => $options['samesite']
                ]
            );
        }

        // Set status code
        http_response_code($this->statusCode);

        // Output body
        echo $this->body;
    }

    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
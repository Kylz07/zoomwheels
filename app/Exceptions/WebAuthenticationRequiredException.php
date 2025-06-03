<?php
// filepath: c:\Apache24\htdocs\mysite\Zoomwheels\app\Exceptions\WebAuthenticationRequiredException.php
namespace App\Exceptions;

class WebAuthenticationRequiredException extends \Exception
{
    public function __construct($message = "Authentication required.", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

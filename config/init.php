<?php

namespace Config;

// Remove require_once and use class imports
use App\Controllers\RentalController;
use App\Controllers\UserController;
use App\Core\Database;
use App\Core\DBORM;
use App\Core\Request;
use App\Core\Response;
use App\Core\RouteMatcher;
use App\Core\Router;
use App\Core\Interfaces\DataRepositoryInterface;
use App\Core\Interfaces\iDBfuncs;
use App\Core\Interfaces\RequestInterface;
use App\Repositories\RentalRepository;
use App\Repositories\UserRepository;
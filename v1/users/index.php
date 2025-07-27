<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/UserController.php';

handleCors();
$controller = new UserController();
$controller->handleRequest();
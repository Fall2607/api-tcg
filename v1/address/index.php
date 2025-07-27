<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/AddressController.php';

handleCors();
$controller = new AddressController();
$controller->handleRequest();
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/OrderController.php';

handleCors();
$controller = new OrderController();
$controller->handleRequest();
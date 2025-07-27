<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/OrderItemController.php';

handleCors();
$controller = new OrderItemController();
$controller->handleRequest();
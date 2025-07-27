<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

handleCors();
$controller = new ProductController();
$controller->handleRequest();
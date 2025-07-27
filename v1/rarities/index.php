<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/RarityController.php';

handleCors();
$controller = new RarityController();
$controller->handleRequest();
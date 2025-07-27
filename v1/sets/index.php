<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/SetController.php';

handleCors();
$controller = new SetController();
$controller->handleRequest();
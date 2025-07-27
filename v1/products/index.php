<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

// HAPUS BLOK 'function handleCors() { ... }' DARI SINI.

// Panggil fungsi yang sudah didefinisikan di file lain
handleCors();

$controller = new ProductController();
$controller->handleRequest();

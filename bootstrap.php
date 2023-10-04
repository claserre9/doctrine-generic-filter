<?php
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php'; // Adjust the path as needed
use Dotenv\Dotenv;

// Load the .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/'); // Adjust the path as needed
$dotenv->load();

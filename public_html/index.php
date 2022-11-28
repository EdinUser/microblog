<?php

require __DIR__ . '/../vendor/autoload.php';

// Use .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// Bootstrapping all things
require $_ENV['ROOT_FOLDER'] . '/core/bootstrap.php';

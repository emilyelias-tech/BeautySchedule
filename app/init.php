<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/banco/database.php';
require_once __DIR__ . '/banco/crud.php';
require_once __DIR__ . "/nav.php";

$pdo = database();


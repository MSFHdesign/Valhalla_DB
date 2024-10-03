<?php
// db_connection.php

// Sørg for at inkludere env_loader.php for at indlæse miljøvariablerne
require_once __DIR__ . '/env_loader.php';

// Brug miljøvariablerne til at oprette forbindelse til databasen
$dbHost = getenv('MYSQL_HOST');
$dbUser = getenv('MYSQL_USER');
$dbPass = getenv('MYSQL_PASSWORD');
$dbName = getenv('MYSQL_DATABASE');

// Opret forbindelse til databasen
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Tjek forbindelsen
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Funktion til at lukke forbindelsen, hvis nødvendigt
function closeDbConnection($mysqli) {
    $mysqli->close();
}

<?php
try {
    $dbh = new PDO("mysql:host=localhost;dbname=khamia4_db", "khamia4_local", "Nx58]XvS");
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}

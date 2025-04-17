<?php
try {
    $dbh = new PDO("mysql:host=localhost;dbname=khamia4_db", "root", "");
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}

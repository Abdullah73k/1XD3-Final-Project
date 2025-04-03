<?php
try {
    $dbh = new PDO("mysql:host=localhost;dbname=final_proj", "root", "");
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
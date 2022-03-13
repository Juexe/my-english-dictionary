<?php
$db  = 'word_dictionary';
$dsn = "mysql:host=localhost;dbname=$db";
$pdo = new PDO($dsn, 'root', '');
$pdo->exec('SET NAMES utf8mb4');
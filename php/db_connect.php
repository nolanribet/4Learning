<?php
// =============================================================
//  db_connect.php — Connexion à la base de données (PDO)
// =============================================================

define('DB_HOST',    'localhost:3306');
define('DB_NAME',    'db_grp11');
define('DB_USER',    'grp11');
define('DB_PASS',    'thah9Oow');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']));
        }
    }

    return $pdo;
}

<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../php/db_connect.php';
echo json_encode(['success'=>true,'tags'=>getDB()->query('SELECT id, nom, couleur FROM tags ORDER BY nom')->fetchAll()]);

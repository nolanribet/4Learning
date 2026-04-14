<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../php/db_connect.php';
echo json_encode(['success'=>true,'badges'=>getDB()->query('SELECT id, nom, description, icone, couleur FROM badges')->fetchAll()]);

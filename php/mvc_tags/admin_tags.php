<?php
header('Content-Type: application/json'); session_start();
require_once __DIR__ . '/../../php/db_connect.php';
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Non autorise.']); exit; }
// Vérifie que le user connecté est admin
$pdo = getDB();
$stmtRole = $pdo->prepare('SELECT role FROM users WHERE id = ?'); $stmtRole->execute([$_SESSION['user_id']]); $roleRow = $stmtRole->fetch();
if (!$roleRow || $roleRow['role'] !== 'admin') { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Droits insuffisants.']); exit; }
$action = $_POST['action'] ?? '';
if ($action === 'create') {
    $nom = trim($_POST['nom'] ?? ''); $couleur = trim($_POST['couleur'] ?? '#5c6672');
    if (strlen($nom) < 2) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Nom trop court.']); exit; }
    $pdo->prepare('INSERT INTO tags (nom, couleur) VALUES (?, ?)')->execute([$nom, $couleur]);
    echo json_encode(['success'=>true,'message'=>'Tag cree.']);
} elseif ($action === 'delete') {
    $pdo->prepare('DELETE FROM tags WHERE id = ?')->execute([(int)($_POST['tag_id'] ?? 0)]);
    echo json_encode(['success'=>true,'message'=>'Tag supprime.']);
} else { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Action inconnue.']); }

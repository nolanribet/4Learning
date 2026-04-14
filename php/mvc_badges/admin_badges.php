<?php
header('Content-Type: application/json'); session_start();
require_once __DIR__ . '/../../php/db_connect.php';
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Non autorise.']); exit; }
$saison_id = (int)($_POST['saison_id'] ?? 0);
if (($_POST['action'] ?? '') !== 'award_top3' || $saison_id < 1) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Parametres invalides.']); exit; }
$pdo = getDB(); $top3 = $pdo->query('SELECT id FROM users ORDER BY points DESC LIMIT 3')->fetchAll();
foreach ($top3 as $u) {
    $stmt = $pdo->prepare('SELECT id FROM user_badges WHERE user_id = ? AND badge_id = 3 AND saison_id = ?'); $stmt->execute([$u['id'], $saison_id]);
    if (!$stmt->fetch()) { $pdo->prepare('INSERT INTO user_badges (user_id, badge_id, saison_id) VALUES (?, 3, ?)')->execute([$u['id'], $saison_id]); }
}
echo json_encode(['success'=>true,'message'=>'Badge Top 3 attribue.']);

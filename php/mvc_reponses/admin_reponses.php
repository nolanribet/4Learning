<?php
// admin_reponses.php — Classement saisonnier
header('Content-Type: application/json');
require_once __DIR__ . '/../../php/db_connect.php';
$pdo = getDB();
$saison = $pdo->query('SELECT nom FROM saisons WHERE active = 1 LIMIT 1')->fetchColumn();
$users = $pdo->query('SELECT u.id, u.username, u.niveau, u.points, u.fiabilite FROM users u ORDER BY u.points DESC LIMIT 20')->fetchAll();
$rang = 1;
foreach ($users as &$u) {
    $u['rang'] = $rang++;
    $stmtB = $pdo->prepare('SELECT b.icone FROM user_badges ub JOIN badges b ON b.id = ub.badge_id WHERE ub.user_id = ?');
    $stmtB->execute([$u['id']]); $u['badges'] = array_column($stmtB->fetchAll(), 'icone'); unset($u['id']);
}
$badges = $pdo->query('SELECT icone, nom, description FROM badges')->fetchAll();
echo json_encode(['success'=>true,'saison'=>$saison ?: 'Saison en cours','users'=>$users,'badges'=>$badges]);

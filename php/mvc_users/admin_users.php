<?php
// =============================================================
//  admin_users.php — Stats globales (page d'accueil + admin)
//  GET → nombre d'utilisateurs, questions, réponses
// =============================================================

header('Content-Type: application/json');
require_once __DIR__ . '/../../php/db_connect.php';

$pdo = getDB();

$users     = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$questions = (int) $pdo->query('SELECT COUNT(*) FROM questions')->fetchColumn();
$answers   = (int) $pdo->query('SELECT COUNT(*) FROM answers')->fetchColumn();

echo json_encode([
    'success'   => true,
    'users'     => $users,
    'questions' => $questions,
    'answers'   => $answers,
]);

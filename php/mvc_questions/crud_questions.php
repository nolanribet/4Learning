<?php
// crud_questions.php — POST action=create
header('Content-Type: application/json'); session_start();
require_once __DIR__ . '/../../php/db_connect.php';
if (($_POST['action'] ?? '') !== 'create') { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Action inconnue.']); exit; }
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Connectez-vous.']); exit; }
$user_id = (int)$_SESSION['user_id'];
$titre = trim($_POST['titre'] ?? ''); $contenu = trim($_POST['contenu'] ?? '');
$tag_id = (int)($_POST['tag_id'] ?? 0); $niveau_requis = (int)($_POST['niveau_requis'] ?? 1);
$errors = [];
if (strlen($titre) < 10 || strlen($titre) > 255) $errors[] = 'Titre : 10 a 255 caracteres.';
if (strlen($contenu) < 20) $errors[] = 'Description : 20 caracteres minimum.';
if ($tag_id < 1) $errors[] = 'Selectionnez une matiere.';
if ($errors) { http_response_code(400); echo json_encode(['success'=>false,'message'=>implode(' ',$errors)]); exit; }
$pdo = getDB();
$pdo->prepare('INSERT INTO questions (user_id, tag_id, titre, contenu, niveau_requis) VALUES (?, ?, ?, ?, ?)')->execute([$user_id, $tag_id, $titre, $contenu, $niveau_requis]);
$id = $pdo->lastInsertId();
$pdo->prepare('UPDATE users SET points = points + 2 WHERE id = ?')->execute([$user_id]);
echo json_encode(['success'=>true,'message'=>'Question publiee !','question_id'=>$id]);

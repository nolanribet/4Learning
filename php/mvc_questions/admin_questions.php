<?php
// admin_questions.php — POST action=resolve|close & question_id=X
header('Content-Type: application/json'); session_start();
require_once __DIR__ . '/../../php/db_connect.php';
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Non autorise.']); exit; }
$action = $_POST['action'] ?? ''; $question_id = (int)($_POST['question_id'] ?? 0);
if ($question_id < 1) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'question_id manquant.']); exit; }
$pdo = getDB(); $stmt = $pdo->prepare('SELECT user_id FROM questions WHERE id = ?'); $stmt->execute([$question_id]); $q = $stmt->fetch();
if (!$q || $q['user_id'] !== $_SESSION['user_id']) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Non autorise.']); exit; }
$statut = ($action === 'resolve') ? 'resolue' : (($action === 'close') ? 'fermee' : null);
if (!$statut) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Action inconnue.']); exit; }
$pdo->prepare('UPDATE questions SET statut = ? WHERE id = ?')->execute([$statut, $question_id]);
echo json_encode(['success'=>true,'message'=>'Statut mis a jour.']);

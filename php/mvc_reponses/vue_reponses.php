<?php
// vue_reponses.php — GET ?question_id=X
header('Content-Type: application/json');
require_once __DIR__ . '/../../php/db_connect.php';
$question_id = (int)($_GET['question_id'] ?? 0);
if ($question_id < 1) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'question_id manquant.']); exit; }
$pdo = getDB();
$stmt = $pdo->prepare('SELECT a.id, a.contenu, a.note_moy, a.nb_votes, a.created_at, u.username AS auteur FROM answers a JOIN users u ON u.id = a.user_id WHERE a.question_id = ? ORDER BY a.note_moy DESC, a.created_at ASC');
$stmt->execute([$question_id]); $answers = $stmt->fetchAll();
foreach ($answers as &$a) { $a['date'] = (new DateTime($a['created_at']))->format('d M Y'); $a['note_moy'] = (float)$a['note_moy']; unset($a['created_at']); }
echo json_encode(['success'=>true,'answers'=>$answers]);

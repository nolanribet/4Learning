<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../php/db_connect.php';
$tag_id = (int)($_GET['tag_id'] ?? 0);
if ($tag_id < 1) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'tag_id manquant.']); exit; }
$pdo = getDB(); $stmt = $pdo->prepare('SELECT id, nom, couleur FROM tags WHERE id = ?'); $stmt->execute([$tag_id]); $tag = $stmt->fetch();
if (!$tag) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Tag introuvable.']); exit; }
$stmtQ = $pdo->prepare('SELECT q.id, q.titre, q.statut, COUNT(a.id) AS nb_reponses FROM questions q LEFT JOIN answers a ON a.question_id = q.id WHERE q.tag_id = ? GROUP BY q.id ORDER BY q.created_at DESC LIMIT 20');
$stmtQ->execute([$tag_id]);
echo json_encode(['success'=>true,'tag'=>$tag,'questions'=>$stmtQ->fetchAll()]);

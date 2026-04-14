<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../php/db_connect.php';
$user_id = (int)($_GET['user_id'] ?? 0);
if ($user_id < 1) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'user_id manquant.']); exit; }
$pdo = getDB();
$stmtQ = $pdo->prepare('SELECT COUNT(*) FROM questions WHERE user_id = ?'); $stmtQ->execute([$user_id]); $nb_questions = (int)$stmtQ->fetchColumn();
$stmtA = $pdo->prepare('SELECT COUNT(*) FROM answers WHERE user_id = ?'); $stmtA->execute([$user_id]); $nb_answers = (int)$stmtA->fetchColumn();
$stmtB = $pdo->prepare('SELECT b.icone, b.nom, b.description FROM user_badges ub JOIN badges b ON b.id = ub.badge_id WHERE ub.user_id = ?'); $stmtB->execute([$user_id]); $badges = $stmtB->fetchAll();
$stmtQR = $pdo->prepare('SELECT q.id, q.titre, q.statut, q.created_at, t.nom AS matiere, t.couleur AS matiere_couleur, COUNT(a.id) AS nb_reponses FROM questions q JOIN tags t ON t.id = q.tag_id LEFT JOIN answers a ON a.question_id = q.id WHERE q.user_id = ? GROUP BY q.id ORDER BY q.created_at DESC LIMIT 10');
$stmtQR->execute([$user_id]); $questions = $stmtQR->fetchAll();
foreach ($questions as &$q) { $q['date'] = (new DateTime($q['created_at']))->format('d M Y'); unset($q['created_at']); }
$stmtAR = $pdo->prepare('SELECT a.id, a.contenu, a.note_moy, a.nb_votes, a.created_at, q.titre AS question_titre FROM answers a JOIN questions q ON q.id = a.question_id WHERE a.user_id = ? ORDER BY a.created_at DESC LIMIT 10');
$stmtAR->execute([$user_id]); $answers = $stmtAR->fetchAll();
foreach ($answers as &$a) { $a['date'] = (new DateTime($a['created_at']))->format('d M Y'); $a['note_moy'] = (float)$a['note_moy']; unset($a['created_at']); }
echo json_encode(['success'=>true,'nb_questions'=>$nb_questions,'nb_answers'=>$nb_answers,'badges'=>$badges,'questions'=>$questions,'answers'=>$answers]);

<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../php/db_connect.php';
$user_id = (int)($_GET['user_id'] ?? 0);
if ($user_id < 1) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'user_id manquant.']); exit; }
$pdo = getDB();
$stmtU = $pdo->prepare('SELECT fiabilite FROM users WHERE id = ?'); $stmtU->execute([$user_id]); $user = $stmtU->fetch();
$stmtNbQ = $pdo->prepare('SELECT COUNT(*) FROM questions WHERE user_id = ?'); $stmtNbQ->execute([$user_id]); $nb_questions = (int)$stmtNbQ->fetchColumn();
$stmtNbA = $pdo->prepare('SELECT COUNT(*) FROM answers WHERE user_id = ?'); $stmtNbA->execute([$user_id]); $nb_answers = (int)$stmtNbA->fetchColumn();
$stmtAvg = $pdo->prepare('SELECT AVG(note_moy) FROM answers WHERE user_id = ? AND nb_votes > 0'); $stmtAvg->execute([$user_id]); $avg_note = (float)$stmtAvg->fetchColumn();
$stmtE = $pdo->prepare('SELECT badge_id FROM user_badges WHERE user_id = ?'); $stmtE->execute([$user_id]); $existing = array_column($stmtE->fetchAll(), 'badge_id');
$awarded = [];
if ($nb_answers  >= 1  && !in_array(1,$existing)) { $pdo->prepare('INSERT INTO user_badges (user_id, badge_id) VALUES (?,1)')->execute([$user_id]); $awarded[] = 'Premiere reponse'; }
if ($avg_note    >= 4.5 && !in_array(2,$existing)) { $pdo->prepare('INSERT INTO user_badges (user_id, badge_id) VALUES (?,2)')->execute([$user_id]); $awarded[] = 'Expert'; }
if ($user && $user['fiabilite'] >= 100 && !in_array(4,$existing)) { $pdo->prepare('INSERT INTO user_badges (user_id, badge_id) VALUES (?,4)')->execute([$user_id]); $awarded[] = 'Fiable'; }
if ($nb_questions >= 10 && !in_array(5,$existing)) { $pdo->prepare('INSERT INTO user_badges (user_id, badge_id) VALUES (?,5)')->execute([$user_id]); $awarded[] = 'Curieux'; }
echo json_encode(['success'=>true,'badges_awarded'=>$awarded]);

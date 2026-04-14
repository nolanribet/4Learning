<?php
// crud_reponses.php — POST action=create|rate
header('Content-Type: application/json'); session_start();
require_once __DIR__ . '/../../php/db_connect.php';
$action = $_POST['action'] ?? '';
if ($action === 'create') {
    if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Connectez-vous.']); exit; }
    $user_id = (int)$_SESSION['user_id']; $question_id = (int)($_POST['question_id'] ?? 0); $contenu = trim($_POST['contenu'] ?? '');
    if ($question_id < 1 || strlen($contenu) < 10) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Donnees invalides.']); exit; }
    $pdo = getDB();
    $stmt = $pdo->prepare('SELECT niveau_requis, statut FROM questions WHERE id = ?'); $stmt->execute([$question_id]); $question = $stmt->fetch();
    if (!$question) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Question introuvable.']); exit; }
    if ($question['statut'] !== 'ouverte') { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Question fermee.']); exit; }
    $stmtU = $pdo->prepare('SELECT niveau FROM users WHERE id = ?'); $stmtU->execute([$user_id]); $user = $stmtU->fetch();
    if ($user['niveau'] < $question['niveau_requis']) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Niveau insuffisant (requis: '.$question['niveau_requis'].').']); exit; }
    $pdo->prepare('INSERT INTO answers (question_id, user_id, contenu) VALUES (?, ?, ?)')->execute([$question_id, $user_id, $contenu]);
    $pdo->prepare('UPDATE users SET points = points + 5 WHERE id = ?')->execute([$user_id]);
    echo json_encode(['success'=>true,'message'=>'Reponse publiee !']);
} elseif ($action === 'rate') {
    if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Connectez-vous.']); exit; }
    $user_id = (int)$_SESSION['user_id']; $answer_id = (int)($_POST['answer_id'] ?? 0); $note = (int)($_POST['note'] ?? 0);
    if ($answer_id < 1 || $note < 1 || $note > 5) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Note invalide.']); exit; }
    $pdo = getDB(); $stmt = $pdo->prepare('SELECT user_id FROM answers WHERE id = ?'); $stmt->execute([$answer_id]); $answer = $stmt->fetch();
    if (!$answer) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Reponse introuvable.']); exit; }
    if ($answer['user_id'] === $user_id) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Impossible de noter sa propre reponse.']); exit; }
    try { $pdo->prepare('INSERT INTO ratings (answer_id, user_id, note) VALUES (?, ?, ?)')->execute([$answer_id, $user_id, $note]); }
    catch (PDOException $e) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'Deja note.']); exit; }
    $pdo->prepare('UPDATE answers SET note_moy = (SELECT AVG(note) FROM ratings WHERE answer_id = ?), nb_votes = nb_votes + 1 WHERE id = ?')->execute([$answer_id, $answer_id]);
    $stmtAvg = $pdo->prepare('SELECT note_moy FROM answers WHERE id = ?'); $stmtAvg->execute([$answer_id]); $avg = (float)$stmtAvg->fetchColumn();
    // Fiabilité = moyenne globale de TOUTES les réponses notées du user (pas juste cette réponse)
    $stmtFiab = $pdo->prepare('SELECT AVG(note_moy) FROM answers WHERE user_id = ? AND nb_votes > 0'); $stmtFiab->execute([$answer['user_id']]); $avgGlobal = (float)$stmtFiab->fetchColumn();
    $pdo->prepare('UPDATE users SET fiabilite = ? WHERE id = ?')->execute([min(100,max(0,(int)round($avgGlobal*20))), $answer['user_id']]);
    echo json_encode(['success'=>true,'message'=>'Note enregistree.','note_moy'=>round($avg,2)]);
} else { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Action inconnue.']); }

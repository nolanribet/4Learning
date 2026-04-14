<?php
// vue_questions.php — GET ?tag_id=X&statut=X&search=X
header('Content-Type: application/json');
require_once __DIR__ . '/../../php/db_connect.php';
$pdo = getDB(); $where = []; $params = [];
if (!empty($_GET['tag_id'])) { $where[] = 'q.tag_id = ?'; $params[] = (int)$_GET['tag_id']; }
if (!empty($_GET['statut']))  { $where[] = 'q.statut = ?'; $params[] = $_GET['statut']; }
if (!empty($_GET['search']))  { $s = '%' . $_GET['search'] . '%'; $where[] = '(q.titre LIKE ? OR q.contenu LIKE ?)'; $params[] = $s; $params[] = $s; }
$wc = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$sql = "SELECT q.id, q.titre, q.contenu, q.statut, q.niveau_requis, q.created_at, u.username AS auteur, t.nom AS matiere, t.couleur AS matiere_couleur, t.id AS tag_id, COUNT(a.id) AS nb_reponses FROM questions q JOIN users u ON u.id = q.user_id JOIN tags t ON t.id = q.tag_id LEFT JOIN answers a ON a.question_id = q.id $wc GROUP BY q.id ORDER BY q.created_at DESC LIMIT 50";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $questions = $stmt->fetchAll();
foreach ($questions as &$q) { $q['date'] = (new DateTime($q['created_at']))->format('d M Y'); unset($q['created_at']); }
echo json_encode(['success'=>true,'questions'=>$questions]);

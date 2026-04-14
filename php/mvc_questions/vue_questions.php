<?php
/**
 * VUE: Composants de l'interface pour les Questions
 */

function html_table_questions($questions) {
    $html  = "<table class='table-questions'>\n";
    $html .= "<thead><tr><th>Titre</th><th>Matière</th><th>Auteur</th><th>Actions</th></tr></thead>\n";
    $html .= "<tbody>\n";
    foreach ($questions as $question) {
        $html .= html_tr_question($question);
    }
    $html .= "</tbody></table>\n";
    return $html;
}

function html_tr_question($question) {
    $id      = $question['id'];
    $titre   = $question['titre'];
    $matiere = $question['tag_matiere'];
    $auteur  = $question['id_auteur'];
    $html  = "<tr>";
    $html .= "<td>$titre</td><td>$matiere</td><td>$auteur</td>";
    $html .= "<td>" . html_a_update_question($id) . " " . html_a_delete_question($id) . "</td>";
    $html .= "</tr>";
    return $html;
}

function html_a_delete_question($id) {
    return "<a href='admin_questions.php?action=delete&id=$id' onclick='return confirm(\"Supprimer cette question ?\")'><img src='../../../assets/images/delete-good.png' width='25px'></a>";
}

function html_a_update_question($id) {
    return "<a href='admin_questions.php?action=update_form&id=$id'><img src='../../../assets/images/update.png' width='25px'></a>";
}

function html_form_maj_question($question) {
    $id      = $question['id'];
    $titre   = htmlspecialchars($question['titre']);
    $content = htmlspecialchars($question['content']);
    $matiere = htmlspecialchars($question['tag_matiere']);
    $html  = "<form action='admin_questions.php' method='POST' class='form-container'>\n";
    $html .= "<h3>Modifier la question #$id</h3>\n";
    $html .= "<label>Sujet</label><br><input type='text' name='titre' value='$titre' required><br>\n";
    $html .= "<label>Contenu</label><br><textarea name='content' rows='5' required>$content</textarea><br>\n";
    $html .= "<label>Matière</label><br><input type='text' name='tag_matiere' value='$matiere'><br>\n";
    $html .= "<input type='hidden' name='id' value='$id'>\n";
    $html .= "<input type='hidden' name='action' value='update'>\n";
    $html .= "<button type='submit'>Enregistrer</button>\n</form>\n";
    return $html;
}

function html_form_create_question() {
    $html  = "<form action='admin_questions.php' method='POST' class='form-card'>\n";
    $html .= "<h3>Poser une nouvelle question</h3>\n";
    $html .= "<label>Titre</label><br><input type='text' name='titre' placeholder=\"Ex: Problème d'algèbre...\" required><br>\n";
    $html .= "<label>Explications</label><br><textarea name='content' rows='5' required></textarea><br>\n";
    $html .= "<label>Matière</label><br><input type='text' name='tag_matiere' placeholder='Ex: Maths, Info...'><br>\n";
    $html .= "<input type='hidden' name='action' value='create'>\n";
    $html .= "<button type='submit'>Publier la question</button>\n</form>\n";
    return $html;
}

// Appelé directement (AJAX questions.js) → liste des questions en JSON
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    header('Content-Type: application/json');
    include '../db_connect.php';

    $conditions = [];
    if (!empty($_GET['tag_id'])) $conditions[] = "q.tag_matiere='" . mysqli_real_escape_string($conn, $_GET['tag_id']) . "'";
    if (!empty($_GET['statut']))  $conditions[] = "q.statut='"      . mysqli_real_escape_string($conn, $_GET['statut'])  . "'";
    if (!empty($_GET['search'])) {
        $s = mysqli_real_escape_string($conn, $_GET['search']);
        $conditions[] = "(q.titre LIKE '%$s%' OR q.content LIKE '%$s%')";
    }
    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

    $sql = "SELECT q.id, q.titre, q.content as contenu, q.statut, q.niveau_requis, q.created_at,
                   u.username as auteur, t.name as matiere, t.color as matiere_couleur, q.tag_matiere as tag_id,
                   COUNT(r.id) as nb_reponses
            FROM questions q
            JOIN users u ON u.id = q.id_auteur
            LEFT JOIN tags_matiere t ON t.id = q.tag_matiere
            LEFT JOIN reponses r ON r.id_question = q.id
            $where
            GROUP BY q.id
            ORDER BY q.created_at DESC
            LIMIT 50";

    $res       = mysqli_query($conn, $sql);
    $questions = mysqli_fetch_all($res, MYSQLI_ASSOC);

    foreach ($questions as &$q) {
        $q['date'] = date('d M Y', strtotime($q['created_at']));
        unset($q['created_at']);
    }

    echo json_encode(['success'=>true, 'questions'=>$questions]);
}
?>

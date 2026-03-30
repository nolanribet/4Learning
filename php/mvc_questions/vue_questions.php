<?php
/* * VUE: Composants de l'interface pour les Questions 🎓
 */

/** * Liste des questions sous forme de tableau (ou de liste de cartes)
 */
function html_table_questions($questions) {
    $html = "<table class='table-questions'>\n";
    $html .= "<thead><tr><th>Titre</th><th>Matière</th><th>Auteur</th><th>Actions</th></tr></thead>\n";
    $html .= "<tbody>\n";

    foreach($questions as $question) {
        $html .= html_tr_question($question);    
    }

    $html .= "</tbody></table>\n"; 
    return $html; 
}

/**
 * Ligne du tableau: Titre | Matière | Auteur | Edition | Suppression
 */
function html_tr_question($question) {
    $id      = $question["id"]; 
    $titre   = $question["titre"]; 
    $matiere = $question["tag_matiere"]; 
    // On suppose que tu récupères le nom de l'auteur via une jointure SQL plus tard
    $auteur  = isset($question["pseudo"]) ? $question["pseudo"] : "ID: " . $question["id_auteur"]; 

    $html = "\t<tr>\n"; 
    $html .= "\t\t<td><strong>$titre</strong></td>\n";
    $html .= "\t\t<td><span class='badge'>$matiere</span></td>\n";
    $html .= "\t\t<td>$auteur</td>\n";

    // Boutons d'action
    $html .= "\t\t<td>";
    $html .= html_a_update_question($id);
    $html .= " " . html_a_delete_question($id);
    $html .= "\t\t</td>\n";
    
    $html .= "\t</tr>\n"; 
    return $html;
}

/** Lien de suppression */
function html_a_delete_question($id) {
    $href = "admin_questions.php?action=delete&id=$id"; 
    return "<a href='$href' onclick='return confirm(\"Supprimer cette question ?\")'><img src='delete.png' width='25px'></a>";
}

/** Lien de mise à jour */
function html_a_update_question($id) {
    $href = "admin_questions.php?action=update_form&id=$id"; 
    return "<a href='$href'><img src='pencil.png' width='25px'></a>";
}

/**
 * Formulaire de mise à jour (UPDATE)
 */
function html_form_maj_question($question) {
    $id      = $question["id"]; 
    $titre   = htmlspecialchars($question["titre"]); 
    $content = htmlspecialchars($question["content"]); 
    $matiere = htmlspecialchars($question["tag_matiere"]); 
    
    $html = "<form action='admin_questions.php' method='POST' class='form-card'>\n"; 
    $html .= "<h3>Modifier la question</h3>\n";
    
    $html .= "<label>Sujet</label><br>\n";
    $html .= "<input type='text' name='titre' value='$titre' required><br>\n"; 
    
    $html .= "<label>Contenu de la question</label><br>\n";
    $html .= "<textarea name='content' rows='5' required>$content</textarea><br>\n"; 
    
    $html .= "<label>Matière (Tag)</label><br>\n";
    $html .= "<input type='text' name='tag_matiere' value='$matiere'><br>\n"; 
    
    $html .= "<input type='hidden' name='id' value='$id'>\n"; 
    $html .= "<input type='hidden' name='action' value='update'>\n"; 
    $html .= "<button type='submit'>Enregistrer les modifications</button>\n"; 
    $html .= "</form>\n";

    return $html; 
}

/**
 * Formulaire de création (CREATE)
 */
function html_form_create_question() {
    $html = "<form action='admin_questions.php' method='POST' class='form-card'>\n"; 
    $html .= "<h3>Poser une nouvelle question</h3>\n";
    
    $html .= "<label>Titre de la question</label><br>\n";
    $html .= "<input type='text' name='titre' placeholder='Ex: Problème d'algèbre...' required><br>\n"; 
    
    $html .= "<label>Explications</label><br>\n";
    $html .= "<textarea name='content' placeholder='Détaillez votre blocage ici...' rows='5' required></textarea><br>\n"; 
    
    $html .= "<label>Matière</label><br>\n";
    $html .= "<input type='text' name='tag_matiere' placeholder='Ex: Maths, Info...'><br>\n"; 
    
    $html .= "<input type='hidden' name='action' value='create'>\n"; 
    $html .= "<button type='submit'>Publier la question</button>\n"; 
    $html .= "</form>\n";

    return $html; 
}
?>
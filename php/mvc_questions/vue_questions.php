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
    // C'est CETTE ligne qui garantit que le bouton "Modifier" 
    // correspond à la bonne question !
    $id = $question["id"]; 

    $titre   = $question["titre"]; 
    $matiere = $question["tag_matiere"]; 
    $auteur  = $question["id_auteur"]; 

    $html = "<tr>"; 
    $html .= "<td>$titre</td>";
    $html .= "<td>$matiere</td>";
    $html .= "<td>$auteur</td>";
    $html .= "<td>";
    
    $html .= html_a_update_question($id); 
    $html .= " " . html_a_delete_question($id);
    
    $html .= "</td></tr>"; 
    return $html;
}
/** Lien de suppression */
function html_a_delete_question($id) {
    $href = "admin_questions.php?action=delete&id=$id"; 
    return "<a href='$href' onclick='return confirm(\"Supprimer cette question ?\")'><img src='../../../assets/images/delete-good.png' width='25px'></a>";
}

/** Lien de mise à jour */
function html_a_update_question($id) {
    $href = "admin_questions.php?action=update_form&id=$id"; 
    return "<a href='$href'><img src='../../../assets/images/update.png' width='25px'></a>";
}

/**
 * Formulaire de mise à jour (UPDATE)
 */
function html_form_maj_question($question) {

    // 2. Extraction des données (Lignes 61, 62, 63, 64 qui posaient problème)
    $id      = $question["id"]; 
    $titre   = htmlspecialchars($question["titre"]); 
    $content = htmlspecialchars($question["content"]); 
    $matiere = htmlspecialchars($question["tag_matiere"]); 
    
    // 3. Génération du HTML
    $html = "<form action='admin_questions.php' method='POST' class='form-container'>\n"; 
    $html .= "<h3>Modifier la question #$id</h3>\n";
    
    $html .= "<label>Sujet</label><br>\n";
    $html .= "<input type='text' name='titre' value='$titre' required><br>\n"; 
    
    $html .= "<label>Contenu</label><br>\n";
    $html .= "<textarea name='content' rows='5' required>$content</textarea><br>\n"; 
    
    $html .= "<label>Matière</label><br>\n";
    $html .= "<input type='text' name='tag_matiere' value='$matiere'><br>\n"; 
    
    $html .= "<input type='hidden' name='id' value='$id'>\n"; 
    $html .= "<input type='hidden' name='action' value='update'>\n"; 
    $html .= "<button type='submit'>Enregistrer</button>\n"; 
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
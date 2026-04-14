<?php
/* * VUE: Composants de l'interface pour les Matières (Tags)
 */

/** * Liste des matières sous forme de tableau
 */
function html_table_matieres($matieres) {
    $html = "<table class='table-matieres'>\n";
    $html .= "<thead><tr><th>Nom</th><th>Couleur</th><th>Aperçu</th><th>Actions</th></tr></thead>\n";
    $html .= "<tbody>\n";

    foreach($matieres as $matiere) {
        $html .= html_tr_matiere($matiere);    
    }

    $html .= "</tbody></table>\n"; 
    return $html; 
}

/** * Ligne du tableau pour une matière
 */
function html_tr_matiere($matiere) {
    $id    = $matiere["id"]; 
    $name  = $matiere["name"]; 
    $color = $matiere["color"]; 

    $html = "<tr>"; 
    $html .= "<td>$name</td>";
    $html .= "<td><code>$color</code></td>";
    // Petit aperçu visuel de la couleur 🌈
    $html .= "<td><div style='width:20px; height:20px; border-radius:50%; background-color:$color;'></div></td>";
    $html .= "<td>";
    
    $html .= html_a_update_matiere($id); 
    $html .= " " . html_a_delete_matiere($id);
    
    $html .= "</td></tr>"; 
    return $html;
}

/** Liens d'actions (Update / Delete) 🔗 */
function html_a_delete_matiere($id) {
    $href = "admin_matieres.php?action=delete&id=$id"; 
    return "<a href='$href' onclick='return confirm(\"Supprimer cette matière ?\")'><img src='../../../assets/images/delete-good.png' width='25px'></a>";
}

function html_a_update_matiere($id) {
    $href = "admin_matieres.php?action=update_form&id=$id"; 
    return "<a href='$href'><img src='../../../assets/images/update.png' width='25px'></a>";
}

/** * Formulaire de modification d'une matière
 */
function html_form_maj_matiere($matiere) {
    $id    = $matiere["id"]; 
    $name  = htmlspecialchars($matiere["name"]); 
    $color = htmlspecialchars($matiere["color"]); 
    
    $html = "<form action='admin_matieres.php' method='POST' class='form-container'>\n"; 
    $html .= "<h3>Modifier la matière : $name</h3>\n";
    
    $html .= "<label>Nom de la matière</label><br>\n";
    $html .= "<input type='text' name='name' value='$name' required><br>\n"; 
    
    $html .= "<label>Couleur (Hexadécimal)</label><br>\n";
    $html .= "<input type='color' name='color' value='$color'><br>\n"; // Utilisation du sélecteur de couleur HTML5 🎨
    
    $html .= "<input type='hidden' name='id' value='$id'>\n"; 
    $html .= "<input type='hidden' name='action' value='update'>\n"; 
    $html .= "<button type='submit'>Mettre à jour</button>\n"; 
    $html .= "</form>\n";

    return $html; 
}

/** * Formulaire de création d'une matière
 */
function html_form_create_matiere() {
    $html = "<form action='admin_matieres.php' method='POST' class='form-card'>\n"; 
    $html .= "<h3>Ajouter une nouvelle matière</h3>\n";
    
    $html .= "<label>Nom</label><br>\n";
    $html .= "<input type='text' name='name' placeholder='Ex: Algorithmique' required><br>\n"; 
    
    $html .= "<label>Couleur</label><br>\n";
    $html .= "<input type='color' name='color' value='#3498db'><br>\n"; 
    
    $html .= "<input type='hidden' name='action' value='create'>\n"; 
    $html .= "<button type='submit'>Créer la matière</button>\n"; 
    $html .= "</form>\n";

    return $html; 
}
?>
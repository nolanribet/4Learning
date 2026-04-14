<?php
session_start();

include '../db_connect.php';
include 'crud_tags.php';
include 'vue_tags.php';

?>
<html>
<head>
    <title>4Learning - Gestion des Matières</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table tr td, table tr th { border: 1px solid #ddd; padding: 12px; text-align: left; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .btn-add { display: inline-block; padding: 10px 15px; background: #2ecc71; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        .form-container { background: #eee; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>

<header>
    <h1>🎓 4Learning - Panel des matières</h1>
    <hr>
    <p><a href="../../index.php">🏠 Retour à l'accueil</a></p>
</header>

<?php

/** * 1. CONTRÔLEUR : Initialisation des variables GET 🔍
 */

// On crée les variables avec une valeur par défaut
$action = ""; 
$id = 0;

// On vérifie si l'action est présente dans l'URL
if (isset($_GET["action"])) {
    $action = $_GET["action"];
}

// On vérifie si l'ID est présent dans l'URL
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

// --- AFFICHAGE DES FORMULAIRES ---

if ($action == "create_form") {
    echo "<div class='form-container'>" . html_form_create_matiere() . "</div>";
}
elseif ($action == "update_form") {
    $res = readTags($conn, $id);
    $matiere = mysqli_fetch_assoc($res);
    
    if ($matiere) {
        echo "<div class='form-container'>". html_form_maj_matiere($matiere) . "</div>";
    }
}
elseif ($action == "delete") {
    deleteTags($conn, $id);
    echo "<p style='color:red;'> Matière supprimée.</p>";
}

/** * 2. CONTRÔLEUR : Traitement des données POST 
 */
if (isset($_POST["action"])) {
    
    $post_action = $_POST["action"];
    $name = $_POST["name"];
    $color = $_POST["color"]; 

    if ($post_action == "update") {
        if (isset($_POST["id"])) {
            $id_to_update = $_POST["id"];
            updateTags($conn, $name, $color, $id_to_update);
            echo "<p style='color:blue;'>✨ Matière mise à jour !</p>";
        }
    } 
    elseif ($post_action == "create") {
        createTags($conn, $name, $color);
        echo "<p style='color:green;'>✅ Nouvelle matière ajoutée !</p>";
    }
}

/** * 3. AFFICHAGE : Liste des matières 📋
 */
echo "<h3>Toutes les matières :</h3>";

$res_list = listTags($conn);
$all_tags = []; 

// Boucle pour remplir le tableau PHP
while ($row = mysqli_fetch_assoc($res_list)) {
    $all_tags[] = $row; 
}

// Affichage final via la fonction de vue
echo html_table_matieres($all_tags);

?>

<br>
<a href="admin_tags.php?action=create_form" class="btn-add">➕ Ajouter une matière</a>

</body>
</html>
<?php
session_start();
include '../db_connect.php';
include 'crud_tags.php';
include 'vue_tags.php';

$action = ""; 
$id = 0;
if (isset($_GET["action"])) {
    
$action = $_GET["action"]; }

if (isset($_GET["id"])) { 
    
    $id = $_GET["id"]; }


// --- TRAITEMENT POST ---
if (isset($_POST["action"])) {
    $post_action = $_POST["action"];
    $name = $_POST["name"];
    $color = $_POST["color"]; 

    if ($post_action == "update" && isset($_POST["id"])) {
        updateTags($conn, $name, $color, $_POST["id"]);
        echo "<p style='color:blue;'>✨ Mise à jour réussie !</p>";
    } 
    elseif ($post_action == "create") {
        createTags($conn, $name, $color);
        echo "<p style='color:green;'>✅ Ajout réussi !</p>";
    }
}

// --- TRAITEMENT DELETE ---
if ($action == "delete") {
    deleteTags($conn, $id);
    echo "<p style='color:red;'>✅ Matière supprimée.</p>";
}
?>
<html>
<head>
    <title>4Learning - Gestion des Matières</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table tr td, table tr th { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .form-container { background: #eee; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .btn-add { display: inline-block; padding: 10px 15px; background: #2ecc71; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <header>
        <h1>🎓 4Learning - Panel des matières</h1>
        <hr>
        <p><a href="../../index.php">🏠 Accueil</a></p>
    </header>

    <?php
    if ($action == "create_form") {
        echo "<div class='form-container'>" . html_form_create_matiere() . "</div>";
    }
    elseif ($action == "update_form") {
        $res = readTags($conn, $id);
        $matiere = mysqli_fetch_assoc($res);
        if ($matiere) echo "<div class='form-container'>". html_form_maj_matiere($matiere) . "</div>";
    }

    echo "<h3>Toutes les matières :</h3>";
    $res_list = listTags($conn);
    $all_tags = []; 
    while ($row = mysqli_fetch_assoc($res_list)) { $all_tags[] = $row; }
    echo html_table_matieres($all_tags);
    ?>
    <br>
    <a href="admin_tags.php?action=create_form" class="btn-add">➕ Ajouter une matière</a>
</body>
</html>
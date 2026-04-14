<?php
session_start();

include '../db_connect.php';
include 'crud_tags.php';
include 'vue_tags.php';

/** * 1. TRAITEMENT DES ACTIONS (SQL) 
 * On place ce bloc en haut pour que les modifs soient visibles immédiatement
 */
$action = ""; 
$id = 0;

if (isset($_GET["action"])) { $action = $_GET["action"]; }
if (isset($_GET["id"])) { $id = $_GET["id"]; }

// --- Traitement des envois de formulaires (POST) ---
if (isset($_POST["action"])) {
    $post_action = $_POST["action"];
    $name = $_POST["name"];
    $color = $_POST["color"]; 

    if ($post_action == "update" && isset($_POST["id"])) {
        $id_to_update = $_POST["id"];
        $res = updateTags($conn, $name, $color, $id_to_update);
        
        if($res) {
            echo "<p style='color:blue;'>✨ Matière mise à jour !</p>";
        } else {
            echo "<p style='color:red;'>❌ Erreur SQL : " . mysqli_error($conn) . "</p>";
        }
    } 
    elseif ($post_action == "create") {
        $res = createTags($conn, $name, $color);
        
        if($res) {
            echo "<p style='color:green;'>✅ Nouvelle matière ajoutée !</p>";
        } else {
            echo "<p style='color:red;'>❌ Erreur SQL : " . mysqli_error($conn) . "</p>";
        }
    }
}

// --- Traitement de la suppression (GET) ---
if ($action == "delete" && $id > 0) {
    deleteTags($conn, $id);
    echo "<p style='color:red;'>🗑️ Matière supprimée.</p>";
}
?>

<html>
<head>
    <title>4Learning - Gestion des Matières</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table tr td, table tr th { border: 1px solid #ddd; padding: 12px; text-align: left; }
        tr:nth-child(even) { background-color: #f9f9f9; }
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
/** * 2. AFFICHAGE DES FORMULAIRES 📝
 */
if ($action == "create_form") {
    echo "<div class='form-container'>" . html_form_create_matiere() . "</div>";
}
elseif ($action == "update_form" && $id > 0) {
    $res = readTags($conn, $id);
    $matiere = mysqli_fetch_assoc($res);
    
    if ($matiere) {
        echo "<div class='form-container'>". html_form_maj_matiere($matiere) . "</div>";
    }
}

/** * 3. AFFICHAGE DE LA LISTE 
 */
echo "<h3>Toutes les matières :</h3>";

$res_list = listTags($conn);
$all_tags = []; 
while ($row = mysqli_fetch_assoc($res_list)) {
    $all_tags[] = $row; 
}

echo html_table_matieres($all_tags);
?>

<br>
<a href="admin_tags.php?action=create_form" class="btn-add">➕ Ajouter une matière</a>

</body>
</html>
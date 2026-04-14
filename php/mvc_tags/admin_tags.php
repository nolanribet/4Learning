<?php
session_start();

include '../db_connect.php';
include 'crud_tags.php';
include 'vue_tags.php';




?>
<html>
<head>
    <title>4Learning - Gestion des Matieres</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table tr td, table tr th { border: 1px solid #ddd; padding: 12px; }
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
    <h1><a href="../../../index.php">Retour a l'acceuil</a></h1>
</header>

<?php

//Controleur de l'action GET(savoir quelle action nous faisons)
if(isset($_GET["action"])){

    $action = $_GET["action"];
    if(isset($_GET["id"])){
        $id = $_GET["id"];
    }else{
        $id = 0;
    }
}

if($action == "create_form"){
    echo "<div class='form-container'>" . html_form_create_matiere() . "</div>";
}
elseif($action == "update_form"){
    $res = readTags($conn, $id);
    $matiere = mysqli_fetch_assoc($res);
    echo "<div class='form-container'>". html_form_maj_matiere($matiere) . "</div>";
}
elseif($action == "delete"){
    deleteTags($conn, $id);
    echo "<p style='color : red;'> Matière Supprimé </p>";
}

//Controleur Action POST (Affichages des formulaire pour chaque action precedemennt données)
if(isset($_POST["action"])){
    $post_action = $_POST["action"];
    $name = $_POST["name"];
    $couleur = $_POST["color"];
    if($post_action == "update"){
        $id_to_update = $_POST["id"];
        updateTags($conn, $name, $color, $id_to_update);
        echo "<p style='color:blue;'>Matière mise à jour ! ✨</p>";

    } elseif($post_action == "create"){
        createTags($conn, $name, $color);
        echo "<p style='color:green;'>Nouvelle matière ajoutée ! ✅</p>";
    }
}

//AFFICHAGE DES MATIERES

echo "<h3>Toutes les matières :</h3>";
$res_list = listTags($conn);
$all_tags = []; 

while ($row = mysqli_fetch_assoc($res_list)) {
    
    $all_tags[] = $row; 
    
}

echo html_table_matieres($all_tags);


?>


<br>
<a href="admin_questions.php?action=create_form" class="btn-add">➕ Poser une question</a>

</body>
</html>

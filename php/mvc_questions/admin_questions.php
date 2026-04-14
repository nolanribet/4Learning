<?php
include("../../db_connect.php"); 
include("crud_questions.php"); 
include("vue_questions.php");  


session_start();
/* // Optionnel : Décommenter si tu as une page de login
if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}
*/
?>
<html>
<head>
    <title>4Learning - Gestion des Questions</title>
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
    <h1>🎓 4Learning - Panel d'Entraide</h1>
    <hr>
    <h1><a href="../../../index.php">Retour a l'acceuil</a></h1>
</header>

<?php

/**
 * CONTRÔLEUR : Traite les actions 
 */
if(isset($_GET["action"])){

    $action = $_GET["action"];
    // Si on a un ID on le prend, sinon on met 0 (utile pour le formulaire de création)
    $id = isset($_GET["id"]) ? $_GET["id"] : 0;

    if($action == "update_form"){
        /* Affiche le formulaire de modif d'une question */
        $question = readQuestion($conn, $id);
        echo "<div class='form-container'>";
        echo html_form_maj_question($question);
        echo "</div>";          
        
    } elseif($action == "create_form"){
        /* Affiche le formulaire de création */
        echo "<div class='form-container'>";
        echo html_form_create_question();
        echo "</div>"; 
    
    } elseif($action == "delete"){
        /* On utilise bien la fonction pour les questions ! */
        deleteQuestion($conn, $id);
        echo "<p style='color:red;'>Question supprimée.</p>";
    }
} 

/**
 * CONTRÔLEUR : Traite les actions POST 
 */
if(isset($_POST["action"])){
    $action = $_POST["action"];
    
    // On récupère les champs du formulaire
    $titre   = $_POST["titre"];
    $content = $_POST["content"];
    $tag     = $_POST["tag_matiere"];

    if($action == "update" && isset($_POST["id"])){
        /* Traitement de la modification */
        $id = intval($_POST["id"]);
        updateQuestion($conn, $id, $titre, $content, $tag);
        echo "<p style='color:blue;'>Question mise à jour !</p>";

    } elseif($action == "create"){
        $res = create_question($conn, $titre, $content, $tag, 1, 1);
        
        if($res){
            echo "<p style='color:green;'>Question publiée !</p>";
        } else {
            echo "<p style='color:red;'>Erreur SQL : " . mysqli_error($conn) . "</p>";
        }
    }
}


/**
 * AFFICHAGE : Liste des questions
 */
echo "<h3>Questions en cours</h3>";
$sql = "SELECT * FROM questions ORDER BY created_at DESC";
$res = mysqli_query($conn, $sql);
$all_questions = mysqli_fetch_all($res, MYSQLI_ASSOC);

echo html_table_questions($all_questions);

?>

<br>
<a href="admin_questions.php?action=create_form" class="btn-add">➕ Poser une question</a>

</body>
</html>

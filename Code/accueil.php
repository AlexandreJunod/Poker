<!--====================================================================================================================
||  Auteur : Junod Alexandre                                                                                           ||
||  Derniere modification : 09.03.2018                                                                                 ||
||  Résumé : Page de jeux, les joueurs peuvent rejoindre une table et jouer une partie de poker                        ||
||====================================================================================================================-->

<?php
//----------------------------- Démarrage SESSION ----------------------------------------

session_start();
require_once("includes/fonctions.php");
ConnectDB();

//----------------------------- Traitement SESSION ---------------------------------------

if(isset($_SESSION['Pseudo']))
{
    $Pseudo = $_SESSION['Pseudo'];
}

if(isset($_SESSION['TablePleine'])) //Message d'erreur, s'il a rejoint la table lorsqu'elle était pleine
{
    echo "<div class='ErrorMsg'>Partie en cours, veuillez ressayer plus tard</div>";
}

//----------------------------- Traitement POST ------------------------------------------


//----------------------------- Traitement GET -------------------------------------------

//QUE DU PHP JUSQU'ICI
//----------------------------- Génération de la page-------------------------------------
//HTML + PHP depuis ici

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="includes/style.css"/>
        <title><?php echo $TitleTab; ?></title>
    </head>
    <body>
        Hello world, there is the lobby
    </body>
</html>

<?php
//----------------------------- Sauvegarde de SESSION ------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
?>
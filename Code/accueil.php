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

if(isset($_SESSION['Erreur'])) //Message d'erreur, s'il a rejoint la table lorsqu'elle était pleine
{
    echo "<div class='ErrorMsg'>Partie en cours, veuillez ressayer plus tard</div>";
    echo "<script>setInterval(function(){location.reload()},2000);</script>"; //Refresh la page, code donné par mon chef de projet. Evite que le message reste toujours affiché
}

//----------------------------- Traitement POST ------------------------------------------

if(isset($_POST['RejoindreTable']))
{
    header('Location: table.php');
}

if(isset($_POST['Deconnexion']))
{
    unset($_SESSION);
    header('Location: index.php');
}

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
    <body background="includes\images\TablePoker.jpg">
        <div class="InfosJoueur"><?php echo $Pseudo; ?>
            <form method="post" id="DeconnexionForm"></form>
            <button type="submit" form="DeconnexionForm" name="Deconnexion">Déconnexion</button>
        </div>
        <form method="post" id="RejoindreTableForm"></form>
        <div class="ContainerAccueil"><button type="submit" form="RejoindreTableForm" name="RejoindreTable">Rejoindre la table</button></div>
    </body>
</html>

<?php
//----------------------------- Sauvegarde de SESSION ------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
unset($_SESSION['Erreur']);
?>
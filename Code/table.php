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
    
    //Regarde s'il reste un siège disponible.
    $query = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NULL ORDER BY fkJoueurSiege ASC LIMIT 1";
    $CheckSieges = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Le joueur quitte la table
    //UPDATE poker.siege SET fkJoueurSiege=NULL WHERE idSiege='1';
    
    if($CheckSieges->rowCount() > 0) // S'assure qu'un siège a été trouvé
    {
        $CheckSiege = $CheckSieges->fetch(); //fetch -> aller chercher
        extract($CheckSiege); //$idSiege
        
        //Attribue un siège a un joueur
        $query2 = "UPDATE poker.siege SET fkJoueurSiege=(SELECT idJoueur FROM poker.joueur WHERE PseudoJoueur='$Pseudo') WHERE idSiege='$idSiege';";
        $dbh->query($query2) or die ("SQL Error in:<br> $query2 <br>Error message:".$dbh->errorInfo()[2]);
    } 
    else // Aucun siège trouvé, le renvoye a la page d'accueil avec un message d'erreur
    {
        $_SESSION['TablePleine'] = '1';
        header('Location: accueil.php');
    }
    
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
        Hello world, there is your table
    </body>
</html>

<?php
//----------------------------- Sauvegarde de SESSION ------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
?>
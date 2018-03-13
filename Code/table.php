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
    
    //Recherche l'argent que le joueur possède, et son id
    $query = "SELECT idSiege, ArgentSiege FROM poker.joueur INNER JOIN poker.siege ON joueur.idJoueur=siege.fkJoueurSiege WHERE fkJoueurSiege=(SELECT idJoueur FROM poker.joueur WHERE PseudoJoueur='$Pseudo')";
    $Jetons = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde s'il reste un siège disponible.
    $query = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NULL ORDER BY fkJoueurSiege ASC LIMIT 1";
    $CheckSieges = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
    if($Jetons->rowCount() > 0) //Recherche l'argent du joueur
    {
        $Jeton = $Jetons->fetch(); //fetch -> aller chercher
        extract($Jeton); //$idSiege, $ArgentSiege
        $ArgentSiege = number_format ($ArgentSiege, $decimals = 0 ,$dec_point = "." , $thousands_sep = "'" );
    }
    else //Si l'id et l'argent n'as pas été trouver, l'utilisateur n'est pas encore sur une chaise
    {
        if($CheckSieges->rowCount() > 0) // S'assure qu'un siège a été trouvé
        {
            $CheckSiege = $CheckSieges->fetch(); //fetch -> aller chercher
            extract($CheckSiege); //$idSiege

            //Attribue un siège a un joueur et lui donne 150'000$
            $query = "UPDATE poker.siege SET ArgentSiege='150000', fkJoueurSiege=(SELECT idJoueur FROM poker.joueur WHERE PseudoJoueur='$Pseudo') WHERE idSiege='$idSiege'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
            header('Location: table.php'); //Refresh la page, afin de pouvoir afficher la somme que le joueur possède
        } 
        else // Aucun siège trouvé, le renvoye a la page d'accueil avec un message d'erreur
        {
            $_SESSION['TablePleine'] = '1';
            header('Location: accueil.php');
        }
    }
}

//----------------------------- Traitement POST ------------------------------------------

// Remet la chaise dans son état initial, et renvoie le joueur a la page d'accueil 
if(isset($_POST['SeLever']))
{
    //Le joueur quitte la table
    $query = "UPDATE poker.siege SET ArgentSiege=NULL, MainSiege=NULL, fkJoueurSiege=NULL WHERE idSiege='$idSiege'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    header('Location: accueil.php');
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
        <div class="InfosJoueur"><?php echo "$Pseudo<br>$ArgentSiege$"; ?>
            <form method="post" id="SeLeverForm"></form>
            <button type="submit" form="SeLeverForm" name="SeLever">Se lever</button>
        </div>
    </body>
</html>

<?php
//----------------------------- Sauvegarde de SESSION ------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
?>
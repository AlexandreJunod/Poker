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
    
    //Regarde combien de places libre il reste
    $query = "SELECT COUNT(idSiege) AS Places FROM poker.siege WHERE fkJoueurSiege IS NULL AND fkEtatSiege='1'";
    $SiegesVides = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde quelles places sont occupées, afin d'afficher les différents joueurs et leurs noms
    $query = "SELECT idSiege, ArgentSiege, PseudoJoueur FROM poker.joueur INNER JOIN poker.siege ON joueur.idJoueur=siege.fkJoueurSiege WHERE fkJoueurSiege IS NOT NULL AND fkJoueurSiege=idJoueur";
    $SiegesOccupes = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde si la partie à déjà commencer
    $query = "SELECT HeureDebutPartie FROM poker.partie WHERE HeureDebutPartie IS NOT NULL";
    $PartieEnCours = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    foreach($SiegesOccupes as $SiegeOccupe) //Indique le numéro des places occupées
    {
        $PlacePrise = $SiegeOccupe['idSiege'];
        $PlacePseudo = $SiegeOccupe['PseudoJoueur'];
        $PlaceArgent = number_format ($SiegeOccupe['ArgentSiege'], $decimals = 0, $dec_point = ".", $thousands_sep = "'" ); //Format de nombre, afin de montrer les milliers plus facilement
        echo "<div class='Joueur$PlacePrise'>$PlacePseudo<br>$PlaceArgent$</div>";
    }
            
    if($Jetons->rowCount() > 0) //Recherche l'argent du joueur
    {
        $Jeton = $Jetons->fetch(); //fetch -> aller chercher
        extract($Jeton); //$idSiege, $ArgentSiege
        $ArgentSiege = number_format ($ArgentSiege, $decimals = 0, $dec_point = ".", $thousands_sep = "'" ); //Format de nombre, afin de montrer les milliers plus facilement
        
        if($SiegesVides->rowCount() > 0) //Donne le nombre de joueurs manquants
        {
            $SiegesVide = $SiegesVides->fetch(); //fetch -> aller chercher
            extract($SiegesVide); //$Places
            
            if($PartieEnCours->rowCount() == 0) //S'assure que la partie n'as pas encore commencer // PROBLEME ICI, JE NE SAIS PAS OU PLACER SA POUR PASSER QUE LES JOUEURS QUI REJOIGNENT EN MILLIEU DE PARTE PASSE EN FKETATSIEGE = 2
            {
                if($Places > 0) // Compte le nombre de joueurs manquants
                {
                    echo "<div class='ErrorMsg'>En attente de $Places joueurs</div>";
                }
                else //Il ne manque plus aucun joueur
                {           
                    date_default_timezone_set('Europe/Berlin'); //Règle l'heure en UTC+01:00
                    $heure = date('H:i:s');

                    //La partie commence
                    $query = "UPDATE poker.siege SET fkEtatSiege='2' WHERE idSiege='$idSiege' AND fkEtatSiege = '1'";
                    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

                    //L'heure du début de partie est enregistrée
                    $query = "UPDATE poker.partie SET HeureDebutPartie='$heure'";
                    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

                    echo "<div class='ErrorMsg'>La partie à commencé</div>";
                }
            }
        }
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
    $query = "UPDATE poker.siege SET ArgentSiege=NULL, MainSiege=NULL, fkEtatSiege='1', fkJoueurSiege=NULL WHERE idSiege='$idSiege'";
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
    <script>setInterval(function(){location.reload()},3000);</script> <!-- Refresh la page, code donné par mon chef de projet. Rend le jeu plus fluide --> 
</html>

<?php
//----------------------------- Sauvegarde de SESSION ------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
?>
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
    
    //Regarde qui est le dealer, le small blind et le big blind
    $query = "SELECT DealerPartie, SmallBlindPartie, BigBlindPartie FROM poker.partie";
    $JetonJoueurs = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde quel est le prochain joueur après le dealer
    $query = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL and idSiege > (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) and idSiege != (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) and idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    $Dealers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde quel est le prochain joueur après le dealer en recommençant depuis le premier joueur a table
    $query = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL and idSiege != (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) and idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    $DealersRetourTable = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde quel est le prochain joueur après le small blind
    $query = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL and idSiege > (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) and idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) and idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    $SmallBlinds = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde quel est le prochain joueur après le small blind en recommençant depuis le premier joueur a table
    $query = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL and idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) and idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    $SmallBlindsRetourTable = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde quel est le prochain joueur après le big blind
    $query = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL and idSiege > (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) and idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) and idSiege != (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    $BigBlinds = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde quel est le prochain joueur après le big blind en recommençant depuis le premier joueur a table
    $query = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL and idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) and idSiege != (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    $BigBlindsRetourTable = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde si la partie à déjà commencer
    $query = "SELECT HeureDebutPartie FROM poker.partie WHERE HeureDebutPartie IS NOT NULL";
    $PartieEnCours = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
                
    if($Jetons->rowCount() > 0) //Recherche l'argent du joueur
    {
        $Jeton = $Jetons->fetch(); //fetch -> aller chercher
        extract($Jeton); //$idSiege, $ArgentSiege
        $ArgentSiege = number_format ($ArgentSiege, $decimals = 0, $dec_point = ".", $thousands_sep = "'" ); //Format de nombre, afin de montrer les milliers plus facilement
        
        if($SiegesVides->rowCount() > 0) //Regarde si la table est pleine
        {
            $SiegesVide = $SiegesVides->fetch(); //fetch -> aller chercher
            extract($SiegesVide); //$Places
            
            if($PartieEnCours->rowCount() == 0) //S'assure que la partie n'as pas encore commencer
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
            else //La partie est en cours, le joueur qui rejoins en pleine partie, se retrouve en jeu
            {
                //Change l'etat du joueur qui à rejoins la table
                $query = "UPDATE poker.siege SET fkEtatSiege='2' WHERE idSiege='$idSiege' AND fkEtatSiege = '1'";
                $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
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
    
    $PremierePlace = 7 - $idSiege; //Permet de savoir de combien de place il faut avancer, pour se trouver a la première place
    
    foreach($SiegesOccupes as $SiegeOccupe) //Indique le numéro des places occupées
    {
        $PlacePrise = $SiegeOccupe['idSiege'];
        $PlacePseudo = $SiegeOccupe['PseudoJoueur'];
        $PlaceArgent = number_format ($SiegeOccupe['ArgentSiege'], $decimals = 0, $dec_point = ".", $thousands_sep = "'" ); //Format de nombre, afin de montrer les milliers plus facilement
        
        for($i = 1; $i <= $PremierePlace; $i++) //Avance le joueur du nombre de places pour se trouvé à la première place
        {
            $PlacePrise++;
            if($PlacePrise == 7) //Quand le calcul dépasse la place numéro 7, qui n'existe pas, il recommenc à la place 1
            {
                $PlacePrise = 1;
            }
        }        
        
        echo "<div class='Joueur$PlacePrise'>$PlacePseudo<br>$PlaceArgent$</div>"; //Affiche les joueurs
        
        /* =============== Ancien système de calcul de places, pas adapté pour les blinds ===============
        echo "User : $Pseudo, Place BD : $idSiege, Nb tour a tourné : $PremierePlace, place occupée : $PlacePrise";
        
        echo "<div class='Joueur1'>$Pseudo<br>$ArgentSiege$</div>"; //Le joueur se voit toujours à la première place
        
        if($idSiege == 1)
        {
            echo "<div class='Joueur$PlacePrise'>$PlacePseudo<br>$PlaceArgent$</div>"; 
        }
        else if($PlacePrise < $idSiege)
        {
            $PlacePrise++;
            echo $PlacePrise;
            echo "<div class='Joueur$PlacePrise'>$PlacePseudo<br>$PlaceArgent$</div>";
        }
        else if($PlacePrise != $idSiege)
        {
            echo "<div class='Joueur$PlacePrise'>$PlacePseudo<br>$PlaceArgent$</div>"; 
        }*/
    }
    
    // Affiche le jeton de tous les joueurs
    if($JetonJoueurs->rowCount() > 0 && $PartieEnCours->rowCount() != 0) //S'assure que les jetons sont assignés aux joueurs et que la partie a commencer
    {
        //Affiche le dealer, le small blind et le big blind
        $JetonJoueur = $JetonJoueurs->fetch(); //fetch -> aller chercher
        $LeDealer = $JetonJoueur['DealerPartie'];
        $LeSmallBlind = $JetonJoueur['SmallBlindPartie'];
        $LeBigBlind = $JetonJoueur['BigBlindPartie'];

        for($i = 1; $i <= $PremierePlace; $i++) //Le jeton fait autant de nombres de tours que le joueur à fait
        {
            $LeDealer++;
            $LeSmallBlind++;
            $LeBigBlind++;

            if($LeDealer == 7) 
            {
                $LeDealer = 1;
            }

            if($LeSmallBlind == 7)
            {
                $LeSmallBlind = 1;
            }

            if($LeBigBlind == 7)
            {
                $LeBigBlind = 1;
            }
        }     
        //Affiche les jetons
        echo "<div class='Jeton$LeDealer'>D</div>";
        echo "<div class='Jeton$LeSmallBlind'>SB</div>";
        echo "<div class='Jeton$LeBigBlind'>BB</div>";
    }
}

//----------------------------- Traitement POST ------------------------------------------

// Remet la chaise dans son état initial, et renvoie le joueur a la page d'accueil 
if(isset($_POST['SeLever']))
{       
    if($SiegesVides->rowCount() > 0) //Regarde si la table est pleine
    {
        $SiegesVide = $SiegesVides->fetch(); //fetch -> aller chercher
        extract($SiegesVide); //$Places
        
        if($Places >=4 ) //Supprime l'heure de début de partie, quand il ne reste plus que 1 ou 0 joueurs à table
        {            
            //L'heure du début de partie est enlevé
            $query = "UPDATE poker.partie SET HeureDebutPartie=NULL";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
            
            //Le dernier joueur passe en mode "attente"
            $query = "Update poker.siege SET fkEtatSiege='1' WHERE fkEtatSiege='2'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
    }
    
    //Le joueur quitte la table
    $query = "UPDATE poker.siege SET ArgentSiege=NULL, MainSiege=NULL, fkEtatSiege='1', fkJoueurSiege=NULL WHERE idSiege='$idSiege'";
    $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    header('Location: accueil.php');
}

// Passe à la prochaine main
if(isset($_POST['ProchaineMain']))
{
    if($Places < 3) //S'assure qu'il reste au moins 3 joueurs
    {
        if($Dealers->rowCount() > 0) //S'assure qu'il y a un joueur après le Dealer
        {
            $Dealer = $Dealers->fetch(); //fetch -> aller chercher
            extract($Dealer); //$idSiege
            
            //Rend le prochain joueur dealer
            $query = "UPDATE poker.partie SET DealerPartie ='$idSiege'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
        else
        {
            $DealerRetourTable = $DealersRetourTable->fetch(); //fetch -> aller chercher
            extract($DealerRetourTable); //$idSiege
            
            //Rend le prochain joueur dealer, en recommençant depuis le premier joueur de la table
            $query = "UPDATE poker.partie SET DealerPartie ='$idSiege'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
        
        if($SmallBlinds->rowCount() > 0) //S'assure qu'il y a un joueur après le Small Blind
        {
            $SmallBlind = $SmallBlinds->fetch(); //fetch -> aller chercher
            extract($SmallBlind); //$idSiege
            
            //Rend le prochain joueur small blind
            $query = "UPDATE poker.partie SET SmallBlindPartie ='$idSiege'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
        else
        {
            $SmallBlindRetourTable = $SmallBlindsRetourTable->fetch(); //fetch -> aller chercher
            extract($SmallBlindRetourTable); //$idSiege
            
            //Rend le prochain joueur dealer, en recommençant depuis le premier joueur de la table
            $query = "UPDATE poker.partie SET SmallBlindPartie ='$idSiege'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
        
        if($BigBlinds->rowCount() > 0) //S'assure qu'il y a un joueur après le Big Blind
        {
            $BigBlind = $BigBlinds->fetch(); //fetch -> aller chercher
            extract($BigBlind); //$idSiege
            
            //Rend le prochain joueur big blind
            $query = "UPDATE poker.partie SET BigBlindPartie ='$idSiege'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
        else
        {
            $BigBlindRetourTable = $BigBlindsRetourTable->fetch(); //fetch -> aller chercher
            extract($BigBlindRetourTable); //$idSiege
            
            //Rend le prochain joueur dealer, en recommençant depuis le premier joueur de la table
            $query = "UPDATE poker.partie SET BigBlindPartie ='$idSiege'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
    }
    header('Location: table.php'); //Empêcher le formulaire de se renvoyer en boucle
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
        <?php 
        if($Pseudo == 'Alexandre')
        {
            ?>
            <div class="Button">
                <form method="post" id="ProchaineMainForm"></form>
                <button type="submit" form="ProchaineMainForm" name="ProchaineMain">Prochaine main</button>
            </div>
            <?php 
        }
        ?>
    </body>
    <script>setInterval(function(){location.reload()},3000);</script> <!-- Refresh la page, code donné par mon chef de projet. Rend le jeu plus fluide --> 
</html>

<?php
//----------------------------- Sauvegarde de SESSION ------------------------------------

$_SESSION['Pseudo'] = $Pseudo;
?>
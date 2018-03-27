<meta charset='utf-8'/>

<?php
//Création d'une variable supergloable qui permet de changer le titre de l'onglet
$GLOBALS['TitleTab'] = 'Poker online';

//Connection à base de données
function ConnectDB()
{
    // Toutes les infos nécessaires pour la connexion à une base de donnée
    $hostname = 'localhost';
    $dbname = 'poker';
    $username = 'root';
    $password = '';

    // PDO = Persistant Data Object
    // Entre "" = Connection String
    $connectionString = "mysql:host=$hostname; dbname=$dbname";

    global $dbh; 

    try
    {
        $dbh = new PDO($connectionString, $username, $password);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->exec("SET NAMES UTF8");
    }
    catch(PDOException $e)
    {
        die("Erreur de connexion au serveur (".$e->getMessage().")");
    }
}

function TournerLesJetons()
{    
    global $dbh;
    
    //Regarde combien de places libre il reste
    $query = "SELECT COUNT(idSiege) AS Places FROM poker.siege WHERE fkJoueurSiege IS NULL AND fkEtatSiege='1'";
    $SiegesVides = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde quel est le prochain joueur après le big blind
    $queryBB = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL AND idSiege > (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) AND idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) AND idSiege != (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    
    //Regarde quel est le prochain joueur après le big blind en recommençant depuis le premier joueur a table
    $queryBBT = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL AND idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) AND idSiege != (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    
    //Regarde quel est le prochain joueur après le small blind
    $querySB = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL AND idSiege > (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) AND idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) AND idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    
    //Regarde quel est le prochain joueur après le small blind en recommençant depuis le premier joueur a table
    $querySBT = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL AND idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) AND idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    
    //Regarde quel est le prochain joueur après le dealer
    $queryD = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL AND idSiege > (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) AND idSiege != (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) AND idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    
    //Regarde quel est le prochain joueur après le dealer en recommençant depuis le premier joueur a table
    $queryDT = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL AND idSiege != (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) AND idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    
    //Donne le numéro de siege du dealer
    $query = "SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL";
    $SiegeDealers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Donne le numéro de siege du small blind
    $query = "SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL";
    $SiegeSmallBlinds = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Donne le numéro de siege du big blind
    $query = "SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL";
    $SiegeBigBlinds = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    
    if($SiegesVides->rowCount() > 0) //Regarde si la table est pleine
    {
        $SiegesVide = $SiegesVides->fetch(); //fetch -> aller chercher
        extract($SiegesVide); //$Places
    }
    
    
    if($Places < 3) //S'assure qu'il reste au moins 3 joueurs
    { 
        $BigBlinds = $dbh->query($queryBB) or die ("SQL Error in:<br> $queryBB <br>Error message:".$dbh->errorInfo()[2]);
        $BigBlindsRetourTable = $dbh->query($queryBBT) or die ("SQL Error in:<br> $queryBBT <br>Error message:".$dbh->errorInfo()[2]);
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
        
        $SmallBlinds = $dbh->query($querySB) or die ("SQL Error in:<br> $querySB <br>Error message:".$dbh->errorInfo()[2]);
        $SmallBlindsRetourTable = $dbh->query($querySBT) or die ("SQL Error in:<br> $querySBT <br>Error message:".$dbh->errorInfo()[2]);
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
        
        
        $Dealers = $dbh->query($queryD) or die ("SQL Error in:<br> $queryD <br>Error message:".$dbh->errorInfo()[2]);
        $DealersRetourTable = $dbh->query($queryDT) or die ("SQL Error in:<br> $queryDT <br>Error message:".$dbh->errorInfo()[2]);
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
    }
    else 
    {
        if($Places == 3) //S'il y a 3 joueurs, le dealer devient small blind, le small blind devient big blind et le big blind devient dealer
        {
            $SiegeDealer = $SiegeDealers->fetch(); //fetch -> aller chercher
            extract($SiegeDealer); //$DealerPartie

            $SiegeSmallBlind = $SiegeSmallBlinds->fetch(); //fetch -> aller chercher
            extract($SiegeSmallBlind); //$SmallBlindPartie

            $SiegeBigBlind = $SiegeBigBlinds->fetch(); //fetch -> aller chercher
            extract($SiegeBigBlind); //$BigBlindPartie
            
            //Le small blind devient big blind
            $query = "UPDATE poker.partie SET SmallBlindPartie ='$BigBlindPartie'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
            
            //Le big blind devient dealer
            $query = "UPDATE poker.partie SET BigBlindPartie ='$DealerPartie'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);

            //Le dealer devient small blind
            $query = "UPDATE poker.partie SET DealerPartie ='$SmallBlindPartie'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);


        }
        else //Lorsqu'il n'y a que 2 joueurs, un joueur est dealer et small blind, l'autre big blind
        {   
            if($SiegeDealers->rowCount() > 0) //S'assure qu'il y a un dealer
            {
                $SiegeDealer = $SiegeDealers->fetch(); //fetch -> aller chercher
                extract($SiegeDealer); //$DealerPartie

                //Le dealer devient big blind
                $query = "UPDATE poker.partie SET BigBlindPartie ='$DealerPartie'";
                $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
            }

            if($SiegeBigBlinds->rowCount() > 0) //S'assure qu'il y a un big blind
            {
                $SiegeBigBlind = $SiegeBigBlinds->fetch(); //fetch -> aller chercher
                extract($SiegeBigBlind); //$BigBlindPartie

                //Le big blind devient dealer et small blind
                $query = "UPDATE poker.partie SET DealerPartie ='$BigBlindPartie'";
                $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
                $query = "UPDATE poker.partie SET SmallBlindPartie ='$BigBlindPartie'";
                $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
            }
        }
    }
}



/* ========================== Code non utilisé. Il était prévu afin de distribuer un jeton par un, afin de gérer le problème du jeton qui disparait, lorsqu'un joueur quitte la table ============================
============================= Cette façon de gérer le problème ne marche pas, puisqu'on selectionne un joueur ne possèdant aucun jeton, également parce qu'à la fin du tour les jetons changent de joeur =========

//Fonctione pour rendre les jetons un par un
function RendreJetons()
{
    //Regarde si le joueur possède un jeton dealer. Afin de le rendre lorsqu'il quitte la partie
    $query = "SELECT DealerPartie FROM poker.partie INNER JOIN poker.joueur ON partie.DealerPartie=joueur.idJoueur WHERE PseudoJoueur = '$Pseudo'";
    $RendreJetonDealers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde si le joueur possède un jeton small blind. Afin de le rendre lorsqu'il quitte la partie
    $query = "SELECT SmallBlindPartie FROM poker.partie INNER JOIN poker.joueur ON partie.SmallBlindPartie=joueur.idJoueur WHERE PseudoJoueur = '$Pseudo'";
    $RendreJetonSmallBlinds = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde si le joueur possède un jeton big blind. Afin de le rendre lorsqu'il quitte la partie
    $query = "SELECT BigBlindPartie FROM poker.partie INNER JOIN poker.joueur ON partie.BigBlindPartie=joueur.idJoueur WHERE PseudoJoueur = '$Pseudo'";
    $RendreJetonBigBlinds = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    if($RendreJetonDealers->rowCount() > 0) //S'assure que le joueur possède le jeton dealer
    {
        $RendreJetonDealer = $RendreJetonDealers->fetch(); //fetch -> aller chercher
        extract($RendreJetonDealer); //$DealerPartie
        RendreJetonDealer();
    }
    
    if($RendreJetonSmallBlinds->rowCount() > 0) //S'assure que le joueur possède le jeton small blind
    {
        $RendreJetonSmallBlind = $RendreJetonSmallBlinds->fetch(); //fetch -> aller chercher
        extract($RendreJetonSmallBlind); //$SmallBlindPartie
        RendreJetonBigBlind();
    }
    
    if($RendreJetonBigBlinds->rowCount() > 0) //S'assure que le joueur possède le jeton big blind
    {
        $RendreJetonBigBlind = $RendreJetonBigBlinds->fetch(); //fetch -> aller chercher
        extract($RendreBigSmallBlind); //$BigBlindPartie
        RendreJetonSmallBlind();
    }
}

//Fonction pour rendre le jeton le small blind
function RendreJetonSmallBlind()
{    
    global $dbh;
    
    //Regarde combien de places libre il reste
    $query = "SELECT COUNT(idSiege) AS Places FROM poker.siege WHERE fkJoueurSiege IS NULL AND fkEtatSiege='1'";
    $SiegesVides = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    //Regarde quel est le prochain joueur après le small blind
    $querySB = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL AND idSiege > (SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL) AND idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) AND idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    
    //Regarde quel est le prochain joueur après le small blind en recommençant depuis le premier joueur a table
    $querySBT = "SELECT idSiege FROM poker.siege WHERE fkJoueurSiege IS NOT NULL AND idSiege != (SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL) AND idSiege != (SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL) ORDER BY idSiege ASC LIMIT 1";
    
    //Donne le numéro de siege du small blind
    $query = "SELECT SmallBlindPartie FROM poker.partie WHERE SmallBlindPartie IS NOT NULL";
    $SiegeSmallBlinds = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    ///Donne le numéro de siege du dealer
    $query = "SELECT DealerPartie FROM poker.partie WHERE DealerPartie IS NOT NULL";
    $SiegeDealers = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        
    //Donne le numéro de siege du big blind
    $query = "SELECT BigBlindPartie FROM poker.partie WHERE BigBlindPartie IS NOT NULL";
    $SiegeBigBlinds = $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
    
    
    if($SiegesVides->rowCount() > 0) //Vérifie que la table n'est pas vide
    {
        $SiegesVide = $SiegesVides->fetch(); //fetch -> aller chercher
        extract($SiegesVide); //$Places
    }
    
    if($Places < 3) //S'assure qu'il reste au moins 3 joueurs
    { 
        $SmallBlinds = $dbh->query($querySB) or die ("SQL Error in:<br> $querySB <br>Error message:".$dbh->errorInfo()[2]);
        $SmallBlindsRetourTable = $dbh->query($querySBT) or die ("SQL Error in:<br> $querySBT <br>Error message:".$dbh->errorInfo()[2]);
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
    }
    else 
    {
        if($Places == 3) //S'il y a 3 joueurs, le small blind devient big blind
        {
            $SiegeSmallBlind = $SiegeSmallBlinds->fetch(); //fetch -> aller chercher
            extract($SiegeSmallBlind); //$SmallBlindPartie
            
            //Le small blind devient big blind
            $query = "UPDATE poker.partie SET SmallBlindPartie ='$BigBlindPartie'";
            $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
        }
        else //Lorsqu'il n'y a que 2 joueurs, un joueur est dealer et small blind, l'autre big blind. Le big blind reprend le jeton du small blind
        {   
            if($SiegeSmallBlinds->rowCount() > 0) //S'assure qu'il y a un Small blind
            {
                //On récupére le siege du big blind, afin de lui assigné le jeton small blind
                $SiegeBigBlind = $SiegeBigBlinds->fetch(); //fetch -> aller chercher
                extract($SiegeBigBlind); //$BigBlindPartie

                //Le big blind devient le small blind
                $query = "UPDATE poker.partie SET SmallBlindPartie ='$BigBlindPartie'";
                $dbh->query($query) or die ("SQL Error in:<br> $query <br>Error message:".$dbh->errorInfo()[2]);
            }
        }
    }
}*/
?>

<meta charset='utf-8'/>

<?php
@session_start();

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

<meta charset='utf-8'/>

<?php
//Creation of a superglobale for change the title of the tab
$GLOBALS['TitleTab'] = 'Poker online';

//Log in to the data base
function ConnectDB()
{
    //Required datas for connect to a database
    $hostname = 'localhost';
    $dbname = 'poker';
    $username = 'root';
    $password = '';

    // PDO = Persistant Data Object
    // Between "" = Connection String
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

?>

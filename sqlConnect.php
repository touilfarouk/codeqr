
<?php


$servername = "localhost";
$dbname = "attestation";
$username = "root";
$password = "";
try
{
    $bdd = new PDO("mysql:host=$servername;dbname=$dbname; charset=utf8", $username, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      
    $bdd->exec("SET CHARACTER SET 'utf8'");
    $bdd->exec("SET SESSION collation_connection ='utf8_unicode_ci'");
    $bdd->exec("SET lc_time_names = 'fr_FR'");
    
}
catch (Exception $e)
{  
    die('Erreur: '.$e->getMessage());
}
?>
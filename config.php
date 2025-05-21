<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

;
 


// Configuration pour l'envoi de mail (SMTP) - ici, on laisse tel quel ou on change plus tard
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'jackiningwe@gmail.com');
define('SMTP_PASS', 'uvcv hoim wbeu vdbp');
define('SMTP_FROM', 'jackiningwe@gmail.com');
define('SMTP_FROM_NAME', 'ANTONIO JACK ININGWE');

// Connexion à la base de données InfinityFree
$servername = "sql207.infinityfree.com"; // Change avec le serveur MySQL donné par InfinityFree
$username = "if0_38993980";       // Ton nom d'utilisateur MySQL InfinityFree
$password = "ErShIB01gDR";    // Ton mot de passe MySQL InfinityFree
$dbname = "if0_38993980_auth_demo"; // Le nom de ta base de données InfinityFree

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

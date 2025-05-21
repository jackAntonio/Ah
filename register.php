<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $sexe = $_POST['sexe'];
    $contact = $_POST['contact'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $code = rand(100000, 999999);
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];

// Upload photo
   $photo = '';
   if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $photo = 'uploads/' . uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
}

   
    

    // Insertion de l'utilisateur avec code de validation
  // Ajoute dans la récupération des champs


// Mets à jour l’insertion SQL
$sql = "INSERT INTO users (photo, nom, prenom, email, sexe, contact, password, code, is_verified, date_naissance, adresse)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", $photo, $nom, $prenom, $email, $sexe, $contact, $password, $code, $date_naissance, $adresse);
    if($stmt->execute()){
        // Envoi du mail
        require 'send_mail.php';
        sendValidationEmail($email, $code);

        header("Location: verify.php?email=$email");
        exit;
    } else {
        echo "Erreur d'inscription.";
    }
}
?>
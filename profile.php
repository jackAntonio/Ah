<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $sexe = $_POST['sexe'];
    $contact = $_POST['contact'];
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];
    $photo = $_POST['old_photo'];
    // upload nouvelle photo ?
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'uploads/' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
    }
    $sql = "UPDATE users SET nom=?, prenom=?, sexe=?, contact=?, date_naissance=?, adresse=?, photo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $nom, $prenom, $sexe, $contact, $date_naissance, $adresse, $photo, $id);
    $stmt->execute();
    $success = "Mise à jour effectuée";
}

$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mon Profil</title>
     <link rel="stylesheet" href="css/style.css">
    <style>
      form { width:300px; margin:30px auto; }
      img { width:80px; display:block; margin-bottom:10px;}
    </style>
</head>
<body>
  <form method="POST" enctype="multipart/form-data">
    <h2>Mon Profil</h2>
    <img src="<?= htmlspecialchars($user['photo']) ?>" alt="photo">
    <input type="file" name="photo" accept="image/*">
    <input type="hidden" name="old_photo" value="<?= htmlspecialchars($user['photo']) ?>">
    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
    <select name="sexe" required>
        <option value="Homme" <?= $user['sexe']=='Homme'?'selected':'' ?>>Homme</option>
        <option value="Femme" <?= $user['sexe']=='Femme'?'selected':'' ?>>Femme</option>
    </select>
    <input type="text" name="contact" value="<?= htmlspecialchars($user['contact']) ?>" required>
    <input type="date" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>" required>
    <input type="text" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>" required>
    <button type="submit">Mettre à jour</button>
    <?php if(!empty($success)) echo "<div style='color:green'>$success</div>"; ?>
  </form>
</body>
</html>
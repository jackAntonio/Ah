<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, is_verified, nom FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->bind_result($id, $hash, $is_verified, $nom);
    if ($stmt->fetch()) {
        if (!$is_verified) {
            $error = "Compte non vérifié.";
        } elseif (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['nom'] = $nom;
            header("Location: accueil.php");
            exit;
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Email non trouvé.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="background"></div>
  <div class="login-container">
    
    <form method="POST" action="login.php">
      <h2>Connexion</h2>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Mot de passe" required>
      <button type="submit">Se connecter</button>
      <p class="error" id="errorDisplay"></p>
      <p>Pas encore inscrit ? <a href="formulaire.html">Créer un compte</a></p>
      <a href="admin_login.php" class="admin-link">Espace Admin</a>
    </form>
    <button id="themeToggle">🌓 Mode sombre</button>
  </div>
  <script >
      document.getElementById("themeToggle").addEventListener("click", () => {
  document.body.classList.toggle("dark");
});

// Afficher l’erreur passée par l’URL (?error=...)
const params = new URLSearchParams(window.location.search);
if (params.get("error")) {
  document.getElementById("errorDisplay").textContent = decodeURIComponent(params.get("error"));
}

  </script>
</body>
</html>
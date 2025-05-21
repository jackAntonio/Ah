<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, nom, password FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->bind_result($id, $nom, $hash);
    if ($stmt->fetch()) {
        if (password_verify($password, $hash)) {
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_nom'] = $nom;
            $_SESSION['admin'] = true;
            header("Location: admin.php");
            exit;
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Email non trouvé.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="theme-toggle" id="themeToggle" title="Changer le thème">
    <span id="themeIcon">🌙</span>
  </div>
  <div class="container">
    <h2>Connexion Admin</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Mot de passe" required>
      <button type="submit">Connexion</button>
      <p>Vous n'etes pas Admin ? <a href="login.php">bougé !!!</a></p>
      <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    </form>
  </div>
  <script>
   document.getElementById("themeToggle").addEventListener("click", () => {
  document.body.classList.toggle("dark");
});

document.getElementById("registerForm").addEventListener("submit", function (e) {
  const button = this.querySelector("button");
  button.classList.add("loading");
});

// Modale d'inscription réussie
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get("registered") === "true") {
  document.getElementById("successModal").style.display = "flex";
}

document.getElementById("closeModal").addEventListener("click", function () {
  document.getElementById("successModal").style.display = "none";
  window.location.href = "verify.php";
});
  </script>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'config.php';
// Récupérer l'utilisateur connecté
$id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Profil - Accueil</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="profile-card.css">
    <!-- FontAwesome pour les icônes sociaux -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
  <div class="theme-toggle" id="themeToggle" title="Changer le thème">
    <span id="themeIcon">🌙</span>
  </div>
  <div class="container d-flex justify-content-center">
    <div class="card p-3 py-4">
        <div class="text-center">
          <center>  <img src="<?= htmlspecialchars($user['photo'] ?: 'default-user.png') ?>"
     class="profile-pic"
     alt="Photo de profil"
     onerror="this.src='default-user.png'"></center><br><br>
            <h3 class="mt-2"><?= htmlspecialchars($user['prenom']) . ' ' . htmlspecialchars($user['nom']) ?></h3><br><br>
            <span class="mt-1 clearfix"><?= htmlspecialchars($user['email']) ?></span><br><br>

            <div class="row mt-3 mb-3" style="display: flex; justify-content: center;">
                <div class="col-md-4">
                    <h5>Sexe</h5><br><br>
                    <span class="num"><?= htmlspecialchars($user['sexe']) ?></span><br><br>
                </div><br><br>
                <div class="col-md-4">
                    <h5>Naissance</h5><br><br>
                    <span class="num"><?= htmlspecialchars($user['date_naissance']) ?></span><br><br>
                </div><br><br>
                <div class="col-md-4">
                    <h5>Contact</h5><br><br>
                    <span class="num"><?= htmlspecialchars($user['contact']) ?></span><br><br>
                </div>
            </div>

            <hr class="line">
            
            <div style="margin-top:22px;">
           <center>     <a href="profile.php" class="neo-button" >Modifier mes informations</a><br><br>
                <a href="logout.php" class="neo-button" >Déconnexion</a></center>
            </div>
        </div>
    </div>
  </div>
  <script>
    // Gestion du thème clair/sombre
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    function setTheme(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('theme', theme);
      themeIcon.textContent = theme === 'dark' ? '☀️' : '🌙';
    }
    themeToggle.onclick = () => setTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    (function() {
      const userTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      setTheme(userTheme);
    })();
  </script>
</body>
</html>
<?php
session_start();
require 'config.php';
// Vérifie si l'utilisateur est admin (à sécuriser selon ton système)
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) { header("Location: login.php"); exit; }

$result = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Liste des inscrits</title>
    <style>
      table { border-collapse: collapse; width: 90%; margin: 30px auto;}
      th, td { border: 1px solid #ccc; padding: 8px;}
      img { width: 50px; }
      .actions button { margin-right: 5px; }
    </style>
</head>
<body>
<h2 style="text-align:center;">Liste des inscrits</h2>
<table>
    <tr>
        <th>Photo</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Sexe</th>
        <th>Contact</th>
        <th>Date de naissance</th>
        <th>Adresse</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><img src="<?= htmlspecialchars($row['photo']) ?>" alt="photo"></td>
        <td><?= htmlspecialchars($row['nom']) ?></td>
        <td><?= htmlspecialchars($row['prenom']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['sexe']) ?></td>
        <td><?= htmlspecialchars($row['contact']) ?></td>
        <td><?= htmlspecialchars($row['date_naissance']) ?></td>
        <td><?= htmlspecialchars($row['adresse']) ?></td>
        <td class="actions">
            <form style="display:inline;" method="GET" action="voir.php"><input type="hidden" name="id" value="<?= $row['id'] ?>"><button>Voir</button></form>
            <form style="display:inline;" method="GET" action="modifier.php"><input type="hidden" name="id" value="<?= $row['id'] ?>"><button>Modifier</button></form>
            <form style="display:inline;" method="POST" action="supprimer.php" onsubmit="return confirm('Supprimer cet utilisateur ?');"><input type="hidden" name="id" value="<?= $row['id'] ?>"><button>Supprimer</button></form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
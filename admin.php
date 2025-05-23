<?php
session_start();
require 'config.php';

// Vérifie si l'utilisateur est admin
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit;
}
$admin_id = $_SESSION['admin_id']; // on suppose que tu stocks l'id admin ici
// Récupère les infos de l'admin pour l'en-tête
$admin = $conn->query("SELECT * FROM admin WHERE id = $admin_id")->fetch_assoc();

// Récupère les utilisateurs
$result = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Admin - Liste des inscrits</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6c47ec;
            --primary-dark: #4b2db1;
            --accent: #ffba43;
            --danger: #e53935;
            --success: #11a36e;
            --bg: #f7f8fa;
            --white: #fff;
            --gray-100: #f1f2f6;
            --gray-300: #dee1e7;
            --gray-500: #a4a7b3;
            --gray-900: #22223b;
        }
        body { background: var(--bg); font-family: 'Inter', Arial, sans-serif; margin: 0; color: var(--gray-900);}
        header {
            background: var(--white);
            box-shadow: 0 2px 8px #0001;
            padding: 18px 0 13px 0;
            border-radius: 0 0 18px 18px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .admin-info {
            display: flex; align-items: center; gap: 14px; margin-left: 32px;
        }
        .admin-info img {
            width: 58px; height: 58px; border-radius: 50%; object-fit: cover;
            border: 3px solid var(--primary); background: var(--gray-100);
        }
        .admin-info-details {
            display: flex; flex-direction: column;
        }
        .admin-info-details span {
            font-size: 1.1em; font-weight: 600; color: var(--primary-dark);
        }
        .admin-info-details small {
            color: var(--gray-500); font-size:0.97em;
        }
        .welcome {
            font-size: 1.15em; color: var(--gray-900); margin-bottom: 2px;
        }
        .header-btns {
            display: flex; gap: 15px; align-items: center; margin-right: 28px;
        }
        .header-btns button, .header-btns a {
            padding: 7px 17px;
            border-radius: 7px;
            border: none;
            font-weight: 500;
            font-size: 1em;
            cursor: pointer;
            background: var(--primary);
            color: var(--white);
            transition: background 0.18s, box-shadow 0.17s, transform 0.08s;
            box-shadow: 0 1px 4px #0001;
            outline: none;
            text-decoration: none;
        }
        .header-btns button.edit-profile { background: var(--accent); color: #4a2b00;}
        .header-btns button.edit-profile:hover { background: #ffd570;}
        .header-btns a.logout { background: var(--danger);}
        .header-btns a.logout:hover { background: #c62828;}
        /* ... (reprend ici le CSS du tableau, modale, responsive, etc. comme dans la version précédente) ... */
        main { margin: 36px auto; max-width: 1220px; padding: 0 2vw;}
        .table-wrapper { background: var(--white); border-radius: 18px; box-shadow: 0 2px 18px #0002; overflow-x: auto; margin-bottom: 38px; animation: fadein 0.7s;}
        @keyframes fadein { from { opacity: 0; } to { opacity: 1; } }
        table { border-collapse: collapse; width: 100%; min-width: 950px; font-size: 1.02rem;}
        th, td { padding: 13px 8px; text-align: center;}
        th { background: linear-gradient(90deg, var(--primary), var(--primary-dark)); color: var(--white); font-weight: 600; letter-spacing: 0.03em; border-bottom: 3px solid var(--primary-dark);}
        tr { transition: background 0.18s;}
        tbody tr:hover { background: var(--gray-100);}
        td { border-bottom: 1px solid var(--gray-300); vertical-align: middle;}
        img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2.5px solid var(--gray-300); box-shadow: 0 2px 10px #0001; background: var(--gray-100);}
        .actions { display: flex; flex-wrap: wrap; gap: 6px; justify-content: center;}
        .actions button { padding: 7px 18px; border-radius: 7px; border: none; font-weight: 500; font-size: 1em; cursor: pointer; transition: background 0.17s, box-shadow 0.17s, transform 0.08s; box-shadow: 0 1px 4px #0001; outline: none;}
        .actions .view { background: var(--primary); color: var(--white);}
        .actions .edit { background: var(--accent); color: #4a2b00;}
        .actions .delete { background: var(--danger); color: var(--white);}
        .actions button:hover, .modal-actions button:hover { filter: brightness(0.95); transform: translateY(-1px) scale(1.04); box-shadow: 0 2px 12px #0002;}
        /* Modal styles */
        .modal-bg { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #22223b88; z-index: 1000; align-items: center; justify-content: center; animation: fadein 0.3s;}
        .modal { background: var(--white); border-radius: 13px; box-shadow: 0 4px 32px #22223b33; min-width: 345px; max-width: 97vw; padding: 33px 28px 24px 28px; position: relative; animation: slidein 0.3s;}
        @keyframes slidein { from { transform: translateY(25px); opacity:0; } to { transform: none; opacity:1; } }
        .modal-close { position: absolute; top: 10px; right: 18px; font-size: 2rem; color: var(--primary); cursor: pointer; font-weight: bold; line-height: 1; transition: color 0.2s;}
        .modal-close:hover { color: var(--danger);}
        .modal h3 { margin-top: 0; font-size: 1.25rem; color: var(--primary-dark);}
        .modal label { display: block; margin: 13px 0 4px; font-weight: 500;}
        .modal input, .modal select, .modal textarea { width: 98%; padding: 8px 9px; border-radius: 5px; border: 1px solid var(--gray-300); background: var(--gray-100); font-size: 1em; margin-bottom: 2px; transition: border 0.18s;}
        .modal input:focus, .modal select:focus, .modal textarea:focus { border: 1.5px solid var(--primary); outline: none; background: var(--white);}
        .modal-actions { margin-top: 17px; text-align: right;}
        .modal-actions button { margin-left: 10px; min-width: 105px; padding: 8px 0; font-size: 1em;}
        .modal .avatar { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent); margin-bottom: 14px; background: var(--gray-100); box-shadow: 0 2px 10px #0001;}
        .modal-content { text-align: left;}
        .modal .modal-content b { color: var(--primary-dark);}
        .success-msg, .error-msg { display: block; font-weight: 500; margin: 10px auto 0; border-radius: 6px; padding: 7px 0; max-width: 380px; font-size: 1.07em; text-align: center;}
        .success-msg { color: var(--success); background: #d8f9e7;}
        .error-msg { color: var(--danger); background: #ffe5e5;}
        #msgArea { min-height: 36px;}
        @media (max-width: 1000px) { .table-wrapper { min-width: 0; } table { min-width: 700px;}}
        @media (max-width: 700px) { main { padding: 0 1vw;} table, th, td {font-size: 13px;} .modal {min-width: 210px;} .modal .avatar { width: 48px; height: 48px;} .admin-info img {width:36px; height:36px;} }
    </style>
</head>
<body>
<header>
    <div class="admin-info">
        <img src="<?= htmlspecialchars($admin['photo'] ?: 'default-user.png') ?>" alt="Photo de l'admin">
        <div class="admin-info-details">
            <span><?= htmlspecialchars($admin['nom']) . ' ' . htmlspecialchars($admin['prenom']) ?></span>
            <small><?= htmlspecialchars($admin['email']) ?></small>
            <div class="welcome">Bienvenue, Admin !</div>
        </div>
    </div>
    <div class="header-btns">
        <button class="edit-profile" onclick="openAdminEdit()">Modifier mon profil</button>
        <a href="logout.php" class="logout">Déconnexion</a>
    </div>
</header>
<main>
    <div id="msgArea"></div>
    <div class="table-wrapper" role="region" aria-label="Liste des utilisateurs">
    <table id="usersTable">
        <thead>
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
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr data-user='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>' id="tr-<?= $row['id'] ?>">
            <td><img src="<?= htmlspecialchars($row['photo'] ?: 'default-user.png') ?>" alt="photo"></td>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['prenom']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['sexe']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= htmlspecialchars($row['date_naissance']) ?></td>
            <td><?= htmlspecialchars($row['adresse']) ?></td>
            <td class="actions">
                <button class="view" onclick="openView(this)" title="Voir les détails"><span aria-hidden="true">👁️</span> Voir</button>
                <button class="edit" onclick="openEdit(this)" title="Modifier"><span aria-hidden="true">✏️</span> Modifier</button>
                <button class="delete" onclick="openDelete(this)" title="Supprimer"><span aria-hidden="true">🗑️</span> Supprimer</button>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>

    <!-- Modale Voir Utilisateur -->
    <div class="modal-bg" id="modalView" aria-modal="true" role="dialog">
      <div class="modal">
        <span class="modal-close" onclick="closeModal('modalView')" title="Fermer">&times;</span>
        <div style="text-align:center">
          <img class="avatar" id="viewPhoto" src="" alt="photo utilisateur">
          <h3 id="viewNom"></h3>
          <small id="viewEmail" style="color:var(--gray-500);"></small>
        </div>
        <div class="modal-content" style="margin-top:13px">
          <b>Sexe :</b> <span id="viewSexe"></span><br>
          <b>Contact :</b> <span id="viewContact"></span><br>
          <b>Date de naissance :</b> <span id="viewNaissance"></span><br>
          <b>Adresse :</b> <span id="viewAdresse"></span>
        </div>
      </div>
    </div>

    <!-- Modale Modifier Utilisateur -->
    <div class="modal-bg" id="modalEdit" aria-modal="true" role="dialog">
      <div class="modal">
        <span class="modal-close" onclick="closeModal('modalEdit')" title="Fermer">&times;</span>
        <h3>Modifier l'utilisateur</h3>
        <form id="editForm">
          <input type="hidden" name="id" id="editId">
          <label for="editNom">Nom</label>
          <input type="text" name="nom" id="editNom" required>
          <label for="editPrenom">Prénom</label>
          <input type="text" name="prenom" id="editPrenom" required>
          <label for="editEmail">Email</label>
          <input type="email" name="email" id="editEmail" required>
          <label for="editSexe">Sexe</label>
          <select name="sexe" id="editSexe" required>
            <option value="Homme">Homme</option>
            <option value="Femme">Femme</option>
          </select>
          <label for="editContact">Contact</label>
          <input type="text" name="contact" id="editContact">
          <label for="editNaissance">Date de naissance</label>
          <input type="date" name="date_naissance" id="editNaissance">
          <label for="editAdresse">Adresse</label>
          <textarea name="adresse" id="editAdresse"></textarea>
          <div class="modal-actions">
            <button type="button" onclick="closeModal('modalEdit')">Annuler</button>
            <button type="submit" class="edit">Enregistrer</button>
          </div>
          <div id="editMsg"></div>
        </form>
      </div>
    </div>

    <!-- Modale Modifier Admin -->
    <div class="modal-bg" id="modalAdminEdit" aria-modal="true" role="dialog">
      <div class="modal">
        <span class="modal-close" onclick="closeModal('modalAdminEdit')" title="Fermer">&times;</span>
        <h3>Modifier mon profil</h3>
        <form id="adminEditForm" enctype="multipart/form-data">
          <input type="hidden" name="id" id="adminEditId" value="<?= $admin['id'] ?>">
          <label for="adminEditNom">Nom</label>
          <input type="text" name="nom" id="adminEditNom" value="<?= htmlspecialchars($admin['nom']) ?>" required>
          <label for="adminEditPrenom">Prénom</label>
          <input type="text" name="prenom" id="adminEditPrenom" value="<?= htmlspecialchars($admin['prenom']) ?>" required>
          <label>Email</label>
          <input type="email" value="<?= htmlspecialchars($admin['email']) ?>" disabled>
          <label for="adminEditPhoto">Photo (laisser vide pour ne pas changer)</label>
          <input type="file" name="photo" id="adminEditPhoto" accept="image/*">
          <div class="modal-actions">
            <button type="button" onclick="closeModal('modalAdminEdit')">Annuler</button>
            <button type="submit" class="edit-profile">Enregistrer</button>
          </div>
          <div id="adminEditMsg"></div>
        </form>
      </div>
    </div>

    <!-- Modale Supprimer Utilisateur -->
    <div class="modal-bg" id="modalDelete" aria-modal="true" role="dialog">
      <div class="modal">
        <span class="modal-close" onclick="closeModal('modalDelete')" title="Fermer">&times;</span>
        <h3>Confirmer la suppression</h3>
        <p id="deleteText"></p>
        <div class="modal-actions">
          <button onclick="closeModal('modalDelete')">Annuler</button>
          <button class="delete" id="confirmDeleteBtn">Supprimer</button>
        </div>
      </div>
    </div>
</main>
<script>
// Utilisateur
function getUserFromBtn(btn) {
    let tr = btn.closest('tr');
    return JSON.parse(tr.getAttribute('data-user'));
}
function openView(btn) {
    let user = getUserFromBtn(btn);
    document.getElementById('viewPhoto').src = user.photo || 'default-user.png';
    document.getElementById('viewNom').textContent = user.nom + " " + user.prenom;
    document.getElementById('viewEmail').textContent = user.email;
    document.getElementById('viewSexe').textContent = user.sexe;
    document.getElementById('viewContact').textContent = user.contact;
    document.getElementById('viewNaissance').textContent = user.date_naissance;
    document.getElementById('viewAdresse').textContent = user.adresse;
    document.getElementById('modalView').style.display = 'flex';
}
function closeModal(id) { document.getElementById(id).style.display = 'none'; }
function openEdit(btn) {
    let user = getUserFromBtn(btn);
    document.getElementById('editId').value = user.id;
    document.getElementById('editNom').value = user.nom;
    document.getElementById('editPrenom').value = user.prenom;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editSexe').value = user.sexe;
    document.getElementById('editContact').value = user.contact;
    document.getElementById('editNaissance').value = user.date_naissance;
    document.getElementById('editAdresse').value = user.adresse;
    document.getElementById('editMsg').textContent = '';
    document.getElementById('modalEdit').style.display = 'flex';
}
document.getElementById('editForm').onsubmit = function(e) {
    e.preventDefault();
    let fd = new FormData(this);
    fd.append('action', 'update');
    fetch('ajax_user.php', {
        method:'POST',
        body:fd
    }).then(r=>r.json()).then(res=>{
        if(res.success) {
            document.getElementById('editMsg').innerHTML = "<span class='success-msg'>Modifié !</span>";
            let tr = document.getElementById('tr-'+fd.get('id'));
            let user = res.user;
            tr.setAttribute('data-user', JSON.stringify(user));
            tr.children[1].textContent = user.nom;
            tr.children[2].textContent = user.prenom;
            tr.children[3].textContent = user.email;
            tr.children[4].textContent = user.sexe;
            tr.children[5].textContent = user.contact;
            tr.children[6].textContent = user.date_naissance;
            tr.children[7].textContent = user.adresse;
            setTimeout(()=>closeModal('modalEdit'), 800);
        } else {
            document.getElementById('editMsg').innerHTML = "<span class='error-msg'>" + res.message + "</span>";
        }
    });
};
let deleteUserId = null;
function openDelete(btn) {
    let user = getUserFromBtn(btn);
    deleteUserId = user.id;
    document.getElementById('deleteText').textContent = "Voulez-vous vraiment supprimer " + user.nom + " " + user.prenom + " ?";
    document.getElementById('modalDelete').style.display = 'flex';
}
document.getElementById('confirmDeleteBtn').onclick = function() {
    fetch('ajax_user.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body: 'action=delete&id='+encodeURIComponent(deleteUserId)
    }).then(r=>r.json()).then(res=>{
        if(res.success) {
            document.getElementById('tr-'+deleteUserId).remove();
            showMsg("Utilisateur supprimé !", true);
        } else {
            showMsg(res.message, false);
        }
        closeModal('modalDelete');
    });
};
function showMsg(msg, ok) {
    let d = document.getElementById('msgArea');
    d.innerHTML = "<span class='"+(ok?'success-msg':'error-msg')+"'>" + msg + "</span>";
    setTimeout(()=>{d.innerHTML=''}, 2500);
}

// Admin
function openAdminEdit() {
    document.getElementById('modalAdminEdit').style.display = 'flex';
}
document.getElementById('adminEditForm').onsubmit = function(e) {
    e.preventDefault();
    let fd = new FormData(this);
    fd.append('action', 'update_admin');
    fetch('ajax_admin.php', {
        method:'POST',
        body:fd
    }).then(r=>r.json()).then(res=>{
        if(res.success) {
            document.getElementById('adminEditMsg').innerHTML = "<span class='success-msg'>Profil mis à jour !</span>";
            // Rafraîchir la page pour mettre à jour header (nom/photo)
            setTimeout(()=>{ location.reload(); }, 1000);
        } else {
            document.getElementById('adminEditMsg').innerHTML = "<span class='error-msg'>" + res.message + "</span>";
        }
    });
};
</script>
</body>
</html>
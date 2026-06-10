<?php
// On force le fuseau horaire français pour éviter les décalages d'heure du serveur
date_default_timezone_set('Europe/Paris');

$fichier_commandes = __DIR__ . '/../commandes.json';
$commandes = []; // Initialisation d'un tableau vide par sécurité

// Sécurité anti-warning : vérification du fichier json
if (file_exists($fichier_commandes)) {
    $contenu = file_get_contents($fichier_commandes);
    if (!empty(trim($contenu))) { 
        $donnees = json_decode($contenu, true);
        if (is_array($donnees)) {
            $commandes = $donnees;
        }
    }
}

// 1. GESTION DU BOUTON "PRÊT"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pret'])) {
    $id_a_mettre_pret = $_POST['id_pret'];
    foreach ($commandes as &$c) {
        if ($c['id'] === $id_a_mettre_pret) {
            $c['statut'] = 'en_livraison';
        }
    }
    file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
    header("Location: commande.php");
    exit;
}

// 2. SÉPARATION DES COMMANDES (Moins de 2h vs Plus tard vs Livrées)
$commandes_urgentes = [];
$commandes_futures = [];
$commandes_livrees = []; // Nouveau tableau pour stocker les commandes terminées

$maintenant = time(); // Heure actuelle
$limite_2h = $maintenant + (2 * 3600); // Heure actuelle + 2 heures

foreach ($commandes as $cmd) {
    if (isset($cmd['statut'])) {
        // Si la commande est à préparer (en cours ou en attente)
        if ($cmd['statut'] === 'en_cours' || $cmd['statut'] === 'en_attente') {
            $temps_commande = strtotime($cmd['date_livraison'] . ' ' . $cmd['heure_livraison']);
            
            if ($temps_commande <= $limite_2h) {
                $commandes_urgentes[] = $cmd;
            } else {
                $commandes_futures[] = $cmd;
            }
        } 
        // Si la commande a été marquée comme livrée
        elseif ($cmd['statut'] === 'livree') {
            $commandes_livrees[] = $cmd;
        }
    }
}

// 3. TRI CHRONOLOGIQUE
$fonction_tri = function($a, $b) {
    return strtotime($a['date_livraison'] . ' ' . $a['heure_livraison']) - strtotime($b['date_livraison'] . ' ' . $b['heure_livraison']);
};

// Pour les livrées, on trie à l'envers (la plus récente livrée apparaît tout en haut de l'historique)
$fonction_tri_archive = function($a, $b) {
    return strtotime($b['date_livraison'] . ' ' . $b['heure_livraison']) - strtotime($a['date_livraison'] . ' ' . $a['heure_livraison']);
};

usort($commandes_urgentes, $fonction_tri);
usort($commandes_futures, $fonction_tri);
usort($commandes_livrees, $fonction_tri_archive);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="administrateur.css">
    <meta charset="UTF-8">
    <title>Gestion des Commandes</title>
</head>
<body>

<div style="margin-bottom: 40px;">
    <h2 style="color: #d9534f;">🔥 Commandes Urgentes (Moins de 2h)</h2>
    <table border="1" style="width:100%; text-align:center; border-collapse: collapse;">
        <tr style="background-color: #f2dede;">
            <th>ID Commande</th>
            <th>Client</th>
            <th>Date & Heure Livraison</th>
            <th>Détail (Quantité x Plat)</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php if (empty($commandes_urgentes)): ?>
            <tr><td colspan="6">Aucune commande urgente.</td></tr>
        <?php else: ?>
            <?php foreach ($commandes_urgentes as $cmd): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($cmd['id'], -5)) ?></td>
                    <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                    <td><strong><?= htmlspecialchars($cmd['date_livraison'] . ' à ' . $cmd['heure_livraison']) ?></strong></td>
                    <td>
                        <?php foreach ($cmd['panier'] as $item): ?>
                            <?= $item['quantite'] ?>x <?= htmlspecialchars($item['nom']) ?><br>
                        <?php endforeach; ?>
                    </td>
                    <td><?= $cmd['total'] ?>€</td>
                    <td>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="id_pret" value="<?= $cmd['id'] ?>">
                            <button type="submit" style="cursor:pointer; background-color:#FF9800; color:white; border:none; padding:6px 12px; border-radius:3px; font-weight:bold;">
                                Prêt ➔
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<div style="margin-bottom: 40px;">
    <h2 style="color: #5bc0de;">📅 Commandes à venir (Plus de 2h)</h2>
    <table border="1" style="width:100%; text-align:center; border-collapse: collapse;">
        <tr style="background-color: #d9edf7;">
            <th>ID Commande</th>
            <th>Client</th>
            <th>Date & Heure Livraison</th>
            <th>Détail (Quantité x Plat)</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php if (empty($commandes_futures)): ?>
            <tr><td colspan="6">Aucune commande future prévue pour le moment.</td></tr>
        <?php else: ?>
            <?php foreach ($commandes_futures as $cmd): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($cmd['id'], -5)) ?></td>
                    <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                    <td><strong><?= htmlspecialchars($cmd['date_livraison'] . ' à ' . $cmd['heure_livraison']) ?></strong></td>
                    <td>
                        <?php foreach ($cmd['panier'] as $item): ?>
                            <?= $item['quantite'] ?>x <?= htmlspecialchars($item['nom']) ?><br>
                        <?php endforeach; ?>
                    </td>
                    <td><?= $cmd['total'] ?>€</td>
                    <td>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="id_pret" value="<?= $cmd['id'] ?>">
                            <button type="submit" style="cursor:pointer; background-color:#FF9800; color:white; border:none; padding:6px 12px; border-radius:3px; font-weight:bold;">
                                Prêt ➔
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<hr style="margin: 30px 0; border: 1px solid #ccc;">

<div>
    <h2 style="color: #6c757d;">✅ Historique des Commandes Livrées</h2>
    <table border="1" style="width:100%; text-align:center; border-collapse: collapse;">
        <tr style="background-color: #e2e3e5; color: #383d41;">
            <th>ID Commande</th>
            <th>Client</th>
            <th>Date & Heure Livraison</th>
            <th>Détail (Quantité x Plat)</th>
            <th>Total</th>
        </tr>
        <?php if (empty($commandes_livrees)): ?>
            <tr><td colspan="5">Aucune commande livrée pour le moment.</td></tr>
        <?php else: ?>
            <?php foreach ($commandes_livrees as $cmd): ?>
                <tr style="color: #6c757d; background-color: #f8f9fa;">
                    <td><?= htmlspecialchars(substr($cmd['id'], -5)) ?></td>
                    <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                    <td><?= htmlspecialchars($cmd['date_livraison'] . ' à ' . $cmd['heure_livraison']) ?></td>
                    <td>
                        <?php foreach ($cmd['panier'] as $item): ?>
                            <?= $item['quantite'] ?>x <?= htmlspecialchars($item['nom']) ?><br>
                        <?php endforeach; ?>
                    </td>
                    <td><?= $cmd['total'] ?>€</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<nav style="margin-top: 40px;">
    <a href="../utilisateur/profil.php">profil</a>
    <a href="commande.php">commande</a>
    <a href="livraison.php">livraison</a>
    <a href="admin.php">admin</a>    
</nav>

</body>
</html>
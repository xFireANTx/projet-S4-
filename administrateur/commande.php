<?php

// On force le fuseau horaire français pour éviter les décalages d'heure du serveur
date_default_timezone_set('Europe/Paris');

$fichier_commandes = __DIR__ . '/../commandes.json';
$commandes = file_exists($fichier_commandes) ? json_decode(file_get_contents($fichier_commandes), true) : [];

//bouton pret
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

// separation des commandes
$commandes_urgentes = [];
$commandes_futures = [];

$maintenant = time(); // Heure actuelle en secondes
$limite_2h = $maintenant + (2 * 3600); // Heure actuelle + 2 heures (3600 secondes par heure)

foreach ($commandes as $cmd) {
    if (isset($cmd['statut']) && $cmd['statut'] === 'en_cours') {
        // On convertit la date de la commande en secondes pour la comparer
        $temps_commande = strtotime($cmd['date_livraison'] . ' ' . $cmd['heure_livraison']);
        
        if ($temps_commande <= $limite_2h) {
            $commandes_urgentes[] = $cmd;
        } else {
            $commandes_futures[] = $cmd;
        }
    }
}

// tri chronologique
$fonction_tri = function($a, $b) {
    return strtotime($a['date_livraison'] . ' ' . $a['heure_livraison']) - strtotime($b['date_livraison'] . ' ' . $b['heure_livraison']);
};

usort($commandes_urgentes, $fonction_tri);
usort($commandes_futures, $fonction_tri);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="administrateur.css">
    <meta charset="UTF-8">
    <title>Commandes en cours</title>
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

<hr>

<div>
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

<nav style="margin-top: 30px;">
    <a href="../utilisateur/profil.php">profil</a>
    <a href="commande.php">commande</a>
    <a href="livraison.php">livraison</a>
    <a href="admin.php">admin</a>    
</nav>

</body>
</html>
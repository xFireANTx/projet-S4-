<?php
$fichier_commandes = __DIR__ . '/../commandes.json';
$commandes = file_exists($fichier_commandes) ? json_decode(file_get_contents($fichier_commandes), true) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="administrateur.css">
    <meta charset="UTF-8">
    <title>Commandes en attente</title>
</head>
<body>

<div>
    <h2> Commandes en attente </h2>
    <table border="1" style="width:100%; text-align:center;">
        <tr>
            <th>ID Commande</th>
            <th>Client</th>
            <th>Date & Heure</th>
            <th>Détail (Quantité x Plat)</th>
            <th>Total</th>
            <th>Statut</th>
        </tr>
        <?php foreach ($commandes as $cmd): ?>
            <?php if ($cmd['statut'] === 'en_attente'): ?>
            <tr>
                <td><?= htmlspecialchars(substr($cmd['id'], -5)) ?></td>
                <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                <td><?= htmlspecialchars($cmd['date_livraison'] . ' ' . $cmd['heure_livraison']) ?></td>
                <td>
                    <?php foreach ($cmd['panier'] as $item): ?>
                        <?= $item['quantite'] ?>x <?= htmlspecialchars($item['nom']) ?><br>
                    <?php endforeach; ?>
                </td>
                <td><?= $cmd['total'] ?>€</td>
                <td>En préparation ⏳</td>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</div>

<nav>
    <a href="../utilisateur/profil.php">profil</a>
    <a href="commande.php">commande</a>
    <a href="livraison.php">livraison</a>
    <a href="admin.php">admin</a>    
</nav>

</body>
</html>
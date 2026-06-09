<?php
$fichier_commandes = __DIR__ . '/../commandes.json';
$commandes = file_exists($fichier_commandes) ? json_decode(file_get_contents($fichier_commandes), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pret'])) {
    $id_a_mettre_pret = $_POST['id_pret'];
    foreach ($commandes as &$c) {
        if ($c['id'] === $id_a_mettre_pret) {
            $c['statut'] = 'en_livraison'; // Envoie la commande à la page livraison
        }
    }
    file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
    header("Location: commande.php");
    exit;
}

usort($commandes, function($a, $b) {
    $tempsA = strtotime($a['date_livraison'] . ' ' . $a['heure_livraison']);
    $tempsB = strtotime($b['date_livraison'] . ' ' . $b['heure_livraison']);
    return $tempsA - $tempsB; 
});
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="administrateur.css">
    <meta charset="UTF-8">
    <title>Commandes en cours</title>
</head>
<body>

<div>
    <h2> Commandes en cours de préparation </h2>
    <table border="1" style="width:100%; text-align:center;">
        <tr>
            <th>ID Commande</th>
            <th>Client</th>
            <th>Date & Heure Livraison</th>
            <th>Détail (Quantité x Plat)</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php foreach ($commandes as $cmd): ?>
            <?php if (isset($cmd['statut']) && $cmd['statut'] === 'en_cours'): ?>
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
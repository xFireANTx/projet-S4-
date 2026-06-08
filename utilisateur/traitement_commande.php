<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

// Récupérer les données envoyées par panier.js
$donnees_json = file_get_contents('php://input');
$donnees = json_decode($donnees_json, true);

if (!$donnees || empty($donnees['panier'])) {
    echo json_encode(['success' => false, 'message' => 'Panier vide']);
    exit;
}

$email_client = $_SESSION['client']['email'];
$total = 0;
$articles_resume = [];

// Calcul du total et résumé pour le profil
foreach ($donnees['panier'] as $item) {
    $total += $item['prix'] * $item['quantite'];
    $articles_resume[] = $item['quantite'] . 'x ' . $item['nom'];
}

$nouvelle_commande = [
    'id' => uniqid(), // ID unique pour la commande
    'client_email' => $email_client,
    'client_nom' => $_SESSION['client']['nom'] . ' ' . $_SESSION['client']['prenom'],
    'client_adresse' => $_SESSION['client']['adresse'] ?? 'Adresse non renseignée',
    'client_phone' => $_SESSION['client']['phone'] ?? 'Non renseigné',
    'date_livraison' => $donnees['date'],
    'heure_livraison' => $donnees['heure'],
    'panier' => $donnees['panier'],
    'total' => $total,
    'statut' => 'en_attente' // Peut devenir 'livrée' plus tard
];

// 1. Sauvegarder dans commandes.json (pour l'admin)
$fichier_commandes = __DIR__ . '/../commandes.json';
$toutes_commandes = file_exists($fichier_commandes) ? json_decode(file_get_contents($fichier_commandes), true) : [];
$toutes_commandes[] = $nouvelle_commande;
file_put_contents($fichier_commandes, json_encode($toutes_commandes, JSON_PRETTY_PRINT));

// 2. Mettre à jour l'historique dans utilisateurs.json (pour le profil)
$fichier_users = __DIR__ . '/../utilisateurs.json';
$utilisateurs = json_decode(file_get_contents($fichier_users), true);

foreach ($utilisateurs as &$u) {
    if ($u['email'] === $email_client) {
        if (!isset($u['orders'])) {
            $u['orders'] = [];
        }
        // Ajouter la commande simplifiée pour le profil
        $u['orders'][] = [
            'date' => $donnees['date'] . ' à ' . $donnees['heure'],
            'total' => $total,
            'items' => $articles_resume
        ];
        $_SESSION['client'] = $u; // Mettre à jour la session
        break;
    }
}
file_put_contents($fichier_users, json_encode($utilisateurs, JSON_PRETTY_PRINT));

echo json_encode(['success' => true]);
?>
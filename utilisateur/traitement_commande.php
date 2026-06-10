<?php
session_start();
header('Content-Type: application/json');

//Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

//Récupérer les données envoyées par panier.js
$donnees_json = file_get_contents('php://input');
$donnees = json_decode($donnees_json, true);

if (!$donnees || empty($donnees['panier'])) {
    echo json_encode(['success' => false, 'message' => 'Panier vide']);
    exit;
}

$email_client = $_SESSION['client']['email'];
$total = 0;
$articles_resume = [];

// Calcul du nouveau total et résumé textuel
foreach ($donnees['panier'] as $item) {
    $total += $item['prix'] * $item['quantite'];
    $articles_resume[] = $item['quantite'] . 'x ' . $item['nom'];
}

$id_commande_modif = $donnees['id_commande_modif'] ?? '';
$is_modification = false;
$ancien_total = 0;

// Chargement de l'historique global des commandes
$fichier_commandes = __DIR__ . '/../commandes.json';
$toutes_commandes = file_exists($fichier_commandes) ? json_decode(file_get_contents($fichier_commandes), true) : [];

// Vérification s'il s'agit d'une modification valide
if (!empty($id_commande_modif)) {
    foreach ($toutes_commandes as $cmd) {
        if ($cmd['id'] === $id_commande_modif) {
            // Sécurité : on ne peut modifier que si le statut est une forme d'"en_attente" (ex: en_attente, en_attente_paiement)
            if (isset($cmd['statut']) && strpos($cmd['statut'], 'en_attente') !== 0) {
                echo json_encode(['success' => false, 'message' => 'La commande est déjà en préparation ou livrée, modification impossible.']);
                exit;
            }
            $ancien_total = (float)$cmd['total'];
            $is_modification = true;
            break;
        }
    }
    $id_commande_unique = $is_modification ? $id_commande_modif : uniqid();
} else {
    $id_commande_unique = uniqid();
}

// nouvelle commande moins chere
if ($is_modification && $total <= $ancien_total) {
    
    // Structure mise à jour de la commande
    $nouvelle_commande = [
        'id' => $id_commande_unique,
        'client_email' => $email_client,
        'client_nom' => $_SESSION['client']['nom'] . ' ' . $_SESSION['client']['prenom'],
        'client_adresse' => $_SESSION['client']['adresse'] ?? 'Adresse non renseignée',
        'client_phone' => $_SESSION['client']['phone'] ?? 'Non renseigné',
        'date_livraison' => $donnees['date'],
        'heure_livraison' => $donnees['heure'],
        'panier' => $donnees['panier'], // Sauvegarde du panier brut
        'total' => $total,
        'statut' => 'en_attente'
    ];

    // Écrasement direct dans commandes.json
    foreach ($toutes_commandes as &$cmd) {
        if ($cmd['id'] === $id_commande_unique) {
            $cmd = $nouvelle_commande;
            break;
        }
    }
    file_put_contents($fichier_commandes, json_encode($toutes_commandes, JSON_PRETTY_PRINT), LOCK_EX);

    // Écrasement direct dans l'historique utilisateurs.json
    $fichier_users = __DIR__ . '/../utilisateurs.json';
    $utilisateurs = file_exists($fichier_users) ? json_decode(file_get_contents($fichier_users), true) : [];

    foreach ($utilisateurs as &$u) {
        if ($u['email'] === $email_client) {
            if (!isset($u['orders'])) {
                $u['orders'] = [];
            }
            foreach ($u['orders'] as &$o) {
                if ($o['id'] === $id_commande_unique) {
                    $o['date'] = $donnees['date'] . ' à ' . $donnees['heure'];
                    $o['total'] = $total;
                    $o['items'] = $articles_resume;
                    break;
                }
            }
            $_SESSION['client'] = $u;
            break;
        }
    }
    file_put_contents($fichier_users, json_encode($utilisateurs, JSON_PRETTY_PRINT), LOCK_EX);

    // Réponse de succès direct (sans données bancaires)
    echo json_encode(['success' => true, 'cybank' => null]);
    exit;
}
//nouvelle commande plus chere
require_once(__DIR__ . '/../getapikey.php');
$vendeur = "MEF-2_C";
$api_key = getAPIKey($vendeur);

// Règle de calcul : On ne paye QUE la différence si c'est une modification supérieure
$montant_final = $is_modification ? max(0, $total - $ancien_total) : $total;
$montant_banque = number_format($montant_final, 2, '.', '');

// Si le montant final est nul (ex: modification moins chère déjà gérée), on enregistre la commande et on renvoie cybank=null
if ($montant_final == 0.00) {
    $nouvelle_commande = [
        'id' => $id_commande_unique,
        'client_email' => $email_client,
        'client_nom' => $_SESSION['client']['nom'] . ' ' . $_SESSION['client']['prenom'],
        'client_adresse' => $_SESSION['client']['adresse'] ?? 'Adresse non renseignée',
        'client_phone' => $_SESSION['client']['phone'] ?? 'Non renseigné',
        'date_livraison' => $donnees['date'],
        'heure_livraison' => $donnees['heure'],
        'panier' => $donnees['panier'],
        'total' => $total,
        'statut' => 'en_attente'
    ];

    // Mettre à jour ou ajouter dans commandes.json
    $trouve = false;
    foreach ($toutes_commandes as &$cmd) {
        if ($cmd['id'] === $id_commande_unique) {
            $cmd = $nouvelle_commande;
            $trouve = true;
            break;
        }
    }
    if (!$trouve) {
        $toutes_commandes[] = $nouvelle_commande;
    }
    file_put_contents($fichier_commandes, json_encode($toutes_commandes, JSON_PRETTY_PRINT), LOCK_EX);

    // Mettre à jour utilisateurs.json
    $fichier_users = __DIR__ . '/../utilisateurs.json';
    $utilisateurs = file_exists($fichier_users) ? json_decode(file_get_contents($fichier_users), true) : [];
    foreach ($utilisateurs as &$u) {
        if ($u['email'] === $email_client) {
            if (!isset($u['orders'])) {
                $u['orders'] = [];
            }
            $trouveU = false;
            foreach ($u['orders'] as &$o) {
                if ($o['id'] === $id_commande_unique) {
                    $o['date'] = $donnees['date'] . ' à ' . $donnees['heure'];
                    $o['total'] = $total;
                    $o['items'] = $articles_resume;
                    $trouveU = true;
                    break;
                }
            }
            if (!$trouveU) {
                $u['orders'][] = ['id' => $id_commande_unique, 'date' => $donnees['date'] . ' à ' . $donnees['heure'], 'total' => $total, 'items' => $articles_resume];
            }
            $_SESSION['client'] = $u;
            break;
        }
    }
    file_put_contents($fichier_users, json_encode($utilisateurs, JSON_PRETTY_PRINT), LOCK_EX);

    // Pas de paiement requis
    echo json_encode(['success' => true, 'cybank' => null]);
    exit;
}

//Avant de rediriger vers la banque, créer une commande provisoire indiquant le montant dû 
$nouvelle_commande = [
    'id' => $id_commande_unique,
    'client_email' => $email_client,
    'client_nom' => $_SESSION['client']['nom'] . ' ' . $_SESSION['client']['prenom'],
    'client_adresse' => $_SESSION['client']['adresse'] ?? 'Adresse non renseignée',
    'client_phone' => $_SESSION['client']['phone'] ?? 'Non renseigné',
    'date_livraison' => $donnees['date'],
    'heure_livraison' => $donnees['heure'],
    'panier' => $donnees['panier'],
    'total' => $total,
    'montant_a_payer' => $montant_banque,
    'statut' => 'en_attente_paiement'
];

// Mettre à jour ou ajouter dans commandes.json
$trouve = false;
foreach ($toutes_commandes as &$cmd) {
    if ($cmd['id'] === $id_commande_unique) {
        $cmd = $nouvelle_commande;
        $trouve = true;
        break;
    }
}
if (!$trouve) {
    $toutes_commandes[] = $nouvelle_commande;
}
file_put_contents($fichier_commandes, json_encode($toutes_commandes, JSON_PRETTY_PRINT), LOCK_EX);

// Mettre à jour utilisateurs.json
$fichier_users = __DIR__ . '/../utilisateurs.json';
$utilisateurs = file_exists($fichier_users) ? json_decode(file_get_contents($fichier_users), true) : [];
foreach ($utilisateurs as &$u) {
    if ($u['email'] === $email_client) {
        if (!isset($u['orders'])) {
            $u['orders'] = [];
        }
        $trouveU = false;
        foreach ($u['orders'] as &$o) {
            if ($o['id'] === $id_commande_unique) {
                $o['date'] = $donnees['date'] . ' à ' . $donnees['heure'];
                $o['total'] = $total;
                $o['items'] = $articles_resume;
                $o['montant_a_payer'] = $montant_banque;
                $o['statut'] = 'en_attente_paiement';
                $trouveU = true;
                break;
            }
        }
        if (!$trouveU) {
            $u['orders'][] = ['id' => $id_commande_unique, 'date' => $donnees['date'] . ' à ' . $donnees['heure'], 'total' => $total, 'items' => $articles_resume, 'montant_a_payer' => $montant_banque, 'statut' => 'en_attente_paiement'];
        }
        $_SESSION['client'] = $u;
        break;
    }
}
file_put_contents($fichier_users, json_encode($utilisateurs, JSON_PRETTY_PRINT), LOCK_EX);


$url_retour = "http://localhost/utilisateur/retour_paiement.php";
$chaine_hash = $api_key.'#'.$id_commande_unique.'#'.$montant_banque.'#'.$vendeur.'#'.$url_retour.'#';
$cle_control = md5($chaine_hash);

echo json_encode([
    'success' => true,
    'cybank' => [
        'transaction' => $id_commande_unique,
        'montant' => $montant_banque,
        'vendeur' => $vendeur,
        'retour' => $url_retour,
        'control' => $cle_control
    ]
]);
?>
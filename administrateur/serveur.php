<?php

    //connexion
    $connexion_adresse_mail = $_POST['connexion_Adresse_e-mail'];
    $connexion_mdp = $_POST['connexion_mdp'];

    //inscription
    $nom = $_POST['nom'];
    $prenom = $_POST['prénom'];
    $adresse = $_POST['adresse'];
    $num_postale = $_POST['Num postale'];
    $localite = $_POST['Localité'];
    $phone = $_POST['phone'];
    $adresse_mail = $_POST['Adresse e-mail'];
    $mdp = $_POST['mdp'];
    $confirmation_mdp = $_POST['confirmation mdp'];
        
    //acceuil
    $search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
    $sections = ['section1', 'section2', 'section3'];

    $targetSection = null;
    foreach ($sections as $section) {
        if (strpos($section, $search) !== false) {
            $targetSection = $section;
            break;
        }
    }

    if ($targetSection) {
        header("Location: index.php#$targetSection");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }

//liste de mots cles
$results = [
    "maki" => [
        "url" => "https://ton-site.fr/html"
    ],
    "samousa" => [
        "url" => "https://ton-site.fr/css"
    ],
    "cheese naan" => [
        "url" => "https://ton-site.fr/php"
    ],
    "poulet tandoori mmariné au whiskey" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "soupe miso" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "brochette" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "onigiri" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "donburi" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "poulpe" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "tonkatsu" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "ramen" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "curry" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "chirachi" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "suchi" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "yakiniku" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "tikkaa massala" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "poulet tandoori" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "dahl" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "aloo gobi" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "ladoo" => [
        "url" => "https://ton-site.fr/javascript"
    ],
    "raita" => [
        "url" => "https://ton-site.fr/javascript"
    ],
];

$filteredResults = [];
foreach ($results as $key => $value) {
    if (stripos($key, $texte_barre_recherche) !== false || stripos($value, $texte_barre_recherche) !== false) {
        $filteredResults[$key] = $value;
    }
}


?>
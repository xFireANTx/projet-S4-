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

    $motsCles = [
        "maki" => "section 1",
        "samousa" => "section 2",
        "cheese naan" => "section 3",
        "poulet tandoori mmariné au whiskey" => "section 4",
        "soupe miso" => "section 5",
        "brochette" => "section 6",
        "onigiri" => "section 7",
        "donburi" => "section 8",
        "poulpe" => "section 9",
    ];

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
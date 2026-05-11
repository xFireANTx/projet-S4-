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
    $sections = ['section1', 'section2', 'section3', 'section4', 'section5', 'section6', 'section7', 'section8', 'section9'];

    $motsCles = [
        "maki" => "section1",
        "samousa" => "section2",
        "cheese naan" => "section3",
        "poulet tandoori mmariné au whiskey" => "section4",
        "soupe miso" => "section5",
        "brochette" => "section6",
        "onigiri" => "section7",
        "donburi" => "section8",
        "poulpe" => "section9",
    ];

    $targetSection = null;
    foreach ($sections as $section) {
        if (strpos($section, $search) !== false) {
            $targetSection = $section;
            break;
        }
    }

    if ($targetSection) {
        header("Location: Accueil.html#$targetSection");
        exit();
    } else {
        header("Location: Acceuil.html");
        exit();
    }
    ?>
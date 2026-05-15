<?php
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

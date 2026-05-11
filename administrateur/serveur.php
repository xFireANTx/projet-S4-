<?php

    //accueil
    $texte_barre_recherche = $_POST['texte_barre_recherche'];

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

/*
 $host = 'localhost'; $dbname = 'uni'; $username = 'root'; $password = ''; &#x200B; 
  // Create a mysqli object and establish a database connection 
  $db = new mysqli($host, $username, $password, $dbname); &#x200B; 
  // Check if the database connection was successful 
  if ($db->connect\_error) { die('Database connection failed: ' . $db->connect\_error); } &#x200B; 
  // Check if the form has been submitted 
  if ($\_SERVER\['REQUEST\_METHOD'\] === 'POST') { 
    // Get the submitted email and password 
    $email = trim($\_POST\['email'\]); $password = trim($\_POST\['password'\]); &#x200B; 
    // Validate the email 
    if (empty($email)) { die("Email is required"); } elseif (!filter\_var($email, FILTER\_VALIDATE\_EMAIL)) { die("Invalid Email"); } &#x200B; 
    // Check if the email already exists in the database 
    $stmt = $db->prepare("SELECT COUNT(\*) FROM data1 WHERE email=?"); $stmt->bind\_param('s', $email); if (!$stmt->execute()) { die('Error executing query: ' . $stmt->error); } $stmt->bind\_result($count); $stmt->fetch(); $stmt->close(); &#x200B; 
    if ($count > 0) { die("Email already exists"); } &#x200B; 
    // Check if the password is at least 8 characters long 
    if (strlen($password) < 8) { die("Password must be at least 8 characters long"); } &#x200B; 
    // Hash the password 
    $password = password\_hash($password, PASSWORD\_DEFAULT); &#x200B; 
    // Generate an activation token 
    $token = bin2hex(random\_bytes(16)); &#x200B; 
    // Insert the user data into the database 
    $stmt = $db->prepare("INSERT INTO data1 (email, password, token) VALUES (?, ?, ?)"); $stmt->bind\_Param('sss', $email, $password, $token); if (!$stmt->execute()) { die('Error executing query: ' . $stmt->error); } $stmt->close(); &#x200B; 
    // Send an activation email 
    $to = $email; $subject = "Activate Your Account"; $message = "Hi,\\r\\n\\r\\nPlease click the following link to activate your account:\\r\\n\\r\\nhttp://localhost/registration/activate.php?token=$token\\r\\n\\r\\nRegards,\\r\\nYour Name"; $headers = "From: sender@email\\r\\n"; mail($to, $subject, $message, $headers); &#x200B; 
    // Redirect to a success page header
    ("Location: success.php"); exit(); }*/
?>
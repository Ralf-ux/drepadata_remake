<?php
// logoutRedirect.php
// This page is shown after logout and redirects user to login page with a button

// Start session and destroy it to ensure logout
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Déconnexion</title>
    <link rel="stylesheet" href="../styles/Home.css" />
    <style>
        body {
            margin: 0;
            height: 100vh;
            background-color: #ffd1d1;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            color: #212529;
        }
        .container {
            text-align: center;
        }
        h1 {
            color: #d32f2f;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .btn-home {
            background-color: #d32f2f;
            color: #ffffff;
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .btn-home:hover {
            background-color: #b71c1c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vous êtes déconnecté</h1>
        <a href="../auth/loginPage.php" class="btn-home" role="button" aria-label="Retour à la page de connexion">Retour à la page de connexion</a>
    </div>
</body>
</html>

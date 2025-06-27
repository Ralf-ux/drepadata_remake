<?php
// Simple error page for unauthorized access
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="../assets/logo/logo-removebg-previewe.png" />
    <title>Erreur d'accès</title>
    <link rel="stylesheet" href="../styles/Home.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8d7da;
            color: #721c24;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .error-container {
            background-color: #f5c6cb;
            border: 1px solid #f1b0b7;
            padding: 30px;
            border-radius: 8px;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        a {
            color: #721c24;
            text-decoration: underline;
            font-weight: bold;
        }
        a:hover {
            color: #491217;
        }
    </style>
</head>
<body>
    <div class="error-container" role="alert" aria-live="assertive">
        <h1>Erreur d'accès</h1>
        <p>Vous êtes sur la mauvaise voie. Veuillez vous connecter ou vous inscrire pour accéder à cette page.</p>
        <p><a href="../auth/loginPage.php">Se connecter</a> | <a href="../auth/signupPage.php">S'inscrire</a></p>
    </div>
</body>
</html>

<?php
// calendrier_vaccinal_du_pev.php - Vaccination calendar form page

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Display any error messages passed via query string
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Calendrier vaccinal du PEV</title>
    <link rel="icon" type="image/png" href="../assets/logo/logo-removebg-preview.png" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fef2f2;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            position: relative;
            min-height: 100vh;
        }

        .animation-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            opacity: 0.25;
        }

        .dna-strand {
            stroke: #d32f2f;
            stroke-width: 3;
            fill: none;
            animation: dnaWave 10s ease-in-out infinite;
        }

        .blood-cell {
            fill: #b71c1c;
            opacity: 0.5;
            animation: float 15s ease-in-out infinite;
        }

        @keyframes dnaWave {
            0%,
            100% {
                transform: translateY(0) scaleY(1);
            }

            50% {
                transform: translateY(-40px) scaleY(1.2);
            }
        }

        @keyframes float {
            0%,
            100% {
                transform: translate(0, 0);
                opacity: 0.5;
            }

            25% {
                transform: translate(50px, -60px);
                opacity: 0.8;
            }

            50% {
                transform: translate(-30px, 80px);
                opacity: 0.4;
            }

            75% {
                transform: translate(60px, 40px);
                opacity: 0.7;
            }
        }

        .dna-strand:nth-child(2) {
            animation-delay: -2s;
        }

        .dna-strand:nth-child(3) {
            animation-delay: -4s;
        }

        .blood-cell:nth-child(4) {
            animation-delay: -3s;
        }

        .blood-cell:nth-child(5) {
            animation-delay: -6s;
        }

        .blood-cell:nth-child(6) {
            animation-delay: -9s;
        }

        .blood-cell:nth-child(7) {
            animation-delay: -12s;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #b91c1c;
            margin-bottom: 1.5rem;
            font-weight: 700;
            font-size: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1rem;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 0.75rem;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #ef4444;
            color: white;
            font-weight: 600;
        }

        input[type="checkbox"] {
            width: 1.25rem;
            height: 1.25rem;
            cursor: pointer;
        }

        label {
            display: block;
            margin-bottom: 0.25rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="animation-container">
        <svg width="100%" height="100%" preserveAspectRatio="none">
            <path class="dna-strand" d="M0,200 C300,50 600,350 900,200 C1200,50 1500,350 1800,200" />
            <path class="dna-strand" d="M0,250 C300,400 600,100 900,250 C1200,400 1500,100 1800,250" />
            <path class="dna-strand" d="M0,300 C300,150 600,450 900,300 C1200,150 1500,450 1800,300" />
            <circle class="blood-cell" cx="200" cy="300" r="12" />
            <circle class="blood-cell" cx="500" cy="600" r="10" />
            <circle class="blood-cell" cx="800" cy="200" r="15" />
            <circle class="blood-cell" cx="1100" cy="400" r="8" />
            <circle class="blood-cell" cx="1400" cy="500" r="11" />
        </svg>
    </div>

    <div class="container">
        <h1>Calendrier vaccinal du PEV</h1>
        <form id="vaccination-calendar-form" action="#" method="POST" novalidate>
            <table>
                <thead>
                    <tr>
                        <th>Période</th>
                        <th>Vaccin</th>
                        <th>Voie d’administration</th>
                        <th>Reçu Oui/Non</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Naissance</td>
                        <td>BCG</td>
                        <td>Intra dermique/Orale</td>
                        <td><input type="checkbox" name="naissance_bcg" /></td>
                    </tr>
                    <tr>
                        <td>6 Semaines</td>
                        <td>
                            DTC- Hep B+Hib 1<br />
                            Pneumo 13-1<br />
                            VPO-1<br />
                            ROTA-1
                        </td>
                        <td>Intra musculaire/Orale</td>
                        <td><input type="checkbox" name="6semaines_dtchb" /></td>
                    </tr>
                    <tr>
                        <td>10 Semaines</td>
                        <td>
                            DTC- Hep B+Hib 2<br />
                            Pneumo 13-2<br />
                            VPO-2<br />
                            ROTA-2
                        </td>
                        <td>Intra musculaire/Orale</td>
                        <td><input type="checkbox" name="10semaines_dtchb" /></td>
                    </tr>
                    <tr>
                        <td>14 Semaines</td>
                        <td>
                            DTC- Hep B+Hib 3<br />
                            Pneumo 13-3<br />
                            VPO-3<br />
                            ROTA-3
                        </td>
                        <td>Intra musculaire/Orale</td>
                        <td><input type="checkbox" name="14semaines_dtchb" /></td>
                    </tr>
                    <tr>
                        <td>9 Mois</td>
                        <td>
                            Vit A<br />
                            VAR<br />
                            VAA
                        </td>
                        <td>Orale<br />Sous cutanée</td>
                        <td><input type="checkbox" name="9mois_vitamins" /></td>
                    </tr>
                </tbody>
            </table>
        </form>
        <a href="consultation.php" class="back-link">Retour à la page de consultation</a>
    </div>
</body>

</html>

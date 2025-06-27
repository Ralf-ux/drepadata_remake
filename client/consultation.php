<?php
// consultation.php - Multi-step consultation form page

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

// Check for Composer dependencies
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Missing dependencies. Please run composer install.');
}

// Display any error messages passed via query string
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Formulaire de Consultation</title>
<link rel="icon" type="image/png" href="../assets/logo/logo-removebg-preview.png" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .step {
            display: none;
        }

        form#consultation-form {
            position: relative;
            z-index: 1;
            background-color: #fef2f2;
            /* Adjusted to match signup page background color */
        }

        .step.active {
            display: block;
        }

        .progress-bar {
            width: 0%;
            transition: width 0.3s ease-in-out;
            background-color: #ef4444;
        }

        .required-asterisk {
            color: #ef4444;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="tel"],
        select,
        textarea {
            border: 2px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.75rem;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="tel"]:focus,
        select:focus,
        textarea:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3);
        }

        .conditional-field {
            display: none;
        }

        .phone-input {
            display: flex;
            align-items: center;
        }

        .phone-prefix {
            background-color: #f3f4f6;
            padding: 0.75rem;
            border: 2px solid #ef4444;
            border-right: none;
            border-radius: 0.375rem 0 0 0.375rem;
            color: #374151;
        }

        .phone-number {
            border-radius: 0 0.375rem 0.375rem 0;
            flex-grow: 1;
            border: 2px solid #ef4444;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .conditional-container {
            margin-top: 1rem;
            margin-left: 1rem;
        }

        button.bg-blue-600 {
            background-color: #ef4444;
        }

        button.bg-blue-600:hover {
            background-color: #dc2626;
        }

        button.bg-green-600 {
            background-color: #16a34a;
        }

        button.bg-green-600:hover {
            background-color: #15803d;
        }

        /* Added styles from signup page for background animation */
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
    </style>
</head>

<body class="bg-gray-100 font-sans">
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
    <div class="container mx-auto p-8 max-w-5xl">
        <h1 class="text-4xl font-bold text-center mb-8">Formulaire de Consultation</h1>
<div class="text-center mb-6" style="position: relative; z-index: 10;">
<a href="../client/calendrier_vaccinal_du_pev.php" class="inline-block bg-red-600 text-white px-6 py-3 rounded-md text-base hover:bg-red-700 focus:ring-2 focus:ring-red-500 cursor-pointer">Accéder au Calendrier vaccinal du PEV</a>
        </div>

        <?php if ($error): ?>
            <script>
                console.error("Database error: <?php echo addslashes(htmlspecialchars($error, ENT_QUOTES, 'UTF-8')); ?>");
            </script>
        <?php endif; ?>

        <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded mb-8 text-base"></div>

        <div class="mb-8">
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-red-600 h-3 rounded-full progress-bar"></div>
            </div>
            <p class="text-center mt-3 text-base text-gray-600">Étape <span id="current-step">1</span> sur <span id="total-steps"></span></p>
        </div>

        <form id="consultation-form" action="save_consultation.php" method="POST" class="bg-white p-8 rounded-lg shadow-md" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
            <input type="hidden" id="current_step_input" name="current_step" value="1" />

            <!-- Step 1: Informations administratives -->
            <div class="step active" data-step="1">
                <h2 class="text-3xl font-semibold mb-6">Informations administratives</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fosa" class="block text-base font-medium text-gray-700">Nom du site (FOSA) <span class="required-asterisk">*</span></label>
                        <select id="fosa" name="fosa" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" required>
                            <option value="">--Sélectionner--</option>
                            <option value="Hôpital Général de Douala">Hôpital Général de Douala</option>
                            <option value="Centre Médical de Yaoundé">Centre Médical de Yaoundé</option>
                            <option value="Hôpital Régional de Bamenda">Hôpital Régional de Bamenda</option>
                            <option value="Autres">Autres</option>
                        </select>
                        <div id="fosa_other_field" class="conditional-container" style="display:none;">
                            <label for="fosa_other" class="block text-base font-medium text-gray-700">Veuillez préciser le FoSA</label>
                            <input type="text" id="fosa_other" name="fosa_other" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                        </div>
                    </div>
                    <div>
                        <label for="region" class="block text-base font-medium text-gray-700">Région <span class="required-asterisk">*</span></label>
                        <select id="region" name="region" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" required>
                            <option value="">--Sélectionner--</option>
                            <option value="Adamaoua">Adamaoua</option>
                            <option value="Centre">Centre</option>
                            <option value="Est">Est</option>
                            <option value="Extrême-Nord">Extrême-Nord</option>
                            <option value="Littoral">Littoral</option>
                            <option value="Nord">Nord</option>
                            <option value="Nord-Ouest">Nord-Ouest</option>
                            <option value="Ouest">Ouest</option>
                            <option value="Sud">Sud</option>
                            <option value="Sud-Ouest">Sud-Ouest</option>
                        </select>
                    </div>
                    <div>
                        <label for="district" class="block text-base font-medium text-gray-700">District de santé</label>
                        <input type="text" id="district" name="district" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="diagnostic_date" class="block text-base font-medium text-gray-700">Date (période) du diagnostic</label>
                        <input type="date" id="diagnostic_date" name="diagnostic_date" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="ipp" class="block text-base font-medium text-gray-700">Numéro de dossier / IPP</label>
                        <input type="text" id="ipp" name="ipp" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="personnel" class="block text-base font-medium text-gray-700">Personnel remplissant le formulaire</label>
                        <input type="text" id="personnel" name="personnel" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="referred" class="block text-base font-medium text-gray-700">Référé</label>
                        <select id="referred" name="referred" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                        <div id="referred_from_field" class="conditional-field conditional-container">
                            <label for="referred_from" class="block text-base font-medium text-gray-700">Référé de</label>
                            <input type="text" id="referred_from" name="referred_from" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                        </div>
                        <div id="referred_for_field" class="conditional-field conditional-container">
                            <label for="referred_for" class="block text-base font-medium text-gray-700">Pour</label>
                            <input type="text" id="referred_for" name="referred_for" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                        </div>
                    </div>
                    <div>
                        <label for="evolution" class="block text-base font-medium text-gray-700">Evolution</label>
                        <select id="evolution" name="evolution" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Suivi régulier">Suivi régulier</option>
                            <option value="Perdu de vue">Perdu de vue</option>
                            <option value="Décédé">Décédé</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 2: Données démographiques -->
            <div class="step" data-step="2">
                <h2 class="text-3xl font-semibold mb-6">Données démographiques</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="full_name" class="block text-base font-medium text-gray-700">Nom et Prénom <span class="required-asterisk">*</span></label>
                        <input type="text" id="full_name" name="full_name" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" required />
                    </div>
                    <div>
                        <label for="age" class="block text-base font-medium text-gray-700">Age</label>
                        <input type="number" id="age" name="age" min="0" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="birth_date" class="block text-base font-medium text-gray-700">Date de naissance</label>
                        <input type="date" id="birth_date" name="birth_date" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="sex" class="block text-base font-medium text-gray-700">Sexe</label>
                        <select id="sex" name="sex" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="M">M</option>
                            <option value="F">F</option>
                        </select>
                    </div>
                    <div>
                        <label for="address" class="block text-base font-medium text-gray-700">Adresse</label>
                        <input type="text" id="address" name="address" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="emergency_contact_name" class="block text-base font-medium text-gray-700">Nom de la personne à contacter en cas d'urgence</label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="emergency_contact_relation" class="block text-base font-medium text-gray-700">Lien avec le patient</label>
                        <select id="emergency_contact_relation" name="emergency_contact_relation" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Father">Père</option>
                            <option value="Mother">Mère</option>
                            <option value="Grandmother">Grand-mère</option>
                            <option value="Grandfather">Grand-père</option>
                            <option value="Brother">Frère</option>
                            <option value="Sister">Sœur</option>
                            <option value="Uncle">Oncle</option>
                            <option value="Aunt">Tante</option>
                            <option value="Other">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label for="emergency_contact_phone" class="block text-base font-medium text-gray-700">Téléphone de la personne à contacter</label>
                        <div class="phone-input mt-2">
                            <span class="phone-prefix">+237</span>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" class="phone-number block border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" pattern="[0-9]{9}" placeholder="6XXXXXXXX" />
                        </div>
                    </div>
                    <div>
                        <label for="lives_with" class="block text-base font-medium text-gray-700">Vit avec le patient</label>
                        <select id="lives_with" name="lives_with" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="insurance" class="block text-base font-medium text-gray-700">Assurance / Couverture sociale</label>
                        <input type="text" id="insurance" name="insurance" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="group" class="block text-base font-medium text-gray-700">Appartient à un groupe/Association de patients drépanocytaires</label>
                        <select id="group" name="group" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                        <div id="group_name_field" class="conditional-field conditional-container">
                            <label for="group_name" class="block text-base font-medium text-gray-700">Nom du groupe/Association</label>
                            <input type="text" id="group_name" name="group_name" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                        </div>
                    </div>
                    <div>
                        <label for="parents" class="block text-base font-medium text-gray-700">Vit avec ses parents biologiques</label>
                        <select id="parents" name="parents" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                        <div id="sibling_rank_field" class="conditional-field conditional-container">
                            <label for="sibling_rank" class="block text-base font-medium text-gray-700">Rang dans la fratrie</label>
                            <input type="number" id="sibling_rank" name="sibling_rank" min="1" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Antécédents médicaux -->
            <div class="step" data-step="3">
                <h2 class="text-3xl font-semibold mb-6">Antécédents médicaux</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sickle_type" class="block text-base font-medium text-gray-700">Type de drépanocytose</label>
                        <select id="sickle_type" name="sickle_type" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="SS">SS</option>
                            <option value="SC">SC</option>
                            <option value="Sβ⁰">Sβ⁰</option>
                            <option value="Sβ⁺">Sβ⁺</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label for="diagnosis_age" class="block text-base font-medium text-gray-700">Age au diagnostic</label>
                        <select id="diagnosis_age" name="diagnosis_age" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="A la naissance">A la naissance</option>
                            <option value="0-3 mois">0-3 mois</option>
                            <option value="4-6 mois">4-6 mois</option>
                            <option value="7-12 mois">7-12 mois</option>
                            <option value="2-3 ans">2-3 ans</option>
                            <option value="4-5 ans">4-5 ans</option>
                        </select>
                    </div>
                    <div>
                        <label for="diagnosis_circumstance" class="block text-base font-medium text-gray-700">Circonstance de diagnostic</label>
                        <select id="diagnosis_circumstance" name="diagnosis_circumstance" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Hospitalisation">Hospitalisation</option>
                            <option value="Contexte familial de drépanocytose">Contexte familial de drépanocytose</option>
                            <option value="Dépistage néonatal">Dépistage néonatal</option>
                            <option value="Anémie sévère">Anémie sévère</option>
                            <option value="Infection grave">Infection grave</option>
                        </select>
                    </div>
                    <div>
                        <label for="family_history" class="block text-base font-medium text-gray-700">Histoire familiale de drépanocytose</label>
                        <select id="family_history" name="family_history" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="seul_dans_la_fratrie">Seul dans la fratrie</option>
                            <option value="2_enfants">2 enfants drépanocytaires</option>
                            <option value="3_enfants">3 enfants drépanocytaires</option>
                            <option value="plus_de_3_enfants">Plus de 3 enfants drépanocytaires</option>
                        </select>
                    </div>
                    <div>
                        <label for="other_medical_history" class="block text-base font-medium text-gray-700">Autres antécédents médicaux</label>
                        <select id="other_medical_history" name="other_medical_history" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="previous_surgeries" class="block text-base font-medium text-gray-700">Chirurgies antérieures</label>
                        <select id="previous_surgeries" name="previous_surgeries" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="allergies" class="block text-base font-medium text-gray-700">Allergies connues</label>
                        <select id="allergies" name="allergies" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 4: Antécédents spécifiques liés à la drépanocytose -->
            <div class="step" data-step="4">
                <h2 class="text-3xl font-semibold mb-6">Antécédents spécifiques liés à la drépanocytose</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="vocs" class="block text-base font-medium text-gray-700">Nombre total d’épisodes de crises vaso-occlusives (3 derniers mois)</label>
                        <select id="vocs" name="vocs" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Aucune">Aucune</option>
                            <option value="moins de 2">moins de 2</option>
                            <option value="3-5">3-5</option>
                            <option value="6-8">6-8</option>
                            <option value="9-10">9-10</option>
                            <option value="plus de 10">plus de 10</option>
                        </select>
                    </div>
                    <div>
<label for="hospitalizations" class="block text-base font-medium text-gray-700">Nombre total d’hospitalisations (3 derniers mois)</label>
<select id="hospitalizations" name="hospitalizations" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
    <option value="">--Sélectionner--</option>
    <option value="0">0</option>
    <option value="moins de 2">moins de 2</option>
    <option value="2-5">2-5</option>
    <option value="6-8">6-8</option>
    <option value="9-10">9-10</option>
    <option value="plus de 10">plus de 10</option>
</select>
                        <div id="hospitalization_cause_field" class="conditional-field conditional-container">
                            <label for="hospitalization_cause" class="block text-base font-medium text-gray-700">Cause de l’hospitalisation</label>
                            <input type="text" id="hospitalization_cause" name="hospitalization_cause" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                        </div>
                    </div>
                    <div>
                        <label for="longest_hospitalization" class="block text-base font-medium text-gray-700">Durée de la plus longue hospitalisation</label>
                        <select id="longest_hospitalization" name="longest_hospitalization" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="moins de 1 semaine">moins de 1 semaine</option>
                            <option value="2-5 semaines">2-5 semaines</option>
                            <option value="5-8 semaines">5-8 semaines</option>
                            <option value="8-10 semaines">8-10 semaines</option>
                            <option value="plus de 10 semaines">plus de 10 semaines</option>
                        </select>
                    </div>
                    <div>
                    </div>
                    <div>
<label class="block text-base font-medium text-gray-700">Evolution du taux d’Hb (3 derniers mois)</label>
<div class="flex gap-4 mt-2">
    <select id="hb_1" name="hb_1" class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
        <option value="">--Sélectionner--</option>
        <option value="<5"><5</option>
        <option value="5-8">5-8</option>
        <option value="8-10">8-10</option>
        <option value="10-13">10-13</option>
        <option value=">13">>13</option>
    </select>
    <select id="hb_2" name="hb_2" class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
        <option value="">--Sélectionner--</option>
        <option value="<5"><5</option>
        <option value="5-8">5-8</option>
        <option value="8-10">8-10</option>
        <option value="10-13">10-13</option>
        <option value=">13">>13</option>
    </select>
    <select id="hb_3" name="hb_3" class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
        <option value="">--Sélectionner--</option>
        <option value="<5"><5</option>
        <option value="5-8">5-8</option>
        <option value="8-10">8-10</option>
        <option value="10-13">10-13</option>
        <option value=">13">>13</option>
    </select>
</div>
                    </div>
                    <div>
                        <label for="recent_hb" class="block text-base font-medium text-gray-700">Taux d’hémoglobine le plus récent</label>
                        <input type="text" id="recent_hb" name="recent_hb" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
<label class="block text-base font-medium text-gray-700">Suivi hémoglobinique HbF (3 derniers mois)</label>
<div class="flex gap-4 mt-2">
    <select id="hbf_1" name="hbf_1" class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
        <option value="">--Sélectionner--</option>
        <option value="<5"><5</option>
        <option value="5-8">5-8</option>
        <option value="8-10">8-10</option>
        <option value="10-13">10-13</option>
        <option value=">13">>13</option>
    </select>
    <select id="hbf_2" name="hbf_2" class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
        <option value="">--Sélectionner--</option>
        <option value="<5"><5</option>
        <option value="5-8">5-8</option>
        <option value="8-10">8-10</option>
        <option value="10-13">10-13</option>
        <option value=">13">>13</option>
    </select>
    <select id="hbf_3" name="hbf_3" class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
        <option value="">--Sélectionner--</option>
        <option value="<5"><5</option>
        <option value="5-8">5-8</option>
        <option value="8-10">8-10</option>
        <option value="10-13">10-13</option>
        <option value=">13">>13</option>
    </select>
</div>
                    </div>
                    <div>
<label class="block text-base font-medium text-gray-700">Suivi hémoglobinique HbS (3 derniers mois)</label>
<div class="flex gap-4 mt-2">
    <select id="hbs_1" name="hbs_1" class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
        <option value="">--Sélectionner--</option>
        <option value="<5"><5</option>
        <option value="5-8">5-8</option>
        <option value="8-10">8-10</option>
        <option value="10-13">10-13</option>
        <option value=">13">>13</option>
    </select>
    <select id="hbs_2" name="hbs_2" class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
        <option value="">--Sélectionner--</option>
        <option value="<5"><5</option>
        <option value="5-8">5-8</option>
        <option value="8-10">8-10</option>
        <option value="10-13">10-13</option>
        <option value=">13">>13</option>
    </select>
    <select id="hbs_3" name="hbs_3" class="block w-1/3 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
        <option value="">--Sélectionner--</option>
        <option value="<5"><5</option>
        <option value="5-8">5-8</option>
        <option value="8-10">8-10</option>
        <option value="10-13">10-13</option>
        <option value=">13">>13</option>
    </select>
</div>
                    </div>
                    <div>
                        <label for="transfusion_reaction" class="block text-base font-medium text-gray-700">Antécédents de réaction transfusionnelle</label>
                        <select id="transfusion_reaction" name="transfusion_reaction" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                        <div id="reaction_types_field" class="conditional-field conditional-container">
                            <label class="block text-base font-medium text-gray-700">Type de réaction</label>
                            <div class="checkbox-group">
                                <label><input type="checkbox" name="reaction_types[]" value="Allergique" class="mr-2">Allergique</label>
                                <label><input type="checkbox" name="reaction_types[]" value="Fébrile" class="mr-2">Fébrile</label>
                                <label><input type="checkbox" name="reaction_types[]" value="Hémolytique" class="mr-2">Hémolytique</label>
                                <label><input type="checkbox" id="reaction_type_other_check" name="reaction_types[]" value="Autre" class="mr-2">Autre</label>
                                <input type="text" id="reaction_type_other" name="reaction_type_other" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base conditional-field" placeholder="Préciser" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="allo_immunization" class="block text-base font-medium text-gray-700">Antécédents d’allo-immunisation</label>
                        <select id="allo_immunization" name="allo_immunization" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="hyperviscosity" class="block text-base font-medium text-gray-700">Signes d’hyperviscosité observés</label>
                        <select id="hyperviscosity" name="hyperviscosity" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="acute_chest_syndrome" class="block text-base font-medium text-gray-700">Épisodes de syndrome thoracique aigu (3 derniers mois)</label>
                        <select id="acute_chest_syndrome" name="acute_chest_syndrome" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="0">0</option>
                            <option value="moins de 2">moins de 2</option>
                            <option value="2-5">2-5</option>
                            <option value="5-8">5-8</option>
                            <option value="8-10">8-10</option>
                            <option value="plus de 10">plus de 10</option>
                        </select>
                    </div>
                    <div>
                        <label for="stroke" class="block text-base font-medium text-gray-700">Antécédent d’AVC</label>
                        <select id="stroke" name="stroke" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="priapism" class="block text-base font-medium text-gray-700">Antécédent de priapisme</label>
                        <select id="priapism" name="priapism" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="leg_ulcer" class="block text-base font-medium text-gray-700">Antécédent d’ulcère de jambe</label>
                        <select id="leg_ulcer" name="leg_ulcer" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="cholecystectomy" class="block text-base font-medium text-gray-700">Antécédent de cholecystectomie</label>
                        <select id="cholecystectomy" name="cholecystectomy" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="asplenia" class="block text-base font-medium text-gray-700">Antécédent d’asplénie fonctionnelle ou splénectomie</label>
                        <select id="asplenia" name="asplenia" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                    </div>
                    <div>
                        <label class="block text-base font-medium text-gray-700">Vaccins recommandés</label>
                        <div class="checkbox-group mt-2">
                            <label><input type="checkbox" name="recommended_vaccines[]" value="Antipneumococcique" class="mr-2">Antipneumococcique</label>
                            <label><input type="checkbox" name="recommended_vaccines[]" value="Antigrippal" class="mr-2">Antigrippal</label>
                            <label><input type="checkbox" name="recommended_vaccines[]" value="Anti méningococcique" class="mr-2">Anti méningococcique</label>
                        </div>
                    </div>
                    <div>
                        <label for="drug_side_effects" class="block text-base font-medium text-gray-700">Effets secondaires liés à un médicament</label>
                        <textarea id="drug_side_effects" name="drug_side_effects" rows="5" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base"></textarea>
                    </div>
                </div>
            </div>

            <!-- Step 5: Traitements en cours -->
            <div class="step" data-step="5">
                <h2 class="text-3xl font-semibold mb-6">Traitements en cours</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="hydroxyurea" class="block text-base font-medium text-gray-700">Hydroxyurée</label>
                        <select id="hydroxyurea" name="hydroxyurea" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                        <div id="tolerance_field" class="conditional-field conditional-container">
                            <label for="tolerance" class="block text-base font-medium text-gray-700">Tolérance</label>
                            <select id="tolerance" name="tolerance" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                                <option value="">--Sélectionner--</option>
                                <option value="Bonne tolérance">Bonne tolérance</option>
                                <option value="Tolérance moyenne">Tolérance moyenne</option>
                                <option value="Mauvaise tolérance">Mauvaise tolérance</option>
                            </select>
                        </div>
                        <div id="hydroxyurea_reasons_field" class="conditional-field conditional-container">
                            <label for="hydroxyurea_reasons" class="block text-base font-medium text-gray-700">Raisons de non-utilisation de l’hydroxyurée</label>
                            <select id="hydroxyurea_reasons" name="hydroxyurea_reasons" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                                <option value="">--Sélectionner--</option>
                                <option value="Non disponible en pharmacie">Non disponible en pharmacie</option>
                                <option value="Manque de moyens financiers">Manque de moyens financiers</option>
                                <option value="Crainte des effets secondaires">Crainte des effets secondaires</option>
                                <option value="Crainte de l’irrégularité dans l’approvisionnement du médicament">Crainte de l’irrégularité dans l’approvisionnement du médicament</option>
                                <option value="Manque de conviction sur l’intérêt du médicament">Manque de conviction sur l’intérêt du médicament</option>
                                <option value="Autre raison">Autre raison</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="hydroxyurea_dosage" class="block text-base font-medium text-gray-700">Posologie de l’hydroxyurée</label>
                        <input type="text" id="hydroxyurea_dosage" name="hydroxyurea_dosage" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="folic_acid" class="block text-base font-medium text-gray-700">Acide folique</label>
                        <select id="folic_acid" name="folic_acid" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="penicillin" class="block text-base font-medium text-gray-700">Antibioprophylaxie (Pénicilline)</label>
                        <select id="penicillin" name="penicillin" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="regular_transfusion" class="block text-base font-medium text-gray-700">Transfusions régulières</label>
                        <select id="regular_transfusion" name="regular_transfusion" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                        <div id="transfusion_details_field" class="conditional-field conditional-container">
                            <label for="transfusion_type" class="block text-base font-medium text-gray-700">Type de transfusion</label>
                            <select id="transfusion_type" name="transfusion_type" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                                <option value="">--Sélectionner--</option>
                                <option value="Simple">Simple</option>
                                <option value="Échange">Échange</option>
                                <option value="Fractionné">Fractionné</option>
                            </select>
                            <label for="transfusion_frequency" class="block text-base font-medium text-gray-700 mt-4">Fréquence des transfusions</label>
                            <input type="text" id="transfusion_frequency" name="transfusion_frequency" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                            <label for="last_transfusion_date" class="block text-base font-medium text-gray-700 mt-4">Date de la dernière transfusion</label>
                            <input type="date" id="last_transfusion_date" name="last_transfusion_date" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                        </div>
                    </div>
                    <div>
                        <label for="other_treatments" class="block text-base font-medium text-gray-700">Autres traitements spécifiques</label>
                        <textarea id="other_treatments" name="other_treatments" rows="5" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base"></textarea>
                    </div>
                </div>
            </div>

            <!-- Step 6: Examens paracliniques complémentaires -->
            <div class="step" data-step="6">
                <h2 class="text-3xl font-semibold mb-6">Examens paracliniques complémentaires</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nfs_gb" class="block text-base font-medium text-gray-700">NFS (GB)</label>
                        <select id="nfs_gb" name="nfs_gb" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="GB inférieur à 4000">GB inférieur à 4000</option>
                            <option value="GB entre 4000-10000">GB entre 4000-10000</option>
                            <option value="GB supérieur à 10000">GB supérieur à 10000</option>
                        </select>
                    </div>
                    <div>
                        <label for="nfs_hb" class="block text-base font-medium text-gray-700">NFS (Hb)</label>
                        <select id="nfs_hb" name="nfs_hb" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="HB inférieure à 5">HB inférieure à 5</option>
                            <option value="HB 5-8">HB 5-8</option>
                            <option value="HB 8-10">HB 8-10</option>
                            <option value="HB 10-13">HB 10-13</option>
                        </select>
                    </div>
                    <div>
                        <label for="nfs_pqts" class="block text-base font-medium text-gray-700">NFS (Pqts)</label>
                        <select id="nfs_pqts" name="nfs_pqts" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Pqttes inférieure à 4000">Pqttes inférieure à 4000</option>
                            <option value="Pqttes 5000-10000">Pqttes 5000-10000</option>
                            <option value="Pqttes 10000-15000">Pqttes 10000-15000</option>
                            <option value="Pqttes sup à 15000">Pqttes sup à 15000</option>
                        </select>
                    </div>
                    <div>
                        <label for="reticulocytes" class="block text-base font-medium text-gray-700">Taux de réticulocytes</label>
                        <input type="text" id="reticulocytes" name="reticulocytes" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="microalbuminuria" class="block text-base font-medium text-gray-700">Microalbuminurie de 24h</label>
                        <input type="text" id="microalbuminuria" name="microalbuminuria" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="hemolysis" class="block text-base font-medium text-gray-700">Bilan d’hémolyse</label>
                        <input type="text" id="hemolysis" name="hemolysis" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                    <div>
                        <label for="gs_rh" class="block text-base font-medium text-gray-700">GS Rh</label>
                        <select id="gs_rh" name="gs_rh" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div>
                        <label for="imagerie_medical" class="block text-base font-medium text-gray-700">Imagerie médicale</label>
                        <select id="imagerie_medical" name="imagerie_medical" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="echo doppler transcranien">echo doppler transcranien</option>
                            <option value="echographie abdomino-pelvienne">echographie abdomino-pelvienne</option>
                            <option value="echographie pleuropulmonaire">echographie pleuropulmonaire</option>
                            <option value="IRM Thoracique">IRM Thoracique</option>
                            <option value="Radio Bassin">Radio Bassin</option>
                            <option value="ECG/Echo coeur">ECG/Echo coeur</option>
                            <option value="Fond d'oeil">Fond d'oeil</option>
                            <option value="consultation spécialisée">consultation spécialisée</option>
                        </select>
                    </div>
                    <div>
                        <label for="ophtalmologie" class="block text-base font-medium text-gray-700">Ophtalmologie</label>
                        <select id="ophtalmologie" name="ophtalmologie" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="FO">FO</option>
                            <option value="Rétinopathie">Rétinopathie</option>
                        </select>
                    </div>
                    <div>
                        <label for="consultations_specialisees" class="block text-base font-medium text-gray-700">Consultations spécialisées associées</label>
                        <select id="consultations_specialisees" name="consultations_specialisees" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Neurologie">Neurologie</option>
                            <option value="Cardiologie">Cardiologie</option>
                            <option value="Psychologie">Psychologie</option>
                            <option value="Orthopédie">Orthopédie</option>
                            <option value="Autres">Autres</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 7: Suivi psychologique et social -->
            <div class="step" data-step="7">
                <h2 class="text-3xl font-semibold mb-6">Suivi psychologique et social</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="impact_scolaire" class="block text-base font-medium text-gray-700">Impact scolaire / absentéisme</label>
                        <select id="impact_scolaire" name="impact_scolaire" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="accompagnement_psychologique" class="block text-base font-medium text-gray-700">Accompagnement psychologique</label>
                        <select id="accompagnement_psychologique" name="accompagnement_psychologique" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="soutien_social" class="block text-base font-medium text-gray-700">Soutien social / Prestations spécifiques</label>
                        <select id="soutien_social" name="soutien_social" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="famille_informee" class="block text-base font-medium text-gray-700">Famille informée et éduquée sur la maladie</label>
                        <select id="famille_informee" name="famille_informee" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
<label for="plan_suivi_personnalise" class="block text-base font-medium text-gray-700">Impact social de la drépanocytose</label>
<select id="plan_suivi_personnalise" name="plan_suivi_personnalise" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
    <option value="">--Sélectionner--</option>
    <option value="absentéisme scolaire">absentéisme scolaire</option>
    <option value="absentéisme professionnel">absentéisme professionnel</option>
    <option value="soutien social">soutien social</option>
    <option value="famille et éducation sur la maladie">famille et éducation sur la maladie</option>
</select>
                    </div>
                    <div>
                        <label for="date_prochaine_consultation" class="block text-base font-medium text-gray-700">Date de prochaine consultation</label>
                        <input type="date" id="date_prochaine_consultation" name="date_prochaine_consultation" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                </div>
            </div>

            <!-- Step 8: Plan de suivi personnalisé -->
            <div class="step" data-step="8">
                <h2 class="text-3xl font-semibold mb-6">Plan de suivi personnalisé</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-base font-medium text-gray-700">Examens à réaliser avant la consultation</label>
                        <div id="examens-container" class="mt-2">
                            <div class="flex items-center mb-3">
                                <input type="text" name="examens_avant_consultation[]" class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" placeholder="Entrez un examen" />
                                <button type="button" id="add-examen-btn" class="ml-2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700" title="Ajouter un autre examen">+</button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="education_therapeutique" class="block text-base font-medium text-gray-700">Éducation thérapeutique prévue</label>
                        <select id="education_therapeutique" name="education_therapeutique" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base">
                            <option value="">--Sélectionner--</option>
                            <option value="Oui">Oui</option>
                            <option value="Non">Non</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_prochaine_consultation_plan" class="block text-base font-medium text-gray-700 mt-4">Date de la prochaine consultation</label>
                        <input type="date" id="date_prochaine_consultation_plan" name="date_prochaine_consultation_plan" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" />
                    </div>
                </div>
            </div>

            <!-- Step 9: Commentaires / Observations libres -->
            <div class="step" data-step="9">
                <h2 class="text-3xl font-semibold mb-6">Commentaires / Observations libres</h2>
                <div>
                    <label for="commentaires" class="block text-base font-medium text-gray-700">Commentaires / Observations libres</label>
                    <textarea id="commentaires" name="commentaires" rows="5" class="mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" placeholder="______________________________________________________________________________ 
______  
______________________________________________________________________________ 
______  
_________"></textarea>
                </div>
            </div>

            <!-- Step 10: Suivi trimestriel / Consultation -->
            <div class="step" data-step="10">
    <h2 class="text-4xl font-bold mb-8 text-blue-700">Calendrier vaccinal du PEV</h2>
    <form id="vaccination-calendar-form" action="save_vaccination_calendar.php" method="POST" novalidate class="bg-white shadow-lg rounded-lg p-6">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
        <table class="min-w-full border-collapse border-2 border-blue-500">
            <thead>
                <tr class="bg-blue-100">
                    <th class="border-2 border-blue-500 px-6 py-4 text-left text-lg font-semibold text-red-600">Période</th>
                    <th class="border-2 border-blue-500 px-6 py-4 text-left text-lg font-semibold text-red-600">Vaccin</th>
                    <th class="border-2 border-blue-500 px-6 py-4 text-left text-lg font-semibold text-red-600">Voie d’administration</th>
                    <th class="border-2 border-blue-500 px-6 py-4 text-left text-lg font-semibold text-red-600">Reçu Oui/Non</th>
                </tr>
            </thead>
            <tbody>
                <!-- Naissance -->
                <tr class="hover:bg-blue-50">
                    <td rowspan="1" class="border-2 border-blue-500 px-6 py-4 text-center align-middle text-gray-700 font-medium bg-blue-50">Naissance</td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[bcg][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">BCG</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[bcg][administration][]" value="Intra dermique" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra dermique</label>
                        <label class="block"><input type="checkbox" name="vaccination[bcg][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[bcg][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[bcg][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <!-- Section Separator -->
                <tr class="section-divider"><td colspan="4" class="border-t-4 border-blue-700"></td></tr>

                <!-- 6 Semaines -->
                <tr class="hover:bg-blue-50">
                    <td rowspan="4" class="border-2 border-blue-500 px-6 py-4 text-center align-middle text-gray-700 font-medium bg-blue-50">6 Semaines</td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[six_weeks_dtchb1][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">DTC- Hep B+Hib 1</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[six_weeks_dtchb1][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[six_weeks_dtchb1][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[six_weeks_dtchb1][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[six_weeks_dtchb1][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[six_weeks_pneumo13_1][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">Pneumo 13-1</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[six_weeks_pneumo13_1][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[six_weeks_pneumo13_1][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[six_weeks_pneumo13_1][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[six_weeks_pneumo13_1][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[six_weeks_vpo1][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">VPO-1</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[six_weeks_vpo1][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[six_weeks_vpo1][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[six_weeks_vpo1][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[six_weeks_vpo1][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[six_weeks_rota1][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">ROTA-1</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[six_weeks_rota1][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[six_weeks_rota1][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[six_weeks_rota1][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[six_weeks_rota1][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <!-- Section Separator -->
                <tr class="section-divider"><td colspan="4" class="border-t-4 border-blue-700"></td></tr>

                <!-- 10 Semaines -->
                <tr class="hover:bg-blue-50">
                    <td rowspan="4" class="border-2 border-blue-500 px-6 py-4 text-center align-middle text-gray-700 font-medium bg-blue-50">10 Semaines</td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[ten_weeks_dtchb2][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">DTC- Hep B+Hib 2</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[ten_weeks_dtchb2][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[ten_weeks_dtchb2][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[ten_weeks_dtchb2][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[ten_weeks_dtchb2][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[ten_weeks_pneumo13_2][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">Pneumo 13-2</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[ten_weeks_pneumo13_2][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[ten_weeks_pneumo13_2][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[ten_weeks_pneumo13_2][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[ten_weeks_pneumo13_2][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[ten_weeks_vpo2][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">VPO-2</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[ten_weeks_vpo2][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[ten_weeks_vpo2][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[ten_weeks_vpo2][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[ten_weeks_vpo2][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[ten_weeks_rota2][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">ROTA-2</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[ten_weeks_rota2][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[ten_weeks_rota2][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[ten_weeks_rota2][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[ten_weeks_rota2][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <!-- Section Separator -->
                <tr class="section-divider"><td colspan="4" class="border-t-4 border-blue-700"></td></tr>

                <!-- 14 Semaines -->
                <tr class="hover:bg-blue-50">
                    <td rowspan="4" class="border-2 border-blue-500 px-6 py-4 text-center align-middle text-gray-700 font-medium bg-blue-50">14 Semaines</td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[fourteen_weeks_dtchb3][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">DTC- Hep B+Hib 3</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[fourteen_weeks_dtchb3][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[fourteen_weeks_dtchb3][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[fourteen_weeks_dtchb3][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[fourteen_weeks_dtchb3][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[fourteen_weeks_pneumo13_3][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">Pneumo 13-3</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[fourteen_weeks_pneumo13_3][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[fourteen_weeks_pneumo13_3][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[fourteen_weeks_pneumo13_3][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[fourteen_weeks_pneumo13_3][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[fourteen_weeks_vpo3][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">VPO-3</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[fourteen_weeks_vpo3][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[fourteen_weeks_vpo3][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[fourteen_weeks_vpo3][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[fourteen_weeks_vpo3][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[fourteen_weeks_rota3][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">ROTA-3</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[fourteen_weeks_rota3][administration][]" value="Intra musculaire" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Intra musculaire</label>
                        <label class="block"><input type="checkbox" name="vaccination[fourteen_weeks_rota3][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[fourteen_weeks_rota3][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[fourteen_weeks_rota3][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <!-- Section Separator -->
                <tr class="section-divider"><td colspan="4" class="border-t-4 border-blue-700"></td></tr>

                <!-- 9 Mois -->
                <tr class="hover:bg-blue-50">
                    <td rowspan="3" class="border-2 border-blue-500 px-6 py-4 text-center align-middle text-gray-700 font-medium bg-blue-50">9 Mois</td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[nine_months_vita][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">Vit A</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[nine_months_vita][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                        <label class="block"><input type="checkbox" name="vaccination[nine_months_vita][administration][]" value="Sous cutanée" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Sous cutanée</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[nine_months_vita][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[nine_months_vita][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[nine_months_var][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">VAR</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[nine_months_var][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                        <label class="block"><input type="checkbox" name="vaccination[nine_months_var][administration][]" value="Sous cutanée" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Sous cutanée</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[nine_months_var][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[nine_months_var][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
                <tr class="hover:bg-blue-50">
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vaccination[nine_months_vaa][vaccine]" class="h-5 w-5 text-blue-600 border-gray-300 rounded" /> 
                            <span class="text-gray-800">VAA</span>
                        </label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="checkbox" name="vaccination[nine_months_vaa][administration][]" value="Orale" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Orale</label>
                        <label class="block"><input type="checkbox" name="vaccination[nine_months_vaa][administration][]" value="Sous cutanée" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Sous cutanée</label>
                    </td>
                    <td class="border-2 border-blue-500 px-6 py-4">
                        <label class="block mb-2"><input type="radio" name="vaccination[nine_months_vaa][received]" value="Oui" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Oui</label>
                        <label class="block"><input type="radio" name="vaccination[nine_months_vaa][received]" value="Non" class="h-5 w-5 text-blue-600 border-gray-300 rounded mr-2" /> Non</label>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>


            <!-- Navigation Buttons -->
            <div class="mt-8 flex justify-between items-center">
                <button type="button" id="prev-btn" class="bg-gray-600 text-white px-6 py-3 rounded-md text-base hover:bg-gray-700 focus:ring-2 focus:ring-red-500" disabled>Précédent</button>
                <button type="button" id="next-btn" class="bg-blue-600 text-white px-6 py-3 rounded-md text-base hover:bg-red-700 focus:ring-2 focus:ring-red-500">Suivant</button>
                <button type="submit" id="submit-btn" class="hidden bg-green-600 text-white px-6 py-3 rounded-md text-base hover:bg-green-700 focus:ring-2 focus:ring-green-500">Soumettre</button>
            </div>
        </form>
    </div>

    <script>
        const steps = document.querySelectorAll('.step');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');
        const progressBar = document.querySelector('.progress-bar');
        const currentStepDisplay = document.getElementById('current-step');
        const totalStepsDisplay = document.getElementById('total-steps');
        const errorMessage = document.getElementById('error-message');
        const form = document.getElementById('consultation-form');
        let currentStep = 1;

        totalStepsDisplay.textContent = steps.length;

        function updateProgress() {
            const progress = ((currentStep - 1) / (steps.length - 1)) * 100;
            progressBar.style.width = `${progress}%`;
            currentStepDisplay.textContent = currentStep;
            prevBtn.disabled = currentStep === 1;
            nextBtn.classList.toggle('hidden', currentStep === steps.length);
            submitBtn.classList.toggle('hidden', currentStep !== steps.length);
        }

        function showStep(step) {
            steps.forEach((s, index) => {
                s.classList.toggle('active', index + 1 === step);
            });
            document.getElementById('current_step_input').value = step;
            updateProgress();
        }

        function validateStep(step) {
            const currentStepElement = document.querySelector(`.step[data-step="${step}"]`);
            const requiredFields = currentStepElement.querySelectorAll('[required]');
            let isValid = true;
            errorMessage.classList.add('hidden');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    field.classList.remove('border-gray-300');
                } else {
                    field.classList.remove('border-red-500');
                    field.classList.add('border-gray-300');
                }
            });

            if (!isValid) {
                errorMessage.textContent = 'Veuillez remplir tous les champs obligatoires.';
                errorMessage.classList.remove('hidden');
            }

            return isValid;
        }

        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (validateStep(currentStep)) {
                if (currentStep < steps.length) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        });

        // Handle conditional fields
        function toggleConditionalField(selectId, fieldId, showValue) {
            const select = document.getElementById(selectId);
            const field = document.getElementById(fieldId);
            if (!select || !field) {
                return; // Do nothing if elements not found
            }
            function toggleField() {
                field.classList.toggle('conditional-field', select.value !== showValue);
            }
            select.addEventListener('change', toggleField);
            // Initial check on page load
            toggleField();
        }

        toggleConditionalField('referred', 'referred_from_field', 'Oui');
        toggleConditionalField('referred', 'referred_for_field', 'Oui');
        toggleConditionalField('group', 'group_name_field', 'Oui');
        toggleConditionalField('parents', 'sibling_rank_field', 'Oui');
        toggleConditionalField('hospitalizations', 'hospitalization_cause_field', '0');
        toggleConditionalField('transfusion_reaction', 'reaction_types_field', 'Oui');
        toggleConditionalField('vaccination', 'vaccination_types_field', 'Oui');
        toggleConditionalField('hydroxyurea', 'tolerance_field', 'Oui');
        toggleConditionalField('hydroxyurea', 'hydroxyurea_reasons_field', 'Non');
        toggleConditionalField('regular_transfusion', 'transfusion_details_field', 'Oui');
        // Improved toggle for fosa_other_field to use style.display and required attribute
        document.addEventListener('DOMContentLoaded', () => {
            const fosaSelect = document.getElementById('fosa');
            const fosaOtherField = document.getElementById('fosa_other_field');
            const fosaOtherInput = document.getElementById('fosa_other');

            function toggleFosaOtherField() {
                if (fosaSelect.value && fosaSelect.value.toLowerCase() === 'autres') {
                    fosaOtherField.style.display = 'block';
                    fosaOtherInput.required = true;
                } else {
                    fosaOtherField.style.display = 'none';
                    fosaOtherInput.required = false;
                    fosaOtherInput.value = '';
                }
            }

            fosaSelect.addEventListener('change', toggleFosaOtherField);
            // Initial check on page load
            toggleFosaOtherField();
        });

        // Handle dynamic examens fields
        const examensContainer = document.getElementById('examens-container');
        const addExamenBtn = document.getElementById('add-examen-btn');

        addExamenBtn.addEventListener('click', () => {
            const div = document.createElement('div');
            div.className = 'flex items-center mb-3';
            div.innerHTML = `
                <input type="text" name="examens_avant_consultation[]" class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 p-3 text-base" placeholder="Entrez un examen" />
                <button type="button" class="remove-examen-btn ml-2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700" title="Supprimer cet examen">-</button>
            `;
            examensContainer.appendChild(div);

            // Add event listener for remove button
            div.querySelector('.remove-examen-btn').addEventListener('click', () => {
                div.remove();
            });
        });

        // Form submission validation
        form.addEventListener('submit', (e) => {
            if (!validateStep(currentStep)) {
                e.preventDefault();
            }
        });

        // Auto-hide error message after 5 seconds if visible
        const errorMessageDiv = document.getElementById('error-message');
        if (!errorMessageDiv.classList.contains('hidden')) {
            setTimeout(() => {
                errorMessageDiv.classList.add('hidden');
            }, 5000);
        }
        // Auto-hide PHP error message after 5 seconds if visible
        const phpErrorMessageDiv = document.getElementById('php-error-message');
        if (phpErrorMessageDiv) {
            setTimeout(() => {
                phpErrorMessageDiv.style.display = 'none';
            }, 3000);
        }

        // Auto-calculate age from dob and dob from age
        const birthDateInput = document.getElementById('birth_date');
        const ageInput = document.getElementById('age');

        let isUpdating = false;

        function calculateAgeFromDOB(dob) {
            const today = new Date();
            const birthDate = new Date(dob);
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age >= 0 ? age : '';
        }

        function calculateDOBFromAge(age) {
            const today = new Date();
            const birthYear = today.getFullYear() - age;
            // Set dob to Dec 31 of birthYear for better accuracy
            return new Date(birthYear, 11, 31).toISOString().split('T')[0];
        }

        birthDateInput.addEventListener('change', () => {
            if (isUpdating) return;
            isUpdating = true;
            const dobValue = birthDateInput.value;
            if (dobValue) {
                const age = calculateAgeFromDOB(dobValue);
                ageInput.value = age;
            } else {
                ageInput.value = '';
            }
            isUpdating = false;
        });

        ageInput.addEventListener('input', () => {
            if (isUpdating) return;
            isUpdating = true;
            const ageValue = parseInt(ageInput.value, 10);
            if (!isNaN(ageValue) && ageValue >= 0) {
                const dob = calculateDOBFromAge(ageValue);
                birthDateInput.value = dob;
            } else {
                birthDateInput.value = '';
            }
            isUpdating = false;
        });
    </script>
</body>

</html>
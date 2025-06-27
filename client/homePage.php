<!--  -->
<?php
session_start();
$welcomeMessage = '';
if (isset($_SESSION['user_name'])) {
    $welcomeMessage = 'Bienvenue, ' . htmlspecialchars($_SESSION['user_name']) . ' !';
    // Unset the session variable to show the message only once
    unset($_SESSION['user_name']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/logo/logo-removebg-previewe.png" type="image/png" />
    <link rel="stylesheet" href="../styles/Home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
     <link rel="stylesheet" href="../styles/loader.css">
    <title>Drepadata</title>
    <style>
        /* Loader Styles */
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .loader-moon {
            width: 50px;
            height: 50px;
            border: 5px solid #d32f2f;
            border-radius: 50%;
            border-right-color: transparent;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        body.blur {
            filter: blur(5px);
        }
        #welcome-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 1.2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            opacity: 1;
            transition: opacity 1s ease;
            z-index: 1100;
        }
        #welcome-message.hide {
            opacity: 0;
        }
    </style>
</head>
<body>
    <?php if ($welcomeMessage): ?>
        <div id="welcome-message" role="alert" aria-live="assertive"><?php echo $welcomeMessage; ?></div>
    <?php endif; ?>
    <div id="loader-overlay" class="loader-overlay">
        <div class="loader-moon"></div>
    </div>
    <header>
        <nav>
            <div class="LogoC"></div>
            <div class="Getstarted-button-Container">
                <a href="../client/aboutPage.php" class="nav-link" onclick="showLoader()">À propos</a>
                <button class="login-btn" onclick="showLoader(); window.location.href='../auth/loginPage.php'">Se connecter</button>
            </div>
        </nav>
    </header>

    <section class="text-and-image">
        <div class="landing-text">
            <h1 id="mainMessage" class="fade-in">Partagez vos données pour lutter contre la drépanocytose</h1>
            <h2 id="subMessage" class="fade-in">Contribuez à la recherche pour des solutions innovantes</h2>
            <div class="text-and-image-buttons">
                <button class="get-started-btn" onclick="showLoader(); window.location.href='../auth/signupPage.php'">Commencer</button>
                <button class="login-btn" onclick="showLoader(); window.location.href='../auth/loginPage.php'">Se connecter</button>
            </div>
        </div>
        <div class="landing-image">
            <img id="landingImage" src="https://via.placeholder.com/300?text=Sickle+Cell+Research" alt="Consultations Drepadata" class="fade-in" />
        </div>
    </section>

    <section class="why">
        <h2>Pourquoi utiliser <span>Drepadata</span></h2>
        <div class="message-container">
            <div class="why-card">
                <i class="fas fa-flask why-icon"></i>
                <h3>Contribuer à la recherche</h3>
                <p>Vos données aident à mieux comprendre la drépanocytose.</p>
            </div>
            <div class="why-card">
                <i class="fas fa-calendar-check why-icon"></i>
                <h3>Suivre vos consultations</h3>
                <p>Enregistrez facilement chaque consultation pour un suivi précis.</p>
            </div>
            <div class="why-card">
                <i class="fas fa-users why-icon"></i>
                <h3>Soutenir les patients</h3>
                <p>Partagez vos expériences pour un accompagnement plus efficace.</p>
            </div>
            <div class="why-card">
                <i class="fas fa-shield-alt why-icon"></i>
                <h3>Données sécurisées</h3>
                <p>Vos informations sont protégées et utilisées de manière éthique.</p>
            </div>
        </div>
    </section>

    <section class="how-to-use">
        <h2>Comment utiliser <span>Drepadata</span></h2>
        <div class="how-to-use-container">
            <div class="how-to-use-card">
                <span class="step-number">1</span>
                <i class="fas fa-user-plus how-to-use-icon"></i>
                <h3>Créer un compte</h3>
                <p>Inscrivez-vous pour commencer à contribuer vos données.</p>
            </div>
            <div class="how-to-use-card">
                <span class="step-number">2</span>
                <i class="fas fa-notes-medical how-to-use-icon"></i>
                <h3>Enregistrer une consultation</h3>
                <p>Documentez vos consultations pour un suivi sécurisé.</p>
            </div>
            <div class="how-to-use-card">
                <span class="step-number">3</span>
                <i class="fas fa-database how-to-use-icon"></i>
                <h3>Télécharger les données</h3>
                <p>Partagez vos données pour soutenir la recherche.</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo"></div>
            <p class="footer-tagline">Favoriser la recherche sur la drépanocytose grâce aux données</p>
            <div class="footer-contact">
                <p>Email: <a href="mailto:drepadata@gmail.com">drepadata@gmail.com</a></p>
                <p>Contact: <a href="tel:+237691627438">+237 691-627-438</a></p>
            </div>
            <div class="footer-social-media">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="https://facebook.com" aria-label="Facebook" target="_blank" rel="noopener noreferrer" onclick="showLoader()">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com" aria-label="Twitter" target="_blank" rel="noopener noreferrer" onclick="showLoader()">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://instagram.com" aria-label="Instagram" target="_blank" rel="noopener noreferrer" onclick="showLoader()">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://linkedin.com" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer" onclick="showLoader()">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Drepadata. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const messages = [
            "Partagez vos données pour lutter contre la drépanocytose",
            "Contribuez à la recherche sur la drépanocytose",
            "Aidez à trouver des solutions grâce à vos données",
            "Enregistrez vos expériences pour la recherche",
            "Soutenez la science avec vos données médicales",
            "Documentez vos crises pour aider la recherche",
            "Participez à la lutte contre la drépanocytose",
            "Collaborez pour des avancées médicales"
        ];

        const subtitles = [
            "Contribuez à la recherche pour des solutions innovantes",
            "Vos données peuvent accélérer les découvertes médicales",
            "Aidez les chercheurs à mieux comprendre la maladie",
            "Votre contribution fait avancer les traitements",
            "Partagez en toute sécurité pour un avenir meilleur",
            "Chaque donnée compte pour combattre la drépanocytose",
            "Rejoignez la communauté pour des solutions durables",
            "Votre participation soutient la recherche mondiale"
        ];

        const images = [
            '../assets/landing images/1-removebg-preview.png',
            '../assets/landing images/3-removebg-preview.png',
            '../assets/landing images/4-removebg-preview.png',
            '../assets/landing images/5-removebg-preview.png',
            '../assets/landing images/22-removebg-preview.png',
            '../assets/landing images/33-removebg-preview.png',
            '../assets/landing images/44-removebg-preview.png',
            '../assets/landing images/66-removebg-preview.png'
        ];

        let previousImage = null;

        function updateContent() {
            const mainMessage = document.getElementById('mainMessage');
            const subMessage = document.getElementById('subMessage');
            const landingImage = document.getElementById('landingImage');

            mainMessage.classList.remove('fade-in');
            mainMessage.classList.add('fade-out');
            subMessage.classList.remove('fade-in');
            subMessage.classList.add('fade-out');
            landingImage.classList.remove('fade-in');
            landingImage.classList.add('fade-out');

            let randomIndex;
            do {
                randomIndex = Math.floor(Math.random() * images.length);
            } while (images[randomIndex] === previousImage && images.length > 1);

            const messageIndex = Math.floor(Math.random() * messages.length);
            mainMessage.textContent = messages[messageIndex];
            subMessage.textContent = subtitles[messageIndex];
            landingImage.src = images[randomIndex];
            previousImage = images[randomIndex];

            mainMessage.classList.remove('fade-out');
            mainMessage.classList.add('fade-in');
            subMessage.classList.remove('fade-out');
            subMessage.classList.add('fade-in');
            landingImage.classList.remove('fade-out');
            landingImage.classList.add('fade-in');
        }

        // Initial call to set content
        updateContent();

        // Rotate content every 8 seconds
        setInterval(updateContent, 8000);

        function showLoader() {
            const loaderOverlay = document.getElementById('loader-overlay');
            loaderOverlay.style.display = 'flex';
            document.body.classList.add('blur');
            document.body.style.pointerEvents = 'none';
            setTimeout(hideLoader, 2000); // Hide after 2 seconds
        }

        function hideLoader() {
            const loaderOverlay = document.getElementById('loader-overlay');
            loaderOverlay.style.display = 'none';
            document.body.classList.remove('blur');
            document.body.style.pointerEvents = 'auto';
        }

        // Attach loader to all buttons and links
        document.querySelectorAll('button, a.nav-link').forEach(element => {
            element.addEventListener('click', (e) => {
                showLoader();
            });
        });

        // Hide loader on page load
        window.addEventListener('load', hideLoader);
    </script>
</body>
</html>
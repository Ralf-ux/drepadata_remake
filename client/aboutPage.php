<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="../assets/logo/logo-removebg-previewe.png" />
    <link rel="stylesheet" href="../styles/Home.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
     <link rel="stylesheet" href="../styles/loader.css">
    <title>À propos - Drepadata</title>
   
</head>
<body>
    <div id="loader-overlay" class="loader-overlay">
        <div class="loader-moon"></div>
    </div>
    <header>
        <nav>
            <a href="homePage.php" aria-label="Accueil">
                <div class="LogoC"></div>
            </a>
            <div class="Getstarted-button-Container">
                <a href="homePage.php" class="nav-link">Accueil</a>
                <a href="aboutPage.php" class="nav-link active">À propos</a>
                <button class="login-btn" onclick="showLoader(); window.location.href='../auth/loginPage.php'">Se connecter</button>
            </div>
        </nav>
    </header>

    <main class="about-content">
        <h1>Comment Drepadata aide à la collecte de données sur la drépanocytose</h1>
        <p>
            Drepadata est une plateforme dédiée à la collecte sécurisée et éthique de données médicales
            concernant la drépanocytose. En partageant vos données, vous contribuez à la recherche
            scientifique visant à mieux comprendre cette maladie et à développer des traitements innovants.
        </p>
        <p>
            Notre application facilite l'enregistrement des consultations, la documentation des crises
            et le suivi personnalisé des patients atteints de drépanocytose. Les données collectées sont
            anonymisées et protégées pour garantir la confidentialité et le respect de la vie privée.
        </p>
        <p>
            En rejoignant la communauté Drepadata, vous participez activement à la lutte contre la drépanocytose
            et soutenez les chercheurs dans leurs efforts pour améliorer la qualité de vie des patients.
        </p>
        <p>
            Ensemble, nous pouvons faire avancer la recherche et trouver des solutions durables.
        </p>
        <a href="homePage.php" class="back-link" onclick="showLoader()">Retour à l'accueil</a>
    </main>

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
        document.querySelectorAll('button, a').forEach(element => {
            if (element.tagName === 'A' || element.tagName === 'BUTTON') {
                element.addEventListener('click', (e) => {
                    showLoader();
                });
            }
        });

        // Hide loader on page load
        window.addEventListener('load', hideLoader);
    </script>
</body>
</html>
 
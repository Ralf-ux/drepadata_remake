<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Drepadata - Connexion</title>
    <link rel="icon" href="../assets/logo/logo-removebg-previewe.png" type="image/png" />
    <link rel="stylesheet" href="../styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .loader-moon {
            width: 60px;
            height: 60px;
            border: 6px solid #d32f2f;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        body.blur {
            filter: blur(4px);
        }
        .message-success, .message-error {
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            color: white;
            text-align: center;
            opacity: 1;
            transition: opacity 0.5s ease, transform 0.3s ease;
            transform: translateY(0);
        }
        .message-success {
            background-color: #28a745;
        }
        .message-error {
            background-color: #dc3545;
        }
        .message-hidden {
            opacity: 0;
            transform: translateY(-10px);
            height: 0;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        /* Animation styles */
        .animation-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
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
            0%, 100% { transform: translateY(0) scaleY(1); }
            50% { transform: translateY(-40px) scaleY(1.2); }
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0); opacity: 0.5; }
            25% { transform: translate(50px, -60px); opacity: 0.8; }
            50% { transform: translate(-30px, 80px); opacity: 0.4; }
            75% { transform: translate(60px, 40px); opacity: 0.7; }
        }
        .dna-strand:nth-child(2) { animation-delay: -2s; }
        .dna-strand:nth-child(3) { animation-delay: -4s; }
        .blood-cell:nth-child(4) { animation-delay: -3s; }
        .blood-cell:nth-child(5) { animation-delay: -6s; }
        .blood-cell:nth-child(6) { animation-delay: -9s; }
        .blood-cell:nth-child(7) { animation-delay: -12s; }
    </style>
</head>
<body>
    <div class="animation-container" aria-hidden="true">
        <svg width="100%" height="100%" preserveAspectRatio="none" focusable="false" aria-hidden="true">
            <!-- DNA Strands -->
            <path class="dna-strand" d="M0,200 C300,50 600,350 900,200 C1200,50 1500,350 1800,200" />
            <path class="dna-strand" d="M0,250 C300,400 600,100 900,250 C1200,400 1500,100 1800,250" />
            <path class="dna-strand" d="M0,300 C300,150 600,450 900,300 C1200,150 1500,450 1800,300" />
            <!-- Blood Cells -->
            <circle class="blood-cell" cx="200" cy="300" r="12" />
            <circle class="blood-cell" cx="500" cy="600" r="10" />
            <circle class="blood-cell" cx="800" cy="200" r="15" />
            <circle class="blood-cell" cx="1100" cy="400" r="8" />
            <circle class="blood-cell" cx="1400" cy="500" r="11" />
        </svg>
    </div>
    <div id="loader-overlay" class="loader-overlay" aria-live="polite" aria-label="Chargement en cours">
        <div class="loader-moon"></div>
    </div>
    <div class="login-container">
        <a href="../client/homePage.php" class="return-arrow" aria-label="Retour à l'accueil" onclick="showLoader()">
            <i class="fas fa-arrow-left" id="arrow"></i>
        </a>
        <div class="login-message" role="region" aria-live="polite" aria-atomic="true">
            <h2>Connectez-vous à Drepadata</h2>
            <p>Accédez à votre compte pour contribuer à la recherche sur la drépanocytose.</p>
            <div class="message-highlight" id="span">
                <span>Rejoignez la communauté pour faire avancer la science</span>
            </div>
        </div>
        <div class="login-form-card" role="form" aria-label="Formulaire de connexion">
            <h3 id="connect">Se connecter</h3>
            <div id="form-messages" aria-live="polite" aria-atomic="true">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="message-error"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
            </div>
<form id="login-form" class="login-form" method="POST" action="/server/login.php" novalidate aria-label="Formulaire de connexion">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Votre email" autocomplete="email" required aria-required="true" aria-describedby="form-messages" />
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Votre mot de passe" required aria-required="true" aria-describedby="form-messages" />
                        <button type="button" class="toggle-password" aria-label="Afficher le mot de passe">
                            <i id="toggle-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" id="submit-btn" aria-label="Se connecter" disabled>Se connecter</button>
                <p class="signup-link">
                    Pas de compte ? <a href="../auth/signupPage.php" aria-label="Créer un compte" onclick="showLoader()">Créer un compte</a>
                </p>
            </form>
        </div>
    </div>
    <footer class="footer">
        <p>© 2025 Drepadata. Tous droits réservés.</p>
    </footer>
    <script>
        // Loader functions
        function showLoader() {
            const loaderOverlay = document.getElementById('loader-overlay');
            loaderOverlay.style.display = 'flex';
            document.body.classList.add('blur');
            document.body.style.pointerEvents = 'none';
        }

        function hideLoader() {
            const loaderOverlay = document.getElementById('loader-overlay');
            loaderOverlay.style.display = 'none';
            document.body.classList.remove('blur');
            document.body.style.pointerEvents = 'auto';
        }

        // Toggle submit button based on input values
        function toggleSubmitButton() {
            const emailValue = document.getElementById('email').value.trim();
            const passwordValue = document.getElementById('password').value.trim();
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = !(emailValue.length > 0 || passwordValue.length > 0);
        }

        // Restrict invalid email characters
        function restrictInput(event) {
            const emailInput = event.target;
            if (emailInput.id === 'email') {
                const value = emailInput.value;
                const validEmailChars = /^[a-zA-Z0-9._%+-@]*$/;
                if (value && !validEmailChars.test(value)) {
                    emailInput.value = value.replace(/[^a-zA-Z0-9._%+-@]/g, '');
                }
            }
        }

        // Password toggle visibility
        const passwordInput = document.getElementById('password');
        const togglePassword = document.querySelector('.toggle-password');
        const toggleIcon = document.getElementById('toggle-icon');

        togglePassword.addEventListener('click', () => {
            const isPasswordVisible = passwordInput.type === 'password';
            passwordInput.type = isPasswordVisible ? 'text' : 'password';
            toggleIcon.classList.toggle('fa-eye', isPasswordVisible);
            toggleIcon.classList.toggle('fa-eye-slash', !isPasswordVisible);
            togglePassword.setAttribute('aria-label', isPasswordVisible ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
        });

        // Handle form submission
        document.getElementById('login-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formMessages = document.getElementById('form-messages');
            formMessages.innerHTML = '';

            // Client-side validation
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!email || !password) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message-error';
                messageDiv.textContent = 'Veuillez remplir tous les champs obligatoires.';
                formMessages.appendChild(messageDiv);
                setTimeout(() => {
                    messageDiv.classList.add('message-hidden');
                    setTimeout(() => messageDiv.remove(), 500);
                }, 5000);
                return;
            }

            if (!emailRegex.test(email)) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message-error';
                messageDiv.textContent = 'Veuillez entrer une adresse email valide.';
                formMessages.appendChild(messageDiv);
                setTimeout(() => {
                    messageDiv.classList.add('message-hidden');
                    setTimeout(() => messageDiv.remove(), 500);
                }, 5000);
                return;
            }

            showLoader();

            const form = event.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('../server/login.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Erreur réseau ou serveur.');
                }

                let result;
                try {
                    result = await response.json();
                } catch (jsonError) {
                    throw new Error('Erreur lors du traitement de la réponse du serveur.');
                }

                if (result.type === 'success') {
                    window.location.href = '../client/consultation.php';
                } else {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message-error';
                    messageDiv.textContent = result.message;
                    formMessages.appendChild(messageDiv);
                    setTimeout(() => {
                        messageDiv.classList.add('message-hidden');
                        setTimeout(() => messageDiv.remove(), 500);
                    }, 5000);
                }
            } catch (error) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message-error';
                messageDiv.textContent = `Erreur lors de la connexion : ${error.message}`;
                formMessages.appendChild(messageDiv);
                setTimeout(() => {
                    messageDiv.classList.add('message-hidden');
                    setTimeout(() => messageDiv.remove(), 500);
                }, 5000);
            } finally {
                hideLoader();
            }
        });

        // Attach input event listeners
        document.getElementById('email').addEventListener('input', (event) => {
            restrictInput(event);
            toggleSubmitButton();
        });
        document.getElementById('password').addEventListener('input', toggleSubmitButton);

        // Initialize on page load
        window.addEventListener('load', () => {
            toggleSubmitButton();
            hideLoader();
            const initialMessages = document.querySelectorAll('.message-error');
            initialMessages.forEach(message => {
                setTimeout(() => {
                    message.classList.add('message-hidden');
                    setTimeout(() => message.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>

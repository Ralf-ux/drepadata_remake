<?php
ob_start(); // Start output buffering to catch any unintended output
session_start(); // Start session at the very top
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Inscription - Drepadata</title>
    <link rel="stylesheet" href="../styles/signup.css" />
    <link rel="icon" href="../assets/logo/logo-removebg-previewe.png" type="image/png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
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
        .loader-spinner {
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
    <div id="loader-overlay" class="loader-overlay">
        <div class="loader-spinner"></div>
    </div>
    <a href="../client/homePage.php" class="return-button" aria-label="Retour à l'accueil" onclick="showLoader()">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div class="signup-container">
        <div class="signup-content">
            <div class="signup-header">
                <a href="../client/homePage.php" class="logo-link" aria-label="Accueil">
                    <img src="../assets/logo/logo-removebg-previewe.png" alt="Drepadata Logo" class="logo" />
                </a>
                <h1>Inscription</h1>
                <p>Rejoignez Drepadata pour contribuer à la recherche sur la drépanocytose.</p>
            </div>
            <div class="signup-form-card">
                <form id="signup-form" class="signup-form" action="../server/signup.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div id="form-messages" aria-live="polite"></div>
                    <div class="form-group">
                        <label for="firstName">Prénom</label>
                        <input type="text" id="firstName" name="firstName" placeholder="Votre prénom" required />
                    </div>
                    <div class="form-group">
                        <label for="lastName">Nom</label>
                        <input type="text" id="lastName" name="lastName" placeholder="Votre nom" required />
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Votre email" required />
                    </div>
                    <div class="form-group">
                        <label for="country">Pays</label>
                        <select id="country" name="country" required>
                            <option value="">Sélectionnez un pays</option>
                            <option value="Cameroon">Cameroun</option>
                            <option value="Central African Republic">République Centrafricaine</option>
                            <option value="Chad">Tchad</option>
                            <option value="Congo (Brazzaville)">Congo (Brazzaville)</option>
                            <option value="Democratic Republic of the Congo">RDC</option>
                            <option value="Equatorial Guinea">Guinée Équatoriale</option>
                            <option value="Gabon">Gabon</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Numéro de téléphone</label>
                        <div class="phone-input-wrapper">
                            <span class="phone-code"></span>
                            <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="Votre numéro" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="Votre mot de passe" required aria-invalid="false" />
                            <button type="button" class="toggle-password" aria-label="Afficher le mot de passe">
                                <i id="toggle-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordHelp" class="password-help">
                            <p>Le mot de passe doit contenir :</p>
                            <ul>
                                <li id="length" class="invalid"><i class="fas fa-circle"></i> 8 caractères</li>
                                <li id="uppercase" class="invalid"><i class="fas fa-circle"></i> Une majuscule</li>
                                <li id="lowercase" class="invalid"><i class="fas fa-circle"></i> Une minuscule</li>
                                <li id="number" class="invalid"><i class="fas fa-circle"></i> Un chiffre</li>
                                <li id="special" class="invalid"><i class="fas fa-circle"></i> Un caractère spécial</li>
                                <li id="confirm" class="invalid"><i class="fas fa-circle"></i> Confirmation identique</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirmer le mot de passe</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmez le mot de passe" required />
                    </div>
                    <button type="submit" class="submit-btn" disabled>Créer un compte</button>
                    <p class="login-link">
                        Déjà membre ? <a href="loginPage.php" onclick="showLoader()">Se connecter</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p>© 2025 Drepadata. Tous droits réservés.</p>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const countryCodes = {
                "Cameroon": "+237",
                "Central African Republic": "+236",
                "Chad": "+235",
                "Congo (Brazzaville)": "+242",
                "Democratic Republic of the Congo": "+243",
                "Equatorial Guinea": "+240",
                "Gabon": "+241"
            };

            const elements = {
                countrySelect: document.getElementById("country"),
                phoneCode: document.querySelector(".phone-code"),
                phoneInput: document.getElementById("phoneNumber"),
                passwordInput: document.getElementById("password"),
                confirmPasswordInput: document.getElementById("confirmPassword"),
                lengthReq: document.getElementById("length"),
                uppercaseReq: document.getElementById("uppercase"),
                lowercaseReq: document.getElementById("lowercase"),
                numberReq: document.getElementById("number"),
                specialReq: document.getElementById("special"),
                confirmReq: document.getElementById("confirm"),
                passwordHelp: document.getElementById("passwordHelp"),
                togglePassword: document.querySelector(".toggle-password"),
                submitBtn: document.querySelector(".submit-btn"),
                form: document.getElementById("signup-form"),
                formMessages: document.getElementById("form-messages"),
                loaderOverlay: document.getElementById("loader-overlay")
            };

            if (Object.values(elements).some(el => !el)) {
                console.error("Missing DOM elements:", Object.keys(elements).filter(k => !elements[k]));
                const errorDiv = document.createElement("div");
                errorDiv.className = "message-error";
                errorDiv.textContent = "Erreur : Certains éléments de la page sont introuvables.";
                elements.formMessages?.appendChild(errorDiv);
                return;
            }

            function updatePhoneCode() {
                const selectedCountry = elements.countrySelect.value;
                const code = countryCodes[selectedCountry] || "";
                elements.phoneCode.textContent = code;
                let currentValue = elements.phoneInput.value.trim();
                let numberPart = currentValue;
                for (const prefix of Object.values(countryCodes)) {
                    if (numberPart.startsWith(prefix)) {
                        numberPart = numberPart.slice(prefix.length).trim();
                        break;
                    }
                }
                numberPart = numberPart.replace(/[^0-9]/g, "");
                elements.phoneInput.value = code ? `${code} ${numberPart}` : numberPart;
            }

            function handlePhoneInput() {
                const selectedCountry = elements.countrySelect.value;
                const code = countryCodes[selectedCountry] || "";
                let currentValue = elements.phoneInput.value.trim();
                if (!code) {
                    elements.phoneInput.value = currentValue.replace(/[^0-9]/g, "");
                    return;
                }
                if (!currentValue.startsWith(code)) {
                    const numberPart = currentValue.replace(/^\+\d+\s*|[^0-9]/g, "");
                    elements.phoneInput.value = `${code} ${numberPart}`;
                } else {
                    const numberPart = currentValue.slice(code.length).trim().replace(/[^0-9]/g, "");
                    elements.phoneInput.value = `${code} ${numberPart}`;
                }
            }

            updatePhoneCode();
            elements.countrySelect.addEventListener("change", updatePhoneCode);
            elements.phoneInput.addEventListener("input", handlePhoneInput);

            function validatePassword() {
                const password = elements.passwordInput.value || "";
                const confirmPassword = elements.confirmPasswordInput.value || "";
                let allValid = true;
                const checks = [
                    { element: elements.lengthReq, condition: password.length >= 8 },
                    { element: elements.uppercaseReq, condition: /[A-Z]/.test(password) },
                    { element: elements.lowercaseReq, condition: /[a-z]/.test(password) },
                    { element: elements.numberReq, condition: /[0-9]/.test(password) },
                    { element: elements.specialReq, condition: /[^A-Za-z0-9]/.test(password) },
                    { element: elements.confirmReq, condition: password && confirmPassword && password === confirmPassword }
                ];
                checks.forEach(({ element, condition }) => {
                    element.classList.toggle("valid", condition);
                    element.classList.toggle("invalid", !condition);
                    if (!condition) allValid = false;
                });
                elements.submitBtn.disabled = !allValid;
                elements.passwordHelp.style.display = allValid ? "none" : "block";
                elements.passwordInput.setAttribute("aria-invalid", !allValid);
            }

            elements.passwordInput.addEventListener("input", validatePassword);
            elements.confirmPasswordInput.addEventListener("input", validatePassword);

            elements.togglePassword.addEventListener("click", () => {
                const isPasswordVisible = elements.passwordInput.type === "password";
                elements.passwordInput.type = isPasswordVisible ? "text" : "password";
                elements.confirmPasswordInput.type = isPasswordVisible ? "text" : "password";
                const toggleIcon = document.getElementById("toggle-icon");
                toggleIcon.classList.toggle("fa-eye", isPasswordVisible);
                toggleIcon.classList.toggle("fa-eye-slash", !isPasswordVisible);
                elements.togglePassword.setAttribute("aria-label", isPasswordVisible ? "Masquer le mot de passe" : "Afficher le mot de passe");
            });

            elements.form.addEventListener("submit", async (e) => {
                e.preventDefault();
                showLoader();
                const phoneValue = elements.phoneInput.value.trim();
                const selectedCountry = elements.countrySelect.value;
                const code = countryCodes[selectedCountry] || "";
                if (code && (phoneValue === code || phoneValue === `${code} `)) {
                    const messageDiv = document.createElement("div");
                    messageDiv.className = "message-error";
                    messageDiv.textContent = "Veuillez entrer un numéro de téléphone valide.";
                    elements.formMessages.innerHTML = "";
                    elements.formMessages.appendChild(messageDiv);
                    setTimeout(() => {
                        messageDiv.className = "message-error message-hidden";
                        setTimeout(() => messageDiv.remove(), 500);
                    }, 5000);
                    hideLoader();
                    return;
                }

                const formData = new FormData(elements.form);
                try {
const response = await fetch("../server/signup.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin"
});
                    if (!response.ok) {
                        throw new Error(`Erreur réseau ou serveur: ${response.status} ${response.statusText}`);
                    }
                    let result;
                    try {
                        result = await response.json();
                    } catch (e) {
                        const rawResponse = await response.text();
                        console.error("Raw response:", rawResponse);
                        throw new Error("Erreur lors du traitement de la réponse du serveur: Réponse non-JSON reçue.");
                    }
                    elements.formMessages.innerHTML = '';
                    const messages = Array.isArray(result.messages) ? result.messages : [{ type: result.type, message: result.message }];
                    messages.forEach(msg => {
                        const messageDiv = document.createElement("div");
                        messageDiv.className = `message-${msg.type}`;
                        messageDiv.textContent = msg.message;
                        elements.formMessages.appendChild(messageDiv);
                        setTimeout(() => {
                            messageDiv.className = `message-${msg.type} message-hidden`;
                            setTimeout(() => messageDiv.remove(), 500);
                        }, 5000);
                    });
if (messages.some(msg => msg.type === "success")) {
    elements.form.reset();
    elements.submitBtn.disabled = true;
    elements.passwordHelp.style.display = 'block';
    ["length", "uppercase", "lowercase", "number", "special", "confirm"].forEach(id => {
        document.getElementById(id).classList.remove("valid");
        document.getElementById(id).classList.add("invalid");
    });
    updatePhoneCode();
    // Delay redirect to consultation page to allow message display
    setTimeout(() => {
        window.location.href = "../client/consultation.php";
    }, 5000); // 5 seconds delay
}
                } catch (error) {
                    console.error("Form submission error:", error);
                    const messageDiv = document.createElement("div");
                    messageDiv.className = "message-error";
                    messageDiv.textContent = `Erreur lors de l'inscription : ${error.message}`;
                    elements.formMessages.innerHTML = "";
                    elements.formMessages.appendChild(messageDiv);
                    setTimeout(() => {
                        messageDiv.className = "message-error message-hidden";
                        setTimeout(() => messageDiv.remove(), 500);
                    }, 5000);
                } finally {
                    hideLoader();
                }
            });

            function showLoader() {
                elements.loaderOverlay.style.display = "flex";
                document.body.classList.add("blur");
                document.body.style.pointerEvents = "none";
            }

            function hideLoader() {
                elements.loaderOverlay.style.display = "none";
                document.body.classList.remove("blur");
                document.body.style.pointerEvents = "auto";
            }

            document.querySelectorAll("a").forEach(link => {
                link.addEventListener("click", showLoader);
            });

            window.addEventListener("load", () => {
                hideLoader();
                updatePhoneCode();
            });

            function restrictInput(event) {
                const value = event.target.value;
                const regex = /[^\p{L}\p{M}\s.,'-]/gu;
                if (value && regex.test(value)) {
                    event.target.value = value.replace(regex, "");
                }
            }

            ["firstName", "lastName"].forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener("input", restrictInput);
                }
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); // Flush output buffer ?>

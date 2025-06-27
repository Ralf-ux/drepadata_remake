<?php
// Start output buffering
ob_start();

error_log("signup.php executed - debug log at start of file");


// Start session
session_start();

// Include PHPMailer classes at top-level
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting (disable in production)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Global try-catch for all errors
try {
    // Include DB connection
    $conn_path = 'conn.php'; // Relative to C:\xampp\htdocs\Drepa data remake\server\
    if (!file_exists($conn_path)) {
        throw new Exception("Database connection file not found: $conn_path");
    }
    require_once $conn_path;
    if (!isset($pdo)) {
        throw new Exception('PDO connection not established in conn.php');
    }

    // Include PHPMailer autoloader
    $autoload_path = '../vendor/autoload.php'; // Relative to C:\xampp\htdocs\Drepa data remake\
    if (!file_exists($autoload_path)) {
        throw new Exception("Autoload file not found: $autoload_path");
    }
    require_once $autoload_path;

    // Validate password strength
    function isPasswordStrong($password)
    {
        return strlen($password) >= 8 &&
            preg_match('@[A-Z]@', $password) &&
            preg_match('@[a-z]@', $password) &&
            preg_match('@[0-9]@', $password) &&
            preg_match('@[^\w]@', $password);
    }

    // Send verification email
    function sendVerificationEmail($pdo, $userId, $email, $firstName)
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        try {
            $stmt = $pdo->prepare("INSERT INTO user_verifications (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $token, $expires]);
        } catch (PDOException $e) {
            error_log("Token insertion error: " . $e->getMessage());
            return ['type' => 'error', 'message' => 'Erreur lors de l\'enregistrement du jeton de vérification.'];
        }

        $mail = new PHPMailer(true);
        try {
            // Mailtrap SMTP settings - Replace with actual credentials
            $mail->isSMTP();
            $mail->Host = 'live.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-mailtrap-username'; // TODO: Replace with your Mailtrap SMTP username
            $mail->Password = 'your-mailtrap-password'; // TODO: Replace with your Mailtrap SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Debugging (set to 2 for testing, 0 in production)
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = function ($str, $level) {
                error_log("PHPMailer: $str");
            };

            // Sender and recipient
            $mail->setFrom('noreply@drepadata.com', 'Drepadata');
            $mail->addAddress($email, $firstName);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Vérifiez votre compte Drepadata';
            $verificationLink = "http://localhost/Drepa%20data%20remake/auth/verify.php?token=$token";
            $mail->Body = "
                <h2>Bienvenue chez Drepadata, $firstName !</h2>
                <p>Merci de vous être inscrit. Veuillez vérifier votre adresse email en cliquant sur le lien ci-dessous :</p>
                <p><a href='$verificationLink' style='color: #d32f2f; font-weight: bold;'>Vérifier mon compte</a></p>
                <p>Ce lien expire dans 1 heure.</p>
                <p>Si vous n'avez pas créé de compte, ignorez cet email.</p>
                <p>L'équipe Drepadata</p>
            ";
            $mail->AltBody = "Bienvenue chez Drepadata, $firstName ! Veuillez vérifier votre compte : $verificationLink. Expire dans 1 heure.";

            $mail->send();
            return ['type' => 'success', 'message' => 'Compte créé ! Veuillez vérifier votre email pour activer votre compte.'];
        } catch (Exception $e) {
            error_log("Email sending error: " . $mail->ErrorInfo);
            return ['type' => 'error', 'message' => 'Erreur lors de l\'envoi de l\'email de vérification : ' . $mail->ErrorInfo];
        }
    }

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        ob_end_clean();
        echo json_encode(['type' => 'error', 'message' => 'Méthode de requête invalide.']);
        exit;
    }

/* Temporarily disable CSRF token validation for testing */
#if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
#    error_log("CSRF token invalid or missing. Received: " . ($_POST['csrf_token'] ?? 'none') . ", Expected: " . ($_SESSION['csrf_token'] ?? 'none'));
#    ob_end_clean();
#    echo json_encode(['type' => 'error', 'message' => 'Jeton CSRF invalide.']);
#    exit;
#}

    $lastName = trim($_POST['lastName'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $phoneNumber = trim($_POST['phoneNumber'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $responseMessages = [];

    // Validation
    if (empty($lastName) || empty($firstName) || empty($email) || empty($country) || empty($phoneNumber) || empty($password) || empty($confirmPassword)) {
        $responseMessages[] = ['type' => 'error', 'message' => 'Veuillez remplir tous les champs obligatoires.'];
    } /* Remove email format validation to allow simple signup */
    // elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //     $responseMessages[] = ['type' => 'error', 'message' => 'Format d\'email invalide.'];
    // } 
    elseif ($password !== $confirmPassword) {
        $responseMessages[] = ['type' => 'error', 'message' => 'Les mots de passe ne correspondent pas.'];
    } elseif (!isPasswordStrong($password)) {
        $responseMessages[] = ['type' => 'error', 'message' => 'Le mot de passe doit contenir au moins 8 caractères, incluant majuscule, minuscule, chiffre et caractère spécial.'];
    } elseif (!preg_match('/^\+\d{1,4}\s?\d{6,}$/', $phoneNumber)) {
        $responseMessages[] = ['type' => 'error', 'message' => 'Numéro de téléphone invalide. Utilisez le format : +code numéro (ex. +237 123456789).'];
    } else {
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Check email existence
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $responseMessages[] = ['type' => 'error', 'message' => 'Cet email est déjà enregistré.'];
            } else {
                // Insert user
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $fullPhoneNumber = preg_replace('/\s+/', '', $phoneNumber); // Remove spaces
$sql = "INSERT INTO users (surename, name, email, country, phone, password, verified) VALUES (?, ?, ?, ?, ?, ?, 1)";
                $stmt = $pdo->prepare($sql);
                $params = [$lastName, $firstName, $email, $country, $fullPhoneNumber, $passwordHash];
                error_log("Executing query: $sql with params: " . json_encode($params));
                if (!$stmt->execute($params)) {
                    throw new PDOException("Failed to insert user into database.");
                }

                // Get user ID
                $userId = $pdo->lastInsertId();

$responseMessages[] = ['type' => 'success', 'message' => 'Compte créé avec succès.'];

                // Set session user_name for welcome message on consultation page
                $_SESSION['user_name'] = $firstName;
                // Set session user_id to mark user as logged in
                $_SESSION['user_id'] = $userId;

                // Commit transaction
                $pdo->commit();
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Database error: " . $e->getMessage());
            $responseMessages[] = ['type' => 'error', 'message' => 'Erreur de base de données : ' . $e->getMessage()];
        }
    }

    // Consolidate messages
    $type = 'error';
    $messageText = '';
    foreach ($responseMessages as $msg) {
        if ($msg['type'] === 'error') {
            $type = 'error';
            $messageText = $msg['message'];
            break;
        } else {
            $type = $msg['type'];
            $messageText = $msg['message'];
        }
    }

    ob_end_clean();
    echo json_encode(['type' => $type, 'message' => $messageText]);
    exit;
} catch (Exception $e) {
    // Catch all unhandled errors
    error_log("Fatal error in signup.php: " . $e->getMessage());
    ob_end_clean();
    header('Content-Type: application/json', true, 500);
    echo json_encode(['type' => 'error', 'message' => 'Erreur serveur : ' . $e->getMessage()]);
    exit;
}

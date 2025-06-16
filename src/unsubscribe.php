<?php
session_start();
require_once 'functions.php';

$message = '';

// Autofill session from URL if present
if (isset($_GET['email'])) {
    $_SESSION['unsubscribe_email'] = $_GET['email'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'send_code' && isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        $_SESSION['unsubscribe_email'] = $email;
        if (in_array($email, file(__DIR__ . '/registered_emails.txt', FILE_IGNORE_NEW_LINES))) {
            $code = generateVerificationCode();
            saveVerificationCode($email, $code);
            sendVerificationEmail($email, $code, 'unsubscribe');
            $message = "Verification code sent to $email.";
        } else {
            $message = "Email is not subscribed.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'verify_code' && isset($_POST['verification_code'])) {
        $email = $_SESSION['unsubscribe_email'] ?? '';
        $code = trim($_POST['verification_code']);
        if ($email && verifyCode($email, $code)) {
            if (unsubscribeEmail($email)) {
                $message = "Email unsubscribed successfully.";
                unset($_SESSION['unsubscribe_email']);
            } else {
                $message = "Email not found or already unsubscribed.";
            }
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
    <style>
        body {
        font-family: 'Comic Sans MS', cursive;
            background: #222;
            color: #fff;
            text-align: center;
            padding: 40px;
        }
        form {
            background: #333;
            padding: 20px;
            margin: 20px auto;
            width: 300px;
            border-radius: 8px;
        }
    input[type=text], input[type=email]
    {
        background-color: rgba(131, 130, 130, 0.6);
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
        color: white;
    }

    .button-group 
    {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 10px;
        
    }

    .button-group button 
    {
    font-family: 'Comic Sans MS', cursive;
    background-color: rgba(131, 130, 130, 0.6);
    color:white;
    }
    </style>
</head>
<body>
    <h2>Unsubscribe from XKCD Comics :(</h2>

    <form method="POST">
        <input type="email" name="unsubscribe_email" value="<?php echo htmlspecialchars($_SESSION['unsubscribe_email'] ?? ''); ?>" required placeholder="Your email">
        <input type="hidden" name="action" value="send_code">
        <div class="button-group">
            <button type="submit" id="submit-unsubscribe">Unsubscribe</button>
        </div>
        
    </form>

    <form method="POST">
        <input type="text" name="verification_code" maxlength="6" required placeholder="Verification code">
        <input type="hidden" name="action" value="verify_code">
        <div class="button-group">
            <button type="submit" id="submit-verification">Verify</button>
        </div>
    </form>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</body>
</html>
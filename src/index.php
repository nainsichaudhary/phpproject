<?php
require_once 'functions.php';

$message = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $code = trim($_POST['verification_code'] ?? '');

    if ($email && !$code) {
        $code = generateVerificationCode();
        saveVerificationCode($email, $code);
        sendVerificationEmail($email, $code);
        $message = "Verification code sent to $email.";
    } elseif ($email && $code) {
        if (verifyCode($email, $code)) {
            registerEmail($email);
            $message = "Email verified and registered successfully.";
            $email = '';
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>XKCD Email Registration</title>
    <style>
    body {
        background-image: url('background.png'); 
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: #000;
       
    }

    form {
        text-align:center; 
        font-family: 'Comic Sans MS', cursive;
        background-color: rgba(80, 80, 80, 0.6);
        padding: 20px;
        max-width: 400px;
        margin: 50px auto;
        border-radius: 10px;
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
    <form method="POST">
       <h2 style="color: white; text-align: center;">Email registration for daily comic</h2>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? '') ?>" required placeholder="Enter your email">
        <input type="text" name="verification_code" maxlength="6" placeholder="Enter verification code if received">
        <div class="button-group">
            <button type="submit" id="submit-email">Submit & Verify</button>
        </div>
    <?php if ($message): ?>
        <p style=" color: white;"><?php echo $message; ?></p>
    <?php endif; ?>
    </form>

    
</body>
</html>
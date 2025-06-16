<?php

// Load .env file if it exists
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}


function generateVerificationCode(): string {
    // TODO: Implement this function
    return strval(rand(100000, 999999));
}

/**
 * Send a verification code to an email.
 */
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($email, $code, $type = 'subscribe') {
    $subject = $type === 'unsubscribe' ? 'Confirm Un-subscription' : 'Your Verification Code';
    $message = $type === 'unsubscribe'
        ? "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>"
        : "<p>Your verification code is: <strong>$code</strong></p>";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_EMAIL'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($_ENV['SMTP_EMAIL'], 'XKCD Bot');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}


function registerEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return false;
  }

  $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

  if (!in_array($email, $emails)) {
      return file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
  }

  return true;
}


function unsubscribeEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
  if (!file_exists($file)) return false;

  $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  $filtered = array_filter($emails, fn($e) => trim($e) !== trim($email));

  return file_put_contents($file, implode(PHP_EOL, $filtered) . PHP_EOL) !== false;
}


function fetchAndFormatXKCDData(string $email): string {
    $latestData = json_decode(file_get_contents('https://xkcd.com/info.0.json'), true);
    $latestId = $latestData['num'];
    $randomId = rand(1, $latestId);
    $comicData = json_decode(file_get_contents("https://xkcd.com/$randomId/info.0.json"), true);
    $imgUrl = $comicData['img'];

    return "<h2>XKCD Comic</h2>
            <img src=\"$imgUrl\" alt=\"XKCD Comic\">
            <p><a href=\"http://localhost:8000/unsubscribe.php?email=" . urlencode($email) . "\" id=\"unsubscribe-button\">Unsubscribe</a></p>";}
function saveVerificationCode(string $email, string $code): void {
    $codeFile = __DIR__ . '/tmp/codes.json';
    $codes = file_exists($codeFile) ? json_decode(file_get_contents($codeFile), true) : [];
    $codes[$email] = $code;
    file_put_contents($codeFile, json_encode($codes));
}

function verifyCode(string $email, string $code): bool {
    $codeFile = __DIR__ . '/tmp/codes.json';
    if (!file_exists($codeFile)) return false;

    $codes = json_decode(file_get_contents($codeFile), true);
    if (!isset($codes[$email])) return false;

    return $codes[$email] === $code;
}


function sendXKCDUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($emails as $email) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_EMAIL'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom($_ENV['SMTP_EMAIL'], 'XKCD Bot');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your XKCD Comic';
            $htmlContent = fetchAndFormatXKCDData($email);
            $mail->Body    = $htmlContent;

            if ($mail->send()) {
                file_put_contents(__DIR__ . '/tmp/xkcd_mail_log.txt', "[" . date('Y-m-d H:i:s') . "] Sent to $email\n", FILE_APPEND);
            } else {
                file_put_contents(__DIR__ . '/tmp/xkcd_mail_log.txt', "[" . date('Y-m-d H:i:s') . "] FAILED to $email: " . $mail->ErrorInfo . "\n", FILE_APPEND);
            }
        } catch (Exception $e) {
            error_log("XKCD Mail Error to $email: " . $mail->ErrorInfo);
        }
    }
}

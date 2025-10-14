<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = isset($_POST["name"]) ? strip_tags(trim($_POST["name"])) : "";
    $contact = isset($_POST["contact"]) ? strip_tags(trim($_POST["contact"])) : "";

    if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
        $replyToEmail = $contact;
    } else {
        $replyToEmail = null;
    }

    $message = isset($_POST["message"]) ? strip_tags(trim($_POST["message"])) : "";

    if (empty($name) || empty($contact) || empty($message)) {
        echo json_encode([
            "success" => false,
            "message" => "Будь ласка, заповніть усі обов’язкові поля."
        ]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'nichogo-osobistogo.com.ua';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'alexey.kuliw@nichogo-osobistogo.com.ua';
        $mail->Password   = 'Mamanegorui2012';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;


        $mail->setFrom('alexey.kuliw@nichogo-osobistogo.com.ua', 'Nichogo Osobistogo LGBT');

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';


        if ($replyToEmail) {
            $mail->addReplyTo($replyToEmail, $name);
        }


        // Кому
        $mail->addAddress('alexey.kuliw@gmail.com');
        // $mail->addAddress('alexey.kuliw@ecomitize.com');


        // Контент письма
        $mail->isHTML(false);
        $mail->Subject = "Нова заявка з сайту LGBT";
        $mail->Body = "Ім’я: $name\nКонтакт: $contact\n\nПовідомлення:\n$message";

        $mail->send();

        echo json_encode([
            "success" => true,
            "message" => "Ваша заявка успішно надіслана!"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Сталася помилка під час відправки: {$mail->ErrorInfo}"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Некоректний метод відправки форми."
    ]);
}

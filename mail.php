<?php

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $json = array();

    // Get and sanitize fields
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r","\n"), array(" "," "), $name);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = trim($_POST["message"]);

    // Validate inputs
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $json['status'] = 400;
        $json['message'] = "Validation error — please try again!";
        echo json_encode($json);
        exit;
    }

    // Recipient email
    $recipient = "gustavoroo@gmail.com";

    // Email subject
    $subject = "New Portfolio Contact from $name";

    // Email content
    $email_content  = "Name: $name\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Message:\n$message\n";

    // Email headers (more reliable)
    $email_headers  = "From: Portfolio Contact <no-reply@yourdomain.com>\r\n";
    $email_headers .= "Reply-To: $email\r\n";
    $email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Try sending
    if (mail($recipient, $subject, $email_content, $email_headers)) {
        $json['status'] = 200;
        $json['message'] = "Thank you! Your message has been sent.";
    } else {
        $json['status'] = 500;
        $json['message'] = "Oops! Something went wrong and we couldn't send your message.";
        error_log("Mail failed for $email — message: $message");
    }

} else {
    $json['status'] = 403;
    $json['message'] = "Invalid request method.";
}

echo json_encode($json);

?>
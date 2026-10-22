<?php
// send-mail.php - Processes form and sends email directly from your server

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form fields and sanitize input
    $name = strip_tags(trim($_POST["Name"]));
    $name = str_replace(array("\r","\n"),array(" "," "),$name);
    $email = filter_var(trim($_POST["Email"]), FILTER_SANITIZE_EMAIL);
    
    // The target department selected from the dropdown
    $recipient = filter_var(trim($_POST["Department"]), FILTER_SANITIZE_EMAIL);
    
    // Fallback recipient if none selected
    $allowed_recipients = [
        "info@jirobeacons.com",
        "projects@jirobeacons.com",
        "careers@jirobeacons.com"
    ];
    
    if (!in_array($recipient, $allowed_recipients)) {
        $recipient = "info@jirobeacons.com";
    }

    $message_content = trim($_POST["Message"]);

    // Validate data
    if (empty($name) || empty($message_content) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please complete the form and provide a valid email address.";
        exit;
    }

    // Email Subject & Body
    $subject = "New Website Inquiry from " . $name;
    
    $email_body = "You have received a new message from your website contact form.\n\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n\n";
    $email_body .= "Message:\n$message_content\n";

    // Email Headers (so replies go directly to the sender)
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send the email using the server's mail system
    if (mail($recipient, $subject, $email_body, $headers)) {
        http_response_code(200);
        echo "Thank you! Your message has been sent successfully.";
    } else {
        http_response_code(500);
        echo "Oops! Something went wrong and we couldn't send your message.";
    }

} else {
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}
?>

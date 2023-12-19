<?php
require 'vendor/autoload.php'; // Include the AWS SDK for PHP

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

// Function to send an email via SES
function sendEmailViaSES($recipientEmail, $senderEmail, $awsConfig) {
    $ses = new SesClient($awsConfig);

    // Specify the email subject and body
    $subject = 'Subject of the Email';
    $body = 'Hello ' . $recipientEmail . ', this is the body of the email.';

    try {
        $result = $ses->sendEmail([
            'Destination' => [
                'ToAddresses' => [$recipientEmail],
            ],
            'Message' => [
                'Body' => [
                    'Text' => [
                        'Charset' => 'UTF-8',
                        'Data' => $body,
                    ],
                ],
                'Subject' => [
                    'Charset' => 'UTF-8',
                    'Data' => $subject,
                ],
            ],
            'Source' => $senderEmail,
        ]);

        echo "Email sent via SES successfully!";
    } catch (AwsException $e) {
        echo "Error sending email via SES: " . $e->getMessage();
    }
}

// Specify your AWS SES credentials and region
$awsConfig = [
    'version' => 'latest',
    'region'  => 'your-aws-region',
    'credentials' => [
        'key'    => 'your-aws-access-key',
        'secret' => 'your-aws-secret-key',
    ],
];

// Specify your RDS connection details
$servername = "your-database-instance-name.some-region.rds.amazonaws.com";
$username = "your-master-username";
$password = "your-master-password";
$dbname = "your-initial-database-name";

// Create connection to RDS
$conn = new mysqli($servername, $username, $password, $dbname);

// Check RDS connection
if ($conn->connect_error) {
    die("RDS Connection failed: " . $conn->connect_error);
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $primeiroNome = $_POST["primeiro_nome"];
    $segundoNome = $_POST["segundo_nome"];
    $email = $_POST["email"];

    // Insert data into the database
    $sql = "INSERT INTO pessoa (nome, sobrenome, email) VALUES ('$primeiroNome', '$segundoNome', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo "Form data stored in RDS successfully!";
        
        // Send an email via SES
        sendEmailViaSES($email, 'your-verified-sender@example.com', $awsConfig);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close RDS connection
$conn->close();
?>

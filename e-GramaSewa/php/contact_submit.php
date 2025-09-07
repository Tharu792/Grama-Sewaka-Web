<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grama_sewa";

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Get form data and sanitize
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);
        
        // Validation
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        if (empty($subject)) {
            $errors[] = "Subject is required";
        }
        
        if (empty($message)) {
            $errors[] = "Message is required";
        }
        
        // If no errors, insert into database
        if (empty($errors)) {
            
            // Create contact table if it doesn't exist
            $createTableSQL = "
                CREATE TABLE IF NOT EXISTS contact_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    phone VARCHAR(20),
                    subject VARCHAR(50) NOT NULL,
                    message TEXT NOT NULL,
                    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            
            $pdo->exec($createTableSQL);
            
            // Insert contact message
            $insertSQL = "
                INSERT INTO contact_messages (name, email, phone, subject, message, status) 
                VALUES (:name, :email, :phone, :subject, :message, 'new')
            ";
            
            $stmt = $pdo->prepare($insertSQL);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            
            if ($stmt->execute()) {
                // Success - redirect back to contact page with success message
                $success_message = "Thank you for your message! We have received your inquiry and will get back to you within 24 hours.";
                header("Location: ../pages/contact.html?success=" . urlencode($success_message));
                exit();
            } else {
                $error_message = "Sorry, there was an error sending your message. Please try again.";
                header("Location: ../pages/contact.html?error=" . urlencode($error_message));
                exit();
            }
            
        } else {
            // Validation errors - redirect back with error message
            $error_message = implode(", ", $errors);
            header("Location: ../pages/contact.html?error=" . urlencode($error_message));
            exit();
        }
        
    } else {
        // Not a POST request - redirect to contact page
        header("Location: ../pages/contact.html");
        exit();
    }
    
} catch (PDOException $e) {
    // Database error
    $error_message = "Database connection failed. Please try again later.";
    header("Location: ../pages/contact.html?error=" . urlencode($error_message));
    exit();
} catch (Exception $e) {
    // General error
    $error_message = "An unexpected error occurred. Please try again.";
    header("Location: ../pages/contact.html?error=" . urlencode($error_message));
    exit();
}
?>

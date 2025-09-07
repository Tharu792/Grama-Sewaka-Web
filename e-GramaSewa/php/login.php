<?php
// Start the session
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grama_sewa";

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify reCAPTCHA first
    $recaptcha_secret = "6LccU8ArAAAAAEixUncND_WYx3KJKrG3fCkeAXar";
    $recaptcha_response = $_POST['g-recaptcha-response'];
    
    // Verify reCAPTCHA
    $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
    $recaptcha_data = array(
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    );
    
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        )
    );
    
    $context = stream_context_create($options);
    $result = file_get_contents($recaptcha_url, false, $context);
    $recaptcha_result = json_decode($result, true);
    
    // Check if reCAPTCHA verification failed
    if (!$recaptcha_result['success']) {
        $error_message = "Please complete the reCAPTCHA verification.";
    } else {
        // Collect form data
        $input_username = $_POST['username'];
        $input_password = $_POST['password'];

    // Query the customers table for the username
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE username = :username");
    $stmt->bindParam(":username", $input_username);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // Query the admins table for the username
    $stmt2 = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
    $stmt2->bindParam(":username", $input_username);
    $stmt2->execute();
    $admin = $stmt2->fetch(PDO::FETCH_ASSOC);

    // Query the grama_sewaka table for the username
    $stmt3 = $pdo->prepare("SELECT * FROM grama_sewaka WHERE username = :username");
    $stmt3->bindParam(":username", $input_username);
    $stmt3->execute();
    $grama_sewaka = $stmt3->fetch(PDO::FETCH_ASSOC);

    // Check if the username exists in the customers table and verify password using password_verify (for hashed passwords)
    if ($customer && password_verify($input_password, $customer['password'])) {
        // Store customer information in session and redirect to customer dashboard
        $_SESSION['username'] = $input_username;
        header("Location: customer_dashboard.php"); // Replace with your customer dashboard page
        exit();
    } 
    // Check if the username exists in the admins table and verify password directly (without hashing)
    elseif ($admin && $input_password === $admin['password']) {
        // Store admin information in session and redirect to admin dashboard
        $_SESSION['username'] = $input_username;
        header("Location: admin_dashboard.php"); // Replace with your admin dashboard page
        exit();
    }
    // Check if the username exists in the grama_sewaka table and verify password (with hashed password check)
    elseif ($grama_sewaka && password_verify($input_password, $grama_sewaka['password'])) {
        // Store grama_sewaka information in session and redirect to grama_sewaka dashboard
        $_SESSION['username'] = $input_username;
        header("Location: grama_sewaka_dashboard.php"); // Replace with your Grama Sewaka dashboard page
        exit();
    } else {
        // If login fails, show an error message
        $error_message = "Invalid username or password.";
    }
    } // Close the reCAPTCHA verification else block
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grama Sewaka Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
      <style>
        /* Track App Style for Login Form - No Animations */
        :root {
            --primary: rgba(37, 99, 235, 0.9);
            --secondary: rgba(16, 185, 129, 0.9);
            --card: rgba(255, 255, 255, 0.95);
            --text: #1e293b;
            --text-light: #64748b;
        }

        /* Body Background */
        body {
            background-image: 
                
                url('../images/image1.jpg');
            background-size: cover;
            color: white;
            line-height: 1.6;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Login Container */
        .login-container {
            width: 100%;
            max-width: 450px;
            margin: 80px auto;
            background: var(--card);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 10;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.05), transparent);
            transition: left 0.6s ease;
        }

        .login-container:hover::before {
            left: 100%;
        }

        /* Login Title */
        .login-container h2 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: var(--text);
            text-align: center;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .login-container h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            border-radius: 2px;
        }

        /* Input Groups */
        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--text);
            font-size: 1rem;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid rgba(16, 185, 129, 0.2);
            border-radius: 12px;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            color: var(--text);
            position: relative;
        }

        .input-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
            outline: none;
            transform: translateY(-2px);
        }

        .input-group input:hover {
            border-color: var(--secondary);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
        }

        .input-group input::placeholder {
            color: var(--text-light);
        }

        /* Submit Button */
        button[type="submit"] {
            width: 100%;
            padding: 18px 25px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 18px;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        button[type="submit"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        button[type="submit"]:hover::before {
            left: 100%;
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(16, 185, 129, 0.4);
        }

        /* reCAPTCHA Section */
        .g-recaptcha {
            margin: 25px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .g-recaptcha > div {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Error Message Styling */
        .error-message {
            background: var(--card);
            color: #dc2626;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
            border: 2px solid rgba(220, 38, 38, 0.3);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                margin: 40px 20px;
                padding: 30px;
                max-width: none;
            }

            .login-container h2 {
                font-size: 2rem;
            }

            .login-container h2::after {
                width: 60px;
            }

            .input-group input {
                padding: 12px 15px;
                font-size: 14px;
            }

            button[type="submit"] {
                padding: 15px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px 15px;
                padding: 25px;
            }

            .login-container h2 {
                font-size: 1.8rem;
            }

            .input-group input {
                padding: 12px 15px;
                font-size: 14px;
            }

            button[type="submit"] {
                padding: 12px;
                font-size: 16px;
            }
        }
      </style>
</head>
<body>
      <header>
       
        <nav class="middle-nav">
            <ul>
                <li><a href="../pages/Home.html">Home</a></li>
                <li><a href="../pages/About.html">About</a></li>
                <li><a href="../pages/Service.html">Services</a></li>
                <li><a href="../pages/contact.html">Contact</a></li>
            </ul>
        </nav>
        <div class="right-nav">
            <a href="login.php" class="login-btn">Login</a>
            <a href="register.php" class="register-btn">Register</a>
        </div>
    </header>

    <div class="login-container">
        <h2>Login</h2>
        <?php
        // Display error message if any
        if (isset($error_message)) {
            echo "<p style='color:red;'>$error_message</p>";
        }
        ?>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <!-- Google reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6LccU8ArAAAAABHJcoOZmr1poK8IZlPSnDHDFhkr"></div>
            
            <button type="submit">Login</button>
        </form>
    </div>
    
</body>
</html>

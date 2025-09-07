<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grama_sewa";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Ensure that the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login_form.html');
    exit();
}

// Get the logged-in customer's username
$customer_username = $_SESSION['username'];

// Fetch customer data from the database
$stmt = $pdo->prepare("SELECT * FROM customers WHERE username = :username");
$stmt->bindParam(":username", $customer_username);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission for profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated data from the form
    $name = $_POST['name'];
    $address = $_POST['address'];
    $tel_no = $_POST['tel_no'];
    $email = $_POST['email'];
    $district = $_POST['district'];
    $new_username = $_POST['username'];  // New username
    $new_password = $_POST['password'];  // New password

    // If password is provided, hash it
    if (!empty($new_password)) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
    } else {
        // If no new password is provided, keep the old one
        $new_password = $customer['password'];
    }

    // Prepare and execute the update query
    $updateStmt = $pdo->prepare("UPDATE customers SET name = :name, address = :address, tel_no = :tel_no, email = :email, district = :district, username = :new_username, password = :new_password WHERE username = :username");
    $updateStmt->bindParam(':name', $name);
    $updateStmt->bindParam(':address', $address);
    $updateStmt->bindParam(':tel_no', $tel_no);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':district', $district);
    $updateStmt->bindParam(':new_username', $new_username);
    $updateStmt->bindParam(':new_password', $new_password);
    $updateStmt->bindParam(':username', $customer_username);

    if ($updateStmt->execute()) {
        // Update the session with the new username if it was changed
        if ($new_username !== $customer_username) {
            $_SESSION['username'] = $new_username;
        }
        $message = "Profile updated successfully!";
    } else {
        $message = "There was an error updating your profile.";
    }

    // Fetch the updated customer data
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Your Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* ================== Beautiful User-Friendly Profile Interface ================== */
        
        /* CSS Variables for White/Blue/Green Grama Sewaka Theme */
        :root {
            --profile-primary: #3b82f6;
            --profile-secondary: #10b981;
            --profile-accent: #059669;
            --profile-success: #22c55e;
            --profile-warning: #f59e0b;
            --profile-error: #ef4444;
            --profile-info: #06b6d4;
            --profile-light: #f8fafc;
            --profile-dark: #1e293b;
            --profile-gray: #64748b;
            --profile-white: #ffffff;
            --profile-shadow: rgba(0, 0, 0, 0.1);
            --profile-shadow-lg: rgba(0, 0, 0, 0.15);
            --soft-blue: #3b82f6;
            --fresh-green: #10b981;
            --gray-800: #1f2937;
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Enhanced Body Background with Grama Sewaka Image */
        body {
           background-image: 
                
                url('../images/image1.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            padding: 2rem 0;
        }

        

        /* Enhanced Profile Container */
        .profile-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(59, 130, 246, 0.15);
            position: relative;
            overflow: hidden;
            z-index: 10;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .profile-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                var(--soft-blue), 
                var(--fresh-green), 
                var(--soft-blue));
            background-size: 200% 100%;
            animation: profileGradientShift 3s ease infinite;
        }

        .profile-container:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 35px 70px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.3);
            border-color: var(--soft-blue);
        }

        /* Enhanced Profile Title */
        .profile-container h2 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, 
                var(--soft-blue) 0%, 
                var(--fresh-green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 2.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            animation: profileTitleGlow 3s ease-in-out infinite alternate;
        }

        .profile-container h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, var(--soft-blue), var(--fresh-green));
            border-radius: 2px;
        }

        /* Enhanced Message Styling */
        .message {
            text-align: center;
            font-size: 1.3rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, 
                rgba(34, 197, 94, 0.1) 0%, 
                rgba(16, 185, 129, 0.1) 100%);
            border-radius: 15px;
            border-left: 4px solid var(--profile-success);
            color: var(--profile-success);
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.1);
            animation: profileFadeIn 0.6s ease-out;
        }

        /* Enhanced Form Styling */
        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        /* Enhanced Form Labels */
        .form-group label {
            display: block;
            margin-bottom: 0.8rem;
            color: var(--profile-dark);
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        .form-group label::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, var(--soft-blue), var(--fresh-green));
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        /* Enhanced Form Inputs */
        .form-group input {
            width: 100%;
            padding: 1.2rem 1.5rem;
            font-size: 1.1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            color: var(--profile-dark);
            transition: all 0.3s ease;
            box-sizing: border-box;
            font-weight: 500;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--soft-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 1);
        }

        .form-group input:hover {
            border-color: #94a3b8;
            transform: translateY(-1px);
        }

        /* Enhanced Submit Button */
        .form-group button {
            width: 100%;
            padding: 1.2rem 2rem;
            font-size: 1.3rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(135deg, 
                var(--soft-blue) 0%, 
                var(--fresh-green) 100%);
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
            margin-top: 1rem;
        }

        .form-group button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .form-group button:hover::before {
            left: 100%;
        }

        .form-group button:hover {
            background: linear-gradient(135deg, 
                var(--fresh-green) 0%, 
                var(--soft-blue) 100%);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }

        .form-group button:active {
            transform: translateY(-1px) scale(1.01);
        }

        /* Enhanced Animations */
        @keyframes profileGradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes profileBackgroundMove {
            0% { transform: translateX(0) translateY(0); }
            25% { transform: translateX(-5%) translateY(-5%); }
            50% { transform: translateX(5%) translateY(-10%); }
            75% { transform: translateX(-3%) translateY(5%); }
            100% { transform: translateX(0) translateY(0); }
        }

        @keyframes profileFloat {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-100px) rotate(360deg); }
        }

        @keyframes profileTitleGlow {
            0% { text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
            100% { text-shadow: 0 4px 20px rgba(59, 130, 246, 0.3); }
        }

        @keyframes profileFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced Responsive Design */
        @media (max-width: 768px) {
            .profile-container {
                margin: 1rem;
                padding: 2rem;
                max-width: none;
            }

            .profile-container h2 {
                font-size: 2rem;
            }

            .form-group input {
                padding: 1rem 1.2rem;
                font-size: 1rem;
            }

            .form-group button {
                padding: 1rem 1.5rem;
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem 0;
            }

            .profile-container {
                margin: 0.5rem;
                padding: 1.5rem;
            }

            .profile-container h2 {
                font-size: 1.8rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-group label {
                font-size: 1rem;
            }

            .form-group input {
                padding: 0.8rem 1rem;
            }

            .form-group button {
                padding: 0.8rem 1.2rem;
                font-size: 1rem;
            }
        }

        /* Enhanced Focus States for Accessibility */
        .form-group input:focus,
        .form-group button:focus {
            outline: 3px solid rgba(59, 130, 246, 0.3);
            outline-offset: 2px;
        }

        /* Smooth Transitions for All Interactive Elements */
        .profile-container,
        .form-group input,
        .form-group button {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Form Animation on Load */
        .form-group {
            animation: profileFadeIn 0.6s ease-out forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }
        .form-group:nth-child(7) { animation-delay: 0.7s; }
        .form-group:nth-child(8) { animation-delay: 0.8s; }

        /* Back Button Container */
.back-btn {
    text-align: center; /* center align */
    margin-top: 20px;
}

/* Back Button Styling */
.back-btn button {
    background: #4CAF50; /* green */
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

/* Hover Effect */
.back-btn button:hover {
    background: #45a049; /* darker green */
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
}

/* Active Click Effect */
.back-btn button:active {
    transform: translateY(1px);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
}
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Update Your Profile</h2>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($customer['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>" required>
            </div>
            <div class="form-group">
                <label for="tel_no">Phone Number</label>
                <input type="text" id="tel_no" name="tel_no" value="<?php echo htmlspecialchars($customer['tel_no']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="district">District</label>
                <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($customer['district']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password (Leave empty if not changing)</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <button type="submit">Update Profile</button>
                
            </div>
            
        </form>

        <div class="back-btn">
    <form action="customer_dashboard.php" method="POST">
        <button type="submit">Back to Dashboard</button>
    </form>
</div>
    </div>
</body>
</html>

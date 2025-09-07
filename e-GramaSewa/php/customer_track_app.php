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

// Fetch all applications' statuses from the various certificates
$stmt_residence = $pdo->prepare("SELECT status, created_at, rejection_comment FROM residence_certificates WHERE username = :username ORDER BY created_at DESC");
$stmt_residence->bindParam(":username", $customer_username);
$stmt_residence->execute();
$residence_statuses = $stmt_residence->fetchAll(PDO::FETCH_ASSOC);

$stmt_character = $pdo->prepare("SELECT status, created_at, rejection_comment FROM character_certificates WHERE username = :username ORDER BY created_at DESC");
$stmt_character->bindParam(":username", $customer_username);
$stmt_character->execute();
$character_statuses = $stmt_character->fetchAll(PDO::FETCH_ASSOC);

$stmt_income = $pdo->prepare("SELECT status, created_at, rejection_comment FROM income_certificates WHERE username = :username ORDER BY created_at DESC");
$stmt_income->bindParam(":username", $customer_username);
$stmt_income->execute();
$income_statuses = $stmt_income->fetchAll(PDO::FETCH_ASSOC);

$stmt_death = $pdo->prepare("SELECT status, created_at, rejection_comment FROM death_registrations WHERE username = :username ORDER BY created_at DESC");
$stmt_death->bindParam(":username", $customer_username);
$stmt_death->execute();
$death_statuses = $stmt_death->fetchAll(PDO::FETCH_ASSOC);

// Fetch Land Use Certificates
$stmt_land_use = $pdo->prepare("SELECT status, created_at, rejection_comment FROM land_use_certificates WHERE username = :username ORDER BY created_at DESC");
$stmt_land_use->bindParam(":username", $customer_username);
$stmt_land_use->execute();
$land_use_statuses = $stmt_land_use->fetchAll(PDO::FETCH_ASSOC);

// Fetch Tree Cutting Certificates
$stmt_tree_cutting = $pdo->prepare("SELECT status, created_at, rejection_comment FROM tree_cutting_certificates WHERE username = :username ORDER BY created_at DESC");
$stmt_tree_cutting->bindParam(":username", $customer_username);
$stmt_tree_cutting->execute();
$tree_cutting_statuses = $stmt_tree_cutting->fetchAll(PDO::FETCH_ASSOC);

// Fetch National ID Requests
$stmt_national_id = $pdo->prepare("SELECT status, created_at, rejection_comment FROM national_id_requests WHERE username = :username ORDER BY created_at DESC");
$stmt_national_id->bindParam(":username", $customer_username);
$stmt_national_id->execute();
$national_id_statuses = $stmt_national_id->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Application Status</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
    /* ================== Beautiful User-Friendly Track App Interface ================== */
    
    /* CSS Variables for White/Blue/Green Grama Sewaka Theme */
    :root {
        --track-primary: #3b82f6;
        --track-secondary: #10b981;
        --track-accent: #059669;
        --track-success: #22c55e;
        --track-warning: #f59e0b;
        --track-error: #ef4444;
        --track-info: #06b6d4;
        --track-light: #f8fafc;
        --track-dark: #1e293b;
        --track-gray: #64748b;
        --track-white: #ffffff;
        --track-shadow: rgba(0, 0, 0, 0.1);
        --track-shadow-lg: rgba(0, 0, 0, 0.15);
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
    }

    
    /* Enhanced Header Styles with White/Blue/Green Theme */
    header {
        background: linear-gradient(135deg, 
            var(--soft-blue) 0%, 
            var(--fresh-green) 50%, 
            var(--soft-blue) 100%);
        background-size: 200% 100%;
        animation: trackGradientShift 8s ease infinite;
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 
            0 10px 30px rgba(0, 0, 0, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 4px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(20px);
        transition: all 0.3s ease;
    }

    /* Header Hover Effect */
    header:hover {
        transform: translateY(-2px);
        box-shadow: 
            0 15px 40px rgba(0, 0, 0, 0.25),
            0 0 0 1px rgba(255, 255, 255, 0.2);
    }

    /* Enhanced Logo */
    .logo img {
        height: 55px;
        transition: all 0.3s ease;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    .logo img:hover {
        transform: scale(1.1) rotate(2deg);
        filter: drop-shadow(0 8px 16px rgba(255, 255, 255, 0.3));
    }

    /* Enhanced Middle Navigation */
    .middle-nav ul {
        display: flex;
        list-style: none;
        gap: 2rem;
    }

    .middle-nav a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        padding: 0.8rem 1.2rem;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.95rem;
    }

    .middle-nav a::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        transform: scale(0);
        transition: transform 0.3s ease;
        z-index: -1;
    }

    .middle-nav a:hover::before {
        transform: scale(1);
    }

    .middle-nav a:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
        transform: translateY(-2px);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Enhanced Right Navigation */
    .right-nav {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .right-nav a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    /* Enhanced Login Button */
    .login-btn {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        padding: 0.8rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        position: relative;
        overflow: hidden;
    }

    .login-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.6s ease;
    }

    .login-btn:hover::before {
        left: 100%;
    }

    .login-btn:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    }

    /* Enhanced Main Title with White/Blue/Green Theme */
    h1 {
        font-size: 3rem;
        
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-align: center;
        margin: 3rem 0;
        padding: 2rem;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.1),
            0 0 0 1px rgba(255, 255, 255, 0.2);
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 1px;
        position: relative;
        overflow: hidden;
        animation: trackTitleGlow 3s ease-in-out infinite alternate;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    h1::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: trackShimmer 3s ease-in-out infinite;
    }

    /* Enhanced Status Container */
    .status-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
        position: relative;
        z-index: 10;
    }

    /* Enhanced Status Cards with White/Blue/Green Theme */
    .status-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        margin: 2rem 0;
        padding: 2.5rem;
        box-shadow: 
            0 25px 50px rgba(0, 0, 0, 0.1),
            0 0 0 1px rgba(255, 255, 255, 0.2);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(59, 130, 246, 0.15);
    }

    .status-card::before {
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
        animation: trackGradientShift 3s ease infinite;
    }

    .status-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 
            0 35px 70px rgba(0, 0, 0, 0.15),
            0 0 0 1px rgba(255, 255, 255, 0.3);
        border-color: var(--soft-blue);
    }

    /* Enhanced Status Card Title with White/Blue/Green Theme */
    .status-card h3 {
        font-size: 1.8rem;
       
        background-clip: text;
        margin-bottom: 1.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Enhanced Status Card Links with White/Blue/Green Theme */
    .status-card a {
        text-decoration: none;
        color: var(--soft-blue);
        font-weight: 700;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        position: relative;
        display: inline-block;
    }

    .status-card a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--soft-blue), var(--fresh-green));
        transition: width 0.3s ease;
    }

    .status-card a:hover::after {
        width: 100%;
    }

    .status-card a:hover {
        color: var(--fresh-green);
        transform: translateY(-2px);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Enhanced Status Card Content */
    .status-card p {
        margin: 1rem 0;
        font-size: 1.1rem;
        line-height: 1.6;
        color: var(--track-dark);
        font-weight: 500;
    }

    .status-card strong {
        color: var(--soft-blue);
        font-weight: 700;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Enhanced Rejection Comment */
    .rejection-comment {
        background: linear-gradient(135deg, 
            rgba(239, 68, 68, 0.1) 0%, 
            rgba(220, 38, 38, 0.1) 100%);
        padding: 1.5rem;
        border-radius: 15px;
        border-left: 4px solid var(--track-error);
        margin-top: 1.5rem;
        font-style: italic;
        color: var(--track-error);
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.1);
    }

    /* Enhanced Buttons with White/Blue/Green Theme */
    button {
        padding: 1rem 2rem;
        margin-top: 2rem;
        background: linear-gradient(135deg, 
            var(--soft-blue) 0%, 
            var(--fresh-green) 100%);
        color: white;
        border: none;
        border-radius: 15px;
        font-size: 1.1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        position: relative;
        overflow: hidden;
    }

    button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s ease;
    }

    button:hover::before {
        left: 100%;
    }

    button:hover {
        background: linear-gradient(135deg, 
            var(--fresh-green) 0%, 
            var(--soft-blue) 100%);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
    }

    /* Enhanced Links with White/Blue/Green Theme */
    a {
        text-decoration: none;
        color: var(--soft-blue);
        transition: all 0.3s ease;
        font-weight: 600;
    }

    a:hover {
        color: var(--fresh-green);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Enhanced Animations */
    @keyframes trackGradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes trackBackgroundMove {
        0% { transform: translateX(0) translateY(0); }
        25% { transform: translateX(-5%) translateY(-5%); }
        50% { transform: translateX(5%) translateY(-10%); }
        75% { transform: translateX(-3%) translateY(5%); }
        100% { transform: translateX(0) translateY(0); }
    }

    @keyframes trackFloat {
        0% { transform: translateY(0) rotate(0deg); }
        100% { transform: translateY(-100px) rotate(360deg); }
    }

    @keyframes trackTitleGlow {
        0% { text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        100% { text-shadow: 0 4px 20px rgba(59, 130, 246, 0.3); }
    }

    @keyframes trackShimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    @keyframes trackFadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Enhanced Card Animations */
    .status-card {
        animation: trackFadeIn 0.6s ease-out forwards;
        opacity: 0;
    }

    .status-card:nth-child(1) { animation-delay: 0.1s; }
    .status-card:nth-child(2) { animation-delay: 0.2s; }
    .status-card:nth-child(3) { animation-delay: 0.3s; }
    .status-card:nth-child(4) { animation-delay: 0.4s; }
    .status-card:nth-child(5) { animation-delay: 0.5s; }
    .status-card:nth-child(6) { animation-delay: 0.6s; }
    .status-card:nth-child(7) { animation-delay: 0.7s; }
    .status-card:nth-child(8) { animation-delay: 0.8s; }

    /* Enhanced Responsive Design */
    @media (max-width: 1200px) {
        .status-container {
            padding: 0 1rem;
        }
    }

    @media (max-width: 900px) {
        header {
            flex-direction: column;
            padding: 1rem;
        }

        .middle-nav ul {
            margin: 1rem 0;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        .right-nav {
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        h1 {
            font-size: 2.5rem;
            padding: 1.5rem;
        }

        .status-card {
            padding: 2rem;
            margin: 1.5rem 0;
        }
    }

    @media (max-width: 600px) {
        h1 {
            font-size: 2rem;
            padding: 1rem;
        }

        .status-card {
            padding: 1.5rem;
            margin: 1rem 0;
        }

        .status-card h3 {
            font-size: 1.5rem;
        }

        .status-card p {
            font-size: 1rem;
        }

        button {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
        }
    }

    /* Enhanced Focus States for Accessibility */
    .middle-nav a:focus,
    .right-nav a:focus,
    button:focus,
    .status-card a:focus {
        outline: 3px solid rgba(59, 130, 246, 0.5);
        outline-offset: 2px;
    }

    /* Smooth Transitions for All Interactive Elements */
    * {
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    </style>
</head>
<body>
    <header>
  <div class="logo">
    <img src="../images/Logo_3.png" alt="e-GramaSewa Logo">
  </div>

  <!-- Middle Navigation Links -->
  <nav class="middle-nav">
    <ul>
      <li><a href="customer_dashboard.php">Home</a></li>
      <li><a href="../pages/About.html">About</a></li>
      <li><a href="../pages/Service.html">Services</a></li>
      <li><a href="../pages/contact.html">Contact</a></li>
    </ul>
  </nav>

  <!-- Right Side Buttons -->
  <div class="right-nav">
    <a href="../php/login.php" class="login-btn">Login</a>
    <a href="logout.php" class="login-btn">Logout</a>
    
  </div>
</header>
    <div class="status-container">
        <h1>Application Status</h1>

        <!-- Display Residence Certificate Statuses -->
        <?php foreach ($residence_statuses as $residence_status): ?>
            <div class="status-card">
                <h3><a href="view_details.php?certificate=residence&username=<?php echo urlencode($customer_username); ?>">Residence Certificate (PRINT)</a></h3>
                <p><strong>Status:</strong> 
                    <?php echo htmlspecialchars($residence_status['status']); ?>
                </p>
                <p><strong>Applied on:</strong> 
                    <?php echo date('d-m-Y', strtotime($residence_status['created_at'])); ?>
                </p>
                <?php if ($residence_status['status'] == 'Rejected'): ?>
                    <p class="rejection-comment"><strong>Rejection Comment:</strong> <?php echo htmlspecialchars($residence_status['rejection_comment']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Display Character Certificate Statuses -->
        <?php foreach ($character_statuses as $character_status): ?>
            <div class="status-card">
                <h3><a href="view_details.php?certificate=character&username=<?php echo urlencode($customer_username); ?>">Character Certificate (PRINT)</a></h3>
                <p><strong>Status:</strong> 
                    <?php echo htmlspecialchars($character_status['status']); ?>
                </p>
                <p><strong>Applied on:</strong> 
                    <?php echo date('d-m-Y', strtotime($character_status['created_at'])); ?>
                </p>
                <?php if ($character_status['status'] == 'Rejected'): ?>
                    <p class="rejection-comment"><strong>Rejection Comment:</strong> <?php echo htmlspecialchars($character_status['rejection_comment']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Display Income Certificate Statuses -->
        <?php foreach ($income_statuses as $income_status): ?>
            <div class="status-card">
                <h3><a href="view_details.php?certificate=income&username=<?php echo urlencode($customer_username); ?>">Income Certificate (PRINT)</a></h3>
                <p><strong>Status:</strong> 
                    <?php echo htmlspecialchars($income_status['status']); ?>
                </p>
                <p><strong>Applied on:</strong> 
                    <?php echo date('d-m-Y', strtotime($income_status['created_at'])); ?>
                </p>
                <?php if ($income_status['status'] == 'Rejected'): ?>
                    <p class="rejection-comment"><strong>Rejection Comment:</strong> <?php echo htmlspecialchars($income_status['rejection_comment']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Display Death Registration Statuses -->
        <?php foreach ($death_statuses as $death_status): ?>
            <div class="status-card">
                <h3><a href="view_details.php?certificate=death&username=<?php echo urlencode($customer_username); ?>">Death Registration (PRINT)</a></h3>
                <p><strong>Status:</strong> 
                    <?php echo htmlspecialchars($death_status['status']); ?>
                </p>
                <p><strong>Applied on:</strong> 
                    <?php echo date('d-m-Y', strtotime($death_status['created_at'])); ?>
                </p>
                <?php if ($death_status['status'] == 'Rejected'): ?>
                    <p class="rejection-comment"><strong>Rejection Comment:</strong> <?php echo htmlspecialchars($death_status['rejection_comment']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Display Land Use Certificate Statuses -->
        <?php foreach ($land_use_statuses as $land_use_status): ?>
            <div class="status-card">
                <h3><a href="view_details.php?certificate=land_use&username=<?php echo urlencode($customer_username); ?>">Land Use Certificate (PRINT)</a></h3>
                <p><strong>Status:</strong> 
                    <?php echo htmlspecialchars($land_use_status['status']); ?>
                </p>
                <p><strong>Applied on:</strong> 
                    <?php echo date('d-m-Y', strtotime($land_use_status['created_at'])); ?>
                </p>
                <?php if ($land_use_status['status'] == 'Rejected'): ?>
                    <p class="rejection-comment"><strong>Rejection Comment:</strong> <?php echo htmlspecialchars($land_use_status['rejection_comment']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Display Tree Cutting Certificate Statuses -->
        <?php foreach ($tree_cutting_statuses as $tree_cutting_status): ?>
            <div class="status-card">
                <h3><a href="view_details.php?certificate=tree_cutting&username=<?php echo urlencode($customer_username); ?>">Tree Cutting Certificate (PRINT)</a></h3>
                <p><strong>Status:</strong> 
                    <?php echo htmlspecialchars($tree_cutting_status['status']); ?>
                </p>
                <p><strong>Applied on:</strong> 
                    <?php echo date('d-m-Y', strtotime($tree_cutting_status['created_at'])); ?>
                </p>
                <?php if ($tree_cutting_status['status'] == 'Rejected'): ?>
                    <p class="rejection-comment"><strong>Rejection Comment:</strong> <?php echo htmlspecialchars($tree_cutting_status['rejection_comment']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Display National ID Request Statuses -->
        <?php foreach ($national_id_statuses as $national_id_status): ?>
            <div class="status-card">
                <h3><a href="view_details.php?certificate=national_id&username=<?php echo urlencode($customer_username); ?>">National ID Request (PRINT)</a></h3>
                <p><strong>Status:</strong> 
                    <?php echo htmlspecialchars($national_id_status['status']); ?>
                </p>
                <p><strong>Applied on:</strong> 
                    <?php echo date('d-m-Y', strtotime($national_id_status['created_at'])); ?>
                </p>
                <?php if ($national_id_status['status'] == 'Rejected'): ?>
                    <p class="rejection-comment"><strong>Rejection Comment:</strong> <?php echo htmlspecialchars($national_id_status['rejection_comment']); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div>
</body>
</html>

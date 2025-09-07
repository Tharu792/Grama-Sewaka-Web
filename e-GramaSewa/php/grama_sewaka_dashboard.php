<?php
// Start the session
session_start();

// Database connection details
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "grama_sewa";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Ensure that the Grama Sewaka is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Fetch the Grama Sewaka's details
$grama_sewaka_username = $_SESSION['username'];
$stmt_sewaka = $pdo->prepare("SELECT * FROM grama_sewaka WHERE username = :username");
$stmt_sewaka->bindParam(":username", $grama_sewaka_username);
$stmt_sewaka->execute();
$grama_sewaka = $stmt_sewaka->fetch(PDO::FETCH_ASSOC);

// === Notification: pending applications ===
$new_apps_count = 0;
$certificate_types = [
    'residence_certificates',
    'character_certificates',
    'income_certificates',
    'land_use_certificates',
    'tree_cutting_certificates',
    'death_registrations',
    'national_id_requests'
];

$pending_applications = [];
foreach ($certificate_types as $certificate_type) {
    $stmt_pending = $pdo->prepare("SELECT COUNT(*) FROM $certificate_type WHERE status = 'Pending' AND divisional_secretariat = :divisional_secretariat");
    $stmt_pending->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
    $stmt_pending->execute();
    $count = $stmt_pending->fetchColumn();
    $pending_applications[$certificate_type] = $count;
    $new_apps_count += $count;
}

// === Status counts per table ===
// Residence Certificates (FIXED: table + where clause)
$stmt_residence_approved = $pdo->prepare("SELECT COUNT(*) FROM residence_certificates WHERE status = 'Approved' AND divisional_secretariat = :divisional_secretariat");
$stmt_residence_approved->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_residence_approved->execute();
$residence_approved_count = $stmt_residence_approved->fetchColumn();

$stmt_residence_rejected = $pdo->prepare("SELECT COUNT(*) FROM residence_certificates WHERE status = 'Rejected' AND divisional_secretariat = :divisional_secretariat");
$stmt_residence_rejected->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_residence_rejected->execute();
$residence_rejected_count = $stmt_residence_rejected->fetchColumn();

$stmt_residence_pending = $pdo->prepare("SELECT COUNT(*) FROM residence_certificates WHERE status = 'Pending' AND divisional_secretariat = :divisional_secretariat");
$stmt_residence_pending->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_residence_pending->execute();
$residence_pending_count = $stmt_residence_pending->fetchColumn();

// Character Certificates
$stmt_character_approved = $pdo->prepare("SELECT COUNT(*) FROM character_certificates WHERE status = 'Approved' AND divisional_secretariat = :divisional_secretariat");
$stmt_character_approved->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_character_approved->execute();
$character_approved_count = $stmt_character_approved->fetchColumn();

$stmt_character_rejected = $pdo->prepare("SELECT COUNT(*) FROM character_certificates WHERE status = 'Rejected' AND divisional_secretariat = :divisional_secretariat");
$stmt_character_rejected->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_character_rejected->execute();
$character_rejected_count = $stmt_character_rejected->fetchColumn();

$stmt_character_pending = $pdo->prepare("SELECT COUNT(*) FROM character_certificates WHERE status = 'Pending' AND divisional_secretariat = :divisional_secretariat");
$stmt_character_pending->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_character_pending->execute();
$character_pending_count = $stmt_character_pending->fetchColumn();

// Income Certificates
$stmt_income_approved = $pdo->prepare("SELECT COUNT(*) FROM income_certificates WHERE status = 'Approved' AND divisional_secretariat = :divisional_secretariat");
$stmt_income_approved->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_income_approved->execute();
$income_approved_count = $stmt_income_approved->fetchColumn();

$stmt_income_rejected = $pdo->prepare("SELECT COUNT(*) FROM income_certificates WHERE status = 'Rejected' AND divisional_secretariat = :divisional_secretariat");
$stmt_income_rejected->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_income_rejected->execute();
$income_rejected_count = $stmt_income_rejected->fetchColumn();

$stmt_income_pending = $pdo->prepare("SELECT COUNT(*) FROM income_certificates WHERE status = 'Pending' AND divisional_secretariat = :divisional_secretariat");
$stmt_income_pending->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_income_pending->execute();
$income_pending_count = $stmt_income_pending->fetchColumn();

// Land Use Certificates
$stmt_land_use_approved = $pdo->prepare("SELECT COUNT(*) FROM land_use_certificates WHERE status = 'Approved' AND divisional_secretariat = :divisional_secretariat");
$stmt_land_use_approved->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_land_use_approved->execute();
$land_use_approved_count = $stmt_land_use_approved->fetchColumn();

$stmt_land_use_rejected = $pdo->prepare("SELECT COUNT(*) FROM land_use_certificates WHERE status = 'Rejected' AND divisional_secretariat = :divisional_secretariat");
$stmt_land_use_rejected->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_land_use_rejected->execute();
$land_use_rejected_count = $stmt_land_use_rejected->fetchColumn();

$stmt_land_use_pending = $pdo->prepare("SELECT COUNT(*) FROM land_use_certificates WHERE status = 'Pending' AND divisional_secretariat = :divisional_secretariat");
$stmt_land_use_pending->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_land_use_pending->execute();
$land_use_pending_count = $stmt_land_use_pending->fetchColumn();

// Tree Cutting Certificates
$stmt_tree_cutting_approved = $pdo->prepare("SELECT COUNT(*) FROM tree_cutting_certificates WHERE status = 'Approved' AND divisional_secretariat = :divisional_secretariat");
$stmt_tree_cutting_approved->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_tree_cutting_approved->execute();
$tree_cutting_approved_count = $stmt_tree_cutting_approved->fetchColumn();

$stmt_tree_cutting_rejected = $pdo->prepare("SELECT COUNT(*) FROM tree_cutting_certificates WHERE status = 'Rejected' AND divisional_secretariat = :divisional_secretariat");
$stmt_tree_cutting_rejected->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_tree_cutting_rejected->execute();
$tree_cutting_rejected_count = $stmt_tree_cutting_rejected->fetchColumn();

$stmt_tree_cutting_pending = $pdo->prepare("SELECT COUNT(*) FROM tree_cutting_certificates WHERE status = 'Pending' AND divisional_secretariat = :divisional_secretariat");
$stmt_tree_cutting_pending->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_tree_cutting_pending->execute();
$tree_cutting_pending_count = $stmt_tree_cutting_pending->fetchColumn();

// Death Registrations
$stmt_death_reg_approved = $pdo->prepare("SELECT COUNT(*) FROM death_registrations WHERE status = 'Approved' AND divisional_secretariat = :divisional_secretariat");
$stmt_death_reg_approved->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_death_reg_approved->execute();
$death_reg_approved_count = $stmt_death_reg_approved->fetchColumn();

$stmt_death_reg_rejected = $pdo->prepare("SELECT COUNT(*) FROM death_registrations WHERE status = 'Rejected' AND divisional_secretariat = :divisional_secretariat");
$stmt_death_reg_rejected->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_death_reg_rejected->execute();
$death_reg_rejected_count = $stmt_death_reg_rejected->fetchColumn();

$stmt_death_reg_pending = $pdo->prepare("SELECT COUNT(*) FROM death_registrations WHERE status = 'Pending' AND divisional_secretariat = :divisional_secretariat");
$stmt_death_reg_pending->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_death_reg_pending->execute();
$death_reg_pending_count = $stmt_death_reg_pending->fetchColumn();

// National ID Requests
$stmt_national_id_approved = $pdo->prepare("SELECT COUNT(*) FROM national_id_requests WHERE status = 'Approved' AND divisional_secretariat = :divisional_secretariat");
$stmt_national_id_approved->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_national_id_approved->execute();
$national_id_approved_count = $stmt_national_id_approved->fetchColumn();

$stmt_national_id_rejected = $pdo->prepare("SELECT COUNT(*) FROM national_id_requests WHERE status = 'Rejected' AND divisional_secretariat = :divisional_secretariat");
$stmt_national_id_rejected->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_national_id_rejected->execute();
$national_id_rejected_count = $stmt_national_id_rejected->fetchColumn();

$stmt_national_id_pending = $pdo->prepare("SELECT COUNT(*) FROM national_id_requests WHERE status = 'Pending' AND divisional_secretariat = :divisional_secretariat");
$stmt_national_id_pending->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
$stmt_national_id_pending->execute();
$national_id_pending_count = $stmt_national_id_pending->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grama Sewaka Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* ================== Grama Sewaka Dashboard Interface ================== */

    /* CSS Variables for Custom Color Palette */
    :root {
        --primary: rgba(37, 99, 235, 0.9);
        --secondary: rgba(16, 185, 129, 0.9);
        --card: rgba(255, 255, 255, 0.95);
        --text: #1e293b;
        --text-light: #64748b;
        --accent: #e74c3c;
        --light: #ecf0f1;
        --dark: #2c3e50;
        --success: #27ae60;
        --warning: #f39c12;
        --danger: #e74c3c;
        --gray: #95a5a6;
        --light-gray: #f8f9fa;
    }

    /* Global Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        font-family: Arial, sans-serif;
        background-image: 
                
                url('../images/image1.jpg');
        background-size: cover;
        color: white;
        line-height: 1.6;
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    /* Enhanced Header */
    header {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 15px 5%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 4px solid rgba(255, 255, 255, 0.9);
    }

    .logo img {
        height: 50px;
        filter: brightness(0) invert(1);
    }

    .middle-nav ul {
        display: flex;
        list-style: none;
        gap: 25px;
    }

    .middle-nav a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        padding: 8px 15px;
        border-radius: 30px;
        transition: background 0.3s ease, color 0.3s ease;
    }

    .middle-nav a:hover {
        background: rgba(255, 255, 255, 0.9);
        color: var(--primary);
    }

    .right-nav a {
        color: white;
        text-decoration: none;
        font-weight: 500;
    }

    .login-btn, .register-btn {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 10px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
        position: relative;
        z-index: 10;
    }

    .dashboard-container h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
        font-weight: 800;
        text-align: center;
        color: white;
        text-transform: uppercase;
        letter-spacing: 2px;
        position: relative;
    }

    .dashboard-container h1::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 150px;
        height: 4px;
        background: var(--secondary);
        border-radius: 2px;
    }

    .dashboard-container h2 {
        font-size: 2.5rem;
        color: white;
        text-align: center;
        margin: 30px 0 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
    }

    .dashboard-container h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: var(--secondary);
        border-radius: 2px;
    }

    /* Notification */
    .notification {
        background: var(--card);
        color: var(--text);
        padding: 20px 25px;
        border-radius: 20px;
        margin-bottom: 30px;
        font-size: 16px;
        border-left: 4px solid var(--success);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Profile Card */
    .profile-card {
        background: var(--card);
        padding: 40px;
        border-radius: 20px;
        margin-bottom: 40px;
    }

    .profile-info h3 {
        color: var(--text);
        margin-bottom: 20px;
        font-size: 1.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .profile-info p {
        margin-bottom: 12px;
        color: var(--text-light);
        font-size: 1.1rem;
        background: rgba(16, 185, 129, 0.05);
        padding: 15px;
        border-radius: 12px;
        border-left: 4px solid var(--secondary);
    }

    .profile-info strong {
        color: var(--primary);
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
    }

    /* Table */
    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 40px;
        background: var(--card);
        border-radius: 20px;
        overflow: hidden;
    }

    th, td {
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 20px 25px;
        text-align: left;
    }

    thead {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }

    td {
        color: var(--text);
        font-weight: 500;
    }

    /* Buttons */
    button {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
    }

    button:hover {
        background: linear-gradient(135deg, var(--secondary), var(--primary));
    }

    .logout-section {
        text-align: center;
        margin: 40px 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        header {
            flex-direction: column;
            padding: 15px;
        }

        .middle-nav ul {
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .dashboard-container h1 {
            font-size: 2.5rem;
        }

        .dashboard-container h2 {
            font-size: 2rem;
        }

        table {
            display: block;
            overflow-x: auto;
        }
    }

    @media (max-width: 576px) {
        .middle-nav ul {
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .dashboard-container h1 {
            font-size: 2rem;
        }

        .dashboard-container h2 {
            font-size: 1.8rem;
        }

        .profile-card {
            padding: 25px;
        }

        .notification {
            padding: 15px 20px;
        }
    }
</style>

</head>
<body>
<header>
    <div class="logo">
        <img src="../images/LOGO.jpg" alt="e-GramaSewa Logo">
    </div>
    <nav class="middle-nav">
        <ul>
            <li><a href="grama_sewaka_dashboard.php">Home</a></li>
            <li><a href="../pages/About.html">About</a></li>
            <li><a href="user_application.php">User Application</a></li>
            <li><a href="reject_reason_application.php">reject_reason</a></li>
            <li><a href="../pages/Service.html">Services</a></li>
            <li><a href="Gramasewaka_view_user_application.php">All User</a></li>
        </ul>
    </nav>
    <div class="right-nav">
        <a href="login.php" class="login-btn">Login</a>
        <a href="logout.php" class="register-btn">Log Out</a>
    </div>
</header>

<div class="dashboard-container">
    <h1>Welcome, Grama Sewaka</h1>

    <?php if ($new_apps_count > 0): ?>
        <div class="notification">
            <span><strong>You have <?php echo (int)$new_apps_count; ?> new applications pending.</strong></span>
            <button onclick="this.parentElement.style.display='none'">Dismiss</button>
        </div>
    <?php endif; ?>

    <div class="profile-card">
        <div class="profile-info">
            <h3><strong>Name:</strong> <?php echo htmlspecialchars($grama_sewaka['name']); ?></h3>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($grama_sewaka['username']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($grama_sewaka['contact']); ?></p>
            <p><strong>Divisional Secretariat:</strong> <?php echo htmlspecialchars($grama_sewaka['divisional_secretariat']); ?></p>
        </div>
    </div>

    <h2>Certificate Application Status Counts</h2>
    <table>
        <thead>
        <tr>
            <th>Certificate Type</th>
            <th>Approved</th>
            <th>Rejected</th>
            <th>Pending</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Residence Certificates</td>
            <td><?php echo (int)$residence_approved_count; ?></td>
            <td><?php echo (int)$residence_rejected_count; ?></td>
            <td><?php echo (int)$residence_pending_count; ?></td>
        </tr>
        <tr>
            <td>Character Certificates</td>
            <td><?php echo (int)$character_approved_count; ?></td>
            <td><?php echo (int)$character_rejected_count; ?></td>
            <td><?php echo (int)$character_pending_count; ?></td>
        </tr>
        <tr>
            <td>Income Certificates</td>
            <td><?php echo (int)$income_approved_count; ?></td>
            <td><?php echo (int)$income_rejected_count; ?></td>
            <td><?php echo (int)$income_pending_count; ?></td>
        </tr>
        <tr>
            <td>Land Use Certificates</td>
            <td><?php echo (int)$land_use_approved_count; ?></td>
            <td><?php echo (int)$land_use_rejected_count; ?></td>
            <td><?php echo (int)$land_use_pending_count; ?></td>
        </tr>
        <tr>
            <td>Tree Cutting Certificates</td>
            <td><?php echo (int)$tree_cutting_approved_count; ?></td>
            <td><?php echo (int)$tree_cutting_rejected_count; ?></td>
            <td><?php echo (int)$tree_cutting_pending_count; ?></td>
        </tr>
        <tr>
            <td>Death Registrations</td>
            <td><?php echo (int)$death_reg_approved_count; ?></td>
            <td><?php echo (int)$death_reg_rejected_count; ?></td>
            <td><?php echo (int)$death_reg_pending_count; ?></td>
        </tr>
        <tr>
            <td>National ID Requests</td>
            <td><?php echo (int)$national_id_approved_count; ?></td>
            <td><?php echo (int)$national_id_rejected_count; ?></td>
            <td><?php echo (int)$national_id_pending_count; ?></td>
        </tr>
        </tbody>
    </table>

    <div class="logout-section" style="margin:24px 0;">
        <a href="logout.php"><button>Logout</button></a>
    </div>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-section footer-info">
            <h4>E-Gramasevaka</h4>
            <p>Digital government services platform providing efficient and transparent access to various government services and certificates.</p>
            <div class="social-icons">
                <a href="https://www.whatsapp.com" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
                <a href="https://www.google.com" target="_blank"><i class="fa-brands fa-google"></i></a>
                <a href="https://www.facebook.com" target="_blank"><i class="fa-brands fa-facebook"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 e-GramaSewa. All rights reserved.</p>
    </div>
</footer>
</body>
</html>

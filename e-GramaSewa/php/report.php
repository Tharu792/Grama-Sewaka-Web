<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

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

// Fetch Grama Sewaka Summary (Village Employees)
$stmt_sewaka = $pdo->query("SELECT COUNT(*) AS total_grama_sewaka, province, position FROM grama_sewaka GROUP BY province, position");
$grama_sewaka_summary = $stmt_sewaka->fetchAll(PDO::FETCH_ASSOC);

// Fetch Customer Summary
$stmt_customers = $pdo->query("SELECT COUNT(*) AS total_customers, province, district FROM customers GROUP BY province, district");
$customer_summary = $stmt_customers->fetchAll(PDO::FETCH_ASSOC);

// Fetch Character Certificates Summary
$stmt_characters = $pdo->query("SELECT COUNT(*) AS total_certificates, status FROM character_certificates GROUP BY status");
$character_summary = $stmt_characters->fetchAll(PDO::FETCH_ASSOC);

// Fetch Death Registrations Summary
$stmt_death = $pdo->query("SELECT COUNT(*) AS total_deaths, status FROM death_registrations GROUP BY status");
$death_summary = $stmt_death->fetchAll(PDO::FETCH_ASSOC);

// Fetch Income Certificates Summary
$stmt_income = $pdo->query("SELECT COUNT(*) AS total_incomes, status FROM income_certificates GROUP BY status");
$income_summary = $stmt_income->fetchAll(PDO::FETCH_ASSOC);

// Fetch Land Use Certificates Summary
$stmt_land = $pdo->query("SELECT COUNT(*) AS total_land_certificates, status FROM land_use_certificates GROUP BY status");
$land_summary = $stmt_land->fetchAll(PDO::FETCH_ASSOC);

// Fetch National ID Requests Summary
$stmt_national_id = $pdo->query("SELECT COUNT(*) AS total_national_ids, status FROM national_id_requests GROUP BY status");
$national_id_summary = $stmt_national_id->fetchAll(PDO::FETCH_ASSOC);

// Fetch Residence Certificates Summary
$stmt_residence = $pdo->query("SELECT COUNT(*) AS total_residence_certificates, status FROM residence_certificates GROUP BY status");
$residence_summary = $stmt_residence->fetchAll(PDO::FETCH_ASSOC);

// Fetch Tree Cutting Certificates Summary
$stmt_tree_cutting = $pdo->query("SELECT COUNT(*) AS total_tree_cutting_certificates, status FROM tree_cutting_certificates GROUP BY status");
$tree_cutting_summary = $stmt_tree_cutting->fetchAll(PDO::FETCH_ASSOC);

// Fetch Service Applications (if required)
$stmt_applications = $pdo->query("SELECT * FROM service_applications");
$applications = $stmt_applications->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f4f9;
            background-image: 
                
                url('../images/image1.jpg');
            color: #333;
        }

        /* Header Styles */
        header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            height: 50px;
        }

        .middle-nav ul {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }

        .middle-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            border-radius: 6px;
            transition: background-color 0.3s, color 0.3s;
        }

        .middle-nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .right-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            background-color: #ef4444;
            transition: background-color 0.3s;
        }

        .right-nav a:hover {
            background-color: #dc2626;
        }

        /* Main Container */
        .report-container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .report-container h2 {
            text-align: center;
            font-size: 2rem;
            color: #1e3a8a;
            margin-bottom: 2rem;
        }

        /* Summary Section */
        .summary-section {
            display: flex;
            justify-content: space-between;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            flex: 1;
        }

        .summary-card h3 {
            color: #3b82f6;
            margin-bottom: 1rem;
        }

        .summary-card p {
            font-size: 1.2rem;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #3b82f6;
            color: white;
            font-weight: bold;
        }

        td {
            background-color: #f9fafb;
        }

        tr:hover td {
            background-color: #dbeafe;
        }

        .generate-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
            transition: background-color 0.3s ease;
            margin: 20px auto;
            display: block;
        }

        .generate-btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../images/Logo_3.png" alt="e-GramaSewa Logo">
        </div>
        <nav class="middle-nav">
            <ul>
                <li><a href="admin_dashboard.php">Home</a></li>
                <li><a href="add_grama_sewaka.php">Add Grama Sewaka</a></li>
                <li><a href="admin_view_user_application.php">View User Applications</a></li>
                <li><a href="admin_view_user.php">View Users</a></li>
                <li><a href="report.php">Generate Report</a></li>
            </ul>
        </nav>
        <div class="right-nav">
            <a href="logout.php" class="login-btn">Logout</a>
        </div>
    </header>

    <div class="report-container">
        <h2>Admin Report</h2>

        <!-- Summary Section -->
        <div class="summary-section">
            <!-- Grama Sewaka Summary -->
            <div class="summary-card">
                <h3>Grama Sewaka Summary</h3>
                <p><strong>Total Grama Sewaka:</strong> <?php echo count($grama_sewaka_summary); ?></p>
                <ul>
                    <?php foreach ($grama_sewaka_summary as $sewaka): ?>
                        <li><strong>Province:</strong> <?php echo htmlspecialchars($sewaka['province']); ?>, 
                            <strong>Position:</strong> <?php echo htmlspecialchars($sewaka['position']); ?>, 
                            <strong>Count:</strong> <?php echo htmlspecialchars($sewaka['total_grama_sewaka']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Customer Summary -->
            <div class="summary-card">
                <h3>Customer Summary</h3>
                <p><strong>Total Customers:</strong> <?php echo count($customer_summary); ?></p>
                <ul>
                    <?php foreach ($customer_summary as $customer): ?>
                        <li><strong>Province:</strong> <?php echo htmlspecialchars($customer['province']); ?>, 
                            <strong>District:</strong> <?php echo htmlspecialchars($customer['district']); ?>, 
                            <strong>Count:</strong> <?php echo htmlspecialchars($customer['total_customers']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Additional Tables Summary Section -->
        <div class="summary-section">
            <!-- Character Certificates Summary -->
            <div class="summary-card">
                <h3>Character Certificates Summary</h3>
                <p><strong>Total Certificates:</strong> <?php echo count($character_summary); ?></p>
                <ul>
                    <?php foreach ($character_summary as $cert): ?>
                        <li><strong>Status:</strong> <?php echo htmlspecialchars($cert['status']); ?>, 
                            <strong>Count:</strong> <?php echo htmlspecialchars($cert['total_certificates']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Death Registrations Summary -->
            <div class="summary-card">
                <h3>Death Registrations Summary</h3>
                <p><strong>Total Deaths:</strong> <?php echo count($death_summary); ?></p>
                <ul>
                    <?php foreach ($death_summary as $death): ?>
                        <li><strong>Status:</strong> <?php echo htmlspecialchars($death['status']); ?>, 
                            <strong>Count:</strong> <?php echo htmlspecialchars($death['total_deaths']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Additional Tables Summary Section (Continued) -->
        <div class="summary-section">
            <!-- Income Certificates Summary -->
            <div class="summary-card">
                <h3>Income Certificates Summary</h3>
                <p><strong>Total Income Certificates:</strong> <?php echo count($income_summary); ?></p>
                <ul>
                    <?php foreach ($income_summary as $income): ?>
                        <li><strong>Status:</strong> <?php echo htmlspecialchars($income['status']); ?>, 
                            <strong>Count:</strong> <?php echo htmlspecialchars($income['total_incomes']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Land Use Certificates Summary -->
            <div class="summary-card">
                <h3>Land Use Certificates Summary</h3>
                <p><strong>Total Land Use Certificates:</strong> <?php echo count($land_summary); ?></p>
                <ul>
                    <?php foreach ($land_summary as $land): ?>
                        <li><strong>Status:</strong> <?php echo htmlspecialchars($land['status']); ?>, 
                            <strong>Count:</strong> <?php echo htmlspecialchars($land['total_land_certificates']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Tree Cutting Certificates Summary -->
        <div class="summary-section">
            <div class="summary-card">
                <h3>Tree Cutting Certificates Summary</h3>
                <p><strong>Total Tree Cutting Certificates:</strong> <?php echo count($tree_cutting_summary); ?></p>
                <ul>
                    <?php foreach ($tree_cutting_summary as $tree): ?>
                        <li><strong>Status:</strong> <?php echo htmlspecialchars($tree['status']); ?>, 
                            <strong>Count:</strong> <?php echo htmlspecialchars($tree['total_tree_cutting_certificates']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        
        <!-- Button to generate the report -->
        <a href="generate_report.php" class="generate-btn">Generate Full Report</a>
    </div>
</body>
</html>

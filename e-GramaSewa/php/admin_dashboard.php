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

// **Fetch Grama Sewaka Records**
$stmt_grama_sewaka = $pdo->query("SELECT * FROM grama_sewaka");
$grama_sewaka_records = $stmt_grama_sewaka->fetchAll(PDO::FETCH_ASSOC);

// **Fetch Grama Sewaka Summary Data**
$stmt_grama_sewaka_summary = $pdo->query("SELECT 
                                         COUNT(*) AS total_grama_sewaka,
                                         province,
                                         position,
                                         COUNT(province) AS grama_sewaka_per_province,
                                         COUNT(position) AS grama_sewaka_per_position
                                         FROM grama_sewaka
                                         GROUP BY province, position");
$grama_sewaka_summary = $stmt_grama_sewaka_summary->fetchAll(PDO::FETCH_ASSOC);

// **Total Grama Sewakas Count**
$stmt_total_grama_sewaka = $pdo->query("SELECT COUNT(*) AS total_grama_sewaka FROM grama_sewaka");
$total_grama_sewaka = $stmt_total_grama_sewaka->fetch(PDO::FETCH_ASSOC)['total_grama_sewaka'];

// **Fetch Customer Summary Data**
$stmt_customer_summary = $pdo->query("SELECT 
                                      COUNT(*) AS total_customers,
                                      district,
                                      COUNT(district) AS customers_per_district
                                      FROM customers
                                      GROUP BY district");
$customer_summary = $stmt_customer_summary->fetchAll(PDO::FETCH_ASSOC);

// **Total Customers Count**
$stmt_total_customers = $pdo->query("SELECT COUNT(*) AS total_customers FROM customers");
$total_customers = $stmt_total_customers->fetch(PDO::FETCH_ASSOC)['total_customers'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Clean White Background Admin Dashboard */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #a2d3f1ff;
            color: #333333;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Enhanced Header */
        header {
            background: linear-gradient(135deg, #434141ff, #46627eff, #e2e8f0);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
            color: #1e293b;
            padding: 12px 3%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            border-bottom: 2px solid rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo img {
            height: 50px;
            filter: none;
            transition: all 0.3s ease;
        }

        .logo img:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 0 8px rgba(0, 0, 0, 0.2));
        }

        .logo-text {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e293b;
            text-shadow: none;
        }

        .middle-nav ul {
            display: flex;
            list-style: none;
            gap: 6px;
            background: rgba(218, 250, 239, 0.8);
            padding: 8px;
            border-radius: 50px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .middle-nav a {
            color: #1e293b;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            font-size: 0.9rem;
            letter-spacing: 0.3px;
            min-width: 120px;
            text-align: center;
            display: inline-block;
            white-space: nowrap;
            background: transparent;
        }

        .middle-nav a:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #1e40af;
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(59, 130, 246, 0.2);
        }

        .middle-nav a.active {
            background: rgba(59, 130, 246, 0.15);
            color: #1e40af;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }

        .middle-nav a::after {
            content: '';
            position: absolute;
            bottom: 3px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #1e40af);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .middle-nav a:hover::after {
            width: 50%;
        }

        .right-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #10b981, #34d399);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .user-name {
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .login-btn {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
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
            transition: left 0.5s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
        }

        /* Main Content */
        .admin-dashboard {
            padding: 40px 5%;
            background-color: #ffffff;
        }

        /* Welcome Section */
        .welcome-admin {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .welcome-admin h2 {
            font-size: 2.5rem;
            color: #1e40af;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .welcome-admin p {
            font-size: 1.2rem;
            color: #64748b;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Total Count Cards */
        .total-count-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .total-count-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .total-count-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .total-count-card h3 {
            font-size: 1.5rem;
            color: #1e40af;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .total-count-card .count-number {
            font-size: 4rem;
            font-weight: 800;
            color: #10b981;
            display: block;
            margin: 20px 0;
        }

        .total-count-card .count-icon {
            font-size: 4rem;
            color: #10b981;
            margin-bottom: 20px;
        }

        .total-count-card .count-description {
            font-size: 1.1rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Summary Sections */
        .grama-sewaka-summary, .district-summary {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .grama-sewaka-summary h3, .district-summary h3 {
            font-size: 1.8rem;
            color: #1e40af;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f8fafc;
            color: #1e40af;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        td {
            color: #374151;
            font-weight: 500;
        }

        /* Footer */
        footer {
            background: #1f2937;
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 0 5% 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-section h4 {
            font-size: 1.4rem;
            margin-bottom: 20px;
            font-weight: 600;
            color: #10b981;
        }

        .footer-info p {
            margin-bottom: 20px;
            line-height: 1.8;
            color: #d1d5db;
        }

        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #374151;
            border-radius: 50%;
            color: white;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background: #10b981;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #d1d5db;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: #10b981;
        }

        .footer-contact p {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #d1d5db;
        }

        .footer-contact i {
            color: #10b981;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #374151;
            margin: 0 5%;
            color: #9ca3af;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 15px;
                gap: 15px;
            }
            
            .logo {
                justify-content: center;
            }
            
            .logo-text {
                font-size: 1.2rem;
            }
            
            .middle-nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 6px;
                padding: 8px;
            }
            
            .middle-nav a {
                padding: 12px 20px;
                font-size: 0.9rem;
                min-width: 120px;
            }
            
            .right-nav {
                flex-direction: column;
                gap: 10px;
            }
            
            .user-info {
                padding: 6px 12px;
            }
            
            .user-avatar {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }
            
            .user-name {
                font-size: 0.8rem;
            }
            
            .login-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .welcome-admin h2 {
                font-size: 2rem;
            }
            
            .welcome-admin p {
                font-size: 1rem;
            }
            
            .total-count-cards {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .total-count-card {
                padding: 25px;
            }

            .total-count-card h3 {
                font-size: 1.3rem;
            }

            .total-count-card .count-number {
                font-size: 3.5rem;
            }

            .total-count-card .count-icon {
                font-size: 3.5rem;
            }

            .grama-sewaka-summary, .district-summary {
                padding: 20px;
            }

            th, td {
                padding: 12px 15px;
            }
        }

        @media (max-width: 576px) {
            .welcome-admin {
                padding: 20px 15px;
            }
            
            .welcome-admin h2 {
                font-size: 1.8rem;
            }

            .middle-nav a {
                padding: 10px 15px;
                font-size: 0.85rem;
                min-width: 100px;
            }

            .total-count-card {
                padding: 20px;
            }

            .total-count-card h3 {
                font-size: 1.2rem;
            }

            .total-count-card .count-number {
                font-size: 3rem;
            }

            .total-count-card .count-icon {
                font-size: 3rem;
            }

            .total-count-card .count-description {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <div class="admin-dashboard">
        <!-- Enhanced Navigation Bar -->
        <header>
            <div class="logo">
                <img src="../images/Logo_3.png" alt="e-GramaSewa Logo">
                <span class="logo-text">Admin Panel</span>
            </div>
            <nav class="middle-nav">
                <ul>
                    <li><a href="admin_dashboard.php" class="active">Home</a></li>
                    <li><a href="add_grama_sewaka.php">Add Grama Sewaka</a></li>
                    <li><a href="admin_view_user_application.php">View Applications</a></li>
                    <li><a href="admin_view_user.php">View Users</a></li>
                    <li><a href="report.php">Generate Report</a></li>
                </ul>
            </nav>
            <div class="right-nav">
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <span class="user-name">Admin</span>
                </div>
                <a href="logout.php" class="login-btn">Logout</a>
            </div>
        </header>

        <!-- Welcome Admin Section -->
        <div class="welcome-admin">
            <h2>Welcome Admin</h2>
            <p>Welcome to your Admin Dashboard. You can manage Grama Sewaka records, add new entries, and delete existing ones.</p>
        </div>

        <!-- Grama Sewaka Summary Section -->
        <div class="grama-sewaka-summary">
            <div class="summary">
                <div>
                    <h3>Total Grama Sewakas</h3>
                    <p><?php echo $total_grama_sewaka; ?></p>
                </div>
            </div>
            <h3>Grama Sewaka Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Province</th>
                        <th>Position</th>
                        <th>Total Grama Sewakas</th>
                        <th>Grama Sewakas per Province</th>
                        <th>Grama Sewakas per Position</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grama_sewaka_summary as $summary): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($summary['province']); ?></td>
                            <td><?php echo htmlspecialchars($summary['position']); ?></td>
                            <td><?php echo htmlspecialchars($summary['total_grama_sewaka']); ?></td>
                            <td><?php echo htmlspecialchars($summary['grama_sewaka_per_province']); ?></td>
                            <td><?php echo htmlspecialchars($summary['grama_sewaka_per_position']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Total Count Cards Section -->
        <div class="total-count-cards">
            <div class="total-count-card">
                <i class="fas fa-users count-icon"></i>
                <h3>Total Grama Sewakas</h3>
                <span class="count-number"><?php echo $total_grama_sewaka; ?></span>
                <p class="count-description">Registered Grama Sewakas across all provinces</p>
            </div>
            <div class="total-count-card">
                <i class="fas fa-user-friends count-icon"></i>
                <h3>Total Customers</h3>
                <span class="count-number"><?php echo $total_customers; ?></span>
                <p class="count-description">Registered customers using our services</p>
            </div>
        </div>

        <!-- District Wise Customer Summary -->
        <div class="district-summary">
            <h3>Customers by District</h3>
            <table>
                <thead>
                    <tr>
                        <th>District</th>
                        <th>Number of Customers</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customer_summary as $district_data): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($district_data['district']); ?></td>
                            <td><?php echo htmlspecialchars($district_data['customers_per_district']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Footer Section -->
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
    <div class="footer-section footer-links">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="Home.html">Home</a></li>
        <li><a href="About.html">Services</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li><a href="register.html">Register</a></li>
      </ul>
    </div>
    <div class="footer-section footer-contact">
      <h4>Contact Info</h4>
      <p><strong>Email:</strong> support@egramasewa.lk</p>
      <p><strong>Hotline:</strong> 1919</p>
      <p><strong>Address:</strong> Government Office, Colombo, Sri Lanka</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; 2025 e-GramaSewa. All rights reserved.</p>
  </div>
</footer>

</body>
</html>

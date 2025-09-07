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

// Fetch data from tables
$stmt_sewaka = $pdo->query("SELECT * FROM grama_sewaka");
$grama_sewaka_details = $stmt_sewaka->fetchAll(PDO::FETCH_ASSOC);

$stmt_customers = $pdo->query("SELECT * FROM customers");
$customer_details = $stmt_customers->fetchAll(PDO::FETCH_ASSOC);

$stmt_applications = $pdo->query("SELECT * FROM service_applications");
$applications_details = $stmt_applications->fetchAll(PDO::FETCH_ASSOC);

// Character Certificates
$stmt_characters = $pdo->query("SELECT * FROM character_certificates");
$character_certificates = $stmt_characters->fetchAll(PDO::FETCH_ASSOC);

// Death Registrations
$stmt_death = $pdo->query("SELECT * FROM death_registrations");
$death_registrations = $stmt_death->fetchAll(PDO::FETCH_ASSOC);

// Income Certificates
$stmt_income = $pdo->query("SELECT * FROM income_certificates");
$income_certificates = $stmt_income->fetchAll(PDO::FETCH_ASSOC);

// Land Use Certificates
$stmt_land = $pdo->query("SELECT * FROM land_use_certificates");
$land_use_certificates = $stmt_land->fetchAll(PDO::FETCH_ASSOC);

// National ID Requests
$stmt_national_id = $pdo->query("SELECT * FROM national_id_requests");
$national_id_requests = $stmt_national_id->fetchAll(PDO::FETCH_ASSOC);

// Tree Cutting Certificates
$stmt_tree_cutting = $pdo->query("SELECT * FROM tree_cutting_certificates");
$tree_cutting_certificates = $stmt_tree_cutting->fetchAll(PDO::FETCH_ASSOC);
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

        /* Print Styling */
        @media print {
            .generate-btn {
                display: none; /* Hide the 'Generate Full Report' button during print */
            }

            body {
                font-size: 14px;
                width: 210mm;
                height: 297mm;
                margin: 0;
            }

            table {
                width: 100%;
                margin-top: 0;
            }

            th, td {
                padding: 8px;
                font-size: 12px;
            }

            header {
                display: none;
            }
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
                <p><strong>Total Grama Sewaka:</strong> <?php echo count($grama_sewaka_details); ?></p>
            </div>

            <!-- Customer Summary -->
            <div class="summary-card">
                <h3>Customer Summary</h3>
                <p><strong>Total Customers:</strong> <?php echo count($customer_details); ?></p>
            </div>
        </div>

        <!-- Grama Sewaka Details -->
        <h3>Grama Sewaka Details</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Province</th>
                    <th>District</th>
                    <th>Divisional Secretariat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grama_sewaka_details as $sewaka): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sewaka['id']); ?></td>
                        <td><?php echo htmlspecialchars($sewaka['name']); ?></td>
                        <td><?php echo htmlspecialchars($sewaka['position']); ?></td>
                        <td><?php echo htmlspecialchars($sewaka['province']); ?></td>
                        <td><?php echo htmlspecialchars($sewaka['district']); ?></td>
                        <td><?php echo htmlspecialchars($sewaka['divisional_secretariat']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Customer Details -->
        <h3>Customer Details</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Province</th>
                    <th>District</th>
                    <th>Divisional Secretariat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customer_details as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['id']); ?></td>
                        <td><?php echo htmlspecialchars($customer['name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo htmlspecialchars($customer['tel_no']); ?></td>
                        <td><?php echo htmlspecialchars($customer['province']); ?></td>
                        <td><?php echo htmlspecialchars($customer['district']); ?></td>
                        <td><?php echo htmlspecialchars($customer['divisional_secretariat']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


        <!-- Character Certificates -->
        <h3>Character Certificates</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>NIC</th>
                    
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($character_certificates as $character): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($character['id']); ?></td>
                        <td><?php echo htmlspecialchars($character['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($character['nic']); ?></td>
                        
                        <td><?php echo htmlspecialchars($character['status']); ?></td>
                        <td><?php echo htmlspecialchars($character['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Death Registrations -->
        <h3>Death Registrations</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Deceased Name</th>
                    <th>Age</th>
                    <th>Contact Person</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($death_registrations as $death): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($death['id']); ?></td>
                        <td><?php echo htmlspecialchars($death['deceased_name']); ?></td>
                        <td><?php echo htmlspecialchars($death['deceased_age']); ?></td>
                        <td><?php echo htmlspecialchars($death['contact_person_name']); ?></td>
                        <td><?php echo htmlspecialchars($death['status']); ?></td>
                        <td><?php echo htmlspecialchars($death['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Income Certificates -->
        <h3>Income Certificates</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>NIC</th>
                    <th>Income</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($income_certificates as $income): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($income['id']); ?></td>
                        <td><?php echo htmlspecialchars($income['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($income['nic']); ?></td>
                        <td><?php echo htmlspecialchars($income['income']); ?></td>
                        <td><?php echo htmlspecialchars($income['status']); ?></td>
                        <td><?php echo htmlspecialchars($income['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Land Use Certificates -->
        <h3>Land Use Certificates</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>NIC</th>
                    <th>Land Location</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($land_use_certificates as $land): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($land['id']); ?></td>
                        <td><?php echo htmlspecialchars($land['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($land['nic']); ?></td>
                        <td><?php echo htmlspecialchars($land['land_location']); ?></td>
                        <td><?php echo htmlspecialchars($land['status']); ?></td>
                        <td><?php echo htmlspecialchars($land['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- National ID Requests -->
        <h3>National ID Requests</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>NIC Number</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($national_id_requests as $national_id): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($national_id['id']); ?></td>
                        <td><?php echo htmlspecialchars($national_id['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($national_id['nic_number']); ?></td>
                        <td><?php echo htmlspecialchars($national_id['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($national_id['status']); ?></td>
                        <td><?php echo htmlspecialchars($national_id['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tree Cutting Certificates -->
        <h3>Tree Cutting Certificates</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tree Name</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tree_cutting_certificates as $tree): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tree['id']); ?></td>
                        <td><?php echo htmlspecialchars($tree['tree_name']); ?></td>
                        <td><?php echo htmlspecialchars($tree['reason']); ?></td>
                        <td><?php echo htmlspecialchars($tree['status']); ?></td>
                        <td><?php echo htmlspecialchars($tree['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Print Button -->
        <button onclick="window.print()" class="generate-btn">Print Report</button>
    </div>
</body>
</html> 

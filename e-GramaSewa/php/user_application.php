<?php
// Start the session
session_start();

// Database connection details
$servername = "localhost"; // your database host
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "grama_sewa"; // your database name

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Ensure that the Grama Sewaka is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Fetch the Grama Sewaka's details
$grama_sewaka_username = $_SESSION['username'];
$stmt_sewaka = $pdo->prepare("SELECT * FROM grama_sewaka WHERE username = :username");
$stmt_sewaka->bindParam(":username", $grama_sewaka_username);
$stmt_sewaka->execute();
$grama_sewaka = $stmt_sewaka->fetch(PDO::FETCH_ASSOC);

// Define table names for iteration and validation
$table_names = [
    'residence_certificates',
    'character_certificates',
    'income_certificates',
    'land_use_certificates',
    'tree_cutting_certificates',
    'death_registrations',
    'national_id_requests'
];

// Handle the approval, rejection, or pending status of applications
if (isset($_GET['action']) && isset($_GET['id']) && isset($_GET['table'])) {
    $action = $_GET['action'];
    $application_id = $_GET['id'];
    $table = $_GET['table']; // Get the table name from the URL

    // Validate the table name to prevent SQL injection
    if (in_array($table, $table_names)) {
        $status = '';
        if ($action === 'approve') {
            $status = 'Approved';
        } elseif ($action === 'reject') {
            $status = 'Rejected';
        } else {
            $status = 'Pending';
        }

        // Update the application status in the specific table
        $stmt_update = $pdo->prepare("UPDATE $table SET status = :status WHERE id = :id");
        $stmt_update->bindParam(':status', $status);
        $stmt_update->bindParam(':id', $application_id);
        $stmt_update->execute();

        // Redirect back to the same page to refresh the data
        header("Location: user_application.php");
        exit();
    }
}

// Fetch data from various tables based on divisional_secretariat
$residence_certificates = [];
$character_certificates = [];
$income_certificates = [];
$land_use_certificates = [];
$tree_cutting_certificates = [];
$death_registrations = [];
$national_id_requests = [];

foreach ($table_names as $table_name) {
    $stmt = $pdo->prepare("SELECT * FROM $table_name WHERE divisional_secretariat = :divisional_secretariat");
    $stmt->bindParam(":divisional_secretariat", $grama_sewaka['divisional_secretariat']);
    $stmt->execute();

    // Assign fetched data to the corresponding variable
    switch ($table_name) {
        case 'residence_certificates':
            $residence_certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'character_certificates':
            $character_certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'income_certificates':
            $income_certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'land_use_certificates':
            $land_use_certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'tree_cutting_certificates':
            $tree_cutting_certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'death_registrations':
            $death_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'national_id_requests':
            $national_id_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
}
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
    /* ====== GLOBAL STYLES ====== */
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

    h1 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
        font-size: 32px;
    }

    h2 {
        margin: 40px 0 15px;
        color: #eff3f6ff;
        font-size: 22px;
        border-left: 6px solid #3498db;
        padding-left: 12px;
         text-align: center; /* ✅ Center align text */
    }
/* Profile Section */
.profile-section {
    background: var(--card, #2c3e50); /* fallback dark background */
    padding: 20px; /* smaller padding */
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin: 20px auto;  /* center card with auto */
    position: relative;
    overflow: hidden;
    color: #fff;
    text-align: center;
    max-width: 500px; /* ✅ limit card width */
}

.profile-section h2 {
    color: #0d0ab1ff;
    margin-bottom: 15px;
    font-size: 22px;
}

.profile-section p {
    color: #000000ff;
    margin: 6px 0;
    font-size: 15px;
}



    /* ====== CARD CONTAINER ====== */
    .card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    /* ====== TABLE STYLING ====== */
    .table-container {
        width: 100%;
        overflow-x: auto;
        margin: 20px 0;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    table {
        width: 100%;
        min-width: 1200px;
        border-collapse: collapse;
        border-radius: 15px;
        background: #fff;
    }

    table th, table td {
        padding: 12px 10px;
        text-align: center;
        border: 1px solid #e0e0e0;
        font-size: 0.9rem;
        color: #2c3e50;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Column Widths */
    table th:nth-child(1), table td:nth-child(1) { width: 4%; }
    table th:nth-child(2), table td:nth-child(2) { width: 8%; }
    table th:nth-child(3), table td:nth-child(3) { width: 10%; }
    table th:nth-child(4), table td:nth-child(4) { width: 6%; }
    table th:nth-child(5), table td:nth-child(5) { width: 8%; }
    table th:nth-child(6), table td:nth-child(6) { width: 12%; }
    table th:nth-child(7), table td:nth-child(7) { width: 8%; }
    table th:nth-child(8), table td:nth-child(8) { width: 8%; }
    table th:nth-child(9), table td:nth-child(9) { width: 10%; }
    table th:nth-child(10), table td:nth-child(10) { width: 6%; }
    table th:nth-child(11), table td:nth-child(11) { width: 12%; }
    table th:nth-child(12), table td:nth-child(12) { width: 8%; }
    table th:nth-child(13), table td:nth-child(13) { width: 8%; }
    table th:nth-child(14), table td:nth-child(14) { width: 10%; }

    table th {
        background: #3498db;
        color: #fff;
        text-transform: uppercase;
        font-size: 0.8rem;
        font-weight: 600;
    }

    table tr:nth-child(even) {
        background: #f8f9fa;
    }

    /* Action Links Styling */
    table td a {
        color: #2980b9;
        text-decoration: none;
        font-weight: 500;
        padding: 4px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
    }

    table td a[href*="uploads"] {
        background: #e8f5e8;
        color: #27ae60;
        border: 1px solid #27ae60;
        padding: 3px 6px;
        font-size: 0.7rem;
    }

    /* ===== Logout Section ===== */
.logout-section {
    text-align: right;        /* Align logout button to the right */
    margin-top: 20px;
    margin-bottom: 10px;
}

.logout-section button {
    background: #e74c3c;      /* Red background for logout */
    color: #fff;              /* White text */
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
}

.logout-section button:hover {
    background: #c0392b;      /* Darker red on hover */
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);
}

.logout-section a {
    text-decoration: none; /* Remove underline from link */
}


    /* Status Badges */
    .status {
        padding: 6px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }

    .status.pending { background: #f39c12; color: #fff; }
    .status.approved { background: #27ae60; color: #fff; }
    .status.rejected { background: #e74c3c; color: #fff; }

    /* Responsive */
    @media (max-width: 1200px) {
        table { min-width: 1000px; }
        table th, table td { font-size: 0.8rem; padding: 8px; }
    }

    @media (max-width: 900px) {
        table { min-width: 800px; }
        table th, table td { font-size: 0.75rem; padding: 6px; }
    }

    @media (max-width: 768px) {
        table { min-width: 700px; }
        table th, table td { font-size: 0.7rem; padding: 5px; }
    }
</style>

    
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="logo">
            <img src="../images/LOGO.jpg" alt="e-GramaSewa Logo">
        </div>
        <nav class="middle-nav">
            <ul>
                <li><a href="grama_sewaka_dashboard.php">Home</a></li>
                <li><a href="../pages/About.html">About</a></li>
                <li><a href="user_application.php">User Application</a></li>
                <li><a href="../pages/Service.html">Services</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </nav>
        <div class="right-nav">
            <a href="login.php" class="login-btn">Login</a>
            <a href="register.php" class="register-btn">Register</a>
        </div>
    </header>

    <div class="dashboard-container">
        <h1>Welcome, Grama Sewaka</h1>

        <!-- Profile Section -->
        <div class="profile-section">
            <h2>Your Profile</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($grama_sewaka['name']); ?></p>
            <p><strong>Position:</strong> <?php echo htmlspecialchars($grama_sewaka['position']); ?></p>
            <p><strong>District:</strong> <?php echo htmlspecialchars($grama_sewaka['district']); ?></p>
            <p><strong>Divisional Secretariat:</strong> <?php echo htmlspecialchars($grama_sewaka['divisional_secretariat']); ?></p>
        </div>

        <!-- Residence Certificates Table -->
        <h2>Residence Certificates</h2>
        <div class="table-container">
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC</th>
                    <th>Permanent Address</th>
                    <th>Current Address</th>
                    <th>Contact</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Created_at</th>
                    <th>District</th>
                    <th>Divisional_secretariat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($residence_certificates as $certificate) { ?>
                    <tr>
                        <td><?php echo $certificate['id']; ?></td>
                        <td><?php echo $certificate['username']; ?></td>
                        <td><?php echo $certificate['full_name']; ?></td>
                        <td><?php echo $certificate['dob']; ?></td>
                        <td><?php echo $certificate['nic']; ?></td>
                        <td><?php echo $certificate['permanent_address']; ?></td>
                        <td><?php echo $certificate['current_address']; ?></td>
                        <td><?php echo $certificate['contact']; ?></td>

                        <td>
                            <?php
                            // Decode the JSON to get the document names
                            $documents = json_decode($certificate['proof_document']);

                            // Custom labels for each uploaded document (based on order)
                            $labels = ["Electrical_Bill"];

                            if (!empty($documents)) {
                                foreach ($documents as $index => $doc) {
                                    // Use the label if available, else fallback to 'Document X'
                                    $label = isset($labels[$index]) ? $labels[$index] : "Document " . ($index + 1);

                                    echo '<a href="uploads/residence_certificates/' . htmlspecialchars($doc) . '" target="_blank">' . $label . '</a><br>';
                                }
                            } else {
                                echo "No document uploaded";
                            }

                            // Check if the birth certificate exists and display the link
                            if (isset($certificate['birth_certificate']) && !empty($certificate['birth_certificate'])) {
                                echo '<a href="uploads/birth_certificates/' . htmlspecialchars($certificate['birth_certificate']) . '" target="_blank">View_Birth_Certificate</a>';
                            } else {
                                echo "No birth certificate uploaded";
                            }

                            ?>

                        </td>
                        <td><?php echo $certificate['status']; ?></td>
                        <td>
                            <a href="user_application.php?action=approve&id=<?php echo $certificate['id']; ?>&table=residence_certificates" onclick="return confirm('Are you sure you want to approve this certificate?')">Approve</a> |
                            <a href="user_application.php?action=reject&id=<?php echo $certificate['id']; ?>&table=residence_certificates" onclick="return confirm('Are you sure you want to reject this certificate?')">Reject</a> |
                            <a href="user_application.php?action=pending&id=<?php echo $certificate['id']; ?>&table=residence_certificates" onclick="return confirm('Are you sure you want to set this certificate as pending?')">Set Pending</a>
                        </td>
                        <td><?php echo $certificate['created_at']; ?></td>
                        <td><?php echo $certificate['district']; ?></td>
                        <td><?php echo $certificate['divisional_secretariat']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>

        <!-- Character Certificates Table -->
        <h2>Character Certificates</h2>
        <div class="table-container">
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC</th>
                    <th>Permanent Address</th>
                    <th>Current Address</th>
                    <th>Contact</th>
                    <th>Purpose</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Created_at</th>
                    <th>District</th>
                    <th>Divisional_secretariat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($character_certificates as $certificate) { ?>
                    <tr>
                        <td><?php echo $certificate['id']; ?></td>
                        <td><?php echo $certificate['username']; ?></td>
                        <td><?php echo $certificate['full_name']; ?></td>
                        <td><?php echo $certificate['dob']; ?></td>
                        <td><?php echo $certificate['nic']; ?></td>
                        <td><?php echo $certificate['permanent_address']; ?></td>
                        <td><?php echo $certificate['current_address']; ?></td>
                        <td><?php echo $certificate['contact']; ?></td>
                        <td><?php echo $certificate['purpose']; ?></td>
                        <td>
                           <?php
                            // Decode the JSON to get the document names
                            $documents = json_decode($certificate['proof_document'], true);

                            // Custom labels for each uploaded document (based on order)
                            $labels = ["ID", "Utility Bill", "Birth Certificate"];

                            if (!empty($documents)) {
                                foreach ($documents as $index => $doc) {
                                    // Use label if available, else fallback to 'Document X'
                                    $label = isset($labels[$index]) ? $labels[$index] : "Document " . ($index + 1);

                                    echo '<a href="uploads/character_certificates/' . htmlspecialchars($doc) . '" target="_blank">' . $label . '</a><br>';
                                }
                            } else {
                                echo "No document uploaded";
                            }
                        ?>

                        </td>
                        <td><?php echo $certificate['status']; ?></td>
                        <td>
                            <a href="user_application.php?action=approve&id=<?php echo $certificate['id']; ?>&table=character_certificates" onclick="return confirm('Are you sure you want to approve this certificate?')">Approve</a> |
                            <a href="user_application.php?action=reject&id=<?php echo $certificate['id']; ?>&table=character_certificates" onclick="return confirm('Are you sure you want to reject this certificate?')">Reject</a> |
                            <a href="user_application.php?action=pending&id=<?php echo $certificate['id']; ?>&table=character_certificates" onclick="return confirm('Are you sure you want to set this certificate as pending?')">Set Pending</a>
                        </td>

                        <td><?php echo $certificate['created_at']; ?></td>
                        <td><?php echo $certificate['district']; ?></td>
                        <td><?php echo $certificate['divisional_secretariat']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>


        <!-- Income Certificates Table -->
        <h2>Income Certificates</h2>
        <div class="table-container">
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC</th>
                    <th>Income</th>
                    <th>Contact</th>
                    <th>Purpose</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Created_at</th>
                    <th>District</th>
                    <th>Divisional_secretariat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($income_certificates as $certificate) { ?>
                    <tr>
                        <td><?php echo $certificate['id']; ?></td>
                        <td><?php echo $certificate['username']; ?></td>
                        <td><?php echo $certificate['full_name']; ?></td>
                        <td><?php echo $certificate['dob']; ?></td>
                        <td><?php echo $certificate['nic']; ?></td>
                        <td><?php echo $certificate['income']; ?></td>
                        <td><?php echo $certificate['contact']; ?></td>
                        <td><?php echo $certificate['purpose']; ?></td>
                        <td>
                            <?php
                        // Decode the JSON to get the document names
                        $documents = json_decode($certificate['proof_document']);

                        // Custom labels for each uploaded document (based on order)
                        $labels = ["Income Report", "NIC", "Tax Returns"];

                        if (!empty($documents)) {
                            foreach ($documents as $index => $doc) {
                                // Use the label if available, else fallback to 'Document X'
                                $label = isset($labels[$index]) ? $labels[$index] : "Document " . ($index + 1);

                                echo '<a href="uploads/income_certificates/' . htmlspecialchars($doc) . '" target="_blank">' . $label . '</a><br>';
                            }
                        } else {
                            echo "No document uploaded";
                        }
                            ?>


                        </td>
                        <td><?php echo $certificate['status']; ?></td>
                        <td>
                            <a href="user_application.php?action=approve&id=<?php echo $certificate['id']; ?>&table=income_certificates" onclick="return confirm('Are you sure you want to approve this certificate?')">Approve</a> |
                            <a href="user_application.php?action=reject&id=<?php echo $certificate['id']; ?>&table=income_certificates" onclick="return confirm('Are you sure you want to reject this certificate?')">Reject</a> |
                            <a href="user_application.php?action=pending&id=<?php echo $certificate['id']; ?>&table=income_certificates" onclick="return confirm('Are you sure you want to set this certificate as pending?')">Set Pending</a>
                        </td>
                        <td><?php echo $certificate['created_at']; ?></td>
                        <td><?php echo $certificate['district']; ?></td>
                        <td><?php echo $certificate['divisional_secretariat']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>

        <!-- Land Use Certificates Table -->
        <h2>Land Use Certificates</h2>
        <div class="table-container">
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC</th>
                    <th>Address</th>
                    <th>Purpose</th>
                    <th>Contact</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Created_at</th>
                    <th>District</th>
                    <th>Divisional_secretariat</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($land_use_certificates as $certificate) { ?>
                    <tr>
                        <td><?php echo $certificate['id']; ?></td>
                        <td><?php echo $certificate['username']; ?></td>
                        <td><?php echo $certificate['full_name']; ?></td>
                        <td><?php echo $certificate['dob']; ?></td>
                        <td><?php echo $certificate['nic']; ?></td>
                        <td><?php echo $certificate['land_location']; ?></td>
                        <td><?php echo $certificate['purpose']; ?></td>
                        <td><?php echo $certificate['contact']; ?></td>
                        <td>
                            <?php
                            // Decode the JSON to get the document names
                            $documents = json_decode($certificate['proof_document']);

                            // Custom labels for each uploaded document (based on order)
                            $labels = ["Land Deed", "Electricity Bill", "NIC Photo"];

                            if (!empty($documents)) {
                                foreach ($documents as $index => $doc) {
                                    // Use the label if available, else fallback to 'Document X'
                                    $label = isset($labels[$index]) ? $labels[$index] : "Document " . ($index + 1);

                                    echo '<a href="uploads/land_use_certificates/' . htmlspecialchars($doc) . '" target="_blank">' . $label . '</a><br>';
                                }
                            } else {
                                echo "No document uploaded";
                            }
                            ?>

                        </td>
                        <td><?php echo $certificate['status']; ?></td>
                        <td>
                            <a href="user_application.php?action=approve&id=<?php echo $certificate['id']; ?>&table=land_use_certificates" onclick="return confirm('Are you sure you want to approve this certificate?')">Approve</a> |
                            <a href="user_application.php?action=reject&id=<?php echo $certificate['id']; ?>&table=land_use_certificates" onclick="return confirm('Are you sure you want to reject this certificate?')">Reject</a> |
                            <a href="user_application.php?action=pending&id=<?php echo $certificate['id']; ?>&table=land_use_certificates" onclick="return confirm('Are you sure you want to set this certificate as pending?')">Set Pending</a>
                        </td>
                        <td><?php echo $certificate['created_at']; ?></td>
                        <td><?php echo $certificate['district']; ?></td>
                        <td><?php echo $certificate['divisional_secretariat']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>

        <!-- Tree Cutting Certificates Table -->
        <h2>Tree Cutting Certificates</h2>
        <div class="table-container">
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>Tree Name</th>
                    <th>Reason</th>
                    <th>Contact</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Created_at</th>
                    <th>District</th>
                    <th>Divisional_secretariat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tree_cutting_certificates as $certificate) { ?>
                    <tr>
                        <td><?php echo $certificate['id']; ?></td>
                        <td><?php echo $certificate['username']; ?></td>
                        <td><?php echo $certificate['full_name']; ?></td>
                        <td><?php echo $certificate['dob']; ?></td>
                        <td><?php echo $certificate['tree_name']; ?></td>
                        <td><?php echo $certificate['reason']; ?></td>
                        <td><?php echo $certificate['contact']; ?></td>
                        <td>
                           <?php
                        // Decode the JSON to get the document names
                        $documents = json_decode($certificate['proof_document']);

                        // Custom labels for each uploaded document (based on order)
                        $labels = ["Tree Photo", "NIC", "Electricity Bill"];

                        if (!empty($documents)) {
                            foreach ($documents as $index => $doc) {
                                // Use the label if available, else fallback to 'Document X'
                                $label = isset($labels[$index]) ? $labels[$index] : "Document " . ($index + 1);

                                echo '<a href="uploads/tree_cutting_certificates/' . htmlspecialchars($doc) . '" target="_blank">' . $label . '</a><br>';
                            }
                        } else {
                            echo "No document uploaded";
                        }
                    ?>

                        </td>
                        <td><?php echo $certificate['status']; ?></td>
                        <td>
                            <a href="user_application.php?action=approve&id=<?php echo $certificate['id']; ?>&table=tree_cutting_certificates" onclick="return confirm('Are you sure you want to approve this certificate?')">Approve</a> |
                            <a href="user_application.php?action=reject&id=<?php echo $certificate['id']; ?>&table=tree_cutting_certificates" onclick="return confirm('Are you sure you want to reject this certificate?')">Reject</a> |
                            <a href="user_application.php?action=pending&id=<?php echo $certificate['id']; ?>&table=tree_cutting_certificates" onclick="return confirm('Are you sure you want to set this certificate as pending?')">Set Pending</a>
                        </td>
                        <td><?php echo $certificate['created_at']; ?></td>
                        <td><?php echo $certificate['district']; ?></td>
                        <td><?php echo $certificate['divisional_secretariat']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>


        <!-- Death Registrations Table -->
        <h2>Death Registrations</h2>
        <div class="table-container">
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Deceased Name</th>
                    <th>Deceased Age</th>
                    <th>Deceased Address</th>
                    <th>Death Date</th>
                    <th>Contact Person Name</th>
                    <th>Contact Person Number</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Created_at</th>
                    <th>District</th>
                    <th>Divisional_secretariat</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($death_registrations as $registration) { ?>
                    <tr>
                        <td><?php echo $registration['id']; ?></td>
                        <td><?php echo $registration['username']; ?></td>
                        <td><?php echo $registration['deceased_name']; ?></td>
                        <td><?php echo $registration['deceased_age']; ?></td>
                        <td><?php echo $registration['deceased_address']; ?></td>
                        <td><?php echo $registration['death_date']; ?></td>
                        <td><?php echo $registration['contact_person_name']; ?></td>
                        <td><?php echo $registration['contact_person_number']; ?></td>
                        <td>
                            <?php
                        // Decode the JSON to get the document names
                        $documents = json_decode($registration['proof_document']);

                        if (!empty($documents)) {
                            // Assuming only one document, display the label "Death Certificate"
                            echo '<a href="uploads/death_registrations/' . htmlspecialchars($documents[0]) . '" target="_blank">Death Certificate</a><br>';
                        } else {
                            echo "No document uploaded";
                        }
                        ?>

                        </td>
                        <td><?php echo $registration['status']; ?></td>
                        <td><?php echo $registration['created_at']; ?></td>
                        <td><?php echo $registration['district']; ?></td>
                        <td><?php echo $registration['divisional_secretariat']; ?></td>
                        <td>
                            <a href="user_application.php?action=approve&id=<?php echo $registration['id']; ?>&table=death_registrations" onclick="return confirm('Are you sure you want to approve this registration?')">Approve</a> |
                            <a href="user_application.php?action=reject&id=<?php echo $registration['id']; ?>&table=death_registrations" onclick="return confirm('Are you sure you want to reject this registration?')">Reject</a> |
                            <a href="user_application.php?action=pending&id=<?php echo $registration['id']; ?>&table=death_registrations" onclick="return confirm('Are you sure you want to set this registration as pending?')">Set Pending</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>

        <!-- National ID Requests Table -->
        <h2>National ID Requests</h2>
        <div class="table-container">
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC Number</th>
                    <th>Phone_number</th>
                    <th>Address</th>
                    <th>Apply new nic</th>
                    <th>Replace_lost_nic</th>
                    <th>Proof document</th>
                    <th>Status</th>
                    <th>Created_at</th>
                    <th>Province</th>
                    <th>District</th>
                    <th>Divisional</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($national_id_requests as $request) { ?>
                    <tr>
                        <td><?php echo $request['id']; ?></td>
                        <td><?php echo $request['full_name']; ?></td>
                        <td><?php echo $request['dob']; ?></td>
                        <td><?php echo $request['nic_number']; ?></td>
                        <td><?php echo $request['phone_number']; ?></td>
                        <td><?php echo $request['address']; ?></td>
                        <td><?php echo $request['applying_for_new_nic']; ?></td>
                        <td><?php echo $request['replacing_lost_nic']; ?></td>
                        <td>
                            <?php
                            // Assuming these are individual file names, not JSON arrays for national_id_requests
                            if (isset($request['birth_certificate']) && !empty($request['birth_certificate'])) {
                                echo '<a href="' . htmlspecialchars($request['birth_certificate']) . '" target="_blank">Birth Certificate</a> <br>';
                            } else {
                                echo 'No birth certificate uploaded<br>';
                            }
                            if (isset($request['photo']) && !empty($request['photo'])) {
                                echo '<a href="' . htmlspecialchars($request['photo']) . '" target="_blank">Photo</a> <br>';
                            } else {
                                echo 'No photo uploaded<br>';
                            }
                            if (isset($request['policy_report']) && !empty($request['policy_report'])) {
                                echo '<a href="' . htmlspecialchars($request['policy_report']) . '" target="_blank">Police Report</a> <br>';
                            } else {
                                echo 'No police report uploaded<br>';
                            }
                            ?>
                        </td>
                        <td><?php echo $request['status']; ?></td>
                        <td><?php echo $request['created_at']; ?></td>
                        <td><?php echo $request['province']; ?></td>
                        <td><?php echo $request['district']; ?></td>
                        <td><?php echo $request['divisional_secretariat']; ?></td>
                        <td>
                            <a href="user_application.php?action=approve&id=<?php echo $request['id']; ?>&table=national_id_requests" onclick="return confirm('Are you sure you want to approve this request?')">Approve</a> |
                            <a href="user_application.php?action=reject&id=<?php echo $request['id']; ?>&table=national_id_requests" onclick="return confirm('Are you sure you want to reject this request?')">Reject</a> |
                            <a href="user_application.php?action=pending&id=<?php echo $request['id']; ?>&table=national_id_requests" onclick="return confirm('Are you sure you want to set this request as pending?')">Set Pending</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>



        <!-- Logout Section -->
        <div class="logout-section">
            <a href="logout.php"><button>Logout</button></a>
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
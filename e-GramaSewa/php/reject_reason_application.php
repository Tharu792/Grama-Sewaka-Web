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

// Get Divisional Secretariat
$divisional_secretariat = $grama_sewaka['divisional_secretariat'];

// Fetch all pending and rejected applications for the Divisional Secretariat
$stmt_pending_apps_residence = $pdo->prepare("SELECT * FROM residence_certificates WHERE status IN ('Pending', 'Rejected') AND divisional_secretariat = :divisional_secretariat");
$stmt_pending_apps_residence->bindParam(":divisional_secretariat", $divisional_secretariat);
$stmt_pending_apps_residence->execute();
$pending_residence_applications = $stmt_pending_apps_residence->fetchAll(PDO::FETCH_ASSOC);

$stmt_pending_apps_character = $pdo->prepare("SELECT * FROM character_certificates WHERE status IN ('Pending', 'Rejected') AND divisional_secretariat = :divisional_secretariat");
$stmt_pending_apps_character->bindParam(":divisional_secretariat", $divisional_secretariat);
$stmt_pending_apps_character->execute();
$pending_character_applications = $stmt_pending_apps_character->fetchAll(PDO::FETCH_ASSOC);

$stmt_pending_apps_death = $pdo->prepare("SELECT * FROM death_registrations WHERE status IN ('Pending', 'Rejected') AND divisional_secretariat = :divisional_secretariat");
$stmt_pending_apps_death->bindParam(":divisional_secretariat", $divisional_secretariat);
$stmt_pending_apps_death->execute();
$pending_death_applications = $stmt_pending_apps_death->fetchAll(PDO::FETCH_ASSOC);

$stmt_pending_apps_income = $pdo->prepare("SELECT * FROM income_certificates WHERE status IN ('Pending', 'Rejected') AND divisional_secretariat = :divisional_secretariat");
$stmt_pending_apps_income->bindParam(":divisional_secretariat", $divisional_secretariat);
$stmt_pending_apps_income->execute();
$pending_income_applications = $stmt_pending_apps_income->fetchAll(PDO::FETCH_ASSOC);

// Fetch applications for new tables
$stmt_pending_apps_land_use = $pdo->prepare("SELECT * FROM land_use_certificates WHERE status IN ('Pending', 'Rejected') AND divisional_secretariat = :divisional_secretariat");
$stmt_pending_apps_land_use->bindParam(":divisional_secretariat", $divisional_secretariat);
$stmt_pending_apps_land_use->execute();
$pending_land_use_applications = $stmt_pending_apps_land_use->fetchAll(PDO::FETCH_ASSOC);

$stmt_pending_apps_tree_cutting = $pdo->prepare("SELECT * FROM tree_cutting_certificates WHERE status IN ('Pending', 'Rejected') AND divisional_secretariat = :divisional_secretariat");
$stmt_pending_apps_tree_cutting->bindParam(":divisional_secretariat", $divisional_secretariat);
$stmt_pending_apps_tree_cutting->execute();
$pending_tree_cutting_applications = $stmt_pending_apps_tree_cutting->fetchAll(PDO::FETCH_ASSOC);

$stmt_pending_apps_national_id = $pdo->prepare("SELECT * FROM national_id_requests WHERE status IN ('Pending', 'Rejected') AND divisional_secretariat = :divisional_secretariat");
$stmt_pending_apps_national_id->bindParam(":divisional_secretariat", $divisional_secretariat);
$stmt_pending_apps_national_id->execute();
$pending_national_id_applications = $stmt_pending_apps_national_id->fetchAll(PDO::FETCH_ASSOC);

// Handle rejection comment update
if (isset($_POST['rejection_comment'], $_POST['application_id'], $_POST['certificate_type'])) {
    $rejection_comment = $_POST['rejection_comment']; // Fetch the rejection comment
    $application_id = $_POST['application_id'];
    $certificate_type = $_POST['certificate_type']; // Identify which certificate table to update

    // Update the rejection comment in the database
    if ($certificate_type == 'residence') {
        $stmt_update = $pdo->prepare("UPDATE residence_certificates SET rejection_comment = :rejection_comment WHERE id = :id");
    } elseif ($certificate_type == 'character') {
        $stmt_update = $pdo->prepare("UPDATE character_certificates SET rejection_comment = :rejection_comment WHERE id = :id");
    } elseif ($certificate_type == 'death') {
        $stmt_update = $pdo->prepare("UPDATE death_registrations SET rejection_comment = :rejection_comment WHERE id = :id");
    } elseif ($certificate_type == 'income') {
        $stmt_update = $pdo->prepare("UPDATE income_certificates SET rejection_comment = :rejection_comment WHERE id = :id");
    } elseif ($certificate_type == 'land_use') {
        $stmt_update = $pdo->prepare("UPDATE land_use_certificates SET rejection_comment = :rejection_comment WHERE id = :id");
    } elseif ($certificate_type == 'tree_cutting') {
        $stmt_update = $pdo->prepare("UPDATE tree_cutting_certificates SET rejection_comment = :rejection_comment WHERE id = :id");
    } else {
        $stmt_update = $pdo->prepare("UPDATE national_id_requests SET rejection_comment = :rejection_comment WHERE id = :id");
    }

    $stmt_update->bindParam(':rejection_comment', $rejection_comment); // Bind the comment to the query
    $stmt_update->bindParam(':id', $application_id);
    $stmt_update->execute();

    // Redirect after updating the comment
    header("Location: reject_reason_application.php?success=1");
    exit();
}

$success_message = isset($_GET['success']) ? "The application comment has been updated successfully." : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reject Application - Grama Sewaka</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
    :root {
        --primary: rgba(37, 99, 235, 0.9);
        --secondary: rgba(16, 185, 129, 0.9);
        --card: rgba(255, 255, 255, 0.95);
        --text: #1e293b;
        --text-light: #64748b;
    }

    body {
        background-image: 
                
                url('../images/image1.jpg');
        background-size: cover; /* make background fit screen */
        color: white;
        line-height: 1.6;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
        margin: 0;
        padding: 0;
    }

    header {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        padding: 15px 5%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        position: sticky;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(20px);
        border-bottom: 4px solid rgba(255, 255, 255, 0.9);
    }

    .logo img {
        height: 50px;
        filter: brightness(0) invert(1);
    }

    nav ul {
        display: flex;
        list-style: none;
        gap: 25px;
    }

    nav a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        padding: 8px 15px;
        border-radius: 30px;
        transition: all 0.3s ease;
    }

    nav a:hover {
        background: rgba(255, 255, 255, 0.9);
        color: var(--primary);
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.5rem;
        position: relative;
        z-index: 10;
    }

    .dashboard-container h1 {
        font-size: 2.5rem;
        margin-bottom: 20px;
        font-weight: 800;
        text-align: center;
        color: white;
    }

    .dashboard-container h2 {
        font-size: 1.8rem;
        color: white;
        text-align: center;
        margin: 30px 0 20px;
        font-weight: 600;
    }

    .success-message {
        background: var(--card);
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .success-message p {
        color: var(--secondary);
        font-weight: 600;
        margin: 0;
        font-size: 1.1rem;
    }

    .profile-card {
        background: var(--card);
        padding: 30px;
        border-radius: 20px;
        margin-bottom: 30px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .profile-card h2 {
        color: var(--text);
        margin-bottom: 20px;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .profile-card p {
        color: var(--text-light);
        margin-bottom: 10px;
        background: rgba(16, 185, 129, 0.05);
        padding: 12px;
        border-radius: 10px;
        border-left: 4px solid var(--secondary);
        font-size: 1rem;
    }

    .profile-card strong {
        color: var(--primary);
        font-weight: 600;
    }

    /* Table Styling */
    table {
        border-collapse: collapse;
        width: 100%;
        max-width: 95%;
        margin: 0 auto 30px auto;
        background: var(--card);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        table-layout: auto;
    }

    table th, table td {
        border: 1px solid rgba(0, 0, 0, 0.1);
        padding: 10px 12px;
        text-align: center;
        font-size: 0.9rem;
        color: var(--text);
    }

    thead {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }

    th {
        font-weight: bold;
        text-transform: uppercase;
    }

    tbody tr:nth-child(even) {
        background: rgba(0, 0, 0, 0.03);
    }

    tbody tr:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    td a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    td a:hover {
        text-decoration: underline;
    }

    textarea {
        width: 100%;
        max-width: 100%;
        padding: 8px;
        border: 2px solid var(--secondary);
        border-radius: 8px;
        font-size: 0.9rem;
        background: white;
        color: var(--text);
        resize: vertical;
        min-height: 50px;
    }

    input[type="submit"] {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        transition: 0.3s ease;
    }

    input[type="submit"]:hover {
        background: linear-gradient(135deg, var(--secondary), var(--primary));
    }

    @media (max-width: 768px) {
        table {
            display: block;
            overflow-x: auto;
            max-width: 100%;
        }
        th, td {
            font-size: 0.8rem;
            white-space: nowrap;
        }
    }
</style>

</head>
<body>
    <header>
        <div class="logo">
            <img src="../images/LOGO.jpg" alt="e-GramaSewa Logo">
        </div>
        <nav>
            <ul>
                <li><a href="grama_sewaka_dashboard.php">Home</a></li>
                <li><a href="user_application.php">User Application</a></li>
                <li><a href="../pages/Service.html">Services</a></li>
                <li><a href="../pages/contact.html">Contact</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard-container">
        <h1>Reject and Pending Applications</h1>

        <!-- Success Message (if any) -->
        <?php if ($success_message): ?>
            <div class="success-message">
                <p><?php echo $success_message; ?></p>
            </div>
        <?php endif; ?>

        <!-- Display Grama Sewaka's Details -->
        <div class="profile-card">
            <h2>Your Profile</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($grama_sewaka['name']); ?></p>
            <p><strong>Position:</strong> <?php echo htmlspecialchars($grama_sewaka['position']); ?></p>
            <p><strong>District:</strong> <?php echo htmlspecialchars($grama_sewaka['district']); ?></p>
            <p><strong>Divisional Secretariat:</strong> <?php echo htmlspecialchars($grama_sewaka['divisional_secretariat']); ?></p>
        </div>

        <!-- Pending Applications Tables -->

        <!-- Residence Certificate Applications -->
        <h2> Residence Applications for Divisional Secretariat: <?php echo htmlspecialchars($divisional_secretariat); ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC</th>
                    <th>Permanent Address</th>
                    <th>Current Address</th>
                    <th>Contact</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Rejection Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_residence_applications as $application): ?>
                    <tr>
                        <td><?php echo $application['id']; ?></td>
                        <td><?php echo $application['full_name']; ?></td>
                        <td><?php echo $application['dob']; ?></td>
                        <td><?php echo $application['nic']; ?></td>
                        <td><?php echo $application['permanent_address']; ?></td>
                        <td><?php echo $application['current_address']; ?></td>
                        <td><?php echo $application['contact']; ?></td>
                        <td>
                            <?php 
                            $documents = json_decode($application['proof_document']);
                            if (!empty($documents)) {
                                foreach ($documents as $doc) {
                                    echo '<a href="uploads/residence_certificates/' . htmlspecialchars($doc) . '" target="_blank">View Document</a><br>';
                                }
                            } else {
                                echo 'No document uploaded';
                            }
                            if (!empty($documents)) {
                                foreach ($documents as $doc) {
                                    echo '<a href="uploads/birth_certificates/' . htmlspecialchars($doc) . '" target="_blank">View Document</a><br>';
                                }
                            } else {
                                echo 'No document uploaded';
                            }
                            ?>
                        </td>
                        <td><?php echo $application['status']; ?></td>
                        <td><?php echo $application['rejection_comment'] ?: 'No comment'; ?></td>
                        <td>
                            <form method="POST" action="reject_reason_application.php">
                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                <input type="hidden" name="certificate_type" value="residence">
                                <textarea name="rejection_comment" rows="3" cols="40" placeholder="Enter rejection comment (optional)"><?php echo htmlspecialchars($application['rejection_comment']); ?></textarea><br><br>
                                <input type="submit" value="Save Comment">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Character Certificate Applications -->
        <h2>Character Applications for Divisional Secretariat: <?php echo htmlspecialchars($divisional_secretariat); ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC</th>
                    <th>Permanent Address</th>
                    <th>Current Address</th>
                    <th>Contact</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Rejection Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_character_applications as $application): ?>
                    <tr>
                        <td><?php echo $application['id']; ?></td>
                        <td><?php echo $application['full_name']; ?></td>
                        <td><?php echo $application['dob']; ?></td>
                        <td><?php echo $application['nic']; ?></td>
                        <td><?php echo $application['permanent_address']; ?></td>
                        <td><?php echo $application['current_address']; ?></td>
                        <td><?php echo $application['contact']; ?></td>
                        <td>
                            <?php 
                            $documents = json_decode($application['proof_document']);
                            if (!empty($documents)) {
                                foreach ($documents as $doc) {
                                    echo '<a href="uploads/character_certificates/' . htmlspecialchars($doc) . '" target="_blank">View Document</a><br>';
                                }
                            } else {
                                echo 'No document uploaded';
                            }
                            ?>
                        </td>
                        <td><?php echo $application['status']; ?></td>
                        <td><?php echo $application['rejection_comment'] ?: 'No comment'; ?></td>
                        <td>
                            <form method="POST" action="reject_reason_application.php">
                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                <input type="hidden" name="certificate_type" value="character">
                                <textarea name="rejection_comment" rows="3" cols="40" placeholder="Enter rejection comment (optional)"><?php echo htmlspecialchars($application['rejection_comment']); ?></textarea><br><br>
                                <input type="submit" value="Save Comment">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add similar sections for Death, Income, Land Use, Tree Cutting, and National ID -->

        <!-- Death Certificate Applications -->
        <h2>Death Applications for Divisional Secretariat: <?php echo htmlspecialchars($divisional_secretariat); ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Deceased Name</th>
                    <th>Deceased Age</th>
                    <th>Contact Person Name</th>
                    <th>Contact Person Number</th>
                    <th>Death Date</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Rejection Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_death_applications as $application): ?>
                    <tr>
                        <td><?php echo $application['id']; ?></td>
                        <td><?php echo $application['deceased_name']; ?></td>
                        <td><?php echo $application['deceased_age']; ?></td>
                        <td><?php echo $application['contact_person_name']; ?></td>
                        <td><?php echo $application['contact_person_number']; ?></td>
                        <td><?php echo $application['death_date']; ?></td>
                        <td>
                            <?php 
                            $documents = json_decode($application['proof_document']);
                            if (!empty($documents)) {
                                foreach ($documents as $doc) {
                                    echo '<a href="uploads/death_registrations/' . htmlspecialchars($doc) . '" target="_blank">View Document</a><br>';
                                }
                            } else {
                                echo 'No document uploaded';
                            }
                            ?>
                        </td>
                        <td><?php echo $application['status']; ?></td>
                        <td><?php echo $application['rejection_comment'] ?: 'No comment'; ?></td>
                        <td>
                            <form method="POST" action="reject_reason_application.php">
                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                <input type="hidden" name="certificate_type" value="death">
                                <textarea name="rejection_comment" rows="3" cols="40" placeholder="Enter rejection comment (optional)"><?php echo htmlspecialchars($application['rejection_comment']); ?></textarea><br><br>
                                <input type="submit" value="Save Comment">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Income Certificate Applications -->
        <h2> Income Applications for Divisional Secretariat: <?php echo htmlspecialchars($divisional_secretariat); ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC</th>
                    <th>Income</th>
                    <th>Contact</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Rejection Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_income_applications as $application): ?>
                    <tr>
                        <td><?php echo $application['id']; ?></td>
                        <td><?php echo $application['full_name']; ?></td>
                        <td><?php echo $application['dob']; ?></td>
                        <td><?php echo $application['nic']; ?></td>
                        <td><?php echo $application['income']; ?></td>
                        <td><?php echo $application['contact']; ?></td>
                        <td>
                            <?php 
                            $documents = json_decode($application['proof_document']);
                            if (!empty($documents)) {
                                foreach ($documents as $doc) {
                                    echo '<a href="uploads/income_certificates/' . htmlspecialchars($doc) . '" target="_blank">View Document</a><br>';
                                }
                            } else {
                                echo 'No document uploaded';
                            }
                            ?>
                        </td>
                        <td><?php echo $application['status']; ?></td>
                        <td><?php echo $application['rejection_comment'] ?: 'No comment'; ?></td>
                        <td>
                            <form method="POST" action="reject_reason_application.php">
                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                <input type="hidden" name="certificate_type" value="income">
                                <textarea name="rejection_comment" rows="3" cols="40" placeholder="Enter rejection comment (optional)"><?php echo htmlspecialchars($application['rejection_comment']); ?></textarea><br><br>
                                <input type="submit" value="Save Comment">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Land Use Certificate Applications -->
        <h2>Land Use Applications for Divisional Secretariat: <?php echo htmlspecialchars($divisional_secretariat); ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC</th>
                    <th>Land Location</th>
                    <th>Purpose</th>
                    <th>Contact</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Rejection Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_land_use_applications as $application): ?>
                    <tr>
                        <td><?php echo $application['id']; ?></td>
                        <td><?php echo $application['full_name']; ?></td>
                        <td><?php echo $application['dob']; ?></td>
                        <td><?php echo $application['nic']; ?></td>
                        <td><?php echo $application['land_location']; ?></td>
                        <td><?php echo $application['purpose']; ?></td>
                        <td><?php echo $application['contact']; ?></td>
                        <td>
                            <?php 
                            $documents = json_decode($application['proof_document']);
                            if (!empty($documents)) {
                                foreach ($documents as $doc) {
                                    echo '<a href="uploads/' . htmlspecialchars($doc) . '" target="_blank">View Document</a><br>';
                                }
                            } else {
                                echo 'No document uploaded';
                            }
                            ?>
                        </td>
                        <td><?php echo $application['status']; ?></td>
                        <td><?php echo $application['rejection_comment'] ?: 'No comment'; ?></td>
                        <td>
                            <form method="POST" action="reject_reason_application.php">
                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                <input type="hidden" name="certificate_type" value="land_use">
                                <textarea name="rejection_comment" rows="3" cols="40" placeholder="Enter rejection comment (optional)"><?php echo htmlspecialchars($application['rejection_comment']); ?></textarea><br><br>
                                <input type="submit" value="Save Comment">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tree Cutting Certificate Applications -->
        <h2>Tree Cutting Applications for Divisional Secretariat: <?php echo htmlspecialchars($divisional_secretariat); ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC</th>
                    <th>Tree Name</th>
                    <th>Reason</th>
                    <th>Contact</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Rejection Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_tree_cutting_applications as $application): ?>
                    <tr>
                        <td><?php echo $application['id']; ?></td>
                        <td><?php echo $application['full_name']; ?></td>
                        <td><?php echo $application['dob']; ?></td>
                        <td><?php echo $application['nic']; ?></td>
                        <td><?php echo $application['tree_name']; ?></td>
                        <td><?php echo $application['reason']; ?></td>
                        <td><?php echo $application['contact']; ?></td>
                        <td>
                            <?php 
                            $documents = json_decode($application['proof_document']);
                            if (!empty($documents)) {
                                foreach ($documents as $doc) {
                                    echo '<a href="uploads/tree_cutting_certificates/' . htmlspecialchars($doc) . '" target="_blank">View Document</a><br>';
                                }
                            } else {
                                echo 'No document uploaded';
                            }
                            ?>
                        </td>
                        <td><?php echo $application['status']; ?></td>
                        <td><?php echo $application['rejection_comment'] ?: 'No comment'; ?></td>
                        <td>
                            <form method="POST" action="reject_reason_application.php">
                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                <input type="hidden" name="certificate_type" value="tree_cutting">
                                <textarea name="rejection_comment" rows="3" cols="40" placeholder="Enter rejection comment (optional)"><?php echo htmlspecialchars($application['rejection_comment']); ?></textarea><br><br>
                                <input type="submit" value="Save Comment">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- National ID Certificate Applications -->
        <h2>National ID Applications for Divisional Secretariat: <?php echo htmlspecialchars($divisional_secretariat); ?></h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Full Name</th>
                    <th>DOB</th>
                    <th>NIC Number</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Proof Document</th>
                    <th>Status</th>
                    <th>Rejection Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($pending_national_id_applications as $application): ?>
        <tr>
            <td><?php echo $application['id']; ?></td>
            <td><?php echo $application['full_name']; ?></td>
            <td><?php echo $application['dob']; ?></td>
            <td><?php echo $application['nic_number']; ?></td>
            <td><?php echo $application['phone_number']; ?></td>
            <td><?php echo $application['address']; ?></td>
            <td>
    <?php 
    // Check for each document type and display appropriately
    if (isset($application['birth_certificate']) && !empty($application['birth_certificate'])) {
        echo '<a href="' . htmlspecialchars($application['birth_certificate']) . '" target="_blank">View Birth Certificate</a><br>';
    } else {
        echo 'No birth certificate uploaded<br>';
    }

    if (isset($application['photo']) && !empty($application['photo'])) {
        echo '<a href="' . htmlspecialchars($application['photo']) . '" target="_blank">View Photo</a><br>';
    } else {
        echo 'No photo uploaded<br>';
    }

    if (isset($application['policy_report']) && !empty($application['policy_report'])) {
        echo '<a href="' . htmlspecialchars($application['policy_report']) . '" target="_blank">View Policy Report</a><br>';
    } else {
        echo 'No policy report uploaded<br>';
    }
    ?>
</td>

                    
            <td><?php echo $application['status']; ?></td>
            <td><?php echo $application['rejection_comment'] ?: 'No comment'; ?></td>
            <td>
                <form method="POST" action="reject_reason_application.php">
                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                    <input type="hidden" name="certificate_type" value="national_id">
                    <textarea name="rejection_comment" rows="3" cols="40" placeholder="Enter rejection comment (optional)"><?php echo htmlspecialchars($application['rejection_comment']); ?></textarea><br><br>
                    <input type="submit" value="Save Comment">
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>


        </table>

    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-section footer-info">
                <h4>E-Gramasevaka</h4>
                <p>Digital government services platform providing efficient and transparent access to various government services and certificates.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 e-GramaSewa. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

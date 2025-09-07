<?php
// Start the session to check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grama_sewa";

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetching data for residence certificates with district filter
$districtFilter = isset($_GET['district']) && $_GET['district'] != '' ? $_GET['district'] : null;
$residenceQuery = "SELECT * FROM residence_certificates";
if ($districtFilter) {
    $residenceQuery .= " WHERE district = :district";
}
$stmt = $pdo->prepare($residenceQuery);
if ($districtFilter) {
    $stmt->bindParam(':district', $districtFilter);
}
$stmt->execute();
$residenceCertificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetching data for character certificates with district filter
$characterQuery = "SELECT * FROM character_certificates";
if ($districtFilter) {
    $characterQuery .= " WHERE district = :district";
}
$stmt = $pdo->prepare($characterQuery);
if ($districtFilter) {
    $stmt->bindParam(':district', $districtFilter);
}
$stmt->execute();
$characterCertificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetching data for income certificates with district filter
$incomeQuery = "SELECT * FROM income_certificates";
if ($districtFilter) {
    $incomeQuery .= " WHERE district = :district";
}
$stmt = $pdo->prepare($incomeQuery);
if ($districtFilter) {
    $stmt->bindParam(':district', $districtFilter);
}
$stmt->execute();
$incomeCertificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetching data for land use certificates with district filter
$landQuery = "SELECT * FROM land_use_certificates";
if ($districtFilter) {
    $landQuery .= " WHERE district = :district";
}
$stmt = $pdo->prepare($landQuery);
if ($districtFilter) {
    $stmt->bindParam(':district', $districtFilter);
}
$stmt->execute();
$landUseCertificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetching data for tree cutting certificates with district filter
$treeQuery = "SELECT * FROM tree_cutting_certificates";
if ($districtFilter) {
    $treeQuery .= " WHERE district = :district";
}
$stmt = $pdo->prepare($treeQuery);
if ($districtFilter) {
    $stmt->bindParam(':district', $districtFilter);
}
$stmt->execute();
$treeCuttingCertificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetching data for death registrations with district filter
$deathQuery = "SELECT * FROM death_registrations";
if ($districtFilter) {
    $deathQuery .= " WHERE district = :district";
}
$stmt = $pdo->prepare($deathQuery);
if ($districtFilter) {
    $stmt->bindParam(':district', $districtFilter);
}
$stmt->execute();
$deathRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetching data for national id requests with district filter
$nationalIdQuery = "SELECT * FROM national_id_requests";
if ($districtFilter) {
    $nationalIdQuery .= " WHERE district = :district";
}
$stmt = $pdo->prepare($nationalIdQuery);
if ($districtFilter) {
    $stmt->bindParam(':district', $districtFilter);
}
$stmt->execute();
$nationalIdRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Certificates</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to your custom CSS -->
    <style>
   /* ================== Clean Admin Interface ================== */

/* CSS Variables for Color Palette */
:root {
    --blue-primary: #2563eb;
    --teal-primary: #10b981;
    --white: #ffffff;
    --gray-light: #f3f4f6;
    --gray-dark: #1f2937;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body Background */
body {
    background-image: url('../images/image4.jpg');
    background-size: cover;       /* fills entire screen */
    background-position: center;  /* centers the image */
    background-repeat: no-repeat; /* no tiling */
    color: white;
    line-height: 1.6;
    min-height: 100vh;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* Header */
header {
    background: rgba(255, 255, 255, 0.95);
    color: var(--gray-dark);
    padding: 15px 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 2px solid var(--gray-light);
}

header .logo img {
    height: 50px;
}

/* Navigation */
.middle-nav ul {
    display: flex;
    list-style: none;
    gap: 20px;
}

.middle-nav a {
    color: var(--gray-dark);
    text-decoration: none;
    font-weight: 500;
    padding: 8px 15px;
    border-radius: 5px;
}

.middle-nav a:hover {
    background: var(--gray-light);
}

/* Login Button */
.login-btn {
    background: var(--blue-primary);
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
}

.login-btn:hover {
    background: var(--teal-primary);
}

/* Main Content */
.admin-dashboard {
    padding: 30px 5%;
}

/* Page Titles */
h1, h2 {
    font-weight: 600;
    color: var(--gray-dark);
    margin-bottom: 20px;
}

h1 {
    font-size: 2.5rem;
}

h2 {
    font-size: 1.8rem;
}

/* Filter Form */
form {
    background: var(--white);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

form label {
    display: inline-block;
    margin-bottom: 8px;
    color: var(--gray-dark);
    font-weight: 500;
}

form select, form button {
    padding: 10px 15px;
    font-size: 1rem;
    border-radius: 5px;
    border: 1px solid #ccc;
}

form select {
    width: auto;
    margin-right: 10px;
}

form button {
    background: var(--blue-primary);
    color: white;
    border: none;
    cursor: pointer;
}

form button:hover {
    background: var(--teal-primary);
}

/* Separator */
hr {
    border: none;
    height: 2px;
    background: var(--gray-light);
    margin: 20px 0;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    background: var(--white);
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

th {
    background: var(--blue-primary);
    color: white;
    padding: 12px 10px;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
}

td {
    padding: 12px 10px;
    border-bottom: 1px solid #e5e7eb;
    color: var(--gray-dark);
    font-size: 0.9rem;
}

tr:nth-child(even) {
    background-color: var(--gray-light);
}

tr:hover {
    background-color: #d1fae5;
}

/* Table Links */
table a {
    color: var(--blue-primary);
    text-decoration: none;
    padding: 3px 6px;
    border-radius: 3px;
}

table a:hover {
    background: var(--teal-primary);
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    header {
        flex-direction: column;
        align-items: flex-start;
        padding: 15px;
    }
    
    .middle-nav ul {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    h1 { font-size: 2rem; }
    h2 { font-size: 1.5rem; }
    
    form label, form select, form button {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }
    
    table {
        font-size: 0.8rem;
    }
    
    th, td {
        padding: 10px 5px;
    }
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
                <li><a href="admin_dashboard.php">Home</a></li>
                <li><a href="add_grama_sewaka.php">Add Grama Sewaka</a></li>
                <li><a href="admin_view_user_application.php">User Application</a></li>
                <li><a href="admin_view_user.php">View User</a></li>
                <li><a href="report.php">Report</a></li>
            </ul>
        </nav>

        <div class="right-nav">
            <a href="logout.php" class="login-btn">Logout</a>
        </div>
    </header>

       <h1>View User Certificates</h1>

    <!-- District Filter Form -->
    <form method="GET" action="admin_view_user_application.php">
        <label for="district">Select District: </label>
        <select name="district" id="district">
            <option value="">All Districts</option>
            <?php
            // List of all 25 Sri Lankan districts
            $districts = [
                "Colombo", "Gampaha", "Kalutara", "Kandy", "Matale", "Nuwara Eliya", "Galle", "Matara", "Hambantota",
                "Jaffna", "Kilinochchi", "Mannar", "Vavuniya", "Batticaloa", "Ampara", "Trincomalee", "Polonnaruwa",
                "Anuradhapura", "Kurunegala", "Puttalam", "Badulla", "Moneragala", "Ratnapura", "Kegalle", "Mullaitivu", 
                "Mannar"
            ];

            // Loop through districts and create options
            foreach ($districts as $district) {
                $selected = (isset($_GET['district']) && $_GET['district'] == $district) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($district) . "' $selected>" . htmlspecialchars($district) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Filter</button>
    </form>
    <hr> <!-- separator line -->

    <h2>Residence Certificates</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>NIC</th>
                <th>Permanent Address</th>
                <th>Current Address</th>
                <th>Contact</th>
                <th>Purpose</th>
                <th>Documents</th>
                <th>Status</th>
                <th>Created At</th>
                
            </tr>
        </thead>
        <tbody>
            <?php foreach ($residenceCertificates as $certificate): ?>
                <tr>
                    <td><?php echo htmlspecialchars($certificate['id']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['username']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['dob']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['nic']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['permanent_address']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['current_address']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['contact']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['purpose']); ?></td>
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
                    <td><?php echo htmlspecialchars($certificate['status']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['created_at']); ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Character Certificates</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>NIC</th>
                <th>Permanent Address</th>
                <th>Current Address</th>
                <th>Contact</th>
                <th>Purpose</th>
                <th>Documents</th>
                <th>Status</th>
                <th>Created At</th>
                <th>District</th>
                <th>Divisional Secretariat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($characterCertificates as $certificate): ?>
                <tr>
                    <td><?php echo htmlspecialchars($certificate['id']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['username']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['dob']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['nic']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['permanent_address']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['current_address']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['contact']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['purpose']); ?></td>
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
                    <td><?php echo htmlspecialchars($certificate['status']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['district']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['divisional_secretariat']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Income Certificates</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>NIC</th>
                <th>Income</th>
                <th>Contact</th>
                <th>Purpose</th>
                <th>Documents</th>
                <th>Status</th>
                <th>Created At</th>
                <th>District</th>
                <th>Divisional Secretariat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($incomeCertificates as $certificate): ?>
                <tr>
                    <td><?php echo htmlspecialchars($certificate['id']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['username']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['dob']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['nic']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['income']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['contact']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['purpose']); ?></td>
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
                    <td><?php echo htmlspecialchars($certificate['status']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['district']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['divisional_secretariat']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Land Use Certificates</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>NIC</th>
                <th>Land Location</th>
                <th>Purpose</th>
                <th>Contact</th>
                <th>Documents</th>
                <th>Status</th>
                <th>Created At</th>
                <th>District</th>
                <th>Divisional Secretariat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($landUseCertificates as $certificate): ?>
                <tr>
                    <td><?php echo htmlspecialchars($certificate['id']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['username']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['dob']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['nic']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['land_location']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['purpose']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['contact']); ?></td>
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
                    <td><?php echo htmlspecialchars($certificate['status']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['district']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['divisional_secretariat']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Tree Cutting Certificates</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>NIC</th>
                <th>Tree Name</th>
                <th>Reason</th>
                <th>Contact</th>
                <th>Documents</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>District</th>
                <th>Divisional Secretariat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($treeCuttingCertificates as $certificate): ?>
                <tr>
                    <td><?php echo htmlspecialchars($certificate['id']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['username']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['dob']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['nic']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['tree_name']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['reason']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['contact']); ?></td>
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
                    <td><?php echo htmlspecialchars($certificate['status']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['updated_at']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['district']); ?></td>
                    <td><?php echo htmlspecialchars($certificate['divisional_secretariat']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Death Registrations</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Deceased Name</th>
                <th>Age</th>
                <th>Address</th>
                <th>Contact Person</th>
                <th>Contact Person Number</th>
                <th>Death Date</th>
                <th>Documents</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>District</th>
                <th>Divisional Secretariat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deathRegistrations as $death): ?>
                <tr>
                    <td><?php echo htmlspecialchars($death['id']); ?></td>
                    <td><?php echo htmlspecialchars($death['username']); ?></td>
                    <td><?php echo htmlspecialchars($death['deceased_name']); ?></td>
                    <td><?php echo htmlspecialchars($death['deceased_age']); ?></td>
                    <td><?php echo htmlspecialchars($death['deceased_address']); ?></td>
                    <td><?php echo htmlspecialchars($death['contact_person_name']); ?></td>
                    <td><?php echo htmlspecialchars($death['contact_person_number']); ?></td>
                    <td><?php echo htmlspecialchars($death['death_date']); ?></td>
                    <td>
                       <?php 
                        // Decode the JSON to get the document names
                        $documents = json_decode($death['proof_document']);

                        if (!empty($documents)) {
                        // Assuming only one document, display the label "Death Certificate"
                        echo '<a href="uploads/death_registrations/' . htmlspecialchars($documents[0]) . '" target="_blank">Death Certificate</a><br>';
                        } else {
                         echo "No document uploaded";
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($death['status']); ?></td>
                    <td><?php echo htmlspecialchars($death['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($death['updated_at']); ?></td>
                    <td><?php echo htmlspecialchars($death['district']); ?></td>
                    <td><?php echo htmlspecialchars($death['divisional_secretariat']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>National ID Requests</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>NIC Number</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>Applying for New NIC</th>
                <th>Replacing Lost NIC</th>
                <th>Proof_document</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Province</th>
                <th>District</th>
                <th>Divisional Secretariat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($nationalIdRequests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['id']); ?></td>
                    <td><?php echo htmlspecialchars($request['username']); ?></td>
                    <td><?php echo htmlspecialchars($request['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($request['dob']); ?></td>
                    <td><?php echo htmlspecialchars($request['nic_number']); ?></td>
                    <td><?php echo htmlspecialchars($request['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($request['address']); ?></td>
                    <td><?php echo htmlspecialchars($request['applying_for_new_nic']); ?></td>
                    <td><?php echo htmlspecialchars($request['replacing_lost_nic']); ?></td>

                     <td>
                            <?php 
                            if (isset($request['birth_certificate']) && !empty($request['birth_certificate'])) {
                                echo '<a href="' . $request['birth_certificate'] . '" target="_blank">birth_certificate</a> <br>';
                            } else {
                                echo 'No document uploaded';
                            }
                            if (isset($request['photo']) && !empty($request['photo'])) {
                                echo '<a href="' . $request['photo'] . '" target="_blank">View photo</a> <br>';
                            } else {
                                echo 'No document uploaded';
                            }
                            if (isset($request['policy_report']) && !empty($request['policy_report'])) {
                                echo '<a href="' . $request['policy_report'] . '" target="_blank">policy_report</a> <br>';
                            } else {
                                echo 'No document uploaded';
                            }
                            ?>
                        </td>
                    
                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                    <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($request['updated_at']); ?></td>
                    <td><?php echo htmlspecialchars($request['province']); ?></td>
                    <td><?php echo htmlspecialchars($request['district']); ?></td>
                    <td><?php echo htmlspecialchars($request['divisional_secretariat']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>

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

// Get the certificate type from the URL (passed as a query parameter)
$certificate_type = isset($_GET['certificate']) ? $_GET['certificate'] : '';

// Fetch data from the corresponding table based on certificate type
switch ($certificate_type) {
    case 'residence':
        $stmt = $pdo->prepare("SELECT * FROM residence_certificates WHERE username = :username ORDER BY created_at DESC");
        break;
    case 'character':
        $stmt = $pdo->prepare("SELECT * FROM character_certificates WHERE username = :username ORDER BY created_at DESC");
        break;
    case 'income':
        $stmt = $pdo->prepare("SELECT * FROM income_certificates WHERE username = :username ORDER BY created_at DESC");
        break;
    case 'death':
        $stmt = $pdo->prepare("SELECT * FROM death_registrations WHERE username = :username ORDER BY created_at DESC");
        break;
    case 'land_use':
        $stmt = $pdo->prepare("SELECT * FROM land_use_certificates WHERE username = :username ORDER BY created_at DESC");
        break;
    case 'tree_cutting':
        $stmt = $pdo->prepare("SELECT * FROM tree_cutting_certificates WHERE username = :username ORDER BY created_at DESC");
        break;
    case 'national_id':
        $stmt = $pdo->prepare("SELECT * FROM national_id_requests WHERE username = :username ORDER BY created_at DESC");
        break;
    default:
        echo "Invalid certificate type!";
        exit();
}

// Bind the username parameter and execute the query
$stmt->bindParam(":username", $customer_username);
$stmt->execute();

// Fetch all rows of data
$certificate_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }

        .print-button {
            margin-top: 10px;
            padding: 5px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #45a049;
        }
    </style>

    <script>
        function printRow(rowId) {
            var printContent = document.getElementById(rowId).innerHTML;
            var newWindow = window.open();
            newWindow.document.write('<html><body>');
            newWindow.document.write('<table border="1">' + printContent + '</table>');
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        }
    </script>
</head>
<body>

    <h1>Certificate Data for: <?php echo htmlspecialchars($certificate_type); ?></h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Full Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Rejection Comment</th>
                <th>District</th>
                <th>Divisional Secretariat</th>
                <th>Other Info</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($certificate_data)): ?>
                <tr><td colspan="10">No data found for this certificate.</td></tr>
            <?php else: ?>
                <?php foreach ($certificate_data as $data): ?>
                    <tr id="row-<?php echo $data['id']; ?>">
                        <td><?php echo htmlspecialchars($data['id']); ?></td>
                        <td><?php echo htmlspecialchars($data['username']); ?></td>
                        <td><?php echo htmlspecialchars($data['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($data['status']); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($data['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($data['rejection_comment']); ?></td>
                        <td><?php echo htmlspecialchars($data['district']); ?></td>
                        <td><?php echo htmlspecialchars($data['divisional_secretariat']); ?></td>
                        <td>
                            <!-- Add any other dynamic data fields you want to show here -->
                            <?php echo isset($data['other_field']) ? htmlspecialchars($data['other_field']) : 'N/A'; ?>
                        </td>
                        <td>
                            <button class="print-button" onclick="printRow('row-<?php echo $data['id']; ?>')">Print This Row</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

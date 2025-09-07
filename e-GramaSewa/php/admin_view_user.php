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

// **Fetch User Details**
$stmt_user_details = $pdo->query("SELECT * FROM customers");
$user_details = $stmt_user_details->fetchAll(PDO::FETCH_ASSOC);

// **Handle Delete Request**
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the user from the database
    $stmt_delete = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt_delete->execute([$delete_id]);

    // Redirect to the same page with a success message
    header("Location: admin_view_user.php?message=User+deleted+successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* General Styles for the Admin Dashboard */
        body {
            font-family: Arial, sans-serif;
             background: url('../images/image6.jpg') no-repeat center center fixed;
        background-size: cover;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

/* Section Title */
.user-details h3 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 1.5rem;
    color: #1e3a8a;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Table Container */
.user-details {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    overflow-x: auto;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 700px;
}

th, td {
    padding: 12px 15px;
    text-align: left;
}

th {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #2563eb;
}

td {
    background-color: #f9fafb;
    color: #1f2937;
}

tr:nth-child(even) td {
    background-color: #eff6ff;
}

tr:hover td {
    background-color: #dbeafe;
    transition: background-color 0.3s;
}

/* Action Button */
.delete-btn {
    display: inline-block;
    padding: 6px 12px;
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: transform 0.3s, box-shadow 0.3s;
}

.delete-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Success Message */
.success-message {
    color: #16a34a;
    font-weight: bold;
    margin-bottom: 20px;
    text-align: center;
}

/* Responsive Design */
@media (max-width: 900px) {
    .middle-nav ul {
        flex-direction: column;
        gap: 10px;
    }
    
    table {
        font-size: 14px;
    }
}

@media (max-width: 600px) {
    .admin-dashboard {
        padding: 0 0.5rem;
    }

    th, td {
        padding: 10px;
    }

    .user-details h3 {
        font-size: 1.5rem;
    }
}
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <!-- Navigation Bar -->
        <header>
            <div class="logo">
                <img src="../images/Logo_3.png" alt="e-GramaSewa Logo">
            </div>

            <!-- Middle Navigation Links -->
            <nav class="middle-nav">
                <ul>
                    <li><a href="admin_dashboard.php">Home</a></li>
                    <li><a href="add_grama_sewaka.php">Add Grama Sewaka</a></li>
                    <li><a href="admin_view_user_application.php">View User Application</a></li>
                    <li><a href="admin_view_user.php">View Users</a></li>
                    <li><a href="report.php">Generate Report</a></li>
                    
                </ul>
            </nav>
            <div class="right-nav">
            <a href="logout.php" class="login-btn">Logout</a>
            </div>
        </header>

        <!-- User Details Section -->
        <div class="user-details">
            <h3>User Details</h3>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>District</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($user_details as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['tel_no']); ?></td>
                            <td><?php echo htmlspecialchars($user['district']); ?></td>
                            <td>
                                <a href="admin_view_user.php?delete_id=<?php echo $user['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

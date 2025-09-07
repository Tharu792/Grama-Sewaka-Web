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

// Check if the customer ID is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the customer's current details
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the customer exists
    if (!$customer) {
        echo "Customer not found.";
        exit();
    }
}

// Handle form submission to update customer details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $nic = $_POST['nic'];
    $tel_no = $_POST['tel_no'];
    $email = $_POST['email'];
    $district = $_POST['district'];

    // Prepare the SQL statement to update the customer record
    $stmt_update = $pdo->prepare("UPDATE customers SET name = :name, address = :address, nic = :nic, tel_no = :tel_no, email = :email, district = :district WHERE id = :id");
    $stmt_update->bindParam(':name', $name);
    $stmt_update->bindParam(':address', $address);
    $stmt_update->bindParam(':nic', $nic);
    $stmt_update->bindParam(':tel_no', $tel_no);
    $stmt_update->bindParam(':email', $email);
    $stmt_update->bindParam(':district', $district);
    $stmt_update->bindParam(':id', $id);

    if ($stmt_update->execute()) {
        header('Location: admin_dashboard.php'); // Redirect back to the admin dashboard after updating
        exit();
    } else {
        echo "Error updating customer.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Customer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="update-customer-container">
        <h2>Update Customer</h2>

        <!-- Customer Update Form -->
        <form action="update_customer.php?id=<?php echo $customer['id']; ?>" method="POST">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($customer['address']); ?>" required>

            <label for="nic">NIC</label>
            <input type="text" id="nic" name="nic" value="<?php echo htmlspecialchars($customer['nic']); ?>" required>

            <label for="tel_no">Telephone No</label>
            <input type="text" id="tel_no" name="tel_no" value="<?php echo htmlspecialchars($customer['tel_no']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>

            <label for="district">District</label>
            <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($customer['district']); ?>" required>

            <button type="submit">Update Customer</button>
        </form>
    </div>
</body>
</html>

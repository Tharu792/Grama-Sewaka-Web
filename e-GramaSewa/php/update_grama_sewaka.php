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

// **Add Logic**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_grama_sewaka'])) {
    // Collect form data
    $name = $_POST['name'];
    $position = $_POST['position'];
    $contact = $_POST['contact'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $divisional_secretariat = $_POST['divisional_secretariat'];

    // Check if the username already exists
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM grama_sewaka WHERE username = :username");
    $stmt_check->bindParam(':username', $username);
    $stmt_check->execute();
    $count = $stmt_check->fetchColumn();

    // If the username already exists, show an error message
    if ($count > 0) {
        $error_message = "Username already exists. Please choose a different username.";
    } else {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert Grama Sewaka into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO grama_sewaka (name, position, contact, username, password, province, district, divisional_secretariat) 
                                   VALUES (:name, :position, :contact, :username, :password, :province, :district, :divisional_secretariat)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':position', $position);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':province', $province);
            $stmt->bindParam(':district', $district);
            $stmt->bindParam(':divisional_secretariat', $divisional_secretariat);

            if ($stmt->execute()) {
                $success_message = "Grama Sewaka added successfully!";
            } else {
                $error_message = "Failed to add Grama Sewaka.";
            }
        } catch (PDOException $e) {
            $error_message = "Error occurred: " . $e->getMessage(); // Provide detailed error message
        }
    }
}

// **Fetch Grama Sewaka Record for Editing**
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt_edit = $pdo->prepare("SELECT * FROM grama_sewaka WHERE id = :id");
    $stmt_edit->bindParam(':id', $edit_id);
    $stmt_edit->execute();
    $record_to_update = $stmt_edit->fetch(PDO::FETCH_ASSOC);
}

// **Update Logic for Editing**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_grama_sewaka'])) {
    // Collect form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $contact = $_POST['contact'];
    $username = $_POST['username'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $divisional_secretariat = $_POST['divisional_secretariat'];

    // Check if the username already exists (excluding current record)
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM grama_sewaka WHERE username = :username AND id != :id");
    $stmt_check->bindParam(':username', $username);
    $stmt_check->bindParam(':id', $id);
    $stmt_check->execute();
    $count = $stmt_check->fetchColumn();

    // If the username already exists, show an error message
    if ($count > 0) {
        $error_message = "Username already exists. Please choose a different username.";
    } else {
        // Update Grama Sewaka in the database
        $stmt = $pdo->prepare("UPDATE grama_sewaka SET name = :name, position = :position, contact = :contact, username = :username, province = :province, district = :district, divisional_secretariat = :divisional_secretariat WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':province', $province);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':divisional_secretariat', $divisional_secretariat);

        if ($stmt->execute()) {
            $success_message = "Grama Sewaka updated successfully!";
        } else {
            $error_message = "Failed to update Grama Sewaka.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Grama Sewaka</title>
    <script>
        // JavaScript to dynamically update districts based on selected province
        const districtsByProvince = {
            "Western": ["Colombo", "Gampaha", "Kalutara"],
            "Southern": ["Galle", "Matara", "Hambantota"],
            "Central": ["Kandy", "Matale", "Nuwara Eliya"],
            "Northern": ["Jaffna", "Kilinochchi", "Mannar", "Vavuniya", "Mullaitivu"],
            "Eastern": ["Trincomalee", "Batticaloa", "Ampara"],
            "North Western": ["Kurunegala", "Puttalam"],
            "Sabaragamuwa": ["Ratnapura", "Kegalle"],
            "Uva": ["Badulla", "Monaragala"],
            "North Central": ["Anuradhapura", "Polonnaruwa"]
        };

        const divisionalSecretariatsByDistrict = {
            "Colombo": ["Colombo", "Kaduwela", "Homagama", "Dehiwala-Mount Lavinia"],
            "Gampaha": ["Negombo", "Wattala", "Katana", "Ja-Ela"],
            "Kalutara": ["Kalutara", "Panadura", "Horana", "Madurawela"],
            "Galle": ["Galle", "Hikkaduwa", "Ambalangoda", "Baddegama"],
            "Matara": ["Matara", "Weligama", "Akuressa", "Devinuwara"],
            "Hambantota": ["Hambantota", "Tangalle", "Beliatta", "Sooriyawewa"],
            "Kandy": ["Kandy", "Gampola", "Nawalapitiya", "Kundasale"],
            "Matale": ["Matale", "Dambulla", "Rattota", "Pallepola"],
            "Nuwara Eliya": ["Nuwara Eliya", "Hatton", "Walapane", "Hanguranketha"],
            "Jaffna": ["Jaffna", "Nallur", "Vaddukoddai", "Point Pedro"],
            "Kilinochchi": ["Kilinochchi", "Pachchilaipalli", "Karachchi", "Kandavalai"],
            "Mannar": ["Mannar", "Nanaddan", "Madhu", "Musali"],
            "Vavuniya": ["Vavuniya", "Vavuniya North", "Vavuniya South", "Vavuniya West"],
            "Mullaitivu": ["Mullaitivu", "Maritimepattu", "Puthukudiyiruppu", "Oddusuddan"],
            "Trincomalee": ["Trincomalee", "Kantalai", "Gomarankadawala", "Seruvila"],
            "Batticaloa": ["Batticaloa", "Kattankudy", "Eravur", "Koralai Pattu"],
            "Ampara": ["Ampara", "Kalmunai", "Dehiattakandiya", "Sainthamaruthu"],
            "Kurunegala": ["Kurunegala", "Ibbagamuwa", "Polgahawela", "Maho"],
            "Puttalam": ["Puttalam", "Chilaw", "Wennappuwa", "Kalpitiya"],
            "Ratnapura": ["Ratnapura", "Kuruwita", "Balangoda", "Kalawana"],
            "Kegalle": ["Kegalle", "Mawanella", "Ruwanwella", "Aranayaka"],
            "Badulla": ["Badulla", "Bandarawela", "Hali-Ela", "Mahiyanganaya"],
            "Monaragala": ["Monaragala", "Wellawaya", "Bibile", "Siyambalanduwa"],
            "Anuradhapura": ["Anuradhapura", "Thalawa", "Galenbindunuwewa", "Kekirawa"],
            "Polonnaruwa": ["Polonnaruwa", "Dimbulagala", "Hingurakgoda", "Lankapura"]
        };

        function updateDistricts() {
            const provinceSelect = document.getElementById("province");
            const districtSelect = document.getElementById("district");
            const selectedProvince = provinceSelect.value;

            // Clear the district dropdown
            districtSelect.innerHTML = "<option value=''>Select District</option>";

            // If a province is selected, populate the district dropdown
            if (selectedProvince) {
                const districts = districtsByProvince[selectedProvince];
                districts.forEach(district => {
                    const option = document.createElement("option");
                    option.value = district;
                    option.textContent = district;
                    districtSelect.appendChild(option);
                });
            }

            // Set the selected district if editing an existing record
            const selectedDistrict = '<?php echo isset($record_to_update) ? $record_to_update['district'] : ''; ?>';
            if (selectedDistrict) {
                districtSelect.value = selectedDistrict;
                updateDivisions(); // Update divisional secretariats
            }
        }

        function updateDivisions() {
            const districtSelect = document.getElementById("district");
            const divisionalSelect = document.getElementById("divisional_secretariat");
            const selectedDistrict = districtSelect.value;

            // Clear the divisional secretariat dropdown
            divisionalSelect.innerHTML = "<option value=''>Select Divisional Secretariat</option>";

            // If a district is selected, populate the divisional secretariat dropdown
            if (selectedDistrict) {
                const divisions = divisionalSecretariatsByDistrict[selectedDistrict] || [];
                divisions.forEach(division => {
                    const option = document.createElement("option");
                    option.value = division;
                    option.textContent = division;
                    divisionalSelect.appendChild(option);
                });
            }

            // Set the selected divisional secretariat if editing an existing record
            const selectedDivision = '<?php echo isset($record_to_update) ? $record_to_update['divisional_secretariat'] : ''; ?>';
            if (selectedDivision) {
                divisionalSelect.value = selectedDivision;
            }
        }

        // On page load, update the districts and divisional secretariats for edit cases
        window.onload = function() {
            updateDistricts(); // Update districts
            updateDivisions(); // Update divisional secretariats
        }
    </script>
</head>
<body>
    <h1><?php echo isset($record_to_update) ? 'Edit' : 'Add'; ?> Grama Sewaka</h1>

    <!-- Success/Error Message -->
    <?php
    if (isset($success_message)) {
        echo "<p style='color:green;'>$success_message</p>";
    } elseif (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    ?>

    <form action="update_grama_sewaka.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $record_to_update['id'] ?? ''; ?>">

        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo $record_to_update['name'] ?? ''; ?>" required>

        <label for="position">Position</label>
        <input type="text" id="position" name="position" value="<?php echo $record_to_update['position'] ?? ''; ?>" required>

        <label for="contact">Contact</label>
        <input type="text" id="contact" name="contact" value="<?php echo $record_to_update['contact'] ?? ''; ?>" required>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo $record_to_update['username'] ?? ''; ?>" required>

        <label for="province">Province</label>
        <select name="province" id="province" onchange="updateDistricts()" required>
            <option value="">Select Province</option>
            <option value="Western" <?php echo (isset($record_to_update) && $record_to_update['province'] == 'Western') ? 'selected' : ''; ?>>Western</option>
            <option value="Southern" <?php echo (isset($record_to_update) && $record_to_update['province'] == 'Southern') ? 'selected' : ''; ?>>Southern</option>
            <option value="Central" <?php echo (isset($record_to_update) && $record_to_update['province'] == 'Central') ? 'selected' : ''; ?>>Central</option>
            <option value="Northern" <?php echo (isset($record_to_update) && $record_to_update['province'] == 'Northern') ? 'selected' : ''; ?>>Northern</option>
            <option value="Eastern" <?php echo (isset($record_to_update) && $record_to_update['province'] == 'Eastern') ? 'selected' : ''; ?>>Eastern</option>
            <option value="North Western" <?php echo (isset($record_to_update) && $record_to_update['province'] == 'North Western') ? 'selected' : ''; ?>>North Western</option>
            <option value="Sabaragamuwa" <?php echo (isset($record_to_update) && $record_to_update['province'] == 'Sabaragamuwa') ? 'selected' : ''; ?>>Sabaragamuwa</option>
            <option value="Uva" <?php echo (isset($record_to_update) && $record_to_update['province'] == 'Uva') ? 'selected' : ''; ?>>Uva</option>
            <option value="North Central" <?php echo (isset($record_to_update) && $record_to_update['province'] == 'North Central') ? 'selected' : ''; ?>>North Central</option>
        </select>

        <label for="district">District</label>
        <select name="district" id="district" required>
            <option value="">Select District</option>
        </select>

        <label for="divisional_secretariat">Divisional Secretariat</label>
        <select name="divisional_secretariat" id="divisional_secretariat" required>
            <option value="">Select Divisional Secretariat</option>
        </select>

        <button type="submit" name="update_grama_sewaka"><?php echo isset($record_to_update) ? 'Update' : 'Add'; ?> Grama Sewaka</button>
    </form>

    <!-- Back Button -->
    <a href="add_grama_sewaka.php"><button>Back to Add Dashboard</button></a>
</body>
</html>

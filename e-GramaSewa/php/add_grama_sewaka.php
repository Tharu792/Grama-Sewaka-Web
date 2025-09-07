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

// **Delete Logic**
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM grama_sewaka WHERE id = :id");
    $stmt->bindParam(':id', $delete_id);

    if ($stmt->execute()) {
        $success_message = "Grama Sewaka deleted successfully!";
    } else {
        $error_message = "Failed to delete Grama Sewaka.";
    }
}



// **Add Grama Sewaka Logic**
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

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username already exists
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM grama_sewaka WHERE username = :username");
    $stmt_check->bindParam(':username', $username);
    $stmt_check->execute();
    $count = $stmt_check->fetchColumn();

    // If the username already exists, show an error message
    if ($count > 0) {
        $error_message = "Username already exists. Please choose a different username.";
    } else {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Grama Sewaka</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ================== Beautiful Add Grama Sewaka Interface ================== */
        
        /* CSS Variables for Custom Color Palette */
        :root {
            --blue-primary: rgba(37, 99, 235, 0.9);
            --teal-primary: rgba(16, 185, 129, 0.9);
            --white-semi: rgba(255, 255, 255, 0.95);
            --white-light: rgba(255, 255, 255, 0.9);
            --teal-very-light: rgba(16, 185, 129, 0.05);
            --teal-light: rgba(16, 185, 129, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Enhanced Body Background with Image */
        body {
            font-family: 'Poppins', sans-serif;
            background-image: 
                
                url('../images/image1.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: white;
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
           
            animation: backgroundMove 20s ease infinite;
            z-index: 1;
        }

        /* Floating Particles */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, var(--white-light), transparent),
                radial-gradient(2px 2px at 40px 70px, var(--teal-light), transparent),
                radial-gradient(1px 1px at 90px 40px, var(--blue-primary), transparent);
            background-repeat: repeat;
            background-size: 200px 100px;
            animation: float 20s linear infinite;
            z-index: 2;
        }

        /* Enhanced Header */
        header {
            background: linear-gradient(135deg, var(--blue-primary), var(--teal-primary));
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
            border-bottom: 4px solid var(--white-light);
            transition: all 0.3s ease;
        }

        header:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
        }

        .logo img {
            height: 50px;
            transition: all 0.3s ease;
            filter: brightness(0) invert(1);
        }

        .logo img:hover {
            transform: scale(1.05);
        }

        .middle-nav ul {
            display: flex;
            list-style: none;
            gap: 25px;
        }

        .middle-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 30px;
            transition: all 0.3s ease;
            position: relative;
        }

        .middle-nav a:hover {
            background: var(--white-light);
            color: var(--blue-primary);
        }

        .middle-nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--teal-primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 3px;
        }

        .middle-nav a:hover::after {
            width: 70%;
        }

        .login-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        }

        /* Main Content */
        .admin-dashboard {
            padding: 30px 5%;
            position: relative;
            z-index: 10;
        }

        /* Enhanced Page Title */
        h2 {
            text-align: center;
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--white-semi), var(--teal-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--teal-primary);
            border-radius: 2px;
        }

        /* Enhanced Message Styling */
        .message {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 15px;
            backdrop-filter: blur(20px);
            border: 2px solid var(--white-light);
            position: relative;
            overflow: hidden;
            background: var(--white-semi);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .message::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--teal-primary);
        }

        .message p {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            color: var(--blue-primary);
        }

        /* Enhanced Form Styling */
        form {
            background: var(--white-semi);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(20px);
            border: 2px solid var(--white-light);
            position: relative;
            overflow: hidden;
        }

        form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--teal-primary), var(--blue-primary));
            background-size: 200% 100%;
            animation: gradientShift 3s ease infinite;
        }

        form::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, var(--white-light), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        /* Enhanced Form Elements */
        label {
            display: block;
            margin-bottom: 8px;
            color: var(--blue-primary);
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        label::before {
            content: '●';
            color: var(--teal-primary);
            margin-right: 8px;
            font-size: 1.2rem;
        }

        input[type="text"], 
        input[type="password"], 
        select {
            width: 100%;
            padding: 15px 20px;
            font-size: 1rem;
            border: 2px solid var(--teal-light);
            border-radius: 12px;
            background: var(--white-light);
            color: var(--blue-primary);
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        input[type="text"]:focus, 
        input[type="password"]:focus, 
        select:focus {
            outline: none;
            border-color: var(--teal-primary);
            box-shadow: 0 0 0 3px var(--teal-light);
            transform: translateY(-2px);
            background: var(--white-semi);
        }

        input[type="text"]:hover, 
        input[type="password"]:hover, 
        select:hover {
            border-color: var(--blue-primary);
            transform: translateY(-1px);
        }

        /* Enhanced Submit Button */
        button[type="submit"] {
            width: 100%;
            padding: 18px 30px;
            font-size: 1.2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(135deg, var(--blue-primary), var(--teal-primary));
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        button[type="submit"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, var(--white-light), transparent);
            transition: left 0.6s ease;
        }

        button[type="submit"]:hover::before {
            left: 100%;
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, var(--teal-primary), var(--blue-primary));
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(16, 185, 129, 0.4);
        }

        button[type="submit"]:active {
            transform: translateY(-1px);
        }

        /* Enhanced Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white-semi);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(20px);
            border: 2px solid var(--white-light);
        }

        th {
            background: linear-gradient(135deg, var(--blue-primary), var(--teal-primary));
            color: white;
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid var(--teal-light);
            color: var(--blue-primary);
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: var(--teal-very-light);
        }

        tr:hover {
            background-color: var(--teal-light);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        /* Enhanced Action Buttons */
        .action-buttons a {
            text-decoration: none;
            padding: 8px 16px;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 2px;
        }

        .action-buttons a:first-child {
            background: linear-gradient(135deg, var(--blue-primary), var(--teal-primary));
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .action-buttons a:first-child:hover {
            background: linear-gradient(135deg, var(--teal-primary), var(--blue-primary));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .action-buttons a.delete {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .action-buttons a.delete:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 15px;
            }
            
            .logo {
                margin-bottom: 15px;
            }
            
            .middle-nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
                margin-bottom: 15px;
            }
            
            h2 {
                font-size: 2rem;
            }
            
            form {
                padding: 25px;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 12px 8px;
            }
        }

        @media (max-width: 576px) {
            .middle-nav ul {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }
            
            h2 {
                font-size: 1.8rem;
            }
            
            form {
                padding: 20px;
            }
            
            input[type="text"], 
            input[type="password"], 
            select {
                padding: 12px 15px;
            }
            
            button[type="submit"] {
                padding: 15px 25px;
                font-size: 1.1rem;
            }
        }
    </style>
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

            // Reset divisional secretariats if no district is selected
            document.getElementById("divisional_secretariat").innerHTML = "<option value=''>Select Divisional Secretariat</option>";
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
        }

        window.onload = function () {
            updateDistricts(); // Update districts
            updateDivisions(); // Update divisional secretariats
        };
    </script>
    
</head>
<body>
    
    <div class="admin-dashboard">
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
                    <li><a href="admin_view_user.php">View User</a></li>
                    <li><a href="report.php">Generate Report</a></li>
                    
                </ul>
            </nav>
            <div class="right-nav">
            <a href="logout.php" class="login-btn">Logout</a>
             </div>
        </header>

        <h2>Add Grama Sewaka</h2>
        <?php if (isset($success_message)) { echo "<p style='color:green;'>$success_message</p>"; } ?>
        <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>
        
        <!-- Add Grama Sewaka Form -->
        <form action="add_grama_sewaka.php" method="POST">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="position">Position</label>
            <input type="text" id="position" name="position" required>

            <label for="contact">Contact</label>
            <input type="text" id="contact" name="contact" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="province">Province</label>
            <select name="province" id="province" onchange="updateDistricts()" required>
                <option value="">Select Province</option>
                <option value="Western">Western</option>
                <option value="Southern">Southern</option>
                <option value="Central">Central</option>
                <option value="Northern">Northern</option>
                <option value="Eastern">Eastern</option>
                <option value="North Western">North Western</option>
                <option value="Sabaragamuwa">Sabaragamuwa</option>
                <option value="Uva">Uva</option>
                <option value="North Central">North Central</option>
            </select>

            <label for="district">District</label>
            <select name="district" id="district" onchange="updateDivisions()" required>
                <option value="">Select District</option>
            </select>

            <label for="divisional_secretariat">Divisional Secretariat</label>
            <select name="divisional_secretariat" id="divisional_secretariat" required>
                <option value="">Select Divisional Secretariat</option>
            </select>

            <button type="submit" name="add_grama_sewaka">Add Grama Sewaka</button>
        </form>


        <!-- Success/Error Message -->
        <?php
        if (isset($success_message)) {
            echo "<div class='message'><p style='color:green;'>$success_message</p></div>";
        } elseif (isset($error_message)) {
            echo "<div class='message'><p style='color:red;'>$error_message</p></div>";
        }
        ?>

        <!-- Grama Sewaka Records -->
        <h2>Grama Sewaka Records</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Contact</th>
                    <th>Username</th>
                    <th>Province</th>
                    <th>District</th>
                    <th>Divisional Secretariat</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grama_sewaka_records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['id']); ?></td>
                        <td><?php echo htmlspecialchars($record['name']); ?></td>
                        <td><?php echo htmlspecialchars($record['position']); ?></td>
                        <td><?php echo htmlspecialchars($record['contact']); ?></td>
                        <td><?php echo htmlspecialchars($record['username']); ?></td>
                        <td><?php echo htmlspecialchars($record['province']); ?></td>
                        <td><?php echo htmlspecialchars($record['district']); ?></td>
                        <td><?php echo htmlspecialchars($record['divisional_secretariat']); ?></td>
                        <td class="action-buttons">
                            <a href="update_grama_sewaka.php?edit_id=<?php echo $record['id']; ?>">Edit</a> | 
                            <a href="add_grama_sewaka.php?delete_id=<?php echo $record['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this Grama Sewaka?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</body>
</html>
        
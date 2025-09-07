<?php
// Start session
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grama_sewa";

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Encrypt password
    $name = $_POST['name'];
    $address = $_POST['address'];
    $nic = $_POST['nic'];
    $tel_no = $_POST['tel_no'];
    $email = $_POST['email'];
    $district = $_POST['district']; // Capture the district field
    $province = $_POST['province']; // Capture the province field
    $divisional_secretariat = $_POST['divisional_secretariat']; // Capture the divisional secretariat field

    // Check if the username already exists
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE username = ?");
    $stmt_check->execute([$username]);
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        // If the username already exists, show an error message
        $error_message = "Username already exists. Please choose a different username.";
    } else {
        // Insert the user into the database if the username is unique
        $stmt = $pdo->prepare("INSERT INTO customers (username, password, name, address, nic, tel_no, email, district, province, divisional_secretariat) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $name, $address, $nic, $tel_no, $email, $district, $province, $divisional_secretariat]);

        // Redirect to login page after successful registration
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - e-GramaSewa</title>
    <link rel="stylesheet" href="/e-GramaSewa/css/style.css">
    <style>
        /* ================== Beautiful Register Page Styles ================== */
        
        /* CSS Variables for Consistent Design */
        :root {
            --register-primary: #3b82f6;
            --register-secondary: #10b981;
            --register-accent: #f59e0b;
            --register-success: #22c55e;
            --register-error: #ef4444;
            --register-warning: #f59e0b;
            --register-info: #06b6d4;
            --register-light: #f8fafc;
            --register-dark: #1e293b;
            --register-gray: #64748b;
            --register-white: #ffffff;
            --register-shadow: rgba(0, 0, 0, 0.1);
            --register-shadow-lg: rgba(0, 0, 0, 0.15);
        }

        /* Enhanced Body Background */
        body {
            background-image: url('/e-GramaSewa/images/image6.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Enhanced Register Container */
        .register-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Container Hover Effect */
        .register-container:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 
                0 35px 70px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        /* Container Top Border */
        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                var(--register-primary), 
                var(--register-secondary), 
                var(--register-accent), 
                var(--register-primary));
        }

        /* Enhanced Title */
        .register-container h2 {
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, 
                var(--register-primary) 0%, 
                var(--register-secondary) 50%, 
                var(--register-accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            animation: registerTitleGlow 3s ease-in-out infinite alternate;
        }

        /* Title Underline */
        .register-container h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, 
                var(--register-primary), 
                var(--register-secondary));
            border-radius: 2px;
            animation: registerUnderline 2s ease-in-out infinite alternate;
        }

        /* Enhanced Input Groups */
        .input-group {
            margin-bottom: 2rem;
            position: relative;
        }

        /* Enhanced Labels */
        .input-group label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--register-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            transition: all 0.3s ease;
        }

        /* Label Icon Effect */
        .input-group label::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, 
                var(--register-primary), 
                var(--register-secondary));
            border-radius: 50%;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .input-group:focus-within label::before {
            opacity: 1;
            left: -15px;
        }

        /* Enhanced Input Fields */
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 1.2rem 1.5rem;
            border: 2px solid rgba(59, 130, 246, 0.2);
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-sizing: border-box;
            position: relative;
            color: var(--register-dark);
        }

        /* Input Focus Effects */
        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: var(--register-primary);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 
                0 0 0 4px rgba(59, 130, 246, 0.1),
                0 10px 25px rgba(59, 130, 246, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Input Hover Effects */
        .input-group input:hover,
        .input-group select:hover {
            border-color: var(--register-secondary);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.1);
        }

        /* Enhanced Submit Button */
        button[type="submit"] {
            width: 100%;
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, 
                var(--register-primary) 0%, 
                var(--register-secondary) 50%, 
                var(--register-accent) 100%);
            background-size: 200% 100%;
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 1.3rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 
                0 10px 30px rgba(59, 130, 246, 0.3),
                0 0 0 0 rgba(16, 185, 129, 0.3);
        }

        /* Button Shimmer Effect */
        button[type="submit"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.3), 
                transparent);
            transition: left 0.6s ease;
        }

        button[type="submit"]:hover::before {
            left: 100%;
        }

        /* Button Hover Effects */
        button[type="submit"]:hover {
            background-position: 100% 0;
            transform: translateY(-5px) scale(1.02);
            box-shadow: 
                0 20px 40px rgba(59, 130, 246, 0.4),
                0 0 0 8px rgba(16, 185, 129, 0.1);
        }

        /* Button Active Effect */
        button[type="submit"]:active {
            transform: translateY(-2px) scale(0.98);
        }

        /* Enhanced Error Message */
        p {
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--register-error);
            padding: 1rem 2rem;
            background: rgba(239, 68, 68, 0.1);
            border: 2px solid rgba(239, 68, 68, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            position: relative;
            animation: registerErrorShake 0.5s ease-in-out;
        }

        /* Error Message Icon */
        p::before {
            content: '⚠️';
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Enhanced Loader */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: all 0.3s ease;
        }

        /* Enhanced Spinner */
        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(59, 130, 246, 0.2);
            border-top: 4px solid var(--register-primary);
            border-right: 4px solid var(--register-secondary);
            border-radius: 50%;
            animation: registerSpin 1s linear infinite;
            position: relative;
        }

        .spinner::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, 
                var(--register-primary), 
                var(--register-secondary));
            border-radius: 50%;
            animation: registerPulse 2s ease-in-out infinite;
        }

        /* Enhanced Select Dropdowns */
        select {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1.2rem 1.5rem;
            border: 2px solid rgba(59, 130, 246, 0.2);
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 500;
            width: 100%;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            color: var(--register-dark);
        }

        select:hover {
            border-color: var(--register-secondary);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.1);
        }

        select:focus {
            outline: none;
            border-color: var(--register-primary);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 
                0 0 0 4px rgba(59, 130, 246, 0.1),
                0 10px 25px rgba(59, 130, 246, 0.15);
            transform: translateY(-2px);
        }

        /* Form Section Styling */
        .form-section {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Success Message Styling */
        .success-message {
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--register-success);
            padding: 1rem 2rem;
            background: rgba(34, 197, 94, 0.1);
            border: 2px solid rgba(34, 197, 94, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .success-message::before {
            content: '✅';
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Animations */

        @keyframes registerTitleGlow {
            0% { text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
            100% { text-shadow: 0 4px 20px rgba(59, 130, 246, 0.3); }
        }

        @keyframes registerUnderline {
            0% { width: 100px; }
            100% { width: 200px; }
        }

        @keyframes registerSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes registerPulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.7; }
        }

        @keyframes registerErrorShake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Enhanced Responsive Design */
        @media screen and (max-width: 768px) {
            .register-container {
                padding: 2rem 1.5rem;
                margin: 1rem auto;
                border-radius: 20px;
            }

            .register-container h2 {
                font-size: 2.2rem;
                margin-bottom: 2rem;
            }

            .input-group {
                margin-bottom: 1.5rem;
            }

            .input-group input,
            .input-group select {
                padding: 1rem 1.2rem;
                font-size: 1rem;
            }

            button[type="submit"] {
                font-size: 1.1rem;
                padding: 1.2rem 1.5rem;
            }
        }

        @media screen and (max-width: 480px) {
            .register-container {
                padding: 1.5rem 1rem;
                margin: 0.5rem auto;
            }

            .register-container h2 {
                font-size: 1.8rem;
            }

            .input-group input,
            .input-group select {
                padding: 0.8rem 1rem;
                font-size: 0.95rem;
            }

            button[type="submit"] {
                font-size: 1rem;
                padding: 1rem 1.2rem;
            }
        }

        /* Enhanced Focus States for Accessibility */
        .input-group input:focus,
        .input-group select:focus,
        button[type="submit"]:focus {
            outline: 3px solid rgba(59, 130, 246, 0.5);
            outline-offset: 2px;
        }

        /* Smooth Transitions for All Interactive Elements */
        * {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
    </style>
    <script>
        // JavaScript to dynamically update districts and divisional secretariats based on selected province and district
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
            
            // Hide loader when page loads
            const loader = document.getElementById('loader');
            if (loader) {
                loader.style.display = 'none';
            }
        };
    </script>
</head>
<body>
    <!-- Loader -->
<div id="loader">
  <div class="spinner"></div>
</div>

<header>
  <div class="logo">
    <img src="/e-GramaSewa/images/Logo_3.png" alt="e-GramaSewa Logo">
  </div>

  <!-- Middle Navigation Links -->
  <nav class="middle-nav">
    <ul>
      <li><a href="/e-GramaSewa/pages/Home.html">Home</a></li>
      <li><a href="/e-GramaSewa/pages/About.html">About</a></li>
      <li><a href="/e-GramaSewa/pages/Service.html">Services</a></li>
      <li><a href="/e-GramaSewa/pages/contact.html">Contact</a></li>
      <li><a href="/e-GramaSewa/php/customer_my_profile.php">My Profile</a></li>
    </ul>
  </nav>

  <!-- Right Side Buttons -->
  <div class="right-nav">
    <a href="/e-GramaSewa/php/login.php" class="login-btn">Login</a>
    <a href="/e-GramaSewa/php/logout.php" class="login-btn">Logout</a>
    
  </div>
</header>

    <div class="register-container">
        <h2>Register</h2>
        
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <form action="register.php" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="input-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="input-group">
                <label for="nic">NIC</label>
                <input type="text" id="nic" name="nic" required>
            </div>
            <div class="input-group">
                <label for="tel_no">Telephone No</label>
                <input type="text" id="tel_no" name="tel_no" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
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
            </div>
            <div class="input-group">
                <label for="district">District</label>
                <select name="district" id="district" onchange="updateDivisions()" required>
                    <option value="">Select District</option>
                </select>
            </div>
            <div class="input-group">
                <label for="divisional_secretariat">Divisional Secretariat</label>
                <select name="divisional_secretariat" id="divisional_secretariat" required>
                    <option value="">Select Divisional Secretariat</option>
                </select>
            </div>
            <button type="submit">Register</button>
        </form>
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
        <li><a href="/e-GramaSewa/pages/Home.html">Home</a></li>
        <li><a href="/e-GramaSewa/pages/About.html">Services</a></li>
        <li><a href="/e-GramaSewa/pages/contact.html">Contact</a></li>
        <li><a href="/e-GramaSewa/php/register.php">Register</a></li>
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

<!-- JS File -->
<script src="/e-GramaSewa/js/script.js">
  
</script>
</body>
</html>

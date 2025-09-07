<?php
session_start();

// Redirect user to login page if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

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

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $nic_number = $_POST['nic_number'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $applying_for_new_nic = isset($_POST['applying_for_new_nic']) ? 1 : 0;
    $replacing_lost_nic = isset($_POST['replacing_lost_nic']) ? 1 : 0;

    // Check if province, district, and divisional_secretariat are set in the POST request
    $province = isset($_POST['province']) ? $_POST['province'] : null;
    $district = isset($_POST['district']) ? $_POST['district'] : null;
    $divisional_secretariat = isset($_POST['divisional_secretariat']) ? $_POST['divisional_secretariat'] : null;

    // Validate phone number (must be 10 digits)
    if (!preg_match("/^\d{10}$/", $phone_number)) {
        $message = "Phone number must be exactly 10 digits.";
    }

    // Validate NIC number (minimum length of 12 characters)
    if (strlen($nic_number) < 12) {
        $message = "NIC number must be at least 12 characters.";
    }

    // File uploads
    $birth_certificate = $_FILES['birth_certificate']['name'];
    $photo = $_FILES['photo']['name'];
    $policy_report = $_FILES['policy_report']['name'];  // New field for policy entry report
    
    // Directory for uploads
    $uploadDir = 'uploads/';
    $birth_certificate_path = $uploadDir . basename($birth_certificate);
    $photo_path = $uploadDir . basename($photo);
    $policy_report_path = $uploadDir . basename($policy_report); // Policy entry report path

    // Move uploaded files
    move_uploaded_file($_FILES['birth_certificate']['tmp_name'], $birth_certificate_path);
    move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);

    // Move policy report only if replacing lost NIC
    if ($replacing_lost_nic && $_FILES['policy_report']['size'] > 0) {
        move_uploaded_file($_FILES['policy_report']['tmp_name'], $policy_report_path);
    } else {
        $policy_report_path = null;  // No report uploaded if not replacing lost NIC
    }

    // Insert data into database if no errors
    if (!$message) {
        $sql = "INSERT INTO national_id_requests (username, full_name, dob, nic_number, phone_number, address, applying_for_new_nic, replacing_lost_nic, birth_certificate, photo, policy_report, province, district, divisional_secretariat, status)
                VALUES (:username, :full_name, :dob, :nic_number, :phone_number, :address, :applying_for_new_nic, :replacing_lost_nic, :birth_certificate, :photo, :policy_report, :province, :district, :divisional_secretariat, 'Pending')";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([ 
            ':username' => $_SESSION['username'],
            ':full_name' => $full_name,
            ':dob' => $dob,
            ':nic_number' => $nic_number,
            ':phone_number' => $phone_number,
            ':address' => $address,
            ':applying_for_new_nic' => $applying_for_new_nic,
            ':replacing_lost_nic' => $replacing_lost_nic,
            ':birth_certificate' => $birth_certificate_path,
            ':photo' => $photo_path,
            ':policy_report' => $policy_report_path,
            ':province' => $province,
            ':district' => $district,
            ':divisional_secretariat' => $divisional_secretariat
        ]);
        $message = "NIC application submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIC Application Form</title>
    <style>
        /* Track App Style for National ID Verification Form - No Animations */
        :root {
            --primary: rgba(37, 99, 235, 0.9);
            --secondary: rgba(16, 185, 129, 0.9);
            --card: rgba(255, 255, 255, 0.95);
            --text: #1e293b;
            --text-light: #64748b;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: url('../images/image6.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            line-height: 1.6;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .container {
            max-width: 700px;
            margin: 2rem auto;
            background: var(--card);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 10;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.05), transparent);
            transition: left 0.6s ease;
        }

        .container:hover::before {
            left: 100%;
        }

        h2 {
            text-align: center;
            color: var(--text);
            margin-bottom: 30px;
            font-size: 2.5rem;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            border-radius: 2px;
        }

        /* Label and Form Inputs */
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--text);
            font-size: 1rem;
            position: relative;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        input[type="text"],
        input[type="date"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 0;
            border: 2px solid rgba(16, 185, 129, 0.2);
            border-radius: 12px;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            color: var(--text);
            position: relative;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
            outline: none;
            transform: translateY(-2px);
        }

        input[type="text"]:hover,
        input[type="date"]:hover,
        textarea:hover,
        select:hover {
            border-color: var(--secondary);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
        }

        input[type="checkbox"] {
            margin-right: 12px;
            transform: scale(1.2);
            accent-color: var(--secondary);
        }

        /* Textarea */
        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Buttons */
        button {
            width: 100%;
            padding: 18px 25px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 18px;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        button:hover::before {
            left: 100%;
        }

        button:hover {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(16, 185, 129, 0.4);
        }

        .back-btn {
            text-align: center;
            margin-top: 20px;
        }

        .back-btn button {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }

        .back-btn button:hover {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        /* Messages */
        .message {
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 15px;
            background: var(--card);
            color: var(--secondary);
            border: 2px solid rgba(16, 185, 129, 0.3);
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
            position: relative;
            overflow: hidden;
        }

        .message::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .message:hover::before {
            left: 100%;
        }

        .hidden {
            display: none;
        }

        /* National ID Specific Styling */
        .nic-options-section {
            background: rgba(16, 185, 129, 0.05);
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            border: 2px solid rgba(16, 185, 129, 0.1);
        }

        .nic-options-section h3 {
            color: var(--text);
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
        }

        .nic-options-section label {
            color: var(--text);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .file-upload-section {
            background: rgba(37, 99, 235, 0.05);
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            border: 2px solid rgba(37, 99, 235, 0.1);
        }

        .file-upload-section h3 {
            color: var(--text);
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
        }

        .file-upload-section label {
            color: var(--text);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .personal-info-section {
            background: rgba(16, 185, 129, 0.05);
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            border: 2px solid rgba(16, 185, 129, 0.1);
        }

        .personal-info-section h3 {
            color: var(--text);
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
        }

        .personal-info-section label {
            color: var(--text);
            font-weight: 600;
            margin-bottom: 8px;
        }

        input[type="file"] {
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(16, 185, 129, 0.2);
            border-radius: 12px;
            color: var(--text);
        }

        input[type="file"]:hover {
            border-color: var(--secondary);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 25px;
                max-width: 100%;
            }

            h2 {
                font-size: 2rem;
            }

            input, textarea, select {
                padding: 12px 15px;
                font-size: 14px;
            }

            button {
                padding: 15px 20px;
                font-size: 16px;
            }

            .nic-options-section, .file-upload-section, .personal-info-section {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>NIC Application Form</h2>
        
        <!-- Button to open the modal -->
        <button onclick="openModal()" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color:white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3); transition: all 0.3s ease;">
            View Guidelines
        </button>

        <!-- Modal for Guidelines -->
        <div id="guidelinesModal" class="modal" style="display: none;">
            <div class="modal-content" style="background-color:#fff; color:#000; padding:20px; border-radius:8px; width:80%; margin:10% auto; box-shadow:0 5px 15px rgba(0,0,0,0.3); font-family: Arial, sans-serif;">
                <span class="close" onclick="closeModal()" 
                      style="background-color:#b0b0b0; color:#fff; float:right; font-size:28px; font-weight:bold; cursor:pointer; border-radius:50%; width:35px; height:35px; display:flex; align-items:center; justify-content:center; transition:background 0.3s ease;">
                    &times;
                </span>
                <h3>NIC Application Guidelines</h3>
                <p><strong>Full Name:</strong> Enter your legal full name as per your official documents.</p>
                <p><strong>Date of Birth:</strong> Enter your date of birth in the correct format (YYYY-MM-DD).</p>
                <p><strong>NIC Number:</strong> Enter your existing NIC number if you have one (optional for new applications).</p>
                <p><strong>Phone Number:</strong> Enter a valid 10-digit contact number.</p>
                <p><strong>Address:</strong> Provide your complete residential address.</p>
                <p><strong>Province/District/Divisional Secretariat:</strong> Select your location from the dropdown menus.</p>
                <p><strong>Application Type:</strong> Check the appropriate box for new NIC or replacement of lost NIC.</p>
                <p><strong>Birth Certificate:</strong> Upload a clear copy of your birth certificate.</p>
                <p><strong>Photo:</strong> Upload a recent passport-size photo.</p>
                <p><strong>Policy Entry Report:</strong> Required only if replacing a lost NIC.</p>
            </div>
        </div>
        
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" name="dob" id="dob" required>

            <label for="nic_number">NIC Number (Optional):</label>
            <input type="text" name="nic_number" id="nic_number">

            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" required>

            <label for="address">Address:</label>
            <textarea name="address" id="address" required></textarea>

            <label for="province">Province:</label>
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

            <label for="district">District:</label>
            <select name="district" id="district" onchange="updateDivisions()" required>
                <option value="">Select District</option>
            </select>

            <label for="divisional_secretariat">Divisional Secretariat:</label>
            <select name="divisional_secretariat" id="divisional_secretariat" required>
                <option value="">Select Divisional Secretariat</option>
            </select>

            <label for="applying_for_new_nic">Applying for New NIC:</label>
            <input type="checkbox" name="applying_for_new_nic" id="applying_for_new_nic">

            <label for="replacing_lost_nic">Replacing Lost NIC:</label>
            <input type="checkbox" name="replacing_lost_nic" id="replacing_lost_nic">

            <label for="birth_certificate">Upload Birth Certificate:</label>
            <input type="file" name="birth_certificate" id="birth_certificate" required>

            <label for="photo">Upload Photo:</label>
            <input type="file" name="photo" id="photo" required>

            <div id="policyReportField" class="hidden">
                <label for="policy_report">Upload Policy Entry Report (Required for Lost NIC replacement):</label>
                <input type="file" name="policy_report" id="policy_report">
            </div>

            <button type="submit">Submit</button>
        </form>

        <div class="back-btn">
            <form action="customer_dashboard.php" method="POST">
                <button type="submit">Back to Dashboard</button>
            </form>
        </div>

    </div>

    <script>
        // JavaScript to toggle the Policy Entry Report field based on the "Replacing Lost NIC" checkbox
        document.getElementById('replacing_lost_nic').addEventListener('change', function() {
            var policyReportField = document.getElementById('policyReportField');
            if (this.checked) {
                policyReportField.classList.remove('hidden');
            } else {
                policyReportField.classList.add('hidden');
            }
        });

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

            districtSelect.innerHTML = "<option value=''>Select District</option>";
            if (selectedProvince) {
                const districts = districtsByProvince[selectedProvince];
                districts.forEach(district => {
                    const option = document.createElement("option");
                    option.value = district;
                    option.textContent = district;
                    districtSelect.appendChild(option);
                });
            }
        }

        function updateDivisions() {
            const districtSelect = document.getElementById("district");
            const divisionalSelect = document.getElementById("divisional_secretariat");
            const selectedDistrict = districtSelect.value;

            divisionalSelect.innerHTML = "<option value=''>Select Divisional Secretariats</option>";
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

        // Function to open the modal
        function openModal() {
            document.getElementById('guidelinesModal').style.display = 'block';
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('guidelinesModal').style.display = 'none';
        }

        // Close modal if clicked outside the content
        window.onclick = function(event) {
            const modal = document.getElementById('guidelinesModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>

</body>
</html>

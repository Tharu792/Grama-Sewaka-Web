<?php
session_start();

// Redirect to login page if the user is not logged in
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

// List of districts categorized by province
$districts_by_province = [
    // Add your districts data here...
];

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $full_name = $_POST['full_name'];
    $dob = $_POST['dob'];
    $nic = $_POST['nic'];
    $permanent_address = $_POST['permanent_address'];
    $current_address = $_POST['current_address'];
    $contact = $_POST['contact'];
    $purpose = $_POST['purpose'];
    $district = $_POST['district']; // District field added
    $divisional_secretariat = $_POST['divisional_secretariat']; // Divisional Secretariat field added

    // File upload handling for proof of residence
    $uploadedFiles = [];
    if (isset($_FILES['proof_document']) && $_FILES['proof_document']['error'] == 0) {
        $uploadDir = 'uploads/residence_certificates/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES['proof_document']['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($fileType, $allowedTypes)) {
            $message = "Only PDF, JPG, and PNG files are allowed for proof of residence.";
        } elseif (!move_uploaded_file($_FILES['proof_document']['tmp_name'], $targetFile)) {
            $message = "Error uploading proof of residence file.";
        } else {
            $uploadedFiles[] = $fileName; // Store proof document
        }
    } else {
        $message = "Proof of residence file is required.";
    }

    // File upload handling for birth certificate
    if (isset($_FILES['birth_certificate']) && $_FILES['birth_certificate']['error'] == 0) {
        $uploadDir = 'uploads/birth_certificates/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $birthCertName = time() . "_" . basename($_FILES['birth_certificate']['name']);
        $targetFile = $uploadDir . $birthCertName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($fileType, $allowedTypes)) {
            $message = "Only PDF, JPG, and PNG files are allowed for birth certificate.";
        } elseif (!move_uploaded_file($_FILES['birth_certificate']['tmp_name'], $targetFile)) {
            $message = "Error uploading birth certificate.";
        } else {
            $birthCertificateFile = $birthCertName; // Store birth certificate file name
        }
    } else {
        $message = "Birth certificate file is required.";
    }

    // If files are uploaded successfully, insert data into the database
    if (!$message && $uploadedFiles && $birthCertificateFile) {
        $proofDocumentJSON = json_encode($uploadedFiles); // Convert proof documents to JSON string

        // Insert data into the database
        $stmt = $pdo->prepare("INSERT INTO residence_certificates 
            (username, full_name, dob, nic, permanent_address, current_address, contact, purpose, proof_document, birth_certificate, district, divisional_secretariat, status) 
            VALUES (:username, :full_name, :dob, :nic, :permanent_address, :current_address, :contact, :purpose, :proof_document, :birth_certificate, :district, :divisional_secretariat, 'Pending')");

        $stmt->bindParam(':username', $_SESSION['username']);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':nic', $nic);
        $stmt->bindParam(':permanent_address', $permanent_address);
        $stmt->bindParam(':current_address', $current_address);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':purpose', $purpose);
        $stmt->bindParam(':proof_document', $proofDocumentJSON);
        $stmt->bindParam(':birth_certificate', $birthCertificateFile); // Bind birth certificate file name
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':divisional_secretariat', $divisional_secretariat);

        if ($stmt->execute()) {
            $message = "Application submitted successfully! Status: Pending approval.";
        } else {
            $message = "Error submitting application.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Residence Certificate Application</title>


     

    <style>
        /* Track App Style for Residence Certificate Application */
        :root {
            --primary: rgba(37, 99, 235, 0.9);
            --secondary: rgba(16, 185, 129, 0.9);
            --card: rgba(255, 255, 255, 0.95);
            --text: #1e293b;
            --text-light: #64748b;
        }

        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background: url('../images/image6.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
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
            margin-bottom: 30px;
            font-size: 2.5rem;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: slideInUp 1s ease-out;
            color: var(--text);
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

        /* Form input styles */
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

        input[type="text"], input[type="date"], input[type="tel"], input[type="file"], textarea, select {
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

        input[type="text"]:focus, input[type="date"]:focus, input[type="tel"]:focus, textarea:focus, select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
            outline: none;
            transform: translateY(-2px);
        }

        input[type="text"]:hover, input[type="date"]:hover, input[type="tel"]:hover, textarea:hover, select:hover {
            border-color: var(--secondary);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }

        /* Button styles */
        button {
            width: 100%;
            padding: 18px 25px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            margin-top: 10px;
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

        /* Message box styles */
        .message {
            background: var(--card);
            color: var(--secondary);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
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

        /* Link styles */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: var(--primary);
            font-size: 16px;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .back-link:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        /* Form group layout */
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 25px;
        }

        /* Select box styling */
        select {
            padding: 15px 20px;
            border: 2px solid rgba(16, 185, 129, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            color: var(--text);
        }

        select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
            outline: none;
            transform: translateY(-2px);
        }

        /* Checkbox styling */
        input[type="checkbox"] {
            margin-right: 12px;
            transform: scale(1.2);
            accent-color: var(--secondary);
        }

        .footer {
            font-size: 14px;
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Modal Styling */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--card);
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
           
            width: 80%;
            max-width: 600px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(44, 155, 214, 0.2);
            position: relative;
            animation: slideInUp 0.3s ease-out;
        }

        .close {
            color: var(--text-light);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: var(--primary);
        }

        /* Animations */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes backgroundMove {
            0% { transform: translateX(0) translateY(0); }
            25% { transform: translateX(-5%) translateY(-5%); }
            50% { transform: translateX(5%) translateY(-10%); }
            75% { transform: translateX(-3%) translateY(5%); }
            100% { transform: translateX(0) translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-100px) rotate(360deg); }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 25px;
                max-width: 100%;
            }

            h2 {
                font-size: 2rem;
            }

            input[type="text"], input[type="date"], input[type="tel"], input[type="file"], textarea, select {
                padding: 12px 15px;
                font-size: 14px;
            }

            button {
                padding: 15px 20px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Residence Certificate Application</h2>

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
            <h3>Residence Certificate Guidelines</h3>
            <p><strong>Full Name:</strong> Enter your legal full name as per your official documents.</p>
            <p><strong>Date of Birth:</strong> Enter your date of birth in the correct format (YYYY-MM-DD).</p>
            <p><strong>NIC Number:</strong> Enter the NIC number provided on your National Identity Card.</p>
            <p><strong>Permanent Address:</strong> Provide your permanent residential address as per official records.</p>
            <p><strong>Current Address:</strong> Provide your current residential address if different from permanent address.</p>
            <p><strong>Contact Number:</strong> Enter a valid 10-digit contact number.</p>
            <p><strong>Purpose of Certificate:</strong> Specify the purpose for which you need this residence certificate.</p>
            <p><strong>Proof of Residence:</strong> Upload a recent electrical bill or document that verifies your residence.</p>
            <p><strong>Birth Certificate:</strong> Upload a valid copy of your birth certificate.</p>
            <p><strong>File Formats:</strong> Only PDF, JPG, JPEG, and PNG files are accepted.</p>
            <p><strong>Declaration:</strong> Check the declaration box to confirm all information is accurate.</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Province:</label>
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

        <label>District:</label>
        <select name="district" id="district" onchange="updateDivisionalSecretariats()" required>
            <option value="">Select District</option>
        </select>

        <label>Divisional Secretariats (DS Divisions):</label>
        <select name="divisional_secretariat" id="divisional_secretariat" required>
            <option value="">Select Divisional Secretariats</option>
        </select>

        <label>Full Name:</label>
        <input type="text" name="full_name" placeholder="Enter your full name" required>

        <label>Date of Birth:</label>
        <input type="date" name="dob" required>

        <label>NIC Number:</label>
        <input type="text" name="nic" placeholder="Enter NIC number" required>

        <label>Permanent Address:</label>
        <textarea name="permanent_address" placeholder="Enter your permanent address" required></textarea>

        <label>Current Address:</label>
        <textarea name="current_address" placeholder="Enter current address if different"></textarea>

        <label>Contact Number:</label>
        <input type="tel" name="contact" placeholder="Enter contact number" required>

        <label>Purpose of Certificate:</label>
        <input type="text" name="purpose" placeholder="Reason for applying" required>

        <label>Upload Proof of Electrical Bill:</label>
        <input type="file" name="proof_document" accept=".pdf,.jpg,.jpeg,.png" required>

        <label>Upload Birth Certificate:</label>
        <input type="file" name="birth_certificate" accept=".jpg,.jpeg,.png,.pdf" required>

        <label>
            <input type="checkbox" name="declaration" required>
            I hereby declare that the information provided is true and correct.
        </label>

        <button type="submit">Submit Application</button>
    </form>

    <form action="customer_dashboard.php" method="POST" style="text-align:center; margin-top:20px;">
    <input type="hidden" name="from_page" value="residence_form.php">
    <button type="submit" style="padding:12px 25px; background: linear-gradient(135deg, var(--primary), var(--secondary)); color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight: 600; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3); transition: all 0.3s ease;">
        Back to Dashboard
    </button>
</form>
</div>

<script>
// JavaScript for dynamically updating districts based on the selected province
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
    "Hambantota": ["Hambantota", "Tangalle", "Beliatta", "Sooriyawewa", "Akuressa"],
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
    "Polonnaruwa": ["Polonnaruwa", "Dimbulagala", "Hingurakgoda", "Lankapura"],
};

function updateDistricts() {
    const provinceSelect = document.getElementById("province");
    const districtSelect = document.getElementById("district");
    const selectedProvince = provinceSelect.value;

    // Clear the district dropdown
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

function updateDivisionalSecretariats() {
    const districtSelect = document.getElementById("district");
    const divisionalSelect = document.getElementById("divisional_secretariat");
    const selectedDistrict = districtSelect.value;

    // Clear the divisional secretariat dropdown
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

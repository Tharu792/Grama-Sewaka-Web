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
    $nic = $_POST['nic'];
    $tree_name = $_POST['tree_name'];
    $reason = $_POST['reason']; // Single selection for reason
    $contact = $_POST['contact'];
    $district = $_POST['district']; // District field added
    $divisional_secretariat = $_POST['divisional_secretariat']; // Divisional Secretariat field added

    // Validate telephone number (10 digits)
    if (!preg_match("/^\d{10}$/", $contact)) {
        $message = "Contact number must be exactly 10 digits.";
    }
    // Validate NIC (minimum length of 12 characters)
    elseif (strlen($nic) < 12) {
        $message = "NIC/Passport number must be at least 12 characters.";
    }
    // Initialize file upload variables if no errors yet
    elseif (!$message) {
        $uploadDir = 'uploads/tree_cutting_certificates/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Prepare to store uploaded files
        $uploadedFiles = [];
        $validTypes = ['pdf', 'jpg', 'jpeg', 'png'];

        // Handle file uploads for tree photo
        if (isset($_FILES['tree_photo']) && $_FILES['tree_photo']['error'] == 0) {
            $fileName = time() . "_tree_" . basename($_FILES['tree_photo']['name']);
            $targetFile = $uploadDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            if (!in_array($fileType, $validTypes)) {
                $message = "Only PDF, JPG, and PNG files are allowed for tree photo.";
            } elseif (!move_uploaded_file($_FILES['tree_photo']['tmp_name'], $targetFile)) {
                $message = "Error uploading tree photo.";
            } else {
                $uploadedFiles[] = $fileName; // Store tree photo
            }
        }

        if (isset($_FILES['proof_document']) && $_FILES['proof_document']['error'] == 0) {
            $fileName = time() . "_nic_" . basename($_FILES['proof_document']['name']);
            $targetFile = $uploadDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            if (!in_array($fileType, $validTypes)) {
                $message = "Only PDF, JPG, and PNG files are allowed for NIC.";
            } elseif (!move_uploaded_file($_FILES['proof_document']['tmp_name'], $targetFile)) {
                $message = "Error uploading NIC file.";
            } else {
                $uploadedFiles[] = $fileName; // Store NIC
            }
        }

        if (isset($_FILES['electricity_bill']) && $_FILES['electricity_bill']['error'] == 0) {
            $fileName = time() . "_electricity_bill_" . basename($_FILES['electricity_bill']['name']);
            $targetFile = $uploadDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            if (!in_array($fileType, $validTypes)) {
                $message = "Only PDF, JPG, and PNG files are allowed for electricity bill.";
            } elseif (!move_uploaded_file($_FILES['electricity_bill']['tmp_name'], $targetFile)) {
                $message = "Error uploading electricity bill.";
            } else {
                $uploadedFiles[] = $fileName; // Store electricity bill
            }
        }

        // If files are uploaded successfully, insert the data into the database
        if ($uploadedFiles && !$message) {
            $proofDocumentsJSON = json_encode($uploadedFiles); // Store file names in JSON format

            // Insert form data and uploaded files into the database
            $stmt = $pdo->prepare("INSERT INTO tree_cutting_certificates 
                (username, full_name, dob, nic, tree_name, reason, contact, proof_document, district, divisional_secretariat, category, status) 
                VALUES (:username, :full_name, :dob, :nic, :tree_name, :reason, :contact, :proof_document, :district, :divisional_secretariat, 'tree_cutting', 'Pending')");

            $stmt->bindParam(':username', $_SESSION['username']);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':dob', $dob);
            $stmt->bindParam(':nic', $nic);
            $stmt->bindParam(':tree_name', $tree_name);
            $stmt->bindParam(':reason', $reason);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':proof_document', $proofDocumentsJSON);
            $stmt->bindParam(':district', $district);
            $stmt->bindParam(':divisional_secretariat', $divisional_secretariat);

            if ($stmt->execute()) {
                $message = "Application submitted successfully! Status: Pending approval.";
            } else {
                $message = "Error submitting application.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tree Cutting Certificate Application</title>
<style>
    /* Track App Style for Tree Cutting Certificate Form - No Animations */
    :root {
        --primary: rgba(37, 99, 235, 0.9);
        --secondary: rgba(16, 185, 129, 0.9);
        --card: rgba(255, 255, 255, 0.95);
        --text: #1e293b;
        --text-light: #64748b;
    }

    body { 
        font-family: Arial, sans-serif; 
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

    input, textarea, select { 
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

    input:focus, textarea:focus, select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
        outline: none;
        transform: translateY(-2px);
    }

    input:hover, textarea:hover, select:hover {
        border-color: var(--secondary);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
    }

    textarea { 
        resize: vertical; 
        min-height: 100px;
    }

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

    input[type="checkbox"] { 
        width: auto; 
        margin-right: 12px; 
        transform: scale(1.2);
        accent-color: var(--secondary);
    }

    /* Tree Cutting Specific Styling */
    .tree-reason-select {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(16, 185, 129, 0.2);
        border-radius: 12px;
        padding: 15px 20px;
        font-size: 16px;
        color: var(--text);
        transition: all 0.3s ease;
    }

    .tree-reason-select:hover {
        border-color: var(--secondary);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);
    }

    .tree-reason-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
        outline: none;
        transform: translateY(-2px);
    }

    .file-upload-section {
        background: rgba(16, 185, 129, 0.05);
        padding: 20px;
        border-radius: 15px;
        margin: 20px 0;
        border: 2px solid rgba(16, 185, 129, 0.1);
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

    .tree-info-section {
        background: rgba(37, 99, 235, 0.05);
        padding: 20px;
        border-radius: 15px;
        margin: 20px 0;
        border: 2px solid rgba(37, 99, 235, 0.1);
    }

    .tree-info-section h3 {
        color: var(--text);
        margin-bottom: 15px;
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
    }

    .tree-info-section label {
        color: var(--text);
        font-weight: 600;
        margin-bottom: 8px;
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

        input, textarea, select {
            padding: 12px 15px;
            font-size: 14px;
        }

        button {
            padding: 15px 20px;
            font-size: 16px;
        }

        .file-upload-section, .tree-info-section {
            padding: 15px;
        }
    }
</style>
</head>
<body>

<div class="container">
<h2>Tree Cutting Certificate Application</h2>

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
        <h3>Tree Cutting Certificate Guidelines</h3>
        <p><strong>Full Name:</strong> Enter your legal full name as per your official documents.</p>
        <p><strong>Date of Birth:</strong> Enter your date of birth in the correct format (YYYY-MM-DD).</p>
        <p><strong>NIC/Passport Number:</strong> Enter your National Identity Card or Passport number (minimum 12 characters).</p>
        <p><strong>Contact Number:</strong> Enter a valid 10-digit contact number.</p>
        <p><strong>Tree Name:</strong> Specify the exact name/type of tree you want to cut.</p>
        <p><strong>Reason for Cutting:</strong> Select the appropriate reason from the dropdown menu.</p>
        <p><strong>Tree Photo:</strong> Upload a clear photo of the tree you want to cut.</p>
        <p><strong>NIC Document:</strong> Upload a clear copy of your NIC or Passport.</p>
        <p><strong>Electricity Bill:</strong> Upload a recent electricity bill as proof of residence.</p>
        <p><strong>Declaration:</strong> Check the declaration box to confirm all information is accurate.</p>
    </div>
</div>

<?php if($message): ?>
<div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">

    <!-- Province Dropdown -->
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

    <!-- District Dropdown -->
    <label>District:</label>
    <select name="district" id="district" onchange="updateDivisions()" required>
        <option value="">Select District</option>
    </select>

    <!-- Divisional Secretariat Dropdown -->
    <label>Divisional Secretariat:</label>
    <select name="divisional_secretariat" id="divisional_secretariat" required>
        <option value="">Select Divisional Secretariat</option>
    </select>
    
    <label>Full Name:</label>
    <input type="text" name="full_name" required>

    <label>Date of Birth:</label>
    <input type="date" name="dob" required>

    <label>NIC / Passport Number (min 12 characters):</label>
    <input type="text" name="nic" required>

    <label>Contact Number (10 digits):</label>
    <input type="tel" name="contact" required>

    <label>Tree Name:</label>
    <input type="text" name="tree_name" required>

    <label>Reason for Cutting the Tree:</label>
    <select name="reason" required>
        <option value="Dead Tree">Dead Tree</option>
        <option value="Construction">Construction</option>
        <option value="Safety Hazard">Safety Hazard</option>
        <option value="Space for Planting">Space for Planting</option>
        <option value="Other">Other</option>
    </select>

    <label>Upload Tree Photo:</label>
    <input type="file" name="tree_photo" accept=".jpg,.jpeg,.png,.pdf" required>

    <label>Upload NIC:</label>
    <input type="file" name="proof_document" accept=".pdf,.jpg,.jpeg,.png" required>

    <label>Upload Electricity Bill:</label>
    <input type="file" name="electricity_bill" accept=".jpg,.jpeg,.png,.pdf" required>

    <label><input type="checkbox" name="declaration" required> I hereby declare that the information provided is true.</label>

    <button type="submit">Submit Application</button>
</form>

<div class="back-btn">
    <form action="customer_dashboard.php" method="POST">
        <button type="submit">Back to Dashboard</button>
    </form>
</div>

</div>

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
    "Polonnaruwa": ["Polonnaruwa", "Dimbulagala", "Hingurakgoda", "Lankapura"],
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

    divisionalSelect.innerHTML = "<option value=''>Select Divisional Secretariat</option>";
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

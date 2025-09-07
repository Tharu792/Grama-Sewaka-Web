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
    $deceased_name = $_POST['deceased_name'];
    $deceased_age = $_POST['deceased_age'];
    $deceased_address = $_POST['deceased_address'];
    $contact_person_name = $_POST['contact_person_name'];
    $contact_person_number = $_POST['contact_person_number'];
    $death_date = $_POST['death_date'];
    $district = $_POST['district']; // District field added
    $divisional_secretariat = $_POST['divisional_secretariat']; // Divisional Secretariat field added

    // Validate Contact Number (must be 10 digits)
    if (!preg_match("/^\d{10}$/", $contact_person_number)) {
        $message = "Contact number must be exactly 10 digits.";
    }
    // Initialize file upload variables
    $uploadDir = 'uploads/death_registrations/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Prepare to store uploaded files
    $uploadedFiles = [];
    $validTypes = ['pdf', 'jpg', 'jpeg', 'png'];

    // Handle file uploads for proof document (death certificate)
    if (isset($_FILES['proof_document']) && $_FILES['proof_document']['error'] == 0) {
        $fileName = time() . "_proof_" . basename($_FILES['proof_document']['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (!in_array($fileType, $validTypes)) {
            $message = "Only PDF, JPG, and PNG files are allowed for proof document.";
        } elseif (!move_uploaded_file($_FILES['proof_document']['tmp_name'], $targetFile)) {
            $message = "Error uploading proof document.";
        } else {
            $uploadedFiles[] = $fileName; // Store proof document
        }
    }

    // If files are uploaded successfully, insert the data into the database
    if ($uploadedFiles && !$message) {
        $proofDocumentsJSON = json_encode($uploadedFiles); // Store file names in JSON format

        // Insert form data and uploaded files into the database
        $stmt = $pdo->prepare("INSERT INTO death_registrations 
            (username, deceased_name, deceased_age, deceased_address, contact_person_name, contact_person_number, death_date, proof_document, district, divisional_secretariat, status) 
            VALUES (:username, :deceased_name, :deceased_age, :deceased_address, :contact_person_name, :contact_person_number, :death_date, :proof_document, :district, :divisional_secretariat, 'Pending')");

        $stmt->bindParam(':username', $_SESSION['username']);
        $stmt->bindParam(':deceased_name', $deceased_name);
        $stmt->bindParam(':deceased_age', $deceased_age);
        $stmt->bindParam(':deceased_address', $deceased_address);
        $stmt->bindParam(':contact_person_name', $contact_person_name);
        $stmt->bindParam(':contact_person_number', $contact_person_number);
        $stmt->bindParam(':death_date', $death_date);
        $stmt->bindParam(':proof_document', $proofDocumentsJSON);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':divisional_secretariat', $divisional_secretariat);

        if ($stmt->execute()) {
            $message = "Death registration submitted successfully! Status: Pending approval.";
        } else {
            $message = "Error submitting registration.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Death Registration Application</title>
<style>
    /* Track App Style for Death Registration Form - No Animations */
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

    input[type="number"] {
        -moz-appearance: textfield;
    }

    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Death Registration Specific Styling */
    .deceased-info-section {
        background: rgba(16, 185, 129, 0.05);
        padding: 20px;
        border-radius: 15px;
        margin: 20px 0;
        border: 2px solid rgba(16, 185, 129, 0.1);
    }

    .deceased-info-section h3 {
        color: var(--text);
        margin-bottom: 15px;
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
    }

    .deceased-info-section label {
        color: var(--text);
        font-weight: 600;
        margin-bottom: 8px;
    }

    .contact-info-section {
        background: rgba(37, 99, 235, 0.05);
        padding: 20px;
        border-radius: 15px;
        margin: 20px 0;
        border: 2px solid rgba(37, 99, 235, 0.1);
    }

    .contact-info-section h3 {
        color: var(--text);
        margin-bottom: 15px;
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
    }

    .contact-info-section label {
        color: var(--text);
        font-weight: 600;
        margin-bottom: 8px;
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

        .deceased-info-section, .contact-info-section, .file-upload-section {
            padding: 15px;
        }
    }
</style>
</head>
<body>

<div class="container">
<h2>Death Registration Application</h2>

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
        <h3>Death Registration Guidelines</h3>
        <p><strong>Province/District/Divisional Secretariat:</strong> Select the location where the death occurred from the dropdown menus.</p>
        <p><strong>Deceased Full Name:</strong> Enter the complete legal name of the deceased person.</p>
        <p><strong>Deceased Age:</strong> Enter the age of the deceased at the time of death.</p>
        <p><strong>Deceased Address:</strong> Provide the complete address where the deceased was residing.</p>
        <p><strong>Contact Person Name:</strong> Enter the name of the person submitting this registration.</p>
        <p><strong>Contact Person Number:</strong> Enter a valid 10-digit contact number for the person submitting.</p>
        <p><strong>Death Date:</strong> Enter the date of death in the correct format (YYYY-MM-DD).</p>
        <p><strong>Proof Document:</strong> Upload the official death certificate or medical certificate.</p>
        <p><strong>File Formats:</strong> Only PDF, JPG, JPEG, and PNG files are accepted.</p>
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
    
    <label>Deceased Full Name:</label>
    <input type="text" name="deceased_name" required>

    <label>Deceased Age:</label>
    <input type="number" name="deceased_age" required>

    <label>Deceased Address:</label>
    <textarea name="deceased_address" required></textarea>

    <label>Contact Person Name:</label>
    <input type="text" name="contact_person_name" required>

    <label>Contact Person Number (10 digits):</label>
    <input type="tel" name="contact_person_number" required>

    <label>Death Date:</label>
    <input type="date" name="death_date" required>


    <label>Upload Proof Document (Death Certificate):</label>
    <input type="file" name="proof_document" accept=".jpg,.jpeg,.png,.pdf" required>

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

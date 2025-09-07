<?php
// Start the session
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

// Fetch the customer's profile information
$stmt = $pdo->prepare("SELECT * FROM customers WHERE username = :username");
$stmt->bindParam(":username", $customer_username);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ================== Beautiful Customer Dashboard Interface ================== */
        
        /* CSS Variables for Custom Color Palette */
        :root {
            --primary: rgba(37, 99, 235, 0.9);
            --secondary: rgba(16, 185, 129, 0.9);
            --card: rgba(255, 255, 255, 0.95);
            --text: #1e293b;
            --text-light: #64748b;
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body { 
        font-family: Arial, sans-serif; 
        background: url('../images/e.jpg') no-repeat center center fixed;
        background-size: cover;
        color: white;
        line-height: 1.6;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
        margin: 0; 
        padding: 0; 
    }


       
        /* Enhanced Header */
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
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
            border-bottom: 4px solid rgba(255, 255, 255, 0.9);
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
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary);
        }

        .middle-nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--secondary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 3px;
        }

        .middle-nav a:hover::after {
            width: 70%;
        }

        .right-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .right-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
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

        #language-select {
            padding: 8px 15px;
            border-radius: 12px;
            border: 2px solid rgba(16, 185, 129, 0.1);
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        #language-select:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        /* Main Content */
        .dashboard-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }

        /* Enhanced Welcome Section */
        h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: slideInUp 1s ease-out;
            text-align: center;
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            height: 4px;
            background: var(--secondary);
            border-radius: 2px;
        }

      /* Enhanced Profile Section */
.profile-section {
    background: var(--card);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 40px;
    position: relative;
    overflow: hidden;

    /* 👇 Force text to black */
    color: black;
}


        .profile-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.05), transparent);
            transition: left 0.6s ease;
        }

        .profile-section:hover::before {
            left: 100%;
        }

        .profile-section h2 {
            font-size: 2.2rem;
            color: var(--text);
            margin-bottom: 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            padding-left: 20px;
        }

        .profile-section h2::before {
            content: '👤';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
        }

        .profile-data {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .profile-data p {
            background: rgba(16, 185, 129, 0.05);
            padding: 20px;
            border-radius: 15px;
            font-size: 1.1rem;
            display: flex;
            flex-direction: column;
            border-left: 4px solid var(--secondary);
            transition: all 0.3s ease;
        }

        .profile-data p:hover {
            background: rgba(16, 185, 129, 0.1);
            transform: translateX(5px);
        }

        .profile-data strong {
            color: var(--primary);
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Enhanced Services Section */
        .services {
            margin-top: 40px;
        }

        .services h2 {
            font-size: 2.5rem;
            color: black;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .services h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--secondary);
            border-radius: 2px;
        }

        .services > p {
            text-align: center;
            margin-bottom: 40px;
            color: rgba(31, 4, 4, 0.95);
            font-size: 1.2rem;
            font-weight: 500;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Enhanced Service Cards Grid */
        .service-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 30px;
        }

        /* Enhanced Card Styling */
        .service-card {
            background: var(--card);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.05), transparent);
            transition: left 0.6s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.15);
        }

        .service-card:hover::before {
            left: 100%;
        }

        .icon {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            font-size: 2.5rem;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .icon::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        .service-card-content {
            padding: 30px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .service-card h3 {
            font-size: 1.6rem;
            color: var(--text);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .service-card > p {
            color: var(--text-light);
            margin-bottom: 20px;
            line-height: 1.6;
            font-weight: 500;
        }

        .service-card ul {
            margin: 20px 0;
            padding-left: 20px;
            flex-grow: 1;
        }

        .service-card li {
            margin-bottom: 10px;
            color: var(--text-light);
            font-weight: 500;
        }

        .service-card strong {
            color: var(--primary);
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            margin-top: auto;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(16, 185, 129, 0.4);
        }

        /* Enhanced Animations */
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

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .service-card {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .service-card:nth-child(1) { animation-delay: 0.1s; }
        .service-card:nth-child(2) { animation-delay: 0.2s; }
        .service-card:nth-child(3) { animation-delay: 0.3s; }
        .service-card:nth-child(4) { animation-delay: 0.4s; }
        .service-card:nth-child(5) { animation-delay: 0.5s; }
        .service-card:nth-child(6) { animation-delay: 0.6s; }
        .service-card:nth-child(7) { animation-delay: 0.7s; }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .service-cards {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }

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
            
            h1 {
                font-size: 2.5rem;
            }
            
            .services h2 {
                font-size: 2rem;
            }
            
            .profile-section {
                padding: 25px;
            }
            
            .service-cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .middle-nav ul {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .services h2 {
                font-size: 1.8rem;
            }
            
            .profile-section {
                padding: 20px;
            }
            
            .dashboard-container {
                padding: 0 1rem;
            }
        }

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="logo">
            <img src="../images/LOGO.jpg" alt="e-GramaSewa Logo">
        </div>
        <nav class="middle-nav">
            <ul>
                <li><a href="../pages/Home.html">Home</a></li>
                <li><a href="../pages/About.html">About</a></li>
                <li><a href="customer_track_app.php">My Application</a></li>
                <li><a href="../pages/contact.html">Contact</a></li>
            </ul>
        </nav>
        <div class="right-nav">
             <a href="customer_my_profile.php">My Profile</a>
            <a href="logout.php" class="login-btn">Logout</a>
            
        </div>
    </header>

    <div class="dashboard-container">
        <h1>Welcome, <?php echo htmlspecialchars($customer['name']); ?>!</h1>

        <!-- Customer Profile Section -->
        <div class="profile-section">
            <h2>Your Profile</h2>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($customer['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['tel_no']); ?></p>
        </div>

        <!-- Grama Sevaka Services Section -->
        <section class="services">
            <h2>Grama Sevaka Services</h2>
            <p>Select the service you need and apply online.</p>

            <div class="service-cards">
                <!-- Residence Certificate -->
                <div class="service-card">
                    <div class="icon"><i class="fa-solid fa-house"></i></div>
                    <h3>Residence Certificate</h3>
                    <p>Verify your residence for official purposes.</p>
                    <ul>
                        <li><strong>Eligibility:</strong> Residents of specific Grama Niladari division</li>
                        <li><strong>Required Documents:</strong> Proof of address (utility bill, rental agreement, etc.)</li>
                        <li><strong>How to Apply:</strong> Fill out the online form and upload documents</li>
                        <li><strong>Timeline:</strong> 3-5 working days</li>
                    </ul>
                    <a href="residence-certificate.php" class="btn primary">Apply Now</a>
                </div>

                <!-- Character Certificate -->
                <div class="service-card">
                    <div class="icon"><i class="fa-solid fa-id-card"></i></div>
                    <h3>Character Certificate</h3>
                    <p>Issued for employment or official verification.</p>
                    <ul>
                        <li><strong>Eligibility:</strong> Individuals requiring proof of character</li>
                        <li><strong>Required Documents:</strong> National ID, Police clearance if needed</li>
                        <li><strong>How to Apply:</strong> Complete the online form</li>
                        <li><strong>Timeline:</strong> 5-7 working days</li>
                    </ul>
                    <a href="character-certificate-form.php" class="btn primary">Apply Now</a>
                </div>

                <!-- Income Certificate -->
                <div class="service-card">
                    <div class="icon"><i class="fa-solid fa-money-bill"></i></div>
                    <h3>Income Certificate</h3>
                    <p>Verification of your income for official purposes.</p>
                    <ul>
                        <li><strong>Eligibility:</strong> Employed individuals or businesses</li>
                        <li><strong>Required Documents:</strong> Pay slips, Tax returns (if applicable)</li>
                        <li><strong>How to Apply:</strong> Fill out the online form and attach documents</li>
                        <li><strong>Timeline:</strong> 5-7 working days</li>
                    </ul>
                    <a href="income-certificate-form.php" class="btn primary">Apply Now</a>
                </div>

                <!-- Land Use Certificate -->
                <div class="service-card">
                    <div class="icon"><i class="fa-solid fa-map-marked-alt"></i></div>
                    <h3>Land Use Certificate</h3>
                    <p>Confirm the permitted use of your land.</p>
                    <ul>
                        <li><strong>Eligibility:</strong> Landowners or developers</li>
                        <li><strong>Required Documents:</strong> Land title, Purpose of land use</li>
                        <li><strong>How to Apply:</strong> Submit the form online and attach documents</li>
                        <li><strong>Timeline:</strong> 7-10 working days</li>
                    </ul>
                    <a href="land-use-certificate-form.php" class="btn primary">Apply Now</a>
                </div>

                <!-- Tree Cutting Certificate Section -->
                <div class="service-card">
                    <div class="icon"><i class="fa-solid fa-tree"></i> <!-- Updated icon for tree cutting --></div>
                    <h3>Tree Cutting Certificate</h3>
                    <p>This certificate confirms the permitted use of your land for tree cutting activities.</p>
                    <ul>
                        <li><strong>Eligibility:</strong> Landowners or developers with trees on their property.</li>
                        <li><strong>Required Documents:</strong> Land title, Purpose of land use, and Tree cutting plan.</li>
                        <li><strong>How to Apply:</strong> Complete the online form and submit the required documents.</li>
                        <li><strong>Processing Time:</strong> 7-10 working days.</li>
                    </ul>
                    <a href="tree-cutting-certificate-form.php" class="btn primary">Apply Now</a>
                </div>



                <!-- Death Registration -->
                <div class="service-card">
                    <div class="icon"><i class="fa-solid fa-cross"></i></div>
                    <h3>Death Registration</h3>
                    <p>Register a death and issue the certificate if required.</p>
                    <ul>
                        <li><strong>Eligibility:</strong> Family member or legal representative</li>
                        <li><strong>Required Documents:</strong> Medical certificate, National ID of deceased</li>
                        <li><strong>How to Apply:</strong> Fill out the online form and upload documents</li>
                        <li><strong>Timeline:</strong> 3-7 working days</li>
                    </ul>
                    <a href="death-registration-form.php" class="btn primary">Apply Now</a>
                </div>


            <!-- National ID Verification -->
            <div class="service-card">
                <div class="icon"><i class="fa-solid fa-id-card-clip"></i></div>
                     <h3>National ID Verification</h3>
                    <p>Obtain or verify details required to get your National Identity Card.</p>
                 <ul>
                      <li><strong>Eligibility:</strong> Sri Lankan citizens</li>
                      <li><strong>Required Documents:</strong> Birth certificate, Proof of residence</li>
                      <li><strong>How to Apply:</strong> Fill out the online form and upload documents</li>
                     <li><strong>Timeline:</strong> 5-7 working days</li>
                 </ul>
                 <a href="national-id-verification-form.php" class="btn primary">Apply Now</a>
            </div>

            </div>
        </section>

    </div>
</body>
</html>

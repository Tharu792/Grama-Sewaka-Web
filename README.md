# e-GramaSewa - Digital Government Services Platform

![e-GramaSewa Logo](images/LOGO.jpg)

## 🌟 Overview

**e-GramaSewa** is a comprehensive digital platform that provides Sri Lankan citizens with easy access to essential government services and certificates. This web-based application streamlines the process of applying for various government documents, making government services more accessible, efficient, and transparent.

## 🚀 Features

### Core Functionality
- **User Registration & Authentication** - Secure user account management
- **Multi-language Support** - English and Sinhala language options
- **Real-time Application Tracking** - Monitor application status
- **Document Upload System** - Secure file handling for required documents
- **AI-Powered Chatbot** - 24/7 customer support assistance
- **Responsive Design** - Mobile-friendly interface

### Available Services
- 🏠 **Residence Certificate** - Proof of residence documentation
- 🆔 **Character Certificate** - Character verification for employment
- 💰 **Income Certificate** - Income verification documents
- 🗺️ **Land Use Certificate** - Land use confirmation
- 🌳 **Tree Cutting Certificate** - Environmental clearance
- ⚰️ **Death Registration** - Death certificate processing
- 🆔 **NIC Verification** - National ID verification
- 👥 **Family Composition Certificate** - Family member documentation
- ❤️ **Living Certificate** - Living status verification

## 🛠️ Technology Stack

### Frontend
- **HTML5** - Semantic markup structure
- **CSS3** - Modern styling with animations and responsive design
- **JavaScript** - Interactive functionality and chatbot
- **Font Awesome** - Icon library for enhanced UI

### Backend
- **PHP** - Server-side scripting and logic
- **MySQL** - Database management
- **PDO** - Secure database interactions

### Development Environment
- **XAMPP** - Local development server
- **Apache** - Web server
- **MySQL** - Database server

## 📁 Project Structure

```
e-GramaSewa/
├── css/
│   └── style.css                 # Main stylesheet
├── images/                       # Logo and UI images
├── js/
│   └── script.js                # JavaScript functionality
├── pages/                       # HTML pages
│   ├── Home.html               # Landing page
│   ├── About.html              # About page
│   ├── Service.html            # Services overview
│   ├── contact.html            # Contact information
│   ├── help.html               # Help and support
│   ├── login_form.html         # Login interface
│   ├── track_app.html          # Application tracking
│   └── User_Manual.html        # User documentation
├── php/                        # Backend PHP files
│   ├── register.php            # User registration
│   ├── login.php               # User authentication
│   ├── customer_dashboard.php  # User dashboard
│   ├── admin_dashboard.php     # Admin panel
│   ├── grama_sewaka_dashboard.php # Grama Sewaka panel
│   ├── *-certificate-form.php  # Service application forms
│   ├── uploads/                # Document storage
│   └── ...                     # Additional backend files
└── README.md                   # Project documentation
```

## 🚀 Installation & Setup

### Prerequisites
- XAMPP (Apache, MySQL, PHP)
- Web browser (Chrome, Firefox, Safari, Edge)
- Text editor (VS Code, Sublime Text, etc.)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/e-GramaSewa.git
   ```

2. **Setup XAMPP**
   - Download and install [XAMPP](https://www.apachefriends.org/)
   - Start Apache and MySQL services

3. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `grama_sewa`
   - Import the database schema (if available)

4. **Project Configuration**
   - Copy the project folder to `C:\xampp\htdocs\e-GramaSewa`
   - Update database connection settings in PHP files if needed

5. **Access the Application**
   - Open your browser
   - Navigate to `http://localhost/e-GramaSewa/pages/Home.html`

## 👥 User Roles

### 1. **Citizens/Customers**
- Register and create accounts
- Apply for various certificates
- Track application status
- Upload required documents
- Access customer support

### 2. **Grama Sewaka (Village Officers)**
- Review citizen applications
- Approve or reject applications
- Manage application workflow
- Generate reports

### 3. **Administrators**
- System administration
- User management
- Application oversight
- System configuration

## 🔧 Configuration

### Database Configuration
Update the database connection settings in PHP files:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grama_sewa";
```

### File Upload Settings
- Maximum file size: 5MB per document
- Supported formats: PDF, JPG, PNG
- Upload directory: `php/uploads/`

## 📱 Features in Detail

### User Interface
- **Modern Design** - Clean, professional interface
- **Responsive Layout** - Works on desktop, tablet, and mobile
- **Accessibility** - WCAG compliant design
- **Multi-language** - English and Sinhala support

### Security Features
- **Password Encryption** - Secure password hashing
- **Session Management** - Secure user sessions
- **File Validation** - Document type and size validation
- **SQL Injection Protection** - PDO prepared statements

### Application Workflow
1. **Registration** - User creates account
2. **Login** - Secure authentication
3. **Service Selection** - Choose required certificate
4. **Form Submission** - Complete application form
5. **Document Upload** - Submit required documents
6. **Review Process** - Grama Sewaka reviews application
7. **Status Updates** - Real-time status tracking
8. **Certificate Generation** - Digital certificate delivery

## 🤖 AI Chatbot Features

The integrated chatbot provides:
- **24/7 Support** - Always available assistance
- **Multi-language Support** - English and Sinhala responses
- **Intelligent Responses** - Context-aware answers
- **Quick Actions** - Pre-defined helpful actions
- **Service Guidance** - Step-by-step assistance

## 📊 System Requirements

### Server Requirements
- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- **Apache** 2.4 or higher
- **Storage** Minimum 1GB for uploads

### Browser Compatibility
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## 🔒 Security Considerations

- All passwords are hashed using PHP's `password_hash()` function
- SQL queries use prepared statements to prevent injection
- File uploads are validated for type and size
- Session management includes proper timeout handling
- CSRF protection implemented for forms

## 🚀 Deployment

### Production Deployment
1. **Server Setup**
   - Configure production web server (Apache/Nginx)
   - Setup MySQL database
   - Configure SSL certificate

2. **Environment Configuration**
   - Update database credentials
   - Configure file upload limits
   - Setup email notifications

3. **Security Hardening**
   - Enable HTTPS
   - Configure firewall rules
   - Regular security updates

## 📈 Future Enhancements

- [ ] Mobile application (Android/iOS)
- [ ] SMS notifications
- [ ] Email integration
- [ ] Digital signature support
- [ ] Advanced reporting dashboard
- [ ] API integration for external services
- [ ] Blockchain-based certificate verification

## 🤝 Contributing

We welcome contributions to improve e-GramaSewa! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📞 Support & Contact

- **Email**: support@egramasewa.lk
- **Hotline**: 1919
- **Address**: Government Office, Colombo, Sri Lanka
- **Website**: [e-GramaSewa Platform]

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Sri Lankan Government for digital transformation initiatives
- Open source community for various libraries and tools
- Contributors and testers who helped improve the platform

## 📊 Project Status

![GitHub last commit](https://img.shields.io/github/last-commit/yourusername/e-GramaSewa)
![GitHub issues](https://img.shields.io/github/issues/yourusername/e-GramaSewa)
![GitHub pull requests](https://img.shields.io/github/issues-pr/yourusername/e-GramaSewa)
![GitHub stars](https://img.shields.io/github/stars/yourusername/e-GramaSewa)

---

**Made with ❤️ for the people of Sri Lanka**

*Empowering citizens through digital government services*

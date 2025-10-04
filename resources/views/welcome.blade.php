<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MCC-NAC Portal System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="welcome-page">
    <!-- Header Navigation -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo-section">
                <div class="logo-circle">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span class="logo-text">MCC-NAC</span>
            </div>
            
            <nav class="main-nav">
                <a href="{{ route('login') }}" class="nav-link">Login</a>
                <a href="{{ route('ms365.signup') }}" class="nav-link signup-btn">Signup</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-background">
            <img src="{{ asset('images/mccfront.jpg') }}" alt="Madridejos Community College" class="hero-image">
            <div class="hero-overlay"></div>
        </div>
        
        <div class="hero-content">
            <div class="hero-text">
                <h1>Welcome to MCC-NAC Portal</h1>
                <p>Madridejos Community College News Aggregator with Chatbot</p>
                <p class="hero-subtitle">Access your academic resources and stay connected with campus life</p>
            </div>
        </div>
    </section>

    <!-- Learning Begins Section -->
    <section class="learning-section">
        <div class="container">
            <div class="learning-content">
                <div class="learning-text-left slide-in-left">
                    <h2>Excellence in Education at MCC</h2>
                    <p>At Madridejos Community College, we are committed to providing quality higher education that empowers students to achieve their academic and professional goals. Located in the heart of Madridejos, Cebu, we foster an environment of academic excellence, innovation, and community service that prepares our graduates to become leaders in their chosen fields.</p>
                </div>
                <div class="learning-text-right slide-in-right">
                    <p>Our comprehensive programs in Information Technology, Business Administration, Education, and Hospitality Management are designed to meet the evolving needs of today's workforce. Through hands-on learning, industry partnerships, and dedicated faculty mentorship, MCC students graduate with the skills, knowledge, and confidence to excel in their careers and contribute meaningfully to their communities.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Curriculum Overview Section -->
    <section class="curriculum-section">
        <div class="container">
            <div class="curriculum-header">
                <div class="curriculum-title slide-in-left">
                    <h2>Curriculum Overview</h2>
                </div>
                <div class="curriculum-description slide-in-right">
                    <p>Our diverse academic programs are carefully crafted to provide students with both theoretical knowledge and practical skills. From cutting-edge technology courses to comprehensive teacher education programs, MCC offers a well-rounded educational experience that prepares graduates for success in the modern workplace and lifelong learning.</p>
                </div>
            </div>
            
            <div class="programs-grid">
                <div class="program-card fade-in-up" data-delay="0.2">
                    <div class="program-image">
                        <img src="{{ asset('images/BSIT.jpg') }}" alt="BSIT Program" class="program-img">
                        <div class="program-department" style="color:#1e40af;">BSIT</div>
                        <div class="program-overlay">
                            <div class="overlay-content">
                                <h4>Learn More</h4>
                                <p>Discover our comprehensive IT curriculum</p>
                            </div>
                        </div>
                    </div>
                    <div class="program-info">
                        <h3>Bachelor of Science in Information Technology</h3>
                        <p>Comprehensive IT education covering programming, systems analysis, and emerging technologies.</p>
                    </div>
                </div>
                
                <div class="program-card fade-in-up" data-delay="0.4">
                    <div class="program-image">
                        <img src="{{ asset('images/BSBA.jpg') }}" alt="BSBA Program" class="program-img">
                        <div class="program-department" style="color:#be185d;">BSBA</div>
                        <div class="program-overlay">
                            <div class="overlay-content">
                                <h4>Learn More</h4>
                                <p>Explore business administration opportunities</p>
                            </div>
                        </div>
                    </div>
                    <div class="program-info">
                        <h3>Bachelor of Science in Business Administration</h3>
                        <p>Strategic business education focusing on management, marketing, and entrepreneurship.</p>
                    </div>
                </div>
                
                <div class="program-card fade-in-up" data-delay="0.6">
                    <div class="program-image">
                        <img src="{{ asset('images/BEED.jpg') }}" alt="BEED Program" class="program-img">
                        <div class="program-department" style="color:#ea580c;">BEED</div>
                        <div class="program-overlay">
                            <div class="overlay-content">
                                <h4>Learn More</h4>
                                <p>Shape young minds through education</p>
                            </div>
                        </div>
                    </div>
                    <div class="program-info">
                        <h3>Bachelor of Elementary Education</h3>
                        <p>Comprehensive teacher training program for elementary education professionals.</p>
                    </div>
                </div>
                
                <div class="program-card fade-in-up" data-delay="0.8">
                    <div class="program-image">
                        <img src="{{ asset('images/BSED.jpg') }}" alt="BSED Program" class="program-img">
                        <div class="program-department" style="color:#1e40af;">BSED</div>
                        <div class="program-overlay">
                            <div class="overlay-content">
                                <h4>Learn More</h4>
                                <p>Advanced secondary education training</p>
                            </div>
                        </div>
                    </div>
                    <div class="program-info">
                        <h3>Bachelor of Secondary Education</h3>
                        <p>Advanced teacher preparation for secondary school educators across various subjects.</p>
                    </div>
                </div>
                
                <div class="program-card fade-in-up" data-delay="1.0">
                    <div class="program-image">
                        <img src="{{ asset('images/BSHM.jpg') }}" alt="BSHM Program" class="program-img">
                        <div class="program-department" style="color:#16a34a;">BSHM</div>
                        <div class="program-overlay">
                            <div class="overlay-content">
                                <h4>Learn More</h4>
                                <p>Excellence in hospitality management</p>
                            </div>
                        </div>
                    </div>
                    <div class="program-info">
                        <h3>Bachelor of Science in Hospitality Management</h3>
                        <p>Professional training in hotel management, tourism, and hospitality services.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portal Selection Section -->
    <section class="portal-section">
        <div class="container">
            <div class="section-header">
                <h2>Choose Your Portal</h2>
                <p>Select the appropriate portal to access your account and resources</p>
            </div>
            
            <div class="portal-cards">
                <div class="portal-card admin-portal">
                    <div class="portal-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="portal-info">
                        <h3>Admin Portal</h3>
                        <p>Manage system content, users, and administrative functions</p>
                        <ul class="portal-features">
                            <li><i class="fas fa-check"></i> Content Management</li>
                            <li><i class="fas fa-check"></i> User Administration</li>
                            <li><i class="fas fa-check"></i> System Analytics</li>
                        </ul>
                    </div>
                    <div class="portal-actions">
                        <a href="{{ route('login') }}?type=superadmin" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Admin Login
                        </a>

                    </div>
                </div>
                
                <div class="portal-card user-portal">
                    <div class="portal-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="portal-info">
                        <h3>Student/Faculty Portal</h3>
                        <p>Access announcements, events, news, and academic resources</p>
                        <ul class="portal-features">
                            <li><i class="fas fa-check"></i> View Announcements</li>
                            <li><i class="fas fa-check"></i> Campus Events</li>
                            <li><i class="fas fa-check"></i> Latest News</li>
                        </ul>
                    </div>
                    <div class="portal-actions">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Student Login
                        </a>
                        <a href="{{ route('ms365.signup') }}" class="btn btn-outline">
                            <i class="fas fa-user-plus"></i> Register Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2>Portal Features</h2>
                <p>Discover what our portal system offers</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h4>Announcements</h4>
                    <p>Stay updated with the latest campus announcements and important notices from Madridejos Community College administration and faculty.</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h4>Events</h4>
                    <p>Never miss important campus events, seminars, and academic activities</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h4>News</h4>
                    <p>Read the latest news and updates from the college community</p>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Community</h4>
                    <p>Connect with fellow students, faculty, and staff members</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <div class="logo-circle">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <span class="logo-text">MCC-NAC</span>
                    </div>
                    <p>Madridejos Community College<br>News Aggregator with Chatbot</p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('ms365.signup') }}">Register</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Bunakan, Madridejos, Cebu</p>
                    <p><i class="fas fa-envelope"></i> info@mcc-nac.edu.ph</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Madridejos Community College. All rights reserved.</p>
            </div>
        </div>
    </footer>

@include('components.chatbot-widget')
</div>

<style>
/* Welcome Page Styles */
.welcome-page {
    min-height: 100vh;
    background: #f8fafc;
}

/* Header Styles */
.main-header {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    padding: 1rem 0;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.logo-circle {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1e40af;
    font-size: 1.25rem;
}

.logo-text {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: 0.05em;
}

.main-nav {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.nav-link {
    color: white;
    text-decoration: none;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.signup-btn {
    background: #10b981;
    color: white !important;
    font-weight: 600;
}

.signup-btn:hover {
    background: #059669;
    transform: translateY(-1px);
}

/* Hero Section */
.hero-section {
    position: relative;
    height: 70vh;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
    max-width: 800px;
    padding: 0 2rem;
}

.hero-text h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-text p {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.hero-subtitle {
    font-size: 1.125rem !important;
    opacity: 0.9;
    margin-top: 1rem !important;
}

/* Common Styles */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
}

.btn-primary {
    background: #1e40af;
    color: white;
}

.btn-primary:hover {
    background: #1e3a8a;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.4);
}

.btn-outline {
    background: transparent;
    color: #1e40af;
    border: 2px solid #1e40af;
}

.btn-outline:hover {
    background: #1e40af;
    color: white;
    transform: translateY(-2px);
}

/* Portal Section */
.portal-section {
    padding: 5rem 0;
    background: white;
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
}

.section-header p {
    font-size: 1.125rem;
    color: #6b7280;
    max-width: 600px;
    margin: 0 auto;
}

.portal-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 3rem;
    max-width: 1000px;
    margin: 0 auto;
}

.portal-card {
    background: white;
    border-radius: 1rem;
    padding: 2.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.portal-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #1e40af, #3b82f6);
}

.portal-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.portal-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin-bottom: 2rem;
}

.portal-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
}

.portal-info p {
    color: #6b7280;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.portal-features {
    list-style: none;
    padding: 0;
    margin-bottom: 2rem;
}

.portal-features li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    color: #374151;
}

.portal-features i {
    color: #10b981;
    font-size: 0.875rem;
}

.portal-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Features Section */
.features-section {
    padding: 5rem 0;
    background: #f8fafc;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}

.feature-item {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #1e40af, #3b82f6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin: 0 auto 1.5rem;
}

.feature-item h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
}

.feature-item p {
    color: #6b7280;
    line-height: 1.6;
}

/* Footer */
.main-footer {
    background: #1f2937;
    color: white;
    padding: 3rem 0 1rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section h4 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: white;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section ul li a {
    color: #d1d5db;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-section ul li a:hover {
    color: white;
}

.footer-section p {
    color: #d1d5db;
    line-height: 1.6;
    margin-bottom: 0.5rem;
}

.footer-section i {
    margin-right: 0.5rem;
    color: #3b82f6;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.footer-bottom {
    border-top: 1px solid #374151;
    padding-top: 2rem;
    text-align: center;
}

.footer-bottom p {
    color: #9ca3af;
    margin: 0;
}

/* Learning Begins Section */
.learning-section {
    padding: 5rem 0;
    background: #f8fafc;
}

.learning-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: start;
}

.learning-text-left h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 2rem;
    line-height: 1.2;
}

.learning-text-left p,
.learning-text-right p {
    font-size: 1rem;
    line-height: 1.8;
    color: #4b5563;
    text-align: justify;
}

/* Curriculum Section */
.curriculum-section {
    padding: 5rem 0;
    background: white;
}

.curriculum-header {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    margin-bottom: 4rem;
    align-items: start;
}

.curriculum-title h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1.2;
}

.curriculum-description p {
    font-size: 1rem;
    line-height: 1.8;
    color: #4b5563;
    text-align: justify;
}

.programs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin-bottom: 4rem;
    padding: 0 1rem;
    align-items: stretch;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
    justify-items: center;
}

.programs-grid .program-card:nth-child(4),
.programs-grid .program-card:nth-child(5) {
    grid-column: span 1;
    width: 100%;
    height: 500px;
    max-width: 100%;
    box-sizing: border-box;
}

.programs-grid .program-card:nth-child(4) {
    grid-column: 1 / 2;
    grid-row: 2;
    justify-self: end;
    margin-right: 1rem;
}

.programs-grid .program-card:nth-child(5) {
    grid-column: 3 / 4;
    grid-row: 2;
    justify-self: start;
    margin-left: 1rem;
}

/* Ensure all program cards have identical dimensions */
.program-card:nth-child(1),
.program-card:nth-child(2),
.program-card:nth-child(3),
.program-card:nth-child(4),
.program-card:nth-child(5) {
    width: 100%;
    height: 500px;
    max-width: 100%;
    box-sizing: border-box;
}

/* Ensure all program images have identical dimensions */
.program-card:nth-child(1) .program-image,
.program-card:nth-child(2) .program-image,
.program-card:nth-child(3) .program-image,
.program-card:nth-child(4) .program-image,
.program-card:nth-child(5) .program-image {
    width: 100%;
    height: 280px;
    min-height: 280px;
    max-height: 280px;
    box-sizing: border-box;
}

/* Ensure all program info sections have identical dimensions */
.program-card:nth-child(1) .program-info,
.program-card:nth-child(2) .program-info,
.program-card:nth-child(3) .program-info,
.program-card:nth-child(4) .program-info,
.program-card:nth-child(5) .program-info {
    width: 100%;
    height: 220px;
    box-sizing: border-box;
}

.program-card {
    background: linear-gradient(145deg, #ffffff, #f8fafc);
    border-radius: 1.5rem;
    overflow: hidden;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.08),
        0 8px 16px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    opacity: 1;
    transform: translateY(0);
    position: relative;
    backdrop-filter: blur(10px);
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 500px;
    max-width: 100%;
    box-sizing: border-box;
}

.program-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
    background-size: 300% 100%;
    animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.program-card.animate {
    opacity: 1;
    transform: translateY(0);
}

.program-card:hover {
    transform: translateY(-20px) scale(1.03);
    box-shadow: 
        0 35px 70px rgba(0, 0, 0, 0.15),
        0 15px 30px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.program-image {
    width: 100%;
    height: 280px;
    overflow: hidden;
    position: relative;
    border-radius: 1rem 1rem 0 0;
    margin: 0;
    background: #f8fafc;
    flex-shrink: 0;
    box-sizing: border-box;
    max-width: 100%;
    min-height: 280px;
    max-height: 280px;
}

.program-image::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(102, 126, 234, 0.1) 0%,
        rgba(118, 75, 162, 0.1) 50%,
        rgba(240, 147, 251, 0.1) 100%
    );
    opacity: 0;
    transition: opacity 0.4s ease;
}

.program-card:hover .program-image::after {
    opacity: 1;
}

.program-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    filter: brightness(1) contrast(1.05) saturate(1.1);
    display: block;
}

.program-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg, 
        rgba(102, 126, 234, 0.95) 0%,
        rgba(118, 75, 162, 0.9) 50%,
        rgba(240, 147, 251, 0.85) 100%
    );
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(30px);
    backdrop-filter: blur(8px);
}

.program-card:hover .program-overlay {
    opacity: 1;
    transform: translateY(0);
}

.program-card:hover .program-img {
    transform: scale(1.15) rotate(2deg);
    filter: brightness(1.1) contrast(1.1) saturate(1.2);
}

.overlay-content {
    text-align: center;
    color: white;
    padding: 1.5rem;
    position: relative;
}

.overlay-content::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
    transition: transform 0.4s ease 0.1s;
}

.program-card:hover .overlay-content::before {
    transform: translate(-50%, -50%) scale(1);
}

.overlay-content h4 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    transform: translateY(15px);
    transition: all 0.4s ease 0.2s;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 2;
}

.overlay-content p {
    font-size: 1rem;
    opacity: 0.95;
    transform: translateY(15px);
    transition: all 0.4s ease 0.3s;
    line-height: 1.5;
    position: relative;
    z-index: 2;
}

.program-card:hover .overlay-content h4,
.program-card:hover .overlay-content p {
    transform: translateY(0);
}

.program-info {
    padding: 1.5rem;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(248, 250, 252, 0.8));
    position: relative;
    height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-sizing: border-box;
    width: 100%;
    max-width: 100%;
}

.program-info::before {
    content: '';
    position: absolute;
    top: 0;
    left: 1.5rem;
    right: 1.5rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.3), transparent);
}

.program-info h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
    line-height: 1.3;
    transition: all 0.3s ease;
    position: relative;
}

.program-card:hover .program-info h3 {
    color: #667eea;
    transform: translateX(5px);
}

.program-info p {
    font-size: 0.95rem;
    color: #6b7280;
    line-height: 1.7;
    margin: 0;
    transition: color 0.3s ease;
}

.program-card:hover .program-info p {
    color: #4b5563;
}

/* Department Badge Styling */
.program-department {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.9));
    backdrop-filter: blur(10px);
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    z-index: 3;
    transition: all 0.3s ease;
}

.program-card:hover .program-department {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}


/* Animation Classes */
.slide-in-left {
    opacity: 0;
    transform: translateX(-50px);
    animation: slideInLeft 0.8s ease forwards;
}

.slide-in-right {
    opacity: 0;
    transform: translateX(50px);
    animation: slideInRight 0.8s ease forwards;
}

.fade-in-up {
    opacity: 1;
    transform: translateY(0);
}

@keyframes slideInLeft {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .programs-grid {
        gap: 1.5rem;
        padding: 0 1rem;
        max-width: 1000px;
    }
    
    .programs-grid .program-card:nth-child(4) {
        margin-right: 0.5rem;
    }
    
    .programs-grid .program-card:nth-child(5) {
        margin-left: 0.5rem;
    }
}

@media (max-width: 1024px) {
    .programs-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        padding: 0 1rem;
        justify-items: stretch;
    }
    
    .programs-grid .program-card:nth-child(4),
    .programs-grid .program-card:nth-child(5) {
        grid-column: span 1;
        grid-row: auto;
        justify-self: stretch;
        margin: 0;
    }
    
    .programs-grid .program-card:nth-child(4) {
        grid-column: 1 / 2;
    }
    
    .programs-grid .program-card:nth-child(5) {
        grid-column: 2 / 3;
    }
}

@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        gap: 1rem;
        padding: 0 1rem;
    }

    .main-nav {
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .hero-text h1 {
        font-size: 2.5rem;
    }

    .hero-text p {
        font-size: 1.25rem;
    }

    .portal-cards {
        grid-template-columns: 1fr;
        padding: 0 1rem;
    }

    .portal-actions {
        flex-direction: column;
    }

    .portal-actions .btn {
        width: 100%;
        justify-content: center;
    }

    .features-grid {
        grid-template-columns: 1fr;
        padding: 0 1rem;
    }

    .section-header h2 {
        font-size: 2rem;
    }
    
    .learning-content,
    .curriculum-header {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .learning-text-left h2,
    .curriculum-title h2 {
        font-size: 2rem;
    }
    
    .programs-grid {
        grid-template-columns: 1fr;
    }
    
    .programs-grid .program-card:nth-child(4),
    .programs-grid .program-card:nth-child(5) {
        margin: 0;
    }

    .chatbot-container {
        width: 350px;
    }
}

@media (max-width: 480px) {
    .hero-text h1 {
        font-size: 2rem;
    }

    .portal-card {
        padding: 1.5rem;
    }

    .feature-item {
        padding: 1.5rem;
    }

    .chatbot-widget {
        bottom: 1rem;
        right: 1rem;
    }
    
    .chatbot-container {
        width: 320px;
        height: 450px;
    }
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 10px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>

<script>
    let conversationContext = {
    currentTopic: null,
    followUp: false,
    lastQuestion: null
};

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
</body>
</html>

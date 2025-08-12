<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ForkNow') }} - Restaurant Management System</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 20px;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .pricing-card {
            border: none;
            border-radius: 20px;
            transition: transform 0.3s ease;
        }
        
        .pricing-card:hover {
            transform: translateY(-5px);
        }
        
        .pricing-card.featured {
            border: 3px solid #ff6b35;
            transform: scale(1.05);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
        }
        
        .btn-outline-custom {
            border: 2px solid #ff6b35;
            color: #ff6b35;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-custom:hover {
            background: #ff6b35;
            color: white;
            transform: translateY(-2px);
        }
        
        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.9) !important;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .section-padding {
            padding: 80px 0;
        }
        
        .contact-form {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 40px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }
        
        .footer {
            background: #2c3e50;
            color: white;
        }
        
        .footer a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: #ff6b35;
        }
        
        .icon-box {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .icon-box i {
            font-size: 24px;
            color: white;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }
        
        .logo-svg {
            width: 24px;
            height: 24px;
            color: white;
        }
        
        .brand-text {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }
        
        .brand-subtitle {
            font-size: 0.7rem;
            color: #6c757d;
            font-weight: 500;
            line-height: 1;
        }
        
        .hero-image {
            position: relative;
            z-index: 2;
        }
        
        .hero-image::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: -1;
        }
        
        .hero-image::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: -20px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: -1;
        }
        
        .floating-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 10px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #ff6b35;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .feature-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #e9ecef;
        }
        
        .feature-card:hover {
            background: linear-gradient(145deg, #ffffff 0%, #fff5f0 100%);
            border-color: #ff6b35;
        }
        
        .pricing-card.featured {
            background: linear-gradient(145deg, #fff5f0 0%, #ffffff 100%);
            border: 2px solid #ff6b35;
            position: relative;
            overflow: hidden;
        }
        
        .pricing-card.featured::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
        }
        
        .navbar {
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
        }
        
        .btn-light {
            background: #ffffff;
            border: none;
            color: #ff6b35;
            font-weight: 600;
        }
        
        .btn-light:hover {
            background: #f8f9fa;
            color: #ff6b35;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .btn-outline-light:hover {
            background: #ffffff;
            color: #ff6b35;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        /* Hero Section Animations */
        .hero-bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .floating-icon {
            position: absolute;
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-icon-1 {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-icon-2 {
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }
        
        .floating-icon-3 {
            top: 80%;
            left: 20%;
            animation-delay: 4s;
        }
        
        .floating-icon-4 {
            top: 30%;
            right: 25%;
            animation-delay: 1s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        /* Tech Badge */
        .tech-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 8px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #fff;
        }
        
        /* Text Gradient Animation */
        .text-gradient-animated {
            background: linear-gradient(45deg, #ff6b35, #f7931e, #ff6b35);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient-shift 3s ease infinite;
        }
        
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        /* Typewriter Effect */
        .typewriter-text {
            border-right: 3px solid #fff;
            animation: typewriter 3s steps(20) infinite, blink 0.75s step-end infinite;
            white-space: nowrap;
            overflow: hidden;
        }
        
        @keyframes typewriter {
            0% { width: 0; }
            50% { width: 100%; }
            100% { width: 100%; }
        }
        
        @keyframes blink {
            from, to { border-color: transparent; }
            50% { border-color: #fff; }
        }
        
        /* Highlight Text */
        .highlight-text {
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.2) 100%);
            padding: 2px 8px;
            border-radius: 4px;
        }
        
        /* Tech Features */
        .tech-features {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .tech-feature {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #fff;
        }
        
        /* App Interface */
        .app-interface {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 20px;
            width: 300px;
            margin: 0 auto;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }
        
        .app-header {
            margin-bottom: 20px;
        }
        
        .app-status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }
        
        .status-icons {
            display: flex;
            gap: 5px;
        }
        
        .app-title {
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
        }
        
        .app-content {
            space-y: 15px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            margin-bottom: 10px;
        }
        
        .order-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .order-details {
            flex: 1;
        }
        
        .order-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .order-status {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .order-time {
            font-size: 0.8rem;
            font-weight: 600;
            color: #ffd700;
        }
        
        /* Tech Elements */
        .tech-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: #fff;
            animation: tech-float 4s ease-in-out infinite;
        }
        
        .tech-element-1 {
            top: -20px;
            right: -80px;
            animation-delay: 0s;
        }
        
        .tech-element-2 {
            bottom: 20px;
            left: -60px;
            animation-delay: 2s;
        }
        
        .tech-element-3 {
            top: 50%;
            right: -100px;
            animation-delay: 1s;
        }
        
        @keyframes tech-float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-10px) scale(1.05); }
        }
        
        /* Animated Stats */
        .animated-stats {
            position: absolute;
            bottom: -80px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            min-width: 80px;
            animation: stat-appear 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        
        .stat-card-1 { animation-delay: 0.2s; }
        .stat-card-2 { animation-delay: 0.4s; }
        .stat-card-3 { animation-delay: 0.6s; }
        
        @keyframes stat-appear {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: white;
            font-size: 1.2rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ff6b35;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.7rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        
        .scroll-arrow {
            width: 2px;
            height: 30px;
            background: rgba(255, 255, 255, 0.5);
            margin: 0 auto 10px;
            position: relative;
            animation: scroll-bounce 2s infinite;
        }
        
        .scroll-arrow::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: -3px;
            width: 8px;
            height: 8px;
            border-right: 2px solid rgba(255, 255, 255, 0.5);
            border-bottom: 2px solid rgba(255, 255, 255, 0.5);
            transform: rotate(45deg);
        }
        
        @keyframes scroll-bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        /* Tech Button */
        .tech-btn {
            position: relative;
            overflow: hidden;
        }
        
        .tech-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .tech-btn:hover::before {
            left: 100%;
        }
        
        /* Debug styles for button */
        .tech-btn {
            position: relative;
            z-index: 1000;
        }
        
        .tech-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
        }
        
        /* Ensure button is clickable */
        .btn-primary-custom {
            position: relative;
            z-index: 1000;
            pointer-events: auto;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand text-gradient" href="{{ route('dashboard') }}">
                                    <div class="d-flex align-items-center">
                        <div class="logo-icon me-3">
                            <svg class="logo-svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                            </svg>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="brand-text">ForkNow</span>
                            <small class="brand-subtitle">Restaurant Management</small>
                        </div>
                    </div>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Xususiyatlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Narxlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Aloqa</a>
                    </li>
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Kirish</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="btn btn-primary-custom text-white ms-2" href="{{ route('register') }}">Ro'yxatdan o'tish</a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white position-relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="hero-bg-animation">
            <div class="floating-icon floating-icon-1">
                <i class="bi bi-egg-fried"></i>
            </div>
            <div class="floating-icon floating-icon-2">
                <i class="bi bi-cup-hot"></i>
            </div>
            <div class="floating-icon floating-icon-3">
                <i class="bi bi-pizza-slice"></i>
            </div>
            <div class="floating-icon floating-icon-4">
                <i class="bi bi-cake2"></i>
            </div>
        </div>
        
        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-right" data-aos-duration="1000">
                        <div class="tech-badge mb-3">
                            <i class="bi bi-cpu me-2"></i>
                            <span>AI Powered</span>
                        </div>
                        <h1 class="display-3 fw-bold mb-4">
                            <span class="text-gradient-animated">Restoran</span> 
                            <span class="text-warning">Biznesini</span>
                            <br>
                            <span class="typewriter-text">Raqamlashtiring</span>
                        </h1>
                        <p class="lead mb-4">
                            <strong>ForkNow</strong> - zamonaviy texnologiyalar asosida qurilgan 
                            <span class="highlight-text">restoran boshqaruv tizimi</span>. 
                            AI, cloud computing va real-time analytics bilan.
                        </p>
                        <div class="tech-features mb-5">
                            <div class="tech-feature">
                                <i class="bi bi-lightning-charge text-warning"></i>
                                <span>Real-time</span>
                            </div>
                            <div class="tech-feature">
                                <i class="bi bi-cloud-check text-info"></i>
                                <span>Cloud-based</span>
                            </div>
                            <div class="tech-feature">
                                <i class="bi bi-shield-check text-success"></i>
                                <span>Secure</span>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-3">
                            <a href="{{ route('register') }}" class="btn btn-primary-custom btn-lg tech-btn" onclick="alert('Button clicked! Going to register page...')" style="cursor: pointer; text-decoration: none; display: inline-block;">
                                <i class="bi bi-rocket-takeoff me-2"></i>
                                Bepul sinab ko'ring
                            </a>
                            <!-- Fallback button for testing -->
                        
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="hero-image position-relative" data-aos="fade-left" data-aos-duration="1000">
                        <!-- Main App Interface -->
                        <div class="app-interface">
                            <div class="app-header">
                                <div class="app-status-bar">
                                    <span class="status-time">14:30</span>
                                    <div class="status-icons">
                                        <i class="bi bi-wifi"></i>
                                        <i class="bi bi-battery-full"></i>
                                    </div>
                                </div>
                                <div class="app-title">
                                    <i class="bi bi-egg-fried me-2"></i>
                                    ForkNow
                                </div>
                            </div>
                            <div class="app-content">
                                <div class="order-item">
                                    <div class="order-icon">
                                        <i class="bi bi-pizza-slice"></i>
                                    </div>
                                    <div class="order-details">
                                        <div class="order-name">Pizza Margherita</div>
                                        <div class="order-status">Tayyorlanmoqda...</div>
                                    </div>
                                    <div class="order-time">15 min</div>
                                </div>
                                <div class="order-item">
                                    <div class="order-icon">
                                        <i class="bi bi-cup-hot"></i>
                                    </div>
                                    <div class="order-details">
                                        <div class="order-name">Latte</div>
                                        <div class="order-status">Tayyor</div>
                                    </div>
                                    <div class="order-time">5 min</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Floating Tech Elements -->
                        <div class="tech-element tech-element-1">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Analytics</span>
                        </div>
                        <div class="tech-element tech-element-2">
                            <i class="bi bi-robot"></i>
                            <span>AI Bot</span>
                        </div>
                        <div class="tech-element tech-element-3">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <span>Cloud Sync</span>
                        </div>
                        
                        <!-- Animated Stats -->
                        <div class="animated-stats">
                            <div class="stat-card stat-card-1">
                                <div class="stat-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="500">0</div>
                                    <div class="stat-label">Restoran</div>
                                </div>
                            </div>
                            <div class="stat-card stat-card-2">
                                <div class="stat-icon">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="10000">0</div>
                                    <div class="stat-label">Buyurtma</div>
                                </div>
                            </div>
                            <div class="stat-card stat-card-3">
                                <div class="stat-icon">
                                    <i class="bi bi-star"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-number" data-count="99">0</div>
                                    <div class="stat-label">% Mamnuniyat</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="scroll-indicator">
            <div class="scroll-arrow"></div>
            <span>Batafsil ko'rish</span>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section-padding bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Nima uchun ForkNow?</h2>
                    <p class="lead text-muted">
                        Zamonaviy restoran boshqaruvi uchun barcha kerakli vositalar
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="icon-box mx-auto">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Oson buyurtma boshqaruvi</h5>
                            <p class="card-text text-muted">
                                Buyurtmalarni real vaqtda qabul qiling, kuryerlarga yuboring va mijozlar bilan aloqada bo'ling.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="icon-box mx-auto">
                                <i class="bi bi-gear"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Menyu boshqaruvi</h5>
                            <p class="card-text text-muted">
                                Mahsulotlaringizni osongina qo'shing, tahrirlang va narxlarni yangilang.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="icon-box mx-auto">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Batafsil hisobotlar</h5>
                            <p class="card-text text-muted">
                                Savdo, mijozlar va kuryerlar haqida batafsil ma'lumotlar va statistikalar.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="icon-box mx-auto">
                                <i class="bi bi-phone"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Mobil ilova</h5>
                            <p class="card-text text-muted">
                                Android va iOS qurilmalarida ishlaydigan mobil ilova bilan har joydan boshqaring.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="icon-box mx-auto">
                                <i class="bi bi-lightning"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Tezkor ishlash</h5>
                            <p class="card-text text-muted">
                                Zamonaviy texnologiyalar asosida qurilgan tez va ishonchli tizim.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="icon-box mx-auto">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Xavfsizlik</h5>
                            <p class="card-text text-muted">
                                Ma'lumotlaringiz to'liq himoyalangan va faqat sizga tegishli.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="section-padding">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Narxlar</h2>
                    <p class="lead text-muted">
                        Har xil o'lchamdagi restoranlar uchun mos narxlar
                    </p>
                </div>
            </div>
            
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 shadow">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold mb-3">Boshlang'ich</h5>
                            <div class="display-4 fw-bold text-warning mb-2">$29</div>
                            <div class="text-muted mb-4">oyiga</div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Kunlik 50 ta buyurtma</li>
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Asosiy hisobotlar</li>
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Email qo'llab-quvvatlash</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-primary-custom w-100">Tanlash</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card featured h-100 shadow">
                        <div class="card-body text-center p-4 position-relative">
                            <span class="badge bg-warning text-dark position-absolute top-0 start-50 translate-middle-x mt-3 px-3 py-2">
                                Eng mashhur
                            </span>
                            <h5 class="card-title fw-bold mb-3">Professional</h5>
                            <div class="display-4 fw-bold text-warning mb-2">$79</div>
                            <div class="text-muted mb-4">oyiga</div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Cheksiz buyurtmalar</li>
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Batafsil hisobotlar</li>
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Telegram bot integratsiyasi</li>
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>24/7 qo'llab-quvvatlash</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-primary-custom w-100">Tanlash</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card h-100 shadow">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title fw-bold mb-3">Korxona</h5>
                            <div class="display-4 fw-bold text-warning mb-2">$199</div>
                            <div class="text-muted mb-4">oyiga</div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Ko'p filiallar</li>
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>API integratsiyasi</li>
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Maxsus yechimlar</li>
                            </ul>
                            <a href="#contact" class="btn btn-primary-custom w-100">Bog'lanish</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Mijozlarimiz fikri</h2>
                    <p class="lead text-muted">
                        ForkNow bilan ishlayotgan restoranlarimizning tajribasi
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </div>
                            <p class="card-text mb-3">
                                "ForkNow bilan buyurtmalar boshqaruvi juda oson bo'ldi. Mijozlarimiz ham mamnun."
                            </p>
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                                <div class="text-start">
                                    <h6 class="mb-0 fw-bold">Aziz Karimov</h6>
                                    <small class="text-muted">Pizza House</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </div>
                            <p class="card-text mb-3">
                                "Telegram bot orqali buyurtmalar qabul qilish juda qulay. Kuryerlar ham tez ishlaydi."
                            </p>
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                                <div class="text-start">
                                    <h6 class="mb-0 fw-bold">Malika Yusupova</h6>
                                    <small class="text-muted">Sushi Master</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </div>
                            <p class="card-text mb-3">
                                "Hisobotlar va statistikalar juda batafsil. Biznesimizni yaxshiroq boshqarish mumkin."
                            </p>
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                                <div class="text-start">
                                    <h6 class="mb-0 fw-bold">Rustam Toshmatov</h6>
                                    <small class="text-muted">Burger King</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section-padding bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold mb-3">Biz bilan bog'laning</h2>
                    <p class="lead text-muted">
                        Savollaringiz bormi? Bizga yozing, tezda javob beramiz
                    </p>
                </div>
            </div>
            
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-4">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Email</h5>
                                    <p class="text-muted mb-0">info@forknow.uz</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-4">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Telefon</h5>
                                    <p class="text-muted mb-0">+998 90 123 45 67</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-4">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Manzil</h5>
                                    <p class="text-muted mb-0">Toshkent shahri, O'zbekiston</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="contact-form">
                        <form>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ism</label>
                                <input type="text" class="form-control" placeholder="Ismingizni kiriting">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" placeholder="Email manzilingizni kiriting">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Xabar</label>
                                <textarea class="form-control" rows="4" placeholder="Xabaringizni yozing"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-custom w-100">Yuborish</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="section-padding" style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold text-white mb-4">
                        Restoranlaringizni bugun raqamlashtiring
                    </h2>
                    <p class="lead text-white-50 mb-5">
                        ForkNow bilan birga bo'ling va restoran biznesingizni keyingi darajaga ko'taring
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 py-3 fw-bold">
                            <i class="bi bi-rocket-takeoff me-2"></i>
                            Bepul boshlash
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box me-3">
                            <i class="bi bi-egg-fried"></i>
                        </div>
                        <span class="h4 fw-bold text-white mb-0">ForkNow</span>
                    </div>
                    <p class="text-muted">
                        Restoranlaringizni raqamlashtirish uchun zamonaviy yechimlar.
                    </p>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Mahsulot</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Xususiyatlar</a></li>
                        <li class="mb-2"><a href="#">Narxlar</a></li>
                        <li class="mb-2"><a href="#">API</a></li>
                        <li class="mb-2"><a href="#">Hujjatlar</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Kompaniya</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Haqida</a></li>
                        <li class="mb-2"><a href="#">Blog</a></li>
                        <li class="mb-2"><a href="#">Karyera</a></li>
                        <li class="mb-2"><a href="#">Yangiliklar</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Qo'llab-quvvatlash</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Yordam markazi</a></li>
                        <li class="mb-2"><a href="#">Aloqa</a></li>
                        <li class="mb-2"><a href="#">Status</a></li>
                        <li class="mb-2"><a href="#">Xavfsizlik</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h6 class="fw-bold mb-3">Ijtimoiy tarmoqlar</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="text-muted fs-4"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-muted fs-4"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-muted fs-4"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-muted fs-4"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 border-secondary">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted mb-0">&copy; 2024 ForkNow. Barcha huquqlar himoyalangan.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-muted me-3">Maxfiylik siyosati</a>
                    <a href="#" class="text-muted">Foydalanish shartlari</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Smooth Scrolling -->
    <script>
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
        
        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe all cards and sections
        document.querySelectorAll('.feature-card, .pricing-card, .card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
        
        // Animated Counter for Stats
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            
            function updateCounter() {
                start += increment;
                if (start < target) {
                    element.textContent = Math.floor(start);
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target;
                }
            }
            
            updateCounter();
        }
        
        // Intersection Observer for Stats
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumber = entry.target.querySelector('.stat-number');
                    const count = parseInt(statNumber.getAttribute('data-count'));
                    animateCounter(statNumber, count);
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        // Observe stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            statsObserver.observe(card);
        });
        
        // Parallax effect for floating icons
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero-bg-animation');
            if (parallax) {
                const speed = scrolled * 0.5;
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });
        
        // Add AOS library for scroll animations
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 1000,
                easing: 'ease-in-out',
                once: true
            });
        }
        
        // Debug button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const registerButton = document.querySelector('a[href="{{ route("register") }}"]');
            const testButton = document.querySelector('a[href="/register"]');
            
            if (registerButton) {
                console.log('Register button found:', registerButton);
                registerButton.addEventListener('click', function(e) {
                    console.log('Register button clicked!');
                });
            }
            
            if (testButton) {
                console.log('Test button found:', testButton);
                testButton.addEventListener('click', function(e) {
                    console.log('Test button clicked!');
                });
            }
            
            // Check if any elements are blocking the button
            const buttonRect = registerButton ? registerButton.getBoundingClientRect() : null;
            if (buttonRect) {
                console.log('Button position:', buttonRect);
                console.log('Button z-index:', window.getComputedStyle(registerButton).zIndex);
            }
        });
    </script>
</body>
</html>

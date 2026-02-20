<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nicey Burger · premium fast food app</title>
    <!-- fonts & icons (same) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Leaflet CSS + JS (for accurate map) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Leaflet search plugin (easy to use, lightweight) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <style>
        /* your original styles — kept exactly, only tiny additions for leaflet & modal */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #ffd400;
            color: #000;
            line-height: 1.5;
            overflow-x: hidden;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 0 20px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            background: #000;
            color: #ffd400;
            padding: 8px 24px;
            border-radius: 60px;
            text-decoration: none;
            box-shadow: 0 10px 15px -8px rgba(0, 0, 0, 0.2);
        }

        .nav-links {
            display: flex;
            gap: 16px;
            font-weight: 600;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #000;
            font-weight: 500;
            font-size: 16px;
            transition: 0.2s;
            padding: 8px 14px;
            border-radius: 40px;
        }

        .nav-links a:hover {
            color: #fff;
            background: #000;
        }

        .btn-outline {
            border: 2px solid #000;
            border-radius: 40px;
            padding: 10px 24px;
            font-weight: 700;
            background: transparent;
            cursor: pointer;
            transition: 0.15s;
            font-size: 15px;
        }

        .btn-outline:hover {
            background: #000;
            color: #ffd400;
        }

        .btn-black {
            background: #000;
            color: #ffd400;
            border: 2px solid #000;
            border-radius: 60px;
            padding: 14px 30px;
            font-weight: 700;
            font-size: 17px;
            cursor: pointer;
            box-shadow: 0 8px 0 #3d3d3d;
            transition: 0.08s linear;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-black:active {
            transform: translateY(4px);
            box-shadow: 0 4px 0 #3d3d3d;
        }

        .btn-yellow {
            background: #ffd400;
            color: #000;
            border: 2px solid #000;
            border-radius: 60px;
            padding: 14px 30px;
            font-weight: 700;
            font-size: 17px;
            cursor: pointer;
            box-shadow: 0 8px 0 #b09000;
            transition: 0.08s linear;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-yellow:active {
            transform: translateY(4px);
            box-shadow: 0 4px 0 #b09000;
        }

        .hero {
            display: flex;
            align-items: center;
            gap: 40px;
            margin: 30px 0 60px;
            flex-wrap: wrap;
        }

        .hero-content {
            flex: 1 1 400px;
        }

        .hero-content .badge {
            background: #000;
            color: #ffd400;
            padding: 6px 20px;
            border-radius: 60px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .hero-content h1 {
            font-size: 64px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -2px;
            margin-bottom: 24px;
        }

        .hero-content h1 span {
            background: #000;
            color: #ffd400;
            padding: 0 12px;
            display: inline-block;
            border-radius: 20px;
        }

        .hero-desc {
            font-size: 20px;
            color: #1e1e1e;
            max-width: 500px;
            margin-bottom: 32px;
            font-weight: 500;
        }

        .cta-group {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .hero-image {
            flex: 1 1 360px;
            display: flex;
            justify-content: center;
        }

        .burger-app-mock {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 70px;
            padding: 20px 12px 12px;
            box-shadow: 0 40px 50px -10px #00000060, 0 0 0 10px #000;
            transform: rotate(3deg);
            transition: transform 0.3s;
        }

        .burger-app-mock:hover {
            transform: rotate(0deg);
        }

        .mock-inner {
            background: #ffd400;
            border-radius: 50px;
            overflow: hidden;
            padding: 20px 12px;
        }

        .mock-header {
            font-weight: 800;
            font-size: 24px;
            background: #000;
            color: #ffd400;
            display: inline-block;
            padding: 6px 30px;
            border-radius: 40px;
            margin-bottom: 20px;
        }

        .mock-card {
            background: #fff;
            border-radius: 30px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            box-shadow: 0 8px 0 #7a7a7a;
        }

        .mock-card .icon-lg {
            font-size: 42px;
        }

        .mock-price {
            background: #2b8c5e;
            color: #fff;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 40px;
        }

        .section-title {
            text-align: center;
            font-size: 44px;
            font-weight: 700;
            margin: 80px 0 40px;
            background: #000;
            color: #ffd400;
            display: inline-block;
            padding: 10px 50px;
            border-radius: 100px;
            letter-spacing: -0.5px;
        }

        .title-wrap {
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }

        .feature-card {
            background: #fff;
            padding: 32px 20px;
            border-radius: 40px;
            box-shadow: 0 30px 25px -15px rgba(0, 0, 0, 0.3);
            text-align: center;
            border: 2px solid #000;
            transition: 0.2s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
        }

        .feature-icon {
            background: #ffd400;
            width: 92px;
            height: 92px;
            border-radius: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 3px solid #000;
            font-size: 38px;
        }

        .feature-card h3 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .screens-showcase {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 24px;
            margin: 40px 0;
        }

        .screen-mini {
            width: 190px;
            background: #000;
            border-radius: 48px;
            padding: 16px 8px;
            box-shadow: 0 20px 30px #00000050;
            border: 3px solid #fff;
        }

        .screen-mini .yellow-bg {
            background: #ffd400;
            border-radius: 36px;
            padding: 16px 8px;
        }

        .screen-mini .mock-bottom {
            background: #000;
            height: 12px;
            width: 80px;
            border-radius: 40px;
            margin: 12px auto 0;
        }

        .stats-bar {
            background: #000;
            color: #ffd400;
            border-radius: 120px;
            padding: 34px 24px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin: 70px 0;
            gap: 24px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 48px;
            font-weight: 800;
            line-height: 1;
        }

        .stat-label {
            font-size: 18px;
            font-weight: 500;
        }

        .location-section {
            background: #fff;
            border-radius: 80px;
            padding: 50px 30px;
            margin: 60px 0;
            border: 4px solid #000;
            text-align: center;
        }

        .location-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .location-icon {
            font-size: 64px;
            color: #ffd400;
            background: #000;
            width: 120px;
            height: 120px;
            border-radius: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            border: 4px solid #ffd400;
        }

        .location-title {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .location-address {
            font-size: 24px;
            font-weight: 600;
            background: #ffd400;
            padding: 15px 30px;
            border-radius: 60px;
            display: inline-block;
            margin: 20px 0;
            border: 3px solid #000;
        }

        .location-link {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #000;
            color: #ffd400;
            padding: 18px 40px;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 700;
            font-size: 20px;
            margin-top: 20px;
            border: 3px solid #ffd400;
            transition: 0.2s;
        }

        .location-link:hover {
            transform: scale(1.05);
            background: #ffd400;
            color: #000;
            border-color: #000;
        }

        .location-link i {
            font-size: 28px;
        }

        /* map modal with leaflet */
        .map-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.62);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 3000;
            padding: 16px;
        }

        .map-modal.show {
            display: flex;
        }

        .map-modal-content {
            width: min(95vw, 1100px);
            background: #fff;
            border: 4px solid #000;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .map-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            background: #ffd400;
            border-bottom: 3px solid #000;
        }

        .map-modal-title {
            font-size: 22px;
            font-weight: 800;
        }

        .map-close-btn {
            width: 40px;
            height: 40px;
            border: 2px solid #000;
            border-radius: 12px;
            background: #fff;
            color: #000;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            font-weight: 700;
        }

        .map-embed-wrap {
            padding: 12px;
            background: #f6f6f6;
            flex: 1 1 auto;
            min-height: 420px;
            position: relative;
        }

        #leafletMap {
            width: 100%;
            height: 460px;
            border-radius: 16px;
            background: #ccc;
            z-index: 1;
        }

        /* Leaflet geocoder search bar inside map */
        .leaflet-control-geocoder {
            border-radius: 40px !important;
            border: 2px solid #000 !important;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .leaflet-control-geocoder input {
            font-family: 'Poppins', sans-serif !important;
            padding: 8px 12px !important;
            font-size: 14px !important;
        }
        .leaflet-control-geocoder-icon {
            display: none;
        }

        .map-modal-footer {
            padding: 10px 14px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .map-modal-footer a {
            color: #000;
            font-weight: 700;
            text-decoration: none;
        }

        .map-modal-footer a:hover {
            text-decoration: underline;
        }

        .map-google-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #000;
            color: #ffd400 !important;
            border: 2px solid #000;
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 14px;
            text-decoration: none !important;
        }

        .download-area {
            text-align: center;
            background: #fff;
            border-radius: 80px;
            padding: 50px 24px;
            margin: 60px 0;
            border: 4px solid #000;
        }

        .download-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 34px;
        }

        .store-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #000;
            color: #ffd400;
            padding: 14px 28px;
            border-radius: 80px;
            font-weight: 600;
            font-size: 20px;
            border: 3px solid #ffd400;
            cursor: pointer;
            transition: 0.1s;
        }

        .store-btn i {
            font-size: 30px;
        }

        .footer {
            background: #000;
            color: #ffd400;
            border-radius: 80px 80px 0 0;
            padding: 40px 24px 24px;
            margin-top: 70px;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }

        .footer-copy {
            font-size: 14px;
            opacity: 0.8;
        }

        @media (max-width: 980px) {
            .hero-content h1 {
                font-size: 52px;
            }

            .navbar {
                flex-direction: column;
            }
        }

        @media (max-width: 760px) {
            .container {
                padding: 0 14px;
            }

            .hero {
                margin-top: 16px;
                gap: 24px;
            }

            .hero-content h1 {
                font-size: 36px;
                letter-spacing: -1px;
            }

            .hero-desc {
                font-size: 16px;
            }

            .burger-app-mock {
                max-width: 320px;
                border-radius: 42px;
                padding: 12px 8px 8px;
                box-shadow: 0 20px 24px -8px #00000060, 0 0 0 6px #000;
            }

            .mock-inner {
                border-radius: 34px;
                padding: 12px 8px;
            }

            .mock-header {
                font-size: 18px;
                padding: 4px 16px;
            }

            .mock-card {
                border-radius: 20px;
                padding: 10px;
                gap: 10px;
                margin-bottom: 12px;
            }

            .mock-card .icon-lg {
                font-size: 28px;
            }

            .section-title {
                font-size: 28px;
                padding: 8px 24px;
                margin-top: 54px;
            }

            .location-title {
                font-size: 28px;
            }

            .location-address {
                font-size: 18px;
            }

            .location-link {
                padding: 14px 25px;
                font-size: 16px;
            }

            .download-area h2 {
                font-size: 32px;
            }

            .download-area p {
                font-size: 16px;
            }

            .store-btn {
                font-size: 16px;
                padding: 10px 20px;
            }

            .store-btn i {
                font-size: 22px;
            }

            .map-modal {
                padding: 10px;
            }

            .map-modal-content {
                width: 96vw;
                border-radius: 18px;
            }

            .map-modal-title {
                font-size: 16px;
            }

            .map-close-btn {
                width: 34px;
                height: 34px;
                font-size: 20px;
            }

            .map-embed-wrap {
                padding: 8px;
            }

            #leafletMap {
                height: 300px;
                border-radius: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <a class="logo" href="landing.php">NICEY BURGER</a>
            <div class="nav-links">
                <a href="landing.php">Home</a>
                <a href="#features">Features</a>
                <a href="#location">Location</a>
                <a href="#download">Download</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <button class="btn-outline" onclick="showDownloadAlert()">Get App</button>
            </div>
        </nav>

        <div class="hero">
            <div class="hero-content">
                <span class="badge"><i class="fas fa-bolt" style="margin-right: 6px;"></i> #1 BURGER APP 2026</span>
                <h1>
                    BOLD.<br>
                    <span>YELLOW AND BLACK</span><br>
                    TASTE EXPLOSION
                </h1>
                <p class="hero-desc">
                    Order your favorite smash burgers with a tap. Customize every layer, save favorites, and get sizzling offers in a clean, premium interface.
                </p>
                <div class="cta-group">
                    <a href="login.php" class="btn-black"><i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i> Login</a>
                    <a href="register.php" class="btn-yellow"><i class="fas fa-user-plus" style="margin-right: 8px;"></i> Create account</a>
                </div>
                <div style="margin-top: 28px; display: flex; gap: 16px; flex-wrap: wrap;">
                    <span><i class="fas fa-star" style="color:#000; background:#ffd400; border-radius:50%; padding:8px;"></i> 4.9 rating</span>
                    <span><i class="fas fa-truck" style="color:#000; background:#ffd400; border-radius:50%; padding:8px;"></i> Free delivery</span>
                </div>
            </div>
            <div class="hero-image">
                <div class="burger-app-mock">
                    <div class="mock-inner">
                        <div class="mock-header">NICEY</div>
                        <div class="mock-card">
                            <div class="icon-lg"><i class="fas fa-hamburger"></i></div>
                            <div style="flex:1;"><strong>Double smash</strong><br>cheddar, onion</div>
                            <div class="mock-price">$4.15</div>
                        </div>
                        <div class="mock-card" style="padding: 12px;">
                            <div class="icon-lg"><i class="fas fa-utensils"></i></div>
                            <div style="flex:1;"><strong>Curly fries</strong><br>crispy</div>
                            <div style="background:#000; color:#ffd400; padding:4px 12px; border-radius:40px;">$2.99</div>
                        </div>
                        <div style="background:#000; border-radius: 30px; padding: 12px; color:#ffd400; display:flex; justify-content:space-between;">
                            <span><i class="fas fa-fire"></i> 342 cal</span>
                            <span><i class="fas fa-leaf"></i> veg option</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="title-wrap" id="features">
            <h2 class="section-title">why nicey?</h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <h3>Instant order</h3>
                <p>Tap, customize, cart under 10 seconds. Optimized for speed.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-heart"></i></div>
                <h3>Favorites sync</h3>
                <p>Save your usual and re-order with one click. Heart it, eat it.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-layer-group"></i></div>
                <h3>Full control</h3>
                <p>Add or remove ingredients, then see price update live.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-wallet"></i></div>
                <h3>Best deals</h3>
                <p>Green price tags, special offers, combos, and daily promos.</p>
            </div>
        </div>

        <div style="text-align: center; margin: 40px 0;">
            <span style="background:#000; color:#ffd400; padding:8px 32px; border-radius: 100px; font-size: 20px; font-weight:700;">designed for iOS and Android</span>
        </div>

        <div class="screens-showcase">
            <div class="screen-mini">
                <div class="yellow-bg">
                    <div style="background:#fff; border-radius: 30px; padding: 8px; margin-bottom:8px;"><i class="fas fa-hamburger"></i> <i class="fas fa-utensils"></i> <b>menu</b></div>
                    <div style="background:#fff; border-radius: 30px; padding: 8px;"><i class="fas fa-heart"></i> favorites</div>
                    <div class="mock-bottom"></div>
                </div>
            </div>
            <div class="screen-mini">
                <div class="yellow-bg">
                    <div style="font-size:40px;"><i class="fas fa-pizza-slice"></i> <i class="fas fa-hamburger"></i></div>
                    <div style="background:#000; color:#ffd400; border-radius: 30px; padding: 6px;">$8.90</div>
                    <div class="mock-bottom"></div>
                </div>
            </div>
            <div class="screen-mini">
                <div class="yellow-bg">
                    <div><i class="fas fa-cog"></i> customize</div>
                    <div style="display:flex; gap:6px;"><i class="fas fa-circle"></i><i class="fas fa-seedling"></i><i class="fas fa-circle-notch"></i></div>
                    <div class="mock-bottom"></div>
                </div>
            </div>
        </div>

        <div class="stats-bar">
            <div class="stat-item"><span class="stat-number">500k+</span><div class="stat-label">downloads</div></div>
            <div class="stat-item"><span class="stat-number">4.9</span><div class="stat-label">app store</div></div>
            <div class="stat-item"><span class="stat-number">1.2M</span><div class="stat-label">burgers sold</div></div>
        </div>

        <!-- New Location Section with Leaflet & search bar (accurate from google maps link) -->
        <div class="title-wrap" id="location">
            <h2 class="section-title">find us</h2>
        </div>
        
        <div class="location-section">
            <div class="location-content">
                <div class="location-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3 class="location-title">Visit Our Flagship Store</h3>
                <p style="font-size: 18px; margin-bottom: 20px;">The original Nicey Burger location - where it all began!</p>
                <div class="location-address">
                    <i class="fas fa-store" style="margin-right: 10px;"></i>
                    Poblacion, Villacarlos Street, Madridejos, Cebu
                </div>
                <p style="margin: 20px 0; font-size: 16px;">
                    <i class="fas fa-clock" style="margin-right: 8px;"></i> Open daily: 24/7
                </p>
                <a href="#" class="location-link" onclick="openMapModal(event)">
                    <i class="fab fa-google"></i> Open in Google Maps
                    <i class="fas fa-external-link-alt" style="font-size: 16px;"></i>
                </a>
                <div style="margin-top: 30px; font-size: 14px; opacity: 0.7;">
                    <i class="fas fa-check-circle" style="color: #2b8c5e;"></i> Verified location · Delivery available from this store
                </div>
            </div>
        </div>

        <div class="download-area" id="download">
            <h2 style="font-size: 48px; font-weight: 800; margin-bottom: 16px;">GRAB THE APP</h2>
            <p style="font-size: 22px; max-width: 600px; margin: 0 auto 20px;">Available now on the App Store and Google Play. Fresh, fast, and fully loaded.</p>
            <div class="download-buttons">
                <button class="store-btn" onclick="showDownloadAlert()"><i class="fab fa-apple"></i> App Store</button>
                <button class="store-btn" onclick="showDownloadAlert()"><i class="fab fa-google-play"></i> Google Play</button>
            </div>
            <p style="margin-top: 32px;"><i class="fas fa-qrcode" style="font-size: 32px; background: #000; color:#ffd400; padding:12px; border-radius:20px;"></i> Scan to download</p>
            <p style="margin-top: 18px;">
                <a href="login.php" class="btn-black">Open Web App</a>
            </p>
        </div>

        <footer class="footer">
            <div class="footer-links">
                <span style="font-weight:700; font-size: 28px;">NICEY</span>
                <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                    <a href="#" style="color:#ffd400; text-decoration: none;">Privacy</a>
                    <a href="#" style="color:#ffd400; text-decoration: none;">Terms</a>
                    <a href="#" style="color:#ffd400; text-decoration: none;">Contact</a>
                    <a href="https://maps.app.goo.gl/ERPJE74gpahaXntc9" target="_blank" style="color:#ffd400; text-decoration: none;"><i class="fab fa-google"></i> Store Location</a>
                </div>
                <div class="footer-copy">Copyright 2026 Nicey Burger · J.D Villacarlos Street, Madridejos, Cebu</div>
            </div>
        </footer>
    </div>

    <!-- Leaflet Modal with search bar (geocoder) and accurate marker -->
    <div class="map-modal" id="store-map-modal" aria-hidden="true">
        <div class="map-modal-content">
            <div class="map-modal-header">
                <div class="map-modal-title">Nicey Burger Junction · interactive map</div>
                <button class="map-close-btn" type="button" onclick="closeMapModal()">&times;</button>
            </div>
            <div class="map-embed-wrap">
                <div id="leafletMap"></div>
            </div>
            <div class="map-modal-footer">
                <a href="https://www.openstreetmap.org/?mlat=11.295779421800987&mlon=123.73113262147038#map=18/11.295779421800987/123.73113262147038" target="_blank" rel="noopener noreferrer">
                    OpenStreetMap
                </a>
                <a class="map-google-btn" href="https://maps.app.goo.gl/ERPJE74gpahaXntc9" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-google"></i> Google Maps
                </a>
            </div>
        </div>
    </div>

    <script>
        function showDownloadAlert() {
            alert('NICEY BURGER app will be available on App Store and Google. Visit us at Poblacion, Villacarlos Street, Madridejos, Cebu!');
        }

        function openMapModal(event) {
            event.preventDefault();
            document.getElementById('store-map-modal').classList.add('show');
            document.body.style.overflow = 'hidden';
            // Initialize map only once (avoid re-initializing)
            if (!window.mapInitialized) {
                initLeafletMap();
                window.mapInitialized = true;
            } else {
                // if map already exists, invalidate size to show correctly
                setTimeout(() => { window.currentLeafletMap?.invalidateSize(); }, 100);
            }
        }

        function closeMapModal() {
            document.getElementById('store-map-modal').classList.remove('show');
            document.body.style.overflow = '';
        }

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('store-map-modal');
            if (event.target === modal) {
                closeMapModal();
            }
        });

        function initLeafletMap() {
            // Accurate Nicey Burger Junction location
            const lat = 11.295779421800987;
            const lng = 123.73113262147038;

            const map = L.map('leafletMap').setView([lat, lng], 18);
            window.currentLeafletMap = map;

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19
            }).addTo(map);

            // custom marker with nicey style
            const burgerIcon = L.divIcon({
                className: 'custom-marker',
                html: '<i class="fas fa-hamburger" style="font-size: 28px; color:#ffd400; background:#000; padding:10px; border-radius: 50%; border: 3px solid white;"></i>',
                iconSize: [48, 48],
                popupAnchor: [0, -24]
            });

            const marker = L.marker([lat, lng], { icon: burgerIcon }).addTo(map);
            marker.bindPopup("<b>Nicey Burger Junction</b><br>Poblacion, Villacarlos St, Madridejos<br>Open 24/7").openPopup();

            // Add search (geocoder) with attribution
            const geocoder = L.Control.geocoder({
                defaultMarkGeocode: true,
                placeholder: 'Search for a place...',
                errorMessage: 'Place not found',
                geocoder: L.Control.Geocoder.nominatim(),
                showResultIcons: true,
            })
            .on('markgeocode', function(e) {
                map.fitBounds(e.geocode.bbox);
            })
            .addTo(map);

            // small hack to make geocoder look good
            setTimeout(() => {
                const geocoderDiv = document.querySelector('.leaflet-control-geocoder');
                if (geocoderDiv) {
                    geocoderDiv.style.borderRadius = '30px';
                }
            }, 200);
        }

        // close modal with escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('store-map-modal').classList.contains('show')) {
                closeMapModal();
            }
        });
    </script>
</body>
</html>

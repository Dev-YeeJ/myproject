<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBMIS - Login | Barangay Calbueg</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* Palette from your resident-profiling.blade.php */
            --primary-blue: #2B5CE6;
            --secondary-blue: #1E3A8A;
            --gold: #FFA500;
            --text-gray: #6c757d;
            --text-gray-light: rgba(255, 255, 255, 0.8);
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            position: relative;
        }

        /* --- Sliding Picture Background --- */
        .background-slider {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -2;
            background-color: #333;
            filter: grayscale(20%) brightness(0.7);
        }

        .background-slider img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 2s ease-in-out, transform 2s ease-in-out;
            animation: slideShow 30s infinite;
        }

        .background-slider img:nth-child(1) { animation-delay: 0s; }
        .background-slider img:nth-child(2) { animation-delay: 6s; }
        .background-slider img:nth-child(3) { animation-delay: 12s; }
        .background-slider img:nth-child(4) { animation-delay: 18s; }
        .background-slider img:nth-child(5) { animation-delay: 24s; }

        @keyframes slideShow {
            0%   { opacity: 0; transform: scale(1.1); }
            10%  { opacity: 1; transform: scale(1); }
            20%  { opacity: 1; transform: scale(1); }
            30%  { opacity: 0; transform: scale(1.1); }
            100% { opacity: 0; transform: scale(1.1); }
        }

        /* --- Gradient Blue Overlay --- */
        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top right, rgba(43, 92, 230, 0.75), rgba(30, 58, 138, 0.75));
            z-index: -1;
        }

        /* --- Login Card Wrapper (ENLARGED) --- */
        .login-wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 990px; /* Increased from 900px */
            width: 90%;
            min-height: 550px; /* Increased from 500px */
            overflow: hidden;
            z-index: 0;
            
            background: transparent;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);

            animation: cardFadeIn 0.6s ease-out;
        }

        @keyframes cardFadeIn {
            from { opacity: 0; transform: translate(-50%, -45%) scale(0.95); }
            to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        /* --- Left Info Panel (Solid Gradient) --- */
        .info-panel {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex: 1;
            min-width: 300px;
        }
        
        .info-panel .seal-container {
            width: 120px;
            height: 120px;
            background: transparent;
            border: none;
            border-radius: 0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none;
            overflow: hidden;
        }

        .info-panel .seal-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .info-panel .badge-philippines-login {
            background: var(--gold);
            color: #333;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .info-panel .barangay-name {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .info-panel .location {
            font-size: 1.0rem;
            opacity: 0.9;
        }

        .info-panel .live-clock {
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0.8;
            margin-top: 30px;
            background: rgba(0,0,0,0.1);
            padding: 8px 15px;
            border-radius: 8px;
        }
        
        .info-panel .tagline-footer {
            color: var(--text-gray-light);
            font-size: 0.9rem;
            font-style: italic;
            margin-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
            width: 80%;
        }

        /* --- Right Login Panel (White Frosted Glass) --- */
        .login-panel {
            background: rgba(255, 255, 255, 0.75);
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
            min-width: 350px;
        }
        
        .login-panel h3 {
            color: var(--secondary-blue);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .login-panel .system-full-name {
            color: var(--text-gray);
            font-size: 0.85rem;
            margin-bottom: 30px;
        }

        /* "Underline" Input Group */
        .clean-input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .clean-input-group .form-label {
            font-size: 0.9rem;
            color: var(--text-gray);
            font-weight: 500;
            margin-bottom: 4px;
        }

        .clean-input-group .form-control {
            background: transparent;
            border: none;
            border-radius: 0;
            border-bottom: 2px solid #ddd;
            color: #333;
            font-size: 1.1rem;
            padding: 10px 40px 10px 40px;
            height: 50px;
            box-shadow: none;
        }

        .clean-input-group .form-control::placeholder {
            color: #999;
            font-size: 1rem;
        }

        .clean-input-group .form-control:focus {
            background: transparent;
            border-bottom-color: var(--primary-blue);
            box-shadow: none;
        }
        
        #username.form-control {
            padding-right: 10px;
        }

        .clean-input-group .input-icon {
            position: absolute;
            left: 10px;
            bottom: 14px;
            color: var(--text-gray);
            transition: color 0.3s ease;
            font-size: 1.1rem;
        }
        
        .clean-input-group .password-toggle {
            position: absolute;
            right: 10px;
            bottom: 14px;
            cursor: pointer;
            color: var(--text-gray);
            font-size: 1.1rem;
        }

        .clean-input-group .form-control:focus ~ .input-icon,
        .clean-input-group .password-toggle:hover {
            color: var(--primary-blue);
        }
        
        .clean-input-group .invalid-feedback {
            color: #dc3545;
            font-weight: 500;
            position: absolute;
            bottom: -1.4rem;
        }

        .btn-login {
            background: var(--primary-blue);
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            width: 100%;
            height: 50px;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background: var(--secondary-blue);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(43, 92, 230, 0.3);
        }
        
        .help-section {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .help-title {
            color: var(--secondary-blue);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        .contact-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        .contact-item {
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .contact-item:hover { color: var(--primary-blue); }
        .contact-item i { color: var(--primary-blue); }
        
        /* --- Responsive Design --- */
        @media (max-width: 991.98px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 450px;
                width: 90%;
                height: auto;
                min-height: auto;
                background: transparent;
                backdrop-filter: none;
                border: none;
                box-shadow: none;
            }
            .info-panel {
                padding: 30px;
                order: -1;
                border-radius: 15px 15px 0 0;
                background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            }
            .info-panel .tagline-footer {
                width: 100%;
            }
            .login-panel {
                padding: 30px 40px;
                width: 100%;
                border-radius: 0 0 15px 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                background: #ffffff;
            }
        }

        @media (max-width: 576px) {
            .login-panel, .info-panel {
                padding: 25px;
            }
            .info-panel .barangay-name {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    
    <div class="background-slider">
        {{-- Online images for Barangay MIS --}}
        <img src="{{ asset('images/login_background1.png') }}" alt="Community people working together">
        <img src="{{ asset('images/login_background2.png') }}" alt="Technology and data visualization">
        <img src="{{ asset('images/login_background3.png') }}" alt="Aerial view of a town/village">
        <img src="{{ asset('images/login_background4.png') }}" alt="Group discussion/meeting">
        <img src="{{ asset('images/login_background5.png') }}" alt="Abstract tech background with lines">
    </div>
    
    <div class="background-overlay"></div>

    <div class="login-wrapper">

        <div class="col-lg-5 info-panel">
            <div class="seal-container">
                <img src="{{ asset('images/barangay-seal.png') }}" alt="Barangay Seal">
            </div>
            
            <span class="badge-philippines-login">Republic of the Philippines</span>
            <h1 class="barangay-name">Barangay Calbueg</h1>
            <p class="location mb-0">Malasiqui, Pangasinan</p>
            
            <div id="liveClock" class="live-clock">Loading time...</div>

            <p class="tagline-footer">Serving the Community with Digital Excellence</p>
        </div>

        <div class="col-12 col-lg-7 login-panel">
            <div class="text-center mb-4">
                <h3>iBMIS</h3>
                <p class="system-full-name mb-0">Integrated Barangay Management Information System</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                
                <div class="clean-input-group">
                    <label for="username" class="form-label">Username</label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                           id="username" name="username" value="{{ old('username') }}" placeholder="Enter your username" required autofocus>
                    @error('username')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="clean-input-group">
                    <label for="password" class="form-label">Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                     @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-login">Login to iBMIS</button>
                
            </form>

            <div class="help-section">
                <p class="help-title">Need Help?</p>
                <div class="contact-info">
                    <a href="tel:0751234567" class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>(075) 123-4567</span>
                    </a>
                    <a href="mailto:support@calbueg.gov.ph" class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>support@calbueg.gov.ph</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // Toggle icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Live Clock Functionality
        function updateClock() {
            const clockElement = document.getElementById('liveClock');
            if (clockElement) {
                const now = new Date();
                const options = {
                    weekday: 'long', 
                    month: 'long', 
                    day: 'numeric',
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true,
                    timeZone: 'Asia/Manila' // Philippine Time
                };
                clockElement.innerHTML = new Intl.DateTimeFormat('en-US', options).format(now);
            }
        }
        
        setInterval(updateClock, 1000);
        updateClock(); // Run once immediately

    </script>
</body>
</html>
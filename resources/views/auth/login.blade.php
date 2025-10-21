<!-- resources/views/auth/login.blade.php -->
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
            --primary-blue: #4169E1;
            --secondary-blue: #5B8FF9;
            --gold: #FFD700;
            --light-gray: #F5F5F5;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: var(--light-gray);
        }

        .container-fluid {
            min-height: 100vh;
        }

        /* Left Panel Styles */
        .left-panel {
            background: linear-gradient(135deg, #4169E1 0%, #5B8FF9 100%);
            color: white;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;



            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><path d="M0,400 Q300,200 600,400 T1200,400 L1200,800 L0,800 Z" fill="rgba(255,255,255,0.05)"/></svg>');
            background-size: cover;
            background-position: center;
            opacity: 0.3;
        }

        .left-panel-content {
            position: relative;
            z-index: 1;
        }

        .system-overview h2 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        .barangay-info {
            margin-top: 50px;
        }

        .badge-philippines {
            background: var(--gold);
            color: #333;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

        .barangay-name {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 15px 0;
        }

        .location {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .tagline {
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 24px;
            border-radius: 25px;
            display: inline-block;
            font-size: 0.95rem;
        }

        /* Right Panel Styles */
        .right-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: var(--light-gray);
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .seal-logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #4169E1, #5B8FF9);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 20px rgba(65, 105, 225, 0.3);
        }

        .seal-logo i {
            font-size: 50px;
            color: white;
        }

        .badge-philippines-login {
            background: var(--gold);
            color: #333;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

        .login-title {
            color: var(--primary-blue);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 15px 0 10px;
        }

        .system-full-name {
            color: var(--secondary-blue);
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 12px 15px;
            border-radius: 10px;
            border: 2px solid #E0E0E0;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(65, 105, 225, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
        }

        .password-toggle:hover {
            color: var(--primary-blue);
        }

        .btn-login {
            background: var(--primary-blue);
            color: white;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--secondary-blue);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(65, 105, 225, 0.3);
        }

        .help-section {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #E0E0E0;
        }

        .help-title {
            color: var(--primary-blue);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .contact-item {
            color: #666;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
        }

        .contact-item:hover {
            color: var(--primary-blue);
        }

        .contact-item i {
            color: var(--primary-blue);
        }

        .tagline-footer {
            color: var(--primary-blue);
            font-size: 0.9rem;
            font-style: italic;
        }

        @media (max-width: 991px) {
            .left-panel {
                min-height: auto;
                padding: 30px 20px;
            }

            .barangay-name {
                font-size: 2rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .login-card {
                padding: 30px;
            }

            .login-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 25px;
            }

            .contact-info {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Left Panel - System Overview -->
            <div class="col-lg-6 left-panel d-none d-lg-block">
                <div class="left-panel-content">
                    <div class="system-overview">
                        <h2>System Overview</h2>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number">2,847</div>
                            <div class="stat-label">Registered Residents</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-number">1234</div>
                            <div class="stat-label">Documents Processed</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="stat-number">0920</div>
                            <div class="stat-label">Active Households</div>
                        </div>
                    </div>

                    <div class="barangay-info">
                        <span class="badge-philippines">Republic of the Philippines</span>
                        <h1 class="barangay-name">Barangay Calbueg</h1>
                        <p class="location">Malasiqui, Pangasinan</p>
                        <span class="tagline">Building a Better Community Through Digital Innovation</span>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Login Form -->
            <div class="col-lg-6 right-panel">
                <div class="login-card">
                    <div class="text-center">
                        <div class="seal-logo">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <span class="badge-philippines-login">Republic of the Philippines</span>
                        <h1 class="barangay-name text-primary">Barangay Calbueg</h1>
                        <p class="location text-muted">Malasiqui, Pangasinan</p>
                        <h2 class="login-title">iBMIS</h2>
                        <p class="system-full-name">Integrated Barangay Management Information System</p>
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
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}">

                        @csrf
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username') }}" required autofocus>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
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
                        <p class="tagline-footer">Serving the Community with Digital Excellence</p>
                    </div>
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
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
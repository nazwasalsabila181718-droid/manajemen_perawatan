<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MaintAsset Sistem Perawatan Aset</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS System -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- Theme Initialization to prevent flash of wrong mode -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
            background: radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.05) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.06) 0%, transparent 40%),
                        var(--bg-primary);
        }

        .auth-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: var(--radius-lg);
            padding: 40px 36px;
            box-shadow: 0 20px 40px -15px rgba(37, 99, 235, 0.1), 0 4px 6px -2px rgba(15, 23, 42, 0.03);
            transition: var(--transition);
        }

        [data-theme="dark"] .auth-card {
            background: rgba(18, 24, 36, 0.88);
            border-color: rgba(30, 41, 59, 0.8);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }

        .auth-logo-badge {
            width: 58px;
            height: 58px;
            border-radius: var(--radius-md);
            background: var(--accent-gradient);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 12px 24px -6px rgba(37, 99, 235, 0.35);
            margin-bottom: 20px;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .auth-card:hover .auth-logo-badge {
            transform: scale(1.06) rotate(-3deg);
        }

        .auth-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 6px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 28px;
        }

        .ambient-glow-1 {
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.18) 0%, rgba(37, 99, 235, 0) 70%);
            top: -160px;
            left: -140px;
            pointer-events: none;
            filter: blur(40px);
        }

        .ambient-glow-2 {
            position: absolute;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(59, 130, 246, 0) 70%);
            bottom: -180px;
            right: -160px;
            pointer-events: none;
            filter: blur(40px);
        }
    </style>
</head>
<body>

    <!-- Ambient Lighting -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <!-- Theme Switcher Top Right -->
    <div class="position-absolute top-0 end-0 p-4" style="z-index: 100;">
        <button class="theme-toggle-btn" id="theme-toggle-btn" aria-label="Toggle Theme">
            <i class="bi bi-sun-fill" id="theme-icon"></i>
        </button>
    </div>

    <div class="auth-wrapper">
        <div class="auth-card">
            
            <div class="text-center">
                <div class="auth-logo-badge">
                    <i class="bi bi-wrench-adjustable-circle-fill"></i>
                </div>
                <h1 class="auth-title">Maint<span class="text-primary">Asset</span></h1>
                <p class="auth-subtitle">Sistem Perawatan Aset & Manajemen Armada</p>
            </div>

            <!-- Notifications / Alerts -->
            @if(session('success'))
                <div class="alert-premium success" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-premium danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                
                <!-- Email Input -->
                <div class="form-group-premium">
                    <label for="email" class="form-label-premium">Alamat Email</label>
                    <div class="position-relative">
                        <input type="email" class="form-control-premium" id="email" name="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com" required autofocus style="padding-left: 42px;">
                        <i class="bi bi-envelope position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 16px;"></i>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-group-premium mb-3">
                    <label for="password" class="form-label-premium">Kata Sandi</label>
                    <div class="position-relative">
                        <input type="password" class="form-control-premium" id="password" name="password" placeholder="••••••••" required style="padding-left: 42px; padding-right: 42px;">
                        <i class="bi bi-lock position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 16px;"></i>
                        <button type="button" id="toggle-password-btn" class="btn btn-link text-muted p-0 position-absolute" style="right: 14px; top: 50%; transform: translateY(-50%); border: none; background: transparent;">
                            <i class="bi bi-eye" id="toggle-password-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary small fw-medium" for="remember">
                            Ingat sesi saya
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-premium primary w-100 btn-lg">
                    Masuk ke Sistem <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>

        </div>

        <div class="text-center mt-4 text-muted small">
            &copy; 2026 MaintAsset System. Minimalist Redesign.
        </div>
    </div>

    <!-- Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeBtn = document.getElementById('theme-toggle-btn');
            const themeIcon = document.getElementById('theme-icon');
            
            function updateThemeUI(theme) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-moon-stars-fill';
                } else {
                    themeIcon.className = 'bi bi-sun-fill';
                }
            }

            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateThemeUI(currentTheme);

            themeBtn.addEventListener('click', () => {
                const activeTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeUI(newTheme);
            });

            // Toggle Password
            const togglePasswordBtn = document.getElementById('toggle-password-btn');
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('toggle-password-icon');

            if (togglePasswordBtn && passwordInput && passwordIcon) {
                togglePasswordBtn.addEventListener('click', () => {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        passwordIcon.className = 'bi bi-eye-slash-fill';
                    } else {
                        passwordInput.type = 'password';
                        passwordIcon.className = 'bi bi-eye-fill';
                    }
                });
            }
        });
    </script>
</body>
</html>

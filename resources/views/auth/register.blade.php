<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Driver - Perawatan Armada</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS System -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

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
        }

        .auth-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
        }

        .auth-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 36px 32px;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
        }

        .auth-logo-badge {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-md);
            background: var(--accent-gradient);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: var(--shadow-indigo);
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

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
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h1 class="h3 fw-bold mb-1">Daftar Akun Driver</h1>
                <p class="text-secondary small mb-4">Buat akun pengemudi baru untuk sistem perawatan armada</p>
            </div>

            <!-- Notifications / Errors -->
            @if($errors->any())
                <div class="alert-premium danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Form Register -->
            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                
                <div class="form-group-premium">
                    <label for="name" class="form-label-premium">Nama Lengkap</label>
                    <div class="position-relative">
                        <input type="text" class="form-control-premium" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required autofocus style="padding-left: 42px;">
                        <i class="bi bi-person position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 16px;"></i>
                    </div>
                </div>

                <div class="form-group-premium">
                    <label for="email" class="form-label-premium">Alamat Email</label>
                    <div class="position-relative">
                        <input type="email" class="form-control-premium" id="email" name="email" value="{{ old('email') }}" placeholder="nama@driver.com" required style="padding-left: 42px;">
                        <i class="bi bi-envelope position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 16px;"></i>
                    </div>
                </div>

                <div class="form-group-premium">
                    <label for="password" class="form-label-premium">Kata Sandi</label>
                    <div class="position-relative">
                        <input type="password" class="form-control-premium" id="password" name="password" placeholder="Minimal 6 karakter" required style="padding-left: 42px;">
                        <i class="bi bi-lock position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 16px;"></i>
                    </div>
                </div>

                <div class="form-group-premium mb-4">
                    <label for="password_confirmation" class="form-label-premium">Konfirmasi Kata Sandi</label>
                    <div class="position-relative">
                        <input type="password" class="form-control-premium" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi" required style="padding-left: 42px;">
                        <i class="bi bi-check-circle position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 16px;"></i>
                    </div>
                </div>

                <button type="submit" class="btn-premium primary w-100 btn-lg mb-3">
                    Daftar Akun Driver <i class="bi bi-arrow-right ms-1"></i>
                </button>

                <div class="text-center small text-secondary">
                    Sudah memiliki akun? <a href="{{ route('login') }}" class="fw-bold text-primary">Masuk di sini</a>
                </div>
            </form>

        </div>
    </div>

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
        });
    </script>
</body>
</html>
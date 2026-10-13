<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Kantin SKANIC SMKN 1 Ciomas</title>

  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <!-- IonIcons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <style>
    :root {
      --primary-orange: #ea580c;
      --primary-orange-hover: #c2410c;
      --primary-light: #fff7ed;
      --text-dark: #0f172a;
      --text-muted: #64748b;
      --border-color: #cbd5e1;
      --border-focus: #f97316;
    }

    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    html, body {
      height: 100%;
      width: 100%;
      background: #ffffff;
      color: var(--text-dark);
      overflow-x: hidden;
    }

    .login-wrapper {
      display: flex;
      min-height: 100vh;
      width: 100%;
    }

    /* =========================================
       LEFT SIDE: FORM PANEL (SeedProd Style)
    ========================================= */
    .login-form-side {
      width: 42%;
      min-width: 380px;
      max-width: 520px;
      background: #ffffff;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 44px 52px;
      z-index: 10;
      box-shadow: 4px 0 24px rgba(0, 0, 0, 0.04);
    }

    .form-container {
      width: 100%;
      max-width: 360px;
      margin: auto;
    }

    /* Brand Header */
    .brand-box {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
      text-decoration: none;
    }

    .brand-logo-icon {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      background: linear-gradient(135deg, #f97316, #ea580c);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 20px;
      box-shadow: 0 4px 10px rgba(234, 88, 12, 0.25);
    }

    .brand-logo-text {
      font-size: 22px;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.5px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .brand-logo-text span {
      color: var(--primary-orange);
    }

    /* Titles */
    .login-heading {
      font-size: 28px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 6px;
      letter-spacing: -0.5px;
    }

    .login-subheading {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 26px;
    }

    .login-subheading a {
      color: var(--primary-orange);
      text-decoration: none;
      font-weight: 600;
    }

    .login-subheading a:hover {
      text-decoration: underline;
    }

    /* Alert Banner */
    .alert-box {
      padding: 10px 14px;
      border-radius: 6px;
      margin-bottom: 18px;
      font-size: 12.5px;
      display: flex;
      align-items: center;
      gap: 8px;
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
    }

    /* Inputs */
    .form-group {
      margin-bottom: 18px;
    }

    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 6px;
    }

    .input-box {
      position: relative;
    }

    .input-field {
      width: 100%;
      padding: 10px 38px 10px 12px;
      border: 1px solid var(--border-color);
      border-radius: 6px;
      font-size: 13.5px;
      color: #0f172a;
      background: #ffffff;
      outline: none;
      transition: all 0.15s ease;
    }

    .input-field:focus {
      border-color: var(--border-focus);
      box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }

    .input-field::placeholder {
      color: #94a3b8;
    }

    .input-icon-right {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 16px;
      color: #94a3b8;
      pointer-events: none;
    }

    /* Options (Remember & Forgot) */
    .form-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 22px;
      font-size: 12.5px;
    }

    .remember-me {
      display: flex;
      align-items: center;
      gap: 7px;
      color: #475569;
      cursor: pointer;
      user-select: none;
    }

    .remember-me input {
      accent-color: var(--primary-orange);
      cursor: pointer;
    }

    .forgot-link {
      color: var(--primary-orange);
      text-decoration: none;
      font-weight: 500;
    }

    .forgot-link:hover {
      text-decoration: underline;
    }

    /* Buttons */
    .btn-login-primary {
      width: 100%;
      padding: 11px;
      border: none;
      border-radius: 6px;
      background: var(--primary-orange);
      color: #ffffff;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.3px;
      cursor: pointer;
      transition: background 0.15s ease, transform 0.1s ease;
    }

    .btn-login-primary:hover {
      background: var(--primary-orange-hover);
    }

    .btn-login-primary:active {
      transform: scale(0.99);
    }

    /* Quick Demo Login Option */
    .quick-login-divider {
      display: flex;
      align-items: center;
      text-align: center;
      margin: 18px 0;
      color: #94a3b8;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .quick-login-divider::before, .quick-login-divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid #e2e8f0;
    }

    .quick-login-divider::before { margin-right: 10px; }
    .quick-login-divider::after { margin-left: 10px; }

    .btn-quick-demo {
      width: 100%;
      padding: 9px 12px;
      border: 1px dashed #f97316;
      border-radius: 6px;
      background: var(--primary-light);
      color: #c2410c;
      font-size: 12.5px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.15s ease;
    }

    .btn-quick-demo:hover {
      background: #ffedd5;
    }

    /* Footer */
    .form-footer {
      font-size: 11px;
      color: #94a3b8;
      text-align: center;
      line-height: 1.6;
      padding-top: 20px;
    }

    .form-footer a {
      color: #64748b;
      text-decoration: none;
    }

    .form-footer a:hover {
      color: var(--primary-orange);
      text-decoration: underline;
    }

    /* =========================================
       RIGHT SIDE: SEEDPROD ART PANEL
    ========================================= */
    .login-hero-side {
      flex: 1;
      background: #f1f5f9 url('{{ asset("assets/images/login-hero.jpg") }}') center center / cover no-repeat;
      position: relative;
      display: flex;
      align-items: flex-end;
      justify-content: flex-start;
      overflow: hidden;
    }

    /* Subtle gradient overlay to soften edges */
    .login-hero-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to right, rgba(0, 0, 0, 0.06) 0%, transparent 12%);
      pointer-events: none;
    }

    .hero-badge-tag {
      position: absolute;
      bottom: 28px;
      right: 28px;
      background: rgba(15, 23, 42, 0.72);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #ffffff;
      padding: 8px 16px;
      border-radius: 30px;
      font-size: 12px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .hero-badge-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 8px #22c55e;
    }

    /* Responsive */
    @media (max-width: 900px) {
      .login-form-side {
        width: 100%;
        max-width: 100%;
        padding: 36px 28px;
      }
      .login-hero-side {
        display: none;
      }
    }
  </style>
</head>
<body>

  <div class="login-wrapper">

    <!-- LEFT SIDE: CLEAN SEEDPROD FORM -->
    <div class="login-form-side">
      <div></div> <!-- Spacer -->

      <div class="form-container">
        
        <!-- Logo -->
        <a href="{{ route('home') }}" class="brand-box">
          <div class="brand-logo-icon">
            <ion-icon name="restaurant"></ion-icon>
          </div>
          <div class="brand-logo-text">
            Kantin<span>SKANIC</span>
          </div>
        </a>

        <!-- Titles -->
        <h1 class="login-heading">Login</h1>
        <p class="login-subheading">
          Don't have an account? <a href="{{ route('home') }}">Lihat Menu Kantin</a>
        </p>

        <!-- Error Alert -->
        @if($errors->any())
          <div class="alert-box">
            <ion-icon name="alert-circle" style="font-size:16px; flex-shrink:0;"></ion-icon>
            <div>{{ $errors->first() }}</div>
          </div>
        @endif

        @if(session('info'))
          <div class="alert-box" style="background:#f0f9ff; border-color:#bae6fd; color:#0369a1;">
            <ion-icon name="information-circle" style="font-size:16px; flex-shrink:0;"></ion-icon>
            <div>{{ session('info') }}</div>
          </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('admin.login.submit') }}" method="POST">
          @csrf

          <!-- Email -->
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-box">
              <input type="email" name="email" class="input-field" value="{{ old('email', 'admin@smkn1ciomas.sch.id') }}" placeholder="admin@smkn1ciomas.sch.id" required autofocus>
              <ion-icon name="mail-outline" class="input-icon-right"></ion-icon>
            </div>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-box">
              <input type="password" name="password" id="passwordInput" class="input-field" value="admin123" placeholder="••••••••" required>
              <ion-icon name="lock-closed-outline" class="input-icon-right" id="togglePassword" style="cursor:pointer; pointer-events:auto;" onclick="togglePasswordVisibility()"></ion-icon>
            </div>
          </div>

          <!-- Options -->
          <div class="form-options">
            <label class="remember-me">
              <input type="checkbox" name="remember" checked>
              <span>Remember Me</span>
            </label>
            <a href="{{ route('home') }}" class="forgot-link">Website Kantin</a>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-login-primary">
            Log In
          </button>
        </form>

        <!-- Quick 1-Click Demo Access -->
        <div class="quick-login-divider">Akses Cepat</div>

        <form action="{{ route('admin.quick-login') }}" method="POST">
          @csrf
          <button type="submit" class="btn-quick-demo">
            <ion-icon name="flash-outline"></ion-icon>
            <span>Masuk Cepat Demo (1-Klik)</span>
          </button>
        </form>

      </div>

      <!-- Footer Info -->
      <footer class="form-footer">
        <div>Copyright &copy; 2026 Kantin SKANIC. All rights reserved.</div>
        <div style="margin-top: 4px;">
          <a href="{{ route('home') }}">Beranda</a> &bull;
          <a href="{{ route('contact') }}">Kontak</a> &bull;
          <a href="{{ route('info') }}">Informasi Sekolah</a>
        </div>
      </footer>
    </div>

    <!-- RIGHT SIDE: HERO ILLUSTRATION ARTWORK -->
    <div class="login-hero-side">
      <div class="login-hero-overlay"></div>
      
      <div class="hero-badge-tag">
        <div class="hero-badge-dot"></div>
        <span>SMKN 1 Ciomas &bull; Kantin Digital POS</span>
      </div>
    </div>

  </div>

  <script>
    function togglePasswordVisibility() {
      const input = document.getElementById('passwordInput');
      const icon = document.getElementById('togglePassword');
      if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('name', 'eye-off-outline');
      } else {
        input.type = 'password';
        icon.setAttribute('name', 'lock-closed-outline');
      }
    }
  </script>
</body>
</html>

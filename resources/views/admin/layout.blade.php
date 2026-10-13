<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin Panel') - Kantin SMKN 1 Ciomas</title>

  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

  <!-- Google Fonts: Inter & Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

  <!-- IonIcons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <style>
    :root {
      --primary: #f97316;
      --primary-hover: #ea580c;
      --primary-soft: #fff7ed;
      --sidebar-bg: #090d16;
      --sidebar-border: #1e293b;
      --sidebar-hover: #172033;
      --body-bg: #f8fafc;
      --surface: #ffffff;
      --border: #e2e8f0;
      --border-subtle: #f1f5f9;
      --text: #0f172a;
      --text-muted: #64748b;
      --text-subtle: #94a3b8;
      
      --radius-sm: 6px;
      --radius-md: 8px;
      --radius-lg: 12px;
      --radius-full: 9999px;
      
      --sidebar-w: 240px;
      --topbar-h: 58px;
    }

    *, *::before, *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    body {
      background-color: var(--body-bg);
      color: var(--text);
      font-size: 13.5px;
      line-height: 1.5;
      min-height: 100vh;
      display: flex;
      -webkit-font-smoothing: antialiased;
    }

    /* =========================================
       SIDEBAR
    ========================================= */
    .admin-sidebar {
      width: var(--sidebar-w);
      background: var(--sidebar-bg);
      border-right: 1px solid var(--sidebar-border);
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 100;
      display: flex;
      flex-direction: column;
      transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .sidebar-header {
      height: var(--topbar-h);
      padding: 0 16px;
      display: flex;
      align-items: center;
      gap: 10px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .brand-icon {
      width: 32px;
      height: 32px;
      border-radius: var(--radius-md);
      background: linear-gradient(135deg, #f97316, #ea580c);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 17px;
      box-shadow: 0 2px 6px rgba(249, 115, 22, 0.3);
    }

    .brand-text {
      line-height: 1.15;
    }

    .brand-title {
      font-size: 13.5px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: -0.2px;
    }

    .brand-subtitle {
      font-size: 11px;
      color: #64748b;
      font-weight: 500;
    }

    .sidebar-nav {
      flex: 1;
      padding: 14px 10px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .nav-label {
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #475569;
      padding: 12px 10px 4px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 10px;
      border-radius: var(--radius-md);
      color: #94a3b8;
      text-decoration: none;
      font-size: 13px;
      font-weight: 500;
      transition: all 0.15s ease;
    }

    .nav-link ion-icon {
      font-size: 17px;
      color: #64748b;
      transition: color 0.15s ease;
    }

    .nav-link:hover {
      background: var(--sidebar-hover);
      color: #ffffff;
    }

    .nav-link:hover ion-icon {
      color: #ffffff;
    }

    .nav-link.active {
      background: rgba(249, 115, 22, 0.12);
      color: #fb923c;
      font-weight: 600;
    }

    .nav-link.active ion-icon {
      color: #fb923c;
    }

    .nav-badge {
      margin-left: auto;
      font-size: 11px;
      font-weight: 700;
      padding: 1px 7px;
      border-radius: var(--radius-full);
      background: #1e293b;
      color: #94a3b8;
    }

    .nav-badge.danger {
      background: #ef4444;
      color: #fff;
    }

    .sidebar-footer {
      padding: 12px 14px;
      border-top: 1px solid rgba(255, 255, 255, 0.07);
      background: rgba(0, 0, 0, 0.2);
    }

    .admin-profile {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .admin-avatar {
      width: 32px;
      height: 32px;
      border-radius: var(--radius-full);
      background: #334155;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #cbd5e1;
      font-size: 14px;
    }

    .admin-user-name {
      font-size: 12.5px;
      font-weight: 600;
      color: #f8fafc;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 140px;
    }

    .admin-user-role {
      font-size: 11px;
      color: #10b981;
      font-weight: 500;
    }

    /* =========================================
       WRAPPER & TOPBAR
    ========================================= */
    .admin-wrapper {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
      min-height: 100vh;
    }

    .admin-topbar {
      height: var(--topbar-h);
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      position: sticky;
      top: 0;
      z-index: 90;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 20px;
      color: var(--text);
      cursor: pointer;
    }

    .topbar-title {
      font-size: 15px;
      font-weight: 700;
      color: var(--text);
      letter-spacing: -0.2px;
    }

    .topbar-subtitle {
      font-size: 12px;
      color: var(--text-muted);
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .btn-public-preview {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: var(--radius-md);
      background: #f8fafc;
      color: #334155;
      font-size: 12.5px;
      font-weight: 600;
      text-decoration: none;
      border: 1px solid var(--border);
      transition: all 0.15s ease;
    }

    .btn-public-preview:hover {
      background: #f1f5f9;
      color: var(--text);
    }

    .btn-logout-clean {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 6px 12px;
      border-radius: var(--radius-md);
      background: transparent;
      color: #ef4444;
      font-size: 12.5px;
      font-weight: 600;
      border: 1px solid #fee2e2;
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .btn-logout-clean:hover {
      background: #fef2f2;
    }

    /* =========================================
       MAIN CONTENT
    ========================================= */
    .admin-content {
      padding: 22px 24px 40px;
      flex: 1;
    }

    /* Flash Messages */
    .alert-banner {
      padding: 10px 14px;
      border-radius: var(--radius-md);
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 13px;
      font-weight: 500;
      animation: fadeIn 0.2s ease;
    }

    .alert-banner.success {
      background: #ecfdf5;
      border: 1px solid #a7f3d0;
      color: #065f46;
    }

    .alert-banner.danger {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
    }

    .alert-banner.info {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      color: #0369a1;
    }

    /* Cards */
    .card-custom {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 18px 20px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
      margin-bottom: 20px;
    }

    .card-header-flex {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .card-header-title {
      font-size: 14.5px;
      font-weight: 700;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Buttons */
    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 14px;
      border-radius: var(--radius-md);
      font-size: 12.5px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.15s ease;
    }

    .btn-primary {
      background: var(--primary);
      color: #fff;
    }

    .btn-primary:hover {
      background: var(--primary-hover);
    }

    .btn-secondary {
      background: #ffffff;
      color: #334155;
      border-color: var(--border);
    }

    .btn-secondary:hover {
      background: #f8fafc;
      color: #0f172a;
    }

    .btn-sm {
      padding: 5px 10px;
      font-size: 12px;
      border-radius: var(--radius-sm);
    }

    /* Tables */
    .table-container {
      width: 100%;
      overflow-x: auto;
    }

    .table-custom {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }

    .table-custom th {
      padding: 9px 12px;
      font-size: 11.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      color: var(--text-muted);
      background: #f8fafc;
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }

    .table-custom td {
      padding: 11px 12px;
      font-size: 13px;
      color: var(--text);
      border-bottom: 1px solid var(--border-subtle);
      vertical-align: middle;
    }

    .table-custom tr:hover td {
      background: #fbfcfe;
    }

    /* Badges */
    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 3px 8px;
      border-radius: var(--radius-full);
      font-size: 11.5px;
      font-weight: 600;
      white-space: nowrap;
    }

    .badge-status.success {
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #d1fae5;
    }

    .badge-status.warning {
      background: #fffbeb;
      color: #b45309;
      border: 1px solid #fef3c7;
    }

    .badge-status.danger {
      background: #fef2f2;
      color: #b91c1c;
      border: 1px solid #fee2e2;
    }

    .badge-status.info {
      background: #f0f9ff;
      color: #0369a1;
      border: 1px solid #e0f2fe;
    }

    .badge-status.gray {
      background: #f1f5f9;
      color: #475569;
      border: 1px solid #e2e8f0;
    }

    /* Ranking badge & Progress bar */
    .rank-badge {
      width: 20px;
      height: 20px;
      border-radius: 4px;
      font-weight: 700;
      font-size: 11px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .rank-badge-gold {
      background: #fef3c7;
      color: #b45309;
    }

    .rank-badge-default {
      background: #f1f5f9;
      color: #64748b;
    }

    .revenue-progress-fill {
      height: 100%;
      background: var(--primary);
      border-radius: 9999px;
    }

    /* Forms */
    .form-group-admin {
      margin-bottom: 14px;
    }

    .form-label-admin {
      display: block;
      font-size: 12.5px;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 5px;
    }

    .form-control-admin {
      width: 100%;
      padding: 7px 11px;
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      font-size: 13px;
      color: var(--text);
      background: #fff;
      transition: all 0.15s ease;
    }

    .form-control-admin:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
    }

    /* Modals */
    .modal-backdrop-custom {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.5);
      backdrop-filter: blur(2px);
      z-index: 1000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .modal-backdrop-custom.active {
      display: flex;
    }

    .modal-card-custom {
      background: #fff;
      border-radius: var(--radius-lg);
      width: 100%;
      max-width: 520px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
      animation: modalSlide 0.2s ease;
    }

    .modal-header-custom {
      padding: 14px 18px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-header-custom h3 {
      font-size: 15px;
      font-weight: 700;
      color: var(--text);
    }

    .modal-close-btn {
      background: none;
      border: none;
      font-size: 20px;
      color: #94a3b8;
      cursor: pointer;
      line-height: 1;
    }

    .modal-body-custom {
      padding: 18px;
    }

    .modal-footer-custom {
      padding: 12px 18px;
      border-top: 1px solid var(--border);
      display: flex;
      justify-content: flex-end;
      gap: 8px;
      background: #f8fafc;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-4px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes modalSlide {
      from { opacity: 0; transform: scale(0.97); }
      to { opacity: 1; transform: scale(1); }
    }

    @media (max-width: 992px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }
      .admin-sidebar.active {
        transform: translateX(0);
      }
      .admin-wrapper {
        margin-left: 0;
      }
      .mobile-toggle {
        display: block;
      }
    }
  </style>

  @stack('styles')
</head>
<body>

  <!-- SIDEBAR NAVIGATION -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
      <div class="brand-icon">
        <ion-icon name="restaurant"></ion-icon>
      </div>
      <div class="brand-text">
        <div class="brand-title">Kantin SKANIC</div>
        <div class="brand-subtitle">SMKN 1 Ciomas Panel</div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-label">Utama</div>
      <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <ion-icon name="grid-outline"></ion-icon>
        <span>Dashboard</span>
      </a>

      <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <ion-icon name="bag-handle-outline"></ion-icon>
        <span>Kelola Pesanan</span>
        @php
          $pendingCount = \App\Models\Order::whereIn('order_status', ['pending_payment', 'confirmed', 'preparing'])->count();
        @endphp
        @if($pendingCount > 0)
          <span class="nav-badge danger">{{ $pendingCount }}</span>
        @endif
      </a>

      <div class="nav-label">Katalog Menu</div>
      <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
        <ion-icon name="fast-food-outline"></ion-icon>
        <span>Daftar Menu</span>
      </a>

      <a href="{{ route('admin.stands.index') }}" class="nav-link {{ request()->routeIs('admin.stands.*') ? 'active' : '' }}">
        <ion-icon name="storefront-outline"></ion-icon>
        <span>Stand Kantin</span>
      </a>

      <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
        <ion-icon name="pricetags-outline"></ion-icon>
        <span>Kategori Menu</span>
      </a>

      <div class="nav-label">Keuangan & Akun</div>
      <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
        <ion-icon name="card-outline"></ion-icon>
        <span>Transaksi Keuangan</span>
      </a>

      <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <ion-icon name="people-outline"></ion-icon>
        <span>Kelola Pengguna</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="admin-profile">
        <div class="admin-avatar">
          <ion-icon name="person"></ion-icon>
        </div>
        <div>
          <div class="admin-user-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
          <div class="admin-user-role">Super Admin</div>
        </div>
      </div>
    </div>
  </aside>

  <!-- MAIN VIEWPORT -->
  <div class="admin-wrapper">
    <!-- TOPBAR -->
    <header class="admin-topbar">
      <div class="topbar-left">
        <button type="button" class="mobile-toggle" onclick="toggleSidebar()" aria-label="Toggle Menu">
          <ion-icon name="menu-outline"></ion-icon>
        </button>
        <div>
          <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
          <div class="topbar-subtitle">@yield('page_subtitle', 'Panel Kontrol Kantin SMKN 1 Ciomas')</div>
        </div>
      </div>

      <div class="topbar-right">
        <a href="{{ route('home') }}" target="_blank" class="btn-public-preview">
          <ion-icon name="open-outline"></ion-icon>
          <span>Kunjungi Website</span>
        </a>

        <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
          @csrf
          <button type="submit" class="btn-logout-clean" title="Keluar">
            <ion-icon name="log-out-outline"></ion-icon>
            <span>Keluar</span>
          </button>
        </form>
      </div>
    </header>

    <!-- CONTENT BODY -->
    <main class="admin-content">
      <!-- Flash Notifications (hanya jika ada flash pesan) -->
      @if(session('success'))
        <div class="alert-banner success">
          <ion-icon name="checkmark-circle-outline" style="font-size:18px;"></ion-icon>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      @if(session('error'))
        <div class="alert-banner danger">
          <ion-icon name="alert-circle-outline" style="font-size:18px;"></ion-icon>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      @if(session('info'))
        <div class="alert-banner info">
          <ion-icon name="information-circle-outline" style="font-size:18px;"></ion-icon>
          <span>{{ session('info') }}</span>
        </div>
      @endif

      @if($errors->any())
        <div class="alert-banner danger">
          <ion-icon name="alert-circle-outline" style="font-size:18px;"></ion-icon>
          <div>
            @foreach($errors->all() as $err)
              <div>&bull; {{ $err }}</div>
            @endforeach
          </div>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  <script>
    function toggleSidebar() {
      const sb = document.getElementById('adminSidebar');
      if (sb) sb.classList.toggle('active');
    }
  </script>

  @stack('scripts')
</body>
</html>

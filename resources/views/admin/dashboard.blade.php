@extends('admin.layout')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan operasional dan transaksi kantin hari ini')

@section('content')

  <!-- 1. KPI STATS ROW (Clean, minimal, SaaS style) -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 22px;">
    
    <!-- Omset Total -->
    <div class="card-custom" style="padding: 16px 18px; margin-bottom: 0;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 8px;">
        <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total Omset</span>
        <div style="width: 32px; height: 32px; border-radius: var(--radius-md); background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 16px;">
          <ion-icon name="wallet-outline"></ion-icon>
        </div>
      </div>
      <div style="font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -0.5px;">
        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
      </div>
      <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px; display:flex; align-items:center; gap:4px;">
        <span style="color: #059669; font-weight: 600;">+Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
        <span>hari ini</span>
      </div>
    </div>

    <!-- Total Pesanan -->
    <div class="card-custom" style="padding: 16px 18px; margin-bottom: 0;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 8px;">
        <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total Pesanan</span>
        <div style="width: 32px; height: 32px; border-radius: var(--radius-md); background: #fff7ed; color: #ea580c; display: flex; align-items: center; justify-content: center; font-size: 16px;">
          <ion-icon name="receipt-outline"></ion-icon>
        </div>
      </div>
      <div style="font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -0.5px;">
        {{ $totalOrders }} <span style="font-size: 14px; font-weight: 600; color: var(--text-muted);">Pesanan</span>
      </div>
      <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
        <span style="color: #ea580c; font-weight: 600;">{{ $todayOrders }} pesanan</span> baru hari ini
      </div>
    </div>

    <!-- Antrean Aktif -->
    <div class="card-custom" style="padding: 16px 18px; margin-bottom: 0;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 8px;">
        <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Perlu Diproses</span>
        <div style="width: 32px; height: 32px; border-radius: var(--radius-md); background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 16px;">
          <ion-icon name="time-outline"></ion-icon>
        </div>
      </div>
      <div style="font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -0.5px;">
        {{ $pendingOrders }} <span style="font-size: 14px; font-weight: 600; color: var(--text-muted);">Antrean</span>
      </div>
      <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
        <span>{{ $completedOrders }} pesanan selesai diambil</span>
      </div>
    </div>

    <!-- Stand & Menu Aktif -->
    <div class="card-custom" style="padding: 16px 18px; margin-bottom: 0;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 8px;">
        <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Stand Kantin</span>
        <div style="width: 32px; height: 32px; border-radius: var(--radius-md); background: #f0f9ff; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 16px;">
          <ion-icon name="storefront-outline"></ion-icon>
        </div>
      </div>
      <div style="font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -0.5px;">
        {{ $openStands }} / {{ $totalStands }} <span style="font-size: 14px; font-weight: 600; color: var(--text-muted);">Buka</span>
      </div>
      <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
        <span>{{ $availableMenus }} menu siap dipesan (dari {{ $totalMenus }})</span>
      </div>
    </div>

  </div>

  <!-- 2. SPLIT SECTION: RECENT ORDERS (LEFT) & STAND + TOP MENUS (RIGHT) -->
  <div style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 20px; align-items: start;">
    
    <!-- LEFT: RECENT ORDERS TABLE -->
    <div class="card-custom" style="padding: 0; overflow: hidden; margin-bottom: 0;">
      <div style="padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
        <div>
          <div style="font-size: 14px; font-weight: 700; color: var(--text);">Pesanan Terbaru Masuk</div>
          <div style="font-size: 12px; color: var(--text-muted);">Daftar pesanan aktif yang perlu dipantau</div>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn-action btn-secondary btn-sm" style="font-size: 12px;">
          <span>Lihat Semua</span>
          <ion-icon name="arrow-forward-outline"></ion-icon>
        </a>
      </div>

      <div class="table-container">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Pemesan</th>
              <th>Menu</th>
              <th>Total</th>
              <th>Status</th>
              <th style="text-align:right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentOrders as $order)
              <tr>
                <td>
                  <span style="font-family:'JetBrains Mono', monospace; font-size: 12px; font-weight: 600; color: var(--primary);">
                    {{ $order->order_number }}
                  </span>
                </td>
                <td>
                  <div style="font-weight: 600; font-size: 13px;">{{ $order->customer_name }}</div>
                  <div style="font-size: 11px; color: var(--text-muted);">{{ ucfirst($order->customer_type) }}</div>
                </td>
                <td>
                  <div style="font-size: 12.5px; color: #334155; max-width: 170px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    @foreach($order->items as $it)
                      {{ $it->quantity }}x {{ $it->menu_name }}@if(!$loop->last), @endif
                    @endforeach
                  </div>
                </td>
                <td>
                  <div style="font-weight: 700; font-size: 13px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                </td>
                <td>
                  @php
                    $badgeInfo = match($order->order_status) {
                      'pending_payment' => ['warning', 'Menunggu Bayar'],
                      'confirmed' => ['info', 'Dikonfirmasi'],
                      'preparing' => ['warning', 'Dimasak'],
                      'ready_for_pickup' => ['success', 'Siap Ambil'],
                      'completed' => ['gray', 'Selesai'],
                      'cancelled' => ['danger', 'Batal'],
                      default => ['gray', $order->order_status]
                    };
                  @endphp
                  <span class="badge-status {{ $badgeInfo[0] }}">
                    <span>{{ $badgeInfo[1] }}</span>
                  </span>
                </td>
                <td style="text-align: right;">
                  <a href="{{ route('admin.orders.index', ['search' => $order->order_number]) }}" class="btn-action btn-secondary btn-sm" style="padding: 3px 8px; font-size: 11.5px;">
                    Detail
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center; padding: 28px; color: var(--text-muted); font-size: 13px;">
                  Belum ada pesanan terbaru.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- RIGHT: STAND PERFORMANCE & TOP MENUS -->
    <div style="display: flex; flex-direction: column; gap: 18px;">
      
      <!-- Stand Revenue List -->
      <div class="card-custom" style="padding: 16px 18px; margin-bottom: 0;">
        <div style="font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 12px; display:flex; justify-content:space-between; align-items:center;">
          <span>Omset Stand Kantin</span>
          <span style="font-size: 11px; color: var(--text-muted); font-weight: 500;">Keseluruhan</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
          @php
            $maxRev = $stands->max('total_revenue') ?: 1;
          @endphp
          @forelse($stands as $st)
            <div>
              <div style="display:flex; justify-content:space-between; font-size: 12.5px; margin-bottom: 3px;">
                <span style="font-weight: 600; color: var(--text);">{{ $st->name }}</span>
                <span style="font-weight: 700; color: var(--text);">Rp {{ number_format($st->total_revenue ?? 0, 0, ',', '.') }}</span>
              </div>
              <div style="height: 5px; border-radius: 9999px; background: #f1f5f9; overflow: hidden;">
                @php
                  $pct = min(100, round((($st->total_revenue ?? 0) / $maxRev) * 100));
                @endphp
                <div class="revenue-progress-fill" @style(['width: ' . $pct . '%'])></div>
              </div>
            </div>
          @empty
            <div style="color: var(--text-muted); font-size: 12px;">Belum ada data stand.</div>
          @endforelse
        </div>
      </div>

      <!-- Top Selling Menus -->
      <div class="card-custom" style="padding: 16px 18px; margin-bottom: 0;">
        <div style="font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 12px; display:flex; justify-content:space-between; align-items:center;">
          <span>Menu Terpopuler</span>
          <a href="{{ route('admin.menus.index') }}" style="font-size: 11.5px; color: var(--primary); text-decoration:none; font-weight:600;">Katalog Menu</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
          @forelse($topMenus as $idx => $m)
            <div style="display:flex; align-items:center; justify-content:space-between; font-size: 12.5px;">
              <div style="display:flex; align-items:center; gap: 8px;">
                <span class="rank-badge {{ $idx === 0 ? 'rank-badge-gold' : 'rank-badge-default' }}">
                  {{ $idx + 1 }}
                </span>
                <div>
                  <div style="font-weight: 600; color: var(--text);">{{ $m->name }}</div>
                  <div style="font-size: 11px; color: var(--text-muted);">{{ $m->category->name ?? 'Kategori' }} &bull; Rp {{ number_format($m->price, 0, ',', '.') }}</div>
                </div>
              </div>
              <div style="text-align:right;">
                <div style="font-weight: 700; color: var(--text);">{{ $m->sold_count ?? 0 }} dipesan</div>
              </div>
            </div>
          @empty
            <div style="color: var(--text-muted); font-size: 12px;">Belum ada data menu terpopuler.</div>
          @endforelse
        </div>
      </div>

    </div>

  </div>

@endsection

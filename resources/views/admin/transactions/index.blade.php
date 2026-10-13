@extends('admin.layout')

@section('title', 'Riwayat Transaksi & Pembayaran')
@section('page_title', 'Transaksi Keuangan & Pembayaran')
@section('page_subtitle', 'Pantau mutasi pembayaran Midtrans (QRIS / VA) dan pembayaran tunai kantin')

@section('content')

  <!-- 1. KPI FINANCIAL CARDS -->
  <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:20px;">
    
    <div class="card-custom" style="padding:14px 18px; margin-bottom:0;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div>
          <div style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Dana Lunas (Sukses)</div>
          <div style="font-size:20px; font-weight:800; color:#047857; margin-top:4px;">
            Rp {{ number_format($totalPaid, 0, ',', '.') }}
          </div>
        </div>
        <div style="width:32px; height:32px; border-radius:6px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:16px;">
          <ion-icon name="wallet-outline"></ion-icon>
        </div>
      </div>
    </div>

    <div class="card-custom" style="padding:14px 18px; margin-bottom:0;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div>
          <div style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Menunggu Bayar</div>
          <div style="font-size:20px; font-weight:800; color:#b45309; margin-top:4px;">
            Rp {{ number_format($totalPending, 0, ',', '.') }}
          </div>
        </div>
        <div style="width:32px; height:32px; border-radius:6px; background:#fffbeb; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:16px;">
          <ion-icon name="time-outline"></ion-icon>
        </div>
      </div>
    </div>

    <div class="card-custom" style="padding:14px 18px; margin-bottom:0;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div>
          <div style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Midtrans Gateway</div>
          <div style="font-size:20px; font-weight:800; color:#0284c7; margin-top:4px;">
            Rp {{ number_format($totalMidtrans, 0, ',', '.') }}
          </div>
        </div>
        <div style="width:32px; height:32px; border-radius:6px; background:#f0f9ff; color:#0284c7; display:flex; align-items:center; justify-content:center; font-size:16px;">
          <ion-icon name="card-outline"></ion-icon>
        </div>
      </div>
    </div>

    <div class="card-custom" style="padding:14px 18px; margin-bottom:0;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div>
          <div style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Kasir / Tunai</div>
          <div style="font-size:20px; font-weight:800; color:var(--primary); margin-top:4px;">
            Rp {{ number_format($totalCash, 0, ',', '.') }}
          </div>
        </div>
        <div style="width:32px; height:32px; border-radius:6px; background:#fff7ed; color:#ea580c; display:flex; align-items:center; justify-content:center; font-size:16px;">
          <ion-icon name="cash-outline"></ion-icon>
        </div>
      </div>
    </div>

  </div>

  <!-- 2. FILTER & SEARCH BAR -->
  <div class="card-custom" style="padding:18px 22px; margin-bottom:24px;">
    <form action="{{ route('admin.transactions.index') }}" method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
      
      <!-- Search input -->
      <div style="position:relative; min-width:240px; flex:1;">
        <ion-icon name="search-outline" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:1.8rem; color:#94a3b8;"></ion-icon>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari ref pembayaran, ID Midtrans, pesanan..." class="form-control-admin" style="padding-left:38px;">
      </div>

      <!-- Status dropdown -->
      <select name="status" class="form-control-admin" style="width:160px;">
        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Lunas / Sukses</option>
        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Bayar</option>
        <option value="expired" {{ $status === 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
        <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Gagal</option>
      </select>

      <!-- Gateway dropdown -->
      <select name="gateway" class="form-control-admin" style="width:160px;">
        <option value="all" {{ $gateway === 'all' ? 'selected' : '' }}>Semua Saluran</option>
        <option value="midtrans" {{ $gateway === 'midtrans' ? 'selected' : '' }}>Midtrans Gateway</option>
        <option value="cash" {{ $gateway === 'cash' ? 'selected' : '' }}>Kasir / Tunai</option>
      </select>

      <button type="submit" class="btn-action btn-secondary" style="padding:10px 16px;">
        <ion-icon name="filter-outline"></ion-icon>
        <span>Filter</span>
      </button>

      @if(!empty($search) || $status !== 'all' || $gateway !== 'all')
        <a href="{{ route('admin.transactions.index') }}" class="btn-action btn-secondary" style="padding:10px 14px;">
          <span>Reset</span>
        </a>
      @endif

    </form>
  </div>

  <!-- 3. TRANSACTIONS TABLE -->
  <div class="card-custom">
    <div class="table-container">
      <table class="table-custom">
        <thead>
          <tr>
            <th>Ref. Pembayaran</th>
            <th>No. Pesanan & Pelanggan</th>
            <th>Saluran & Metode</th>
            <th>Nominal Transaksi</th>
            <th>Status Pembayaran</th>
            <th>Waktu Dibuat / Lunas</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $payment)
            <tr>
              <!-- Ref Pembayaran -->
              <td>
                <div style="font-weight:600; font-family:'JetBrains Mono', monospace; font-size:12.5px; color:var(--text);">
                  {{ $payment->payment_reference }}
                </div>
                @if($payment->gateway_transaction_id)
                  <div style="font-size:11px; color:#64748b; font-family:'JetBrains Mono', monospace;">
                    ID: {{ $payment->gateway_transaction_id }}
                  </div>
                @endif
              </td>

              <!-- Pesanan & Pelanggan -->
              <td>
                @if($payment->order)
                  <a href="{{ route('admin.orders.index', ['search' => $payment->order->order_number]) }}" style="font-weight:600; font-size:12.5px; color:var(--primary); text-decoration:none;">
                    {{ $payment->order->order_number }}
                  </a>
                  <div style="font-size:12px; color:var(--text); font-weight:500;">
                    {{ $payment->order->customer_name }}
                  </div>
                @else
                  <span style="color:#94a3b8; font-size:12px;">Pesanan terhapus</span>
                @endif
              </td>

              <!-- Gateway & Metode -->
              <td>
                @if($payment->gateway === 'midtrans')
                  <span class="badge-status info" style="font-size:11.5px;">
                    <ion-icon name="globe-outline"></ion-icon>
                    <span>Midtrans ({{ strtoupper($payment->payment_method ?? 'QRIS') }})</span>
                  </span>
                @else
                  <span class="badge-status gray" style="font-size:11.5px;">
                    <ion-icon name="cash-outline"></ion-icon>
                    <span>Kasir / Tunai</span>
                  </span>
                @endif
              </td>

              <!-- Nominal -->
              <td>
                <div style="font-weight:700; font-size:13.5px; color:var(--text);">
                  Rp {{ number_format($payment->amount, 0, ',', '.') }}
                </div>
              </td>

              <!-- Status -->
              <td>
                @if($payment->status === 'paid' || $payment->status === 'settled')
                  <span class="badge-status success">
                    <ion-icon name="checkmark-circle"></ion-icon>
                    <span>Lunas</span>
                  </span>
                @elseif($payment->status === 'pending')
                  <span class="badge-status warning">
                    <ion-icon name="time-outline"></ion-icon>
                    <span>Menunggu Bayar</span>
                  </span>
                @elseif($payment->status === 'expired')
                  <span class="badge-status gray">
                    <ion-icon name="hourglass-outline"></ion-icon>
                    <span>Kedaluwarsa</span>
                  </span>
                @else
                  <span class="badge-status danger">
                    <ion-icon name="close-circle"></ion-icon>
                    <span>{{ ucfirst($payment->status) }}</span>
                  </span>
                @endif
              </td>

              <!-- Waktu -->
              <td>
                <div style="font-size:1.25rem; color:var(--dark);">
                  {{ $payment->created_at->format('d M Y, H:i') }}
                </div>
                @if($payment->paid_at)
                  <div style="font-size:1.15rem; color:#065f46; font-weight:600;">
                    Lunas: {{ \Carbon\Carbon::parse($payment->paid_at)->format('H:i:s') }}
                  </div>
                @endif
              </td>

              <!-- Aksi -->
              <td style="text-align:right;">
                @if($payment->status === 'pending')
                  <form action="{{ route('admin.transactions.mark-paid', $payment->id) }}" method="POST" data-confirm="Tandai pembayaran {{ $payment->payment_reference }} sebesar Rp {{ number_format($payment->amount, 0, ',', '.') }} sebagai Lunas?" onsubmit="return confirm(this.dataset.confirm);" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-action btn-primary btn-sm" title="Konfirmasi Pembayaran Lunas">
                      <ion-icon name="checkmark-done-outline"></ion-icon>
                      <span>Tandai Lunas</span>
                    </button>
                  </form>
                @else
                  <span style="font-size:1.15rem; color:#94a3b8; font-weight:600;">Terverifikasi</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center; padding:35px; color:#64748b;">
                Tidak ada data transaksi pembayaran yang ditemukan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top:20px;">
      {{ $payments->links() }}
    </div>
  </div>

@endsection

@extends('admin.layout')

@section('title', 'Kelola Pesanan Kantin')
@section('page_title', 'Kelola Pesanan')
@section('page_subtitle', 'Pantau antrean pesanan, update status pemesanan, dan cetak struk kasir')

@section('content')

@push('styles')
<style>
  .order-tab-link {
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: var(--radius-md);
    font-size: 12.5px;
    font-weight: 600;
    white-space: nowrap;
    transition: all 0.15s ease;
    background: #fff;
    color: #64748b;
    border: 1px solid var(--border);
  }
  .order-tab-link:hover {
    background: #f8fafc;
    color: var(--text);
  }
  .order-tab-link.active {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
  }
  .order-tab-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: var(--radius-full);
    background: #f1f5f9;
    color: #475569;
  }
  .order-tab-link.active .order-tab-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #fff;
  }
</style>
@endpush

  <!-- 1. STATUS FILTER TABS -->
  <div style="display:flex; gap:8px; overflow-x:auto; padding-bottom:14px; margin-bottom:14px;">
    @php
      $tabs = [
        'all' => ['Semua', $counts['all'], 'grid-outline'],
        'pending_payment' => ['Menunggu Bayar', $counts['pending_payment'], 'time-outline'],
        'confirmed' => ['Dikonfirmasi', $counts['confirmed'], 'checkmark-circle-outline'],
        'preparing' => ['Sedang Dimasak', $counts['preparing'], 'restaurant-outline'],
        'ready_for_pickup' => ['Siap Ambil', $counts['ready_for_pickup'], 'bag-check-outline'],
        'completed' => ['Selesai', $counts['completed'], 'checkmark-done-outline'],
        'cancelled' => ['Dibatalkan', $counts['cancelled'], 'close-circle-outline'],
      ];
    @endphp

    @foreach($tabs as $key => $tab)
      <a href="{{ route('admin.orders.index', ['status' => $key, 'search' => $search, 'date' => $dateFilter]) }}" 
         class="order-tab-link {{ $statusFilter === $key ? 'active' : '' }}">
        <ion-icon name="{{ $tab[2] }}"></ion-icon>
        <span>{{ $tab[0] }}</span>
        <span class="order-tab-badge">
          {{ $tab[1] }}
        </span>
      </a>
    @endforeach
  </div>

  <!-- 2. SEARCH & FILTER BAR -->
  <div class="card-custom" style="padding:16px 20px; margin-bottom:20px;">
    <form action="{{ route('admin.orders.index') }}" method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
      <input type="hidden" name="status" value="{{ $statusFilter }}">

      <div style="display:flex; gap:10px; flex:1; min-width:280px;">
        <div style="position:relative; flex:1;">
          <ion-icon name="search-outline" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:1.8rem; color:#94a3b8;"></ion-icon>
          <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode pesanan (SKN-...), nama siswa, no WA..." class="form-control-admin" style="padding-left:38px;">
        </div>
        <input type="date" name="date" value="{{ $dateFilter }}" class="form-control-admin" style="width:160px;">
        <button type="submit" class="btn-action btn-primary" style="padding:10px 16px;">
          <ion-icon name="filter-outline"></ion-icon>
          <span>Filter</span>
        </button>
      </div>

      @if(!empty($search) || !empty($dateFilter) || $statusFilter !== 'all')
        <a href="{{ route('admin.orders.index') }}" class="btn-action btn-secondary" style="padding:10px 16px;">
          <ion-icon name="refresh-outline"></ion-icon>
          <span>Reset Filter</span>
        </a>
      @endif
    </form>
  </div>

  <!-- 3. ORDERS TABLE -->
  <div class="card-custom">
    <div class="table-container">
      <table class="table-custom">
        <thead>
          <tr>
            <th>Kode Pesanan</th>
            <th>Pemesan & Kontak</th>
            <th>Jadwal & Pengambilan</th>
            <th>Rincian Menu Pesanan</th>
            <th>Total Bayar</th>
            <th>Status Pesanan</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr>
              <!-- Kode & Waktu -->
              <td>
                <span style="font-family:'JetBrains Mono', monospace; font-size:12.5px; color:var(--primary); font-weight:700;">{{ $order->order_number }}</span>
                <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">{{ $order->created_at->format('d M, H:i') }} WIB</div>
                <div style="font-size:11px; color:#94a3b8;">{{ $order->created_at->diffForHumans() }}</div>
              </td>

              <!-- Pemesan -->
              <td>
                <div style="font-weight:600; font-size:13px; color:var(--text);">{{ $order->customer_name }}</div>
                <div style="font-size:11.5px; color:var(--text-muted);">
                  <span>{{ ucfirst($order->customer_type) }}</span>
                </div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" style="display:inline-flex; align-items:center; gap:3px; font-size:11.5px; color:#16a34a; text-decoration:none; font-weight:600; margin-top:2px;">
                  <ion-icon name="logo-whatsapp"></ion-icon>
                  <span>{{ $order->customer_phone }}</span>
                </a>
              </td>

              <!-- Jadwal & Lokasi -->
              <td>
                <div style="font-size:12.5px; font-weight:600; color:var(--text);">{{ $order->pickup_time_slot }}</div>
                @if($order->pickup_type === 'deliver_to_room')
                  <div style="font-size:11.5px; color:#2563eb; margin-top:2px; display:flex; align-items:center; gap:4px; font-weight:600;">
                    <ion-icon name="navigate-outline"></ion-icon>
                    <span>Antar: {{ $order->delivery_location ?? 'Ruangan' }}</span>
                  </div>
                @else
                  <div style="font-size:11.5px; color:#64748b; margin-top:2px; display:flex; align-items:center; gap:4px;">
                    <ion-icon name="storefront-outline"></ion-icon>
                    <span>Ambil di Stand</span>
                  </div>
                @endif
              </td>

              <!-- Menu Items -->
              <td>
                <div style="display:flex; flex-direction:column; gap:2px; max-width:240px;">
                  @foreach($order->items as $item)
                    <div style="font-size:12.5px; color:#334155; line-height:1.3;">
                      <strong>{{ $item->quantity }}x</strong> {{ $item->menu_name }}
                      <span style="font-size:11px; color:#94a3b8;">({{ $item->stand->name ?? 'Stand' }})</span>
                    </div>
                  @endforeach
                  @if($order->notes)
                    <div style="font-size:11.5px; color:#ea580c; font-style:italic; margin-top:2px;">
                      "{{ $order->notes }}"
                    </div>
                  @endif
                </div>
              </td>

              <!-- Total & Metode -->
              <td>
                <div style="font-weight:700; font-size:13.5px; color:var(--text);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                @php
                  $isPaid = $order->isPaid();
                  $paymentMethod = $order->payments->first()->payment_method ?? 'qris';
                @endphp
                <span class="badge-status {{ $isPaid ? 'success' : 'warning' }}" style="margin-top:4px;">
                  {{ strtoupper($paymentMethod) }} &bull; {{ $isPaid ? 'Lunas' : 'Belum Lunas' }}
                </span>
              </td>

              <!-- Status -->
              <td>
                @php
                  $badgeMap = [
                    'pending_payment' => ['warning', 'Menunggu Bayar'],
                    'confirmed' => ['info', 'Dikonfirmasi'],
                    'paid' => ['info', 'Lunas / Siap'],
                    'preparing' => ['warning', 'Sedang Masak'],
                    'ready_for_pickup' => ['success', 'Siap Diambil'],
                    'completed' => ['gray', 'Selesai'],
                    'cancelled' => ['danger', 'Dibatalkan'],
                  ];
                  $currBadge = $badgeMap[$order->order_status] ?? ['gray', $order->order_status];
                @endphp
                <span class="badge-status {{ $currBadge[0] }}">{{ $currBadge[1] }}</span>

                <!-- Quick status update select -->
                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="margin-top:6px;">
                  @csrf
                  @method('PATCH')
                  <select name="order_status" onchange="this.form.submit()" style="padding:4px 6px; font-size:11.5px; border:1px solid #cbd5e1; border-radius:6px; background:#fff; cursor:pointer; width:100%;">
                    <option value="pending_payment" {{ $order->order_status === 'pending_payment' ? 'selected' : '' }}>Pending Bayar</option>
                    <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>Konfirmasi</option>
                    <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>Dimasak</option>
                    <option value="ready_for_pickup" {{ $order->order_status === 'ready_for_pickup' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Batalkan</option>
                  </select>
                </form>
              </td>

              <!-- Aksi -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <!-- Detail modal trigger -->
                  <button type="button" class="btn-action btn-secondary btn-sm" data-order="{{ json_encode($order) }}" onclick="showOrderDetailModal(this)" title="Lihat Rekap Lengkap">
                    <ion-icon name="receipt-outline"></ion-icon>
                  </button>

                  <!-- Print receipt -->
                  <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn-action btn-secondary btn-sm" title="Cetak Struk Kasir">
                    <ion-icon name="print-outline"></ion-icon>
                  </a>

                  <!-- Delete -->
                  <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus pesanan ini secara permanen?" onsubmit="return confirm(this.dataset.confirm);" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-secondary btn-sm" style="color:var(--rose);" title="Hapus Pesanan">
                      <ion-icon name="trash-outline"></ion-icon>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center; padding:40px; color:#64748b;">
                <ion-icon name="receipt-outline" style="font-size:3.6rem; color:#cbd5e1; display:block; margin:0 auto 10px;"></ion-icon>
                <div style="font-size:1.5rem; font-weight:700;">Tidak ada pesanan yang sesuai filter</div>
                <div style="font-size:1.25rem;">Coba ubah filter status atau kata kunci pencarian.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top:20px;">
      {{ $orders->links() }}
    </div>
  </div>

  <!-- 4. ORDER DETAIL REKAP MODAL -->
  <div class="modal-backdrop-custom" id="orderDetailModalBackdrop" onclick="if(event.target===this) closeOrderDetailModal()">
    <div class="modal-card-custom" style="max-width:540px;">
      <div class="modal-header-custom">
        <div>
          <h3>Rekap Pesanan Kasir</h3>
          <p id="modalOrderCode" style="font-size:1.2rem; color:var(--text-muted); font-family:'JetBrains Mono', monospace;">SKN-0000</p>
        </div>
        <button class="modal-close-btn" onclick="closeOrderDetailModal()">&times;</button>
      </div>

      <div class="modal-body-custom" id="modalOrderBody">
        <!-- Injected via JavaScript -->
      </div>

      <div class="modal-footer-custom">
        <a href="#" id="modalPrintBtn" target="_blank" class="btn-action btn-primary btn-sm">
          <ion-icon name="print-outline"></ion-icon>
          <span>Cetak Struk Kasir</span>
        </a>
        <button type="button" class="btn-action btn-secondary btn-sm" onclick="closeOrderDetailModal()">Tutup</button>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  function showOrderDetailModal(source) {
    const order = (source && source.dataset && source.dataset.order) ? JSON.parse(source.dataset.order) : source;
    const backdrop = document.getElementById('orderDetailModalBackdrop');
    const codeEl = document.getElementById('modalOrderCode');
    const bodyEl = document.getElementById('modalOrderBody');
    const printBtn = document.getElementById('modalPrintBtn');

    if (!backdrop || !bodyEl) return;

    codeEl.textContent = order.order_number;
    printBtn.href = `/admin/orders/${order.id}/print`;

    let itemsHtml = '';
    (order.items || []).forEach((it, idx) => {
      const standName = (it.stand && it.stand.name) ? it.stand.name : 'Stand Kantin';
      itemsHtml += `
        <div style="display:flex; justify-content:space-between; align-items:flex-start; padding:8px 0; border-bottom:1px dashed #e2e8f0; font-size:1.3rem;">
          <div>
            <div style="font-weight:700; color:#0f172a;">${it.menu_name}</div>
            <div style="font-size:1.15rem; color:#64748b;">${standName} &bull; ${it.quantity}x @ Rp ${Number(it.unit_price).toLocaleString('id-ID')}</div>
            ${it.notes ? `<div style="font-size:1.1rem; color:#ea580c; font-style:italic;">"${it.notes}"</div>` : ''}
          </div>
          <div style="font-weight:700; color:#0f172a;">
            Rp ${Number(it.subtotal).toLocaleString('id-ID')}
          </div>
        </div>
      `;
    });

    const isPaid = (order.order_status === 'confirmed' || order.order_status === 'paid' || order.order_status === 'completed');

    bodyEl.innerHTML = `
      <div style="background:#f8fafc; border-radius:12px; padding:14px; margin-bottom:16px; border:1px solid #e2e8f0; font-size:1.3rem;">
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:#64748b;">Nama Pemesan:</span>
          <strong>${order.customer_name} (${order.customer_type})</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:#64748b;">Nomor WhatsApp:</span>
          <a href="https://wa.me/${(order.customer_phone || '').replace(/[^0-9]/g, '')}" target="_blank" style="color:#16a34a; font-weight:700; text-decoration:none;">${order.customer_phone}</a>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:#64748b;">Jadwal Pengambilan:</span>
          <strong>${order.pickup_time_slot || '-'}</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:#64748b;">Tipe Pengambilan:</span>
          <strong>${order.pickup_type === 'deliver_to_room' ? 'Antar ke ' + (order.delivery_location || 'Ruangan') : 'Ambil di Stand Kantin'}</strong>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; padding-top:8px; border-top:1px dashed #cbd5e1;">
          <span style="color:#64748b;">Status Pembayaran:</span>
          <span class="badge-status ${isPaid ? 'success' : 'warning'}">${isPaid ? 'LUNAS' : 'MENUNGGU PEMBAYARAN'}</span>
        </div>
      </div>

      <div style="margin-bottom:14px;">
        <h4 style="font-size:1.35rem; font-weight:800; color:#0f172a; margin-bottom:10px;">Item Menu Yang Dipesan:</h4>
        ${itemsHtml}
      </div>

      <div style="border-top:1px solid #e2e8f0; padding-top:10px; font-size:1.3rem;">
        <div style="display:flex; justify-content:space-between; color:#64748b; margin-bottom:4px;">
          <span>Subtotal:</span>
          <span>Rp ${Number(order.subtotal || 0).toLocaleString('id-ID')}</span>
        </div>
        <div style="display:flex; justify-content:space-between; color:#64748b; margin-bottom:6px;">
          <span>Biaya Layanan Kantin:</span>
          <span>Rp ${Number(order.service_fee || 500).toLocaleString('id-ID')}</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:1.55rem; font-weight:800; color:var(--dark); border-top:1px dashed #cbd5e1; padding-top:8px;">
          <span>Total Tagihan:</span>
          <span style="color:var(--primary);">Rp ${Number(order.total_amount).toLocaleString('id-ID')}</span>
        </div>
      </div>
    `;

    backdrop.classList.add('active');
  }

  function closeOrderDetailModal() {
    const backdrop = document.getElementById('orderDetailModalBackdrop');
    if (backdrop) backdrop.classList.remove('active');
  }
</script>
@endpush

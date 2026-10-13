<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Struk Pesanan #{{ $order->order_number }} - Kantin SMKN 1 Ciomas</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Courier New', Courier, monospace;
    }
    body {
      width: 100%;
      max-width: 380px;
      margin: 0 auto;
      padding: 20px 10px;
      color: #000;
      font-size: 13px;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bold { font-weight: bold; }
    .dashed-line { border-bottom: 1px dashed #000; margin: 8px 0; }
    .double-line { border-bottom: 2px solid #000; margin: 8px 0; }
    .row { display: flex; justify-content: space-between; margin-bottom: 4px; }
    .item-row { margin-bottom: 6px; }
    .print-btn-bar { margin-bottom: 16px; text-align: center; }
    .btn-print {
      padding: 8px 16px;
      background: #0f172a;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-family: sans-serif;
      font-size: 13px;
    }
    @media print {
      .print-btn-bar { display: none; }
      body { width: 100%; max-width: 100%; padding: 0; }
    }
  </style>
</head>
<body>

  <div class="print-btn-bar">
    <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk Sekarang</button>
  </div>

  <div class="text-center">
    <h2 class="bold" style="font-size:16px;">KANTIN SMKN 1 CIOMAS</h2>
    <p style="font-size:11px;">Jl. Raya Laladon No. 1, Ciomas, Bogor</p>
    <p style="font-size:11px;">Layanan Pesan Antar & Ambil di Stand</p>
  </div>

  <div class="double-line"></div>

  <div class="row">
    <span>Kode:</span>
    <span class="bold">{{ $order->order_number }}</span>
  </div>
  <div class="row">
    <span>Tanggal:</span>
    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
  </div>
  <div class="row">
    <span>Pemesan:</span>
    <span class="bold">{{ $order->customer_name }}</span>
  </div>
  <div class="row">
    <span>Tipe / Kontak:</span>
    <span>{{ ucfirst($order->customer_type) }} / {{ $order->customer_phone }}</span>
  </div>
  <div class="row">
    <span>Jadwal Ambil:</span>
    <span class="bold">{{ $order->pickup_time_slot }}</span>
  </div>
  <div class="row">
    <span>Metode:</span>
    <span class="bold">{{ $order->pickup_type === 'deliver_to_room' ? 'Antar: '.($order->delivery_location ?? 'Ruang Guru') : 'Ambil di Stand' }}</span>
  </div>

  <div class="dashed-line"></div>
  <div class="bold" style="margin-bottom:6px;">RINCIAN MENU:</div>

  @foreach($order->items as $item)
    <div class="item-row">
      <div class="row">
        <span>{{ $item->quantity }}x {{ $item->menu_name }}</span>
        <span class="bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
      </div>
      <div style="font-size:11px; color:#444; padding-left:14px;">
        @ {{ number_format($item->unit_price, 0, ',', '.') }} &bull; {{ $item->stand->name ?? 'Stand Kantin' }}
      </div>
      @if($item->notes)
        <div style="font-size:11px; font-style:italic; padding-left:14px;">
          Catatan: {{ $item->notes }}
        </div>
      @endif
    </div>
  @endforeach

  @if($order->notes)
    <div style="font-size:11px; margin-top:4px;">
      <strong>Catatan Order:</strong> {{ $order->notes }}
    </div>
  @endif

  <div class="dashed-line"></div>

  <div class="row">
    <span>Subtotal Menu:</span>
    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
  </div>
  <div class="row">
    <span>Biaya Layanan:</span>
    <span>Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span>
  </div>

  <div class="double-line"></div>

  <div class="row bold" style="font-size:15px;">
    <span>TOTAL BAYAR:</span>
    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
  </div>

  @php
    $isPaid = $order->isPaid();
    $payMethod = $order->payments->first()->payment_method ?? 'qris';
  @endphp

  <div class="row" style="margin-top:6px;">
    <span>Pembayaran:</span>
    <span class="bold">{{ strtoupper($payMethod) }} ({{ $isPaid ? 'LUNAS' : 'BELUM LUNAS' }})</span>
  </div>

  <div class="dashed-line" style="margin-top:14px;"></div>

  <div class="text-center" style="font-size:11px; margin-top:10px;">
    <p>Simpan struk ini sebagai bukti pemesanan.</p>
    <p>Terima kasih telah jajan di Kantin Sekolah!</p>
    <p>*** SMKN 1 CIOMAS ***</p>
  </div>

</body>
</html>

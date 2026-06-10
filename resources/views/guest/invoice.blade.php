@extends('layouts.app')
@section('title', 'Invoice #' . $booking->booking_code . ' - Hotel Nusantara')
@section('extra-css')
<style>
.invoice{max-width:750px;margin:0 auto;background:var(--dark2);border:1px solid rgba(201,169,110,0.2);padding:3rem}
.invoice-header{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid var(--gold);padding-bottom:1.5rem;margin-bottom:2rem}
.invoice-logo{font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:var(--gold);font-weight:600}
.invoice-logo span{display:block;font-size:0.65rem;color:var(--text-muted);font-family:'DM Sans',sans-serif;letter-spacing:3px;text-transform:uppercase}
.invoice-meta{text-align:right;font-size:0.8rem;color:var(--text-muted)}
.invoice-meta strong{color:var(--gold);font-size:1rem;display:block;margin-bottom:0.3rem}
.inv-section{margin-bottom:2rem}
.inv-section h4{font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin-bottom:0.8rem;font-family:'DM Sans',sans-serif;font-weight:500}
.inv-grid{display:grid;grid-template-columns:1fr 1fr;gap:0.5rem}
.inv-item{font-size:0.82rem;color:var(--text-muted)}
.inv-item strong{color:var(--text)}
.inv-table{width:100%;border-collapse:collapse;margin-top:0.5rem}
.inv-table th{background:var(--dark3);padding:0.6rem 1rem;font-size:0.7rem;letter-spacing:1px;text-transform:uppercase;color:var(--gold);text-align:left}
.inv-table td{padding:0.6rem 1rem;border-bottom:1px solid rgba(201,169,110,0.08);font-size:0.82rem;color:var(--text-muted)}
.inv-table .text-right{text-align:right}
.inv-total-row{display:flex;justify-content:space-between;padding:0.5rem 0;font-size:0.85rem}
.inv-total-row.grand{border-top:2px solid var(--gold);padding-top:1rem;margin-top:0.5rem}
.inv-total-row.grand .val{color:var(--gold);font-family:'Cormorant Garamond',serif;font-size:1.5rem}
.inv-footer{text-align:center;margin-top:2rem;padding-top:1.5rem;border-top:1px solid rgba(201,169,110,0.15)}
@media print{body{background:#fff!important;color:#000!important} nav,footer,.no-print{display:none!important} .invoice{border:none;box-shadow:none;background:#fff!important} .invoice *{color:#333!important} .inv-table th{background:#f5f5f5!important}}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner">
    <div class="invoice">
      <div class="invoice-header">
        <div class="invoice-logo">Nusantara<span>Luxury Collection</span></div>
        <div class="invoice-meta">
          <strong>INVOICE</strong>
          {{ $booking->booking_code }}<br>
          {{ $booking->updated_at->translatedFormat('d M Y, H:i') }}
        </div>
      </div>

      <div class="inv-section">
        <h4>Informasi Tamu</h4>
        <div class="inv-grid">
          <div class="inv-item"><strong>{{ $booking->user->name }}</strong></div>
          <div class="inv-item">{{ $booking->user->email }}</div>
          <div class="inv-item">{{ $booking->user->phone ?? '-' }}</div>
          <div class="inv-item">Status: <span class="badge {{ $booking->getStatusBadgeClass() }}">{{ $booking->getStatusLabel() }}</span></div>
        </div>
      </div>

      <div class="inv-section">
        <h4>Detail Kamar</h4>
        <table class="inv-table">
          <thead><tr><th>Deskripsi</th><th>Durasi</th><th>Harga/Malam</th><th class="text-right">Subtotal</th></tr></thead>
          <tbody>
            <tr>
              <td>{{ $booking->room ? $booking->room->getTypeLabel() . ' (' . $booking->room->room_number . ')' : '-' }}</td>
              <td>{{ $booking->nights }} malam</td>
              <td>Rp {{ $booking->room ? number_format($booking->room->price_per_night, 0, ',', '.') : '-' }}</td>
              <td class="text-right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      @if($booking->services->count() > 0)
      <div class="inv-section">
        <h4>Layanan Tambahan</h4>
        <table class="inv-table">
          <thead><tr><th>Layanan</th><th>Qty</th><th>Harga</th><th class="text-right">Total</th></tr></thead>
          <tbody>
            @foreach($booking->services->where('status','!=','cancelled') as $svc)
            <tr>
              <td>{{ $svc->getServiceTypeLabel() }} - {{ $svc->description }}</td>
              <td>{{ $svc->quantity }}</td>
              <td>Rp {{ number_format($svc->amount, 0, ',', '.') }}</td>
              <td class="text-right">Rp {{ number_format($svc->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif

      <div class="inv-section">
        <h4>Ringkasan Pembayaran</h4>
        <div class="inv-total-row"><span style="color:var(--text-muted)">Biaya Kamar</span><span style="color:var(--text)">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span></div>
        @php $svcTotal = $booking->getServicesTotal(); @endphp
        @if($svcTotal > 0)
        <div class="inv-total-row"><span style="color:var(--text-muted)">Layanan Tambahan</span><span style="color:var(--text)">Rp {{ number_format($svcTotal, 0, ',', '.') }}</span></div>
        @endif
        <div class="inv-total-row"><span style="color:var(--text-muted)">Sudah Dibayar</span><span style="color:var(--success)">- Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span></div>
        @if($booking->refund_amount > 0)
        <div class="inv-total-row"><span style="color:var(--text-muted)">Refund</span><span style="color:var(--warning)">Rp {{ number_format($booking->refund_amount, 0, ',', '.') }}</span></div>
        @endif
        <div class="inv-total-row grand">
          <span style="color:var(--text-muted)">TOTAL</span>
          <span class="val">Rp {{ number_format($booking->total_price + $svcTotal, 0, ',', '.') }}</span>
        </div>
      </div>

      <div class="inv-footer">
        <p style="color:var(--text-muted);font-size:0.8rem">Terima kasih telah menginap di Hotel Nusantara.</p>
        <p style="color:var(--text-muted);font-size:0.72rem;margin-top:0.5rem">Jl. Asia Afrika No. 88, Bandung · +62 22 123 4567</p>
      </div>
    </div>

    <div class="no-print" style="text-align:center;margin-top:2rem;display:flex;gap:1rem;justify-content:center">
      <button onclick="window.print()" class="btn-primary btn-sm">🖨️ Cetak Invoice</button>
      <a href="{{ route('booking.my-bookings') }}" class="btn-outline btn-sm">Kembali</a>
    </div>
  </div>
</section>
@endsection

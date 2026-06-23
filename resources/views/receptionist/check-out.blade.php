@extends('layouts.app')
@section('title', 'Check-Out - Hotel Nusantara')
@section('extra-css')
<style>
.bill-row{display:flex;justify-content:space-between;padding:0.5rem 0;font-size:0.85rem}
.bill-label{color:var(--text-muted)}
.bill-value{color:var(--text)}
.bill-divider{border-top:1px solid rgba(201,169,110,0.15);margin-top:0.5rem;padding-top:0.5rem}
.bill-total{border-top:2px solid var(--gold);padding-top:0.8rem;margin-top:0.3rem}
.bill-total .bill-value{color:var(--gold);font-family:'Cormorant Garamond',serif;font-size:1.5rem}
.svc-form{margin-top:1rem;padding:1rem;background:var(--dark3);border:1px solid rgba(201,169,110,0.1)}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner" style="max-width:750px">
    <div class="card">
      <div class="card-header" style="text-align:center">
        <div class="section-tag">Check-Out</div>
        <h2 style="font-size:1.8rem;color:var(--cream)">Proses Check-Out</h2>
      </div>

      {{-- Validation Errors --}}
      @if($errors->any())
        <div class="alert alert-error">
          <ul style="list-style:none">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Detail Tamu --}}
      <div style="margin-bottom:1.5rem">
        <div class="bill-row"><span class="bill-label">Tamu</span><span style="color:var(--cream)">{{ $booking->user->name }}</span></div>
        <div class="bill-row"><span class="bill-label">Kamar</span><span style="color:var(--cream)">{{ $booking->room->room_number ?? '-' }} - {{ $booking->room ? $booking->room->getTypeLabel() : '-' }}</span></div>
        <div class="bill-row"><span class="bill-label">Lama Menginap</span><span style="color:var(--cream)">{{ $nights }} malam</span></div>
        <div class="bill-row"><span class="bill-label">Check-In Aktual</span><span style="color:var(--cream)">{{ $booking->actual_check_in ? $booking->actual_check_in->format('d/m/Y H:i') : '-' }}</span></div>
      </div>

      {{-- Rincian Biaya --}}
      <h4 style="color:var(--gold);font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem">Rincian Biaya</h4>
      
      <div class="bill-row">
        <span class="bill-label">Biaya Kamar ({{ $nights }} malam)</span>
        <span class="bill-value">Rp {{ number_format($roomCharge,0,',','.') }}</span>
      </div>
      
      @foreach($booking->services->where('status','!=','cancelled') as $svc)
      <div class="bill-row">
        <span class="bill-label">{{ $svc->getServiceTypeLabel() }}: {{ $svc->description }} ×{{ $svc->quantity }}</span>
        <span class="bill-value">Rp {{ number_format($svc->total,0,',','.') }}</span>
      </div>
      @endforeach

      <div class="bill-row bill-divider">
        <strong class="bill-label">Subtotal</strong>
        <strong style="color:var(--cream)">Rp {{ number_format($totalBill,0,',','.') }}</strong>
      </div>
      <div class="bill-row">
        <span class="bill-label">Sudah Dibayar (via Midtrans)</span>
        <span style="color:var(--success)">- Rp {{ number_format($booking->paid_amount,0,',','.') }}</span>
      </div>
      <div class="bill-row bill-total">
        <strong style="color:var(--gold)">{{ $remaining > 0 ? 'SISA TAGIHAN' : ($remaining < 0 ? 'KELEBIHAN BAYAR' : 'LUNAS') }}</strong>
        @if($remaining > 0)
          <span style="color:var(--gold);font-family:'Cormorant Garamond',serif;font-size:1.5rem">Rp {{ number_format($remaining,0,',','.') }}</span>
        @elseif($remaining < 0)
          <span style="color:var(--success);font-family:'Cormorant Garamond',serif;font-size:1.5rem">Rp {{ number_format(abs($remaining),0,',','.') }}</span>
        @else
          <span style="color:var(--success);font-family:'Cormorant Garamond',serif;font-size:1.5rem">Rp 0</span>
        @endif
      </div>

      {{-- Tambah Layanan Tambahan --}}
      <details style="margin:1.5rem 0">
        <summary style="cursor:pointer;color:var(--gold);font-size:0.85rem">+ Tambah Layanan Tambahan</summary>
        <form method="POST" action="{{ route('receptionist.add-service', $booking->id) }}" class="svc-form">
          @csrf
          <div class="form-row">
            <div class="form-group">
              <label>Tipe Layanan</label>
              <select name="service_type" required>
                <option value="room_service">Room Service</option>
                <option value="laundry">Laundry</option>
                <option value="spa">Spa & Wellness</option>
                <option value="transport">Transportasi</option>
                <option value="other">Lainnya</option>
              </select>
            </div>
            <div class="form-group">
              <label>Deskripsi</label>
              <input type="text" name="description" required placeholder="Contoh: Makan malam 2 porsi">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Harga Satuan (Rp)</label>
              <input type="number" name="amount" required min="0" placeholder="50000">
            </div>
            <div class="form-group">
              <label>Jumlah (Qty)</label>
              <input type="number" name="quantity" value="1" required min="1">
            </div>
          </div>
          <button type="submit" class="btn-outline btn-sm">Tambah Layanan</button>
        </form>
      </details>

      {{-- Form Proses Check-Out --}}
      <form method="POST" action="{{ route('receptionist.process-check-out', $booking->id) }}">
        @csrf
        @if($remaining > 0)
        <div class="form-group">
          <label>Metode Pelunasan Sisa Tagihan</label>
          <select name="payment_method" required>
            <option value="cash">Cash</option>
            <option value="transfer">Transfer Bank</option>
            <option value="credit_card">Kartu Kredit</option>
            <option value="e_wallet">E-Wallet</option>
          </select>
        </div>
        @else
        <input type="hidden" name="payment_method" value="cash">
        @if($remaining == 0)
          <div class="alert alert-success" style="margin-bottom:1rem">✅ Pembayaran sudah LUNAS. Tamu dapat langsung check-out.</div>
        @elseif($remaining < 0)
          <div class="alert alert-info" style="margin-bottom:1rem">ℹ️ Tamu memiliki kelebihan bayar sebesar <strong>Rp {{ number_format(abs($remaining),0,',','.') }}</strong>. Harap proses pengembalian secara manual.</div>
        @endif
        @endif
        <button type="submit" class="btn-primary" style="width:100%">✓ Proses Check-Out & Cetak Invoice</button>
      </form>

      <div style="text-align:center;margin-top:1.5rem">
        <a href="{{ route('receptionist.dashboard') }}" class="btn-outline btn-sm">← Kembali ke Dashboard</a>
      </div>
    </div>
  </div>
</section>
@endsection

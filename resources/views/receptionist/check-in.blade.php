@extends('layouts.app')
@section('title', 'Check-In - Hotel Nusantara')
@section('extra-css')
<style>
.checkin-detail{display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)}
.checkin-label{color:var(--text-muted);font-size:0.82rem}
.checkin-value{color:var(--cream);font-weight:500}
.checkin-value.gold{color:var(--gold)}
.id-verify-box{background:rgba(201,169,110,0.05);border:1px dashed rgba(201,169,110,0.3);padding:1.2rem 1.5rem;margin-bottom:1.5rem}
.id-verify-box h4{color:var(--gold);font-size:0.75rem;letter-spacing:2px;text-transform:uppercase;margin-bottom:0.5rem;font-family:'DM Sans',sans-serif}
.id-verify-box p{color:var(--text-muted);font-size:0.82rem;line-height:1.6}
.id-verify-box ul{list-style:none;margin-top:0.5rem}
.id-verify-box ul li{color:var(--text-muted);font-size:0.8rem;padding:0.2rem 0}
.id-verify-box ul li::before{content:'✓ ';color:var(--gold)}
.payment-status{display:flex;align-items:center;gap:0.5rem}
.payment-bar{flex:1;height:6px;background:var(--dark3);overflow:hidden}
.payment-bar-fill{height:100%;background:var(--gold);transition:width 0.5s}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner" style="max-width:700px">
    <div class="card">
      <div class="card-header" style="text-align:center">
        <div class="section-tag">Check-In</div>
        <h2 style="font-size:1.8rem;color:var(--cream)">Proses Check-In</h2>
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

      {{-- Status Warning --}}
      @if($booking->status === 'pending')
        <div class="alert alert-warning">⚠️ Booking ini masih <strong>PENDING</strong>! Tamu harus menyelesaikan pembayaran terlebih dahulu sebelum check-in.</div>
      @endif

      {{-- Detail Booking --}}
      <div style="margin-bottom:2rem">
        <div class="checkin-detail">
          <span class="checkin-label">Kode Booking</span>
          <span class="checkin-value gold">{{ $booking->booking_code }}</span>
        </div>
        <div class="checkin-detail">
          <span class="checkin-label">Nama Tamu</span>
          <span class="checkin-value">{{ $booking->user->name }}</span>
        </div>
        <div class="checkin-detail">
          <span class="checkin-label">No. Telepon</span>
          <span class="checkin-value">{{ $booking->user->phone ?? '-' }}</span>
        </div>
        <div class="checkin-detail">
          <span class="checkin-label">Email</span>
          <span class="checkin-value">{{ $booking->user->email }}</span>
        </div>
        <div class="checkin-detail">
          <span class="checkin-label">Status Booking</span>
          <span class="checkin-value"><span class="badge {{ $booking->getStatusBadgeClass() }}">{{ $booking->getStatusLabel() }}</span></span>
        </div>
        <div class="checkin-detail">
          <span class="checkin-label">Periode Menginap</span>
          <span class="checkin-value">{{ $booking->check_in_date->format('d/m/Y') }} - {{ $booking->check_out_date->format('d/m/Y') }} ({{ $booking->nights }} malam)</span>
        </div>
        <div class="checkin-detail">
          <span class="checkin-label">Jumlah Tamu</span>
          <span class="checkin-value">{{ $booking->guests }} orang</span>
        </div>
        <div class="checkin-detail">
          <span class="checkin-label">Tipe Kamar Dipesan</span>
          <span class="checkin-value">{{ $booking->room ? $booking->room->getTypeLabel() : 'Belum di-assign' }}</span>
        </div>
        <div style="padding:0.6rem 0">
          <span class="checkin-label">Status Pembayaran</span>
          <div class="payment-status" style="margin-top:0.3rem">
            <span style="color:var(--cream);font-size:0.85rem">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
            @php $pct = $booking->total_price > 0 ? min(100, ($booking->paid_amount / $booking->total_price) * 100) : 0; @endphp
            <div class="payment-bar"><div class="payment-bar-fill" style="width:{{ $pct }}%"></div></div>
            <span style="color:var(--text-muted);font-size:0.85rem">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
          </div>
          @if($booking->payment_type === 'dp')
            <small style="color:var(--warning);font-size:0.75rem;margin-top:0.3rem;display:block">💰 Tamu memilih DP 50% — sisa akan dilunasi saat check-out</small>
          @endif
        </div>
      </div>

      {{-- Verifikasi Identitas Manual --}}
      <div class="id-verify-box">
        <h4>📋 Verifikasi Identitas Tamu</h4>
        @if($booking->identity_photo)
          <div style="margin:1rem 0;text-align:center">
            <img src="{{ asset('storage/' . $booking->identity_photo) }}" alt="Identity Photo" style="max-width:100%;max-height:200px;border-radius:4px;border:1px solid rgba(201,169,110,0.3)">
          </div>
        @endif
        <p>Cocokkan data di atas dengan KTP/Paspor fisik yang diberikan oleh tamu. Pastikan:</p>
        <ul>
          <li>Nama sesuai dengan identitas</li>
          <li>Foto tamu sesuai</li>
          <li>Kartu identitas masih berlaku</li>
        </ul>
      </div>

      {{-- Form Assign Kamar & Proses Check-In --}}
      @if($booking->status === 'confirmed')
      <form method="POST" action="{{ route('receptionist.process-check-in', $booking->id) }}">
        @csrf
        <div class="form-group">
          <label>Assign Kamar</label>
          <select name="room_id" required>
            <option value="">-- Pilih Kamar yang Tersedia --</option>
            @foreach($availableRooms as $r)
              <option value="{{ $r->id }}" {{ old('room_id', $booking->room_id) == $r->id ? 'selected' : '' }}>
                {{ $r->room_number }} - {{ $r->getTypeLabel() }} (Rp {{ number_format($r->price_per_night,0,',','.') }}/malam) — Kapasitas: {{ $r->capacity }} org
              </option>
            @endforeach
          </select>
          @error('room_id')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>
        @if($booking->special_request)
        <div style="background:rgba(52,152,219,0.08);border:1px solid rgba(52,152,219,0.2);padding:0.8rem 1rem;margin-bottom:1.5rem">
          <small style="color:var(--info);font-size:0.75rem;letter-spacing:1px;text-transform:uppercase;display:block;margin-bottom:0.3rem">📝 Permintaan Khusus</small>
          <p style="color:var(--text-muted);font-size:0.85rem">{{ $booking->special_request }}</p>
        </div>
        @endif
        <button type="submit" class="btn-primary" style="width:100%">✓ Proses Check-In</button>
      </form>
      @endif

      <div style="text-align:center;margin-top:1.5rem">
        <a href="{{ route('receptionist.dashboard') }}" class="btn-outline btn-sm">← Kembali ke Dashboard</a>
      </div>
    </div>
  </div>
</section>
@endsection

@extends('layouts.app')
@section('title', 'Status Booking - Hotel Nusantara')
@section('extra-css')
<style>
.steps{display:flex;gap:1px;margin-bottom:2rem}
.step{flex:1;padding:0.7rem;background:var(--dark3);font-size:0.65rem;text-align:center;letter-spacing:1px;text-transform:uppercase;color:var(--text-muted);transition:all 0.3s}
.step.active{background:var(--gold);color:var(--dark);font-weight:500}
.step.done{background:rgba(201,169,110,0.2);color:var(--gold)}
.status-card{max-width:700px;margin:0 auto}
.detail-row{display:flex;justify-content:space-between;padding:0.7rem 0;border-bottom:1px solid rgba(201,169,110,0.08)}
.detail-row .label{color:var(--text-muted);font-size:0.82rem}
.detail-row .value{color:var(--cream);font-size:0.85rem;font-weight:500}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner">
    <div class="status-card">
      <div class="card">
        <div style="text-align:center;margin-bottom:2rem">
          <div class="section-tag">Booking {{ $booking->booking_code }}</div>
          <h2 style="font-size:2rem;color:var(--cream);margin-top:0.5rem">Status Booking</h2>
          <div style="margin-top:1rem">
            <span class="badge {{ $booking->getStatusBadgeClass() }}" style="font-size:0.85rem;padding:0.4rem 1rem">{{ $booking->getStatusLabel() }}</span>
          </div>
        </div>

        <div class="steps">
          <div class="step {{ in_array($booking->status, ['pending','confirmed','checked_in','checked_out']) ? ($booking->status == 'pending' ? 'active' : 'done') : '' }}">Booking</div>
          <div class="step {{ in_array($booking->status, ['confirmed','checked_in','checked_out']) ? ($booking->status == 'confirmed' ? 'active' : 'done') : '' }}">Confirmed</div>
          <div class="step {{ in_array($booking->status, ['checked_in','checked_out']) ? ($booking->status == 'checked_in' ? 'active' : 'done') : '' }}">Check-In</div>
          <div class="step {{ $booking->status == 'checked_out' ? 'active' : '' }}">Check-Out</div>
        </div>

        <div class="detail-row"><span class="label">Kode Booking</span><span class="value" style="color:var(--gold)">{{ $booking->booking_code }}</span></div>
        <div class="detail-row"><span class="label">Tamu</span><span class="value">{{ $booking->user->name }}</span></div>
        @if($booking->room)
        <div class="detail-row"><span class="label">Kamar</span><span class="value">{{ $booking->room->getTypeLabel() }} - {{ $booking->room->room_number }}</span></div>
        @endif
        <div class="detail-row"><span class="label">Check-In</span><span class="value">{{ $booking->check_in_date->translatedFormat('d M Y') }}</span></div>
        <div class="detail-row"><span class="label">Check-Out</span><span class="value">{{ $booking->check_out_date->translatedFormat('d M Y') }}</span></div>
        <div class="detail-row"><span class="label">Durasi</span><span class="value">{{ $booking->nights }} Malam</span></div>
        <div class="detail-row"><span class="label">Tipe Pembayaran</span><span class="value">{{ $booking->payment_type === 'dp' ? 'Down Payment (50%)' : 'Lunas' }}</span></div>
        <div class="detail-row"><span class="label">Total Harga</span><span class="value" style="color:var(--gold)">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span></div>
        <div class="detail-row"><span class="label">Sudah Dibayar</span><span class="value">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span></div>
        @if($booking->getRemainingBalance() > 0)
        <div class="detail-row"><span class="label">Sisa Pembayaran</span><span class="value" style="color:var(--danger)">Rp {{ number_format($booking->getRemainingBalance(), 0, ',', '.') }}</span></div>
        @endif

        @if($booking->services->count() > 0)
        <h4 style="margin-top:1.5rem;color:var(--gold);font-size:1.1rem">Layanan Tambahan</h4>
        @foreach($booking->services as $svc)
        <div class="detail-row">
          <span class="label">{{ $svc->getServiceTypeLabel() }} - {{ $svc->description }}</span>
          <span class="value">Rp {{ number_format($svc->total, 0, ',', '.') }}</span>
        </div>
        @endforeach
        @endif

        <div style="display:flex;gap:1rem;margin-top:2rem;flex-wrap:wrap;justify-content:center">
          <a href="{{ route('booking.invoice', $booking->id) }}" class="btn-primary btn-sm">Lihat Invoice</a>
          @if(in_array($booking->status, ['pending', 'confirmed']))
          <form method="POST" action="{{ route('booking.cancel', $booking->id) }}" onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
            @csrf
            <button type="submit" class="btn-outline btn-sm" style="border-color:var(--danger);color:var(--danger)">Batalkan Booking</button>
          </form>
          @endif
          <a href="{{ route('booking.my-bookings') }}" class="btn-outline btn-sm">Booking Saya</a>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

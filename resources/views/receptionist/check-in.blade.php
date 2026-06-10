@extends('layouts.app')
@section('title', 'Check-In - Hotel Nusantara')
@section('content')
<section>
  <div class="section-inner" style="max-width:700px">
    <div class="card">
      <div class="card-header" style="text-align:center">
        <div class="section-tag">Check-In</div>
        <h2 style="font-size:1.8rem;color:var(--cream)">Proses Check-In</h2>
      </div>

      @if($booking->status === 'pending')
        <div class="alert alert-warning">⚠️ Booking ini masih PENDING! Tamu harus menyelesaikan pembayaran terlebih dahulu sebelum check-in.</div>
      @endif

      <div style="margin-bottom:2rem">
        <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)">
          <span style="color:var(--text-muted);font-size:0.82rem">Kode Booking</span>
          <span style="color:var(--gold);font-weight:500">{{ $booking->booking_code }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)">
          <span style="color:var(--text-muted);font-size:0.82rem">Tamu</span>
          <span style="color:var(--cream)">{{ $booking->user->name }} ({{ $booking->user->phone }})</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)">
          <span style="color:var(--text-muted);font-size:0.82rem">Status</span>
          <span class="badge {{ $booking->getStatusBadgeClass() }}">{{ $booking->getStatusLabel() }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)">
          <span style="color:var(--text-muted);font-size:0.82rem">Periode</span>
          <span style="color:var(--cream)">{{ $booking->check_in_date->format('d/m/Y') }} - {{ $booking->check_out_date->format('d/m/Y') }} ({{ $booking->nights }} malam)</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)">
          <span style="color:var(--text-muted);font-size:0.82rem">Pembayaran</span>
          <span style="color:var(--cream)">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }} / Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
        </div>
      </div>

      @if($booking->status === 'confirmed')
      <form method="POST" action="{{ route('receptionist.process-check-in', $booking->id) }}">
        @csrf
        <div class="form-group">
          <label>Assign Kamar</label>
          <select name="room_id" required>
            <option value="">-- Pilih Kamar --</option>
            @php
              $availRooms = \App\Models\Room::where('status','available')->where('type', $booking->room->type ?? '')->get();
              if($availRooms->isEmpty()) $availRooms = \App\Models\Room::where('status','available')->get();
            @endphp
            @foreach($availRooms as $r)
              <option value="{{ $r->id }}" {{ $booking->room_id == $r->id ? 'selected' : '' }}>{{ $r->room_number }} - {{ $r->getTypeLabel() }} (Rp {{ number_format($r->price_per_night,0,',','.') }}/malam)</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn-primary" style="width:100%">✓ Proses Check-In</button>
      </form>
      @endif

      <div style="text-align:center;margin-top:1.5rem">
        <a href="{{ route('receptionist.dashboard') }}" class="btn-outline btn-sm">← Dashboard</a>
      </div>
    </div>
  </div>
</section>
@endsection

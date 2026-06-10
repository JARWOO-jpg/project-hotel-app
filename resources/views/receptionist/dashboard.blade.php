@extends('layouts.app')
@section('title', 'Dashboard Resepsionis - Hotel Nusantara')
@section('extra-css')
<style>
.dash-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;margin-bottom:3rem}
.dash-stat{background:var(--dark2);border:1px solid rgba(201,169,110,0.1);padding:1.5rem;text-align:center;transition:border-color 0.3s}
.dash-stat:hover{border-color:rgba(201,169,110,0.4)}
.dash-stat .num{font-family:'Cormorant Garamond',serif;font-size:2.5rem;color:var(--gold);font-weight:300}
.dash-stat .lbl{font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--text-muted);margin-top:0.3rem}
.dash-section{margin-bottom:3rem}
.dash-section h3{font-size:1.3rem;color:var(--cream);margin-bottom:1.5rem;padding-bottom:0.8rem;border-bottom:1px solid rgba(201,169,110,0.15)}
.dash-section h3 em{color:var(--gold);font-style:italic}
.search-bar{display:flex;gap:1rem;margin-bottom:2rem}
.search-bar input{flex:1;background:var(--dark3);border:1px solid rgba(201,169,110,0.2);padding:0.75rem 1rem;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;font-size:0.9rem}
.search-bar input:focus{border-color:var(--gold)}
.guest-card{background:var(--dark2);border:1px solid rgba(201,169,110,0.1);padding:1.2rem 1.5rem;display:flex;justify-content:space-between;align-items:center;margin-bottom:0.8rem;transition:border-color 0.3s}
.guest-card:hover{border-color:rgba(201,169,110,0.3)}
.guest-info h4{color:var(--cream);font-size:1rem;font-family:'DM Sans',sans-serif;font-weight:500}
.guest-info p{color:var(--text-muted);font-size:0.8rem;margin-top:0.2rem}
.guest-actions{display:flex;gap:0.5rem;align-items:center}
@media(max-width:768px){.dash-stats{grid-template-columns:1fr 1fr}}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner">
    <div class="section-header" style="margin-bottom:2rem">
      <div class="section-tag">Resepsionis</div>
      <h2 class="section-title" style="font-size:2rem">Dashboard <em>Resepsionis</em></h2>
    </div>

    <div class="dash-stats">
      <div class="dash-stat"><div class="num">{{ $availableRooms }}</div><div class="lbl">Kamar Tersedia</div></div>
      <div class="dash-stat"><div class="num">{{ $occupiedRooms }}</div><div class="lbl">Kamar Terisi</div></div>
      <div class="dash-stat"><div class="num">{{ $todayCheckIns->count() }}</div><div class="lbl">Check-In Hari Ini</div></div>
      <div class="dash-stat"><div class="num">{{ $todayCheckOuts->count() }}</div><div class="lbl">Check-Out Hari Ini</div></div>
    </div>

    <!-- Search Booking -->
    <div class="dash-section">
      <h3>🔍 Cari <em>Booking</em></h3>
      <form action="{{ route('receptionist.search') }}" method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Cari berdasarkan Nama Tamu atau Kode Booking..." value="{{ request('search') }}">
        <button type="submit" class="btn-primary btn-sm">Cari</button>
      </form>
    </div>

    <!-- Today's Check-Ins -->
    <div class="dash-section">
      <h3>📥 Check-In <em>Hari Ini</em></h3>
      @forelse($todayCheckIns as $b)
      <div class="guest-card">
        <div class="guest-info">
          <h4>{{ $b->user->name }}</h4>
          <p>{{ $b->booking_code }} · {{ $b->room ? $b->room->getTypeLabel() : 'Belum assign' }} · {{ $b->nights }} malam · <span class="badge {{ $b->getStatusBadgeClass() }}">{{ $b->getStatusLabel() }}</span></p>
        </div>
        <div class="guest-actions">
          @if($b->status === 'confirmed')
            <a href="{{ route('receptionist.check-in', $b->id) }}" class="btn-primary btn-sm">Check-In</a>
          @else
            <span class="badge badge-warning">Belum Bayar</span>
          @endif
        </div>
      </div>
      @empty
      <p style="color:var(--text-muted)">Tidak ada check-in hari ini.</p>
      @endforelse
    </div>

    <!-- Today's Check-Outs -->
    <div class="dash-section">
      <h3>📤 Check-Out <em>Hari Ini</em></h3>
      @forelse($todayCheckOuts as $b)
      <div class="guest-card">
        <div class="guest-info">
          <h4>{{ $b->user->name }}</h4>
          <p>{{ $b->booking_code }} · Kamar {{ $b->room->room_number ?? '-' }} · Sisa: Rp {{ number_format($b->getRemainingBalance(), 0, ',', '.') }}</p>
        </div>
        <div class="guest-actions">
          <a href="{{ route('receptionist.check-out', $b->id) }}" class="btn-primary btn-sm">Check-Out</a>
          <a href="{{ route('receptionist.guest-bill', $b->id) }}" class="btn-outline btn-sm">Guest Bill</a>
        </div>
      </div>
      @empty
      <p style="color:var(--text-muted)">Tidak ada check-out hari ini.</p>
      @endforelse
    </div>

    <!-- Current Guests -->
    <div class="dash-section">
      <h3>🏨 Tamu <em>Menginap</em></h3>
      @forelse($currentGuests as $b)
      <div class="guest-card">
        <div class="guest-info">
          <h4>{{ $b->user->name }}</h4>
          <p>Kamar {{ $b->room->room_number ?? '-' }} · {{ $b->room ? $b->room->getTypeLabel() : '' }} · Check-out: {{ $b->check_out_date->format('d/m/Y') }}</p>
        </div>
        <div class="guest-actions">
          <a href="{{ route('receptionist.guest-bill', $b->id) }}" class="btn-outline btn-sm">Guest Bill</a>
          <a href="{{ route('receptionist.check-out', $b->id) }}" class="btn-outline btn-sm">Check-Out</a>
        </div>
      </div>
      @empty
      <p style="color:var(--text-muted)">Tidak ada tamu yang menginap saat ini.</p>
      @endforelse
    </div>
  </div>
</section>
@endsection

@extends('layouts.app')
@section('title', 'Dashboard Resepsionis - Hotel Nusantara')
@section('extra-css')
<style>
.dash-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:1.5rem;margin-bottom:3rem}
.dash-stat{background:var(--dark2);border:1px solid rgba(201,169,110,0.1);padding:1.5rem;text-align:center;transition:border-color 0.3s,transform 0.3s}
.dash-stat:hover{border-color:rgba(201,169,110,0.4);transform:translateY(-2px)}
.dash-stat .num{font-family:'Cormorant Garamond',serif;font-size:2.5rem;color:var(--gold);font-weight:300}
.dash-stat .lbl{font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--text-muted);margin-top:0.3rem}
.dash-stat .icon{font-size:1.5rem;margin-bottom:0.3rem}
.dash-section{margin-bottom:3rem}
.dash-section h3{font-size:1.3rem;color:var(--cream);margin-bottom:1.5rem;padding-bottom:0.8rem;border-bottom:1px solid rgba(201,169,110,0.15)}
.dash-section h3 em{color:var(--gold);font-style:italic}
.dash-section h3 .count{background:var(--gold);color:var(--dark);font-size:0.7rem;padding:0.15rem 0.5rem;margin-left:0.5rem;vertical-align:middle;font-family:'DM Sans',sans-serif;font-style:normal}
.search-bar{display:flex;gap:1rem;margin-bottom:2rem}
.search-bar input{flex:1;background:var(--dark3);border:1px solid rgba(201,169,110,0.2);padding:0.75rem 1rem;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;font-size:0.9rem}
.search-bar input:focus{border-color:var(--gold)}
.guest-card{background:var(--dark2);border:1px solid rgba(201,169,110,0.1);padding:1.2rem 1.5rem;display:flex;justify-content:space-between;align-items:center;margin-bottom:0.8rem;transition:border-color 0.3s,background 0.3s}
.guest-card:hover{border-color:rgba(201,169,110,0.3);background:rgba(201,169,110,0.02)}
.guest-card.overdue{border-left:3px solid var(--danger)}
.guest-info h4{color:var(--cream);font-size:1rem;font-family:'DM Sans',sans-serif;font-weight:500}
.guest-info p{color:var(--text-muted);font-size:0.8rem;margin-top:0.2rem}
.guest-info .guest-meta{display:flex;flex-wrap:wrap;gap:0.5rem;align-items:center;margin-top:0.4rem}
.guest-info .meta-item{display:inline-flex;align-items:center;gap:0.25rem;font-size:0.75rem;color:var(--text-muted);background:rgba(201,169,110,0.05);padding:0.2rem 0.5rem;border:1px solid rgba(201,169,110,0.08)}
.guest-info .meta-item.paid{border-color:rgba(46,204,113,0.2);color:var(--success)}
.guest-info .meta-item.unpaid{border-color:rgba(231,76,60,0.2);color:var(--danger)}
.guest-info .meta-item.partial{border-color:rgba(243,156,18,0.2);color:var(--warning)}
.guest-actions{display:flex;gap:0.5rem;align-items:center;flex-shrink:0}
.days-left{font-size:0.7rem;padding:0.2rem 0.6rem;letter-spacing:1px;text-transform:uppercase}
.days-left.urgent{background:rgba(231,76,60,0.1);color:var(--danger);border:1px solid rgba(231,76,60,0.2)}
.days-left.normal{background:rgba(46,204,113,0.1);color:var(--success);border:1px solid rgba(46,204,113,0.2)}
.empty-state{text-align:center;padding:2rem;background:var(--dark2);border:1px dashed rgba(201,169,110,0.15)}
.empty-state p{color:var(--text-muted);font-size:0.85rem}
.empty-state .empty-icon{font-size:2rem;margin-bottom:0.5rem;opacity:0.5}
@media(max-width:768px){
  .dash-stats{grid-template-columns:1fr 1fr}
  .guest-card{flex-direction:column;align-items:flex-start;gap:1rem}
  .guest-actions{width:100%;justify-content:flex-end}
}
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
      <div class="dash-stat"><div class="icon">🚪</div><div class="num">{{ $availableRooms }}</div><div class="lbl">Kamar Tersedia</div></div>
      <div class="dash-stat"><div class="icon">🔒</div><div class="num">{{ $occupiedRooms }}</div><div class="lbl">Kamar Terisi</div></div>
      <div class="dash-stat"><div class="icon">📥</div><div class="num">{{ $todayCheckIns->count() }}</div><div class="lbl">Perlu Check-In</div></div>
      <div class="dash-stat"><div class="icon">📤</div><div class="num">{{ $todayCheckOuts->count() }}</div><div class="lbl">Perlu Check-Out</div></div>
      <div class="dash-stat"><div class="icon">🏨</div><div class="num">{{ $currentGuests->count() }}</div><div class="lbl">Tamu Menginap</div></div>
    </div>

    <!-- Search Booking -->
    <div class="dash-section">
      <h3>🔍 Cari <em>Booking</em></h3>
      <form action="{{ route('receptionist.search') }}" method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Cari berdasarkan Nama Tamu atau Kode Booking..." value="{{ request('search') }}">
        <button type="submit" class="btn-primary btn-sm">Cari</button>
      </form>
    </div>

    <!-- Check-Ins -->
    <div class="dash-section">
      <h3>📥 Menunggu <em>Check-In</em> @if($todayCheckIns->count() > 0)<span class="count">{{ $todayCheckIns->count() }}</span>@endif</h3>
      @forelse($todayCheckIns as $b)
      @php
        $isOverdue = $b->check_in_date->lt(today());
        $paidPct = $b->total_price > 0 ? round(($b->paid_amount / $b->total_price) * 100) : 0;
        $remaining = $b->total_price - $b->paid_amount;
      @endphp
      <div class="guest-card {{ $isOverdue ? 'overdue' : '' }}">
        <div class="guest-info">
          <h4>{{ $b->user->name }}</h4>
          <p>
            {{ $b->booking_code }} · {{ $b->room ? $b->room->getTypeLabel() : 'Belum assign' }} · {{ $b->nights }} malam
            · <span class="badge {{ $b->getStatusBadgeClass() }}">{{ $b->getStatusLabel() }}</span>
            @if($isOverdue)
              · <span style="color:var(--danger);font-size:0.75rem">⚠ Overdue sejak {{ $b->check_in_date->format('d/m') }}</span>
            @endif
          </p>
          <div class="guest-meta">
            <span class="meta-item">📅 {{ $b->check_in_date->format('d/m/Y') }} — {{ $b->check_out_date->format('d/m/Y') }}</span>
            <span class="meta-item">👥 {{ $b->guests }} tamu</span>
            @if($paidPct >= 100)
              <span class="meta-item paid">✅ Lunas</span>
            @elseif($paidPct > 0)
              <span class="meta-item partial">💰 DP {{ $paidPct }}% (Sisa: Rp {{ number_format($remaining, 0, ',', '.') }})</span>
            @else
              <span class="meta-item unpaid">❌ Belum Bayar</span>
            @endif
          </div>
        </div>
        <div class="guest-actions">
          @if($b->status === 'confirmed')
            <a href="{{ route('receptionist.check-in', $b->id) }}" class="btn-primary btn-sm">✓ Check-In</a>
          @elseif($b->status === 'pending')
            <span class="badge badge-warning">Belum Bayar</span>
            <a href="{{ route('receptionist.check-in', $b->id) }}" class="btn-outline btn-sm">Detail</a>
          @endif
        </div>
      </div>
      @empty
      <div class="empty-state">
        <div class="empty-icon">📥</div>
        <p>Tidak ada tamu yang menunggu check-in saat ini.</p>
      </div>
      @endforelse
    </div>

    <!-- Check-Outs -->
    <div class="dash-section">
      <h3>📤 Menunggu <em>Check-Out</em> @if($todayCheckOuts->count() > 0)<span class="count">{{ $todayCheckOuts->count() }}</span>@endif</h3>
      @forelse($todayCheckOuts as $b)
      @php
        $isOverdue = $b->check_out_date->lt(today());
        $overdueDays = $isOverdue ? $b->check_out_date->diffInDays(today()) : 0;
        $remainBal = $b->getRemainingBalance();
        $stayedDays = $b->actual_check_in ? $b->actual_check_in->diffInDays(now()) : $b->check_in_date->diffInDays(now());
      @endphp
      <div class="guest-card {{ $isOverdue ? 'overdue' : '' }}">
        <div class="guest-info">
          <h4>{{ $b->user->name }}</h4>
          <p>
            {{ $b->booking_code }} · Kamar {{ $b->room->room_number ?? '-' }} ({{ $b->room ? $b->room->getTypeLabel() : '-' }})
          </p>
          <div class="guest-meta">
            <span class="meta-item">🕐 Sudah {{ $stayedDays }} hari menginap</span>
            <span class="meta-item">📅 C/O: {{ $b->check_out_date->format('d/m/Y') }}</span>
            @if($remainBal > 0)
              <span class="meta-item unpaid">💰 Sisa: Rp {{ number_format($remainBal, 0, ',', '.') }}</span>
            @else
              <span class="meta-item paid">✅ Lunas</span>
            @endif
            @if($isOverdue)
              <span class="days-left urgent">⚠ Overdue {{ $overdueDays }} hari</span>
            @endif
          </div>
        </div>
        <div class="guest-actions">
          <a href="{{ route('receptionist.guest-bill', $b->id) }}" class="btn-outline btn-sm">Guest Bill</a>
          <a href="{{ route('receptionist.check-out', $b->id) }}" class="btn-primary btn-sm">📤 Check-Out</a>
        </div>
      </div>
      @empty
      <div class="empty-state">
        <div class="empty-icon">📤</div>
        <p>Tidak ada tamu yang menunggu check-out saat ini.</p>
      </div>
      @endforelse
    </div>

    <!-- Current Guests -->
    <div class="dash-section">
      <h3>🏨 Tamu <em>Menginap</em> @if($currentGuests->count() > 0)<span class="count">{{ $currentGuests->count() }}</span>@endif</h3>
      @forelse($currentGuests as $b)
      @php
        $daysLeft = now()->startOfDay()->diffInDays($b->check_out_date, false);
        $stayedDays = $b->actual_check_in ? $b->actual_check_in->diffInDays(now()) : $b->check_in_date->diffInDays(now());
        $remainBal = $b->getRemainingBalance();
        $svcTotal = $b->getServicesTotal();
      @endphp
      <div class="guest-card">
        <div class="guest-info">
          <h4>{{ $b->user->name }}</h4>
          <p>{{ $b->booking_code }} · Kamar {{ $b->room->room_number ?? '-' }} · {{ $b->room ? $b->room->getTypeLabel() : '' }}</p>
          <div class="guest-meta">
            <span class="meta-item">📅 C/I: {{ $b->actual_check_in ? $b->actual_check_in->format('d/m/Y H:i') : $b->check_in_date->format('d/m/Y') }}</span>
            <span class="meta-item">📅 C/O: {{ $b->check_out_date->format('d/m/Y') }}</span>
            @if($daysLeft > 0)
              <span class="days-left normal">{{ $daysLeft }} hari lagi</span>
            @elseif($daysLeft == 0)
              <span class="days-left urgent">Check-out hari ini</span>
            @else
              <span class="days-left urgent">Overdue {{ abs($daysLeft) }} hari</span>
            @endif
            <span class="meta-item">🕐 {{ $stayedDays }} hari menginap</span>
            @if($svcTotal > 0)
              <span class="meta-item">🛎 Layanan: Rp {{ number_format($svcTotal, 0, ',', '.') }}</span>
            @endif
            @if($remainBal > 0)
              <span class="meta-item partial">💰 Sisa: Rp {{ number_format($remainBal, 0, ',', '.') }}</span>
            @elseif($remainBal == 0)
              <span class="meta-item paid">✅ Lunas</span>
            @endif
          </div>
        </div>
        <div class="guest-actions">
          <a href="{{ route('receptionist.guest-bill', $b->id) }}" class="btn-outline btn-sm">Guest Bill</a>
          @if($daysLeft <= 0)
            <a href="{{ route('receptionist.check-out', $b->id) }}" class="btn-primary btn-sm">📤 Check-Out</a>
          @endif
        </div>
      </div>
      @empty
      <div class="empty-state">
        <div class="empty-icon">🏨</div>
        <p>Tidak ada tamu yang menginap saat ini.</p>
      </div>
      @endforelse
    </div>

    <!-- Riwayat Check-Out Hari Ini -->
    <div class="dash-section">
      <h3>✅ Riwayat <em>Check-Out</em> Hari Ini @if($todayCheckedOut->count() > 0)<span class="count">{{ $todayCheckedOut->count() }}</span>@endif</h3>
      @forelse($todayCheckedOut as $b)
      @php
        $servicesTotal = $b->getServicesTotal();
        $totalBill = $b->total_price + $servicesTotal;
        $lastTx = $b->transactions->where('type', 'checkout_payment')->first();
        $payMethod = $lastTx ? str_replace('_', ' ', ucfirst($lastTx->payment_method)) : '-';
      @endphp
      <div class="guest-card" style="border-left:3px solid var(--success)">
        <div class="guest-info">
          <h4>{{ $b->user->name }}</h4>
          <p>
            {{ $b->booking_code }} · Kamar {{ $b->room->room_number ?? '-' }} ({{ $b->room ? $b->room->getTypeLabel() : '-' }})
          </p>
          <div class="guest-meta">
            <span class="meta-item">🕐 Check-out: {{ $b->actual_check_out ? $b->actual_check_out->format('H:i') : '-' }}</span>
            <span class="meta-item">📅 {{ $b->check_in_date->format('d/m') }} — {{ $b->check_out_date->format('d/m/Y') }}</span>
            <span class="meta-item">🏷 {{ $b->nights }} malam</span>
            <span class="meta-item paid">💰 Total: Rp {{ number_format($totalBill, 0, ',', '.') }}</span>
            @if($servicesTotal > 0)
              <span class="meta-item">🛎 Layanan: Rp {{ number_format($servicesTotal, 0, ',', '.') }}</span>
            @endif
            <span class="meta-item">💳 {{ $payMethod }}</span>
          </div>
        </div>
        <div class="guest-actions">
          <span class="badge badge-secondary">Selesai</span>
          <a href="{{ route('receptionist.invoice', $b->id) }}" class="btn-outline btn-sm">📄 Invoice</a>
        </div>
      </div>
      @empty
      <div class="empty-state">
        <div class="empty-icon">✅</div>
        <p>Belum ada tamu yang check-out hari ini.</p>
      </div>
      @endforelse
    </div>
  </div>
</section>
@endsection

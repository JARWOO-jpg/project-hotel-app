@extends('layouts.app')
@section('title', 'Admin Dashboard - Hotel Nusantara')
@section('extra-css')
<style>
.dash-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;margin-bottom:3rem}
.dash-stat{background:var(--dark2);border:1px solid rgba(201,169,110,0.1);padding:1.8rem;transition:border-color 0.3s}
.dash-stat:hover{border-color:rgba(201,169,110,0.4)}
.dash-stat .icon{font-size:1.5rem;margin-bottom:0.8rem}
.dash-stat .num{font-family:'Cormorant Garamond',serif;font-size:2.2rem;color:var(--gold);font-weight:300}
.dash-stat .lbl{font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--text-muted);margin-top:0.2rem}
.admin-nav{display:flex;gap:0.8rem;margin-bottom:2rem;flex-wrap:wrap}
.admin-nav a{padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase;transition:all 0.3s}
.admin-nav a:hover,.admin-nav a.active{border-color:var(--gold);color:var(--gold);background:rgba(201,169,110,0.05)}
.chart-bar{display:flex;align-items:end;gap:0.5rem;height:200px;margin-top:1rem}
.chart-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:0.3rem}
.chart-col .bar{width:100%;background:linear-gradient(to top,var(--gold-dark),var(--gold));min-height:2px;transition:height 0.5s}
.chart-col .val{font-size:0.65rem;color:var(--text-muted)}
.chart-col .lab{font-size:0.6rem;color:var(--text-muted);text-align:center}
.dash-grid{display:grid;grid-template-columns:2fr 1fr;gap:2rem;margin-bottom:3rem}
@media(max-width:768px){.dash-stats{grid-template-columns:1fr 1fr}.dash-grid{grid-template-columns:1fr}}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner">
    <div class="section-header" style="margin-bottom:2rem">
      <div class="section-tag">Administrator</div>
      <h2 class="section-title" style="font-size:2rem">Admin <em>Dashboard</em></h2>
    </div>
    <div class="admin-nav">
      <a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a>
      <a href="{{ route('admin.rooms') }}">Kamar</a>
      <a href="{{ route('admin.bookings') }}">Booking</a>
      <a href="{{ route('admin.users') }}">Pengguna</a>
      <a href="{{ route('admin.reports') }}">Laporan</a>
      <a href="{{ route('admin.cms') }}">CMS</a>
    </div>

    <div class="dash-stats">
      <div class="dash-stat"><div class="icon">💰</div><div class="num">Rp {{ number_format($monthlyRevenue/1000000,1,',','.') }}jt</div><div class="lbl">Pendapatan Bulan Ini</div></div>
      <div class="dash-stat"><div class="icon">📋</div><div class="num">{{ $activeBookings }}</div><div class="lbl">Booking Aktif</div></div>
      <div class="dash-stat"><div class="icon">🏨</div><div class="num">{{ $occupancyRate }}%</div><div class="lbl">Okupansi</div></div>
      <div class="dash-stat"><div class="icon">🚪</div><div class="num">{{ $availableRooms }}/{{ $totalRooms }}</div><div class="lbl">Kamar Tersedia</div></div>
    </div>

    <div class="dash-grid">
      <div class="card">
        <h3 style="color:var(--cream);font-size:1.2rem;margin-bottom:1rem">📊 Pendapatan 6 Bulan Terakhir</h3>
        @php $maxRev = max(array_column($revenueChart, 'revenue')) ?: 1; @endphp
        <div class="chart-bar">
          @foreach($revenueChart as $rc)
          <div class="chart-col">
            <div class="val">{{ $rc['revenue'] > 0 ? 'Rp '.number_format($rc['revenue']/1000000,1,',','.').'jt' : '-' }}</div>
            <div class="bar" style="height:{{ ($rc['revenue']/$maxRev)*160 }}px"></div>
            <div class="lab">{{ $rc['month'] }}</div>
          </div>
          @endforeach
        </div>
      </div>
      <div>
        <div class="card" style="margin-bottom:1.5rem">
          <h3 style="color:var(--cream);font-size:1.2rem;margin-bottom:1rem">🏨 Okupansi per Tipe</h3>
          @foreach($roomStats as $rs)
          <div style="margin-bottom:1rem">
            <div style="display:flex;justify-content:space-between;font-size:0.82rem;margin-bottom:0.3rem">
              <span style="color:var(--text-muted)">{{ \App\Models\Room::make(['type'=>$rs->type])->getTypeLabel() }}</span>
              <span style="color:var(--gold)">{{ $rs->occupied }}/{{ $rs->total }}</span>
            </div>
            <div style="height:6px;background:var(--dark3);overflow:hidden"><div style="height:100%;background:var(--gold);width:{{ $rs->total>0?($rs->occupied/$rs->total)*100:0 }}%"></div></div>
          </div>
          @endforeach
        </div>
        @if($waitingList->count() > 0)
        <div class="card">
          <h3 style="color:var(--cream);font-size:1.2rem;margin-bottom:1rem">⏳ Waiting List ({{ $waitingList->count() }})</h3>
          @foreach($waitingList as $wl)
          <div style="border-bottom:1px solid rgba(201,169,110,0.08);padding:0.8rem 0">
            <div style="color:var(--cream);font-size:0.85rem">{{ $wl->user->name }}</div>
            <div style="color:var(--text-muted);font-size:0.75rem">{{ $wl->check_in_date->format('d/m') }} - {{ $wl->check_out_date->format('d/m') }}</div>
            <form method="POST" action="{{ route('admin.bookings.waiting-list', $wl->id) }}" style="display:flex;gap:0.5rem;margin-top:0.5rem">
              @csrf
              <button name="action" value="approve" class="btn-primary btn-sm" style="padding:0.3rem 0.8rem;font-size:0.7rem">Terima</button>
              <button name="action" value="reject" class="btn-outline btn-sm" style="padding:0.3rem 0.8rem;font-size:0.7rem;border-color:var(--danger);color:var(--danger)">Tolak</button>
            </form>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>

    <div class="card">
      <h3 style="color:var(--cream);font-size:1.2rem;margin-bottom:1rem">📋 Booking Terbaru</h3>
      <div class="table-wrap"><table>
        <thead><tr><th>Kode</th><th>Tamu</th><th>Kamar</th><th>Periode</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
          @foreach($recentBookings as $b)
          <tr>
            <td style="color:var(--gold)">{{ $b->booking_code }}</td>
            <td style="color:var(--cream)">{{ $b->user->name }}</td>
            <td>{{ $b->room ? $b->room->room_number : '-' }}</td>
            <td>{{ $b->check_in_date->format('d/m') }} - {{ $b->check_out_date->format('d/m') }}</td>
            <td>Rp {{ number_format($b->total_price,0,',','.') }}</td>
            <td><span class="badge {{ $b->getStatusBadgeClass() }}">{{ $b->getStatusLabel() }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table></div>
    </div>
  </div>
</section>
@endsection

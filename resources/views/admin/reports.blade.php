@extends('layouts.app')
@section('title', 'Laporan - Hotel Nusantara')
@section('extra-css')
<style>
.report-grid{display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:2rem}
.chart-bar{display:flex;align-items:end;gap:0.3rem;height:180px;margin-top:1rem}
.chart-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:0.3rem}
.chart-col .bar{width:100%;background:linear-gradient(to top,var(--gold-dark),var(--gold));min-height:2px}
.chart-col .val{font-size:0.6rem;color:var(--text-muted)}
.chart-col .lab{font-size:0.55rem;color:var(--text-muted);text-align:center}
.demo-stats{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}
.demo-stat{text-align:center;padding:1.5rem;border:1px solid rgba(201,169,110,0.1)}
.demo-stat .num{font-family:'Cormorant Garamond',serif;font-size:2rem;color:var(--gold)}
.demo-stat .lbl{font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px}
@media(max-width:768px){.report-grid{grid-template-columns:1fr}}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner">
    <div class="section-header" style="margin-bottom:2rem">
      <div class="section-tag">Laporan</div>
      <h2 class="section-title" style="font-size:2rem">Laporan & <em>Analitik</em></h2>
    </div>

    <div class="report-grid">
      <div class="card">
        <h3 style="color:var(--cream);font-size:1.2rem;margin-bottom:0.5rem">📊 Pendapatan {{ $year }}</h3>
        @php $maxR = max(array_column($revenueData,'value')) ?: 1; @endphp
        <div class="chart-bar">
          @foreach($revenueData as $rd)
          <div class="chart-col">
            <div class="val">{{ $rd['value']>0?number_format($rd['value']/1000000,1).'jt':'-' }}</div>
            <div class="bar" style="height:{{ ($rd['value']/$maxR)*150 }}px"></div>
            <div class="lab">{{ $rd['label'] }}</div>
          </div>
          @endforeach
        </div>
      </div>

      <div class="card">
        <h3 style="color:var(--cream);font-size:1.2rem;margin-bottom:1rem">🏨 Okupansi per Tipe</h3>
        @foreach($occupancyData as $od)
        <div style="margin-bottom:1.2rem">
          <div style="display:flex;justify-content:space-between;font-size:0.82rem;margin-bottom:0.3rem">
            <span style="color:var(--text-muted)">{{ $od['type'] }}</span>
            <span style="color:var(--gold)">{{ $od['rate'] }}% ({{ $od['occupied'] }}/{{ $od['total'] }})</span>
          </div>
          <div style="height:8px;background:var(--dark3);overflow:hidden"><div style="height:100%;background:linear-gradient(90deg,var(--gold-dark),var(--gold));width:{{ $od['rate'] }}%;transition:width 0.5s"></div></div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="card">
      <h3 style="color:var(--cream);font-size:1.2rem;margin-bottom:1.5rem">👥 Demografi Tamu</h3>
      <div class="demo-stats">
        <div class="demo-stat"><div class="num">{{ $guestDemographics['total_guests'] }}</div><div class="lbl">Total Tamu</div></div>
        <div class="demo-stat"><div class="num">{{ $guestDemographics['total_bookings'] }}</div><div class="lbl">Total Booking</div></div>
        <div class="demo-stat"><div class="num">{{ number_format($guestDemographics['avg_stay'],1) }}</div><div class="lbl">Rata-rata Menginap (Malam)</div></div>
        <div class="demo-stat"><div class="num">{{ $guestDemographics['repeat_guests'] }}</div><div class="lbl">Tamu Berulang</div></div>
      </div>
    </div>
  </div>
</section>
@endsection

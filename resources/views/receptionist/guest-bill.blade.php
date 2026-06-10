@extends('layouts.app')
@section('title', 'Guest Bill - Hotel Nusantara')
@section('content')
<section>
  <div class="section-inner" style="max-width:700px">
    <div class="card">
      <div class="card-header" style="text-align:center">
        <div class="section-tag">Guest Bill</div>
        <h2 style="font-size:1.8rem;color:var(--cream)">{{ $booking->user->name }}</h2>
        <p style="color:var(--text-muted);font-size:0.85rem">{{ $booking->booking_code }} · Kamar {{ $booking->room->room_number ?? '-' }}</p>
      </div>
      @php $svcTotal = $booking->getServicesTotal(); $total = $booking->total_price + $svcTotal; @endphp
      <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)"><span style="color:var(--text-muted)">Biaya Kamar</span><span style="color:var(--cream)">Rp {{ number_format($booking->total_price,0,',','.') }}</span></div>
      @foreach($booking->services->where('status','!=','cancelled') as $s)
      <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)"><span style="color:var(--text-muted)">{{ $s->getServiceTypeLabel() }}: {{ $s->description }} ×{{ $s->quantity }}</span><span style="color:var(--cream)">Rp {{ number_format($s->total,0,',','.') }}</span></div>
      @endforeach
      <div style="display:flex;justify-content:space-between;padding:0.8rem 0;border-top:2px solid var(--gold);margin-top:0.5rem"><strong style="color:var(--gold)">TOTAL</strong><span style="color:var(--gold);font-family:'Cormorant Garamond',serif;font-size:1.5rem">Rp {{ number_format($total,0,',','.') }}</span></div>
      <div style="display:flex;justify-content:space-between;padding:0.5rem 0"><span style="color:var(--text-muted)">Dibayar</span><span style="color:var(--success)">Rp {{ number_format($booking->paid_amount,0,',','.') }}</span></div>
      <div style="display:flex;justify-content:space-between;padding:0.5rem 0"><span style="color:var(--text-muted)">Sisa</span><span style="color:var(--danger)">Rp {{ number_format($total - $booking->paid_amount,0,',','.') }}</span></div>

      <h4 style="color:var(--gold);margin-top:2rem;font-size:1.1rem">Tambah Layanan</h4>
      <form method="POST" action="{{ route('receptionist.add-service', $booking->id) }}" style="margin-top:1rem">@csrf
        <div class="form-row">
          <div class="form-group"><label>Tipe</label><select name="service_type" required><option value="room_service">Room Service</option><option value="laundry">Laundry</option><option value="spa">Spa</option><option value="transport">Transportasi</option><option value="other">Lainnya</option></select></div>
          <div class="form-group"><label>Deskripsi</label><input type="text" name="description" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Harga (Rp)</label><input type="number" name="amount" required min="0"></div>
          <div class="form-group"><label>Qty</label><input type="number" name="quantity" value="1" required min="1"></div>
        </div>
        <button type="submit" class="btn-primary btn-sm">+ Tambah Layanan</button>
      </form>
      <div style="text-align:center;margin-top:2rem"><a href="{{ route('receptionist.dashboard') }}" class="btn-outline btn-sm">← Dashboard</a></div>
    </div>
  </div>
</section>
@endsection

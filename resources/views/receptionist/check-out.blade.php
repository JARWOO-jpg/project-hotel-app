@extends('layouts.app')
@section('title', 'Check-Out - Hotel Nusantara')
@section('content')
<section>
  <div class="section-inner" style="max-width:750px">
    <div class="card">
      <div class="card-header" style="text-align:center">
        <div class="section-tag">Check-Out</div>
        <h2 style="font-size:1.8rem;color:var(--cream)">Proses Check-Out</h2>
      </div>
      <div style="margin-bottom:1.5rem">
        @foreach([['Tamu',$booking->user->name],['Kamar',$booking->room->room_number.' - '.$booking->room->getTypeLabel()],['Lama Menginap',$nights.' malam']] as $d)
        <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)">
          <span style="color:var(--text-muted)">{{ $d[0] }}</span><span style="color:var(--cream)">{{ $d[1] }}</span>
        </div>
        @endforeach
      </div>
      <h4 style="color:var(--gold);font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem">Rincian Biaya</h4>
      <div style="display:flex;justify-content:space-between;padding:0.5rem 0;font-size:0.85rem">
        <span style="color:var(--text-muted)">Biaya Kamar</span><span style="color:var(--text)">Rp {{ number_format($roomCharge,0,',','.') }}</span>
      </div>
      @foreach($booking->services->where('status','!=','cancelled') as $svc)
      <div style="display:flex;justify-content:space-between;padding:0.5rem 0;font-size:0.85rem">
        <span style="color:var(--text-muted)">{{ $svc->getServiceTypeLabel() }}: {{ $svc->description }}</span><span style="color:var(--text)">Rp {{ number_format($svc->total,0,',','.') }}</span>
      </div>
      @endforeach
      <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-top:1px solid rgba(201,169,110,0.15);margin-top:0.5rem">
        <strong style="color:var(--text-muted)">Total</strong><strong style="color:var(--cream)">Rp {{ number_format($totalBill,0,',','.') }}</strong>
      </div>
      <div style="display:flex;justify-content:space-between;padding:0.5rem 0">
        <span style="color:var(--text-muted)">Dibayar</span><span style="color:var(--success)">- Rp {{ number_format($booking->paid_amount,0,',','.') }}</span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:0.8rem 0;border-top:2px solid var(--gold)">
        <strong style="color:var(--gold)">SISA</strong><span style="color:var(--gold);font-family:'Cormorant Garamond',serif;font-size:1.5rem">Rp {{ number_format($remaining,0,',','.') }}</span>
      </div>
      <details style="margin:1.5rem 0"><summary style="cursor:pointer;color:var(--gold);font-size:0.85rem">+ Tambah Layanan</summary>
        <form method="POST" action="{{ route('receptionist.add-service', $booking->id) }}" style="margin-top:1rem">@csrf
          <div class="form-row">
            <div class="form-group"><label>Tipe</label><select name="service_type" required><option value="room_service">Room Service</option><option value="laundry">Laundry</option><option value="spa">Spa</option><option value="other">Lainnya</option></select></div>
            <div class="form-group"><label>Deskripsi</label><input type="text" name="description" required></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>Harga (Rp)</label><input type="number" name="amount" required min="0"></div>
            <div class="form-group"><label>Qty</label><input type="number" name="quantity" value="1" required min="1"></div>
          </div>
          <button type="submit" class="btn-outline btn-sm">Tambah</button>
        </form>
      </details>
      <form method="POST" action="{{ route('receptionist.process-check-out', $booking->id) }}">@csrf
        @if($remaining > 0)
        <div class="form-group"><label>Metode Pelunasan</label><select name="payment_method" required><option value="cash">Cash</option><option value="transfer">Transfer</option><option value="credit_card">Kartu Kredit</option><option value="e_wallet">E-Wallet</option></select></div>
        @else<input type="hidden" name="payment_method" value="cash">@endif
        <button type="submit" class="btn-primary" style="width:100%">✓ Proses Check-Out</button>
      </form>
      <div style="text-align:center;margin-top:1.5rem"><a href="{{ route('receptionist.dashboard') }}" class="btn-outline btn-sm">← Dashboard</a></div>
    </div>
  </div>
</section>
@endsection

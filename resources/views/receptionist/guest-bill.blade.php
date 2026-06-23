@extends('layouts.app')
@section('title', 'Guest Bill - Hotel Nusantara')
@section('extra-css')
<style>
.bill-row{display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)}
.bill-label{color:var(--text-muted);font-size:0.85rem}
.bill-value{color:var(--cream);font-size:0.85rem}
.svc-form{margin-top:1rem;padding:1rem;background:var(--dark3);border:1px solid rgba(201,169,110,0.1)}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner" style="max-width:700px">
    <div class="card">
      <div class="card-header" style="text-align:center">
        <div class="section-tag">Guest Bill</div>
        <h2 style="font-size:1.8rem;color:var(--cream)">{{ $booking->user->name }}</h2>
        <p style="color:var(--text-muted);font-size:0.85rem">{{ $booking->booking_code }} · Kamar {{ $booking->room->room_number ?? '-' }}</p>
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

      @php $svcTotal = $booking->getServicesTotal(); $total = $booking->total_price + $svcTotal; @endphp
      
      <div class="bill-row"><span class="bill-label">Biaya Kamar ({{ $booking->nights }} malam)</span><span class="bill-value">Rp {{ number_format($booking->total_price,0,',','.') }}</span></div>
      
      @foreach($booking->services->where('status','!=','cancelled') as $s)
      <div class="bill-row"><span class="bill-label">{{ $s->getServiceTypeLabel() }}: {{ $s->description }} ×{{ $s->quantity }}</span><span class="bill-value">Rp {{ number_format($s->total,0,',','.') }}</span></div>
      @endforeach
      
      <div style="display:flex;justify-content:space-between;padding:0.8rem 0;border-top:2px solid var(--gold);margin-top:0.5rem"><strong style="color:var(--gold)">TOTAL</strong><span style="color:var(--gold);font-family:'Cormorant Garamond',serif;font-size:1.5rem">Rp {{ number_format($total,0,',','.') }}</span></div>
      <div class="bill-row" style="border:none"><span class="bill-label">Dibayar</span><span style="color:var(--success)">Rp {{ number_format($booking->paid_amount,0,',','.') }}</span></div>
      <div class="bill-row" style="border:none"><span class="bill-label">Sisa</span><span style="color:{{ ($total - $booking->paid_amount) > 0 ? 'var(--danger)' : 'var(--success)' }}">Rp {{ number_format($total - $booking->paid_amount,0,',','.') }}</span></div>

      {{-- Tambah Layanan (hanya jika tamu masih checked_in) --}}
      @if($booking->status === 'checked_in')
      <h4 style="color:var(--gold);margin-top:2rem;font-size:1.1rem">Tambah Layanan</h4>
      <form method="POST" action="{{ route('receptionist.add-service', $booking->id) }}" class="svc-form">
        @csrf
        <div class="form-row">
          <div class="form-group">
            <label>Tipe</label>
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
            <input type="text" name="description" required placeholder="Contoh: Makan malam">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Harga (Rp)</label>
            <input type="number" name="amount" required min="0" placeholder="50000">
          </div>
          <div class="form-group">
            <label>Qty</label>
            <input type="number" name="quantity" value="1" required min="1">
          </div>
        </div>
        <button type="submit" class="btn-primary btn-sm">+ Tambah Layanan</button>
      </form>
      @else
      <p style="color:var(--text-muted);font-size:0.82rem;margin-top:1.5rem;font-style:italic">Layanan tambahan hanya dapat ditambahkan saat tamu sedang menginap (status check-in).</p>
      @endif

      {{-- Riwayat Transaksi --}}
      @if($booking->transactions->count() > 0)
      <h4 style="color:var(--gold);margin-top:2rem;font-size:1.1rem">Riwayat Transaksi</h4>
      @foreach($booking->transactions->sortByDesc('created_at') as $trx)
      <div class="bill-row">
        <span class="bill-label">
          {{ $trx->getTypeLabel() }}
          <small style="display:block;color:var(--text-muted);font-size:0.7rem">{{ $trx->created_at->format('d/m/Y H:i') }} · {{ $trx->payment_method }}</small>
        </span>
        <span style="color:{{ $trx->status === 'success' ? 'var(--success)' : ($trx->status === 'pending' ? 'var(--warning)' : 'var(--danger)') }}">
          Rp {{ number_format($trx->amount,0,',','.') }}
          <small class="badge {{ $trx->status === 'success' ? 'badge-success' : ($trx->status === 'pending' ? 'badge-warning' : 'badge-danger') }}" style="margin-left:0.3rem">{{ ucfirst($trx->status) }}</small>
        </span>
      </div>
      @endforeach
      @endif

      <div style="text-align:center;margin-top:2rem">
        <a href="{{ route('receptionist.dashboard') }}" class="btn-outline btn-sm">← Dashboard</a>
      </div>
    </div>
  </div>
</section>
@endsection

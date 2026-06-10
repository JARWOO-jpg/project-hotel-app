@extends('layouts.app')
@section('title', 'Booking Kamar - Hotel Nusantara')
@section('extra-css')
<style>
.payment-options{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem}
.payment-opt{border:1px solid rgba(201,169,110,0.2);padding:1.2rem;cursor:pointer;transition:all 0.3s;text-align:center}
.payment-opt:hover{border-color:rgba(201,169,110,0.5)}
.payment-opt.selected{border-color:var(--gold);background:rgba(201,169,110,0.05)}
.payment-opt .pay-label{font-size:0.85rem;color:var(--cream);margin-top:0.3rem;font-weight:500}
.payment-opt .pay-sub{font-size:0.72rem;color:var(--text-muted)}
.modal-total{background:var(--dark3);padding:1.2rem;margin-bottom:1.5rem;border-left:2px solid var(--gold)}
.modal-total .total-label{font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--text-muted)}
.modal-total .total-amount{font-family:'Cormorant Garamond',serif;font-size:2rem;color:var(--gold)}
.booking-summary{display:grid;grid-template-columns:1fr 1fr;gap:3rem}
.room-preview{background:var(--dark3);padding:0;overflow:hidden}
.room-preview img{width:100%;aspect-ratio:16/9;object-fit:cover}
.room-preview-body{padding:1.5rem}
.detail-row{display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(201,169,110,0.08)}
.detail-row .label{color:var(--text-muted);font-size:0.82rem}
.detail-row .value{color:var(--cream);font-size:0.85rem;font-weight:500}
@media(max-width:768px){.booking-summary{grid-template-columns:1fr}}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner">
    <div class="section-header">
      <div class="section-tag">Reservasi</div>
      <h2 class="section-title" style="font-size:2rem">Booking <em>Kamar</em></h2>
    </div>
    @if($errors->any())
      <div class="alert alert-error">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
      </div>
    @endif
    <div class="booking-summary">
      <div>
        <div class="room-preview">
          <img src="https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800&q=80" alt="{{ $room->getTypeLabel() }}">
          <div class="room-preview-body">
            <h3 style="color:var(--cream);font-size:1.5rem">{{ $room->getTypeLabel() }}</h3>
            <p style="color:var(--text-muted);font-size:0.85rem;margin:0.5rem 0">Kamar {{ $room->room_number }} · Kapasitas {{ $room->capacity }} tamu</p>
            <p style="color:var(--text-muted);font-size:0.82rem;line-height:1.7">{{ $room->description }}</p>
            <div style="margin-top:1rem;display:flex;flex-wrap:wrap;gap:0.5rem">
              @if($room->amenities) @foreach($room->amenities as $a)
                <span class="badge badge-info">{{ $a }}</span>
              @endforeach @endif
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="card">
          <h3 style="color:var(--cream);font-size:1.4rem;margin-bottom:1.5rem">Detail Reservasi</h3>
          <div class="detail-row"><span class="label">Check-In</span><span class="value">{{ \Carbon\Carbon::parse($checkIn)->translatedFormat('d M Y') }}</span></div>
          <div class="detail-row"><span class="label">Check-Out</span><span class="value">{{ \Carbon\Carbon::parse($checkOut)->translatedFormat('d M Y') }}</span></div>
          <div class="detail-row"><span class="label">Durasi</span><span class="value">{{ $nights }} Malam</span></div>
          <div class="detail-row"><span class="label">Harga/Malam</span><span class="value">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</span></div>

          <div class="modal-total" style="margin-top:1.5rem">
            <div class="total-label">Total Harga</div>
            <div class="total-amount">Rp {{ number_format($totalPrice, 0, ',', '.') }}</div>
          </div>

          <form method="POST" action="{{ route('booking.store') }}">
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
            <input type="hidden" name="check_in" value="{{ $checkIn }}">
            <input type="hidden" name="check_out" value="{{ $checkOut }}">
            <input type="hidden" name="guests" value="{{ $guests }}">

            <div class="form-group">
              <label>Metode Pembayaran</label>
              <div class="payment-options">
                <label class="payment-opt selected" onclick="selectPayment(this,'full')">
                  <input type="radio" name="payment_type" value="full" checked style="display:none">
                  <div class="pay-label">💳 Bayar Lunas</div>
                  <div class="pay-sub">Rp {{ number_format($totalPrice, 0, ',', '.') }}</div>
                </label>
                <label class="payment-opt" onclick="selectPayment(this,'dp')">
                  <input type="radio" name="payment_type" value="dp" style="display:none">
                  <div class="pay-label">💰 Down Payment</div>
                  <div class="pay-sub">Rp {{ number_format($totalPrice * 0.5, 0, ',', '.') }} (50%)</div>
                </label>
              </div>
            </div>

            <div class="form-group">
              <label>Via Pembayaran</label>
              <select name="payment_method" required>
                <option value="transfer">Transfer Bank</option>
                <option value="credit_card">Kartu Kredit</option>
                <option value="e_wallet">E-Wallet</option>
                <option value="cash">Cash</option>
              </select>
            </div>

            <div class="form-group">
              <label>Permintaan Khusus (Opsional)</label>
              <textarea name="special_request" rows="3" placeholder="Contoh: Extra bed, lantai tinggi, dll.">{{ old('special_request') }}</textarea>
            </div>

            <button type="submit" class="btn-primary" style="width:100%">Konfirmasi Booking</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
@section('scripts')
<script>
function selectPayment(el, type) {
  document.querySelectorAll('.payment-opt').forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  el.querySelector('input').checked = true;
}
</script>
@endsection

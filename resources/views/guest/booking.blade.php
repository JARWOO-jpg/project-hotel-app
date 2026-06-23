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

/* ─── Conflict Modal (Pop-up Peringatan Double Booking) ─────────────── */
.conflict-overlay{position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,0.85);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:all 0.35s}
.conflict-overlay.show{opacity:1;visibility:visible}
.conflict-modal{background:var(--dark2);border:1px solid rgba(231,76,60,0.4);max-width:480px;width:90%;padding:2.5rem;text-align:center;transform:translateY(30px) scale(0.95);transition:all 0.4s ease;box-shadow:0 30px 60px rgba(0,0,0,0.6)}
.conflict-overlay.show .conflict-modal{transform:translateY(0) scale(1)}
.conflict-icon{width:72px;height:72px;margin:0 auto 1.2rem;background:rgba(231,76,60,0.12);border:2px solid rgba(231,76,60,0.4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;animation:conflictPulse 1.5s ease-in-out infinite}
@keyframes conflictPulse{0%,100%{box-shadow:0 0 0 0 rgba(231,76,60,0.3)}50%{box-shadow:0 0 0 12px rgba(231,76,60,0)}}
.conflict-title{font-family:'Cormorant Garamond',serif;font-size:1.6rem;color:var(--danger);margin-bottom:0.6rem}
.conflict-msg{color:var(--text-muted);font-size:0.88rem;line-height:1.7;margin-bottom:1.8rem}
.conflict-actions{display:flex;gap:0.8rem;justify-content:center;flex-wrap:wrap}
.conflict-actions .btn-primary{font-size:0.82rem}
.conflict-actions .btn-outline{font-size:0.82rem;border-color:rgba(201,169,110,0.3)}

/* ─── Booking submit button spinner ─────────────────────────────────── */
.btn-booking{position:relative;overflow:hidden}
.btn-booking .booking-spinner{display:none;width:18px;height:18px;border:2px solid rgba(15,14,12,0.3);border-top-color:var(--dark);border-radius:50%;animation:spin 0.8s linear infinite;margin-right:0.5rem}
.btn-booking.loading .booking-spinner{display:inline-block}
.btn-booking.loading .btn-text{opacity:0.7}
@keyframes spin{to{transform:rotate(360deg)}}

/* ─── Timer info ────────────────────────────────────────────────────── */
.timer-info{background:rgba(243,156,18,0.08);border:1px solid rgba(243,156,18,0.25);padding:0.8rem 1rem;margin-bottom:1.2rem;display:flex;align-items:center;gap:0.7rem;font-size:0.78rem;color:#fbbf24}
.timer-info svg{flex-shrink:0;width:18px;height:18px}
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

          {{-- Timer peringatan --}}
          <div class="timer-info">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Setelah booking dibuat, Anda memiliki <strong>15 menit</strong> untuk menyelesaikan pembayaran sebelum booking otomatis dibatalkan.</span>
          </div>

          <form id="booking-form" method="POST" action="{{ route('booking.store') }}" enctype="multipart/form-data">
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
              <label>Permintaan Khusus (Opsional)</label>
              <textarea name="special_request" rows="3" placeholder="Contoh: Extra bed, lantai tinggi, dll.">{{ old('special_request') }}</textarea>
            </div>

            <div class="form-group">
              <label>Foto Identitas / KTP (Wajib)</label>
                <input type="file" name="identity_photo" accept="image/jpeg,image/png,image/jpg" required oninvalid="this.setCustomValidity('Tolong Isi Foto KTP Anda.')" oninput="this.setCustomValidity('')" style="padding:0.5rem;background:var(--dark3);border:1px solid rgba(201,169,110,0.2);color:var(--text);width:100%">
              <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.3rem">Digunakan untuk verifikasi saat check-in. Maksimal 2MB.</div>
            </div>

            {{-- Info Midtrans --}}
            <div style="background:rgba(0,177,64,0.07);border:1px solid rgba(0,177,64,0.2);padding:1rem;margin-bottom:1.2rem;display:flex;align-items:center;gap:0.8rem">
              <span style="font-size:1.5rem">🔒</span>
              <div>
                <div style="color:#4ade80;font-size:0.8rem;font-weight:600">Pembayaran via Midtrans (GoTo Financial)</div>
                <div style="color:var(--text-muted);font-size:0.72rem;margin-top:0.2rem">GoPay · OVO · QRIS · Virtual Account · Kartu Kredit tersedia di langkah berikutnya</div>
              </div>
            </div>

            <button type="submit" id="btn-submit-booking" class="btn-primary btn-booking" style="width:100%">
              <span class="booking-spinner"></span>
              <span class="btn-text">Lanjut ke Pembayaran →</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══ Pop-up Modal: Kamar Sudah Tidak Tersedia (409 Conflict) ═══ --}}
<div id="conflict-overlay" class="conflict-overlay" onclick="closeConflictModal(event)">
  <div class="conflict-modal" onclick="event.stopPropagation()">
    <div class="conflict-icon">🚫</div>
    <h3 class="conflict-title">Kamar Tidak Tersedia</h3>
    <p class="conflict-msg" id="conflict-message">
      Mohon maaf, kamar ini baru saja dipesan oleh pengguna lain.<br>
      Silakan pilih kamar atau tanggal lain.
    </p>
    <div class="conflict-actions">
      <a href="{{ route('home') }}#rooms" class="btn-primary">🔍 Cari Kamar Lain</a>
      <button onclick="closeConflictModal()" class="btn-outline">Tutup</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function selectPayment(el, type) {
  document.querySelectorAll('.payment-opt').forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  el.querySelector('input').checked = true;
}

// ═══════════════════════════════════════════════════════════════════════════
// AJAX Form Submit — Menangkap 409 Conflict untuk tampilkan popup peringatan
// ═══════════════════════════════════════════════════════════════════════════
const bookingForm = document.getElementById('booking-form');
const submitBtn   = document.getElementById('btn-submit-booking');

bookingForm.addEventListener('submit', async function(e) {
  e.preventDefault();

  // Validasi form secara native dulu
  if (!bookingForm.checkValidity()) {
    bookingForm.reportValidity();
    return;
  }

  // Loading state
  submitBtn.classList.add('loading');
  submitBtn.disabled = true;

  const formData = new FormData(bookingForm);

  try {
    const response = await fetch(bookingForm.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json, text/html',
      },
    });

    if (response.status === 409) {
      // ═══ CONFLICT: Kamar sudah diambil user lain ═══
      const data = await response.json();
      showConflictModal(data.error || 'Kamar ini baru saja dipesan oleh pengguna lain.');
      resetButton();
      return;
    }

    if (response.status === 422) {
      // Validation error
      const data = await response.json();
      const errors = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Validasi gagal');
      showInlineError(errors);
      resetButton();
      return;
    }

    if (response.redirected) {
      // Sukses — ikuti redirect ke halaman pembayaran
      window.location.href = response.url;
      return;
    }

    // Jika response adalah HTML redirect (302 diikuti oleh browser)
    if (response.ok) {
      // Cek apakah response adalah JSON (error) atau HTML (redirect)
      const contentType = response.headers.get('content-type') || '';
      if (contentType.includes('application/json')) {
        const data = await response.json();
        if (data.error) {
          showInlineError(data.error);
          resetButton();
          return;
        }
      }
      // Jika HTML, mungkin ada redirect — submit form secara normal
      bookingForm.removeEventListener('submit', arguments.callee);
      bookingForm.submit();
      return;
    }

    // Error lain
    showInlineError('Terjadi kesalahan saat memproses booking. Silakan coba lagi.');
    resetButton();

  } catch (error) {
    console.error('Booking submit error:', error);
    showInlineError('Koneksi gagal: ' + error.message);
    resetButton();
  }
});

function resetButton() {
  submitBtn.classList.remove('loading');
  submitBtn.disabled = false;
}

// ═══ Conflict Modal (Pop-up peringatan kamar sudah dipesan) ═══
function showConflictModal(message) {
  const overlay = document.getElementById('conflict-overlay');
  const msgEl = document.getElementById('conflict-message');
  if (message) {
    msgEl.innerHTML = message + '<br><br><span style="font-size:0.78rem;color:var(--text-muted)">Kamar hanya ditahan selama 15 menit saat proses pembayaran berlangsung.</span>';
  }
  overlay.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeConflictModal(e) {
  if (e && e.target && !e.target.classList.contains('conflict-overlay')) return;
  const overlay = document.getElementById('conflict-overlay');
  overlay.classList.remove('show');
  document.body.style.overflow = '';
}

// Tutup modal dengan ESC
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeConflictModal();
});

// ═══ Inline Error (untuk error selain 409) ═══
function showInlineError(msg) {
  let errBox = document.getElementById('booking-inline-error');
  if (!errBox) {
    errBox = document.createElement('div');
    errBox.id = 'booking-inline-error';
    errBox.className = 'alert alert-error';
    errBox.style.marginBottom = '1rem';
    submitBtn.parentNode.insertBefore(errBox, submitBtn);
  }
  errBox.innerHTML = msg;
  errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>
@endsection

@extends('layouts.app')
@section('title', 'Pembayaran - Hotel Nusantara')
@section('extra-css')
<style>
/* ─── Payment Page ─────────────────────────────────────────────────────────── */
.pay-wrapper {
    max-width: 900px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2.5rem;
    align-items: start;
}
@media(max-width: 768px) { .pay-wrapper { grid-template-columns: 1fr; } }

/* Summary Card */
.pay-summary { background: var(--dark3); padding: 2rem; border: 1px solid rgba(201,169,110,0.12); }
.pay-summary-header { display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem; padding-bottom:1.5rem; border-bottom:1px solid rgba(201,169,110,0.12); }
.pay-summary-icon { width:52px; height:52px; background:rgba(201,169,110,0.1); border:1px solid rgba(201,169,110,0.25); display:flex; align-items:center; justify-content:center; font-size:1.5rem; }
.pay-row { display:flex; justify-content:space-between; align-items:center; padding:0.6rem 0; border-bottom:1px solid rgba(201,169,110,0.06); }
.pay-row:last-child { border-bottom: none; }
.pay-row .lbl { color:var(--text-muted); font-size:0.82rem; }
.pay-row .val { color:var(--cream); font-size:0.85rem; font-weight:500; }
.pay-total-row { margin-top:1rem; padding:1rem; background:rgba(201,169,110,0.05); border-left:3px solid var(--gold); }
.pay-total-row .lbl { color:var(--text-muted); font-size:0.7rem; text-transform:uppercase; letter-spacing:2px; }
.pay-total-row .val { color:var(--gold); font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:700; }
.pay-badge { display:inline-flex; align-items:center; gap:0.4rem; padding:0.25rem 0.75rem; background:rgba(201,169,110,0.1); border:1px solid rgba(201,169,110,0.3); font-size:0.7rem; letter-spacing:1px; color:var(--gold); text-transform:uppercase; }

/* Payment Widget Card */
.pay-widget { background: var(--dark3); padding: 2rem; border: 1px solid rgba(201,169,110,0.12); position:sticky; top: 2rem; }
.pay-widget-title { font-family:'Cormorant Garamond',serif; font-size:1.6rem; color:var(--cream); margin-bottom:0.4rem; }
.pay-widget-sub { font-size:0.78rem; color:var(--text-muted); margin-bottom:1.5rem; }

/* Payment Methods Grid */
.pay-methods { display:grid; grid-template-columns:repeat(3,1fr); gap:0.5rem; margin:1.2rem 0; }
.pay-method-icon { background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.07); padding:0.7rem 0.4rem; display:flex; flex-direction:column; align-items:center; gap:0.3rem; font-size:0.6rem; color:var(--text-muted); text-align:center; }
.pay-method-icon img { width:32px; height:20px; object-fit:contain; filter:brightness(0.8); }

/* Snap Button */
.btn-pay-midtrans {
    width: 100%;
    padding: 1.1rem;
    background: linear-gradient(135deg, #00b140 0%, #008000 100%);
    border: none;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    letter-spacing: 0.5px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    position: relative;
    overflow: hidden;
}
.btn-pay-midtrans::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
}
.btn-pay-midtrans:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,177,64,0.4); }
.btn-pay-midtrans:disabled { opacity:0.6; cursor:not-allowed; transform:none; }
.btn-pay-midtrans svg { width:22px; height:22px; }

/* Midtrans branding */
.midtrans-brand { display:flex; align-items:center; justify-content:center; gap:0.5rem; margin-top:1rem; color:var(--text-muted); font-size:0.7rem; }
.midtrans-brand strong { color:var(--cream); }
.midtrans-secure { display:flex; align-items:center; justify-content:center; gap:0.5rem; font-size:0.68rem; color:var(--text-muted); margin-top:0.5rem; }

/* Loading Spinner */
.spinner { width:18px; height:18px; border:2px solid rgba(255,255,255,0.3); border-top-color:white; border-radius:50%; animation:spin 0.8s linear infinite; display:none; }
@keyframes spin { to { transform:rotate(360deg); } }

/* Status banners */
.pay-info-box { background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.3); padding:0.8rem 1rem; margin-bottom:1.2rem; display:flex; align-items:center; gap:0.7rem; font-size:0.82rem; color:#93c5fd; }
.pay-expiry { font-size:0.72rem; color:var(--text-muted); text-align:center; margin-top:0.8rem; }
#countdown { color:var(--gold); font-weight:600; }

/* Gojek / GoTo badge */
.goto-badge { background:linear-gradient(135deg,#00aa5b,#0070c0); padding:0.15rem 0.5rem; border-radius:2px; font-size:0.6rem; font-weight:700; color:white; letter-spacing:0.5px; }
</style>
@endsection

@section('content')
<section>
  <div class="section-inner">

    {{-- Session Alerts --}}
    @if(session('info'))
      <div class="alert alert-info" style="max-width:900px;margin:0 auto 2rem">{{ session('info') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error" style="max-width:900px;margin:0 auto 2rem">{{ session('error') }}</div>
    @endif

    <div class="section-header">
      <div class="section-tag">Pembayaran Aman</div>
      <h2 class="section-title" style="font-size:2rem">Selesaikan <em>Pembayaran</em></h2>
    </div>

    <div class="pay-wrapper">

      {{-- ─── LEFT: Booking Summary ──────────────────────────────── --}}
      <div>
        <div class="pay-summary">
          <div class="pay-summary-header">
            <div class="pay-summary-icon">🏨</div>
            <div>
              <div style="color:var(--cream);font-weight:600;font-size:1.05rem">{{ $booking->room->getTypeLabel() }}</div>
              <div style="color:var(--text-muted);font-size:0.8rem">Kamar {{ $booking->room->room_number }}</div>
              <div class="pay-badge" style="margin-top:0.4rem">{{ $booking->booking_code }}</div>
            </div>
          </div>

          <div class="pay-row">
            <span class="lbl">Tamu</span>
            <span class="val">{{ $booking->user->name }}</span>
          </div>
          <div class="pay-row">
            <span class="lbl">Check-In</span>
            <span class="val">{{ $booking->check_in_date->translatedFormat('d M Y') }}</span>
          </div>
          <div class="pay-row">
            <span class="lbl">Check-Out</span>
            <span class="val">{{ $booking->check_out_date->translatedFormat('d M Y') }}</span>
          </div>
          <div class="pay-row">
            <span class="lbl">Durasi</span>
            <span class="val">{{ $booking->nights }} Malam</span>
          </div>
          <div class="pay-row">
            <span class="lbl">Jumlah Tamu</span>
            <span class="val">{{ $booking->guests }} orang</span>
          </div>
          <div class="pay-row">
            <span class="lbl">Total Harga Kamar</span>
            <span class="val">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
          </div>

          @if($booking->payment_type === 'dp')
          <div class="pay-row">
            <span class="lbl">Tipe Pembayaran</span>
            <span class="val" style="color:var(--gold)">Down Payment (50%)</span>
          </div>
          <div class="pay-row">
            <span class="lbl">Sisa setelah check-out</span>
            <span class="val" style="color:#f97316">Rp {{ number_format($booking->total_price - $booking->dp_amount, 0, ',', '.') }}</span>
          </div>
          @endif

          <div class="pay-row pay-total-row" style="margin-top:1.2rem">
            <span class="lbl">{{ $booking->payment_type === 'dp' ? 'Down Payment yang dibayar' : 'Total yang dibayar' }}</span>
            <span class="val">Rp {{ number_format($payAmount, 0, ',', '.') }}</span>
          </div>
        </div>

        {{-- Keamanan info --}}
        <div style="margin-top:1rem;padding:1rem;background:rgba(201,169,110,0.03);border:1px solid rgba(201,169,110,0.1)">
          <div style="font-size:0.75rem;color:var(--text-muted);line-height:1.8">
            🔒 <strong style="color:var(--cream)">Pembayaran 100% Aman</strong><br>
            Transaksi Anda dilindungi oleh enkripsi SSL 256-bit dan sistem antifraud Midtrans dari GoTo Financial.
            Kami tidak menyimpan data kartu Anda.
          </div>
        </div>
      </div>

      {{-- ─── RIGHT: Payment Widget ──────────────────────────────── --}}
      <div>
        <div class="pay-widget">
          <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem">
            <div class="pay-widget-title">Bayar via</div>
            <span class="goto-badge">GoTo Financial</span>
          </div>
          <div class="pay-widget-sub">Pilih metode pembayaran yang tersedia di halaman Midtrans Snap</div>

          {{-- Metode pembayaran yang tersedia --}}
          <div style="font-size:0.72rem;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:0.7rem">Metode Tersedia</div>
          <div class="pay-methods">
            <div class="pay-method-icon">
              <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Gopay_logo.svg/200px-Gopay_logo.svg.png" alt="GoPay" onerror="this.style.display='none'">
              <span>GoPay</span>
            </div>
            <div class="pay-method-icon">
              <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/ShopeePay_Logo.svg/200px-ShopeePay_Logo.svg.png" alt="ShopeePay" onerror="this.style.display='none'">
              <span>ShopeePay</span>
            </div>
            <div class="pay-method-icon">
              <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/49/OVO_Logo.svg/200px-OVO_Logo.svg.png" alt="OVO" onerror="this.style.display='none'">
              <span>OVO</span>
            </div>
            <div class="pay-method-icon">🏦<span>Virtual Account</span></div>
            <div class="pay-method-icon">💳<span>Kartu Kredit</span></div>
            <div class="pay-method-icon">📱<span>QRIS</span></div>
          </div>

          {{-- Amount display --}}
          <div style="text-align:center;padding:1rem;background:rgba(201,169,110,0.05);border:1px solid rgba(201,169,110,0.15);margin-bottom:1.2rem">
            <div style="font-size:0.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:2px;margin-bottom:0.3rem">Total Pembayaran</div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:2.2rem;color:var(--gold);font-weight:700">
              Rp {{ number_format($payAmount, 0, ',', '.') }}
            </div>
          </div>

          {{-- CTA Button --}}
          <button id="pay-btn" class="btn-pay-midtrans" onclick="startPayment()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span id="pay-btn-text">Bayar Sekarang</span>
            <div class="spinner" id="pay-spinner"></div>
          </button>

          <div class="midtrans-brand">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
              <line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            Powered by <strong>Midtrans</strong> &nbsp;|&nbsp; GoTo Financial
          </div>
          <div class="midtrans-secure">
            🔐 Transaksi dienkripsi & dilindungi 3D Secure
          </div>

          {{-- Expiry info --}}
          <div class="pay-expiry">
            Booking akan otomatis dibatalkan jika tidak dibayar dalam
            <span id="countdown">15:00</span>
          </div>

          {{-- Back link --}}
          <div style="text-align:center;margin-top:1.2rem">
            <a href="{{ route('booking.status', $booking->id) }}" class="btn-outline btn-sm">
              ← Kembali ke Detail Booking
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
@endsection

@section('scripts')
{{-- Midtrans Snap.js --}}
<script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
// Gunakan relative path agar otomatis menyesuaikan http/https (mencegah CORS error di Cloudflare)
const snapTokenUrl = "/booking/{{ $booking->id }}/snap-token";
const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content;

// ─── Tampilkan error di dalam halaman (bukan alert popup) ────────────────────
function showError(msg) {
    const btn     = document.getElementById('pay-btn');
    const btnText = document.getElementById('pay-btn-text');
    const spinner = document.getElementById('pay-spinner');

    btn.disabled = false;
    btnText.textContent = 'Coba Lagi';
    spinner.style.display = 'none';

    // Buat / update error box
    let errBox = document.getElementById('pay-error-box');
    if (!errBox) {
        errBox = document.createElement('div');
        errBox.id = 'pay-error-box';
        errBox.style.cssText = `
            background:rgba(239,68,68,0.1);
            border:1px solid rgba(239,68,68,0.4);
            padding:1rem;
            margin-bottom:1rem;
            color:#fca5a5;
            font-size:0.82rem;
            line-height:1.6;
        `;
        btn.parentNode.insertBefore(errBox, btn);
    }
    errBox.innerHTML = `❌ <strong>Gagal memproses pembayaran</strong><br>${msg}`;
    errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ─── Main payment function ───────────────────────────────────────────────────
async function startPayment() {
    const btn     = document.getElementById('pay-btn');
    const btnText = document.getElementById('pay-btn-text');
    const spinner = document.getElementById('pay-spinner');

    // Clear previous error
    const errBox = document.getElementById('pay-error-box');
    if (errBox) errBox.remove();

    btn.disabled = true;
    btnText.textContent = 'Memuat...';
    spinner.style.display = 'block';

    try {
        const response = await fetch(snapTokenUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });

        // Cek content-type sebelum parse JSON
        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            // Server mengembalikan HTML (error 500/419/etc)
            const html = await response.text();
            console.error('Non-JSON response:', html.substring(0, 500));

            if (response.status === 500) {
                showError('Server error (500). Kemungkinan Midtrans SDK belum diinstall atau credentials belum dikonfigurasi. Hubungi admin.');
            } else if (response.status === 419) {
                showError('Session expired (419). Silakan refresh halaman dan coba lagi.');
            } else {
                showError(`Server mengembalikan HTTP ${response.status}. Silakan refresh dan coba lagi.`);
            }
            return;
        }

        const data = await response.json();

        if (!response.ok) {
            showError(data.error || data.message || 'Gagal mendapatkan token pembayaran.');
            return;
        }

        if (!data.snap_token) {
            showError('Token pembayaran tidak valid. Silakan coba lagi.');
            return;
        }

        // Reset button
        btn.disabled = false;
        btnText.textContent = 'Bayar Sekarang';
        spinner.style.display = 'none';

        // Pastikan snap sudah loaded
        if (typeof snap === 'undefined') {
            showError('Midtrans Snap.js gagal dimuat. Periksa koneksi internet Anda.');
            return;
        }

        // Buka Midtrans Snap popup
        snap.pay(data.snap_token, {
            onSuccess: function(result) {
                console.log('Payment success:', result);
                window.location.href = "{{ route('booking.payment.finish', $booking->id) }}";
            },
            onPending: function(result) {
                console.log('Payment pending:', result);
                window.location.href = "{{ route('booking.payment.pending', $booking->id) }}";
            },
            onError: function(result) {
                console.error('Payment error:', result);
                window.location.href = "{{ route('booking.payment.error', $booking->id) }}";
            },
            onClose: function() {
                btn.disabled = false;
                btnText.textContent = 'Bayar Sekarang';
                spinner.style.display = 'none';
            }
        });

    } catch (error) {
        console.error('Fetch error:', error);
        showError('Koneksi gagal: ' + error.message + '. Periksa koneksi internet Anda.');
    }
}

// ─── Countdown 15 menit (sesuai expiry booking) ─────────────────────────────
(function() {
    // Hitung sisa waktu dari created_at booking (15 menit)
    const createdAt = new Date('{{ $booking->created_at->toISOString() }}');
    const expiresAt = new Date(createdAt.getTime() + 15 * 60 * 1000);
    const el = document.getElementById('countdown');
    if (!el) return;

    function updateCountdown() {
        const now = new Date();
        let remaining = Math.max(0, Math.floor((expiresAt - now) / 1000));

        if (remaining <= 0) {
            el.textContent = 'KADALUARSA';
            el.style.color = 'var(--danger)';
            // Redirect ke status page setelah expired
            setTimeout(() => {
                window.location.href = '{{ route("booking.status", $booking->id) }}';
            }, 2000);
            return;
        }

        const m = String(Math.floor(remaining / 60)).padStart(2, '0');
        const s = String(remaining % 60).padStart(2, '0');
        el.textContent = `${m}:${s}`;

        // Warna merah jika sisa < 3 menit
        if (remaining < 180) {
            el.style.color = 'var(--danger)';
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
})();
</script>
@endsection


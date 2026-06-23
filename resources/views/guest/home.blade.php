@extends('layouts.app')
@section('title', 'Hotel Nusantara - Luxury Collection')

@section('extra-css')
@php
  $heroImg   = \App\Models\SiteSetting::getValue('hero_image');
  $heroTitle = \App\Models\SiteSetting::getValue('hero_title', 'Selamat Datang di Hotel Nusantara');
  $heroSub   = \App\Models\SiteSetting::getValue('hero_subtitle', 'Pengalaman menginap mewah di jantung kota dengan sentuhan budaya Nusantara');
  $bgUrl     = $heroImg ? asset('storage/' . $heroImg) : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&q=80';
  
  $promoActive = \App\Models\SiteSetting::getValue('promo_active', '0');
  $promoTitle  = \App\Models\SiteSetting::getValue('promo_title', 'Promo Spesial');
  $promoDesc   = \App\Models\SiteSetting::getValue('promo_description', 'Nikmati penawaran terbatas.');
  $promoImg    = \App\Models\SiteSetting::getValue('promo_image');
  $promoLink   = \App\Models\SiteSetting::getValue('promo_link', '#');

  $facilityTitle = \App\Models\SiteSetting::getValue('facility_title', 'Pengalaman Istimewa');
  $facilitySub   = \App\Models\SiteSetting::getValue('facility_subtitle', 'Nikmati berbagai fasilitas premium yang kami sediakan untuk kenyamanan Anda selama menginap');
@endphp
<style>
.hero{height:100vh;position:relative;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-top:-5rem}
.hero-bg{position:absolute;inset:0;background:linear-gradient(135deg,#0F0E0C 0%,#1a1208 40%,#0d1a15 100%)}
.hero-pattern{position:absolute;inset:0;opacity:0.04;background-image:repeating-linear-gradient(0deg,transparent,transparent 49px,var(--gold) 49px,var(--gold) 50px),repeating-linear-gradient(90deg,transparent,transparent 49px,var(--gold) 49px,var(--gold) 50px)}
.hero-img{position:absolute;inset:0;background:url('{{ $bgUrl }}') center/cover;opacity:0.25}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(15,14,12,0.6) 0%,rgba(15,14,12,0.3) 50%,rgba(15,14,12,0.9) 100%)}
.hero-content{position:relative;text-align:center;max-width:800px;padding:2rem}
.hero-tag{display:inline-block;border:1px solid var(--gold);color:var(--gold);font-size:0.7rem;letter-spacing:4px;text-transform:uppercase;padding:0.4rem 1.2rem;margin-bottom:1.5rem}
.hero h1{font-size:clamp(3rem,7vw,6rem);line-height:1.05;color:var(--cream);margin-bottom:1rem}
.hero h1 em{color:var(--gold);font-style:italic}
.hero-sub{font-size:1rem;color:var(--text-muted);letter-spacing:1px;margin-bottom:3rem;font-weight:300}
.hero-actions{display:flex;gap:1rem;justify-content:center;flex-wrap:wrap}
.hero-scroll{position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:0.5rem;color:var(--text-muted);font-size:0.7rem;letter-spacing:3px;text-transform:uppercase}
.scroll-line{width:1px;height:40px;background:linear-gradient(to bottom,var(--gold),transparent);animation:scrollAnim 2s ease-in-out infinite}
@keyframes scrollAnim{0%,100%{opacity:0.3;transform:scaleY(0.8)}50%{opacity:1;transform:scaleY(1)}}

.booking-bar{background:var(--dark2);border-top:1px solid rgba(201,169,110,0.2);border-bottom:1px solid rgba(201,169,110,0.2);padding:1.5rem 3rem;position:relative;z-index:10}
.booking-inner{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:1rem;align-items:end}
.booking-field label{display:block;font-size:0.65rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin-bottom:0.4rem}
.booking-field input,.booking-field select{width:100%;background:transparent;border:none;border-bottom:1px solid rgba(201,169,110,0.3);padding:0.5rem 0;color:var(--text);font-family:'DM Sans',sans-serif;font-size:0.9rem;outline:none;transition:border-color 0.3s}
.booking-field input:focus,.booking-field select:focus{border-color:var(--gold)}
.booking-field select option{background:var(--dark2)}
.booking-btn{background:var(--gold);color:var(--dark);padding:0.7rem 2rem;border:none;font-family:'DM Sans',sans-serif;font-weight:500;letter-spacing:1px;text-transform:uppercase;cursor:pointer;white-space:nowrap;font-size:0.85rem;transition:all 0.3s}
.booking-btn:hover{background:var(--gold-light)}

.section-header{margin-bottom:4rem}
.section-tag{display:inline-block;font-size:0.65rem;letter-spacing:4px;text-transform:uppercase;color:var(--gold);margin-bottom:0.8rem}
.section-title{font-size:clamp(2rem,4vw,3rem);color:var(--cream);line-height:1.2}
.section-title em{font-style:italic;color:var(--gold)}
.section-line{width:60px;height:1px;background:var(--gold);margin-top:1.2rem}

.rooms-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem}
.room-card{background:var(--dark3);overflow:hidden;cursor:pointer;transition:transform 0.4s,box-shadow 0.4s;position:relative}
.room-card:hover{transform:translateY(-6px);box-shadow:0 20px 60px rgba(0,0,0,0.5)}
.room-img-wrap{overflow:hidden;position:relative;aspect-ratio:4/3;background:linear-gradient(135deg,var(--dark3),var(--dark2))}
.room-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform 0.6s}
.room-card:hover .room-img-wrap img{transform:scale(1.05)}
.room-badge{position:absolute;top:1rem;left:1rem;background:var(--gold);color:var(--dark);font-size:0.65rem;font-weight:500;letter-spacing:2px;text-transform:uppercase;padding:0.3rem 0.8rem;z-index:2}
.room-badge-unavail{background:var(--danger)}
.room-body{padding:1.5rem}
.room-name{font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:300;color:var(--cream);margin-bottom:0.5rem}
.room-amenities{display:flex;gap:1rem;margin-bottom:1.2rem;flex-wrap:wrap}
.amenity{font-size:0.72rem;color:var(--text-muted);display:flex;align-items:center;gap:0.3rem}
.amenity::before{content:'•';color:var(--gold)}
.room-footer{display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(201,169,110,0.15);padding-top:1rem}
.room-price .label{font-size:0.65rem;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase}
.room-price .amount{font-family:'Cormorant Garamond',serif;font-size:1.6rem;color:var(--gold)}
.room-price .per{font-size:0.7rem;color:var(--text-muted)}
.book-btn{background:transparent;border:1px solid var(--gold);color:var(--gold);padding:0.5rem 1.2rem;font-family:'DM Sans',sans-serif;font-size:0.75rem;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all 0.3s;text-decoration:none}
.book-btn:hover{background:var(--gold);color:var(--dark)}

.amenities-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem}
.amenity-card{border:1px solid rgba(201,169,110,0.1);transition:all 0.4s;cursor:pointer;overflow:hidden;background:var(--dark3);position:relative}
.amenity-card:hover{border-color:rgba(201,169,110,0.5);transform:translateY(-6px);box-shadow:0 15px 40px rgba(0,0,0,0.4)}
.amenity-card:hover .amenity-card-img img{transform:scale(1.08)}
.amenity-card-img{width:100%;height:200px;overflow:hidden;position:relative}
.amenity-card-img img{width:100%;height:100%;object-fit:cover;transition:transform 0.6s ease}
.amenity-card-icon{width:100%;height:200px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--dark2),var(--dark3));font-size:3rem}
.amenity-card-body{padding:1.5rem;text-align:center}
.amenity-card h4{font-family:'Cormorant Garamond',serif;font-size:1.3rem;color:var(--cream);margin-bottom:0.5rem}
.amenity-card p{font-size:0.8rem;color:var(--text-muted);line-height:1.7}
.amenity-card-overlay{position:absolute;bottom:0;left:0;right:0;padding:0.5rem;background:linear-gradient(to top,rgba(15,14,12,0.8),transparent);display:flex;align-items:flex-end;justify-content:center;opacity:0;transition:opacity 0.3s}
.amenity-card:hover .amenity-card-overlay{opacity:1}
.amenity-card-overlay span{font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold)}
/* Facility Modal */
.fac-modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.88);z-index:1000;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:all 0.35s;backdrop-filter:blur(8px)}
.fac-modal-overlay.active{opacity:1;visibility:visible}
.fac-modal{background:var(--dark2);border:1px solid rgba(201,169,110,0.25);width:90%;max-width:600px;max-height:90vh;overflow-y:auto;position:relative;transform:translateY(30px) scale(0.96);transition:all 0.4s ease;box-shadow:0 30px 60px rgba(0,0,0,0.5)}
.fac-modal-overlay.active .fac-modal{transform:translateY(0) scale(1)}
.fac-modal-close{position:absolute;top:1rem;right:1rem;background:rgba(15,14,12,0.7);border:1px solid var(--gold);color:var(--gold);width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:10;font-size:1.2rem;transition:all 0.3s;backdrop-filter:blur(5px)}
.fac-modal-close:hover{background:var(--gold);color:var(--dark)}
.fac-modal-img{width:100%;height:320px;object-fit:cover;display:block}
.fac-modal-icon{width:100%;height:200px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--dark3),var(--dark2));font-size:5rem}
.fac-modal-body{padding:2rem 2rem 2.5rem}
.fac-modal-tag{display:inline-block;font-size:0.6rem;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:0.6rem;border:1px solid rgba(201,169,110,0.3);padding:0.2rem 0.6rem}
.fac-modal-title{font-family:'Cormorant Garamond',serif;font-size:2rem;color:var(--cream);line-height:1.2;margin-bottom:1rem}
.fac-modal-desc{color:var(--text-muted);font-size:0.92rem;line-height:1.9}
.fac-modal-divider{width:40px;height:1px;background:var(--gold);margin:1rem 0}

.about-grid{display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:center}
.about-stats{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:2.5rem}
.stat{border-left:1px solid var(--gold);padding-left:1rem}
.stat-num{font-family:'Cormorant Garamond',serif;font-size:2.5rem;font-weight:300;color:var(--gold)}
.stat-label{font-size:0.75rem;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase}

.suggestions-box{background:var(--dark2);border:1px solid rgba(201,169,110,0.2);padding:2rem;margin:2rem auto;max-width:1100px}
.suggestions-box h3{color:var(--gold);margin-bottom:1rem;font-size:1.3rem}

@media(max-width:768px){
  .booking-inner{grid-template-columns:1fr 1fr}
  .rooms-grid,.amenities-grid{grid-template-columns:1fr}
  .about-grid{grid-template-columns:1fr}
  .booking-bar{padding:1.5rem}
}
.modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.85);z-index:1000;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:all 0.3s;backdrop-filter:blur(5px);}
.modal-overlay.active{opacity:1;visibility:visible;}
.modal-content{background:var(--dark2);border:1px solid rgba(201,169,110,0.3);width:90%;max-width:800px;max-height:90vh;overflow-y:auto;position:relative;transform:translateY(20px);transition:all 0.4s;display:grid;grid-template-columns:1fr;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);}
@media(min-width:768px){.modal-content{grid-template-columns:1fr 1.2fr;}}
.modal-overlay.active .modal-content{transform:translateY(0);}
.modal-close{position:absolute;top:1rem;right:1rem;background:rgba(15,14,12,0.8);border:1px solid var(--gold);color:var(--gold);width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:10;transition:all 0.3s;}
.modal-close:hover{background:var(--gold);color:var(--dark);}
.modal-gallery{display:flex;flex-direction:column;background:var(--dark3);}
@media(min-width:768px){.modal-gallery{height:100%;}}
.modal-img-container{height:250px;position:relative;flex:1;}
@media(min-width:768px){.modal-img-container{height:auto;}}
.modal-img{width:100%;height:100%;object-fit:cover;transition:all 0.3s ease;}
.modal-thumbnails{display:flex;gap:0.5rem;padding:0.5rem;background:rgba(15,14,12,0.8);overflow-x:auto;}
.modal-thumb{width:80px;height:60px;object-fit:cover;cursor:pointer;opacity:0.5;transition:all 0.2s;border:2px solid transparent;}
.modal-thumb:hover{opacity:1;}
.modal-thumb.active-thumb{opacity:1;border-color:var(--gold);}
.modal-body{padding:2.5rem;display:flex;flex-direction:column;justify-content:center;}
.modal-title{font-family:'Cormorant Garamond',serif;font-size:2.2rem;color:var(--cream);margin-bottom:0.5rem;line-height:1.1;}
.modal-desc{color:var(--text-muted);font-size:0.9rem;line-height:1.7;margin-bottom:1.5rem;}
.modal-specs{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid rgba(201,169,110,0.15);}
.modal-spec-item{display:flex;flex-direction:column;gap:0.2rem;}
.modal-spec-label{font-size:0.65rem;color:var(--gold);letter-spacing:1px;text-transform:uppercase;}
.modal-spec-val{font-size:0.9rem;color:var(--cream);}
.modal-amenities{display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:2rem;}
.modal-amenity{font-size:0.75rem;color:var(--text);background:var(--dark3);padding:0.4rem 0.8rem;border:1px solid rgba(201,169,110,0.1);border-radius:2px;}
.room-actions{display:flex;gap:0.5rem;}
.detail-btn{background:transparent;border:1px solid rgba(201,169,110,0.3);color:var(--cream);padding:0.5rem 1.2rem;font-family:'DM Sans',sans-serif;font-size:0.75rem;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all 0.3s;text-decoration:none}
.detail-btn:hover{border-color:var(--gold);color:var(--gold)}
</style>
@endsection

@section('content')
<!-- HERO -->
<div class="hero">
  <div class="hero-bg"></div>
  <div class="hero-pattern"></div>
  <div class="hero-img"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-tag">Sejak 1998 · Bandung, Jawa Barat</div>
    <h1>{!! nl2br(e($heroTitle)) !!}</h1>
    <p class="hero-sub">{{ $heroSub }}</p>
    <div class="hero-actions">
      @auth
        <a href="#rooms" class="btn-primary">Reservasi Kamar</a>
      @else
        <a href="{{ route('register') }}" class="btn-primary">Reservasi Kamar</a>
      @endauth
      <a href="#rooms" class="btn-outline">Lihat Kamar</a>
    </div>
  </div>
  <div class="hero-scroll"><div class="scroll-line"></div>Gulir ke bawah</div>
</div>

@if($promoActive == '1')
<div style="background:var(--gold);color:var(--dark);padding:0.8rem;text-align:center;font-size:0.85rem;font-weight:500;position:relative;z-index:10;">
  🎉 <strong>{{ $promoTitle }}</strong> - {{ $promoDesc }}
  @if($promoLink)
    <a href="{{ $promoLink }}" style="color:var(--dark);text-decoration:underline;margin-left:1rem;">Selengkapnya</a>
  @endif
</div>
@endif

<!-- BOOKING BAR -->
<div class="booking-bar">
  <form action="{{ route('check-availability') }}" method="GET" class="booking-inner">
    <div class="booking-field">
      <label>Check-In</label>
      <input type="date" name="check_in" value="{{ $checkIn ?? '' }}" min="{{ date('Y-m-d') }}" required>
    </div>
    <div class="booking-field">
      <label>Check-Out</label>
      <input type="date" name="check_out" value="{{ $checkOut ?? '' }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
    </div>
    <div class="booking-field">
      <label>Tipe Kamar</label>
      <select name="room_type">
        <option value="all">Semua Tipe</option>
        <option value="superior" {{ ($roomType ?? '') == 'superior' ? 'selected' : '' }}>Superior Room</option>
        <option value="deluxe" {{ ($roomType ?? '') == 'deluxe' ? 'selected' : '' }}>Deluxe Room</option>
        <option value="junior_suite" {{ ($roomType ?? '') == 'junior_suite' ? 'selected' : '' }}>Junior Suite</option>
        <option value="presidential_suite" {{ ($roomType ?? '') == 'presidential_suite' ? 'selected' : '' }}>Presidential Suite</option>
      </select>
    </div>
    <div class="booking-field">
      <label>Tamu</label>
      <select name="guests">
        <option>1 Tamu</option><option>2 Tamu</option><option>3 Tamu</option><option>4 Tamu</option>
      </select>
    </div>
    <button type="submit" class="booking-btn">Cek Ketersediaan</button>
  </form>
</div>

@if(isset($searchPerformed) && isset($suggestions) && !empty($suggestions))
<div style="padding:0 3rem">
  <div class="suggestions-box">
    <h3>⚠️ Kamar Tidak Tersedia</h3>
    <p style="color:var(--text-muted);margin-bottom:1rem">Maaf, tidak ada kamar yang tersedia untuk tanggal yang dipilih.</p>
    @if(!empty($suggestions['next_dates']))
      <p style="color:var(--text)">📅 Tanggal terdekat tersedia: <strong style="color:var(--gold)">{{ $suggestions['next_dates']['check_in'] }} s/d {{ $suggestions['next_dates']['check_out'] }}</strong></p>
    @endif
    @if(!empty($suggestions['alternative_types']))
      <p style="color:var(--text);margin-top:0.5rem">🏨 Tipe kamar lain tersedia untuk tanggal ini: 
        @foreach($suggestions['alternative_types'] as $r)
          <span class="badge badge-info">{{ $r->getTypeLabel() }}</span>
        @endforeach
      </p>
    @endif
  </div>
</div>
@endif

<!-- ROOMS SECTION -->
<section id="rooms" style="background:var(--dark2)">
  <div class="section-inner">
    <div class="section-header">
      <div class="section-tag">Akomodasi</div>
      <h2 class="section-title">Kamar & <em>Suite</em></h2>
      <div class="section-line"></div>
    </div>
    <div class="rooms-grid">
      @foreach($rooms as $room)
      <div class="room-card">
        <div class="room-img-wrap">
          @if($room->status === 'maintenance')
            <div class="room-badge room-badge-unavail">Maintenance</div>
          @else
            <div class="room-badge">{{ $room->getTypeLabel() }}</div>
          @endif
          @php
            $roomImages = [
              'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600&q=80',
              'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600&q=80',
              'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600&q=80',
              'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600&q=80',
            ];
          @endphp
          <img src="{{ $roomImages[$loop->index % count($roomImages)] }}" alt="{{ $room->getTypeLabel() }}">
        </div>
        <div class="room-body">
          <h3 class="room-name">{{ $room->getTypeLabel() }} - {{ $room->room_number }}</h3>
          <div class="room-amenities">
            @if($room->amenities)
              @foreach(array_slice($room->amenities, 0, 4) as $a)
                <span class="amenity">{{ $a }}</span>
              @endforeach
            @endif
          </div>
          <div class="room-footer">
            <div class="room-price">
              <span class="label">Mulai dari</span>
              <div><span class="amount">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</span> <span class="per">/ malam</span></div>
            </div>
            <div class="room-actions">
              <button type="button" class="detail-btn" onclick="openModal('roomModal-{{ $room->id }}')">Detail</button>
              @if($room->status !== 'maintenance')
                @auth
                  <a href="{{ route('booking.create', ['room_id' => $room->id, 'check_in' => $checkIn ?? date('Y-m-d'), 'check_out' => $checkOut ?? date('Y-m-d', strtotime('+1 day'))]) }}" class="book-btn">Pesan</a>
                @else
                  <a href="{{ route('login') }}" class="book-btn">Pesan</a>
                @endauth
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Detail Kamar -->
      <div id="roomModal-{{ $room->id }}" class="modal-overlay" onclick="closeModal('roomModal-{{ $room->id }}')">
        <div class="modal-content" onclick="event.stopPropagation()">
          <button class="modal-close" onclick="closeModal('roomModal-{{ $room->id }}')">&times;</button>
          <div class="modal-gallery">
            @php
              $mainImage = $roomImages[$loop->index % count($roomImages)];
              $galleryImages = [
                $mainImage,
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80',
                'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=800&q=80',
                'https://images.unsplash.com/photo-1600566753086-00f18efc22e3?w=800&q=80'
              ];
            @endphp
            <div class="modal-img-container">
              <img id="mainImg-{{ $room->id }}" class="modal-img" src="{{ $mainImage }}" alt="{{ $room->getTypeLabel() }}">
            </div>
            <div class="modal-thumbnails">
              @foreach($galleryImages as $idx => $gImg)
                <img class="modal-thumb {{ $idx === 0 ? 'active-thumb' : '' }}" src="{{ $gImg }}" onclick="changeImage('{{ $room->id }}', this.src, this)">
              @endforeach
            </div>
          </div>
          <div class="modal-body">
            <div class="section-tag" style="margin-bottom:0.5rem">{{ $room->getTypeLabel() }}</div>
            <h3 class="modal-title">Kamar {{ $room->room_number }}</h3>
            <p class="modal-desc">{{ $room->description ?? 'Nikmati kenyamanan beristirahat di ruangan mewah yang dirancang khusus dengan perpaduan gaya modern dan sentuhan budaya Nusantara. Fasilitas premium telah disiapkan untuk memastikan masa menginap Anda tak terlupakan.' }}</p>
            
            <div class="modal-specs">
              <div class="modal-spec-item">
                <span class="modal-spec-label">Kapasitas Maksimal</span>
                <span class="modal-spec-val">{{ $room->capacity ?? 2 }} Dewasa</span>
              </div>
              <div class="modal-spec-item">
                <span class="modal-spec-label">Harga per Malam</span>
                <span class="modal-spec-val" style="color:var(--gold);font-weight:bold;">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</span>
              </div>
            </div>
            
            <div class="modal-spec-label" style="margin-bottom:0.8rem">Fasilitas Kamar</div>
            <div class="modal-amenities">
              @if($room->amenities)
                @foreach($room->amenities as $a)
                  <span class="modal-amenity">{{ $a }}</span>
                @endforeach
              @else
                <span class="modal-amenity">AC</span>
                <span class="modal-amenity">TV Flat</span>
                <span class="modal-amenity">WiFi Gratis</span>
                <span class="modal-amenity">Kamar Mandi Dalam</span>
              @endif
            </div>
            
            @if($room->status !== 'maintenance')
              @auth
                <a href="{{ route('booking.create', ['room_id' => $room->id, 'check_in' => $checkIn ?? date('Y-m-d'), 'check_out' => $checkOut ?? date('Y-m-d', strtotime('+1 day'))]) }}" class="booking-btn" style="text-align:center;text-decoration:none;display:block;">Pesan Kamar Ini</a>
              @else
                <a href="{{ route('login') }}" class="booking-btn" style="text-align:center;text-decoration:none;display:block;">Login untuk Pesan</a>
              @endauth
            @else
              <button class="booking-btn" style="background:var(--danger);color:white;cursor:not-allowed;" disabled>Kamar Sedang Maintenance</button>
            @endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- AMENITIES / FASILITAS -->
<section id="amenities">
  <div class="section-inner">
    <div class="section-header">
      <div class="section-tag">Fasilitas</div>
      @php
        $parts = explode(' ', $facilityTitle, 2);
      @endphp
      <h2 class="section-title">{{ $parts[0] }} <em>{{ $parts[1] ?? '' }}</em></h2>
      @if($facilitySub)
        <p style="color:var(--text-muted);font-size:0.9rem;margin-top:0.8rem;max-width:600px">{{ $facilitySub }}</p>
      @endif
      <div class="section-line"></div>
    </div>
    <div class="amenities-grid">
      @if(isset($facilities) && count($facilities) > 0)
        @foreach($facilities as $f)
        <div class="amenity-card" onclick="openFacilityModal('facModal-{{ $f->id }}')">
          @if($f->image)
            <div class="amenity-card-img">
              <img src="{{ asset('storage/' . $f->image) }}" alt="{{ $f->name }}">
              <div class="amenity-card-overlay"><span>Lihat Detail →</span></div>
            </div>
          @else
            <div class="amenity-card-icon">{{ $f->icon ?? '🌟' }}</div>
          @endif
          <div class="amenity-card-body">
            <h4>{{ $f->name }}</h4>
            <p>{{ Str::limit($f->description, 70) }}</p>
          </div>
        </div>

        {{-- Facility Pop-up Modal --}}
        <div id="facModal-{{ $f->id }}" class="fac-modal-overlay" onclick="closeFacilityModal('facModal-{{ $f->id }}')">
          <div class="fac-modal" onclick="event.stopPropagation()">
            <button class="fac-modal-close" onclick="closeFacilityModal('facModal-{{ $f->id }}')">&times;</button>
            @if($f->image)
              <img src="{{ asset('storage/' . $f->image) }}" alt="{{ $f->name }}" class="fac-modal-img">
            @else
              <div class="fac-modal-icon">{{ $f->icon ?? '🌟' }}</div>
            @endif
            <div class="fac-modal-body">
              <div class="fac-modal-tag">Fasilitas Hotel</div>
              <h3 class="fac-modal-title">{{ $f->name }}</h3>
              <div class="fac-modal-divider"></div>
              <p class="fac-modal-desc">{{ $f->description }}</p>
            </div>
          </div>
        </div>
        @endforeach
      @else
        <div class="amenity-card">
          <div class="amenity-card-icon">🏊</div>
          <div class="amenity-card-body"><h4>Infinity Pool</h4><p>Kolam renang rooftop dengan pemandangan kota yang memukau</p></div>
        </div>
        <div class="amenity-card">
          <div class="amenity-card-icon">🍽️</div>
          <div class="amenity-card-body"><h4>Fine Dining</h4><p>Restoran dengan menu Nusantara modern dan internasional</p></div>
        </div>
        <div class="amenity-card">
          <div class="amenity-card-icon">💆</div>
          <div class="amenity-card-body"><h4>Spa & Wellness</h4><p>Perawatan tradisional Jawa dengan sentuhan modern</p></div>
        </div>
        <div class="amenity-card">
          <div class="amenity-card-icon">🏋️</div>
          <div class="amenity-card-body"><h4>Fitness Center</h4><p>Pusat kebugaran 24 jam dengan peralatan premium</p></div>
        </div>
      @endif
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about" style="background:var(--dark2)">
  <div class="section-inner">
    <div class="about-grid">
      <div>
        <div class="section-tag">Tentang Kami</div>
        <h2 class="section-title">Warisan <em>Keramahan</em><br>Nusantara</h2>
        <div class="section-line"></div>
        <p style="color:var(--text-muted);line-height:1.9;margin-top:1.5rem;font-size:0.95rem">Berdiri sejak 1998, Hotel Nusantara menggabungkan kemewahan modern dengan kehangatan budaya Indonesia. Setiap sudut hotel dirancang untuk memberikan pengalaman yang tak terlupakan.</p>
        <div class="about-stats">
          <div class="stat"><div class="stat-num">{{ $rooms->count() }}</div><div class="stat-label">Kamar & Suite</div></div>
          <div class="stat"><div class="stat-num">26+</div><div class="stat-label">Tahun Berdiri</div></div>
          <div class="stat"><div class="stat-num">4.8</div><div class="stat-label">Rating Tamu</div></div>
          <div class="stat"><div class="stat-num">50K+</div><div class="stat-label">Tamu Puas</div></div>
        </div>
      </div>
      <div style="aspect-ratio:3/4;background:url('https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800&q=80') center/cover"></div>
    </div>
  </div>
</section>

<script>
  function changeImage(roomId, src, thumbElem) {
    document.getElementById('mainImg-' + roomId).src = src;
    var thumbs = thumbElem.parentElement.getElementsByClassName('modal-thumb');
    for(var i=0; i<thumbs.length; i++) {
       thumbs[i].classList.remove('active-thumb');
    }
    thumbElem.classList.add('active-thumb');
  }

  function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
  }
  function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
  }

  function openFacilityModal(id) {
    var el = document.getElementById(id);
    if(el) { el.classList.add('active'); document.body.style.overflow = 'hidden'; }
  }
  function closeFacilityModal(id) {
    var el = document.getElementById(id);
    if(el) { el.classList.remove('active'); document.body.style.overflow = ''; }
  }
  
  // Close modal when pressing ESC key
  document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
      document.querySelectorAll('.modal-overlay.active, .fac-modal-overlay.active').forEach(function(modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
      });
    }
  });
</script>
@endsection

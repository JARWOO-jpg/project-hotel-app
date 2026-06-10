<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Hotel Nusantara')</title>
<meta name="description" content="@yield('description', 'Hotel Nusantara - Pengalaman menginap mewah di jantung kota dengan sentuhan budaya Nusantara')">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
--gold:#C9A96E;--gold-light:#E8D5A3;--gold-dark:#8B6914;
--dark:#0F0E0C;--dark2:#1A1915;--dark3:#252420;
--cream:#F5F0E8;--cream2:#EDE5D5;
--text:#F0EAE0;--text-muted:#9E9580;
--success:#2ecc71;--danger:#e74c3c;--info:#3498db;--warning:#f39c12;
}
body{font-family:'DM Sans',sans-serif;background:var(--dark);color:var(--text);overflow-x:hidden}
h1,h2,h3,h4{font-family:'Cormorant Garamond',serif;font-weight:300}
a{text-decoration:none;color:inherit}

/* NAV */
nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:1.2rem 3rem;display:flex;align-items:center;justify-content:space-between;background:rgba(15,14,12,0.85);backdrop-filter:blur(12px);border-bottom:1px solid rgba(201,169,110,0.15)}
.nav-logo{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:600;color:var(--gold);letter-spacing:2px}
.nav-logo span{display:block;font-size:0.7rem;font-weight:300;letter-spacing:4px;color:var(--text-muted);font-family:'DM Sans',sans-serif;text-transform:uppercase}
.nav-links{display:flex;gap:2rem;list-style:none;align-items:center}
.nav-links a{color:var(--text-muted);text-decoration:none;font-size:0.85rem;letter-spacing:1px;text-transform:uppercase;transition:color 0.3s}
.nav-links a:hover,.nav-links a.active{color:var(--gold)}
.nav-btn{background:var(--gold);color:var(--dark);padding:0.55rem 1.4rem;border:none;font-family:'DM Sans',sans-serif;font-size:0.8rem;font-weight:500;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all 0.3s;text-decoration:none;display:inline-block}
.nav-btn:hover{background:var(--gold-light)}
.nav-user{display:flex;align-items:center;gap:1rem}
.nav-user-name{color:var(--gold);font-size:0.85rem}
.nav-logout{background:none;border:1px solid rgba(201,169,110,0.3);color:var(--text-muted);padding:0.4rem 1rem;font-size:0.75rem;letter-spacing:1px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.3s}
.nav-logout:hover{border-color:var(--gold);color:var(--gold)}

/* BUTTONS */
.btn-primary{background:var(--gold);color:var(--dark);padding:0.85rem 2.2rem;border:none;font-family:'DM Sans',sans-serif;font-size:0.85rem;font-weight:500;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.3s;display:inline-block;text-align:center}
.btn-primary:hover{background:var(--gold-light);transform:translateY(-2px)}
.btn-outline{background:transparent;color:var(--gold);padding:0.85rem 2.2rem;border:1px solid var(--gold);font-family:'DM Sans',sans-serif;font-size:0.85rem;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.3s;display:inline-block;text-align:center}
.btn-outline:hover{background:rgba(201,169,110,0.1)}
.btn-sm{padding:0.5rem 1.2rem;font-size:0.75rem}
.btn-danger{background:var(--danger);color:#fff}
.btn-danger:hover{background:#c0392b}
.btn-success{background:var(--success);color:#fff}
.btn-info{background:var(--info);color:#fff}

/* BADGES */
.badge{display:inline-block;padding:0.25rem 0.75rem;font-size:0.7rem;letter-spacing:1px;text-transform:uppercase;font-weight:500}
.badge-warning{background:rgba(243,156,18,0.15);color:var(--warning);border:1px solid rgba(243,156,18,0.3)}
.badge-info{background:rgba(52,152,219,0.15);color:var(--info);border:1px solid rgba(52,152,219,0.3)}
.badge-success{background:rgba(46,204,113,0.15);color:var(--success);border:1px solid rgba(46,204,113,0.3)}
.badge-danger{background:rgba(231,76,60,0.15);color:var(--danger);border:1px solid rgba(231,76,60,0.3)}
.badge-secondary{background:rgba(158,149,128,0.15);color:var(--text-muted);border:1px solid rgba(158,149,128,0.3)}
.badge-waiting{background:rgba(155,89,182,0.15);color:#9b59b6;border:1px solid rgba(155,89,182,0.3)}

/* ALERTS */
.alert{padding:1rem 1.5rem;margin-bottom:1.5rem;border-left:3px solid;font-size:0.85rem}
.alert-success{background:rgba(46,204,113,0.08);border-color:var(--success);color:var(--success)}
.alert-error{background:rgba(231,76,60,0.08);border-color:var(--danger);color:var(--danger)}
.alert-info{background:rgba(52,152,219,0.08);border-color:var(--info);color:var(--info)}
.alert-warning{background:rgba(243,156,18,0.08);border-color:var(--warning);color:var(--warning)}

/* FORMS */
.form-group{margin-bottom:1.5rem}
.form-group label{display:block;font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);margin-bottom:0.5rem}
.form-group input,.form-group select,.form-group textarea{width:100%;background:var(--dark3);border:1px solid rgba(201,169,110,0.2);padding:0.75rem 1rem;color:var(--text);font-family:'DM Sans',sans-serif;font-size:0.9rem;outline:none;transition:border-color 0.3s}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--gold)}
.form-group select option{background:var(--dark3)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}
.form-error{color:var(--danger);font-size:0.75rem;margin-top:0.3rem}

/* SECTION */
section{padding:6rem 3rem}
.section-inner{max-width:1280px;margin:0 auto}
.page-content{padding-top:5rem;min-height:100vh}

/* CARDS */
.card{background:var(--dark2);border:1px solid rgba(201,169,110,0.1);padding:2rem}
.card-header{border-bottom:1px solid rgba(201,169,110,0.15);padding-bottom:1rem;margin-bottom:1.5rem}

/* TABLE */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
table th{font-size:0.7rem;letter-spacing:2px;text-transform:uppercase;color:var(--gold);padding:0.8rem 1rem;text-align:left;border-bottom:1px solid rgba(201,169,110,0.2)}
table td{padding:0.8rem 1rem;border-bottom:1px solid rgba(201,169,110,0.08);font-size:0.85rem;color:var(--text-muted)}
table tr:hover td{background:rgba(201,169,110,0.03)}

/* TOAST */
.toast{position:fixed;bottom:2rem;right:2rem;background:var(--dark2);border:1px solid var(--gold);padding:1rem 1.5rem;z-index:300;transform:translateY(100px);opacity:0;transition:all 0.4s;max-width:350px}
.toast.show{transform:translateY(0);opacity:1}
.toast-title{font-size:0.85rem;color:var(--gold);margin-bottom:0.2rem;font-weight:500}
.toast-msg{font-size:0.78rem;color:var(--text-muted)}

/* FOOTER */
.main-footer{background:var(--dark3);padding:4rem 3rem 2rem;border-top:1px solid rgba(201,169,110,0.15);}
.footer-inner{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:2fr 1fr 1fr;gap:3rem;padding-bottom:3rem;border-bottom:1px solid rgba(201,169,110,0.1);}
.footer-brand{max-width:300px;}
.footer-logo{font-family:'Cormorant Garamond',serif;font-size:2rem;color:var(--gold);line-height:1;}
.footer-tagline{font-size:0.6rem;letter-spacing:4px;color:var(--text-muted);text-transform:uppercase;margin-bottom:1.5rem;}
.footer-desc{font-size:0.85rem;color:var(--text-muted);line-height:1.8;}
.footer-links-group h4,.footer-contact h4{font-family:'Cormorant Garamond',serif;font-size:1.2rem;color:var(--cream);margin-bottom:1.2rem;}
.footer-links-group ul{list-style:none;}
.footer-links-group li{margin-bottom:0.8rem;}
.footer-links-group a{font-size:0.85rem;color:var(--text-muted);transition:color 0.3s;}
.footer-links-group a:hover{color:var(--gold);}
.footer-contact p{font-size:0.85rem;color:var(--text-muted);line-height:1.6;}
.footer-bottom{max-width:1100px;margin:2rem auto 0;display:flex;justify-content:space-between;align-items:center;}
.footer-bottom p{font-size:0.75rem;color:var(--text-muted);}
.footer-socials{display:flex;gap:1.5rem;}
.footer-socials a{font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;transition:color 0.3s;}
.footer-socials a:hover{color:var(--gold);}
@media(max-width:768px){
  .footer-inner{grid-template-columns:1fr;gap:2rem;}
  .footer-bottom{flex-direction:column;gap:1rem;text-align:center;}
}

/* PAGINATION */
.pagination{display:flex;gap:0.5rem;justify-content:center;margin-top:2rem}
.pagination a,.pagination span{padding:0.5rem 0.8rem;font-size:0.8rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);transition:all 0.3s}
.pagination a:hover{border-color:var(--gold);color:var(--gold)}
.pagination .active span{background:var(--gold);color:var(--dark);border-color:var(--gold)}

/* RESPONSIVE */
@media(max-width:768px){
  nav{padding:1rem 1.5rem;flex-wrap:wrap;gap:0.8rem}
  .nav-links{display:none}
  section{padding:4rem 1.5rem}
  .form-row{grid-template-columns:1fr}
  .page-content{padding-top:4rem}
}
@yield('extra-css')
</style>
</head>
<body>

<nav>
  <a href="{{ route('home') }}" class="nav-logo">Nusantara<span>Luxury Collection</span></a>
  <ul class="nav-links">
    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>
    <li><a href="{{ route('home') }}#rooms">Kamar</a></li>
    <li><a href="{{ route('home') }}#amenities">Fasilitas</a></li>
    @auth
      @if(auth()->user()->isAdmin())
        <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin Panel</a></li>
      @endif
      @if(auth()->user()->isReceptionist() || auth()->user()->isAdmin())
        <li><a href="{{ route('receptionist.dashboard') }}" class="{{ request()->routeIs('receptionist.*') ? 'active' : '' }}">Resepsionis</a></li>
      @endif
      @if(auth()->user()->isGuest())
        <li><a href="{{ route('booking.my-bookings') }}" class="{{ request()->routeIs('booking.my-bookings') ? 'active' : '' }}">Booking Saya</a></li>
      @endif
    @endauth
  </ul>
  @auth
    <div class="nav-user">
      <span class="nav-user-name">{{ auth()->user()->name }}</span>
      <form action="{{ route('logout') }}" method="POST" style="display:inline">
        @csrf
        <button type="submit" class="nav-logout">Keluar</button>
      </form>
    </div>
  @else
    <div style="display:flex;gap:0.8rem;align-items:center">
      <a href="{{ route('login') }}" class="nav-links" style="color:var(--text-muted);font-size:0.85rem;letter-spacing:1px;text-transform:uppercase">Login</a>
      <a href="{{ route('register') }}" class="nav-btn">Daftar</a>
    </div>
  @endauth
</nav>

<div class="page-content">
  @if(session('success'))
    <div style="max-width:1280px;margin:1rem auto;padding:0 3rem">
      <div class="alert alert-success">{{ session('success') }}</div>
    </div>
  @endif
  @if(session('error'))
    <div style="max-width:1100px;margin:1rem auto;padding:0 3rem">
      <div class="alert alert-error">{{ session('error') }}</div>
    </div>
  @endif
  @if(session('info'))
    <div style="max-width:1100px;margin:1rem auto;padding:0 3rem">
      <div class="alert alert-info">{{ session('info') }}</div>
    </div>
  @endif

  @yield('content')
</div>

@php
  $footerAddress   = \App\Models\SiteSetting::getValue('footer_address', 'Jl. Jenderal Sudirman No. 123, Bandung, Jawa Barat 40111');
  $footerPhone     = \App\Models\SiteSetting::getValue('footer_phone', '+62 22 1234 5678');
  $footerEmail     = \App\Models\SiteSetting::getValue('footer_email', 'info@hotelnusantara.com');
  $footerCopyright = \App\Models\SiteSetting::getValue('footer_copyright', '© ' . date('Y') . ' Hotel Nusantara. All rights reserved.');
  $socialIg        = \App\Models\SiteSetting::getValue('social_instagram', '#');
  $socialFb        = \App\Models\SiteSetting::getValue('social_facebook', '#');
  $socialTw        = \App\Models\SiteSetting::getValue('social_twitter', '#');
  $socialTiktok    = \App\Models\SiteSetting::getValue('social_tiktok', '');
  $socialYoutube   = \App\Models\SiteSetting::getValue('social_youtube', '');
@endphp
<footer class="main-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <h3 class="footer-logo">Nusantara</h3>
      <div class="footer-tagline">Luxury Collection</div>
      <p class="footer-desc">Pengalaman menginap mewah di jantung kota dengan sentuhan budaya Nusantara yang autentik.</p>
    </div>
    <div class="footer-links-group">
      <h4>Tautan Cepat</h4>
      <ul>
        <li><a href="{{ route('home') }}">Beranda</a></li>
        <li><a href="{{ route('home') }}#rooms">Kamar & Suite</a></li>
        <li><a href="{{ route('home') }}#amenities">Fasilitas</a></li>
        <li><a href="{{ route('home') }}#about">Tentang Kami</a></li>
      </ul>
    </div>
    <div class="footer-contact">
      <h4>Hubungi Kami</h4>
      <p>{!! nl2br(e($footerAddress)) !!}</p>
      <p style="margin-top:0.8rem">Telepon: {{ $footerPhone }}<br>Email: {{ $footerEmail }}</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>{{ $footerCopyright }}</p>
    <div class="footer-socials">
      @if($socialIg)<a href="{{ $socialIg }}">Instagram</a>@endif
      @if($socialFb)<a href="{{ $socialFb }}">Facebook</a>@endif
      @if($socialTw)<a href="{{ $socialTw }}">Twitter</a>@endif
      @if($socialTiktok)<a href="{{ $socialTiktok }}">TikTok</a>@endif
      @if($socialYoutube)<a href="{{ $socialYoutube }}">YouTube</a>@endif
    </div>
  </div>
</footer>

@yield('scripts')
</body>
</html>

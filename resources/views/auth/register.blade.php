@extends('layouts.app')
@section('title', 'Daftar - Hotel Nusantara')
@section('content')
<section style="display:flex;align-items:center;justify-content:center;min-height:80vh">
  <div class="card" style="width:100%;max-width:500px">
    <div class="card-header" style="text-align:center">
      <h2 style="font-size:2rem;color:var(--cream)">Buat Akun Baru</h2>
      <p style="color:var(--text-muted);font-size:0.85rem;margin-top:0.5rem">Bergabung untuk menikmati layanan terbaik kami</p>
    </div>
    @if($errors->any())
      <div class="alert alert-error">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
      </div>
    @endif
    <form method="POST" action="{{ route('register') }}">
      @csrf
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
          <label>No. HP</label>
          <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>
        <div class="form-group">
          <label>Konfirmasi Password</label>
          <input type="password" name="password_confirmation" required>
        </div>
      </div>
      <button type="submit" class="btn-primary" style="width:100%">Daftar</button>
      <p style="text-align:center;margin-top:1.5rem;color:var(--text-muted);font-size:0.85rem">
        Sudah punya akun? <a href="{{ route('login') }}" style="color:var(--gold)">Masuk</a>
      </p>
    </form>
  </div>
</section>
@endsection

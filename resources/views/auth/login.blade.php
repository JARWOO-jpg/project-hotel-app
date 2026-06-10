@extends('layouts.app')
@section('title', 'Login - Hotel Nusantara')
@section('content')
<section style="display:flex;align-items:center;justify-content:center;min-height:80vh">
  <div class="card" style="width:100%;max-width:450px">
    <div class="card-header" style="text-align:center">
      <h2 style="font-size:2rem;color:var(--cream)">Selamat Datang</h2>
      <p style="color:var(--text-muted);font-size:0.85rem;margin-top:0.5rem">Masuk ke akun Anda</p>
    </div>
    @if($errors->any())
      <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <div style="margin-bottom:1.5rem">
        <label style="display:flex;align-items:center;gap:0.5rem;color:var(--text-muted);font-size:0.8rem;cursor:pointer">
          <input type="checkbox" name="remember" style="width:auto"> Ingat saya
        </label>
      </div>
      <button type="submit" class="btn-primary" style="width:100%">Masuk</button>
      <p style="text-align:center;margin-top:1.5rem;color:var(--text-muted);font-size:0.85rem">
        Belum punya akun? <a href="{{ route('register') }}" style="color:var(--gold)">Daftar sekarang</a>
      </p>
    </form>
  </div>
</section>
@endsection

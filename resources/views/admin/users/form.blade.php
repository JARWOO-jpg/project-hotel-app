@extends('layouts.app')
@section('title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('content')
<section>
  <div class="section-inner" style="max-width:600px">
    <div class="card">
      <div class="card-header"><h2 style="color:var(--cream);font-size:1.5rem">{{ isset($user) ? 'Edit '.$user->name : 'Tambah Pengguna' }}</h2></div>
      @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
      <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
        @csrf
        @if(isset($user)) @method('PUT') @endif
        <div class="form-group"><label>Nama</label><input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required></div>
        <div class="form-row">
          <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required></div>
          <div class="form-group"><label>HP</label><input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '' }}</label><input type="password" name="password" {{ isset($user)?'':'required' }}></div>
          <div class="form-group"><label>Role</label><select name="role" required>
            @foreach(['guest'=>'Guest','receptionist'=>'Resepsionis','admin'=>'Admin'] as $v=>$l)
              <option value="{{ $v }}" {{ old('role', $user->role ?? '')==$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select></div>
        </div>
        <div style="display:flex;gap:1rem">
          <button type="submit" class="btn-primary">{{ isset($user) ? 'Update' : 'Simpan' }}</button>
          <a href="{{ route('admin.users') }}" class="btn-outline">Batal</a>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection

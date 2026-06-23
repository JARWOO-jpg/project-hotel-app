@extends('layouts.app')
@section('title', isset($facility) ? 'Edit Fasilitas' : 'Tambah Fasilitas')
@section('content')
<section>
  <div class="section-inner" style="max-width:800px">
    <div style="margin-bottom:2rem">
      <a href="{{ route('admin.facilities') }}" class="btn-outline btn-sm">← Kembali</a>
    </div>
    <div class="card">
      <div class="card-header">
        <h3 style="color:var(--cream);font-size:1.5rem">{{ isset($facility) ? 'Edit Fasilitas' : 'Tambah Fasilitas Baru' }}</h3>
      </div>
      <form method="POST" action="{{ isset($facility) ? route('admin.facilities.update', $facility->id) : route('admin.facilities.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($facility)) @method('PUT') @endif
        
        <div class="form-group">
          <label>Nama Fasilitas</label>
          <input type="text" name="name" value="{{ old('name', $facility->name ?? '') }}" required>
          @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label>Ikon (Emoji atau Teks Pendek)</label>
          <input type="text" name="icon" value="{{ old('icon', $facility->icon ?? '') }}" placeholder="Contoh: 🏊 atau 🍽️">
          @error('icon')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="description" rows="4">{{ old('description', $facility->description ?? '') }}</textarea>
          @error('description')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label>Foto/Gambar Fasilitas (Opsional)</label>
          @if(isset($facility) && $facility->image)
            <div style="margin-bottom:1rem">
              <img src="{{ asset('storage/' . $facility->image) }}" style="width:200px;border-radius:4px">
            </div>
          @endif
          <input type="file" name="image" accept="image/*" style="padding:0.5rem 0">
          <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.3rem">Format: JPG, PNG. Maksimal 2MB.</div>
          @error('image')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div style="margin-top:2rem">
          <button type="submit" class="btn-primary">Simpan Fasilitas</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection

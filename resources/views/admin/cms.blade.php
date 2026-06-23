@extends('layouts.app')
@section('title', 'Manajemen Konten Website (CMS)')
@section('extra-css')
<style>
.facility-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.5rem;margin-top:1.5rem}
.facility-item{background:var(--dark3);border:1px solid rgba(201,169,110,0.1);overflow:hidden;transition:all 0.3s;position:relative}
.facility-item:hover{border-color:rgba(201,169,110,0.4);transform:translateY(-3px);box-shadow:0 8px 25px rgba(0,0,0,0.3)}
.facility-img{width:100%;height:180px;object-fit:cover;display:block;background:var(--dark2)}
.facility-img-placeholder{width:100%;height:180px;display:flex;align-items:center;justify-content:center;background:var(--dark2);font-size:3rem}
.facility-info{padding:1.2rem}
.facility-info h4{font-family:'Cormorant Garamond',serif;font-size:1.2rem;color:var(--cream);margin-bottom:0.3rem}
.facility-info p{font-size:0.8rem;color:var(--text-muted);line-height:1.6}
.facility-actions{display:flex;gap:0.5rem;margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(201,169,110,0.1)}
.facility-actions a,.facility-actions button{flex:1;text-align:center}
.add-facility-card{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:300px;border:2px dashed rgba(201,169,110,0.25);cursor:pointer;transition:all 0.3s;text-decoration:none;background:transparent}
.add-facility-card:hover{border-color:var(--gold);background:rgba(201,169,110,0.03)}
.add-facility-card .plus{font-size:3rem;color:var(--gold);margin-bottom:0.5rem;font-weight:200}
.add-facility-card span{font-size:0.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--text-muted)}
</style>
@endsection
@section('content')
<section>
  <div class="section-inner">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem">
      <div>
        <h1 style="color:var(--cream);font-size:2rem;margin-bottom:0.3rem">Manajemen Konten Website</h1>
        <p style="color:var(--text-muted);font-size:0.85rem">Kelola tampilan visual, fasilitas, informasi kontak, media sosial, dan banner promo hotel Anda.</p>
      </div>
      <a href="{{ route('admin.dashboard') }}" class="btn-outline btn-sm">← Dashboard</a>
    </div>

    <form method="POST" action="{{ route('admin.cms.update') }}" enctype="multipart/form-data">
      @csrf

      @foreach($groups as $groupKey => $groupInfo)
      <div class="card" style="margin-bottom:2rem">
        <div class="card-header" style="display:flex;align-items:center;gap:0.8rem">
          <span style="font-size:1.5rem">{{ $groupInfo['icon'] }}</span>
          <div>
            <h2 style="color:var(--cream);font-size:1.3rem;margin-bottom:0.1rem">{{ $groupInfo['title'] }}</h2>
            <p style="color:var(--text-muted);font-size:0.75rem">{{ $groupInfo['desc'] }}</p>
          </div>
        </div>

        {{-- CMS Settings for this group --}}
        @if(isset($settings[$groupKey]) && $settings[$groupKey]->count())
          @foreach($settings[$groupKey] as $setting)
          <div class="form-group" style="margin-bottom:1.2rem">
            <label>{{ $setting->label }}</label>

            @if($setting->type === 'image')
              @if($setting->value)
                <div style="margin-bottom:0.8rem">
                  <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}" style="max-height:120px;border-radius:4px;border:1px solid var(--gold)">
                  <p style="font-size:0.7rem;color:var(--text-muted);margin-top:0.3rem">File saat ini: {{ basename($setting->value) }}</p>
                </div>
              @else
                <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.5rem;font-style:italic">Belum ada gambar diunggah.</p>
              @endif
              <input type="file" name="setting_{{ $setting->key }}" accept="image/*"
                style="padding:0.5rem;background:var(--dark3);border:1px solid rgba(201,169,110,0.2);color:var(--text);width:100%">

            @elseif($setting->type === 'textarea')
              <textarea name="setting_{{ $setting->key }}" rows="3">{{ $setting->value }}</textarea>

            @elseif($setting->type === 'url')
              <input type="url" name="setting_{{ $setting->key }}" value="{{ $setting->value }}" placeholder="https://...">

            @else
              <input type="text" name="setting_{{ $setting->key }}" value="{{ $setting->value }}">
            @endif
          </div>
          @endforeach
        @endif

        {{-- Facility Management (Standard Form Style) --}}
        @if($groupKey === 'facility' && isset($facilities))
          <div style="margin-top: 1.5rem;">
            @foreach($facilities as $index => $f)
              <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(201,169,110,0.15);">
                <label style="color:var(--gold); font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 1.2rem; display: block;">Fasilitas {{ $index + 1 }}</label>
                
                <div class="form-group" style="margin-bottom:1.2rem">
                  <label>JUDUL FASILITAS {{ $index + 1 }}</label>
                  <input type="text" name="facility[{{ $f->id }}][name]" value="{{ $f->name }}">
                </div>

                <div class="form-group" style="margin-bottom:1.2rem">
                  <label>DESKRIPSI FASILITAS {{ $index + 1 }}</label>
                  <textarea name="facility[{{ $f->id }}][description]" rows="3">{{ $f->description }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom:1.2rem">
                  <label>GAMBAR FASILITAS {{ $index + 1 }}</label>
                  @if($f->image)
                    <div style="margin-bottom:0.8rem">
                      <img src="{{ asset('storage/' . $f->image) }}" alt="{{ $f->name }}" style="max-height:120px;border-radius:4px;border:1px solid var(--gold)">
                      <p style="font-size:0.7rem;color:var(--text-muted);margin-top:0.3rem">File saat ini: {{ basename($f->image) }}</p>
                    </div>
                  @else
                    <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.5rem;font-style:italic">Belum ada gambar diunggah.</p>
                  @endif
                  <input type="file" name="facility[{{ $f->id }}][image]" accept="image/*"
                    style="padding:0.5rem;background:var(--dark3);border:1px solid rgba(201,169,110,0.2);color:var(--text);width:100%">
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
      @endforeach

      <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1rem">
        <button type="submit" class="btn-primary" style="padding:1rem 3rem">
          💾 Simpan Semua Perubahan
        </button>
      </div>
    </form>

  </div>
</section>
@endsection

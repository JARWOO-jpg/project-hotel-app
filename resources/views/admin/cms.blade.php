@extends('layouts.app')
@section('title', 'Manajemen Konten Website (CMS)')
@section('content')
<section>
  <div class="section-inner">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem">
      <div>
        <h1 style="color:var(--cream);font-size:2rem;margin-bottom:0.3rem">Manajemen Konten Website</h1>
        <p style="color:var(--text-muted);font-size:0.85rem">Kelola tampilan visual, informasi kontak, media sosial, dan banner promo hotel Anda.</p>
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

        @if(isset($settings[$groupKey]) && $settings[$groupKey]->count())
          @foreach($settings[$groupKey] as $setting)
          <div class="form-group" style="margin-bottom:1.2rem">
            <label>{{ $setting->label }}</label>

            @if($setting->type === 'image')
              {{-- Preview gambar jika sudah ada --}}
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
        @else
          <p style="color:var(--text-muted);font-size:0.85rem;font-style:italic;padding:1rem 0">Belum ada pengaturan untuk grup ini. Jalankan seeder terlebih dahulu.</p>
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

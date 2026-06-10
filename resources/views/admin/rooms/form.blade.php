@extends('layouts.app')
@section('title', isset($room) ? 'Edit Kamar' : 'Tambah Kamar')
@section('content')
<section>
  <div class="section-inner" style="max-width:650px">
    <div class="card">
      <div class="card-header"><h2 style="color:var(--cream);font-size:1.5rem">{{ isset($room) ? 'Edit Kamar '.$room->room_number : 'Tambah Kamar Baru' }}</h2></div>
      @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
      <form method="POST" action="{{ isset($room) ? route('admin.rooms.update', $room->id) : route('admin.rooms.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($room)) @method('PUT') @endif
        <div class="form-row">
          <div class="form-group"><label>Nomor Kamar</label><input type="text" name="room_number" value="{{ old('room_number', $room->room_number ?? '') }}" required></div>
          <div class="form-group"><label>Tipe</label><select name="type" required>
            @foreach(['superior'=>'Superior Room','deluxe'=>'Deluxe Room','junior_suite'=>'Junior Suite','presidential_suite'=>'Presidential Suite'] as $v=>$l)
              <option value="{{ $v }}" {{ old('type', $room->type ?? '')==$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Harga per Malam (Rp)</label><input type="number" name="price_per_night" value="{{ old('price_per_night', $room->price_per_night ?? '') }}" required min="0"></div>
          <div class="form-group"><label>Kapasitas</label><input type="number" name="capacity" value="{{ old('capacity', $room->capacity ?? 2) }}" required min="1"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Status</label><select name="status" required>
            <option value="available" {{ old('status', $room->status ?? '')=='available'?'selected':'' }}>Available</option>
            @if(isset($room))<option value="occupied" {{ old('status', $room->status ?? '')=='occupied'?'selected':'' }}>Occupied</option>@endif
            <option value="maintenance" {{ old('status', $room->status ?? '')=='maintenance'?'selected':'' }}>Maintenance</option>
          </select></div>
          <div class="form-group"><label>Foto Kamar (Opsional)</label>
            <input type="file" name="image" accept="image/*" style="padding:0.5rem;background:var(--dark3);border:1px solid rgba(201,169,110,0.2);color:var(--text);width:100%">
            @if(isset($room) && $room->image)
              <div style="margin-top:0.5rem">
                <img src="{{ asset('storage/' . $room->image) }}" alt="Preview" style="max-height:80px;border-radius:4px;border:1px solid var(--gold)">
              </div>
            @endif
          </div>
        </div>
        <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="3">{{ old('description', $room->description ?? '') }}</textarea></div>
        <div style="display:flex;gap:1rem">
          <button type="submit" class="btn-primary">{{ isset($room) ? 'Update' : 'Simpan' }}</button>
          <a href="{{ route('admin.rooms') }}" class="btn-outline">Batal</a>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection

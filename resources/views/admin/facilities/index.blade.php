@extends('layouts.app')
@section('title', 'Kelola Fasilitas - Hotel Nusantara')
@section('content')
<section>
  <div class="section-inner">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem">
      <div><div class="section-tag">Master Data</div><h2 class="section-title" style="font-size:2rem">Kelola <em>Fasilitas</em></h2></div>
      <a href="{{ route('admin.facilities.create') }}" class="btn-primary btn-sm">+ Tambah Fasilitas</a>
    </div>
    <div class="admin-nav" style="display:flex;gap:0.8rem;margin-bottom:2rem;flex-wrap:wrap">
      <a href="{{ route('admin.dashboard') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">Dashboard</a>
      <a href="{{ route('admin.rooms') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">Kamar</a>
      <a href="{{ route('admin.facilities') }}" style="padding:0.6rem 1.2rem;border:1px solid var(--gold);color:var(--gold);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase;background:rgba(201,169,110,0.05)">Fasilitas</a>
      <a href="{{ route('admin.bookings') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">Booking</a>
      <a href="{{ route('admin.users') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">Pengguna</a>
      <a href="{{ route('admin.reports') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">Laporan</a>
      <a href="{{ route('admin.cms') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">CMS</a>
    </div>
    <div class="table-wrap"><table>
      <thead><tr><th>Ikon/Gambar</th><th>Nama Fasilitas</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($facilities as $f)
        <tr>
          <td>
            @if($f->image)
              <img src="{{ asset('storage/' . $f->image) }}" alt="{{ $f->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
            @else
              <span style="font-size: 1.5rem">{{ $f->icon ?? '🌟' }}</span>
            @endif
          </td>
          <td style="color:var(--gold);font-weight:500">{{ $f->name }}</td>
          <td>{{ Str::limit($f->description, 50) }}</td>
          <td style="display:flex;gap:0.5rem">
            <a href="{{ route('admin.facilities.edit', $f->id) }}" class="btn-outline btn-sm">Edit</a>
            <form method="POST" action="{{ route('admin.facilities.delete', $f->id) }}" onsubmit="return confirm('Hapus fasilitas ini?')">@csrf @method('DELETE')
              <button class="btn-outline btn-sm" style="border-color:var(--danger);color:var(--danger)">Hapus</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table></div>
  </div>
</section>
@endsection

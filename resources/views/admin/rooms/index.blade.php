@extends('layouts.app')
@section('title', 'Kelola Kamar - Hotel Nusantara')
@section('content')
<section>
  <div class="section-inner">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem">
      <div><div class="section-tag">Master Data</div><h2 class="section-title" style="font-size:2rem">Kelola <em>Kamar</em></h2></div>
      <a href="{{ route('admin.rooms.create') }}" class="btn-primary btn-sm">+ Tambah Kamar</a>
    </div>
    <div class="admin-nav" style="display:flex;gap:0.8rem;margin-bottom:2rem;flex-wrap:wrap">
      <a href="{{ route('admin.dashboard') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">Dashboard</a>
      <a href="{{ route('admin.rooms') }}" style="padding:0.6rem 1.2rem;border:1px solid var(--gold);color:var(--gold);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase;background:rgba(201,169,110,0.05)">Kamar</a>
      <a href="{{ route('admin.bookings') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">Booking</a>
      <a href="{{ route('admin.users') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">Pengguna</a>
      <a href="{{ route('admin.reports') }}" style="padding:0.6rem 1.2rem;border:1px solid rgba(201,169,110,0.2);color:var(--text-muted);font-size:0.8rem;letter-spacing:1px;text-transform:uppercase">Laporan</a>
    </div>
    <div class="table-wrap"><table>
      <thead><tr><th>No. Kamar</th><th>Tipe</th><th>Harga/Malam</th><th>Kapasitas</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($rooms as $r)
        <tr>
          <td style="color:var(--gold);font-weight:500">{{ $r->room_number }}</td>
          <td>{{ $r->getTypeLabel() }}</td>
          <td>Rp {{ number_format($r->price_per_night,0,',','.') }}</td>
          <td>{{ $r->capacity }} tamu</td>
          <td><span class="badge {{ $r->status==='available'?'badge-success':($r->status==='occupied'?'badge-warning':'badge-danger') }}">{{ ucfirst($r->status) }}</span></td>
          <td style="display:flex;gap:0.5rem">
            <a href="{{ route('admin.rooms.edit', $r->id) }}" class="btn-outline btn-sm">Edit</a>
            <form method="POST" action="{{ route('admin.rooms.delete', $r->id) }}" onsubmit="return confirm('Hapus kamar ini?')">@csrf @method('DELETE')
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

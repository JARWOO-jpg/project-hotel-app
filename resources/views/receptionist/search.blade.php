@extends('layouts.app')
@section('title', 'Hasil Pencarian - Hotel Nusantara')
@section('content')
<section>
  <div class="section-inner">
    <div class="section-header" style="margin-bottom:2rem">
      <div class="section-tag">Pencarian</div>
      <h2 class="section-title" style="font-size:2rem">Hasil untuk "<em>{{ $search }}</em>"</h2>
    </div>
    <form action="{{ route('receptionist.search') }}" method="GET" style="display:flex;gap:1rem;margin-bottom:2rem">
      <input type="text" name="search" value="{{ $search }}" placeholder="Cari Nama/Kode Booking..." style="flex:1;background:var(--dark3);border:1px solid rgba(201,169,110,0.2);padding:0.75rem 1rem;color:var(--text);font-family:'DM Sans',sans-serif;outline:none">
      <button class="btn-primary btn-sm">Cari</button>
    </form>
    @if($bookings->isEmpty())
      <div class="card" style="text-align:center;padding:3rem"><p style="color:var(--text-muted)">Tidak ditemukan booking dengan kata kunci tersebut.</p></div>
    @else
      <div class="table-wrap">
        <table>
          <thead><tr><th>Kode</th><th>Tamu</th><th>Kamar</th><th>Check-In</th><th>Check-Out</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
            @foreach($bookings as $b)
            <tr>
              <td style="color:var(--gold)">{{ $b->booking_code }}</td>
              <td style="color:var(--cream)">{{ $b->user->name }}</td>
              <td>{{ $b->room ? $b->room->room_number : '-' }}</td>
              <td>{{ $b->check_in_date->format('d/m/Y') }}</td>
              <td>{{ $b->check_out_date->format('d/m/Y') }}</td>
              <td><span class="badge {{ $b->getStatusBadgeClass() }}">{{ $b->getStatusLabel() }}</span></td>
              <td style="display:flex;gap:0.5rem">
                @if($b->status === 'confirmed')
                  <a href="{{ route('receptionist.check-in', $b->id) }}" class="btn-primary btn-sm">Check-In</a>
                @elseif($b->status === 'checked_in')
                  <a href="{{ route('receptionist.check-out', $b->id) }}" class="btn-primary btn-sm">Check-Out</a>
                @endif
                <a href="{{ route('booking.status', $b->id) }}" class="btn-outline btn-sm">Detail</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
    <div style="margin-top:2rem"><a href="{{ route('receptionist.dashboard') }}" class="btn-outline btn-sm">← Kembali ke Dashboard</a></div>
  </div>
</section>
@endsection

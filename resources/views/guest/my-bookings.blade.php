@extends('layouts.app')
@section('title', 'Booking Saya - Hotel Nusantara')
@section('content')
<section>
  <div class="section-inner">
    <div class="section-header">
      <div class="section-tag">Riwayat</div>
      <h2 class="section-title" style="font-size:2rem">Booking <em>Saya</em></h2>
    </div>
    @if($bookings->isEmpty())
      <div class="card" style="text-align:center;padding:4rem 2rem">
        <p style="color:var(--text-muted);font-size:1.1rem;margin-bottom:1.5rem">Anda belum memiliki booking.</p>
        <a href="{{ route('home') }}" class="btn-primary">Pesan Kamar Sekarang</a>
      </div>
    @else
      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Kode</th><th>Kamar</th><th>Check-In</th><th>Check-Out</th><th>Total</th><th>Status</th><th>Aksi</th>
          </tr></thead>
          <tbody>
            @foreach($bookings as $b)
            <tr>
              <td style="color:var(--gold);font-weight:500">{{ $b->booking_code }}</td>
              <td>{{ $b->room ? $b->room->getTypeLabel() . ' - ' . $b->room->room_number : '-' }}</td>
              <td>{{ $b->check_in_date->format('d/m/Y') }}</td>
              <td>{{ $b->check_out_date->format('d/m/Y') }}</td>
              <td>Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
              <td><span class="badge {{ $b->getStatusBadgeClass() }}">{{ $b->getStatusLabel() }}</span></td>
              <td>
                <a href="{{ route('booking.status', $b->id) }}" class="btn-outline btn-sm">Detail</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</section>
@endsection

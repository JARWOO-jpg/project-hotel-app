@extends('layouts.app')
@section('title', 'Kelola Booking - Hotel Nusantara')
@section('content')
<section>
  <div class="section-inner">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem">
      <div><div class="section-tag">Master Data</div><h2 class="section-title" style="font-size:2rem">Kelola <em>Booking</em></h2></div>
    </div>
    <form method="GET" style="display:flex;gap:1rem;margin-bottom:2rem;flex-wrap:wrap">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode/nama..." style="flex:1;min-width:200px;background:var(--dark3);border:1px solid rgba(201,169,110,0.2);padding:0.6rem 1rem;color:var(--text);font-family:'DM Sans',sans-serif;outline:none">
      <select name="status" style="background:var(--dark3);border:1px solid rgba(201,169,110,0.2);padding:0.6rem 1rem;color:var(--text);font-family:'DM Sans',sans-serif;outline:none">
        <option value="">Semua Status</option>
        @foreach(['pending','confirmed','checked_in','checked_out','cancelled','waiting_list'] as $s)
          <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
        @endforeach
      </select>
      <button class="btn-primary btn-sm">Filter</button>
    </form>
    <div class="table-wrap"><table>
      <thead><tr><th>Kode</th><th>Tamu</th><th>Kamar</th><th>Check-In</th><th>Check-Out</th><th>Total</th><th>Dibayar</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($bookings as $b)
        <tr>
          <td style="color:var(--gold)">{{ $b->booking_code }}</td>
          <td style="color:var(--cream)">{{ $b->user->name }}</td>
          <td>{{ $b->room ? $b->room->room_number : '-' }}</td>
          <td>{{ $b->check_in_date->format('d/m/Y') }}</td>
          <td>{{ $b->check_out_date->format('d/m/Y') }}</td>
          <td>Rp {{ number_format($b->total_price,0,',','.') }}</td>
          <td>Rp {{ number_format($b->paid_amount,0,',','.') }}</td>
          <td><span class="badge {{ $b->getStatusBadgeClass() }}">{{ $b->getStatusLabel() }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table></div>
    <div class="pagination">{{ $bookings->withQueryString()->links('vendor.pagination.simple') }}</div>
  </div>
</section>
@endsection

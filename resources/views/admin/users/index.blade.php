@extends('layouts.app')
@section('title', 'Kelola Pengguna - Hotel Nusantara')
@section('content')
<section>
  <div class="section-inner">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem">
      <div><div class="section-tag">Master Data</div><h2 class="section-title" style="font-size:2rem">Kelola <em>Pengguna</em></h2></div>
      <a href="{{ route('admin.users.create') }}" class="btn-primary btn-sm">+ Tambah Pengguna</a>
    </div>
    <div class="table-wrap"><table>
      <thead><tr><th>Nama</th><th>Email</th><th>HP</th><th>Role</th><th>Terdaftar</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($users as $u)
        <tr>
          <td style="color:var(--cream)">{{ $u->name }}</td>
          <td>{{ $u->email }}</td>
          <td>{{ $u->phone ?? '-' }}</td>
          <td><span class="badge {{ $u->role==='admin'?'badge-danger':($u->role==='receptionist'?'badge-info':'badge-success') }}">{{ ucfirst($u->role) }}</span></td>
          <td>{{ $u->created_at->format('d/m/Y') }}</td>
          <td style="display:flex;gap:0.5rem">
            <a href="{{ route('admin.users.edit', $u->id) }}" class="btn-outline btn-sm">Edit</a>
            @if($u->id !== auth()->id())
            <form method="POST" action="{{ route('admin.users.delete', $u->id) }}" onsubmit="return confirm('Hapus pengguna ini?')">@csrf @method('DELETE')
              <button class="btn-outline btn-sm" style="border-color:var(--danger);color:var(--danger)">Hapus</button>
            </form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table></div>
    <div class="pagination">{{ $users->links('vendor.pagination.simple') }}</div>
  </div>
</section>
@endsection

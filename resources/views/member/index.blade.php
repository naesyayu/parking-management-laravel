@extends('app')

@section('content')
<h4>Data Member</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('member.create') }}" class="btn btn-primary mb-3 mt-4">
    + Tambah Member
</a>

<table class="table table-bordered mt-3">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Pemilik</th>
            <th>Level</th>
            <th>Berlaku</th>
            <th>Status</th>
            <th width="180">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($members as $m)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $m->pemilik->nama }}</td>
            <td>{{ $m->level->nama_level }}</td>
            <td>
                {{ $m->berlaku_mulai }} <br>
                s/d {{ $m->berlaku_hingga }}
            </td>
            <td>
                <span class="badge bg-{{ $m->status=='aktif'?'success':'secondary' }}">
                    {{ ucfirst($m->status) }}
                </span>
            </td>
            <td>
                <a href="{{ route('member.edit', $m->id_member) }}"
                   class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('member.destroy', $m->id_member) }}"
                      method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm"
                        onclick="return confirm('Hapus data ini?')">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

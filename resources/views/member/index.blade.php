@extends('app')

@section('content')
<div class="container-fluid">
    <h4>📝 Data Member</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- SEARCH & BUTTONS -->
    <div class="row mb-3 mt-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex gap-2">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="🔍 Cari nama pemilik..." 
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('member.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('member.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Member
            </a>
            <a href="{{ route('member.trash') }}" class="btn btn-secondary">
                <i class="fas fa-trash"></i> Backup Data
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Pemilik</th>
                            <th>Level</th>
                            <th>Berlaku</th>
                            <th>Status</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $m)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $m->pemilik->nama }}</strong></td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $m->level->nama_level }}
                                </span>
                            </td>
                            <td>
                                <small>
                                    {{ $m->berlaku_mulai }}<br>
                                    s/d {{ $m->berlaku_hingga }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $m->status=='aktif'?'success':'secondary' }}">
                                    {{ ucfirst($m->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('member.edit', $m->id_member) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('member.destroy', $m->id_member) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                @if(request('search'))
                                    Tidak ditemukan member dengan nama "{{ request('search') }}"
                                @else
                                    Belum ada data member
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
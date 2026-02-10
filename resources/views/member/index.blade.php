@extends('app')

@section('content')
<div class="container-fluid">
    <h4>💳 Data Member</h4>

    {{-- SEARCH & BUTTONS --}}
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
            <button onclick="confirmCreate('Member', '{{ route('member.create') }}')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Member
            </button>
            <a href="{{ route('member.trash') }}" class="btn btn-secondary">
                <i class="fas fa-trash"></i> Backup Data
            </a>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Pemilik</th>
                            <th>Level</th>
                            <th>Diskon</th>
                            <th>Berlaku</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->pemilik->nama }}</strong></td>
                            <td><span class="badge bg-info">{{ $item->level->nama_level }}</span></td>
                            <td>{{ $item->level->diskon }}%</td>
                            <td>{{ \Carbon\Carbon::parse($item->berlaku_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->berlaku_hingga)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $item->status=='aktif'?'success':'danger' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                <button onclick="confirmEdit('Member', '{{ $item->pemilik->nama }}', '{{ route('member.edit', $item) }}')" 
                                        class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('member.destroy', $item) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      id="delete-form-{{ $item->id_member }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete('Member', '{{ $item->pemilik->nama }}', 'delete-form-{{ $item->id_member }}')"
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
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
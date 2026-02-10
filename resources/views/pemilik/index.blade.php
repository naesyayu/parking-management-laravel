@extends('app')

@section('content')
<div class="container-fluid">
    <h4>👤 Data Pemilik</h4>

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
                    <a href="{{ route('pemilik.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="col-md-6 text-end">
            <button onclick="confirmCreate('Pemilik', '{{ route('pemilik.create') }}')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Pemilik
            </button>
            <a href="{{ route('pemilik.trash') }}" class="btn btn-secondary">
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
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pemilik as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->nama }}</strong></td>
                            <td>{{ $item->no_hp }}</td>
                            <td>{{ $item->alamat }}</td>
                            <td>
                                <button onclick="confirmEdit('Pemilik', '{{ $item->nama }}', '{{ route('pemilik.edit', $item) }}')" 
                                        class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('pemilik.destroy', $item) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      id="delete-form-{{ $item->id_pemilik }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete('Pemilik', '{{ $item->nama }}', 'delete-form-{{ $item->id_pemilik }}')"
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                @if(request('search'))
                                    Tidak ditemukan pemilik dengan nama "{{ request('search') }}"
                                @else
                                    Belum ada data pemilik
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
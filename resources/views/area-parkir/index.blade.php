@extends('app')

@section('content')
<div class="container-fluid">
    <h4>🅿️ Data Area Parkir</h4>

    <div class="text-end mb-3 mt-4">
        <button onclick="confirmCreate('Area Parkir', '{{ route('area-parkir.create') }}')" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Area
        </button>
        <a href="{{ route('area-parkir.trash') }}" class="btn btn-secondary">
            <i class="fas fa-trash"></i> Backup Data
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Kode Area</th>
                            <th>Nama Area</th>
                            <th>Lokasi</th>
                            <th>Foto</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($areas as $area)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-primary">{{ $area->kode_area }}</span></td>
                            <td><strong>{{ $area->nama_area }}</strong></td>
                            <td>{{ $area->lokasi ?? '-' }}</td>
                            <td>
                                @if($area->foto_lokasi)
                                    <img src="{{ asset('storage/'.$area->foto_lokasi) }}" 
                                         width="80" 
                                         class="rounded"
                                         alt="Foto Area">
                                @else
                                    <em class="text-muted">Tidak ada foto</em>
                                @endif
                            </td>
                            <td>
                                <button onclick="confirmEdit('Area Parkir', '{{ $area->nama_area }}', '{{ route('area-parkir.edit', $area->id_area) }}')" 
                                        class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('area-parkir.destroy', $area->id_area) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      id="delete-form-{{ $area->id_area }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete('Area Parkir', '{{ $area->nama_area }}', 'delete-form-{{ $area->id_area }}')"
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Belum ada data area parkir
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
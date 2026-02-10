@extends('app')

@section('content')
<div class="container-fluid">
    <h4>⏱️ Data Detail Parkir</h4>

    <div class="text-end mb-3 mt-4">
        <button onclick="confirmCreate('Detail Parkir', '{{ route('detail-parkir.create') }}')" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Detail Parkir
        </button>
        <a href="{{ route('detail-parkir.trash') }}" class="btn btn-secondary">
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
                            <th>Jam Minimum</th>
                            <th>Jam Maximum</th>
                            <th>Rentang</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->jam_min }}</strong></td>
                            <td><strong>{{ $item->jam_max }}</strong></td>
                            <td><span class="badge bg-info">{{ $item->jam_min }} - {{ $item->jam_max }} jam</span></td>
                            <td>
                                <button onclick="confirmEdit('Detail Parkir', '{{ $item->jam_min }}-{{ $item->jam_max }} jam', '{{ route('detail-parkir.edit', $item) }}')" 
                                        class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('detail-parkir.destroy', $item) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      id="delete-form-{{ $item->id_tarif_detail }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete('Detail Parkir', '{{ $item->jam_min }}-{{ $item->jam_max }} jam', 'delete-form-{{ $item->id_tarif_detail }}')"
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
                                Belum ada data detail parkir
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
@extends('app')

@section('content')
<div class="container-fluid">
    <h4>🗑️ Backup Data Area Parkir</h4>

    <div class="mb-3 mt-4">
        <a href="{{ route('area-parkir.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali
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
                            <th>Dihapus Pada</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($areas as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-primary">{{ $item->kode_area }}</span></td>
                            <td><strong>{{ $item->nama_area }}</strong></td>
                            <td>{{ $item->lokasi ?? '-' }}</td>
                            <td>
                                @if($item->foto_lokasi)
                                    <img src="{{ asset('storage/'.$item->foto_lokasi) }}" width="80" class="rounded">
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('area-parkir.restore', $item->id_area) }}" 
                                      method="POST"
                                      id="restore-form-{{ $item->id_area }}">
                                    @csrf
                                    <button type="button"
                                            onclick="confirmRestore('Area Parkir', '{{ $item->nama_area }}', 'restore-form-{{ $item->id_area }}')"
                                            class="btn btn-success btn-sm">
                                        <i class="fas fa-undo"></i> Pulihkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                Tidak ada data yang dihapus
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
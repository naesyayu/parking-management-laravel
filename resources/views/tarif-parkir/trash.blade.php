@extends('app')

@section('content')
<div class="container-fluid">
    <h4>🗑️ Backup Data Tarif Parkir</h4>

    <div class="mb-3 mt-4">
        <a href="{{ route('tarif-parkir.index') }}" class="btn btn-primary">
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
                            <th>Tipe Kendaraan</th>
                            <th>Durasi</th>
                            <th>Tarif</th>
                            <th>Dihapus Pada</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tarifParkir as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->tipeKendaraan->tipe_kendaraan }}</strong></td>
                            <td>{{ $item->detailParkir->jam_min }} - {{ $item->detailParkir->jam_max }} jam</td>
                            <td class="fw-bold text-success">Rp {{ number_format($item->tarif, 0, ',', '.') }}</td>
                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('tarif-parkir.restore', $item->id_tarif) }}" 
                                      method="POST"
                                      id="restore-form-{{ $item->id_tarif }}">
                                    @csrf
                                    <button type="button"
                                            onclick="confirmRestore('Tarif Parkir', '{{ $item->tipeKendaraan->tipe_kendaraan }} - Rp {{ number_format($item->tarif, 0, ',', '.') }}', 'restore-form-{{ $item->id_tarif }}')"
                                            class="btn btn-success btn-sm">
                                        <i class="fas fa-undo"></i> Pulihkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
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
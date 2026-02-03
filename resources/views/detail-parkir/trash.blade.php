@extends('app')

@section('content')
    <h4 class="mb-3">Backup Data Detail Durasi Parkir</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <a href="{{ route('detail-parkir.index') }}" class="btn btn-primary mb-3">
        ← Kembali
    </a>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Jam Minimal</th>
                <th>Jam Maksimal</th>
                <th>Dihapus Pada</th>
                <th width="120">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->jam_min }}</td>
                    <td>{{ $detail->jam_max }}</td>
                    <td>{{ $detail->deleted_at }}</td>
                    <td>
                        <form action="{{ route('detail-parkir.restore', $detail->id_tarif_detail) }}"
                              method="POST">
                            @csrf
                            <button class="btn btn-sm btn-success">
                                Restore
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak Ada Data Detail Durasi Parkir yang Terhapus</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

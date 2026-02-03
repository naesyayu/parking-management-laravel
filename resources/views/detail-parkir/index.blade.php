@extends('app')

@section('content')
    <h4 class="mb-3">Detail Parkir</h4>

    <a href="{{ route('detail-parkir.create') }}" class="btn btn-primary mb-3">
        + Tambah Detail Parkir
    </a>

    <a href="{{ route('detail-parkir.trash') }}" class="btn btn-secondary mb-3">
        Backup Data Durasi Parkir
    </a>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Jam Minimal</th>
                <th>Jam Maksimal</th>
                <th width="180">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->jam_min }}</td>
                    <td>{{ $detail->jam_max }}</td>
                    <td>
                        <a href="{{ route('detail-parkir.edit', $detail->id_tarif_detail) }}"
                           class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('detail-parkir.destroy', $detail->id_tarif_detail) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

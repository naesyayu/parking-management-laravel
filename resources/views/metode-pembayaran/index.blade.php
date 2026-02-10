@extends('app')

@section('content')
<div class="container-fluid">
    <h4>💳 Data Metode Pembayaran</h4>

    <div class="text-end mb-3 mt-4">
        <button onclick="confirmCreate('Metode Pembayaran', '{{ route('metode-pembayaran.create') }}')" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Metode Pembayaran
        </button>
        <a href="{{ route('metode-pembayaran.trash') }}" class="btn btn-secondary">
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
                            <th>Metode Pembayaran</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($metodePembayaran as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->metode_bayar }}</strong></td>
                            <td>
                                <button onclick="confirmEdit('Metode Pembayaran', '{{ $item->metode_bayar }}', '{{ route('metode-pembayaran.edit', $item) }}')" 
                                        class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('metode-pembayaran.destroy', $item) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      id="delete-form-{{ $item->id_metode }}">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete('Metode Pembayaran', '{{ $item->metode_bayar }}', 'delete-form-{{ $item->id_metode }}')"
                                            class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Belum ada data metode pembayaran
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
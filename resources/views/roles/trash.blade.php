@extends('app')

@section('content')
<div class="container-fluid">
    <h4>🗑️ Backup Data Role User</h4>

    <div class="mb-3 mt-4">
        <a href="{{ route('roles.index') }}" class="btn btn-primary">
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
                            <th>Role User</th>
                            <th>Dihapus Pada</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->role_user }}</strong></td>
                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('roles.restore', $item->id_role) }}" 
                                      method="POST"
                                      id="restore-form-{{ $item->id_role }}">
                                    @csrf
                                    <button type="button"
                                            onclick="confirmRestore('Role User', '{{ $item->role_user }}', 'restore-form-{{ $item->id_role }}')"
                                            class="btn btn-success btn-sm">
                                        <i class="fas fa-undo"></i> Pulihkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
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
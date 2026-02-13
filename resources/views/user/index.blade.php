@extends('app')

@section('content')
<div class="container-fluid">
    <h4>👥 Data User</h4>

    <div class="text-end mb-3 mt-4">
        <button onclick="confirmCreate('User', '{{ route('user.create') }}')" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah User
        </button>
        <a href="{{ route('user.trash') }}" class="btn btn-secondary">
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
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th width="250">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="{{ $user->id_user === $currentUserId ? 'table-info' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $user->username }}</strong>
                                @if($user->id_user === $currentUserId)
                                    <span class="badge bg-primary ms-2">
                                        <i class="fas fa-user"></i> Anda
                                    </span>
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $user->role->role_user ?? '-' }}</span></td>
                            <td>
                                <span class="badge bg-{{ $user->status=='aktif'?'success':'secondary' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>
                                {{-- ========================================
                                     SELF PROTECTION - DISABLED BUTTONS
                                     ======================================== --}}
                                @if($user->id_user === $currentUserId)
                                    {{-- DISABLED EDIT BUTTON --}}
                                    <button type="button" 
                                            class="btn btn-secondary btn-sm" 
                                            disabled
                                            title="Anda tidak dapat mengedit data sendiri">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    {{-- DISABLED PASSWORD BUTTON --}}
                                    <button type="button" 
                                            class="btn btn-secondary btn-sm" 
                                            disabled
                                            title="Gunakan menu 'Ubah Password' untuk mengubah password Anda">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    
                                    {{-- DISABLED DELETE BUTTON --}}
                                    <button type="button" 
                                            class="btn btn-secondary btn-sm" 
                                            disabled
                                            title="Anda tidak dapat menghapus akun sendiri">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    {{-- HINT MESSAGE --}}
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle"></i> 
                                        Gunakan <a href="{{ route('password.change') }}">Ubah Password</a> untuk mengubah password Anda
                                    </small>
                                @else
                                    {{-- NORMAL EDIT BUTTON --}}
                                    <button onclick="confirmEdit('User', '{{ $user->username }}', '{{ route('user.edit', $user) }}')" 
                                            class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    {{-- NORMAL PASSWORD BUTTON --}}
                                    <button onclick="confirmEdit('Password User', '{{ $user->username }}', '{{ route('user.password.edit', $user) }}')" 
                                            class="btn btn-info btn-sm">
                                        <i class="fas fa-key"></i>
                                    </button>

                                    {{-- NORMAL DELETE FORM --}}
                                    <form action="{{ route('user.destroy', $user) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          id="delete-form-{{ $user->id_user }}">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                onclick="confirmDelete('User', '{{ $user->username }}', 'delete-form-{{ $user->id_user }}')"
                                                class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Belum ada data user
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
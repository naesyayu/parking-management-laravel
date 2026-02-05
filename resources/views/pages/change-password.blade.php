@extends('app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Ubah Password</h4>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Terjadi Kesalahan!</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                <strong>Password Lama</strong>
                            </label>
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" 
                                   name="current_password" 
                                   required>
                            @error('current_password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Masukkan password lama Anda untuk verifikasi.</small>
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                <strong>Password Baru</strong>
                            </label>
                            <input type="password" 
                                   class="form-control @error('new_password') is-invalid @enderror"
                                   id="new_password" 
                                   name="new_password" 
                                   required>
                            @error('new_password')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">
                                <strong>Konfirmasi Password Baru</strong>
                            </label>
                            <input type="password" 
                                   class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   required>
                            @error('new_password_confirmation')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Pastikan password baru sama dengan kolom di atas.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock"></i> Ubah Password
                            </button>
                            <a href="{{ route('dashboard.index') }}" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
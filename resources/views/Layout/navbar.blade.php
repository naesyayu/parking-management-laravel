<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.navbar-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-bottom: 3px solid rgba(255,255,255,0.3);
}

.navbar-custom .nav-link {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 500;
    padding: 0.75rem 1.25rem !important;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin: 0 0.25rem;
}

.navbar-custom .nav-link:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white !important;
    transform: translateY(-2px);
}

.navbar-custom .nav-link.active {
    background: rgba(255, 255, 255, 0.3);
    color: white !important;
    font-weight: 600;
}

.navbar-custom .dropdown-menu {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    margin-top: 0.5rem;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.navbar-custom .dropdown-item {
    padding: 0.75rem 1.5rem;
    transition: all 0.2s ease;
    font-weight: 500;
}

.navbar-custom .dropdown-item:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateX(5px);
}

.navbar-custom .dropdown-item i {
    width: 20px;
    text-align: center;
}

.navbar-custom .dropdown-header {
    font-weight: 700;
    color: #667eea;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 0.75rem 1.5rem 0.5rem;
}

.navbar-custom .dropdown-divider {
    margin: 0.5rem 0;
    border-top-color: rgba(102, 126, 234, 0.2);
}

.user-info-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    color: white;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-info-badge .badge {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.35em 0.65em;
    font-weight: 600;
}

.btn-logout {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.btn-logout:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-2px);
}
</style>

<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
    <div class="container-fluid">
        
        {{-- Toggle Button untuk Mobile --}}
        <button class="navbar-toggler border-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse ms-2" id="navbarMain">
            <ul class="nav navbar-nav me-auto mb-2 mb-lg-0">
                
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" 
                       href="{{ route('dashboard.index') }}">
                        <i class="fas fa-chart-line me-1"></i> Laporan Transaksi Hari Ini
                    </a>
                </li>
                
                @auth
                    @php
                        $user = Auth::user();
                        $role = $user->role;
                    @endphp
                    
                    {{-- Ubah Password (Semua Role) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('password.*') ? 'active' : '' }}" 
                            href="{{ route('password.change') }}" title="Ubah Password">
                            <i class="fa-solid fa-key me-1"></i> Ubah Password
                        </a>
                    </li>
                    
                    {{-- Transaksi Menu (Petugas) --}}
                    @if($role && ($role->hasPermission('transaksi')))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('parkir.masuk') ? 'active' : '' }}" 
                           href="{{ route('parkir.masuk') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> Parkir Masuk
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('parkir.keluar') ? 'active' : '' }}" 
                           href="{{ route('parkir.keluar') }}">
                            <i class="fas fa-sign-out-alt me-1"></i> Parkir Keluar
                        </a>
                    </li>
                    @endif
                    
                    {{-- CRUD Menu (Admin & Owner) --}}
                    @if($role && ($role->hasPermission('master_data')))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('user*') || request()->is('roles*') || request()->is('tipe-kendaraan*') || request()->is('data-kendaraan*') || request()->is('pemilik*') || request()->is('area-parkir*') || request()->is('area-kapasitas*') || request()->is('tarif-parkir*') || request()->is('member*') || request()->is('metode-pembayaran*') ? 'active' : '' }}" 
                           href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <i class="fas fa-database me-1"></i> CRUD
                        </a>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header"><i class="fas fa-users-cog me-2"></i> User Management</h6></li>
                            <li><a class="dropdown-item" href="{{ route('roles.index') }}"><i class="fas fa-user-tag me-2"></i> Role User</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.index') }}"><i class="fas fa-users me-2"></i> User</a></li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <li><h6 class="dropdown-header"><i class="fas fa-car me-2"></i> Data Kendaraan</h6></li>
                            <li><a class="dropdown-item" href="{{ route('tipe-kendaraan.index') }}"><i class="fas fa-list-alt me-2"></i> Tipe Kendaraan</a></li>
                            <li><a class="dropdown-item" href="{{ route('data-kendaraan.index') }}"><i class="fas fa-th-list me-2"></i> Data Kendaraan</a></li>
                            <li><a class="dropdown-item" href="{{ route('pemilik.index') }}"><i class="fas fa-user-circle me-2"></i> Pemilik</a></li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <li><h6 class="dropdown-header"><i class="fas fa-map-marked-alt me-2"></i> Area & Tarif</h6></li>
                            <li><a class="dropdown-item" href="{{ route('area-parkir.index') }}"><i class="fas fa-map-marker-alt me-2"></i> Area Parkir</a></li>
                            <li><a class="dropdown-item" href="{{ route('area-kapasitas.index') }}"><i class="fas fa-th me-2"></i> Kapasitas Area Parkir</a></li>
                            <li><a class="dropdown-item" href="{{ route('detail-parkir.index') }}"><i class="fa-solid fa-clock me-2"></i> Detail Durasi Parkir</a></li>
                            <li><a class="dropdown-item" href="{{ route('tarif-parkir.index') }}"><i class="fas fa-money-bill me-2"></i> Tarif Parkir</a></li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <li><h6 class="dropdown-header"><i class="fas fa-credit-card me-2"></i> Member & Pembayaran</h6></li>
                            <li><a class="dropdown-item" href="{{ route('member.index') }}"><i class="fas fa-id-card me-2"></i> Data Member</a></li>
                            <li><a class="dropdown-item" href="{{ route('metode-pembayaran.index') }}"><i class="fas fa-credit-card me-2"></i> Metode Pembayaran</a></li>
                        </ul>
                    </li>
                    @endif

                    {{-- View Data Master (Semua Role) --}}
                    @if($role && $role->hasPermission('table_master'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" 
                           href="#" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-file me-1"></i> View Data Master
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-list-alt me-2"></i> Master Data Parkir</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-th-list me-2"></i> Data Transaksi</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i> Data Member dan Kendaraan </a></li>
                            <li><hr class="dropdown-divider"></li>
                        </ul>
                    </li>
                    @endif
                    
                    {{-- Activity Log (Admin & Owner) --}}
                    @if($role && ($role->hasPermission('activity_log')))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('activity-log.*') ? 'active' : '' }}" 
                           href="{{ route('activity-log.index') }}">
                            <i class="fas fa-history me-1"></i> Activity Log
                        </a>
                    </li>
                    @endif

                    {{-- Laporan Riwayat Transaksi (Semua Role) --}}
                    @if($role && ($role->hasPermission('laporan')))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" 
                            href="{{ route('laporan.index') }}" title="Laporan Riwayat Transaksi">
                            <i class="fa-solid fa-layer-group me-1"></i> Laporan Riwayat Transaksi
                        </a>
                    </li>
                    @endif
                    
                    
                @endauth
            </ul>
            
            {{-- User Info & Logout --}}
            <div class="d-flex align-items-center gap-2">
                @auth
                    <div class="user-info-badge">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ Auth::user()->username }}</span>
                        <span class="badge">{{ Auth::user()->role->role_user ?? 'N/A' }}</span>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-logout">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-logout">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
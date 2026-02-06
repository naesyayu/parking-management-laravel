<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            text-align: center;
            color: white;
            padding: 40px 20px;
        }
        .error-icon {
            font-size: 120px;
            margin-bottom: 30px;
            animation: shake 0.8s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }
        .error-code {
            font-size: 120px;
            font-weight: 900;
            line-height: 1;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .error-message {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
        }
        .user-info-box {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 15px;
            margin: 30px auto;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        .btn-custom {
            background: white;
            color: #667eea;
            padding: 12px 35px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-ban"></i>
        </div>
        
        <div class="error-code">403</div>
        
        <h1 class="error-title">Akses Ditolak</h1>
        
        <p class="error-message">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>
        
        @auth
        <div class="user-info-box">
            <p class="mb-2">
                <i class="fas fa-user me-2"></i>
                <strong>Username:</strong> {{ Auth::user()->username }}
            </p>
            <p class="mb-0">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Role:</strong> {{ Auth::user()->role->role_user ?? 'N/A' }}
            </p>
        </div>
        @endauth
        
        <div class="mt-4">
            <a href="{{ route('dashboard.index') }}" class="btn-custom">
                <i class="fas fa-home me-2"></i> Kembali ke Dashboard
            </a>
            
            @auth
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-custom" style="background: rgba(255,255,255,0.3); color: white;">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
            @endauth
        </div>
    </div>
</body>
</html>
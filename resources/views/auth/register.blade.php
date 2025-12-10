<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Task Manager</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo-circle.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: radial-gradient(circle at 15% 20%, rgba(6,182,212,0.18), transparent 40%),
                        radial-gradient(circle at 80% 0%, rgba(124,58,237,0.22), transparent 45%),
                        #0b1020;
            font-family: 'Poppins', sans-serif;
            color: #e5e7eb;
        }
        .card {
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            overflow: hidden;
            background: #0f172a;
            box-shadow: 0 25px 50px rgba(0,0,0,0.45);
        }
        .card-header {
            background: linear-gradient(135deg, #06b6d4, #7c3aed);
            color: white;
            font-size: 1.15rem;
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(135deg, #06b6d4, #7c3aed);
            border: none;
            font-weight: 600;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(6, 182, 212, 0.3);
        }
        .btn-primary:hover {
            filter: brightness(1.05);
        }
        .form-control {
            border-radius: 10px;
            background: #111827;
            border: 1px solid rgba(255,255,255,0.08);
            color: #e5e7eb;
        }
        .form-control:focus {
            border-color: #06b6d4;
            box-shadow: 0 0 0 0.2rem rgba(6,182,212,0.2);
        }
        .text-danger {
            font-size: 0.875rem;
        }
        label,
        .form-check-label,
        .card-body,
        .card-footer {
            color: #e5e7eb;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-center p-4 fs-1">
                    <div class="fw-bold">Join Task Manager</div>
                    <div class="small opacity-75">Create your workspace</div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <button type="button" class="btn btn-secondary" id="toggle-password" aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                <button type="button" class="btn btn-secondary" id="toggle-password-confirm" aria-label="Show password confirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </div>
                        <div class="text-center">
                            <small>Already have an account? <a href="{{ route('login') }}">Login</a></small>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0 text-muted">Built by Dilliraj</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const pwd = document.getElementById('password');
    const pwdConfirm = document.getElementById('password_confirmation');
    const togglePwd = document.getElementById('toggle-password');
    const togglePwdConfirm = document.getElementById('toggle-password-confirm');

    function toggleVisibility(input, btn) {
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.innerHTML = isText ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        btn.setAttribute('aria-label', isText ? 'Show password' : 'Hide password');
    }

    togglePwd?.addEventListener('click', () => toggleVisibility(pwd, togglePwd));
    togglePwdConfirm?.addEventListener('click', () => toggleVisibility(pwdConfirm, togglePwdConfirm));
</script>
</body>
</html>


<?php
session_start();
include('koneksi.php');
$error = "";
$success = "";
if (isset($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = trim($_POST['password']);
    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong!";
    } else {
        $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username'") or die(mysqli_error($koneksi));
        if (mysqli_num_rows($query) == 1) {
            $admin = mysqli_fetch_assoc($query);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nama'] = $admin['nama'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: index.php');
                exit;
            } else {
                $error = "Password salah! Silakan coba lagi.";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Sistem Data Mahasiswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Outfit', sans-serif; background: #090d16; min-height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
        body::before, body::after { content: ''; position: fixed; border-radius: 50%; filter: blur(80px); opacity: 0.18; animation: float 8s ease-in-out infinite alternate; pointer-events: none; }
        body::before { width: 200px; height: 400px; background: radial-gradient(circle, #6366f1, #4f46e5); top: -150px; left: -150px; }
        body::after { width: 200px; height: 400px; background: radial-gradient(circle, #06b6d4, #0e7490); bottom: -100px; right: -100px; animation-delay: -4s; }
        @keyframes float { from { transform: translate(0,0) scale(1); } to { transform: translate(40px,30px) scale(1.08); } }
        .auth-card { width: 100%; max-width: 420px; margin: 20px; background: rgba(15,23,42,0.7); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 44px 40px; box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(99,102,241,0.1); position: relative; z-index: 1; animation: slideUp 0.5s ease-out; }
        @keyframes slideUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        .auth-logo { width:60px; height:60px; background: linear-gradient(135deg, #6366f1, #4f46e5); border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:28px; margin:0 auto 20px; box-shadow:0 8px 24px rgba(99,102,241,0.35); }
        .auth-title { text-align:center; font-size:22px; font-weight:700; color:#fff; margin-bottom:6px; }
        .auth-subtitle { text-align:center; font-size:14px; color:#64748b; margin-bottom:32px; }
        .form-group { margin-bottom:18px; }
        .form-label { display:block; font-size:13px; font-weight:500; color:#94a3b8; margin-bottom:8px; letter-spacing:0.3px; }
        .form-input { width:100%; background:rgba(15,23,42,0.8); border:1px solid rgba(255,255,255,0.1); border-radius:10px; color:#f1f5f9; padding:12px 16px; font-size:15px; outline:none; transition:all 0.2s ease; }
        .form-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.2); background:rgba(30,41,59,0.8); }
        .password-wrapper { position:relative; }
        .password-wrapper .form-input { padding-right:44px; }
        .toggle-pass { position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; font-size:18px; color:#475569; user-select:none; transition:color 0.2s; }
        .toggle-pass:hover { color:#818cf8; }
        .btn-primary { width:100%; background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color:#fff; border:none; padding:13px; font-size:15px; font-weight:600; border-radius:10px; cursor:pointer; transition:all 0.25s ease; box-shadow:0 4px 16px rgba(99,102,241,0.3); margin-top:8px; }
        .btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(99,102,241,0.45); background:linear-gradient(135deg, #818cf8 0%, #6366f1 100%); }
        .auth-divider { display:flex; align-items:center; gap:12px; margin:24px 0; }
        .auth-divider::before, .auth-divider::after { content:''; flex:1; height:1px; background:rgba(255,255,255,0.08); }
        .auth-divider span { font-size:12px; color:#475569; }
        .auth-link-row { text-align:center; font-size:14px; color:#64748b; }
        .auth-link-row a { color:#818cf8; font-weight:600; text-decoration:none; transition:color 0.2s; }
        .auth-link-row a:hover { color:#a5b4fc; }
        .alert { padding:12px 16px; border-radius:10px; font-size:13px; font-weight:500; margin-bottom:20px; animation:slideUp 0.3s ease-out; }
        .alert-error { background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#fca5a5; }
        .alert-success { background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.3); color:#86efac; }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-logo">🎓</div>
    <h1 class="auth-title">Selamat Datang</h1>
    <p class="auth-subtitle">Sistem Informasi Data Mahasiswa</p>
    <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form action="" method="post" onsubmit="return validateLogin();">
        <div class="form-group">
            <label class="form-label" for="username">Username Admin</label>
            <input class="form-input" type="text" id="username" name="username" placeholder="Masukkan username Anda" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" autocomplete="username">
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="password-wrapper">
                <input class="form-input" type="password" id="password" name="password" placeholder="Masukkan password Anda" autocomplete="current-password">
                <span class="toggle-pass" onclick="togglePassword('password', this)" title="Tampilkan/sembunyikan password">👁</span>
            </div>
        </div>
        <button type="submit" name="login" class="btn-primary">Masuk ke Dashboard</button>
    </form>
    <div class="auth-divider"><span>atau</span></div>
    <div class="auth-link-row">Belum punya akun admin? <a href="register.php">Daftar di sini</a></div>
</div>
<script>
function validateLogin() {
    var u = document.getElementById('username').value.trim();
    var p = document.getElementById('password').value.trim();
    if (!u) { alert('Username tidak boleh kosong!'); return false; }
    if (!p) { alert('Password tidak boleh kosong!'); return false; }
    return true;
}
function togglePassword(fieldId, icon) {
    var field = document.getElementById(fieldId);
    if (field.type === 'password') { field.type = 'text'; icon.textContent = '🙈'; }
    else { field.type = 'password'; icon.textContent = '👁'; }
}
</script>
</body>
</html>

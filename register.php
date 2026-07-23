<?php
session_start();
include('koneksi.php');

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: beranda.php');
    exit;
}

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

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = trim($_POST['password']);
    $konfirm = trim($_POST['konfirmasi']);

    // Validation
    if (empty($nama) || empty($username) || empty($password) || empty($konfirm)) {
        $error = "Semua kolom wajib diisi!";
    } elseif (strlen($nama) < 3) {
        $error = "Nama harus minimal 3 karakter!";
    } elseif (strlen($username) < 5) {
        $error = "Username harus minimal 5 karakter!";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username hanya boleh berisi huruf, angka, dan garis bawah (_)!";
    } elseif (strlen($password) < 6) {
        $error = "Password harus minimal 6 karakter!";
    } elseif ($password !== $konfirm) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        // Check duplicate username
        $cek = mysqli_query($koneksi, "SELECT id FROM admin WHERE username='$username'") or die(mysqli_error($koneksi));
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username '$username' sudah digunakan! Pilih username lain.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($koneksi, "INSERT INTO admin (nama, username, password) VALUES ('$nama', '$username', '$hash')") or die(mysqli_error($koneksi));
            if ($insert) {
                $_SESSION['flash_success'] = "Akun admin berhasil dibuat! Silakan login sekarang.";
                header('Location: login.php');
                exit;
            } else {
                $error = "Gagal membuat akun. Coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Admin — Sistem Data Mahasiswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Outfit', sans-serif; background: #090d16; min-height: 100vh; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
        body::before, body::after { content: ''; position: fixed; border-radius: 50%; filter: blur(80px); opacity: 0.18; animation: float 8s ease-in-out infinite alternate; pointer-events: none; }
        body::before { width: 200px; height: 200px; background: radial-gradient(circle, #a855f7, #7c3aed); top: 0px; right: -150px; }
        body::after { width: 200px; height: 200px; background: radial-gradient(circle, #6366f1, #4f46e5); bottom: -200px; left: -100px; animation-delay: -4s; }
        @keyframes float { from { transform: translate(0, 0) scale(1); } to { transform: translate(40px, 30px) scale(1.08); } }
        .auth-card { width: 100%; max-width: 420px; margin: 20px; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 24px; padding: 44px 40px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(99, 102, 241, 0.1); position: relative; z-index: 1; animation: slideUp 0.5s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .auth-logo { width: 60px; height: 60px; background: linear-gradient(135deg, #a855f7, #7c3aed); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 20px; box-shadow: 0 8px 24px rgba(168, 85, 247, 0.35); }
        .auth-title { text-align: center; font-size: 22px; font-weight: 700; color: #ffffff; margin-bottom: 6px; }
        .auth-subtitle { text-align: center; font-size: 14px; color: #64748b; margin-bottom: 32px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 8px; letter-spacing: 0.3px; }
        .form-input { width: 100%; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; color: #f1f5f9; padding: 12px 16px; font-size: 15px; font-family: 'Outfit', sans-serif; outline: none; transition: all 0.2s ease; }
        .form-input:focus { border-color: #a855f7; box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.2); background: rgba(30, 41, 59, 0.8); }
        .form-input::placeholder { color: #475569; }
        .password-wrapper { position: relative; }
        .password-wrapper .form-input { padding-right: 44px; }
        .toggle-pass { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 18px; color: #475569; user-select: none; transition: color 0.2s; }
        .toggle-pass:hover { color: #a855f7; }
        .btn-primary { width: 100%; background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%); color: #ffffff; border: none; padding: 13px; font-size: 15px; font-weight: 600; font-family: 'Outfit', sans-serif; border-radius: 10px; cursor: pointer; transition: all 0.25s ease; box-shadow: 0 4px 16px rgba(168, 85, 247, 0.3); margin-top: 8px; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(168, 85, 247, 0.45); background: linear-gradient(135deg, #c084fc 0%, #a855f7 100%); }
        .auth-divider { display: flex; align-items: center; gap: 12px; margin: 24px 0; }
        .auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.08); }
        .auth-divider span { font-size: 12px; color: #475569; white-space: nowrap; }
        .auth-link-row { text-align: center; font-size: 14px; color: #64748b; }
        .auth-link-row a { color: #c084fc; font-weight: 600; text-decoration: none; transition: color 0.2s; }
        .auth-link-row a:hover { color: #e9d5ff; }
        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 500; margin-bottom: 20px; animation: slideUp 0.3s ease-out; }
        .alert-error { background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; }
        .alert-success { background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.3); color: #86efac; }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-logo">🛡️</div>
    <h1 class="auth-title">Daftar Admin</h1>
    <p class="auth-subtitle">Buat akun admin baru untuk mengakses sistem</p>
    <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form action="" method="post" onsubmit="return validateRegister();" id="regForm">
        <div class="form-group">
            <label class="form-label" for="nama">Nama Lengkap</label>
            <input class="form-input" type="text" id="nama" name="nama" placeholder="Nama lengkap admin" autocomplete="name" value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input class="form-input" type="text" id="username" name="username" placeholder="Min. 5 karakter (huruf, angka, _)" autocomplete="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" oninput="checkUsername(this)">
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="password-wrapper">
                <input class="form-input" type="password" id="password" name="password" placeholder="Min. 6 karakter" autocomplete="new-password" oninput="checkStrength(this)">
                <span class="toggle-pass" onclick="togglePassword('password', this)">👁</span>
            </div>
            <div class="strength-bar-wrapper">
                <div class="strength-bar-bg"><div class="strength-bar" id="strength-bar"></div></div>
                <div class="strength-label" id="strength-label">Masukkan password</div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="konfirmasi">Konfirmasi Password</label>
            <div class="password-wrapper">
                <input class="form-input" type="password" id="konfirmasi" name="konfirmasi" placeholder="Ulangi password Anda" autocomplete="new-password" oninput="checkMatch()">
                <span class="toggle-pass" onclick="togglePassword('konfirmasi', this)">👁</span>
            </div>
            <div class="strength-label" id="match-label"></div>
        </div>
        <button type="submit" name="register" class="btn-primary">Buat Akun Admin</button>
    </form>
    <div class="auth-divider"><span>sudah punya akun?</span></div>
    <div class="auth-link-row"><a href="login.php">← Kembali ke halaman Login</a></div>
</div>
<script>
function togglePassword(fieldId, icon) {
    var f = document.getElementById(fieldId);
    if (f.type === 'password') { f.type = 'text'; icon.textContent = '🙈'; }
    else { f.type = 'password'; icon.textContent = '👁'; }
}
function checkStrength(input) {
    var p = input.value;
    var bar = document.getElementById('strength-bar');
    var lbl = document.getElementById('strength-label');
    var score = 0;
    if (p.length >= 6) score++;
    if (p.length >= 10) score++;
    if (/[A-Z]/.test(p)) score++;
    if (/[0-9]/.test(p)) score++;
    if (/[^A-Za-z0-9]/.test(p)) score++;
    var levels = [
        {w:'0%', c:'transparent', t:''},
        {w:'20%', c:'#ef4444', t:'Sangat Lemah'},
        {w:'40%', c:'#f97316', t:'Lemah'},
        {w:'60%', c:'#eab308', t:'Cukup'},
        {w:'80%', c:'#22c55e', t:'Kuat'},
        {w:'100%', c:'#6366f1', t:'Sangat Kuat'}
    ];
    var l = levels[Math.min(score,5)];
    bar.style.width = l.w; bar.style.background = l.c; lbl.textContent = l.t; lbl.style.color = l.c || '#64748b';
    checkMatch();
}
function checkMatch() {
    var p1 = document.getElementById('password').value;
    var p2 = document.getElementById('konfirmasi').value;
    var lbl = document.getElementById('match-label');
    if (!p2) { lbl.textContent=''; return; }
    if (p1===p2) { lbl.textContent='✅ Password cocok!'; lbl.style.color='#86efac'; }
    else { lbl.textContent='❌ Password tidak cocok'; lbl.style.color='#fca5a5'; }
}
function checkUsername(input) {
    var u = input.value.trim();
    var valid = /^[a-zA-Z0-9_]+$/.test(u);
    input.style.borderColor = (!u || valid) ? '' : '#ef4444';
}
function validateRegister() {
    var nama = document.getElementById('nama').value.trim();
    var user = document.getElementById('username').value.trim();
    var pass = document.getElementById('password').value.trim();
    var konf = document.getElementById('konfirmasi').value.trim();
    if (!nama) { alert('Nama Lengkap tidak boleh kosong!'); return false; }
    if (nama.length < 3) { alert('Nama harus minimal 3 karakter!'); return false; }
    if (!user) { alert('Username tidak boleh kosong!'); return false; }
    if (user.length < 5) { alert('Username harus minimal 5 karakter!'); return false; }
    if (!/^[a-zA-Z0-9_]+$/.test(user)) { alert('Username hanya boleh huruf, angka, dan garis bawah!'); return false; }
    if (!pass) { alert('Password tidak boleh kosong!'); return false; }
    if (pass.length < 6) { alert('Password harus minimal 6 karakter!'); return false; }
    if (!konf) { alert('Konfirmasi password tidak boleh kosong!'); return false; }
    if (pass !== konf) { alert('Konfirmasi password tidak cocok!'); return false; }
    return true;
}
</script>
</body>
</html>

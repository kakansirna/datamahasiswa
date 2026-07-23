<?php
session_start();

// Proteksi halaman: redirect ke login jika belum login
if (!isset($_SESSION['admin_id'])) {
    header("location:login.php");
    exit;
}

ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
        <title>Sistem Data Mahasiswa</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel='stylesheet' href='main.css'>
</head>
<body>
<div class="container">
<div class="header">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h1>Sistem Informasi Data Mahasiswa</h1>
        <div class="user-info">
            <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['admin_nama'], 0, 1)); ?></div>
            <div>
                <div style="font-size:14px; font-weight:600; color:#ffffff;"><?php echo htmlspecialchars($_SESSION['admin_nama']); ?></div>
                <div style="font-size:12px; color:#64748b;">@<?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
            </div>
            <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar dari sistem?');">Keluar</a>
        </div>
    </div>
</div>
<div class="main">
    <?php 
    $page = (isset($_GET['page'])) ? $_GET['page'] : 'main'; 
    ?>
    <div class="left">
        <h3 align="left">Menu Navigasi</h3>
        <ul>
            <li><a href="index.php" class="<?php echo ($page == 'main' || $page == '') ? 'active' : ''; ?>">🏠 Beranda</a></li>
            <li><a href="index.php?page=mahasiswa" class="<?php echo in_array($page, ['mahasiswa','tambah-mahasiswa','edit-mahasiswa']) ? 'active' : ''; ?>">🎓 Data Mahasiswa</a></li>
        </ul>
    </div>
    <div class="middle">
        <?php 
        switch($page) {
            case 'mahasiswa':
                include 'tampil-mahasiswa.php';
                break;
            case 'tambah-mahasiswa':
                include 'tambah-mahasiswa.php';
                break;   
            case 'edit-mahasiswa':
                include 'edit-mahasiswa.php';
                break;
            case 'main':
            default:
                include 'beranda.php';
                break;
        }
        ?>
    </div>
</div>
<!-- <div class='footer'>
<p align='center'>Copyright &copy; 2026 Raffa Fajar &mdash; Sistem Informasi Data Mahasiswa</p>
</div> -->
</div>
</body>
</html>
<?php ob_end_flush(); ?>
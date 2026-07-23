<?php
// session_start();
include('koneksi.php');
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda — Sistem Data Mahasiswa</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></h1>
    </div>
        <div class="middle">
            <h3>Statistik Mahasiswa</h3>
            <?php
            // Hitung statistik
            $total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM mahasiswa") or die(mysqli_error($koneksi));
            $total = mysqli_fetch_assoc($total_query)['total'];
            $l_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM mahasiswa WHERE jenis_kelamin='Laki-laki'") or die(mysqli_error($koneksi));
            $total_l = mysqli_fetch_assoc($l_query)['total'];
            $p_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM mahasiswa WHERE jenis_kelamin='Perempuan'") or die(mysqli_error($koneksi));
            $total_p = mysqli_fetch_assoc($p_query)['total'];
            ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
                <div style="background: linear-gradient(135deg, rgba(99,102,241,0.25) 0%, rgba(79,70,229,0.25) 100%); border: 1px solid rgba(99,102,241,0.3); border-radius: 14px; padding: 24px; text-align: center; box-shadow: 0 4px 20px rgba(99,102,241,0.15);">
                    <h4 style="font-size: 14px; text-transform: uppercase; color: #a5b4fc; letter-spacing: 1px; margin-bottom: 8px;">Jumlah Mahasiswa</h4>
                    <div style="font-size: 36px; font-weight: 700; color: #ffffff;"><?php echo $total; ?></div>
                </div>
                <div style="background: linear-gradient(135deg, rgba(56,189,248,0.25) 0%, rgba(14,165,233,0.25) 100%); border: 1px solid rgba(56,189,248,0.3); border-radius: 14px; padding: 24px; text-align: center; box-shadow: 0 4px 20px rgba(56,189,248,0.15);">
                    <h4 style="font-size: 14px; text-transform: uppercase; color: #7dd3fc; letter-spacing: 1px; margin-bottom: 8px;">Mahasiswa Laki‑Laki</h4>
                    <div style="font-size: 36px; font-weight: 700; color: #ffffff;"><?php echo $total_l; ?></div>
                </div>
                <div style="background: linear-gradient(135deg, rgba(244,63,94,0.25) 0%, rgba(225,29,72,0.25) 100%); border: 1px solid rgba(244,63,94,0.3); border-radius: 14px; padding: 24px; text-align: center; box-shadow: 0 4px 20px rgba(244,63,94,0.15);">
                    <h4 style="font-size: 14px; text-transform: uppercase; color: #fda4af; letter-spacing: 1px; margin-bottom: 8px;">Mahasiswa Perempuan</h4>
                    <div style="font-size: 36px; font-weight: 700; color: #ffffff;"><?php echo $total_p; ?></div>
                </div>
            </div>
            <div style="margin-top: 40px; background: rgba(30,41,59,0.25); border: 1px solid rgba(255,255,255,0.05); border-radius: 14px; padding: 20px;">
                <h4 style="color: #ffffff; margin-bottom: 10px;">Petunjuk Penggunaan</h4>
                <ul style="list-style-type: none; padding-left: 0; color: #94a3b8; font-size: 14px; line-height: 1.8;">
                    <li>📁 Pilih <strong>Data Mahasiswa</strong> untuk melihat daftar.</li>
                    <li>➕ Klik <strong>Tambah {+}</strong> untuk menambah data baru.</li>
                    <li>✏️ Gunakan tombol <strong>Edit</strong> pada tiap baris untuk mengubah data.</li>
                    <li>❌ Gunakan tombol <strong>Hapus</strong> pada tiap baris untuk menghapus data.</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer">
        &copy; <?php echo date('Y'); ?> Sistem Data Mahasiswa.By Fajar, Arij, Aldi, Dion.
    </div>
</div>
</body>
</html>

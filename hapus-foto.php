<?php
session_start();
include('koneksi.php');

if (!isset($_SESSION['admin_id'])) {
    header("location:login.php");
    exit;
}

if (!isset($_GET['nim'])) {
    header("location:index.php?page=mahasiswa");
    exit;
}

$nim = mysqli_real_escape_string($koneksi, $_GET['nim']);

// Ambil data mahasiswa
$query = mysqli_query($koneksi, "SELECT nim, foto FROM mahasiswa WHERE nim='$nim'") or die(mysqli_error($koneksi));

if (mysqli_num_rows($query) == 0) {
    header("location:index.php?page=mahasiswa");
    exit;
}

$data = mysqli_fetch_assoc($query);

if (empty($data['foto'])) {
    // Tidak ada foto, langsung kembali
    header("location:index.php?page=edit-mahasiswa&nim=$nim");
    exit;
}

// Hapus file foto dari disk
$file_path = 'uploads/' . $data['foto'];
if (file_exists($file_path)) {
    unlink($file_path);
}

// Set kolom foto menjadi NULL di database
$update = mysqli_query($koneksi, "UPDATE mahasiswa SET foto=NULL WHERE nim='$nim'") or die(mysqli_error($koneksi));

if ($update) {
    header("location:index.php?page=edit-mahasiswa&nim=$nim");
    exit;
} else {
    echo "<script>alert('Gagal menghapus foto!'); window.history.back();</script>";
}
?>

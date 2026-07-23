<?php
include('koneksi.php');

if (isset($_GET['nim'])) {
    $nim = mysqli_real_escape_string($koneksi, $_GET['nim']);

    // Cek keberadaan data dan ambil nama foto
    $cek = mysqli_query($koneksi, "SELECT nim, foto FROM mahasiswa WHERE nim='$nim'") or die(mysqli_error($koneksi));

    if (mysqli_num_rows($cek) == 0) {
        header("location:index.php?page=mahasiswa");
        exit;
    }

    $row = mysqli_fetch_assoc($cek);

    // Hapus file foto dari disk jika ada
    if (!empty($row['foto']) && file_exists('uploads/' . $row['foto'])) {
        unlink('uploads/' . $row['foto']);
    }

    // Hapus data dari database
    $del = mysqli_query($koneksi, "DELETE FROM mahasiswa WHERE nim='$nim'") or die(mysqli_error($koneksi));

    if ($del) {
        header("location:index.php?page=mahasiswa");
        exit;
    } else {
        echo "<script>alert('Error: Gagal menghapus data!'); window.location.href='index.php?page=mahasiswa';</script>";
        exit;
    }
} else {
    header("location:index.php?page=mahasiswa");
    exit;
}
?>

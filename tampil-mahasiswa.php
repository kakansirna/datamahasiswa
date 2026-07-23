<h3>Daftar Data Mahasiswa</h3>

<?php
include('koneksi.php');

// Fitur Pencarian
$search = "";
if (isset($_POST['search_btn'])) {
    $search = mysqli_real_escape_string($koneksi, $_POST['search_query']);
}

$sql = "SELECT * FROM mahasiswa";
if ($search != "") {
    $sql .= " WHERE nim LIKE '%$search%' OR nama LIKE '%$search%' OR jurusan LIKE '%$search%' OR alamat LIKE '%$search%'";
}
$sql .= " ORDER BY nim ASC";

$query = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));
?>

<!-- Search Bar & Add Button Row -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    <div>
        <a href="index.php?page=tambah-mahasiswa" class="btn-add">&#43; Tambah Mahasiswa</a>
    </div>
    
    <form action="" method="post" style="display: flex; gap: 10px; margin: 0; max-width: 420px; width: 100%;">
        <input type="text" name="search_query" placeholder="Cari NIM, Nama, Jurusan..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; min-width: 150px;">
        <button type="submit" name="search_btn">Cari</button>
        <?php if ($search != ""): ?>
            <a href="index.php?page=mahasiswa" style="display:inline-flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;color:#fff;padding:10px 16px;font-weight:bold;white-space:nowrap;">&#215; Reset</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table Display -->
<div style="overflow-x: auto;">
<table>
    <tr class="header-row">
        <th>No.</th>
        <th>Foto</th>
        <th>NIM</th>
        <th>Nama Lengkap</th>
        <th>Jenis Kelamin</th>
        <th>Jurusan</th>
        <th>Alamat</th>
        <th>No. HP</th>
        <th>Opsi</th>
    </tr>
<?php
if (mysqli_num_rows($query) == 0) {
    echo '<tr><td colspan="9" align="center" style="padding: 30px; color: #64748b;">Tidak ada data mahasiswa ditemukan!</td></tr>';
} else {
    $no = 1;
    while ($data = mysqli_fetch_assoc($query)) {
        // Build foto element
        if (!empty($data['foto']) && file_exists('uploads/' . $data['foto'])) {
            $foto_html = '<img src="uploads/' . htmlspecialchars($data['foto']) . '" alt="Foto ' . htmlspecialchars($data['nama']) . '" style="width:50px;height:50px;object-fit:cover;border-radius:50%;border:2px solid rgba(99,102,241,0.4);">';
        } else {
            // Generate initials avatar
            $inisial = strtoupper(substr($data['nama'], 0, 1));
            $foto_html = '<div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:#fff;margin:0 auto;">' . $inisial . '</div>';
        }

        echo '<tr>';
        echo '<td style="text-align:center;">' . $no . '.</td>';
        echo '<td style="text-align:center;">' . $foto_html . '</td>';
        echo '<td>' . htmlspecialchars($data['nim']) . '</td>';
        echo '<td>' . htmlspecialchars($data['nama']) . '</td>';
        echo '<td>' . htmlspecialchars($data['jenis_kelamin']) . '</td>';
        echo '<td>' . htmlspecialchars($data['jurusan']) . '</td>';
        echo '<td>' . htmlspecialchars($data['alamat']) . '</td>';
        echo '<td>' . htmlspecialchars($data['no_hp']) . '</td>';
        echo '<td style="text-align:center;white-space:nowrap;">';
        echo '<a href="index.php?page=edit-mahasiswa&nim=' . urlencode($data['nim']) . '">Edit</a> ';
        echo '<a href="hapus-mahasiswa.php?nim=' . urlencode($data['nim']) . '" onclick="return confirm(\'Yakin ingin menghapus data mahasiswa ' . htmlspecialchars($data['nama'], ENT_QUOTES) . '?\')">Hapus</a>';
        echo '</td>';
        echo '</tr>';
        $no++;
    }
}
?>
</table>
</div>

<style>
.btn-add {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
    color: #ffffff !important;
    padding: 10px 20px !important;
    border-radius: 8px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25) !important;
    transition: all 0.2s ease !important;
}
.btn-add:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4) !important;
    background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%) !important;
}
</style>

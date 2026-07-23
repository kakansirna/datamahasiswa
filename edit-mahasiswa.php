<h3>Edit Data Mahasiswa</h3>

<?php
include('koneksi.php');

// Cari data berdasarkan NIM
if (!isset($_GET['nim'])) {
    header("location:index.php?page=mahasiswa");
    exit;
}

$nim = mysqli_real_escape_string($koneksi, $_GET['nim']);
$show = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE nim='$nim'") or die(mysqli_error($koneksi));

if (mysqli_num_rows($show) == 0) {
    echo "<script>alert('Error: Data mahasiswa tidak ditemukan!'); window.location.href='index.php?page=mahasiswa';</script>";
    exit;
} else {
    $data = mysqli_fetch_assoc($show);
}

// ── Proses Submit Edit ───────────────────────────────────────────────────
if (isset($_POST['kirim'])) {
    $nama           = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $jenis_kelamin  = mysqli_real_escape_string($koneksi, trim($_POST['jenis_kelamin']));
    $jurusan        = mysqli_real_escape_string($koneksi, trim($_POST['jurusan']));
    $alamat         = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $no_hp          = mysqli_real_escape_string($koneksi, trim($_POST['no_hp']));

    // Server-side Validation
    if (empty($nama) || empty($jenis_kelamin) || empty($jurusan) || empty($alamat) || empty($no_hp)) {
        echo "<script>alert('Error: Semua kolom wajib diisi!'); window.history.back();</script>";
        exit;
    }
    if (!ctype_digit($no_hp) || strlen($no_hp) < 10 || strlen($no_hp) > 15) {
        echo "<script>alert('Error: Nomor HP harus berupa angka sepanjang 10–15 digit!'); window.history.back();</script>";
        exit;
    }

    // ── Proses Upload Foto Baru (jika ada) ──────────────────────────────
    $nama_foto_baru = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp   = $_FILES['foto']['tmp_name'];
        $file_name  = $_FILES['foto']['name'];
        $file_size  = $_FILES['foto']['size'];
        $file_ext   = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext  = ['jpg', 'jpeg', 'png'];
        $max_size     = 2 * 1024 * 1024; // 2 MB

        if (!in_array($file_ext, $allowed_ext)) {
            echo "<script>alert('Error: Format foto tidak didukung! Hanya JPG, JPEG, dan PNG.'); window.history.back();</script>";
            exit;
        }
        if ($file_size > $max_size) {
            echo "<script>alert('Error: Ukuran foto melebihi batas 2 MB!'); window.history.back();</script>";
            exit;
        }

        // Validasi MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);
        if (!in_array($mime, ['image/jpeg', 'image/png'])) {
            echo "<script>alert('Error: File yang diunggah bukan gambar valid!'); window.history.back();</script>";
            exit;
        }

        // Hapus foto lama jika ada
        if (!empty($data['foto']) && file_exists('uploads/' . $data['foto'])) {
            unlink('uploads/' . $data['foto']);
        }

        // Simpan foto baru
        $nama_foto_baru = 'mhs_' . $nim . '_' . time() . '.' . $file_ext;
        $upload_path = 'uploads/' . $nama_foto_baru;
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            echo "<script>alert('Error: Gagal menyimpan foto. Pastikan folder uploads/ memiliki izin tulis.'); window.history.back();</script>";
            exit;
        }
    }

    // ── Update Database ──────────────────────────────────────────────────
    if ($nama_foto_baru !== null) {
        // Perbarui dengan foto baru
        $sql_update = "UPDATE mahasiswa SET nama='$nama', jenis_kelamin='$jenis_kelamin', jurusan='$jurusan', alamat='$alamat', no_hp='$no_hp', foto='$nama_foto_baru' WHERE nim='$nim'";
    } else {
        // Pertahankan foto lama
        $sql_update = "UPDATE mahasiswa SET nama='$nama', jenis_kelamin='$jenis_kelamin', jurusan='$jurusan', alamat='$alamat', no_hp='$no_hp' WHERE nim='$nim'";
    }

    $query = mysqli_query($koneksi, $sql_update) or die(mysqli_error($koneksi));
    if ($query) {
        header('location:index.php?page=mahasiswa');
        exit;
    }
}
?>

<script>
function validateForm() {
    var nama    = document.forms["editForm"]["nama"].value.trim();
    var jk      = document.forms["editForm"]["jenis_kelamin"].value;
    var jurusan = document.forms["editForm"]["jurusan"].value.trim();
    var alamat  = document.forms["editForm"]["alamat"].value.trim();
    var no_hp   = document.forms["editForm"]["no_hp"].value.trim();
    var fotoIn  = document.forms["editForm"]["foto"];
    var angka   = /^[0-9]+$/;

    if (!nama)                              { alert("Nama Lengkap tidak boleh kosong!");        return false; }
    if (nama.length < 3)                    { alert("Nama Lengkap minimal 3 karakter!");        return false; }
    if (!jk)                                { alert("Silakan pilih Jenis Kelamin!");            return false; }
    if (!jurusan)                           { alert("Jurusan tidak boleh kosong!");             return false; }
    if (!alamat)                            { alert("Alamat tidak boleh kosong!");              return false; }
    if (!no_hp)                             { alert("Nomor HP tidak boleh kosong!");            return false; }
    if (!angka.test(no_hp))                 { alert("Nomor HP hanya boleh berisi angka!");      return false; }
    if (no_hp.length < 10 || no_hp.length > 15) { alert("Nomor HP harus 10–15 digit angka!"); return false; }

    // Validasi foto baru (jika diisi)
    if (fotoIn.files.length > 0) {
        var file    = fotoIn.files[0];
        var ext     = file.name.split('.').pop().toLowerCase();
        var allowed = ['jpg','jpeg','png'];
        if (!allowed.includes(ext)) {
            alert("Format foto tidak didukung! Hanya JPG, JPEG, PNG.");
            return false;
        }
        if (file.size > 2 * 1024 * 1024) {
            alert("Ukuran foto melebihi batas 2 MB!");
            return false;
        }
    }
    return true;
}

function previewFoto(input) {
    var preview = document.getElementById('foto-preview-new');
    var wrapper = document.getElementById('foto-new-wrapper');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            wrapper.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<form name="editForm" action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm();">
    <table cellpadding="3" cellspacing="0">
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>
                <input type="text" name="nim" value="<?php echo htmlspecialchars($data['nim']); ?>" readonly
                       style="background:rgba(255,255,255,0.05);color:#94a3b8;cursor:not-allowed;">
            </td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td>:</td>
            <td><input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>
                <select name="jenis_kelamin" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-laki"  <?php if ($data['jenis_kelamin'] == 'Laki-laki')  echo 'selected'; ?>>Laki-laki</option>
                    <option value="Perempuan"   <?php if ($data['jenis_kelamin'] == 'Perempuan')  echo 'selected'; ?>>Perempuan</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Jurusan</td>
            <td>:</td>
            <td><input type="text" name="jurusan" value="<?php echo htmlspecialchars($data['jurusan']); ?>" required></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td><textarea name="alamat" rows="3" required style="resize:vertical;"><?php echo htmlspecialchars($data['alamat']); ?></textarea></td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td><input type="text" name="no_hp" value="<?php echo htmlspecialchars($data['no_hp']); ?>" required></td>
        </tr>
        <tr>
            <td>Foto</td>
            <td>:</td>
            <td>
                <div style="display:flex; flex-direction:column; gap:14px;">

                    <!-- Foto saat ini -->
                    <div>
                        <p style="font-size:12px; color:#94a3b8; margin-bottom:8px;">Foto saat ini:</p>
                        <?php if (!empty($data['foto']) && file_exists('uploads/' . $data['foto'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($data['foto']); ?>" alt="Foto Mahasiswa"
                                 style="width:90px;height:90px;object-fit:cover;border-radius:50%;border:3px solid rgba(99,102,241,0.5);box-shadow:0 4px 15px rgba(99,102,241,0.2);">
                        <?php else: ?>
                            <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:30px;color:#fff;">
                                <?php echo strtoupper(substr($data['nama'], 0, 1)); ?>
                            </div>
                            <p style="font-size:12px; color:#64748b; margin-top:6px;">Belum ada foto tersimpan.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Upload foto baru -->
                    <div>
                        <p style="font-size:12px; color:#94a3b8; margin-bottom:8px;">Ganti dengan foto baru (opsional):</p>
                        <label for="foto-input-edit" style="display:inline-flex;align-items:center;gap:8px;background:rgba(99,102,241,0.12);border:1px dashed rgba(99,102,241,0.5);border-radius:8px;padding:10px 16px;color:#818cf8;font-weight:500;cursor:pointer;font-size:13px;width:fit-content;">
                            <span style="font-size:18px;">&#128247;</span> Pilih Foto Baru
                        </label>
                        <input id="foto-input-edit" type="file" name="foto" accept=".jpg,.jpeg,.png" style="display:none;" onchange="previewFoto(this)">
                        <span id="foto-name-edit" style="display:block; font-size:12px; color:#64748b; margin-top:6px;">JPG, JPEG, PNG · Maks. 2 MB</span>

                        <!-- Live Preview Foto Baru -->
                        <div id="foto-new-wrapper" style="display:none; margin-top:10px;">
                            <p style="font-size:12px; color:#94a3b8; margin-bottom:6px;">Preview foto baru:</p>
                            <img id="foto-preview-new" src="#" alt="Preview"
                                 style="width:90px;height:90px;object-fit:cover;border-radius:50%;border:3px solid rgba(99,102,241,0.5);box-shadow:0 4px 15px rgba(99,102,241,0.2);">
                        </div>
                    </div>
                </div>

                <script>
                document.getElementById('foto-input-edit').addEventListener('change', function() {
                    var el = document.getElementById('foto-name-edit');
                    el.textContent = this.files.length > 0 ? this.files[0].name : 'JPG, JPEG, PNG · Maks. 2 MB';
                });
                </script>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top:18px;">
                <button type="submit" name="kirim">Simpan Perubahan</button>
                <a href="index.php?page=mahasiswa" style="margin-left:15px; font-weight:600;">&#8592; Kembali</a>
            </td>
        </tr>
    </table>
</form>

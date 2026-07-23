<h3>Tambah Data Mahasiswa</h3>

<?php
include('koneksi.php');

if (isset($_POST['kirim'])) {
    // Sanitasi input PHP
    $nim            = mysqli_real_escape_string($koneksi, trim($_POST['nim']));
    $nama           = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $jenis_kelamin  = mysqli_real_escape_string($koneksi, trim($_POST['jenis_kelamin']));
    $jurusan        = mysqli_real_escape_string($koneksi, trim($_POST['jurusan']));
    $alamat         = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $no_hp          = mysqli_real_escape_string($koneksi, trim($_POST['no_hp']));

    // ── Server-side Validation ──────────────────────────────────────────
    if (empty($nim) || empty($nama) || empty($jenis_kelamin) || empty($jurusan) || empty($alamat) || empty($no_hp)) {
        echo "<script>alert('Error: Semua kolom wajib diisi!'); window.history.back();</script>";
        exit;
    }
    if (!ctype_digit($nim) || strlen($nim) < 9 || strlen($nim) > 15) {
        echo "<script>alert('Error: NIM harus berupa angka sepanjang 9–15 digit!'); window.history.back();</script>";
        exit;
    }
    if (!ctype_digit($no_hp) || strlen($no_hp) < 10 || strlen($no_hp) > 15) {
        echo "<script>alert('Error: Nomor HP harus berupa angka sepanjang 10–15 digit!'); window.history.back();</script>";
        exit;
    }

    // Cek duplikasi NIM
    $check = mysqli_query($koneksi, "SELECT nim FROM mahasiswa WHERE nim='$nim'") or die(mysqli_error($koneksi));
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Error: NIM $nim sudah terdaftar di sistem!'); window.history.back();</script>";
        exit;
    }

    // ── Proses Upload Foto ──────────────────────────────────────────────
    $nama_foto = NULL;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp   = $_FILES['foto']['tmp_name'];
        $file_name  = $_FILES['foto']['name'];
        $file_size  = $_FILES['foto']['size'];
        $file_ext   = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $max_size    = 2 * 1024 * 1024; // 2 MB

        if (!in_array($file_ext, $allowed_ext)) {
            echo "<script>alert('Error: Format foto tidak didukung! Hanya JPG, JPEG, dan PNG yang diizinkan.'); window.history.back();</script>";
            exit;
        }
        if ($file_size > $max_size) {
            echo "<script>alert('Error: Ukuran foto melebihi batas 2 MB!'); window.history.back();</script>";
            exit;
        }

        // Cek file adalah gambar asli
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);
        $allowed_mime = ['image/jpeg', 'image/png'];
        if (!in_array($mime, $allowed_mime)) {
            echo "<script>alert('Error: File yang diunggah bukan gambar valid!'); window.history.back();</script>";
            exit;
        }

        // Simpan dengan nama unik
        $nama_foto = 'mhs_' . $nim . '_' . time() . '.' . $file_ext;
        $upload_path = 'uploads/' . $nama_foto;

        if (!move_uploaded_file($file_tmp, $upload_path)) {
            echo "<script>alert('Error: Gagal menyimpan foto. Pastikan folder uploads/ memiliki izin tulis.'); window.history.back();</script>";
            exit;
        }
    }

    // ── Insert Database ─────────────────────────────────────────────────
    $foto_val = ($nama_foto !== NULL) ? "'$nama_foto'" : "NULL";
    $sql = "INSERT INTO mahasiswa (nim, nama, jenis_kelamin, jurusan, alamat, no_hp, foto)
            VALUES ('$nim', '$nama', '$jenis_kelamin', '$jurusan', '$alamat', '$no_hp', $foto_val)";
    $query = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));

    if ($query) {
        header('location:index.php?page=mahasiswa');
        exit;
    }
}
?>

<script>
function validateForm() {
    var nim           = document.forms["tambahForm"]["nim"].value.trim();
    var nama          = document.forms["tambahForm"]["nama"].value.trim();
    var jk            = document.forms["tambahForm"]["jenis_kelamin"].value;
    var jurusan       = document.forms["tambahForm"]["jurusan"].value.trim();
    var alamat        = document.forms["tambahForm"]["alamat"].value.trim();
    var no_hp         = document.forms["tambahForm"]["no_hp"].value.trim();
    var fotoInput     = document.forms["tambahForm"]["foto"];
    var angkaOnly     = /^[0-9]+$/;

    if (!nim)                              { alert("NIM tidak boleh kosong!");               return false; }
    if (!angkaOnly.test(nim))              { alert("NIM hanya boleh berisi angka!");          return false; }
    if (nim.length < 9 || nim.length > 15){ alert("NIM harus 9–15 digit angka!");            return false; }
    if (!nama)                             { alert("Nama Lengkap tidak boleh kosong!");       return false; }
    if (nama.length < 3)                   { alert("Nama Lengkap minimal 3 karakter!");       return false; }
    if (!jk)                               { alert("Silakan pilih Jenis Kelamin!");           return false; }
    if (!jurusan)                          { alert("Jurusan tidak boleh kosong!");            return false; }
    if (!alamat)                           { alert("Alamat tidak boleh kosong!");             return false; }
    if (!no_hp)                            { alert("Nomor HP tidak boleh kosong!");           return false; }
    if (!angkaOnly.test(no_hp))            { alert("Nomor HP hanya boleh berisi angka!");     return false; }
    if (no_hp.length < 10 || no_hp.length > 15){ alert("Nomor HP harus 10–15 digit angka!"); return false; }

    // Validasi foto (opsional, jika diisi)
    if (fotoInput.files.length > 0) {
        var file     = fotoInput.files[0];
        var fileExt  = file.name.split('.').pop().toLowerCase();
        var allowed  = ['jpg','jpeg','png'];
        var maxSize  = 2 * 1024 * 1024; // 2 MB

        if (!allowed.includes(fileExt)) {
            alert("Format foto tidak didukung! Hanya JPG, JPEG, dan PNG yang diizinkan.");
            return false;
        }
        if (file.size > maxSize) {
            alert("Ukuran foto melebihi batas 2 MB!");
            return false;
        }
    }
    return true;
}

// Live preview foto sebelum diunggah
function previewFoto(input) {
    var preview = document.getElementById('foto-preview');
    var wrapper = document.getElementById('foto-preview-wrapper');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src  = e.target.result;
            wrapper.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<form name="tambahForm" action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm();">
    <table cellpadding="3" cellspacing="0">
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td><input type="text" name="nim" placeholder="Contoh: 220101004" required></td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td>:</td>
            <td><input type="text" name="nama" placeholder="Nama lengkap mahasiswa" required></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>
                <select name="jenis_kelamin" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Jurusan</td>
            <td>:</td>
            <td><input type="text" name="jurusan" placeholder="Contoh: Teknik Informatika" required></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td><textarea name="alamat" rows="3" placeholder="Alamat tinggal mahasiswa" required style="resize:vertical;"></textarea></td>
        </tr>
        <tr>
            <td>No. HP</td>
            <td>:</td>
            <td><input type="text" name="no_hp" placeholder="Contoh: 081234567890" required></td>
        </tr>
        <tr>
            <td>Foto</td>
            <td>:</td>
            <td>
                <!-- Custom File Upload UI -->
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <label for="foto-input" style="display:inline-flex; align-items:center; gap:8px; background:rgba(99,102,241,0.12); border:1px dashed rgba(99,102,241,0.5); border-radius:8px; padding:10px 16px; color:#818cf8; font-weight:500; cursor:pointer; font-size:13px; width:fit-content;">
                        <span style="font-size:18px;">&#128247;</span>
                        Pilih Foto (JPG, JPEG, PNG &#8212; Maks. 2 MB)
                    </label>
                    <input id="foto-input" type="file" name="foto" accept=".jpg,.jpeg,.png" style="display:none;" onchange="previewFoto(this)">
                    <span id="foto-name" style="font-size:12px; color:#64748b;">Belum ada file dipilih (opsional)</span>

                    <!-- Live Preview -->
                    <div id="foto-preview-wrapper" style="display:none;">
                        <img id="foto-preview" src="#" alt="Preview Foto"
                             style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:3px solid rgba(99,102,241,0.5);box-shadow:0 4px 15px rgba(99,102,241,0.2);">
                    </div>
                </div>
                <script>
                document.getElementById('foto-input').addEventListener('change', function() {
                    var nameDisplay = document.getElementById('foto-name');
                    nameDisplay.textContent = this.files.length > 0 ? this.files[0].name : 'Belum ada file dipilih (opsional)';
                });
                </script>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top:18px;">
                <button type="submit" name="kirim">Simpan Mahasiswa</button>
                <a href="index.php?page=mahasiswa" style="margin-left:15px; font-weight:600;">&#8592; Kembali</a>
            </td>
        </tr>
    </table>
</form>

<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "../config/koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "status" => "gagal",
        "pesan" => "Metode permintaan tidak diizinkan."
    ]);

    exit;
}

/* Mengambil data formulir */

$jenis = $_POST["jenis"] ?? "";
$nama = trim($_POST["nama_pelapor"] ?? "");
$gedung = $_POST["kode_gedung"] ?? "";
$barang = trim($_POST["nama_barang"] ?? "");
$ciri = trim($_POST["ciri_barang"] ?? "");
$lokasi = trim($_POST["lokasi"] ?? "");
$tanggal = $_POST["tanggal"] ?? "";
$kontak = trim($_POST["kontak"] ?? "");


/* Memeriksa kelengkapan data */

if (
    !in_array($jenis, ["hilang", "temuan"], true) ||
    $nama === "" ||
    $gedung === "" ||
    $barang === "" ||
    $ciri === "" ||
    $lokasi === "" ||
    $tanggal === "" ||
    $kontak === ""
) {

    http_response_code(400);

    echo json_encode([
        "status" => "gagal",
        "pesan" => "Semua kolom wajib harus diisi."
    ]);

    exit;
}


/* Memeriksa apakah gedung tersedia */

$cekGedung = mysqli_prepare(
    $koneksi,
    "SELECT kode_gedung
     FROM gedung
     WHERE kode_gedung = ?"
);

mysqli_stmt_bind_param(
    $cekGedung,
    "s",
    $gedung
);

mysqli_stmt_execute($cekGedung);

$hasilGedung = mysqli_stmt_get_result($cekGedung);

if (mysqli_num_rows($hasilGedung) === 0) {

    http_response_code(400);

    echo json_encode([
        "status" => "gagal",
        "pesan" => "Gedung yang dipilih tidak ditemukan."
    ]);

    exit;
}

mysqli_stmt_close($cekGedung);


/* Mengunggah foto barang */

$foto = null;

if (
    isset($_FILES["foto"]) &&
    $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {

        http_response_code(400);

        echo json_encode([
            "status" => "gagal",
            "pesan" => "Foto gagal diunggah."
        ]);

        exit;
    }

    if ($_FILES["foto"]["size"] > 2 * 1024 * 1024) {

        http_response_code(400);

        echo json_encode([
            "status" => "gagal",
            "pesan" => "Ukuran foto maksimal 2 MB."
        ]);

        exit;
    }

    $info = getimagesize($_FILES["foto"]["tmp_name"]);

    if ($info === false) {

        http_response_code(400);

        echo json_encode([
            "status" => "gagal",
            "pesan" => "File yang dikirim bukan gambar."
        ]);

        exit;
    }

    $ekstensi = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    $mime = $info["mime"];

    if (!isset($ekstensi[$mime])) {

        http_response_code(400);

        echo json_encode([
            "status" => "gagal",
            "pesan" => "Format foto harus JPG, PNG, atau WEBP."
        ]);

        exit;
    }

    $namaFile = bin2hex(random_bytes(16))
                . "." . $ekstensi[$mime];

    $folderUpload = "../uploads/";

    if (!is_dir($folderUpload)) {
        mkdir($folderUpload, 0755, true);
    }

    if (!move_uploaded_file(
        $_FILES["foto"]["tmp_name"],
        $folderUpload . $namaFile
    )) {

        http_response_code(500);

        echo json_encode([
            "status" => "gagal",
            "pesan" => "Foto tidak dapat disimpan."
        ]);

        exit;
    }

    $foto = $namaFile;
}


/* Menyimpan laporan ke database tanpa NIM */

$query = "INSERT INTO laporan
(
    jenis,
    nama_pelapor,
    kode_gedung,
    nama_barang,
    ciri_barang,
    lokasi,
    tanggal,
    kontak,
    foto
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $query);

mysqli_stmt_bind_param(
    $stmt,
    "sssssssss",
    $jenis,
    $nama,
    $gedung,
    $barang,
    $ciri,
    $lokasi,
    $tanggal,
    $kontak,
    $foto
);


/* Memeriksa hasil penyimpanan */

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "status" => "berhasil",
        "pesan" => "Laporan berhasil disimpan."
    ]);

} else {

    if (
        $foto !== null &&
        file_exists("../uploads/" . $foto)
    ) {
        unlink("../uploads/" . $foto);
    }

    http_response_code(500);

    echo json_encode([
        "status" => "gagal",
        "pesan" => "Laporan gagal disimpan."
    ]);
}

mysqli_stmt_close($stmt);

mysqli_close($koneksi);

?>
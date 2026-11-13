
<?php
header("Content-Type: application/json; charset=UTF-8");

ini_set("display_errors", "0");

require_once __DIR__ . "/../config/koneksi.php";

$sql = "SELECT kode_gedung, nama_gedung
        FROM gedung
        ORDER BY nama_gedung ASC";

$hasil = $koneksi->query($sql);

if (!$hasil) {
    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "pesan" => "Gagal mengambil daftar gedung."
    ]);

    exit;
}

$data = [];

while ($baris = $hasil->fetch_assoc()) {
    $data[] = $baris;
}

echo json_encode([
    "status" => "success",
    "data" => $data
], JSON_UNESCAPED_UNICODE);

$koneksi->close();
?>

<?php
header("Content-Type: application/json");

require_once "../config/koneksi.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode([
        "pesan" => "ID laporan tidak valid."
    ]);
    exit;
}

$query = "SELECT laporan.*, gedung.nama_gedung
          FROM laporan
          JOIN gedung
          ON laporan.kode_gedung = gedung.kode_gedung
          WHERE laporan.id = ?";

$stmt = mysqli_prepare($koneksi, $query);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    http_response_code(404);
    echo json_encode([
        "pesan" => "Laporan tidak ditemukan."
    ]);
} else {
    echo json_encode($data);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
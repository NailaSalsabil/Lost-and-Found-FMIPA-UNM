<?php
header("Content-Type: application/json");

require_once "../config/koneksi.php";

$cari = trim($_GET["cari"] ?? "");
$gedung = trim($_GET["gedung"] ?? "");

$query = "SELECT laporan.*, gedung.nama_gedung
          FROM laporan
          JOIN gedung
          ON laporan.kode_gedung = gedung.kode_gedung
          WHERE 1=1";

$params = [];
$types = "";

if ($cari !== "") {
    $query .= " AND laporan.nama_barang LIKE ?";
    $params[] = "%" . $cari . "%";
    $types .= "s";
}

if ($gedung !== "") {
    $query .= " AND laporan.kode_gedung = ?";
    $params[] = $gedung;
    $types .= "s";
}

$query .= " ORDER BY laporan.created_at DESC";

$stmt = mysqli_prepare($koneksi, $query);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
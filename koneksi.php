
<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "fmipa_lost_found";

mysqli_report(MYSQLI_REPORT_OFF);

$koneksi = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($koneksi->connect_error) {
    error_log("Koneksi database gagal: " . $koneksi->connect_error);
    die("Koneksi database gagal.");
}

$koneksi->set_charset("utf8mb4");
?>

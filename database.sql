CREATE DATABASE IF NOT EXISTS fmipa_lost_found;
USE fmipa_lost_found;

CREATE TABLE IF NOT EXISTS gedung (
    kode_gedung VARCHAR(10) PRIMARY KEY,
    nama_gedung VARCHAR(100) NOT NULL
);

INSERT IGNORE INTO gedung (kode_gedung, nama_gedung) VALUES
('MF', 'Menara FMIPA'),
('A', 'Laboratorium Fisika'),
('B', 'Laboratorium Geografi'),
('C', 'Laboratorium Biologi'),
('D', 'Laboratorium Kimia'),
('E', 'Ruang Kuliah'),
('F', 'Science Convention Hall dan Ruang Kuliah'),
('G', 'Jurusan Statistika'),
('H', 'Jurusan Matematika'),
('I', 'Laboratorium Pendidikan IPA'),
('LK', 'Sekretariat Lembaga Kemahasiswaan'),
('Ink', 'Inkubator FMIPA'),
('SC', 'Sport Center'),
('WM', 'Workshop Matematika'),
('Kt', 'Kantin'),
('BS', 'Bank Sampah Unit FMIPA'),
('Pg', 'Panggung'),
('Gd', 'Gudang'),
('TM', 'Taman'),
('TB', 'Taman Baca'),
('PK', 'Parkiran'),
('EL', 'Gardu Listrik'),
('LR', 'Lorong/Penghubung');

CREATE TABLE IF NOT EXISTS laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jenis ENUM('hilang', 'temuan') NOT NULL,
    nama_pelapor VARCHAR(100) NOT NULL,
    kode_gedung VARCHAR(10) NOT NULL,
    nama_barang VARCHAR(150) NOT NULL,
    ciri_barang TEXT NOT NULL,
    lokasi VARCHAR(150) NOT NULL,
    tanggal DATE NOT NULL,
    kontak VARCHAR(50) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kode_gedung)
        REFERENCES gedung(kode_gedung)
);
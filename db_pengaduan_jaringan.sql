CREATE TABLE pengaduan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_tiket VARCHAR(20) UNIQUE NOT NULL,
    nama_pelapor VARCHAR(100) NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    jenis_masalah ENUM('LPT/Kabel', 'WiFi/Sinyal', 'Internet Lambat', 'Lainnya') NOT NULL,
    deskripsi TEXT NOT NULL,
    foto VARCHAR(255) NULL,
    status ENUM('Pending', 'Diproses', 'Selesai', 'Ditolak') DEFAULT 'Pending',
    catatan_teknisi TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
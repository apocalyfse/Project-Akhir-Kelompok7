DROP DATABASE IF EXISTS db_kantinku;
CREATE DATABASE db_kantinku CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_kantinku;

-- 1. users: pelanggan (pembeli) dan penjual
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(50) NOT NULL,
  role ENUM('pelanggan','penjual') NOT NULL DEFAULT 'pelanggan'
);

-- 2. stan: satu penjual punya satu stan
CREATE TABLE stan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT NOT NULL,
  nama_stan VARCHAR(100) NOT NULL,
  FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. menu milik stan
CREATE TABLE menu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_stan INT NOT NULL,
  nama_menu VARCHAR(100) NOT NULL,
  kategori ENUM('Makanan','Minuman','Camilan') NOT NULL,
  harga INT NOT NULL,
  stok INT NOT NULL DEFAULT 0,
  FOREIGN KEY (id_stan) REFERENCES stan(id) ON DELETE CASCADE
);

-- 4. slot jam pengambilan (kuota berlaku per stan per tanggal)
CREATE TABLE slot (
  id INT AUTO_INCREMENT PRIMARY KEY,
  jam_mulai TIME NOT NULL,
  jam_selesai TIME NOT NULL,
  kuota INT NOT NULL DEFAULT 8
);

-- 5. pesanan (satu pesanan = satu stan)
CREATE TABLE pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  id_user INT NOT NULL,
  id_stan INT NOT NULL,
  id_slot INT NOT NULL,
  tanggal DATE NOT NULL,
  total INT NOT NULL,
  status ENUM('menunggu','dimasak','siap','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  catatan VARCHAR(200),
  FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (id_stan) REFERENCES stan(id) ON DELETE CASCADE,
  FOREIGN KEY (id_slot) REFERENCES slot(id)
);

-- 6. isi pesanan (nama dan harga disalin, jadi riwayat aman walau menu dihapus)
CREATE TABLE detail_pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan INT NOT NULL,
  id_menu INT NOT NULL,
  nama_menu VARCHAR(100) NOT NULL,
  harga INT NOT NULL,
  jumlah INT NOT NULL,
  FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE
);

-- ===== DATA CONTOH =====
INSERT INTO users (id, username, email, password, role) VALUES
(1, 'bu_ratna',   'ratna@gmail.com',  '123456', 'penjual'),
(2, 'pak_hadi',   'hadi@gmail.com',   '123456', 'penjual'),
(3, 'bu_wati',    'wati@gmail.com',   '123456', 'penjual'),
(4, 'petugas_buu','buu@gmail.com',    '123456', 'penjual'),
(5, 'mahasiswa1', 'mhs1@gmail.com',   '123456', 'pelanggan'),
(6, 'mahasiswa2', 'mhs2@gmail.com',   '123456', 'pelanggan'),
(7, 'dosen1',     'dosen1@gmail.com', '123456', 'pelanggan');

INSERT INTO stan (id, id_user, nama_stan) VALUES
(1, 1, 'Konter Lontong Opor & Pecel'),
(2, 2, 'Konter Nasi Rames & Soto'),
(3, 3, 'Konter Nasi Goreng & Rica-rica'),
(4, 4, 'Kedai Minuman BUU');

INSERT INTO menu (id, id_stan, nama_menu, kategori, harga, stok) VALUES
(1,  1, 'Lontong Opor Ayam',    'Makanan', 13000, 20),
(2,  1, 'Nasi Uduk Komplit',    'Makanan', 12000, 20),
(3,  1, 'Gado-gado',            'Makanan', 11000, 20),
(4,  1, 'Nasi Pecel',           'Makanan', 10000, 25),
(5,  1, 'Peyek Kacang',         'Camilan',  3000, 30),
(6,  2, 'Nasi Rames',           'Makanan', 13000, 25),
(7,  2, 'Nasi Soto Ayam',       'Makanan', 12000, 25),
(8,  2, 'Soto Daging',          'Makanan', 15000, 15),
(9,  2, 'Nasi Ayam Goreng',     'Makanan', 15000, 20),
(10, 2, 'Tahu Tempe Goreng',    'Camilan',  3000, 40),
(11, 3, 'Nasi Goreng Spesial',  'Makanan', 14000, 25),
(12, 3, 'Nasi Rica-rica Ayam',  'Makanan', 15000, 20),
(13, 3, 'Nasi Rica-rica Tongkol','Makanan',13000, 20),
(14, 3, 'Mie Goreng',           'Makanan', 12000, 20),
(15, 4, 'Es Teh Manis',         'Minuman',  4000, 60),
(16, 4, 'Teh Hangat',           'Minuman',  3000, 40),
(17, 4, 'Es Jeruk',             'Minuman',  6000, 40),
(18, 4, 'Kopi Hitam',           'Minuman',  5000, 40),
(19, 4, 'Es Kopi Susu',         'Minuman',  8000, 30),
(20, 4, 'Air Mineral',          'Minuman',  3000, 80);

INSERT INTO slot (id, jam_mulai, jam_selesai, kuota) VALUES
(1, '07:30:00', '07:45:00', 8),
(2, '09:30:00', '09:45:00', 8),
(3, '11:30:00', '11:45:00', 8),
(4, '12:00:00', '12:15:00', 8),
(5, '12:30:00', '12:45:00', 8),
(6, '13:00:00', '13:15:00', 8);

INSERT INTO pesanan (id, kode, id_user, id_stan, id_slot, tanggal, total, status, catatan) VALUES
(1, 'KTN-1001', 5, 1, 3, '2026-11-16', 16000, 'menunggu', 'Sambal dipisah'),
(2, 'KTN-1002', 6, 2, 4, '2026-11-16', 25000, 'dimasak',  ''),
(3, 'KTN-1003', 5, 4, 4, '2026-11-16',  8000, 'siap',     ''),
(4, 'KTN-1004', 6, 1, 2, '2026-11-13', 22000, 'selesai',  ''),
(5, 'KTN-1005', 7, 3, 3, '2026-11-13', 14000, 'selesai',  '');

INSERT INTO detail_pesanan (id_pesanan, id_menu, nama_menu, harga, jumlah) VALUES
(1, 1,  'Lontong Opor Ayam',   13000, 1),
(1, 5,  'Peyek Kacang',         3000, 1),
(2, 6,  'Nasi Rames',          13000, 1),
(2, 7,  'Nasi Soto Ayam',      12000, 1),
(3, 19, 'Es Kopi Susu',         8000, 1),
(4, 3,  'Gado-gado',           11000, 2),
(5, 11, 'Nasi Goreng Spesial', 14000, 1);

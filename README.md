# 📷 SPK Pemilihan Kamera Mirrorless

### Sistem Pendukung Keputusan Kamera Mirrorless Terbaik untuk Fotografer Pemula

Aplikasi web berbasis **Laravel 13** yang menerapkan metode  
**Technique for Order Preference by Similarity to Ideal Solution (TOPSIS)**.

</div>

---

## 📖 Tentang Aplikasi

Aplikasi ini digunakan untuk membantu fotografer pemula menentukan kamera mirrorless yang paling sesuai berdasarkan sejumlah kriteria.

Setiap alternatif kamera dibandingkan menggunakan data numerik, kemudian diproses menggunakan metode TOPSIS untuk menghasilkan nilai preferensi dan urutan rekomendasi.

Sistem dikembangkan secara **dinamis dan database-driven**, sehingga jumlah alternatif maupun kriteria dapat ditambah tanpa mengubah algoritma perhitungan.

---

## ✨ Fitur Utama

- Login dan logout administrator.
- Dashboard statistik dan rekomendasi terbaru.
- CRUD alternatif kamera secara dinamis.
- CRUD kriteria secara dinamis.
- Dukungan atribut `benefit` dan `cost`.
- Dukungan satuan pada setiap kriteria.
- Input matriks keputusan dinamis.
- Bobot relatif dengan nilai bebas.
- Normalisasi bobot otomatis.
- Perhitungan TOPSIS lengkap.
- Penyimpanan riwayat perhitungan.
- Deteksi hasil lama ketika data berubah.
- Grafik hasil perangkingan.
- Cetak laporan.
- Ekspor hasil ke PDF.
- Ekspor hasil ke CSV.
- Tampilan responsif pada desktop, tablet, dan perangkat seluler.
- Unit test untuk algoritma TOPSIS.

---

## 🧮 Tahapan Perhitungan TOPSIS

Sistem menjalankan metode TOPSIS melalui tahapan berikut:

1. Membentuk matriks keputusan **X**.
2. Menormalisasi bobot kriteria.
3. Menghitung pembagi setiap kriteria.
4. Membentuk matriks keputusan ternormalisasi **R**.
5. Membentuk matriks ternormalisasi terbobot **Y**.
6. Menentukan solusi ideal positif **A+**.
7. Menentukan solusi ideal negatif **A-**.
8. Menghitung jarak terhadap solusi ideal positif **D+**.
9. Menghitung jarak terhadap solusi ideal negatif **D-**.
10. Menghitung nilai preferensi.
11. Mengurutkan alternatif berdasarkan nilai preferensi terbesar.

Alternatif yang memperoleh nilai preferensi tertinggi menjadi alternatif yang paling direkomendasikan.

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Backend | PHP 8.3 dan Laravel 13 |
| Frontend | Blade Template, Bootstrap 5, JavaScript |
| Database | MySQL atau MariaDB |
| ORM | Eloquent ORM |
| Grafik | Chart.js |
| Ekspor PDF | Laravel DOMPDF |
| Version Control | Git dan GitHub |
| Development Server | Laragon |

---

## 📊 Dataset Awal

### Alternatif Kamera

| Kode | Alternatif |
|---|---|
| A1 | Canon EOS R50 |
| A2 | Sony Alpha a6400 |
| A3 | Nikon Z50II |
| A4 | Canon EOS R10 |
| A5 | Fujifilm X-M5 |

### Kriteria

| Kode | Kriteria | Satuan | Jenis | Bobot Input |
|---|---|---|---|---:|
| C1 | Harga bodi | Rupiah | Cost | 30 |
| C2 | Resolusi efektif | Megapiksel | Benefit | 20 |
| C3 | Berat operasional | Gram | Cost | 15 |
| C4 | Daya tahan baterai | Jumlah foto | Benefit | 15 |
| C5 | Kecepatan burst maksimum | FPS | Benefit | 20 |

Berdasarkan dataset awal, **Fujifilm X-M5** memperoleh nilai preferensi tertinggi sekitar:

```text
0,790863
```

Fujifilm X-M5 menempati ranking pertama berdasarkan alternatif, kriteria, nilai, dan bobot awal yang digunakan.

---

## ⚖️ Aturan Bobot

Bobot yang dimasukkan pengguna tidak wajib berjumlah `1,00` atau `100`.

Contoh bobot input:

```text
30, 20, 15, 15, 20
```

Sistem melakukan normalisasi menggunakan rumus:

```text
bobot_normalisasi = bobot_input / total_bobot_input
```

Hasil normalisasi:

```text
0,30; 0,20; 0,15; 0,15; 0,20
```

Total bobot setelah dinormalisasi menjadi:

```text
1,00
```

Ketentuan bobot:

- Bobot harus berupa angka.
- Bobot tidak boleh bernilai negatif.
- Seluruh bobot aktif tidak boleh bernilai nol.
- Kriteria dengan bobot positif akan ikut dihitung.
- Bobot dinormalisasi secara otomatis sebelum digunakan dalam TOPSIS.

---

## 🔄 Sistem Dinamis

Jumlah alternatif dan kriteria tidak dibatasi pada dataset awal.

### Penambahan Kriteria

Ketika kriteria baru ditambahkan:

1. Kriteria otomatis muncul pada halaman penilaian.
2. Pengguna mengisi nilai kriteria untuk seluruh alternatif.
3. Bobot baru ikut dinormalisasi.
4. Sistem menghitung ulang seluruh tahapan TOPSIS.
5. Ranking baru dihasilkan berdasarkan data terbaru.

### Penambahan Alternatif

Ketika alternatif baru ditambahkan:

1. Alternatif otomatis muncul pada matriks penilaian.
2. Pengguna mengisi nilai seluruh kriteria.
3. Alternatif ikut dihitung dalam proses TOPSIS.
4. Hasil perangkingan diperbarui secara otomatis.

---

## 📋 Persyaratan Sistem

Pastikan perangkat telah memiliki:

- PHP 8.3 atau lebih baru.
- Composer.
- MySQL atau MariaDB.
- Git.
- Laragon untuk pengguna Windows.
- Ekstensi PHP yang dibutuhkan Laravel.

Periksa versi PHP:

```powershell
php -v
```

Periksa versi Composer:

```powershell
composer --version
```

---

## 🚀 Instalasi

### 1. Clone Repository

```powershell
git clone https://github.com/USERNAME_GITHUB/spk-topsis-kamera.git
cd spk-topsis-kamera
```

Ganti `USERNAME_GITHUB` dengan username GitHub pemilik repository.

### 2. Instal Dependensi

```powershell
composer install
```

### 3. Buat File Environment

Untuk PowerShell:

```powershell
Copy-Item .env.example .env
```

Untuk Command Prompt:

```cmd
copy .env.example .env
```

### 4. Buat Database

Buka phpMyAdmin atau HeidiSQL, kemudian jalankan:

```sql
CREATE DATABASE spk_topsis_kamera
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

### 5. Atur Koneksi Database

### 6. Generate Application Key

```powershell
php artisan key:generate
```

### 7. Jalankan Migration dan Seeder

```powershell
php artisan migrate:fresh --seed
```

> **Peringatan:** Perintah `migrate:fresh` akan menghapus seluruh tabel pada database yang sedang digunakan, kemudian membuatnya kembali.

### 8. Bersihkan Cache

```powershell
php artisan optimize:clear
```

### 9. Jalankan Aplikasi

```powershell
php artisan serve
```

Buka aplikasi melalui:

```text
http://127.0.0.1:8000
```

---

## 🧪 Pengujian

Jalankan seluruh pengujian dengan perintah:

```powershell
php artisan test
```

Pengujian mencakup:

- Perhitungan dataset awal.
- Normalisasi bobot.
- Konsistensi hasil pada skala bobot berbeda.
- Perhitungan nilai preferensi.
- Urutan hasil perangkingan.

---

## 🗃️ Struktur Data Penilaian

Nilai alternatif disimpan menggunakan relasi:

```text
alternative_id
criterion_id
value
```

Struktur tersebut memungkinkan penambahan alternatif dan kriteria tanpa membuat kolom database baru.

Contoh:

```text
Canon EOS R50 + Harga bodi = 10999000
Canon EOS R50 + Resolusi efektif = 24.2
Canon EOS R50 + Berat operasional = 375
```

---

## 📄 Ekspor dan Laporan

Sistem menyediakan beberapa bentuk keluaran:

- Halaman hasil perangkingan.
- Detail seluruh tahapan perhitungan TOPSIS.
- Grafik nilai preferensi.
- Cetak laporan melalui browser.
- Ekspor file PDF.
- Ekspor file CSV.
- Riwayat perhitungan sebelumnya.

Setiap riwayat menyimpan snapshot data sehingga hasil lama tetap dapat diperiksa meskipun data master mengalami perubahan.

---

## 📁 Struktur Folder Utama

```text
app/
├── Http/
├── Models/
└── Services/

database/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
└── views/

routes/
└── web.php

tests/
├── Feature/
└── Unit/
```

## 🌱 Pengembangan Selanjutnya

Beberapa fitur yang dapat dikembangkan:

- Impor alternatif melalui Excel.
- Impor kriteria melalui Excel.
- Impor matriks penilaian melalui CSV.
- Manajemen banyak pengguna.
- Pengaturan bobot per pengguna.
- Perbandingan TOPSIS dengan SAW atau SMART.
- API untuk integrasi aplikasi lain.
- Deployment ke server produksi.

---

## 📜 Lisensi

Proyek ini dikembangkan untuk kebutuhan pembelajaran dan tugas akademik.

---

<div align="center">

### SPK Pemilihan Kamera Mirrorless dengan Metode TOPSIS

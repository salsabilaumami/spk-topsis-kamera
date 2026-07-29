# SPK Pemilihan Kamera Mirrorless — Laravel 13 + TOPSIS

Aplikasi web responsif dan database-driven untuk menentukan kamera mirrorless terbaik bagi fotografer pemula menggunakan metode **TOPSIS**.

## Fitur

- Login administrator.
- Dashboard statistik, rekomendasi terbaru, kesiapan data, dan grafik ranking.
- CRUD alternatif kamera secara dinamis.
- CRUD kriteria dinamis dengan atribut benefit/cost dan satuan.
- Bobot relatif bebas, misalnya `30, 20, 15, 15, 20`.
- Normalisasi bobot otomatis menjadi total `1,00` saat proses TOPSIS.
- Input matriks keputusan dinamis berdasarkan alternatif dan kriteria terbaru.
- Perhitungan lengkap: X, normalisasi bobot, pembagi, R, Y, A+, A-, D+, D-, preferensi, dan ranking.
- Snapshot riwayat perhitungan.
- Deteksi hasil lama setelah data master atau penilaian berubah.
- Cetak laporan serta ekspor PDF dan CSV.
- Unit test untuk dataset kamera awal dan pengujian skala bobot.

## Dataset awal

### Alternatif

1. Canon EOS R50
2. Sony Alpha a6400
3. Nikon Z50II
4. Canon EOS R10
5. Fujifilm X-M5

### Kriteria

| Kode | Kriteria | Jenis | Bobot input |
|---|---|---|---:|
| C1 | Harga bodi | Cost | 30 |
| C2 | Resolusi efektif | Benefit | 20 |
| C3 | Berat operasional | Cost | 15 |
| C4 | Daya tahan baterai | Benefit | 15 |
| C5 | Kecepatan burst maksimum | Benefit | 20 |

Hasil dataset awal menempatkan **Fujifilm X-M5** pada ranking pertama dengan nilai preferensi sekitar **0,790863**.

## Persyaratan

- PHP 8.3 atau lebih baru
- Composer
- MySQL/MariaDB
- Laravel 13
- Laragon direkomendasikan untuk Windows

## Instalasi di Laragon

Ekstrak folder project ke:

```text
C:\laragon\www\spk-topsis-kamera
```

Buka terminal pada folder project, lalu buat database:

```sql
CREATE DATABASE spk_topsis_kamera
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Pastikan `.env` berisi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spk_topsis_kamera
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan:

```powershell
composer install
php artisan key:generate
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

## Akun awal

```text
Email    : admin@spkkamera.test
Password : admin12345
```

## Pengujian

```powershell
php artisan test
```

## Aturan bobot

Bobot input tidak wajib berjumlah `1,00` atau `100`. Semua bobot aktif harus positif. Sistem memakai:

```text
bobot_normalisasi = bobot_input / total_bobot_input
```

Contoh bobot `30, 20, 15, 15, 20` dinormalisasi menjadi `0,30; 0,20; 0,15; 0,15; 0,20`.

Ketika kriteria baru ditambahkan, isi bobot positif dan nilai kriteria tersebut untuk seluruh alternatif. Setelah data lengkap, TOPSIS dapat dihitung kembali dan menghasilkan ranking baru.

<div align="center">
  <img src="public/assets/img/jazzel-monogram.png" alt="Logo Toko Jazzel" width="140">

  # Sistem Kasir Toko Jazzel

  Aplikasi kasir berbasis web untuk mengelola produk, transaksi penjualan, dan laporan toko.
</div>

---

## Tentang Proyek

Sistem Kasir Toko Jazzel membantu admin dan kasir menjalankan kegiatan toko melalui satu aplikasi. Admin dapat mengelola barang dan akun kasir, sedangkan kasir dapat membuat transaksi, menahan transaksi untuk dilanjutkan, serta melihat riwayat dan laporan penjualannya.

## Fitur

- **Dashboard admin** — ringkasan persediaan, barang aktif, stok rendah, dan grafik penjualan.
- **Kelola barang** — tambah, lihat, ubah, hapus, cari barang, dan atur status aktif.
- **Manajemen kasir** — admin dapat membuat akun kasir.
- **Transaksi kasir** — pencarian barang, pengelolaan keranjang, pembayaran tunai, QRIS simulasi, dan transfer.
- **Transaksi tertahan** — simpan transaksi sementara di browser dan lanjutkan pembayarannya nanti.
- **Riwayat transaksi** — pencarian dan filter riwayat transaksi.
- **Laporan penjualan** — ringkasan dan filter laporan berdasarkan hari, minggu, dan bulan.
- **Pengaturan toko** — ubah identitas toko dan logo.
- **Cetak struk** — mendukung printer thermal ESC/POS pada Windows.

> Pembayaran QRIS di aplikasi ini merupakan **simulasi**, bukan pemrosesan pembayaran sungguhan. Transaksi tertahan disimpan di browser yang digunakan dan tidak otomatis tersedia di perangkat lain.

## Teknologi

- PHP 8.2+
- Laravel 12
- MySQL
- Node.js dan npm
- Vite
- Tailwind CSS
- Alpine.js
- [`mike42/escpos-php`](https://github.com/mike42/escpos-php) untuk cetak struk

## Persyaratan

Pastikan perangkat pengembangan sudah memiliki:

- PHP 8.2 atau lebih baru beserta ekstensi yang dibutuhkan Laravel.
- Composer.
- MySQL.
- Node.js dan npm.

## Instalasi dan Menjalankan Aplikasi

Contoh berikut menggunakan PowerShell di Windows.

1. **Clone repository dan masuk ke folder proyek**

   ```powershell
   git clone https://github.com/marquezrona/projek_web_kasir-.git
   cd projek_web_kasir-
   ```

2. **Pasang dependency PHP dan siapkan konfigurasi**

   ```powershell
   composer install
   Copy-Item .env.example .env
   ```

3. **Buat application key, jalankan migrasi dan data awal, lalu siapkan penyimpanan file publik**

   ```powershell
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```

   Seeder membuat akun contoh untuk pengembangan lokal. Ganti kata sandi bawaan dan jangan gunakan akun contoh tersebut untuk deployment.

4. **Pasang dependency frontend dan build aset**

   ```powershell
   npm install
   npm run build
   ```

5. **Jalankan server lokal**

   ```powershell
   php artisan serve
   ```

   Buka alamat yang ditampilkan oleh Artisan, biasanya [http://127.0.0.1:8000](http://127.0.0.1:8000).

Untuk menjalankan Vite dalam mode pengembangan, gunakan terminal terpisah:

```powershell
npm run dev
```

## Cetak Struk

Aplikasi mencetak struk menggunakan printer Windows yang sudah terpasang dan dibagikan. Atur nama printer Windows pada `.env`:

```dotenv
RECEIPT_PRINTER=POS-80
```

Ganti `POS-80` dengan nama printer atau nama share yang sesuai dengan konfigurasi Windows. Tanpa printer yang terhubung, transaksi tetap dapat disimpan, tetapi pencetakan struk tidak akan berhasil.

## Pengujian

Jalankan rangkaian pengujian aplikasi dengan:

```powershell
php artisan test
```

## Lisensi

Proyek ini menggunakan lisensi MIT.

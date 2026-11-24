# FlipPlay REST API (Laravel 12)

API FlipPlay untuk mengelola katalog media (Film, Series, Anime, Podcast, dll.) dan koleksi pribadi pengguna dengan relasi Many-to-Many (`belongsToMany`), Form Request Validation, API Resource, dan Custom Action.

---

## 🛠️ Persyaratan Sistem
- PHP >= 8.2 (dengan extension `pdo_mysql`, `zip`, `mbstring`)
- MySQL / MariaDB (Laragon)
- Composer

---

## 🚀 Panduan Instalasi & Menjalankan

1. **Clone repository dan masuk ke direktori**:
   ```bash
   git clone <url-repository-anda>
   cd flipplay-api
   ```

2. **Setup Environment**:
   Salin file `.env.example` ke `.env` (jika belum ada) dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=flipplay_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Install Dependensi & Generate Key**:
   ```bash
   composer install
   php artisan key:generate
   ```

4. **Jalankan Migrasi dan Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan di `http://localhost:8000`.

---

## 📌 Daftar Endpoint API

Verifikasi endpoint dengan menjalankan:
```bash
php artisan route:list --path=api
```

### 1. Katalog Media (CRUD Standar)
| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/media` | Menampilkan seluruh media (dukung query `?search=...`, `?type=...`, `?genre=...`, `?sort_by=...`) |
| `POST` | `/api/media` | Menambahkan media baru (Validasi: judul unik, type, genre) |
| `GET` | `/api/media/{id}` | Menampilkan detail 1 media beserta daftar pengguna yang mengoleksinya (`whenLoaded('users')`) |
| `PUT/PATCH` | `/api/media/{id}` | Memperbarui data media |
| `DELETE` | `/api/media/{id}` | Menghapus media dari katalog |

### 2. Koleksi Pengguna (Custom Actions & Relasi Many-to-Many)
| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/users/{userId}/collection` | Menampilkan daftar media yang dikoleksi oleh pengguna tertentu |
| `POST` | `/api/users/{userId}/collection` | **Custom Action**: Menambahkan media ke koleksi pengguna (atau update status jika sudah ada) |
| `DELETE` | `/api/users/{userId}/collection/{mediaId}` | **Custom Action**: Menghapus media dari koleksi pengguna |

---

## 🧪 Pengujian Otomatis (Testing)
Jalankan pengujian unit dan fitur API:
```bash
php artisan test
```
Semua 11 skenario pengujian endpoint, validasi unik, pivot status koleksi, filter search, dan CRUD telah lolos 100%.

---

## 💡 Contoh Payload JSON untuk Postman / Client

### 1. Menambahkan Media Baru (`POST /api/media`)
```json
{
  "title": "Spider-Man: Across the Spider-Verse",
  "type": "Movie",
  "genre": "Animation / Action",
  "release_year": 2023,
  "duration_minutes": 140,
  "rating": 8.7,
  "description": "Miles Morales catapults across the Multiverse."
}
```

### 2. Menambahkan Media ke Koleksi Pengguna (`POST /api/users/1/collection`)
```json
{
  "media_id": 1,
  "status": "watching",
  "notes": "Sedang nonton ulang bagian klimaks.",
  "personal_rating": 10
}
```
*(Nilai status yang didukung: `plan_to_watch`, `watching`, `completed`, `on_hold`, `dropped`)*

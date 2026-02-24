# Rekap Pajak - Laravel 12 Desktop/Web Application

Aplikasi pengelolaan rekap pajak menggunakan Laravel 12, Vue 3 (Inertia.js), MySQL, dan Docker (FrankenPHP).

## Quick Start (Mode Development)

Gunakan mode ini untuk pengembangan. Perubahan file di host akan langsung terbaca di dalam container (Hot Reload).

1.  **Persiapkan Environment**
    ```bash
    cp .env.example .env
    # Sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD jika perlu
    ```

2.  **Jalankan Docker Compose Dev**
    ```bash
    docker-compose up -d --build
    ```

3.  **Setup Database & Dependencies**
    ```bash
    # Install Composer dependencies (di dalam container)
    docker-compose exec app composer install

    # Generate App Key
    docker-compose exec app php artisan key:generate

    # Run Migrations & Seeds
    docker-compose exec app php artisan migrate --seed
    ```

4.  **Akses Aplikasi**
    - Web: [http://localhost:8000](http://localhost:8000)
    - PhpMyAdmin: [http://localhost:8080](http://localhost:8080)
    - Vite HMR: Port 5174

---

## Production Deployment (Mode Production)

Gunakan mode ini untuk simulasi atau deployment production. Gambar (image) lebih ringan dan aset sudah di-build secara statis.

1.  **Pastikan `.env` sudah sesuai**
    Set variabel berikut untuk testing lokal mode prod:
    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_FORCE_HTTPS=false
    ```

2.  **Jalankan Docker Compose Prod**
    ```bash
    docker-compose -f docker-compose.prod.yml up -d --build
    ```

3.  **Inisialisasi Database Production**
    ```bash
    # Jalankan migrasi core dan seluruh module
    docker-compose -f docker-compose.prod.yml exec app php artisan migrate --path=database/migrations --path=Modules/Admin/database/migrations --path=Modules/Pajak/database/migrations --path=Modules/Dokumen/database/migrations --force

    # Jalankan Seeder
    docker-compose -f docker-compose.prod.yml exec app php artisan db:seed --force
    ```

4.  **Akses Aplikasi Production**
    - Web: [http://localhost:8087](http://localhost:8087)

---

## Perintah Penting Lainnya

### Mengelola Module
```bash
# Cek daftar module
docker-compose exec app php artisan module:list

# Enable module tertentu
docker-compose exec app php artisan module:enable Admin Pajak Dokumen Tools
```

### Optimasi (Hanya di Prod)
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan optimize
```

### Cek Logs
```bash
docker-compose -f docker-compose.prod.yml logs -f app
```

## Struktur Port
| Service | Development | Production |
| :--- | :--- | :--- |
| **App (HTTP)** | 8000 | 8087 |
| **MySQL** | 3307 | 3308 |
| **PhpMyAdmin**| 8080 | - |
| **Vite** | 5174 | - |

---

## Deployment ke Domain (Production)

Jika aplikasi dideploy ke domain asli (misal: `https://rekap-pajak.sumbarprov.go.id`), lakukan penyesuaian berikut di `.env`:

1.  **Update URL & HTTPS**
    ```env
    APP_URL=https://rekap-pajak.sumbarprov.go.id
    APP_FORCE_HTTPS=true
    SESSION_SECURE_COOKIE=true
    ```

2.  **Update Keycloak Redirect**
    ```env
    KEYCLOAK_REDIRECT_URI=https://rekap-pajak.sumbarprov.go.id/auth/sso/callback
    ```

3.  **Keycloak Admin Dashboard**
    - Tambahkan domain ke **Valid Redirect URIs**.
    - Tambahkan domain ke **Web Origins** (CORS).

4.  **Reverse Proxy (Nginx/Traefik)**
    Jika menggunakan Nginx sebagai proxy, pastikan header `X-Forwarded-Proto` diteruskan agar Laravel mendeteksi HTTPS dengan benar.

---

## Deployment ke Subpath (contoh: /app)

Jika aplikasi diletakkan di `domain.com/app`, lakukan penyesuaian tambahan:

1.  **Update `.env`**
    ```env
    APP_URL=https://domain Utama.com/app
    ASSET_URL=/app
    ```

2.  **Update `vite.config.ts`**
    Tambahkan properti `base`:
    ```typescript
    export default defineConfig({
        base: '/app/',
        // ... plugins
    });
    ```

3.  **Update Nginx Proxy**
    ```nginx
    location /app/ {
        proxy_pass http://localhost:8087/;
        proxy_set_header X-Forwarded-Prefix /app;
    }
    ```

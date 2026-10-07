# ERD Dashboard HSE — v1 (Laravel + MySQL)

Scope: dashboard dulu. Skema siap full flow (regis pekerja, IBPR, tensi harian, ID card).

## 1. Aturan ID

- Site fix: `01` Jember, `02` Situbondo, `03` Bondowoso, `04` Probolinggo, `05` Lumajang
- Pekerja: `{SITE}-{URUT3}` contoh `01-001`. URUT naik per site, bukan global. Unique PK.
- HSE admin (seed, 1 orang): `ADM-001`
- HSE inspector (dibuat admin, 1/kabupaten): `INS-{SITE}-{URUT3}` contoh `INS-02-001`. Kolom `site_code` kunci wilayah.
- Login v1: ID saja (tanpa password). Kolom `pin` disiapkan untuk hardening (nullable).
- QR homepage: isi URL home. QR ID card: isi ID pekerja (`01-001`).

## 2. Diagram

```mermaid
erDiagram
  SITES ||--o{ WORKERS : "01-05"
  SITES ||--o{ USERS : "lock inspector"
  SITES ||--o{ IBPR_REPORTS : "lokasi"
  USERS ||--o{ HEALTH_CHECKS : "input inspector"
  USERS ||--o{ IBPR_REPORTS : "input inspector"
  WORKERS ||--o{ HEALTH_CHECKS : "1/hari"

  SITES {
    char(2) code PK
    string name
  }
  USERS {
    string id PK "ADM-001, INS-01-001"
    enum role "admin,inspector"
    char(2) site_code FK_NULL "null=admin semua, isi=lock 1 site"
    string name
    string pin_NULL "v2 hardening"
    timestamps created_at
  }
  WORKERS {
    string id PK "01-001"
    char(2) site_code FK
    string nama
    string jenis_pekerjaan
    string mandor_subkon
    string foto_path "storage/workers"
    tinyint usia "17-65"
    string asal
    text riwayat_penyakit_NULL
    date tanggal_regis "auto now, untuk lama_bekerja"
    timestamps created_at
  }
  HEALTH_CHECKS {
    bigint id PK
    string worker_id FK
    date tanggal "unique per worker per hari"
    smallint sistol "90-120 normal"
    smallint diastol "60-80 normal"
    decimal suhu_4_1 "36.5-37.5 normal"
    string inspector_id FK_USERS
    enum status "NORMAL,FLAG"
    timestamps created_at
  }
  IBPR_REPORTS {
    bigint id PK
    char(2) site_code FK
    datetime tanggal_realtime "server Asia/Jakarta"
    text kegiatan
    text bahaya
    text risiko
    tinyint likelihood "1-5"
    tinyint severity "1-5"
    smallint skor "auto LxS"
    enum level "LOW,MEDIUM,HIGH,EXTREME"
    text pengendalian "wajib jika HIGH/EXTREME"
    string inspector_id FK_USERS
    timestamps created_at
  }
  RISK_MATRIX {
    tinyint likelihood PK
    tinyint severity PK
    smallint skor
    enum level
  }
```

## 3. Tabel detail + index

### sites (master, seed 5)
| kolom | tipe | ket |
|---|---|---|
| code | CHAR(2) PK | 01-05 |
| name | VARCHAR(50) | Jember dst |

### users (admin + inspector)
| kolom | tipe | ket |
|---|---|---|
| id | VARCHAR(20) PK | ADM-001 / INS-02-001 |
| role | ENUM | admin, inspector |
| site_code | CHAR(2) NULL FK | null=semua (admin), isi=lock inspector |
| name | VARCHAR(100) | nama HSE |
| pin | VARCHAR(255) NULL | hash, v2 (v1 null = login ID saja) |
| created_at/updated_at | timestamps | |

Index: `(role, site_code)`.

### workers (banyak orang, daftar sendiri)
| kolom | tipe | ket |
|---|---|---|
| id | VARCHAR(10) PK | 01-001, generate counter per site |
| site_code | CHAR(2) FK | lock lokasi sesuai ID |
| nama | VARCHAR(100) | index fulltext/search |
| jenis_pekerjaan | VARCHAR(100) | tulis bebas |
| mandor_subkon | VARCHAR(100) | |
| foto_path | VARCHAR(255) | wajib, max 2MB jpg/png |
| usia | TINYINT | 17-65 |
| asal | VARCHAR(100) | daerah asal |
| riwayat_penyakit | TEXT NULL | |
| tanggal_regis | DATE | default today, sumber lama_bekerja |
| created_at/updated_at | timestamps | |

Counter: tabel `site_counters(site_code PK, last_no INT)` untuk auto `01-001` aman concurrent. Atau `MAX()+1` dengan transaction lock (cukup MVP).
`lama_bekerja` = derived `DATEDIFF(now, tanggal_regis)`, tidak disimpan.

Index: `(site_code, nama)`, `(tanggal_regis)`.

### health_checks (tensi+suhu harian, oleh inspector)
| kolom | tipe | ket |
|---|---|---|
| id | BIGINT PK | |
| worker_id | VARCHAR(10) FK workers.id cascade | |
| tanggal | DATE | bagian dari unique |
| sistol | SMALLINT | 70-220 validasi |
| diastol | SMALLINT | 40-130 validasi |
| suhu | DECIMAL(3,1) | 34.0-42.0, contoh 36.8 |
| inspector_id | VARCHAR(20) FK users.id | |
| status | ENUM | NORMAL / FLAG (auto jika di luar ambang) |
| created_at | timestamp | |

Unique: `(worker_id, tanggal)` = 1/hari/riwayat. Update hari sama = update, bukan insert baru.
Ambang v1: sistol 90-120, diastol 60-80, suhu 36.5-37.5. Satu saja di luar = FLAG merah.
Index: `(tanggal, worker_id)`, `(inspector_id, tanggal)`.

### ibpr_reports (input/lihat oleh inspector)
| kolom | tipe | ket |
|---|---|---|
| id | BIGINT PK | |
| site_code | CHAR(2) FK | |
| tanggal_realtime | DATETIME | default now Asia/Jakarta |
| kegiatan | TEXT | kegiatan pekerjaan |
| bahaya | TEXT | |
| risiko | TEXT | potensi risiko |
| likelihood | TINYINT | 1-5 |
| severity | TINYINT | 1-5 |
| skor | SMALLINT | auto LxS |
| level | ENUM | LOW/MEDIUM/HIGH/EXTREME via matrix |
| pengendalian | TEXT | wajib jika HIGH/EXTREME |
| inspector_id | VARCHAR(20) FK | |
| created_at/updated_at | timestamps | |

Index: `(site_code, tanggal_realtime)`, `(level, site_code)`.

### risk_matrix (seed 25 sel, persis gambar 2.png)
Skor = LxS, level ikut sel (bukan threshold skor, karena 15=EXTREME tapi 16=HIGH).

```
L\S: 1,2,3,4,5
1: 1/LOW, 2/LOW, 3/LOW, 4/MEDIUM, 5/MEDIUM
2: 2/LOW, 4/MEDIUM, 6/MEDIUM, 8/HIGH, 10/HIGH
3: 3/LOW, 6/MEDIUM, 9/HIGH, 12/HIGH, 15/EXTREME
4: 4/MEDIUM, 8/HIGH, 12/HIGH, 16/HIGH, 20/EXTREME
5: 5/MEDIUM, 10/HIGH, 15/EXTREME, 20/EXTREME, 25/EXTREME
```

Seed 25 row. App lookup `(likelihood, severity)` -> `(skor, level)`.

### site_counters (helper ID auto)
| kolom | tipe | ket |
|---|---|---|
| site_code | CHAR(2) PK FK | |
| last_no | INT default 0 | increment -> format LPAD 3 `001` |

## 4. Hak akses dashboard (tabel + filter)

- Admin: semua site, semua tabel. Filter site (01-05 + Semua), tanggal, search nama/ID.
- Inspector: hanya `site_code` miliknya. Dropdown site terkunci. Bisa input tensi + IBPR wilayahnya. Lihat data per orang di kabupaten dipegang.
- Pekerja (login ID `01-001`): hanya site sesuai ID + data diri + tensi hari ini + daftar IBPR site-nya. Read-only.

Dashboard v1 kolom:
- Pekerja: foto, ID, nama, site, jenis, mandor, usia, riwayat, tensi+suhu hari ini + status FLAG, lama_bekerja (auto hari).
- IBPR: tanggal, site, kegiatan, bahaya, risiko, L, S, skor, level warna, pengendalian.

## 5. Seed awal

- sites 5 row.
- risk_matrix 25 row.
- users: `ADM-001` admin (nama HSE Admin). Inspector contoh `INS-01-001` s/d `INS-05-001` (dibuat admin via UI nanti, seed opsional).
- site_counters 5 row `last_no=0`.

## 6. Keputusan tunda / butuh konfirmasi

1. Login ID saja tanpa PIN rawan tebak (`01-001` mudah). Saran v2: tambah PIN 4-6 digit untuk HSE. Pekerja tetap ID saja oke.
2. Foto: storage lokal `storage/app/public/workers` + `php artisan storage:link`. Di shared hosting perlu symlink manual.
3. Cetak ID card: ukuran kartu (CR80) + QR isi ID + tombol print CSS. Lib: `simplesoftwareio/simple-qrcode`.
4. Timezone: `Asia/Jakarta` untuk tanggal realtime + tanggal_regis.
5. `Asal` tetap simpan, tampil di detail pekerja (kolom tabel sembunyi, klik row baru muncul) biar tabel tidak lebar.

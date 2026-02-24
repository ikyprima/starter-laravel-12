📑 MD Vibecoder – Form Pelaporan Pajak
1. Tujuan

Menyediakan modul Pelaporan Pajak dengan dua jenis transaksi:

Transaksi LS (Langsung)

Transaksi GU (Ganti Uang)

Setiap transaksi dapat dikelola dengan CRUD, menggunakan dialog modal untuk input/edit, serta validasi NTPN wajib 16 digit dan lookup jenis pajak dari master pajak.

2. Master Data
2.1 Master Akun Pajak

Sumber data dari tabel:

tb_master_akun_pajak
Field	Tipe
id	bigint
kode_akun_pajak	varchar
jenis_pajak	varchar

Digunakan sebagai lookup pada form transaksi.

3. Struktur Menu
Pelaporan Pajak
 ├─ Transaksi LS
 └─ Transaksi GU
4. Transaksi LS
4.1 Tampilan Awal

Dropdown / selector Tahun

Grid Bulan Januari – Desember

Setiap bulan dapat diklik

State awal:
Belum memilih bulan → hanya tampil daftar bulan

4.2 Setelah Bulan Dipilih

Menampilkan tabel daftar transaksi LS untuk bulan & tahun terpilih.

Kolom Tabel
Kolom
Nomor SP2D
Nilai Belanja
Uraian Belanja
Dasar Pengenaan Pajak (DPP)
Kode Akun Pajak
Jenis Pajak
Jumlah Pajak
NPWP
NTPN
Aksi (Edit / Hapus)
4.3 CRUD Transaksi LS
Create / Update

Dibuka dalam Dialog Modal

Tombol: Tambah Transaksi

Field Form
Field	Tipe	Catatan
Nomor SP2D	text	wajib
Nilai Belanja	number	wajib
Uraian Belanja	textarea	wajib
DPP	number	wajib
Kode Akun Pajak	select	dari master pajak
Jenis Pajak	auto	dari master pajak
Jumlah Pajak	number	wajib
NPWP	text	wajib
NTPN	text	wajib 16 digit
Validasi

Semua field wajib

ntpn:

numeric

length = 16

kode_akun_pajak:

foreign key ke master pajak

Delete

Konfirmasi dialog:

“Apakah Anda yakin ingin menghapus transaksi ini?”

5. Transaksi GU
5.1 Tampilan Utama

Langsung menampilkan tabel transaksi GU (tanpa grid bulan).

Kolom Tabel
Kolom
Nomor TBP
Nilai Belanja
Uraian Belanja
Dasar Pengenaan Pajak (DPP)
Kode Akun Pajak
Jenis Pajak
Jumlah Pajak
NPWP
NTPN
Aksi (Edit / Hapus)
5.2 CRUD Transaksi GU
Create / Update

Menggunakan Dialog Modal

Tombol: Tambah Transaksi GU

Field Form
Field	Tipe	Catatan
Nomor TBP	text	wajib
Nilai Belanja	number	wajib
Uraian Belanja	textarea	wajib
DPP	number	wajib
Kode Akun Pajak	select	dari master pajak
Jenis Pajak	auto	dari master pajak
Jumlah Pajak	number	wajib
NPWP	text	wajib
NTPN	text	wajib 16 digit
Validasi

Sama dengan Transaksi LS

ntpn: numeric & length 16

6. Aturan Umum Validasi

Semua input required

NTPN:

regex: ^\d{16}$

Nilai numeric ≥ 0

Jenis Pajak tidak bisa diketik manual

Jenis Pajak otomatis terisi dari Kode Akun Pajak

7. UX / UI Notes

Dialog modal reusable (LS & GU)

Dropdown pajak searchable

Format angka rupiah

Table support pagination & search

Disable submit jika validasi gagal

8. Opsional Pengembangan

Export Excel / PDF per bulan

Rekap pajak per tahun

Lock data jika sudah dilaporkan

Audit trail (created_by, updated_by)

Kalau mau lanjut, aku bisa:

buatkan ERD & struktur tabel LS + GU

buatkan Laravel migration + model

buatkan flow API endpoint

atau contoh component Vue (Dialog + Table)
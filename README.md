# PBB Desa
Plugin wordpress untuk manajemen data PBB di tingkat desa.
Semoga bermanfaat

### DONASI
- Donasi untuk pengembang aplikasi, klik di link ini https://smkasiyahhomeschooling.blogspot.com/p/donasi-pengembangan-smk-asiyah.html

### Cara pakai plugin:
- Install wordpress
- Install plugin ini dan aktifkan

### Fitur:
- Membuat user dengan role petugas pajak
- Membuat manual atau mengimport data wajib pajak dari excel
- Melakukan pembayaran berdasarkan petugas pajak
- Print dan download excel laporan wajib pajak
- Input data profil desa
- Tambah role kepala desa & bendahara desa
- Tambah status pembayaran (Belum Bayar, Diterima Petugas Pajak, Diterima Bendahara Desa, Diterima Kecamatan, Lunas)

Permintaan fitur:
- User umum bisa request penambahan fitur dengan membuat issue

### Demo Aplikasi
- Bisa dilihat di https://pbbdesa.maremjaya.com/

### Video Tutorial 
- ...

### HARUS Update php.ini
Optimasi server apache agar proses import data wajib pajak dari excel berjalan lancar (edit file php.ini):
- max_input_vars = 1000000
- max_execution_time = 300
- max_input_time = 600
- memory_limit = 3556M
- post_max_size = 20M
# PBB Desa
Plugin wordpress untuk manajemen data PBB di tingkat desa.
Semoga bermanfaat

### DONASI
- Donasi untuk pengembang aplikasi, klik di link ini https://smkasiyahhomeschooling.blogspot.com/p/donasi-pengembangan-smk-asiyah.html

### Cara pakai plugin:
- Install wordpress
- Install plugin ini dan aktifkan
- Import SQL file tabel.sql untuk membuat tabel tempat menyimpan data PBB


Permintaan fitur:
- User umum bisa request penambahan fitur dengan membuat issue

### Video Tutorial 
- ...

### HARUS Update php.ini
Optimasi server apache agar proses pengiriman data dari chrome extension ke server wordpress berjalan lancar (edit file php.ini):
- max_input_vars = 1000000
- max_execution_time = 300
- max_input_time = 600
- memory_limit = 3556M
- post_max_size = 20M
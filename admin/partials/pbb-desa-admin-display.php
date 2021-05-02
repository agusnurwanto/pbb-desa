<?php
$input = shortcode_atts( array(
	'tahun_anggaran' => '2021'
), $atts );
global $wpdb;

?>

<h1 style='text-align: center;'>DAFTAR NAMA PELUNASAN PBB TAHUN <?php echo $input['tahun_anggaran']; ?></h1>
<table>
	<thead>
		<tr>
			<th>No.</th>
			<th>NOP</th>
			<th>Nama Wajib Pajak</th>
			<th>Alamat Objek Pajak</th>
			<th>Bayar</th>
			<th>Tgl. Lunas</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>

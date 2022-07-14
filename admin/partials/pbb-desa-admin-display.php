<?php
$input = shortcode_atts( array(
	'tahun_anggaran' => '2021'
), $atts );
global $wpdb;
$body = '<tr><td colspan="9" class="text_tengah">Data kosong. Harap pilih dulu data yang akan ditampilkan!</td></tr>';
$total_nilai = 0;
if(!empty($_GET['data_list'])){
	$list = explode(',', $_GET['data_list']);
	$data_status = $this->functions->data_status_bayar();
	$body_array = [];
	foreach ($list as $k => $id_post) {
		$post = get_post($id_post);
		$nop = get_post_meta( $id_post, '_crb_pbb_nop', true );
		$nama = get_post_meta( $id_post, '_crb_pbb_nama_wp', true );
		$alamat_wp = get_post_meta( $id_post, '_crb_pbb_alamat_wp', true );
		$alamat_op = get_post_meta( $id_post, '_crb_pbb_alamat_op', true );
		$nilai = get_post_meta( $id_post, '_crb_pbb_ketetapan_pbb', true );
		if(empty($nilai)){
			$nilai = 0;
		}
		$total_nilai += $nilai;
		$status_bayar = get_post_meta( $id_post, '_crb_pbb_status_bayar', true );
		if(empty($status_bayar)){
			$status_bayar = 0;
		}
		$petugas_pajak_db = get_post_meta( $id_post, '_crb_pbb_petugas_pajak', true );
		$petugas_pajak = '';
		if(!empty($petugas_pajak_db)){
			$petugas = get_userdata($petugas_pajak_db);
			$petugas_pajak = $petugas->display_name;
		}
		$status_bayar_text = $data_status[$status_bayar];
		$tgl_bayar = '';
		if($status_bayar >= 1){
			$tgl_bayar = get_post_meta( $id_post, '_crb_pbb_tgl_bayar', true );
		}
		if(empty($body_array[date($tgl_bayar)])){
			$body_array[date($tgl_bayar)] = array();
		}
		$body_array[date($tgl_bayar)][] = '
		<tr>
			<td class="text_tengah">{{no}}</td>
			<td class="text_tengah">'.$nop.'</td>
			<td>'.$nama.'</td>
			<td>'.$alamat_wp.'</td>
			<td>'.$alamat_op.'</td>
			<td>'.$petugas_pajak.'</td>
			<td>'.$status_bayar_text.'</td>
			<td class="text_tengah">'.$tgl_bayar.'</td>
			<td class="text_kanan">'.number_format($nilai,0,",",".").'</td>
		</tr>
		';
	}
	krsort($body_array);
	$body = '';
	$no = 0;
	foreach ($body_array as $k => $v) {
		foreach ($v as $body_html) {
			$no++;
			$body .= str_replace(array(
				'{{no}}'
			), array(
				$no
			), $body_html);
		}
		
	}

	$judul = '';
	if(!empty($_GET['judul'])){
		$judul = '<h2 style="text-align: center; text-transform: uppercase;">'.htmlentities(urldecode($_GET['judul'])).'</h2>';
	}
}
?>
<div id="cetak">
	<h1 style='text-align: center;'>DAFTAR NAMA PELUNASAN PBB TAHUN <?php echo $input['tahun_anggaran']; ?></h1>
	<?php echo $judul; ?>
	<table>
		<thead>
			<tr>
				<th class="text_tengah" style="width: 40px;">No.</th>
				<th class="text_tengah" style="width: 190px;">NOP</th>
				<th class="text_tengah" style="width: 150px;">Nama Wajib Pajak</th>
				<th class="text_tengah">Alamat WP</th>
				<th class="text_tengah">Alamat OP</th>
				<th class="text_tengah" style="width: 120px;">Petugas</th>
				<th class="text_tengah" style="width: 240px;">Status Pembayaran</th>
				<th class="text_tengah" style="width: 160px;">Tgl. Transaksi</th>
				<th class="text_tengah" style="width: 140px;">Nilai Pajak (Rp)</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
		<tfoot>
			<tr>
				<th class="text_tengah" colspan="8">Total</th>
				<th class="text_kanan"><?php echo number_format($total_nilai,0,",","."); ?></th>
			</tr>
		</tfoot>
	</table>
</div>

<script type="text/javascript">
	run_download_excel();
</script>

<?php
$input = shortcode_atts( array(
	'tahun_anggaran' => '2021'
), $atts );
global $wpdb;
$body = '<tr><td colspan="8" class="text_tengah">Data kosong. Harap pilih dulu data yang akan ditampilkan!</td></tr>';
if(!empty($_GET['data_list'])){
	$list = explode(',', $_GET['data_list']);
	$data_status = $this->data_status_bayar();
	$body = '';
	foreach ($list as $k => $id_post) {
		$post = get_post($id_post);
		$nop = get_post_meta( $id_post, '_crb_pbb_nop', true );
		$nama = get_post_meta( $id_post, '_crb_pbb_nama_wp', true );
		$alamat_wp = get_post_meta( $id_post, '_crb_pbb_alamat_wp', true );
		$alamat_op = get_post_meta( $id_post, '_crb_pbb_alamat_op', true );
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
		$body .= '
		<tr>
			<td class="text_tengah">'.($k+1).'</td>
			<td class="text_tengah">'.$nop.'</td>
			<td>'.$nama.'</td>
			<td>'.$alamat_wp.'</td>
			<td>'.$alamat_op.'</td>
			<td>'.$petugas_pajak.'</td>
			<td>'.$status_bayar_text.'</td>
			<td class="text_tengah">'.$tgl_bayar.'</td>
		</tr>
		';
	}
}
?>
<div id="cetak">
	<h1 style='text-align: center;'>DAFTAR NAMA PELUNASAN PBB TAHUN <?php echo $input['tahun_anggaran']; ?></h1>
	<table>
		<thead>
			<tr>
				<th class="text_tengah" style="width: 40px;">No.</th>
				<th class="text_tengah" style="width: 210px;">NOP</th>
				<th class="text_tengah" style="width: 150px;">Nama Wajib Pajak</th>
				<th class="text_tengah">Alamat Wajib Pajak</th>
				<th class="text_tengah">Alamat Objek Pajak</th>
				<th class="text_tengah" style="width: 120px;">Petugas Pajak</th>
				<th class="text_tengah" style="width: 240px;">Status Pembayaran</th>
				<th class="text_tengah" style="width: 160px;">Tgl. Transaksi</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $body; ?>
		</tbody>
	</table>
</div>

<script type="text/javascript">
	run_download_excel();
</script>

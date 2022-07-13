<?php
$input = shortcode_atts( array(
	'nop' => '',
	'tahun_anggaran' => '2021'
), $atts );
global $wpdb;

if(empty($input['nop'])){
	echo "<h1 style='text-align: center;'>NOP tidak boleh kosong!</h1>"; exit;
}
$post = get_post();
$nop = get_post_meta( $post->ID, '_crb_pbb_nop', true );
$nama_wp = get_post_meta( $post->ID, '_crb_pbb_nama_wp', true );
$tgl_bayar = get_post_meta( $post->ID, '_crb_pbb_tgl_bayar', true );
$alamat_wp = get_post_meta( $post->ID, '_crb_pbb_alamat_wp', true );
$alamat_op = get_post_meta( $post->ID, '_crb_pbb_alamat_op', true );
$nilai = get_post_meta( $post->ID, '_crb_pbb_ketetapan_pbb', true );
if(empty($nilai)){
	$nilai = 0;
}
$status_bayar = $this->functions->data_status_bayar(array('type' => 'html_color'));
$status = get_post_meta( $post->ID, '_crb_pbb_status_bayar', true );
if(empty($status)){
	$status = 0;
}
$tgl_bayar = get_post_meta( $post->ID, '_crb_pbb_tgl_bayar', true );
?>
<div id="cetak" style="padding: 10px;">
	<h1 style='text-align: center;'>Data Wajib Pajak</h1>
	<table class="table table-bordered" style="max-width: 700px; margin: auto;">
		<tbody>
			<tr>
				<th style="width: 200px;">Tahun Anggaran</th>
				<td><?php echo $input['tahun_anggaran']; ?></td>
			</tr>
			<tr>
				<th>NOP</th>
				<td><?php echo $nop; ?></td>
			</tr>
			<tr>
				<th>Nama Wajib Pajak</th>
				<td><?php echo $nama_wp; ?></td>
			</tr>
			<tr>
				<th>Alamat Wajib Pajak</th>
				<td><?php echo $alamat_wp; ?></td>
			</tr>
			<tr>
				<th>Alamat Objek Pajak</th>
				<td><?php echo $alamat_op; ?></td>
			</tr>
			<tr>
				<th>Nilai Pajak</th>
				<td><?php echo 'Rp '.number_format($nilai,0,",","."); ?></td>
			</tr>
			<tr>
				<th>Status Pembayaran</th>
				<td><?php echo $status_bayar[$status]; ?></td>
			</tr>
			<tr>
				<th>Tangal Transaksi</th>
				<td><?php echo $tgl_bayar; ?></td>
			</tr>
		</tbody>
	</table>
</div>
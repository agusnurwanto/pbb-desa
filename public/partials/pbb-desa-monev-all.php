<?php
$input = shortcode_atts( array(
	'tahun_anggaran' => get_option('_crb_pbb_tahun_anggaran')
), $atts );
global $wpdb;

$posts = get_posts(array( 
	'numberposts'	=> -1,
	'post_type' => 'wajib_pajak', 
	'meta_query' => array(
		array(
			'key'   => '_crb_pbb_tahun_anggaran',
			'value' => $input['tahun_anggaran']
		),
		'relation' => 'AND'
	),
	'post_status' => 'private',
	'meta_key'  => '_crb_pbb_nop',
	'orderby'   => 'meta_value_num',
	'order' => 'ASC'
));

$total_pajak = 0;
$total_belum_bayar = 0;
$total_diterima_petugas_pajak = 0;
$total_diterima_bendahara_desa = 0;
$total_diterima_kecamatan = 0;
$total_lunas = 0;
foreach ($posts as $k => $post) {
	$nilai = get_post_meta( $post->ID, '_crb_pbb_ketetapan_pbb', true );
	if(empty($nilai)){
		$nilai = 0;
	}
	$status = get_post_meta( $post->ID, '_crb_pbb_status_bayar', true );
	if(empty($status)){
		$status = 0;
	}
	$total_pajak += $nilai;
	if($status == 0){
		$total_belum_bayar += $nilai;
	}else if($status == 1){
		$total_diterima_petugas_pajak += $nilai;
	}else if($status == 2){
		$total_diterima_bendahara_desa += $nilai;
	}else if($status == 3){
		$total_diterima_kecamatan += $nilai;
	}else if($status == 4){
		$total_lunas += $nilai;
	}
}
?>
<div id="cetak" style="padding: 10px;">
	<h1 style='text-align: center;'>Laporan PBB Desa Tahun <?php echo $input['tahun_anggaran']; ?></h1>
	<table class="table table-bordered" style="max-width: 700px; margin: auto;">
		<tbody>
			<tr>
				<th style="width: 200px;">Tahun Anggaran</th>
				<td><?php echo $input['tahun_anggaran']; ?></td>
			</tr>
			<tr>
				<th>Jumlah Wajib Pajak</th>
				<td><?php echo count($posts); ?></td>
			</tr>
			<tr>
				<th>Total Pajak</th>
				<td><?php echo 'Rp '.number_format($total_pajak,0,",","."); ?></td>
			</tr>
			<tr>
				<th>Belum Bayar</th>
				<td><?php echo 'Rp '.number_format($total_belum_bayar,0,",","."); ?></td>
			</tr>
			<tr>
				<th>Diterima Petugas Pajak</th>
				<td><?php echo 'Rp '.number_format($total_diterima_petugas_pajak,0,",","."); ?></td>
			</tr>
			<tr>
				<th>Diterima Bendahara Desa</th>
				<td><?php echo 'Rp '.number_format($total_diterima_bendahara_desa,0,",","."); ?></td>
			</tr>
			<tr>
				<th>Diterima Kecamatan</th>
				<td><?php echo 'Rp '.number_format($total_diterima_kecamatan,0,",","."); ?></td>
			</tr>
			<tr>
				<th>Lunas</th>
				<td><?php echo 'Rp '.number_format($total_lunas,0,",","."); ?></td>
			</tr>
		</tbody>
	</table>
</div>
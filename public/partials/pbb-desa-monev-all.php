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
	if(empty($nilai) || !$this->functions->isInteger($nilai)){
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

$body_table = '
		<tr>
			<td style="text-align: center;">'.count($posts).' WP</td>
			<td style="text-align: center;" >Rp '.number_format($total_pajak,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_belum_bayar,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_diterima_petugas_pajak,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_diterima_bendahara_desa,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_diterima_kecamatan,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_lunas,0,",",".").'</td>
		</tr>
	
';

?>

<style>
#wrap-loading {
    display: none;
    width: 100%;
    height: 100vh;
    position: fixed;
    z-index: 9999999;
    background: #00000073;
    top: 0;
}

.center {
  margin: auto;
  width: 50%;
  padding: 10px;
}

</style>
<!-- <div class="text-center">
    <h3>Laporan PBB <br><?php echo get_option('_crb_pbb_desa') ?></h3>
    <label class="text-center">Tahun Anggaran : </label>
    <input type="number" id="tahun_anggaran" value="<?php echo date('Y') ?>">
</div> -->
<div style="padding: 10px;">
	<h1 style='text-align: center;'>Laporan PBB Desa Tahun <?php echo $input['tahun_anggaran']; ?></h1>
    <table id="table-laporan-pbb-desa" class="table table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="text-center" style="width: 170px;">Jumlah Wajib Pajak</th>
                <th class="text-center" style="width: 170px;">Total Pajak</th>
                <th class="text-center" style="width: 170px;">Belum Bayar</th>
                <th class="text-center" style="width: 100px;">Diterima Petugas Pajak</th>
                <th class="text-center" style="width: 125px;">Diterima Bendahara Desa</th>
                <th class="text-center" style="width: 125px;">Diterima Kecamatan</th>
                <th class="text-center" style="width: 125px;">Lunas</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_table ?>
        </tbody>
    </table>
</div>

<?php
$args = array(
    'role'    => 'petugas_pajak',
    'orderby' => 'user_nicename',
    'order'   => 'ASC'
);
$users = get_users( $args );
$filter_query = array();
foreach ($users as $key => $user):
	$filter_query[] = array(
		'key'   => '_crb_pbb_petugas_pajak',
		'value' => $user->ID,
	);
	$filter_query['relation'] = 'AND';
	$posts_petugas = get_posts(array( 
		'numberposts'	=> -1,
		'post_type' => 'wajib_pajak', 
		'meta_query' => $filter_query,
		'post_status' => 'private',
		'meta_key'  => '_crb_pbb_nop',
		'orderby'   => 'meta_value_num',
		'order' => 'ASC'
	));
	$body_table_petugas ='';
	$total_pajak_petugas = 0;
	$total_belum_bayar_petugas = 0;
	$total_diterima_petugas_pajak_petugas = 0;
	$total_diterima_bendahara_desa_petugas = 0;
	$total_diterima_kecamatan_petugas = 0;
	$total_lunas_petugas = 0;
	$no=1;
	foreach ($posts_petugas as $key => $post) {
		$nilai = get_post_meta( $post->ID, '_crb_pbb_ketetapan_pbb', true );
		if(empty($nilai) || !$this->functions->isInteger($nilai)){
			$nilai = 0;
		}
		$status = get_post_meta( $post->ID, '_crb_pbb_status_bayar', true );
		if(empty($status)){
			$status = 0;
		}
		$total_pajak_petugas += $nilai;
		if($status == 0){
			$total_belum_bayar_petugas += $nilai;
		}else if($status == 1){
			$total_diterima_petugas_pajak_petugas += $nilai;
		}else if($status == 2){
			$total_diterima_bendahara_desa_petugas += $nilai;
		}else if($status == 3){
			$total_diterima_kecamatan_petugas += $nilai;
		}else if($status == 4){
			$total_lunas_petugas += $nilai;
		}
		
	}

	$body_table_petugas .= '
		<tr>
			<td style="text-align: center;">'.count($posts_petugas).' WP</td>
			<td style="text-align: center;" >Rp '.number_format($total_pajak_petugas,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_belum_bayar_petugas,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_diterima_petugas_pajak_petugas,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_diterima_bendahara_desa_petugas,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_diterima_kecamatan_petugas,0,",",".").'</td>
			<td style="text-align: center;">Rp '.number_format($total_lunas_petugas,0,",",".").'</td>
		</tr>
	';
?>
	<div style="padding: 10px;">
		<h1 style='text-align: center;'>Laporan PBB Desa Tahun <?php echo $input['tahun_anggaran']; ?> Petugas <?php echo $user->display_name; ?></h1>
	    <table id="table-laporan-pbb-desa" class="table table-bordered" cellspacing="0" width="100%">
	        <thead>
	            <tr>
	                <th class="text-center" style="width: 170px;">Jumlah Wajib Pajak</th>
	                <th class="text-center" style="width: 170px;">Total Pajak</th>
	                <th class="text-center" style="width: 170px;">Belum Bayar</th>
	                <th class="text-center" style="width: 100px;">Diterima Petugas Pajak</th>
	                <th class="text-center" style="width: 125px;">Diterima Bendahara Desa</th>
	                <th class="text-center" style="width: 125px;">Diterima Kecamatan</th>
	                <th class="text-center" style="width: 125px;">Lunas</th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php echo $body_table_petugas ?>
	        </tbody>
	    </table>
	</div>
<?php endforeach; ?>



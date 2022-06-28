<?php

$args = array(
    'role'    => 'petugas_pajak',
    'orderby' => 'user_nicename',
    'order'   => 'ASC'
);
$users = get_users( $args );

$user_id = get_current_user_id();
$user_meta = get_userdata($user_id);
$user_role = $user_meta->roles;
$user_name = $user_meta->display_name;

$filter_query[] = array(
    'key'   => '_crb_pbb_petugas_pajak',
    'value' => $user_id,
);
$filter_query['relation'] = 'AND';

$posts = get_posts(array( 
    'numberposts'	=> -1,
    'post_type' => 'wajib_pajak', 
    'meta_query' => $filter_query,
    'post_status' => 'private',
    'meta_key'  => '_crb_pbb_nop',
    'orderby'   => 'meta_value_num',
    'order' => 'ASC'
));
$body_table = '';
$i = 0;

$list_html = '
			<option value="">Pilih Petugas</option>
			<option value="all">Semua Wajib Pajak</option>
		';
foreach ( $users as $user ) {
    $list[$user->ID] = esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')';
    $list_html .= '<option value="'.$user->ID.'">'.$list[$user->ID].'</option>';
}

$list_petugas = '';

if ($user_role == 'pengawas') {
    $list_petugas = '
        <div>
            <label>Pilih Petugas Pajak : </label>
            <select id="petugas_pajak" style="min-width: 250px;">'.$list_html.'</select>
        </div>
        ';
}


foreach ( $posts as $post ) {
    $i++;
    $nilai = get_post_meta( $post->ID, '_crb_pbb_ketetapan_pbb', true );
    if(empty($nilai)){
        $nilai = 0;
    }
    $status = get_post_meta( $post->ID, '_crb_pbb_status_bayar', true );
    if(empty($status)){
        $status = 0;
    }

    $status_bayar = $this->data_status_bayar();
    $status_bayar_wp = $status_bayar[$status];
    $nop = get_post_meta( $post->ID, '_crb_pbb_nop', true );
    $nama_wp = get_post_meta( $post->ID, '_crb_pbb_nama_wp', true );

    $user_info = get_userdata($user_id);
    $nama_petugas = $user_info->display_name;
    $body_table .= '
    <tr>
        <td style="text-align: center;" data-post-id="'.$post->ID.'" class="table-pbb-desa"></td>
        <td style="text-align: center;">'.$i.'</td>
        <td style="text-align: center;">'.$nop.'</td>
        <td style="text-align: center;">'.$nama_wp.'</td>
        <td style="text-align: center;">'.get_post_meta( $post->ID, '_crb_pbb_alamat_op', true ).'</td>
        <td style="text-align: center;">'.$status_bayar_wp.'</td>
        <td style="text-align: center;">'.'Rp '.number_format($nilai,0,",",".").'</td>
        <td style="text-align: center;">'.get_post_meta( $post->ID, '_crb_pbb_tgl_bayar', true ).'</td>
        <td style="text-align: center;">'.$nama_petugas.'</td>
    </tr>';
}

?>


<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
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
</style>
<div>
    <h3 class="text-center">Dashboard Manajemen Pajak <br>Desa <?php echo get_option('_crb_pbb_desa') ?> Petugas <?php echo $user_name ?></h3>
</div>
<div style="padding: 10px; padding-top: 5%;">
    <div class="form-group row" style="padding: 10px; padding-left: 10px;">
        <div>
            <label>Tahun Anggaran : </label>
            <input type="number" id="tahun_anggaran" value="<?php echo date('Y') ?>" style="margin-right: 20px;">
        </div>
        <?php
            echo $list_petugas;
        ?>
        <div>
            <label> Ubah status bayar : </label>
            <select id="status_bayar" style="min-width: 250px; margin-right: 20px;">
                <?php echo $this->data_status_bayar(array('type' => 'html'), $user_role[0]); ?>
            </select>
        </div>
    </div>
    
    <div style="padding: 10px; padding-top: 5%;">
        <a onclick="bayar_pajak(); return false" href="javascript:void(0);" class="button button-primary">Simpan Status Pajak</a>
        <div style="padding: 10px; padding-top: 5%;">
            <table id="user-table-pembayaran-pbb" class="table table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th></th>
                        <th style="width: 45px;">No</th>
                        <th style="width: 170px;">No. Object Pajak</th>
                        <th style="width: 170px;">Nama Wajib Pajak</th>
                        <th>Alamat</th>
                        <th style="width: 170px;">Status Pembayaran</th>
                        <th style="width: 100px;">Nilai Pajak</th>
                        <th style="width: 125px;">Tgl. Transaksi</th>
                        <th style="width: 125px;">Nama Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $body_table ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
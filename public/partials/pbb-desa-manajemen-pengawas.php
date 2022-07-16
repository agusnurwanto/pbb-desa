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

$datasets = array();
$color_jenis = array(
    'Belum Bayar' => 'rgba(255, 99, 132, 1)',
    'Diterima Petugas Pajak' => 'rgba(54, 162, 235, 1)',
    'Diterima Bendahara Desa' => 'rgba(255, 206, 86, 1)',
    'Diterima Kecamatan' => 'rgba(75, 192, 192, 1)',
    'Lunas' => 'rgba(153, 102, 255, 1)'
);

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
        <td style="text-align: center;">'.$i.'</td>
        <td style="text-align: center;">'.$nop.'</td>
        <td>'.$nama_wp.'</td>
        <td>'.get_post_meta( $post->ID, '_crb_pbb_alamat_op', true ).'</td>
        <td style="text-align: center;">'.$status_bayar_wp.'</td>
        <td class="text_kanan">'.number_format($nilai,0,",",".").'</td>
        <td style="text-align: center;">'.get_post_meta( $post->ID, '_crb_pbb_tgl_bayar', true ).'</td>
        <td style="text-align: center;">'.$nama_petugas.'</td>
    </tr>';

    if(empty($datasets[$status_bayar_wp])){
        $datasets[$status_bayar_wp] = array(
            'label' => $status_bayar_wp,
            'data' => array(0),
            'backgroundColor' => [
                $color_jenis[$status_bayar_wp]
            ]
        );
    }
    $datasets[$status_bayar_wp]['data'][0] += 1;
}

$labels = array();
$newDatashets = array();
foreach($datasets as $k => $v){
    $labels[] = $k;
    $newDatashets[] = $v;
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

.center {
  margin: auto;
  width: 50%;
  padding: 10px;
}

</style>
<div class="text-center">
    <h3>Dashboard Manajemen Pajak <br><?php echo get_option('_crb_pbb_desa') ?><br>Nama Pengawas: <?php echo $user_name ?></h3>
    <label class="text-center">Tahun Anggaran : </label>
    <input type="number" id="tahun_anggaran" value="<?php echo date('Y') ?>">
    <label>Pilih Petugas Pajak : </label>
    <select id="petugas_pajak" style="min-width: 250"><?php echo $list_html ?></select>
</div>
<div style="padding: 10px;">
    <div style="margin-bottom: 20px;">
        <div style="width: 100%; padding: 10px; max-width: 1000px; max-height: 1000px; margin: auto; margin-bottom: 25px;">
            <canvas id="myChart"></canvas>
        </div>
        <div class="form-group row" style="padding: 10px; padding-left: 10px;">
            <div>
                <input type="hidden" id="petugas_pajak" name="petugas_pajak" value="<?php echo $user_id ?>">'
                <label>Filter Status Bayar : </label>
                <select id="status_bayar" style="min-width: 250px; margin-right: 20px;">
                    <?php echo $this->data_status_bayar(array('type' => 'html'), $user_role[0]); ?>
                </select>
            </div>
            <!-- <a onclick="bayar_pajak(); return false" href="javascript:void(0);" class="button button-primary">Simpan Status Pajak</a> -->
        </div>
    </div>
    <table id="user-table-pembayaran-pbb" class="table table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th style="width: 45px;">No</th>
                <th style="width: 170px;">No. Object Pajak</th>
                <th style="width: 170px;">Nama Wajib Pajak</th>
                <th>Alamat</th>
                <th style="width: 170px;">Status Pembayaran</th>
                <th style="width: 100px;">Nilai Pajak (Rp)</th>
                <th style="width: 125px;">Tgl. Transaksi</th>
                <th style="width: 125px;">Nama Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $body_table ?>
        </tbody>
        <tfoot>
            <th colspan="5">Total</th>
            <th id="total_all" class="text_kanan"></th>
            <th colspan="2"></th>
        </tfoot>
    </table>
</div>

<script>
var _url_asli = window.location.href;
var url = new URL(_url_asli);
_url_asli = changeUrl({ url: _url_asli, key: 'key', value: '<?php echo $this->functions->gen_key(); ?>' });
jQuery(document).ready(function() {
    var loading = ''
        +'<div id="wrap-loading">'
            +'<div class="lds-hourglass"></div>'
            +'<div id="persen-loading"></div>'
        +'</div>';
    if(jQuery('#wrap-loading').length == 0){
        jQuery('body').prepend(loading);
    }

    window.table = jQuery('#user-table-pembayaran-pbb').DataTable({
        'order': [[1, 'asc']],
        lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "All"]],
        footerCallback: function ( row, data, start, end, display ) {
            var api = this.api();
            var total_page = api.column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return a + to_number(b);
                }, 0 );
            jQuery('#total_all').text(formatRupiah(total_page));

            if(pieChart2){
                var datasets = {};
                pieChart2.data.datasets = [];
                api.rows( {page:'current'} ).data().map(function(b, i){
                    var key = (b[5]).substring(0, 50);
                    if(!datasets[key]){
                        datasets[key] = {
                            label: key,
                            data: [0],
                            backgroundColor: [
                                color_jenis[key]
                            ]
                        }
                    }
                    datasets[key].data[0]++;
                });
                var newDatasets = [];
                for(var i in datasets){
                    newDatasets.push(datasets[i]);
                }
                pieChart2.data.datasets = newDatasets;
                pieChart2.update();
            }
        }
    });

    jQuery('#petugas_pajak').on('change', function(){
        var url = changeUrl({ url: _url_asli, key: 'petugas_pajak', value: jQuery(this).val() });
        window.location.href = url;
    });
            
    jQuery('#status_bayar').on('change', function(){
        petugas_pajak = jQuery('#petugas_pajak').val();
        if (petugas_pajak) {
            get_wajib_pajak_pengawas();
        }else {
            alert('Pilih petugas');
        }
    });

    // jQuery('#petugas_pajak').on('change', function(){
    //     get_wajib_pajak_pengawas();
    // });
});

window.color_jenis = <?php echo json_encode($color_jenis); ?>;
window.pieChart2 = new Chart(document.getElementById('myChart'), {
    type: 'bar',
    data: {
        labels: ['Rekapitulasi pembayaran pajak'],
        datasets: <?php echo json_encode($newDatashets); ?>
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        size: 16
                    }
                }
            },
            tooltip: {
                bodyFont: {
                    size: 16
                },
                boxPadding: 5
            },
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});


    
</script>
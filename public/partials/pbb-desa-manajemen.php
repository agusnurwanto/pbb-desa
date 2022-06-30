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
$list_petugas = ' <input type="hidden" id="petugas_pajak" name="petugas_pajak" value="'.$user_id.'">';


if ($user_role == 'pengawas') {
    $list_petugas = '
        <div>
            <label>Pilih Petugas Pajak : </label>
            <select id="petugas_pajak" style="min-width: 250">'.$list_html.'</select>
        </div>
        ';
}
$datasets = array();

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

    $color_jenis = ['rgba(255, 99, 132, 0.2)',
    'rgba(255, 99, 132, 0.2)',
    'rgba(54, 162, 235, 0.2)',
    'rgba(255, 206, 86, 0.2)',
    'rgba(75, 192, 192, 0.2)',
    'rgba(153, 102, 255, 0.2)',
    'rgba(255, 159, 64, 0.2)'];

    $datasets[] = array(
        'label' => $status_bayar_wp,
        'data' => array(),
        'backgroundColor' => [
            $color_jenis
        ]
    );

    $user_info = get_userdata($user_id);
    $nama_petugas = $user_info->display_name;
    $body_table .= '
    <tr>
        <td style="text-align: center;" data-post-id="'.$post->ID.'" class="table-pbb-desa"></td>
        <td style="text-align: center;">'.$i.'</td>
        <td style="text-align: center;">'.$nop.'</td>
        <td>'.$nama_wp.'</td>
        <td>'.get_post_meta( $post->ID, '_crb_pbb_alamat_op', true ).'</td>
        <td style="text-align: center;">'.$status_bayar_wp.'</td>
        <td class="text_kanan">'.number_format($nilai,0,",",".").'</td>
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

.center {
  margin: auto;
  width: 50%;
  padding: 10px;
}

</style>
<div>
    <h3 class="text-center">Dashboard Manajemen Pajak <br><?php echo get_option('_crb_pbb_desa') ?><br>Nama Petugas: <?php echo $user_name ?></h3>
</div>
<div style="padding: 10px;">
    <div style="margin-bottom: 20px;">
        <div class="center" style="width:22%;">
            <label class="text-center">Tahun Anggaran : </label>
            <input type="number" id="tahun_anggaran" value="<?php echo date('Y') ?>" style="margin-right: 20px;">
        </div>
    <div style="width: 100%; max-width: 1500px; max-height: 1000px; margin: auto; margin-bottom: 25px;">
        <canvas id="myChart"></canvas>
    </div>
        <div class="form-group row" style="padding: 10px; padding-left: 10px;">
            <div>
                <input type="hidden" id="petugas_pajak" name="petugas_pajak" value="<?php echo $user_id ?>">'
                <label>Ubah status bayar : </label>
                <select id="status_bayar" style="min-width: 250px; margin-right: 20px;">
                    <?php echo $this->data_status_bayar(array('type' => 'html'), $user_role[0]); ?>
                </select>
            </div>
            <a onclick="bayar_pajak(); return false" href="javascript:void(0);" class="button button-primary">Simpan Status Pajak</a>
        </div>
    </div>
    <table id="user-table-pembayaran-pbb" class="table table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th></th>
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
            <th colspan="6">Total</th>
            <th id="total_all" class="text_kanan"></th>
            <th colspan="2"></th>
        </tfoot>
    </table>
</div>

<script>


jQuery(document).ready(function() {
    var loading = ''
        +'<div id="wrap-loading">'
            +'<div class="lds-hourglass"></div>'
            +'<div id="persen-loading"></div>'
        +'</div>';
    if(jQuery('#wrap-loading').length == 0){
        jQuery('body').prepend(loading);
    }

    var table = jQuery('#user-table-pembayaran-pbb').DataTable({     
        'columnDefs': [
            {
                'targets': 0,
                'checkboxes': {
                    'selectRow': true
                }
            }
        ],
        'select': {
            'style': 'multi'
        },
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
                var labels = [];
                var datasets = {};
                pieChart2.data.datasets.map(function(b, i){
                    pieChart2.data.datasets[i].data = [];
                    datasets[pieChart2.data.datasets[i].label] = i;
                });
                api.rows( {page:'current'} ).data().map(function(b, i){
                    var key = (b[5]).substring(0, 50);
                    labels.push(key);
                    const counts = [];
                    labels.forEach(function(x, i){
                        if (typeof counts[x] == 'undefined') {
                            counts[x] = 1;
                        }else {
                            counts[x] += 1;
                        }
                    });

                    console.log(counts[key]);
                    pieChart2.data.datasets[datasets[key]].data[i] = to_number(counts[key]);

                });


                if(labels.length >= 1){
                    pieChart2.data.labels = labels;
                    pieChart2.update();
                }
            }
        }

        
    });

    jQuery('#tahun_anggaran').on('change', function(){
        get_wajib_pajak();
    });
});

window.pieChart2 = new Chart(document.getElementById('myChart'), {
    type: 'bar',
    data: {
        labels: [],
        datasets: <?php echo json_encode($datasets); ?>
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
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
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
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
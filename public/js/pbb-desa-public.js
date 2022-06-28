function run_download_excel(type){
    var current_url = window.location.href;
    var body = '<a id="excel" onclick="return false;" href="#" class="button button-primary">DOWNLOAD EXCEL</a>';
    if(type == 'apbd'){
        body += ''
            +'<div style="padding-top: 20px;">'
                +'<label><input id="tampil-1" type="checkbox" checked="true" onclick="tampilData(this, 1)"> Tampil Rekening</label>'
                +'<label style="margin-left: 10px;"><input id="tampil-2" type="checkbox" checked="true" onclick="tampilData(this, 2)"> Tampil Keterangan</label>'
                +'<label style="margin-left: 10px;"><input id="tampil-3" type="checkbox" checked="true" onclick="tampilData(this, 3)"> Tampil Kelompok</label>'
            +'</div>';
    }
    var download_excel = ''
        +'<div id="action-page" class="hide-print text_tengah" style="margin:20px;">'
            +body
        +'</div>';
    jQuery('body').prepend(download_excel);

    var style = '';

    style = jQuery('.cetak').attr('style');
    if (typeof style == 'undefined'){ style = ''; };
    jQuery('.cetak').attr('style', style+" font-family:'Open Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; padding:0; margin:0; font-size:13px;");
    
    jQuery('.bawah').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" border-bottom:1px solid #000;");
    });
    
    jQuery('.kiri').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" border-left:1px solid #000;");
    });

    jQuery('.kanan').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" border-right:1px solid #000;");
    });

    jQuery('.atas').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" border-top:1px solid #000;");
    });

    jQuery('.text_tengah').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" text-align: center;");
    });

    jQuery('.text_kiri').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" text-align: left;");
    });

    jQuery('.text_kanan').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" text-align: right;");
    });

    jQuery('.text_block').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" font-weight: bold;");
    });

    jQuery('.text_15').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" font-size: 15px;");
    });

    jQuery('.text_20').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+" font-size: 20px;");
    });

    jQuery('td').map(function(i, b){
        style = jQuery(b).attr('style');
        if (typeof style == 'undefined'){ style = ''; };
        jQuery(b).attr('style', style+' mso-number-format:\\@;');
    });

    jQuery('#excel').on('click', function(){
        var name = "Laporan";
        var title = jQuery('#cetak').attr('title');
        if(title){
            name = title;
        }
        tableHtmlToExcel('cetak', name);
    });
}

function tableHtmlToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20').replace(/#/g, '%23');
   
    filename = filename?filename+'.xls':'excel_data.xls';
   
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
   
        downloadLink.download = filename;
       
        downloadLink.click();
    }
}

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
        }
    });

    jQuery('#petugas_pajak').on('change', function(){
        get_wajib_pajak();
    });
      
});

function formatRupiah(angka, prefix){
    var cek_minus = false;
    if(!angka || angka == '' || angka == 0){
        angka = '0';
    }else if(angka < 0){
        angka = angka*-1;
        cek_minus = true;
    }
    try {
        if(typeof angka == 'number'){
            angka = Math.round(angka*100)/100;
            angka += '';
            angka = angka.replace(/\./g, ',').toString();
        }
        angka += '';
        number_string = angka;
    }catch(e){
        console.log('angka', e, angka);
        var number_string = '0';
    }
    var split           = number_string.split(','),
    sisa            = split[0].length % 3,
    rupiah          = split[0].substr(0, sisa),
    ribuan          = split[0].substr(sisa).match(/\d{3}/gi);

    // tambahkan titik jika yang di input sudah menjadi angka ribuan
    if(ribuan){
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    if(cek_minus){
        return '-'+(prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : ''));
    }else{
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
}

function to_number(text){
    if(typeof text == 'number'){
        return text;
    }
    text = +(text.replace(/\./g, '').replace(/,/g, '.'));
    if(typeof text == 'NaN'){
        text = 0;
    }
    return text;
}

function bayar_pajak(){
    var status = jQuery('#status_bayar').val();
    get_data_list();
    if(data_id_post.length == 0){
        alert('Pilih wajib pajak dulu!');
    }else if(status == ''){
        alert('Pilih status pembayaran dulu!');
    }else{
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'ubah_status_pajak',
                data: data_id_post,
                status: status
            },
            success: function(res){
                jQuery('#wrap-loading').hide();
                res = JSON.parse(res);
                alert(res.message);
                if(res.status == 'success'){
                    location.reload();
                }
            }
        });
    }
}

function get_data_list(){
    window.data_id_post = [];

    jQuery('.table-pbb-desa').map(function(i, b){
        var checkbox = jQuery(b).find('input[type="checkbox"]');
        var cek = checkbox.is(':checked');
        if(cek){
            var id = jQuery(b).attr('data-post-id');
            data_id_post.push(id);
        }
    });
}

function get_wajib_pajak(){
    jQuery('#wrap-loading').show();
    get_data_list();
    var tahun_anggaran = jQuery('#tahun_anggaran').val();
    var petugas_pajak = jQuery('#petugas_pajak').val();
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: {
            action: 'get_wajib_pajak',
            tahun_anggaran: tahun_anggaran,
            petugas_pajak: petugas_pajak
        },
        success: function(res){
            res = JSON.parse(res);
            var data_wp = '';
            var data_wp_kosong = ''
                +'<tr>'
                    +'<td colspan="9" style="text-align: center;">Data Kosong!</td>'
                +'</tr>';
            if(res.status == 'success'){
                var total = 0;
                res.data.map(function(b, i){
                    status = pbb.status_bayar[b.crb_pbb_status_bayar];
                    var checked = '';
                    if(typeof data_id_post != 'undefined'){
                        data_id_post.map(function(m, n){
                            if(m == b.post_id){
                                checked = 'checked';
                            }
                        });
                    }
                    var nilai = +(b.crb_pbb_ketetapan_pbb.replace('Rp ','').replace(/\./g,''));
                    total += nilai;
                    data_wp += ''
                        +'<tr>'
                            +'<td class="text_tengah"><input type="checkbox" data-post-id="'+b.post_id+'" '+checked+'></td>'
                            +'<td class="text_tengah" style="text-align: right;">'+(i+1)+'</td>'
                            +'<td class="text_tengah"><a href="'+b.crb_pbb_url+'" target="blank">'+b.crb_pbb_nop+'</a></td>'
                            +'<td>'+b.crb_pbb_nama_wp+'</td>'
                            +'<td>'+b.crb_pbb_alamat_op+'</td>'
                            +'<td style="width: 100px;">'+status+'</td>'
                            +'<td>'+b.crb_pbb_ketetapan_pbb+'</td>'
                            +'<td class="text_tengah tgl_transaksi">'+b.crb_pbb_tgl+'</td>'
                            +'<td class="text_tengah">'+b.crb_display_name+'</td>'
                        +'</tr>';
                });
                if(data_wp == ''){
                    data_wp += data_wp_kosong;
                }else{
                    total = 'Rp '+formatMoney(total, 0, ",", ".");
                    data_wp += ''
                        +'<tr>'
                            +'<td colspan="6">Total</td>'
                            +'<td colspan="3">'+total+'</td>'
                        +'</tr>';
                }
            }else{
                data_wp += data_wp_kosong;
                alert(res.message);
            }
            jQuery('#user-table-pembayaran-pbb tbody').html(data_wp);
            jQuery('#wrap-loading').hide();
        }
    });
}

function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
    try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e)
    }
};

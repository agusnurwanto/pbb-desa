function filePicked(oEvent) {
    jQuery('#wrap-loading').show();
    // Get The File From The Input
    var oFile = oEvent.target.files[0];
    var sFilename = oFile.name;
    // Create A File Reader HTML5
    var reader = new FileReader();

    reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, {
            type: 'binary'
        });

        workbook.SheetNames.forEach(function(sheetName) {
            // Here is your object
            var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
            var data = [];
            XL_row_object.map(function(b, i){
                data.push(b);
            });
            var json_object = JSON.stringify(data);
            jQuery('#data-excel').val(json_object);
            jQuery('#wrap-loading').hide();
        });
    };

    reader.onerror = function(ex) {
      console.log(ex);
    };

    reader.readAsBinaryString(oFile);
}

function import_excel(){
	var data = jQuery('#data-excel').val();
    var tahun_anggaran = jQuery('#tahun_anggaran').val();
    var petugas_pajak = jQuery('#petugas_pajak').val();
    if(!data){
        return alert('Excel Data can not empty!');
    }else{
        data = JSON.parse(data);
        jQuery('#wrap-loading').show();

        var data_all = [];
        var data_sementara = [];
        var max = 250;
        data.map(function(b, i){
            data_sementara.push(b);
            if(data_sementara.length%max == 0){
                data_all.push(data_sementara);
                data_sementara = [];
            }
        });
        if(data_sementara.length > 0){
            data_all.push(data_sementara);
        }
        var sendData = data_all.map(function(b, i){
            return new Promise(function(resolve_redurce, reject_redurce){
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: {
                        action: 'import_excel',
                        tahun_anggaran: tahun_anggaran,
                        petugas_pajak: petugas_pajak,
                        data: b
                    },
                    success: function(res){
                        resolve_redurce(true);
                    }
                });
            })
            .catch(function(e){
                console.log(e);
                return Promise.resolve(true);
            });
        });

        Promise.all(sendData)
        .then(function(val_all){
            jQuery('#wrap-loading').hide();
            alert('Success import wajib pajak dari excel!');
        })
        .catch(function(e){
            console.log(e);
            jQuery('#wrap-loading').hide();
            alert('Error!');
        });
    }
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
                jQuery('#petugas_pajak_bayar').trigger('change');
            }
        });
    }
}

function get_data_list(){
    window.data_id_post = [];
    jQuery('#table-pembayaran-pbb tbody tr').map(function(i, b){
        var tr = jQuery(b);
        var checkbox = tr.find('td input[type="checkbox"]');
        var cek = checkbox.is(':checked');
        if(cek){
            var id = checkbox.attr('data-post-id');
            data_id_post.push(id);
        }
    });
}

function print_pajak(){
    var tahun_anggaran = jQuery('#tahun_anggaran').val();
    get_data_list();
    if(data_id_post.length == 0){
        alert('Pilih wajib pajak dulu!');
    }else{
        jQuery('#wrap-loading').show();
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'get_url_print_pbb',
                tahun_anggaran: tahun_anggaran
            },
            success: function(res){
                jQuery('#wrap-loading').hide();
                res = JSON.parse(res);
                window.open(res.url+'?data_list='+data_id_post.join(','), '_blank').focus();
            }
        });
    }
}

jQuery(document).ready(function(){
    var loading = ''
        +'<div id="wrap-loading">'
            +'<div class="lds-hourglass"></div>'
            +'<div id="persen-loading"></div>'
        +'</div>';
    if(jQuery('#wrap-loading').length == 0){
        jQuery('body').prepend(loading);
    }

    jQuery('#select-all').on('click', function(){
        if(jQuery(this).is(':checked')){
            jQuery('#table-pembayaran-pbb tbody input[type="checkbox"]').prop('checked', true);
        }else{
            jQuery('#table-pembayaran-pbb tbody input[type="checkbox"]').prop('checked', false);
        }
    });
    jQuery('#petugas_pajak_bayar').on('change', function(){
        jQuery('#wrap-loading').show();
        get_data_list();
        var tahun_anggaran = jQuery('#tahun_anggaran').val();
        var petugas_pajak = jQuery(this).val();
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
                        +'<td colspan="6" style="text-align: center;">Data Kosong!</td>'
                    +'</tr>';
                if(res.status == 'success'){
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
                        data_wp += ''
                            +'<tr>'
                                +'<td class="text_tengah"><input type="checkbox" data-post-id="'+b.post_id+'" '+checked+'></td>'
                                +'<td class="text_tengah" style="text-align: right;">'+(i+1)+'</td>'
                                +'<td class="text_tengah"><a href="'+b.crb_pbb_url+'" target="blank">'+b.crb_pbb_nop+'</a></td>'
                                +'<td>'+b.crb_pbb_nama_wp+'</td>'
                                +'<td>'+b.crb_pbb_alamat_op+'</td>'
                                +'<td style="width: 100px;">'+status+'</td>'
                                +'<td>'+b.crb_pbb_ketetapan_pbb+'</td>'
                                +'<td class="text_tengah">'+b.crb_pbb_tgl+'</td>'
                            +'</tr>';
                    });
                    if(data_wp == ''){
                        data_wp += data_wp_kosong;
                    }
                }else{
                    data_wp += data_wp_kosong;
                    alert(res.message);
                }
                jQuery('#table-pembayaran-pbb tbody').html(data_wp);
                jQuery('#wrap-loading').hide();
            }
        });
    });
});
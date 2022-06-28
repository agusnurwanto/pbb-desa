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

        var cek_sheet_name = false;
        workbook.SheetNames.forEach(function(sheetName) {
            // Here is your object
            console.log('sheetName', sheetName);
            if(sheetName == 'data'){
                cek_sheet_name = true;
                var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                var data = [];
                XL_row_object.map(function(b, i){
                    data.push(b);
                });
                var json_object = JSON.stringify(data);
                jQuery('#data-excel').val(json_object);
                jQuery('#wrap-loading').hide();
            }
        });
        setTimeout(function(){
            if(false == cek_sheet_name){
                jQuery('#data-excel').val('');
                alert('Sheet dengan nama "data" tidak ditemukan!');
                jQuery('#wrap-loading').hide();
            }
        }, 2000);
    };

    reader.onerror = function(ex) {
      console.log(ex);
    };

    reader.readAsBinaryString(oFile);
}

function relayAjax(options, retries=20, delay=5000, timeout=90000){
    options.timeout = timeout;
    options.cache = false;
    jQuery.ajax(options)
    .fail(function(jqXHR, exception){
        // console.log('jqXHR, exception', jqXHR, exception);
        if(
            jqXHR.status != '0' 
            && jqXHR.status != '503'
            && jqXHR.status != '500'
        ){
            if(jqXHR.responseJSON){
                options.success(jqXHR.responseJSON);
            }else{
                options.success(jqXHR.responseText);
            }
        }else if (retries > 0) {
            console.log('Koneksi error. Coba lagi '+retries, options);
            var new_delay = Math.random() * (delay/1000);
            setTimeout(function(){ 
                relayAjax(options, --retries, delay, timeout);
            }, new_delay * 1000);
        } else {
            alert('Capek. Sudah dicoba berkali-kali error terus. Maaf, berhenti mencoba.');
        }
    });
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
        var max = 100;
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
        var last = data_all.length - 1;
        data_all.reduce(function(sequence, nextData){
            return sequence.then(function(current_data){
                return new Promise(function(resolve_reduce, reject_reduce){
                    relayAjax({
                        url: ajaxurl,
                        type: 'post',
                        data: {
                            action: 'import_excel',
                            tahun_anggaran: tahun_anggaran,
                            petugas_pajak: petugas_pajak,
                            data: current_data
                        },
                        success: function(res){
                            resolve_reduce(nextData);
                        },
                        error: function(e){
                            console.log('Error import excel', e);
                        }
                    });
                })
                .catch(function(e){
                    console.log(e);
                    return Promise.resolve(nextData);
                });
            })
            .catch(function(e){
                console.log(e);
                return Promise.resolve(nextData);
            });
        }, Promise.resolve(data_all[last]))
        .then(function(data_last){
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
        console.log(data_id_post);
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
    var type_laporan = jQuery('#format-laporan-pajak').val();
    if(type_laporan == ''){
        return alert('Pilih format laporan dulu!');
    }else{
        var tahun_anggaran = jQuery('#tahun_anggaran').val();
        if(type_laporan == 4){
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
        }else if(
            type_laporan == 1
            || type_laporan == 2
            || type_laporan == 3
        ){
            jQuery('#wrap-loading').show();
            if(type_laporan == 1){
                var tgl_awal = jQuery('.tgl_harian input').val();
                var tgl_akhir = tgl_awal;
            }else{
                var tgl_awal = jQuery('.start_date input').val();
                var tgl_akhir = jQuery('.end_date input').val();
            }
            
            var judul = '';
            if(type_laporan == 1){
                judul = 'Laporan Harian Tanggal '+tgl_awal;
            }else if(type_laporan == 2){
                judul = 'Laporan Mingguan, Tanggal Awal '+tgl_awal+' dan Tanggal Akhir '+tgl_akhir;
            }else if(type_laporan == 3){
                judul = 'Laporan Bulanan, Tanggal Awal '+tgl_awal+' dan Tanggal Akhir '+tgl_akhir;
            }
            var data_id_post_tgl = [];
            jQuery('#table-pembayaran-pbb tr td.tgl_transaksi').map(function(i, b){
                var tgl_transaksi = jQuery(b).text().split(' ')[0];
                if(type_laporan == 1 && tgl_awal == tgl_transaksi){
                    var tr = jQuery(b).closest('tr');
                    var id = tr.find('td input[type="checkbox"]').attr('data-post-id');
                    data_id_post_tgl.push(id);
                }else{
                    tgl_transaksi = new Date(tgl_transaksi).getTime();
                    tgl_awal = new Date(tgl_awal).getTime();
                    tgl_akhir = new Date(tgl_akhir).getTime();
                    if(tgl_transaksi >= tgl_awal && tgl_transaksi <= tgl_akhir){
                        var tr = jQuery(b).closest('tr');
                        var id = tr.find('td input[type="checkbox"]').attr('data-post-id');
                        data_id_post_tgl.push(id);
                    }
                }
            });
            if(data_id_post_tgl.length == 0){
                jQuery('#wrap-loading').hide();
                return alert('Data sesuai filter tanggal tidak ditemukan!');
            }else{
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
                        window.open(res.url+'?data_list='+data_id_post_tgl.join(',')+'&judul='+judul, '_blank').focus();
                    }
                });
            }
        }
    }
}

function get_wajib_pajak(){
    jQuery('#wrap-loading').show();
    get_data_list();
    var tahun_anggaran = jQuery('#tahun_anggaran').val();
    var petugas_pajak = jQuery('#petugas_pajak_bayar').val();
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
            jQuery('#table-pembayaran-pbb tbody').html(data_wp);
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
}

function cek_null(number, length){
    number = ''+number;
    if(number.length < length){
        var new_number = [];
        for(var i=length-1; i>=0; i--){
            if(typeof number[i] == 'undefined'){
                new_number.push('0');
            }else{
                new_number.push(number[i]);
            }
        }
        number = new_number.join('');
    }
    return number;
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
        get_wajib_pajak();
    });
    jQuery('#format-laporan-pajak').on('change', function(){
        jQuery('#filter-tanggal-pbb').hide();
        jQuery('#filter-tanggal-pbb label').hide();
        var type_laporan = jQuery(this).val();
        var date = new Date();
        if(type_laporan == 1){
            var today = date.getFullYear()+'-'+cek_null(date.getMonth()+1, 2)+'-'+date.getDate();
            jQuery('#filter-tanggal-pbb').show();
            jQuery('#filter-tanggal-pbb .tgl_harian').show();
            jQuery('.tgl_harian input').val(today);
        }else if(type_laporan == 2){
            // Ubah start day mingguan di hari senin. secara default adalah hari minggu
            var start_day = ((date.getDay()+6)%7);
            var first = date.getDate() - start_day;
            var last = first + 6;

            var firstday = new Date(date.setDate(first));
            var lastday = new Date(date.setDate(last));
            var firstday = firstday.getFullYear()+'-'+cek_null(firstday.getMonth()+1, 2)+'-'+firstday.getDate();
            var lastday = lastday.getFullYear()+'-'+cek_null(lastday.getMonth()+1, 2)+'-'+lastday.getDate();
            jQuery('#filter-tanggal-pbb').show();
            jQuery('#filter-tanggal-pbb .start_date').show();
            jQuery('#filter-tanggal-pbb .end_date').show();
            jQuery('.start_date input').val(firstday);
            jQuery('.end_date input').val(lastday);
        }else if(type_laporan == 3){
            var start_month = date.getFullYear()+'-'+cek_null(date.getMonth()+1, 2)+'-01';
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            var end_month = lastDay.getFullYear()+'-'+cek_null(lastDay.getMonth()+1, 2)+'-'+lastDay.getDate();
            jQuery('#filter-tanggal-pbb').show();
            jQuery('#filter-tanggal-pbb .start_date').show();
            jQuery('#filter-tanggal-pbb .end_date').show();
            jQuery('.start_date input').val(start_month);
            jQuery('.end_date input').val(end_month);
        }
    });
});
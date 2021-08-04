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
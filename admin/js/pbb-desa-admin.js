(function( $ ) {
	'use strict';

	var loading = ''
		+'<div id="wrap-loading">'
	        +'<div class="lds-hourglass"></div>'
	        +'<div id="persen-loading"></div>'
	    +'</div>';
	if(jQuery('#wrap-loading').length == 0){
		jQuery('body').prepend(loading);
	}

})( jQuery );

function filePicked(oEvent) {
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
        });
    };

    reader.onerror = function(ex) {
      console.log(ex);
    };

    reader.readAsBinaryString(oFile);
}

function import_excel(){
	var data = jQuery('#data-excel').val();
    if(!data){
        return alert('Excel Data can not empty!');
    }else{
        data = JSON.parse(data);
        jQuery('#wrap-loading').show();
        var last = data.length-1;
        data.reduce(function(sequence, nextData){
            return sequence.then(function(current_data){
                return new Promise(function(resolve_redurce, reject_redurce){
                    jQuery.ajax({
						url: ajaxurl,
						type: 'post',
						data: {
							action: 'import_excel',
							data: current_data
						},
						success: function(res){
                    		resolve_redurce(nextData);
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
        }, Promise.resolve(data[last]))
        .then(function(data_last){
            jQuery('#wrap-loading').hide();
            alert('Success import wajib pajak dari excel!');
        })
        .catch(function(e){
            console.log(e);
        });
    }
}
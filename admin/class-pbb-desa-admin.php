<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/agusnurwanto
 * @since      1.0.0
 *
 * @package    Pbb_Desa
 * @subpackage Pbb_Desa/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pbb_Desa
 * @subpackage Pbb_Desa/admin
 * @author     Agus Nurwanto <agusnurwantomuslim@gmail.com>
 */
use Carbon_Fields\Container;
use Carbon_Fields\Field;

class Pbb_Desa_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pbb_Desa_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pbb_Desa_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pbb-desa-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pbb_Desa_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pbb_Desa_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name.'jszip', plugin_dir_url( __FILE__ ) . 'js/jszip.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'xlsx', plugin_dir_url( __FILE__ ) . 'js/xlsx.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pbb-desa-admin.js', array( 'jquery' ), $this->version.'.'.time(), false );
		wp_localize_script( $this->plugin_name, 'pbb', array(
		    'status_bayar' => $this->data_status_bayar(array('type' => 'html_color'))
		));

	}

	public function data_status_bayar($option = array('type' => false)){
		$data = array(
    		'' => 'Pilih Status Pembayaran',
    		'0' => 'Belum Bayar',
    		'1' => 'Diterima Petugas Pajak',
    		'2' => 'Diterima Bendahara Desa',
    		'3' => 'Diterima Kecamatan',
    		'4' => 'Lunas'
    	);
		if($option['type'] == 'html'){
			$html = '';
			foreach ($data as $k => $v) {
				$html .= '<option value="'.$k.'">'.$v.'</option>';
			}
			return $html;
		}else if($option['type'] == 'html_color'){
			$new_data = array();
			foreach ($data as $k => $v) {
				if($k >= 1 && $k <=3){
					$new_data[$k] = '<span style="color: orange; font-weight: bold;">'.$v.'</span>';
				}else if($k == 4){
					$new_data[$k] = '<span style="color: green; font-weight: bold;">'.$v.'</span>';
				}else{
					$new_data[$k] = '<span style="color: red; font-weight: bold;">'.$v.'</span>';
				}
			}
			return $new_data;
		}else{
			return $data;
		}
	}

	public function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	public function crb_attach_pbb_options(){
		global $wpdb;

		$args = array(
		    'role'    => 'petugas_pajak',
		    'orderby' => 'user_nicename',
		    'order'   => 'ASC'
		);
		$users = get_users( $args );
		$list = array('' => 'Pilih Petugas');
		$list_html = '
			<option value="">Pilih Petugas</option>
			<option value="all">Semua Wajib Pajak</option>
		';
		foreach ( $users as $user ) {
		    $list[$user->ID] = esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')';
		    $list_html .= '<option value="'.$user->ID.'">'.$list[$user->ID].'</option>';
		}
		$basic_options_container = Container::make( 'theme_options', __( 'PBB Options' ) )
			->set_page_menu_position( 4 )
	        ->add_fields( array(
	            Field::make( 'text', 'crb_pbb_tahun_anggaran', 'Tahun Anggaran' )
	            	->set_default_value('2021')
	            	->set_attribute('placeholder', '2021'),
	            Field::make( 'text', 'crb_pbb_api_key', 'API KEY' )
	            	->set_default_value($this->generateRandomString())
	            	->set_help_text('Wajib diisi. API KEY digunakan untuk integrasi data.'),
	            Field::make( 'text', 'crb_pbb_provinsi', 'Provinsi' )
	            	->set_attribute('placeholder', 'PROVINSI JAWA TIMUR'),
	            Field::make( 'text', 'crb_pbb_kabupaten', 'Kabupaten / Kota' )
	            	->set_attribute('placeholder', 'KABUPATEN MAGETAN'),
	            Field::make( 'text', 'crb_pbb_kecamatan', 'Kecamatan' )
	            	->set_attribute('placeholder', 'KECAMATAN MAOSPATI'),
	            Field::make( 'text', 'crb_pbb_desa', 'Nama Desa / Kelurahan' )
	            	->set_attribute('placeholder', 'DESA GULUN'),
	            Field::make( 'text', 'crb_pbb_kode_wilayah', 'Kode Desa / Wilayah' )
	            	->set_attribute('placeholder', '63392')
	            	->set_help_text( 'Diisi kode wilayah desa sesuai data di kemendagri atau bisa diisi kode pos desa.' ),
	            Field::make( 'text', 'crb_pbb_alamat', 'Alamat' )
	            	->set_attribute('placeholder', 'Jl. Mangga No. 123'),
	            Field::make( 'text', 'crb_pbb_website', 'Website Desa' )
	            	->set_attribute('placeholder', 'gulun.magetan.go.id'),
	            Field::make( 'text', 'crb_pbb_email', 'Email' )
	            	->set_attribute('placeholder', 'gulun@magetan.go.id'),
	            Field::make( 'text', 'crb_pbb_tlp', 'No. Tlp Desa' )
	            	->set_attribute('placeholder', '085708297100'),
	            Field::make( 'radio', 'crb_status_notif_wa', __( 'Aktifkan notifikasi WA' ) )
				    ->add_options( array(
				        '1' => __( 'Ya' ),
				        '2' => __( 'Tidak' )
				    ) )
	            	->set_default_value('2')
	            	->set_help_text('Bendahara akan mendapatkan notifikasi WA rekapitulasi data perubahan status pembayaran. Wajib pajak juga akan mendapatkan notifikasi WA terkait status pembayaran PBB yang bersangkutan.'),
	            Field::make( 'text', 'crb_pbb_wa_bendahara', 'No. WA Bendahara' )
	            	->set_attribute('placeholder', '085708297100'),
	            Field::make( 'text', 'crb_pbb_api_wa_url', 'URL API WA' )
	            	->set_default_value('http://multics.id/api/v1/')
	            	->set_help_text( 'URL perlu dirubah jika ada udpate terbaru dari layanan woo-wa.com' ),
	            Field::make( 'text', 'crb_pbb_device_key', 'Device Key untuk notifikasi WA' )
	            	->set_attribute('placeholder', 'xxxxxxx-xx-xxx-xxxx-xxxxxxxxxx')
	            	->set_help_text( 'Bisa didapatkan dengan berlangganan API WA di <a href="https://woo-wa.com/?ref=5585" target="blank">https://woo-wa.com/?ref=5585</a>.' ),
	            Field::make( 'textarea', 'crb_pbb_template_notifikasi_wa_bendahara', 'Template Notifikasi WA ke bendahara.' )
	            	->set_default_value( '*Update status data PBB:*'.
						PHP_EOL.
						PHP_EOL.
						'{{data_pajak}}'.
						PHP_EOL.
						PHP_EOL.
						'Tanggal transaksi *{{tgl_bayar}}*'
	            	)
	            	->set_help_text( 'Variable kata di dalam {{..}} akan diganti sesuai data wajib pajak.' ),
	            Field::make( 'textarea', 'crb_pbb_template_notifikasi_wa', 'Template Notifikasi WA ke wajib pajak.' )
	            	->set_default_value( 'Yth. Bpk/Ibu *{{nama_wp}}*,'.
	            		PHP_EOL.
	            		PHP_EOL.
	            		'Status pembayaran pajak atas NOP. *{{nop}}* adalah *{{status_bayar}}*.'.
	            		PHP_EOL.
	            		'Tanggal transaksi *{{tgl_bayar}}*.'.
	            		PHP_EOL.
	            		PHP_EOL.
	            		'Informasi lebih lengkap bisa dilihat di {{url_wp}}'
	            	)
	            	->set_help_text( 'Variable kata di dalam {{..}} akan diganti sesuai data wajib pajak.' ),
	            Field::make( 'textarea', 'crb_pbb_wa_penerima_chatbot', 'No Penerima Chatbot WA PBB Desa.' )
	            	->set_help_text( 'Nomor WA bisa lebih dari dipisah dengan ",". Format nomor harus diawalai dengan 62. Contoh "6285708297100,6285708297104,6285708297101". Nomor ini bisa mendapatkan balasan WA misal dengan mengirimkan pesan WA "pbb-desa-2021" ke nomor WA notifikasi.' )
	        ) );

	    Container::make( 'theme_options', __( 'Pembayaran' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( array(
		    	Field::make( 'html', 'crb_hide_sidebar' )
		        	->set_html( '
		        		<style>
		        			.postbox-container { display: none; }
		        			#poststuff #post-body.columns-2 { margin: 0 !important; }
		        			#table-pembayaran-pbb tfoot tr td, #table-pembayaran-pbb tfoot tr th, #table-pembayaran-pbb thead tr td, #table-pembayaran-pbb thead tr th { box-sizing: border-box; }
		        		</style>
		        	' ),
		        Field::make( 'html', 'crb_referensi_html' )
	            	->set_html( 'Referensi: <a target="_blank" href="https://www.youtube.com/watch?v=yi2nQsTsAtU">https://www.youtube.com/watch?v=yi2nQsTsAtU</a>' ),
		        Field::make( 'html', 'crb_pilih_tahun_html' )
	            	->set_html( '
	            		Tahun Anggaran : <input type="number" id="tahun_anggaran" value="'.date('Y').'" style="margin-right: 20px;">
	            		Pilih Petugas Pajak : <select id="petugas_pajak_bayar" style="min-width: 250px;">'.$list_html.'</select>' ),
		        Field::make( 'html', 'crb_status_bayar_html' )
	            	->set_html( '
	            		Ubah status bayar : 
	            		<select id="status_bayar" style="min-width: 250px; margin-right: 20px;">
	            			'.$this->data_status_bayar(array('type' => 'html')).'
	            		</select>
	            		<a onclick="bayar_pajak(); return false" href="javascript:void(0);" class="button button-primary">Simpan Status Pajak</a>
	            ' ),
		        Field::make( 'html', 'crb_print_html' )
	            	->set_html( ' Pilih Format Laporan : 
	            		<select id="format-laporan-pajak" style="min-width: 250px; margin-right: 20px;">
	            			<option value="">Laporan</option>
	            			<option value="4">Print Laporan Terpilih</option>
	            			<option value="1">Print Laporan Harian</option>
	            			<option value="2">Print Laporan Mingguan</option>
	            			<option value="3">Print Laporan Bulanan</option>
	            		</select>
	            		<a onclick="print_pajak(); return false" href="javascript:void(0);" class="button button-secondary">Print Laporan</a>
	            		<div id="filter-tanggal-pbb" style="display:none; margin-top: 10px;">
	            			<label class="tgl_harian">Pilih Hari <input type="date"/></label>
	            			<label class="start_date" style="margin-right: 20px;">Pilih Tanggal Mulai <input type="date"/></label>
	            			<label class="end_date">Pilih Tanggal Akhir <input type="date"/></label>
	            		</div>
	            	' ),
		        Field::make( 'html', 'crb_wp_html' )
	            	->set_html( '
	            	<table id="table-pembayaran-pbb" class="wp-list-table widefat fixed striped table-view-list">
	            		<thead>
	            			<tr>
	            				<th style="width: 35px;"><input type="checkbox" id="select-all" style="margin:0;"></th>
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
	            			<tr>
	            				<td colspan="9" style="text-align: center;">Data Kosong!</td>
	            			</tr>
	            		</tbody>
	            	</table>' )
		    ) );

	    Container::make( 'theme_options', __( 'Import Wajib Pajak' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( array(
		    	Field::make( 'html', 'crb_hide_sidebar' )
		        	->set_html( '
		        		<style>
		        			.postbox-container { display: none; }
		        			#poststuff #post-body.columns-2 { margin: 0 !important; }
		        		</style>
		        	' ),
		        Field::make( 'html', 'crb_referensi_html' )
	            	->set_html( 'Video Referensi: <a target="_blank" href="https://www.youtube.com/watch?v=yi2nQsTsAtU">https://www.youtube.com/watch?v=yi2nQsTsAtU</a>' ),
		        Field::make( 'html', 'crb_pilih_tahun_html' )
	            	->set_html( 'Tahun Anggaran : <input type="number" id="tahun_anggaran" value="'.date('Y').'">' ),
		        Field::make( 'html', 'crb_petugas_html' )
	            	->set_html( 'Pilih Petugas Pajak : <select id="petugas_pajak" style="min-width: 250px;">'.$list_html.'</select>' ),
		        Field::make( 'html', 'crb_upload_html' )
	            	->set_html( 'Pilih file excel .xlsx : <input type="file" id="file-excel" onchange="filePicked(event);"><br>Contoh format file excel bisa <a target="_blank" href="'.plugin_dir_url( __FILE__ ) . 'excel/contoh.xlsx">download di sini</a>. Sheet file excel yang akan diimport harus diberi nama <b>data</b>. Untuk kolom nilai pajak (<b>KETETAPAN_PBB</b>) ditulis tanpa tanda titik.' ),
		        Field::make( 'html', 'crb_textarea_html' )
	            	->set_html( 'Data JSON : <textarea id="data-excel" class="cf-select__input"></textarea>' ),
		        Field::make( 'html', 'crb_save_button' )
	            	->set_html( '<a onclick="import_excel(); return false" href="javascript:void(0);" class="button button-primary">Import WP</a>' )
		    ) );

	    Container::make( 'post_meta', __( 'Data PBB' ) )
		    ->where( 'post_type', '=', 'wajib_pajak' )
	        ->add_fields( array(
	            Field::make( 'select', 'crb_pbb_status_bayar', 'Status Pembayaran' )
	            	->add_options(  $this->data_status_bayar() ),
	            Field::make( 'date_time', 'crb_pbb_tgl_bayar', 'Tanggal Transaksi' ),
	            Field::make( 'text', 'crb_pbb_tahun_anggaran', 'Tahun Anggaran' ),
	            Field::make( 'select', 'crb_pbb_petugas_pajak', 'Petugas Pajak' )
	            	->add_options(  $list ),
	            Field::make( 'text', 'crb_pbb_nop', 'NOP (Nomor Object Pajak)' ),
	            Field::make( 'text', 'crb_pbb_ketetapan_pbb', 'Nilai Pajak' ),
	            Field::make( 'text', 'crb_pbb_prop', 'Nomor PROP' ),
	            Field::make( 'text', 'crb_pbb_dat', 'Nomor DAT' ),
	            Field::make( 'text', 'crb_pbb_kec', 'Nomor KEC' ),
	            Field::make( 'text', 'crb_pbb_kel', 'Nomor KEL' ),
	            Field::make( 'text', 'crb_pbb_blok', 'Nomor BLOK' ),
	            Field::make( 'text', 'crb_pbb_urut', 'Nomor URUT' ),
	            Field::make( 'text', 'crb_pbb_jns', 'Nomor JNS' ),
	            Field::make( 'text', 'crb_pbb_nama_wp', 'Nama Wajib Pajak' ),
	            Field::make( 'textarea', 'crb_pbb_alamat_wp', 'Alamat Wajib Pajak' ),
	            Field::make( 'textarea', 'crb_pbb_alamat_op', 'Alamat Objek Pajak' ),
	            Field::make( 'map', 'crb_pbb_map_op', 'Map' ),
	            Field::make( 'select', 'crb_pbb_notifikasi_wa', 'Kirim Notifikasi WA' )
	            	->add_options( array(
				        '1' => __( 'Ya' ),
				        '2' => __( 'Tidak' )
				    ) )
	            	->set_default_value('2')
	            	->set_help_text('Wajib pajak akan mendapatkan notifikasi lewat WA setiap kali ada perubahan status pembayaran jika dipilih Ya.'),
	            Field::make( 'text', 'crb_pbb_no_wa', 'Nomor WA' )
	        ) );

	}

	public function create_posttype_pbb(){
	    register_post_type( 'wajib_pajak',
	        array(
	            'labels' => array(
	                'name' => __( 'Wajib Pajak' ),
	                'singular_name' => __( 'Wajib Pajak' )
	            ),
	            'public' => true,
	            'has_archive' => true,
	            'rewrite' => array('slug' => 'wajib_pajak'),
	            'show_in_rest' => true,
	            'show_in_menu' => true,
	            'menu_position' => 4
	        )
	    );
	}

	public function import_excel(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil import excel!'
		);
		if (!empty($_POST)) {
			$ret['data'] = array();
			foreach ($_POST['data'] as $k => $data) {
				$nop = $data['PROP'].'.'.$data['DAT'].'.'.$data['KEC'].'.'.$data['KEL'].'.'.$data['BLOK'].'.'.$data['URUT'].'.'.$data['JNS'];
				$nama_post = $_POST['tahun_anggaran'].' | '.$nop.' | '.$data['NAMA_WP'];
				$custom_post = get_page_by_title($nama_post, OBJECT, 'wajib_pajak');
				$_post = array(
					'post_title'	=> $nama_post,
					'post_content'	=> '[tampilpbb nop="'.$nop.'" tahun_anggaran="'.$_POST['tahun_anggaran'].'"]',
					'post_type'		=> 'wajib_pajak',
					'post_status'	=> 'private',
					'comment_status'	=> 'closed'
				);
				if (empty($custom_post) || empty($custom_post->ID)) {
					wp_insert_post($_post);
					$_post['status'] = 'insert';
				}else{
					$_post['ID'] = $custom_post->ID;
					wp_update_post( $_post );
					$_post['status'] = 'update';
				}
				$ret['data'][] = $_post;
				$custom_post = get_page_by_title($nama_post, OBJECT, 'wajib_pajak');
				update_post_meta( $custom_post->ID, '_crb_pbb_nop', $nop );
				update_post_meta( $custom_post->ID, '_crb_pbb_prop', $data['PROP'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_dat', $data['DAT'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_kec', $data['KEC'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_kel', $data['KEL'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_blok', $data['BLOK'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_urut', $data['URUT'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_jns', $data['JNS'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_nama_wp', $data['NAMA_WP'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_alamat_wp', $data['ALAMAT_WP'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_alamat_op', $data['ALAMAT_OP'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_ketetapan_pbb', $data['KETETAPAN_PBB'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_tahun_anggaran', $_POST['tahun_anggaran'] );
				update_post_meta( $custom_post->ID, '_crb_pbb_petugas_pajak', $_POST['petugas_pajak'] );

				// update astra theme
				update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
				update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
				update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
				update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
				update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
				update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
				update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
				update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	function get_wajib_pajak(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil get wajib pajak!'
		);
		if (!empty($_POST)) {
			$filter_query = array(
		        array(
		            'key'   => '_crb_pbb_tahun_anggaran',
		            'value' => $_POST['tahun_anggaran']
		        )
		    );
		    if(
		    	empty($_POST['petugas_pajak']) 
		    	|| $_POST['petugas_pajak'] != 'all'
		    ){
		    	$filter_query[] = array(
		            'key'   => '_crb_pbb_petugas_pajak',
		            'value' => $_POST['petugas_pajak'],
		        );
		    	$filter_query['relation'] = 'AND';
		    }
			$posts = get_posts(array( 
				'numberposts'	=> -1,
				'post_type' => 'wajib_pajak', 
				'meta_query' => $filter_query,
			    'post_status' => 'private',
			    'meta_key'  => '_crb_pbb_nop',
			    'orderby'   => 'meta_value_num',
			    'order' => 'ASC'
			));
			$data_all = array();
			foreach ( $posts as $post ) {
				$nilai = get_post_meta( $post->ID, '_crb_pbb_ketetapan_pbb', true );
				if(empty($nilai)){
					$nilai = 0;
				}
				$status = get_post_meta( $post->ID, '_crb_pbb_status_bayar', true );
				if(empty($status)){
					$status = 0;
				}
				$nop = get_post_meta( $post->ID, '_crb_pbb_nop', true );
				$nama_wp = get_post_meta( $post->ID, '_crb_pbb_nama_wp', true );
				$user_id = get_post_meta( $post->ID, '_crb_pbb_petugas_pajak', true );

				$user_info = get_userdata($user_id);
				$nama_petugas = $user_info->display_name;
				$data_all[] = array(
					'post_id' => $post->ID,
					'crb_pbb_nop'	=> $nop,
					'crb_pbb_nama_wp'	=> $nama_wp,
					'crb_pbb_alamat_op'	=> get_post_meta( $post->ID, '_crb_pbb_alamat_op', true ),
					'crb_pbb_status_bayar'	=> $status,
					'crb_pbb_ketetapan_pbb'	=> 'Rp '.number_format($nilai,0,",","."),
					'crb_pbb_tgl'	=> get_post_meta( $post->ID, '_crb_pbb_tgl_bayar', true ),
					'crb_pbb_url'	=> get_permalink( $post ),
					'crb_display_name'	=> $nama_petugas
				);
		    }
		    $ret['data'] = $data_all;
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	function send_notif_wa($options = array()){
		$url = get_option('_crb_pbb_api_wa_url').'send-text';
		$device_key = get_option('_crb_pbb_device_key');
		$data = array(
		  "number"  => $options['number'],
		  "message" => $options['message']
		);
		$data_string = http_build_query($data,1);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "device-key: $device_key",
		    'Content-Type: application/x-www-form-urlencoded'
		));
		$result = curl_exec($ch);
		curl_close($ch);
		if($options['debug']){
			die($result);
		}
		return $result;
	}

	function ubah_status_pajak(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil ubah status wajib pajak!'
		);
		if (!empty($_POST)) {
			$status_notif_wa = get_option('_crb_status_notif_wa');
			$tgl_bayar = date('Y-m-d H:i:s');
			$data_bayar = array();
			$status_bayar = $this->data_status_bayar();
			$nilai_total = 0;
			$status_bayar_wp = '';
			$petugas_pajak_all = array();
			foreach ($_POST['data'] as $k => $post_id) {
				update_post_meta( $post_id, '_crb_pbb_status_bayar', $_POST['status'] );
				update_post_meta( $post_id, '_crb_pbb_tgl_bayar', $tgl_bayar );
				
				if($status_notif_wa == 1){
					$petugas_pajak = get_post_meta($post_id, '_crb_pbb_petugas_pajak', true);
					if(empty($petugas_pajak_all[$petugas_pajak])){
						$petugas_pajak_all[$petugas_pajak] = array();
					}
					$nama = get_post_meta( $post_id, '_crb_pbb_nama_wp', true );
					$nilai = get_post_meta( $post_id, '_crb_pbb_ketetapan_pbb', true );
					$nop = get_post_meta( $post_id, '_crb_pbb_nop', true );
					if(empty($nilai)){
						$nilai = 0;
					}
					$nilai_total += $nilai;
					$status_bayar_wp = $status_bayar[$_POST['status']];
					$keterangan_pajak = 'a/n '.$nama.' | NOP. '.$nop.' | Rp '.number_format($nilai,0,",",".");
					$data_bayar[] = $keterangan_pajak;
					$petugas_pajak_all[$petugas_pajak][] = array(
						'keterangan' => $keterangan_pajak,
						'nilai' => $nilai
					);
				}

				// kirim notif WA ke wajib pajak
				$status_notif_wa_wp = get_post_meta($post_id, '_crb_pbb_notifikasi_wa', true);
				if($status_notif_wa_wp == 1){
					$no_wp = get_post_meta($post_id, '_crb_pbb_no_wa', true);
					if(!empty($no_wp)){
						$pesan = get_option('_crb_pbb_template_notifikasi_wa');
						$pesan = str_replace(array(
							'{{nama_wp}}',
							'{{nop}}',
							'{{status_bayar}}',
							'{{tgl_bayar}}',
							'{{url_wp}}'
						), array(
							$nama,
							$nop,
							$status_bayar_wp,
							$tgl_bayar,
							$this->get_link_post($post_id, true)
						), $pesan);
						$this->send_notif_wa(array(
							'number' => $no_wp,
							'message' => $pesan
						));
					}
				}
			}
			/*
			$data = array(
	    		'' => 'Pilih Status Pembayaran',
	    		'0' => 'Belum Bayar',
	    		'1' => 'Diterima Petugas Pajak',
	    		'2' => 'Diterima Bendahara Desa',
	    		'3' => 'Diterima Kecamatan',
	    		'4' => 'Lunas'
	    	);
	    	*/
			if($status_notif_wa == 1){
				$pesan = get_option('_crb_pbb_template_notifikasi_wa_bendahara');
				$pesan = str_replace(array(
					'{{data_pajak}}',
					'{{tgl_bayar}}'
				), array(
					implode(PHP_EOL, $data_bayar).
					PHP_EOL.
					PHP_EOL.
					'Status pembayaran: *'.$status_bayar_wp.'*'.
					PHP_EOL.
					'Total *Rp '.number_format($nilai_total,0,",",".").'*',
					$tgl_bayar
				), $pesan);

				$no_bendahara = get_option('_crb_pbb_wa_bendahara');
				foreach ($petugas_pajak_all as $petugas => $data_bayar_petugas) {
					if(!empty($petugas)){
						$no_wa_petugas = get_user_meta( $petugas, 'whatsapp', true );
						if(!empty($no_wa_petugas)){
							$ket_wp = array();
							$nilai_total_petugas = 0;
							foreach ($data_bayar_petugas as $ket) {
								$ket_wp[] = $ket['keterangan'];
								$nilai_total_petugas += $ket['nilai'];
							}
							$pesan_petugas = get_option('_crb_pbb_template_notifikasi_wa_bendahara');
							$pesan_petugas = str_replace(array(
								'{{data_pajak}}',
								'{{tgl_bayar}}'
							), array(
								implode(PHP_EOL, $ket_wp).
								PHP_EOL.
								PHP_EOL.
								'Status pembayaran: *'.$status_bayar_wp.'*'.
								PHP_EOL.
								'Total *Rp '.number_format($nilai_total_petugas,0,",",".").'*',
								$tgl_bayar
							), $pesan_petugas);
							$this->send_notif_wa(array(
								'number' => $no_wa_petugas,
								'message' => $pesan_petugas
							));
						}
					}else{
						if(!empty($no_bendahara)){
							$this->send_notif_wa(array(
								'number' => $no_bendahara,
								'message' => $pesan.PHP_EOL.PHP_EOL.'*Petugas Pajak belum disetting untuk NOP ini!*'
							));
						}
					}
				}

				// jika status bayar lebih sudah diterima bendahara dst, maka bendahara mendapat notifikasi
				if($_POST['status'] >= 2){
					if(!empty($no_bendahara)){
						$this->send_notif_wa(array(
							'number' => $no_bendahara,
							'message' => $pesan
						));
					}
				}
			}
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	function get_url_print_pbb(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil ubah status wajib pajak!'
		);
		if (!empty($_POST)) {
			$nama_post = 'Print PBB Desa '.$_POST['tahun_anggaran'];
			$custom_post = get_page_by_title($nama_post, OBJECT, 'page');
			$_post = array(
				'post_title'	=> $nama_post,
				'post_content'	=> '[printpbb tahun_anggaran='.$_POST['tahun_anggaran'].']',
				'post_type'		=> 'page',
				'post_status'	=> 'private',
				'comment_status'	=> 'closed'
			);
			if (empty($custom_post) || empty($custom_post->ID)) {
				wp_insert_post($_post);
			}else{
				$_post['ID'] = $custom_post->ID;
				wp_update_post( $_post );
			}

			$custom_post = get_page_by_title($nama_post, OBJECT, 'page');
			// update astra theme
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');

			$ret['url'] = esc_url( get_page_link( $custom_post->ID ) );
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	public function printpbb($atts){
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/pbb-desa-admin-display.php';
	}

	function gen_key($key_db = false, $lifetime = false){
		$now = time()*1000;
		if(empty($key_db)){
			$key_db = get_option( '_crb_pbb_api_key' );
		}
		$key_db = md5($key_db);
		if($lifetime){
			$key = base64_encode($now.$key_db.$now.$key_db.'ok');
		}else{
			$key = base64_encode($now.$key_db.$now);
		}
		return $key;
	}

    function allow_access_private_post(){
    	if(
    		!empty($_GET) 
    		&& !empty($_GET['key'])
    	){
    		$key = base64_decode($_GET['key']);
    		$key_db = get_option( '_crb_pbb_api_key' );
    		$key = explode(md5($key_db), $key);
    		$valid = 0;
    		if(!empty($key[2]) && $key[2]=='ok'){
    			$valid = 1;
    		}else if(
    			!empty($key[1]) 
    			&& $key[0] == $key[1]
    			&& is_numeric($key[1])
    		){
    			$tgl1 = new DateTime();
    			$date = substr($key[1], 0, strlen($key[1])-3);
    			$tgl2 = new DateTime(date('Y-m-d', $date));
    			$valid = $tgl2->diff($tgl1)->days+1;
    		}
    		if($valid == 1){
	    		global $wp_query;
		        // print_r($wp_query);
		        // print_r($wp_query->queried_object); die('tes');
		        if(!empty($wp_query->queried_object)){
		    		if($wp_query->queried_object->post_status == 'private'){
						wp_update_post(array(
					        'ID'    =>  $wp_query->queried_object->ID,
					        'post_status'   =>  'publish'
				        ));
				        die('<script>window.location =  window.location.href;</script>');
					}else{
						wp_update_post(array(
					        'ID'    =>  $wp_query->queried_object->ID,
					        'post_status'   =>  'private'
				        ));
					}
				}else if($wp_query->found_posts >= 1){
					global $wpdb;
					$sql = $wp_query->request;
					$post = $wpdb->get_results($sql, ARRAY_A);
					if(!empty($post)){
						if($post[0]['post_status'] == 'private'){
							wp_update_post(array(
						        'ID'    =>  $post[0]['ID'],
						        'post_status'   =>  'publish'
					        ));
					        die('<script>window.location =  window.location.href;</script>');
						}else{
							wp_update_post(array(
						        'ID'    =>  $post[0]['ID'],
						        'post_status'   =>  'private'
					        ));
						}
					}
				}
			}
    	}
    }

    function listen_wa(){
    	$json = file_get_contents('php://input');
		$data = json_decode($json);
		$file = plugin_dir_path(dirname(__FILE__))."listen_wa.txt";
		file_put_contents($file, print_r($data,1));
		$penerima = get_option('_crb_pbb_wa_penerima_chatbot');
		if(!empty($penerima)){
			$semua_penerima = explode(',', $penerima);
			$pengirim = $data->data->number;
			$pesan = $data->data->message;
			$tahun_anggaran = get_option('_crb_pbb_tahun_anggaran');
			foreach ($semua_penerima as $k => $no_penerima) {
				if($no_penerima == $pengirim){
					if(strpos($pesan, 'pbb-desa-'.$tahun_anggaran) !== false){
						$posts = get_posts(array( 
							'numberposts'	=> -1,
							'post_type' => 'wajib_pajak', 
							'meta_query' => array(
						        array(
						            'key'   => '_crb_pbb_tahun_anggaran',
						            'value' => $tahun_anggaran
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
						$nama_post = $tahun_anggaran.' | PBB Desa';
						$custom_post = get_page_by_title($nama_post, OBJECT, 'page');

						$replay = '*PBB Desa '.$tahun_anggaran.'*'.
							PHP_EOL.
							PHP_EOL.
							'Jumlah Objek Pajak: *'.count($posts).' Objek*'.
							PHP_EOL.
							'Total Pajak: *Rp '.number_format($total_pajak,0,",",".").'*'.
							PHP_EOL.
							'Belum Bayar: *Rp '.number_format($total_belum_bayar,0,",",".").'*'.
							PHP_EOL.
							'Diterima Petugas Pajak: *Rp '.number_format($total_diterima_petugas_pajak,0,",",".").'*'.
							PHP_EOL.
							'Diterima Bendahara Desa: *Rp '.number_format($total_diterima_bendahara_desa,0,",",".").'*'.
							PHP_EOL.
							'Diterima Kecamatan: *Rp '.number_format($total_diterima_kecamatan,0,",",".").'*'.
							PHP_EOL.
							'Lunas: *Rp '.number_format($total_lunas,0,",",".").'*'.
							PHP_EOL.
							PHP_EOL.
							'Informasi lebih detail bisa dilihat di *'.$this->get_link_post($custom_post, true).'*';

						$this->send_notif_wa(array(
							'number' => $pengirim,
							'message' => $replay,
							'debug' => false
						));
					}
				}
			}
		}
		die($file);
    }

    public function get_link_post($custom_post, $forever = false){
		$link = get_permalink($custom_post);
		if(strpos($link, '?') === false){
			$link .= '?key=' . $this->gen_key(false, $forever);
		}else{
			$link .= '&key=' . $this->gen_key(false, $forever);
		}
		return $link;
	}

	public function crb_edit_save($save, $value, $field){
		if($field->get_name() == '_crb_pbb_tahun_anggaran'){
			$nama_post = $value.' | PBB Desa';
			$custom_post = get_page_by_title($nama_post, OBJECT, 'page');
			$_post = array(
				'post_title'	=> $nama_post,
				'post_content'	=> '[monitor_all_pajak tahun_anggaran="'.$value.'"]',
				'post_type'		=> 'page',
				'post_status'	=> 'private',
				'comment_status'	=> 'closed'
			);
			if (empty($custom_post) || empty($custom_post->ID)) {
				wp_insert_post($_post);
				$custom_post = get_page_by_title($nama_post, OBJECT, 'page');
				update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
				update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
				update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
				update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
				update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
				update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
				update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
				update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
			}
			return $value;
		}else{
			return $value;
		}
	}
}

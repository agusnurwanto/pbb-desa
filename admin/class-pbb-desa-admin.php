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
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pbb-desa-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'pbb', array(
		    'status_bayar' => $this->data_status_bayar(array('type' => 'html_color'))
		));

	}

	public function data_status_bayar($option = array('type' => false)){
		$data = array(
    		'' => 'Pilih Status Pembayaran',
    		'4' => 'Lunas',
    		'3' => 'Diterima Kecamatan',
    		'2' => 'Diterima Bendahara Desa',
    		'1' => 'Diterima Petugas Pajak',
    		'0' => 'Belum Bayar'
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

	public function crb_attach_pbb_options(){
		global $wpdb;

		$args = array(
		    'role'    => 'petugas_pajak',
		    'orderby' => 'user_nicename',
		    'order'   => 'ASC'
		);
		$users = get_users( $args );
		$list = array('' => 'Pilih Petugas');
		$list_html = '<option value="">Pilih Petugas</option>';
		foreach ( $users as $user ) {
		    $list[$user->ID] = esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')';
		    $list_html .= '<option value="'.$user->ID.'">'.$list[$user->ID].'</option>';
		}
		$basic_options_container = Container::make( 'theme_options', __( 'PBB Options' ) )
			->set_page_menu_position( 4 )
	        ->add_fields( array(
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
	            Field::make( 'text', 'crb_pbb_tlp', 'No. Tlp' )
	            	->set_attribute('placeholder', '085708297100'),
	        ) );

	    Container::make( 'theme_options', __( 'Pembayaran' ) )
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
	            	->set_html( 'Referensi: <a target="_blank" href="https://www.youtube.com/watch?v=UIGDx_6XRV8">https://www.youtube.com/watch?v=UIGDx_6XRV8</a>' ),
		        Field::make( 'html', 'crb_pilih_tahun_html' )
	            	->set_html( 'Tahun Anggaran : <input type="number" id="tahun_anggaran" value="'.date('Y').'">' ),
		        Field::make( 'html', 'crb_petugas_html' )
	            	->set_html( 'Pilih Petugas Pajak : <select id="petugas_pajak_bayar" style="min-width: 250px;">'.$list_html.'</select>' ),
		        Field::make( 'html', 'crb_status_bayar_html' )
	            	->set_html( '
	            		Ubah status bayar : 
	            		<select id="status_bayar" style="min-width: 250px;">
	            			'.$this->data_status_bayar(array('type' => 'html')).'
	            		</select>
	            ' ),
		        Field::make( 'html', 'crb_aksi_html' )
	            	->set_html( '
	            		<a onclick="bayar_pajak(); return false" href="javascript:void(0);" class="button button-primary">Simpan Status Pajak</a>
	            		<a onclick="print_pajak(); return false" href="javascript:void(0);" class="button button-secondary">Print Laporan Pajak</a>' ),
		        Field::make( 'html', 'crb_wp_html' )
	            	->set_html( '
	            	<table id="table-pembayaran-pbb" class="wp-list-table widefat fixed striped table-view-list">
	            		<thead>
	            			<tr>
	            				<th style="width: 20px;"><input type="checkbox" id="select-all" style="margin:0;"></th>
	            				<th style="width: 20px;">No</th>
	            				<th style="width: 170px;">No. Object Pajak</th>
	            				<th style="width: 170px;">Nama Wajib Pajak</th>
	            				<th>Alamat</th>
	            				<th style="width: 170px;">Status Pembayaran</th>
	            				<th style="width: 100px;">Nilai Pajak</th>
	            				<th style="width: 125px;">Tgl. Transaksi</th>
	            			</tr>
	            		</thead>
	            		<tbody>
	            			<tr>
	            				<td colspan="6" style="text-align: center;">Data Kosong!</td>
	            			</tr>
	            		</tbody>
	            	</table>' )
		    ) );

	    Container::make( 'theme_options', __( 'Import Wajib Pajak' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( array(
		        Field::make( 'html', 'crb_referensi_html' )
	            	->set_html( 'Video Referensi: <a target="_blank" href="https://www.youtube.com/watch?v=UIGDx_6XRV8">https://www.youtube.com/watch?v=UIGDx_6XRV8</a>' ),
		        Field::make( 'html', 'crb_pilih_tahun_html' )
	            	->set_html( 'Tahun Anggaran : <input type="number" id="tahun_anggaran" value="'.date('Y').'">' ),
		        Field::make( 'html', 'crb_petugas_html' )
	            	->set_html( 'Pilih Petugas Pajak : <select id="petugas_pajak" style="min-width: 250px;">'.$list_html.'</select>' ),
		        Field::make( 'html', 'crb_upload_html' )
	            	->set_html( 'Pilih file excel .xlsx : <input type="file" id="file-excel" onchange="filePicked(event);"><br>Contoh format file excel bisa <a target="_blank" href="'.plugin_dir_url( __FILE__ ) . 'excel/contoh.xlsx">download di sini</a>.' ),
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
	            Field::make( 'date_time', 'crb_pbb_tgl_bayar', 'Tanggal Bayar' ),
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
	            Field::make( 'map', 'crb_pbb_map_op', 'Map' )
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
	            'show_in_rest' => true
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
			$posts = get_posts(array( 
				'numberposts'	=> -1,
				// 'posts_per_page'	=> 50,
        		// 'offset'	=> 0,
				'post_type' => 'wajib_pajak', 
				'meta_query' => array(
			        array(
			            'key'   => '_crb_pbb_petugas_pajak',
			            'value' => $_POST['petugas_pajak'],
			        ),
			        array(
			            'key'   => '_crb_pbb_tahun_anggaran',
			            'value' => $_POST['tahun_anggaran']
			        ),
        			'relation' => 'AND'
			    ),
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
				$data_all[] = array(
					'post_id' => $post->ID,
					'crb_pbb_nop'	=> get_post_meta( $post->ID, '_crb_pbb_nop', true ),
					'crb_pbb_nama_wp'	=> get_post_meta( $post->ID, '_crb_pbb_nama_wp', true ),
					'crb_pbb_alamat_op'	=> get_post_meta( $post->ID, '_crb_pbb_alamat_op', true ),
					'crb_pbb_status_bayar'	=> $status,
					'crb_pbb_ketetapan_pbb'	=> 'Rp '.number_format($nilai,0,",","."),
					'crb_pbb_tgl'	=> get_post_meta( $post->ID, '_crb_pbb_tgl_bayar', true )
				);
		    }
		    $ret['data'] = $data_all;
		} else {
			$ret['status'] = 'error';
			$ret['message'] = 'Format Salah!';
		}
		die(json_encode($ret));
	}

	function ubah_status_pajak(){
		global $wpdb;
		$ret = array(
			'status'	=> 'success',
			'message'	=> 'Berhasil ubah status wajib pajak!'
		);
		if (!empty($_POST)) {
			foreach ($_POST['data'] as $k => $post_id) {
				update_post_meta( $post_id, '_crb_pbb_status_bayar', $_POST['status'] );
				update_post_meta( $post_id, '_crb_pbb_tgl_bayar', date('Y-m-d H:i:s') );
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

	public function printpbb($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/pbb-desa-admin-display.php';
	}
}

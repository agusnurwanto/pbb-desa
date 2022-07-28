<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/agusnurwanto
 * @since      1.0.0
 *
 * @package    Pbb_Desa
 * @subpackage Pbb_Desa/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pbb_Desa
 * @subpackage Pbb_Desa/public
 * @author     Agus Nurwanto <agusnurwantomuslim@gmail.com>
 */
class Pbb_Desa_Public {

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

	private $functions;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $functions ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->functions = $functions;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name . 'bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . 'datatables', plugin_dir_url(__FILE__) . 'css/jquery.dataTables.min.css', array(), $this->version, 'all');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pbb-desa-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		wp_enqueue_script($this->plugin_name . 'bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.bundle.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name . 'datatables', plugin_dir_url(__FILE__) . 'js/jquery.dataTables.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pbb-desa-public.js', array( 'jquery' ), $this->version.'.'.time(), false );
		wp_enqueue_script($this->plugin_name . 'chart', plugin_dir_url(__FILE__) . 'js/chart.min.js', array('jquery'), $this->version, false);
		wp_localize_script( $this->plugin_name, 'pbb', array(
		    'status_bayar' => $this->data_status_bayar(array('type' => 'html_color'))
		));

	}

	public function tampilpbb($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/pbb-desa-public-display.php';
	}

	public function monitor_all_pajak($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/pbb-desa-monev-all.php';
	}
	
	public function monitor_all_pajak_pengawas($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/pbb-desa-monev-all-pengawas.php';
	}

	public function manajemen_pbb($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/pbb-desa-manajemen.php';
	}

	public function manajemen_pbb_pengawas($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}

		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/pbb-desa-manajemen-pengawas.php';
	}

	public function menu_manajemen_pbb($atts)
	{

		$user_id = get_current_user_id();
		$user_meta = get_userdata($user_id);
		$user_role = $user_meta->roles;
		$tahun = get_option('_crb_pbb_tahun_anggaran'); 
		$desa = get_option('_crb_pbb_desa'); 
		$laporan_pbb = 'Pengawas PBB Desa '.$desa.' tahun '.$tahun;
		$content = '[monitor_all_pajak_pengawas tahun_anggaran="'.$tahun.'"]';

		$link_monev = $this->generatePage($laporan_pbb, $content);

		if ($user_role[0] == 'pengawas_pajak') {
			$nama_page = 'Manajemen Pajak Desa Pengawas';
			$url_page = $this->function->generatePage($nama_page, '[manajemen_pbb_pengawas]');
			echo '
				<ul class="pbb-desa-manajemen">
					<li><a href="'.$url_page.'" target="_blank" class="btn btn-info">'.$nama_page.'</a></li><br>
					<li><a target="_blank" class="btn btn-info" href="'.$link_monev.'">'.$laporan_pbb.'</a></li>
				</ul>
				<style>
					.pbb-desa-manajemen{
						margin: 0;
					}
					.pbb-desa-manajemen li {
						list-style: none;
						text-align: center;
					}
				</style>
			';
		}else {
			$nama_page = 'Manajemen Pajak Desa';
			$url_page = $this->function->generatePage($nama_page, '[manajemen_pbb]');
			echo '
				<ul class="pbb-desa-manajemen">
					<li><a href="'.$url_page.'" target="_blank" class="btn btn-info">'.$nama_page.'</a></li>
				</ul>
				<style>
					.pbb-desa-manajemen{
						margin: 0;
					}
					.pbb-desa-manajemen li {
						list-style: none;
						text-align: center;
					}
				</style>
			';
		}
	}

	public function data_status_bayar($option = array('type' => false), $role=''){
		$data = array(
			'' => 'Pilih Status Pembayaran',
			'0' => 'Belum Bayar',
			'1' => 'Diterima Petugas Pajak',
			'2' => 'Diterima Bendahara Desa',
			'3' => 'Diterima Kecamatan',
			'4' => 'Lunas'
		);

		// key array disesuaikan dengan array di atas
		if ($role == 'petugas_pajak') {
			$data = array(
				'' => 'Pilih Status Pembayaran',
				'0' => 'Belum Bayar',
				'1' => 'Diterima Petugas Pajak',
			);
		}else if ($role == 'pengawas_pajak') {
			$data = array(
				'' => 'Pilih Status Pembayaran',
				'0' => 'Belum Bayar',
				'4' => 'Lunas'
			);
		}


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

	function myplugin_ajaxurl() {
		echo '<script type="text/javascript">
				var ajaxurl = "' . admin_url('admin-ajax.php') . '";
			  </script>';
	 }

	public function get_data_pajak_datatable(){
		global $wpdb;
		$params = $_REQUEST;
		$colums_sort = $params['columns'][$params['order'][0]['column']]['data'];
		$colums_sort_order = $params['order'][0]['dir'];

		$filter_query = array(
	        array(
	            'key'   => '_crb_pbb_tahun_anggaran',
	            'value' => $params['tahun_anggaran']
	        )
	    );
	    if(!empty($params['petugas_pajak'])){
	    	$filter_query[] = array(
	            'key'   => '_crb_pbb_petugas_pajak',
	            'value' => $params['petugas_pajak'],
	        );
	    }

		// check search value exist
		if( !empty($params['search']['value']) ) {
	    	$filter_query[] = array(
	    		'relation' => 'OR',
	    		array(
		            'key'   => '_crb_pbb_nama_wp',
		            'value' => $params['search']['value'],
		            'compare' => 'LIKE'
		        ),
		        array(
		            'key'   => '_crb_pbb_nop',
		            'value' => $params['search']['value'],
		            'compare' => 'LIKE'
		        )
		    );
		}

		$opsi = array( 
			'numberposts'	=> -1,
			'post_type' => 'wajib_pajak', 
			'meta_query' => $filter_query,
		    'post_status' => 'private',
		    'meta_key'  => '_crb_pbb_nop',
		    'orderby'   => 'meta_value_num',
		    'order' => 'ASC'
		);
		if(!empty($colums_sort)){
			$opsi['meta_key'] = '_'.$colums_sort;
			$opsi['order'] = $colums_sort_order;
			if($colums_sort == 'crb_pbb_nama_wp'){
				$opsi['orderby'] = 'meta_value';
			}
		}
		$posts_all = get_posts($opsi);

		$opsi['numberposts'] = $params['length'];
		$opsi['offset'] = $params['start'];
		$opsi['meta_query'] = $filter_query;

		$posts = get_posts($opsi);
		$queryRecords = array();

		foreach ( $posts as $post ) {
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
			$user_id = get_post_meta( $post->ID, '_crb_pbb_petugas_pajak', true );

			$user_info = get_userdata($user_id);
			$nama_petugas = $user_info->display_name;
			$queryRecords[] = array(
				'post_id' => $post->ID,
				'crb_pbb_nop'	=> $nop,
				'crb_pbb_nama_wp'	=> $nama_wp,
				'crb_pbb_alamat_op'	=> get_post_meta( $post->ID, '_crb_pbb_alamat_op', true ),
				'crb_pbb_status_bayar'	=> $status_bayar_wp,
				'crb_pbb_ketetapan_pbb'	=> number_format($nilai,0,",","."),
				'crb_pbb_tgl'	=> get_post_meta( $post->ID, '_crb_pbb_tgl_bayar', true ),
				'crb_pbb_url'	=> get_permalink( $post ),
				'crb_display_name'	=> $nama_petugas
			);
	    }

	    $json_data = array(
			"draw"            => intval( $params['draw'] ),   
			"recordsTotal"    => count($posts_all),  
			"recordsFiltered" => count($posts_all),
			"data"            => $queryRecords
		);

		die(json_encode($json_data));
	}
}

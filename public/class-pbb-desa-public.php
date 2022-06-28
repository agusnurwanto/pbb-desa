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

	public function manajemen_pbb($atts)
	{
		// untuk disable render shortcode di halaman edit page/post
		if(!empty($_GET) && !empty($_GET['post'])){
			return '';
		}
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/pbb-desa-manajemen.php';
	}

	public function menu_manajemen_pbb($atts)
	{
		$nama_page = 'Manajemen Pajak Desa';
		$url_page = $this->generatePage($nama_page, '[manajemen_pbb]');
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

	public function get_link_post($custom_post){
		$link = get_permalink($custom_post);
		$options = array();
		if(!empty($custom_post->custom_url)){
			$options['custom_url'] = $custom_post->custom_url;
		}
		if(strpos($link, '?') === false){
			$link .= '?key=' . $this->gen_key(false, $options);
		}else{
			$link .= '&key=' . $this->gen_key(false, $options);
		}
		return $link;
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
		}else if ($role == 'pengawas') {
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

	public function decode_key($value){
		$key = base64_decode($value);
		$key_db = md5(get_option( '_crb_pbb_api_key' ));
		$key = explode($key_db, $key);
		$get = array();
		if(!empty($key[2])){
			$all_get = explode('&', $key[2]);
			foreach ($all_get as $k => $v) {
				$current_get = explode('=', $v);
				$get[$current_get[0]] = $current_get[1];
			}
		}
		return $get;
	}

	function gen_key($key_db = false, $options = array()){
		$now = time()*1000;
		if(empty($key_db)){
			$key_db = md5(get_option( '_crb_pbb_api_key' ));
		}
		$tambahan_url = '';
		if(!empty($options['custom_url'])){
			$custom_url = array();
			foreach ($options['custom_url'] as $k => $v) {
				$custom_url[] = $v['key'].'='.$v['value'];
			}
			$tambahan_url = $key_db.implode('&', $custom_url);
		}
		$key = base64_encode($now.$key_db.$now.$tambahan_url);
		return $key;
	}

	public function generatePage($nama_page, $content = false, $update = false){
		$custom_post = get_page_by_title($nama_page, OBJECT, 'page');

		$_post = array(
			'post_title'	=> $nama_page,
			'post_content'	=> $content,
			'post_type'		=> 'page',
			'post_status'	=> 'private',
			'comment_status'	=> 'closed'
		);
		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post($_post);
			$_post['insert'] = 1;
			$_post['ID'] = $id;
			$custom_post = get_page_by_title($nama_page, OBJECT, 'page');
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
			update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		}else if($update){
			$_post['ID'] = $custom_post->ID;
			wp_update_post( $_post );
			$_post['update'] = 1;
		}
		return $this->get_link_post($custom_post);
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

}

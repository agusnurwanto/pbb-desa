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
	            Field::make( 'select', 'crb_pbb_petugas_pajak', 'Pilih Petugas Pajak' )
    				->add_options(  $list )
	        ) );

	    Container::make( 'theme_options', __( 'Pembayaran' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( array(
		        Field::make( 'html', 'crb_referensi_html' )
	            	->set_html( 'Referensi: <a target="_blank" href="https://www.youtube.com/watch?v=UIGDx_6XRV8">https://www.youtube.com/watch?v=UIGDx_6XRV8</a>' ),
		        Field::make( 'html', 'crb_petugas_html' )
	            	->set_html( '<select id="petugas_pajak" class="cf-select__input">'.$list_html.'</select>' ),
		        Field::make( 'html', 'crb_wp_html' )
	            	->set_html( '
	            	<table class="wp-list-table widefat fixed striped table-view-list">
	            		<thead>
	            			<tr>
	            				<th><input type="checkbox"></th>
	            				<th>No</th>
	            				<th>No. Object Pajak</th>
	            				<th>Nama Wajib Pajak</th>
	            				<th>Alamat</th>
	            				<th>Pajak</th>
	            			</tr>
	            		</thead>
	            		<tbody>
	            		</tbody>
	            	</table>' )
		    ) );

	    Container::make( 'theme_options', __( 'Import Wajib Pajak' ) )
		    ->set_page_parent( $basic_options_container )
		    ->add_fields( array(
		        Field::make( 'html', 'crb_referensi_html' )
	            	->set_html( 'Video Referensi: <a target="_blank" href="https://www.youtube.com/watch?v=UIGDx_6XRV8">https://www.youtube.com/watch?v=UIGDx_6XRV8</a>' ),
		        Field::make( 'html', 'crb_upload_html' )
	            	->set_html( 'Pilih file excel .xlsx : <input type="file" id="file-excel" onchange="filePicked(event);">' ),
		        Field::make( 'html', 'crb_textarea_html' )
	            	->set_html( 'Data JSON : <textarea id="data-excel" class="cf-select__input"></textarea>' ),
		        Field::make( 'html', 'crb_save_button' )
	            	->set_html( '<a onclick="import_excel(); return false" href="javascript:void(0);" class="button button-primary">Import WP</a>' )
		    ) );

	    Container::make( 'post_meta', __( 'Data PBB' ) )
		    ->where( 'post_type', '=', 'wajib_pajak' )
	        ->add_fields( array(
	            Field::make( 'select', 'crb_pbb_petugas_pajak', 'Petugas Pajak' )
	            	->add_options(  $list ),
	            Field::make( 'text', 'crb_pbb_nop', 'NOP (Nomor Object Pajak)' ),
	            Field::make( 'text', 'crb_pbb_nama', 'Nama Wajib Pajak' ),
	            Field::make( 'textarea', 'crb_pbb_alamat', 'Alamat' ),
	            Field::make( 'map', 'crb_pbb_map', 'Map' ),
	            Field::make( 'text', 'crb_pbb_nilai', 'Nilai Pajak' )
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

}

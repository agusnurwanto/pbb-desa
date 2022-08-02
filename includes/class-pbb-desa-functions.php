<?php
class Pbb_Desa_Functions
{

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	
	private $opsi_nilai_rincian;

	private $status_koneksi_simda;
	
	public $custom_mapping;

	public function __construct($plugin_name, $version){

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->status_koneksi_simda = true;
	}

    function allow_access_private_post(){
    	if(
    		!empty($_GET) 
    		&& !empty($_GET['key'])
    	){
    		$key = base64_decode($_GET['key']);
    		$decode = $this->decode_key($_GET['key']);
    		if(!empty($decode['skip'])){
    			return;
    		}
    		
    		$key_db = md5(get_option( '_crb_apikey_simda_bmd' ));
    		$key = explode($key_db, $key);
    		$valid = 0;
    		if(
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

	function gen_key($key_db = false, $options = array()){
		$now = time()*1000;
		if(empty($key_db)){
			$key_db = md5(get_option( '_crb_apikey_simda_bmd' ));
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

	public function decode_key($value){
		$key = base64_decode($value);
		$key_db = md5(get_option( '_crb_apikey_simda_bmd' ));
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

	public function generatePage($options = array()){
		$post_type = 'page';
		$status = 'private';
		if(!empty($options['post_status'])){
			$status = $options['post_status'];
		}
		if(!empty($options['post_type'])){
			$post_type = $options['post_type'];
		}

		if(!empty($options['post_id'])){
			$custom_post = get_page($options['post_id']);
		}else{
			$custom_post = get_page_by_title($options['nama_page'], OBJECT, $post_type);
		}
		$_post = array(
			'post_title'	=> $options['nama_page'],
			'post_content'	=> $options['content'],
			'post_type'		=> $post_type,
			'post_status'	=> $status,
			'comment_status'	=> 'closed'
		);
		if (empty($custom_post) || empty($custom_post->ID)) {
			$id = wp_insert_post($_post);
			$_post['insert'] = 1;
			$_post['ID'] = $id;
			$custom_post = get_page_by_title($options['nama_page'], OBJECT, $post_type);
			if(empty($options['show_header'])){
				update_post_meta($custom_post->ID, 'ast-main-header-display', 'disabled');
				update_post_meta($custom_post->ID, 'footer-sml-layout', 'disabled');
			}
			update_post_meta($custom_post->ID, 'ast-breadcrumbs-content', 'disabled');
			update_post_meta($custom_post->ID, 'ast-featured-img', 'disabled');
			update_post_meta($custom_post->ID, 'site-content-layout', 'page-builder');
			update_post_meta($custom_post->ID, 'site-post-title', 'disabled');
			update_post_meta($custom_post->ID, 'site-sidebar-layout', 'no-sidebar');
			update_post_meta($custom_post->ID, 'theme-transparent-header-meta', 'disabled');
		}else if(!empty($options['update'])){
			$_post['ID'] = $custom_post->ID;
			wp_update_post( $_post );
			$_post['update'] = 1;
		}
		if(!empty($options['custom_url'])){
			$custom_post->custom_url = $options['custom_url'];
		}
		if(!empty($options['no_key'])){
			$link = get_permalink($custom_post);
		}else{
			$link = $this->get_link_post($custom_post);
		}
		return array(
			'post' => $custom_post,
			'id' => $custom_post->ID,
			'title' => $options['nama_page'],
			'url' => $link
		);
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

	public function CekNull($number, $length=2){
        $l = strlen($number);
        $ret = '';
        for($i=0; $i<$length; $i++){
            if($i+1 > $l){
                $ret .= '0';
            }
        }
        $ret .= $number;
        return $ret;
    }

	function user_has_role($user_id, $role_name, $return=false){
		if(empty($user_id)){
			return false;
		}
	    $user_meta = get_userdata($user_id);
	    $user_roles = $user_meta->roles;
	    if($return){
	    	return $user_roles;
	    }else{
	    	return in_array($role_name, $user_roles);
	    }
	}

	function get_option_complex($key, $type){
		global $wpdb;
        $ret = $wpdb->get_results('select option_name, option_value from '.$wpdb->prefix.'options where option_name like \''.$key.'|%\'', ARRAY_A);
        $res = array();
        $types = array();
        foreach($ret as $v){
            $k = explode('|', $v['option_name']);
            $column = $k[1];
            $group = $k[3];
            if($column == ''){
            	$types[$group] = $v['option_value'];
            }
        }
        foreach($ret as $v){
            $k = explode('|', $v['option_name']);
            $column = $k[1];
            $loop = $k[2];
            $group = $k[3];
            if($column != ''){
	            if(
	            	isset($types[$loop])
	            	&& $type == $types[$loop]
	            ){
		            if(empty($res[$loop])){
		                $res[$loop] = array();
		            }
		            $res[$loop][$column] = $v['option_value'];
		        }
		    }
        }
        return $res;
    }

	function get_option_multiselect($key){
		global $wpdb;
        $ret = $wpdb->get_results('select option_name, option_value from '.$wpdb->prefix.'options where option_name like \''.$key.'|||%\'', ARRAY_A);
        $res = array();
        foreach($ret as $v){
            $res[$v['option_value']] = $v['option_value'];
        }
        return $res;
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

	function isInteger($input){
	    return(ctype_digit(strval($input)));
	}
}
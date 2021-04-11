<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/agusnurwanto
 * @since      1.0.0
 *
 * @package    Pbb_Desa
 * @subpackage Pbb_Desa/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pbb_Desa
 * @subpackage Pbb_Desa/includes
 * @author     Agus Nurwanto <agusnurwantomuslim@gmail.com>
 */
class Pbb_Desa_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$role = get_role('petugas_pajak');
		if ( empty($role) ) {
		    $result = add_role( 
				'petugas_pajak', 
				__('Petugas Pajak' ),
				array(
					'read' => true, // true allows this capability
				)
			);
		}
	}

}

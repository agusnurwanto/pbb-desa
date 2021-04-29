<?php
$input = shortcode_atts( array(
	'nop' => '',
	'tahun_anggaran' => '2021'
), $atts );
global $wpdb;

if(empty($input['nop'])){
	echo "<h1 style='text-align: center;'>NOP tidak boleh kosong!</h1>"; exit;
}

echo "<h1 style='text-align: center;'>NOP : ".$input['nop']."</h1>";
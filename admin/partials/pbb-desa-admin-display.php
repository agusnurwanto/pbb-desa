<?php
$input = shortcode_atts( array(
	'tahun_anggaran' => '2021'
), $atts );
global $wpdb;


echo "<h1 style='text-align: center;'>Tahun Anggaran : ".$input['tahun_anggaran']."</h1>";
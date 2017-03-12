<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
/**
 * @param $id
 *
 * @return string
 */
function reslide_show_published_sliders($id ) {
	global $wpdb;
	$query   = $wpdb->prepare( "SELECT * FROM " . RESLIDE_TABLE_SLIDERS . " WHERE id = '%d' ", $id );
	$reslider = $wpdb->get_results( $query );

	$params = json_decode($reslider[0]->params);
	$sortimagesby = $params->sortimagesby;

	switch($sortimagesby){
		case '0':
			$orderBy = 'ordering DESC';
			break;
		case '1':
			$orderBy = 'title';
			break;
		case '2':
			$orderBy = 'RAND()';
			break;
	}

	$query   = $wpdb->prepare( "SELECT * FROM " . RESLIDE_TABLE_SLIDES . " WHERE sliderid = '%d' ORDER BY " . $orderBy, $id );
	$reslides = $wpdb->get_results( $query );

	return reslider_front_end( $id, $reslider, $reslides );
}

<?php

Class CactusCounter {

	public function __construct() {
	}

	public static function check() {

		if ( osp_get('ct_video_settings', 'use_video_network_data') != 'on' && ! function_exists('tptn_get_settings') && ot_get_option('use_ct_counter', 'off') != 'off' ) { 
            return true;
        } else {
        	return false;
        }
	}

	public static function increase( $id = '', $just_update = true ) {
		if ( ! $id ) {
			$id = get_the_ID();
		}

		$total = get_post_meta( $id, 'ct_total_count', true );
		if ( ! $total ) {
			$total = CactusCounter::get_top_ten_count( $id, 'top_ten' );
		}

		$total++;
		if ( !$just_update ) {
			update_post_meta( $id, 'ct_total_count', $total );
		} else {
			return $total;
		}

		$now = new DateTime();

		$daily_suffix = $now->format('Y-m-d');
		$daily = get_post_meta( $id, 'ct_daily_' . $daily_suffix, true );
		if ( !$daily ) {
			$daily = CactusCounter::get_top_ten_daily_count( $id, $daily_suffix );
		}
		$daily++;
		if ( !$just_update ) {
			update_post_meta( $id, 'ct_daily_' . $daily_suffix, $daily );
		}
		// monthly

		$monthly_suffix = $now->format('Y-m');
		$monthly = get_post_meta( $id, 'ct_monthly_' . $monthly_suffix, true );
		$monthly = $monthly ? $monthly : 0;
		$monthly++;
		if ( !$just_update ) {
			update_post_meta( $id, 'ct_monthly_' . $monthly_suffix, $monthly );
		}

		return $total;
	}

	public static function get_top_ten_count( $id, $table = 'top_ten' ) {
		global $wpdb;

		$table_name = $wpdb->prefix . $table;

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			return 0;
		}

		$results = $wpdb->get_results( 'SELECT cntaccess FROM ' .  $wpdb->prefix . 'top_ten WHERE postnumber = ' . $id  );

		if ( count( $results ) && isset( $results[0]->cntaccess ) ) {
			return $results[0]->cntaccess;
		} else {
			return 0;
		}
	}

	public static function get_top_ten_daily_count( $id, $date ) {
		global $wpdb;
		$table = 'top_ten_daily';
		$table_name = $wpdb->prefix . $table;

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			return 0;
		}
		
		$results = $wpdb->get_results( 'SELECT cntaccess FROM ' .  $wpdb->prefix . $table . ' WHERE postnumber = ' . $id . ' AND dp_date >= "' . $date . '" ORDER BY dp_date DESC' );
		
		if ( count( $results ) && isset( $results[0]->cntaccess ) ) {
			return $results[0]->cntaccess;
		} else {
			return 0;
		}
	}

}

add_action('wp_head', 'ct_simple_counter_daily');
function ct_simple_counter_daily() {
	if ( is_singular() ) {
		CactusCounter::increase( get_the_ID(), false );
	}
}

<?php
/*
Plugin Name: WordPress Site Performance
Plugin URI: http://hccoder.info/
Description: Measure your WordPress performance, generate statistics for every page you have
Author: hccoder - Sándor Fodor
Version: 1.0
Author URI: http://hccoder.info/
*/

$plugin_title = 'Site Performance';
$plugin_url = 'site-performance';
$plugin_menu = 'AddMenu';

if ( ! class_exists( 'PluginSkeleton' ) ) {
	if ( ! file_exists( ABSPATH.'wp-content/plugins/'.$plugin_url.'/lib/plugin.skeleton.php' ) )
		die( 'Skeleton not found!' );
		
	require( ABSPATH.'wp-content/plugins/'.$plugin_url.'/lib/plugin.skeleton.php' );
}

class SitePerformance extends PluginSkeleton {
	
	public function PluginAdmin() {
		global $wpdb;
		
		$stats_num_queries = $wpdb->get_results('SELECT 
																		AVG(num_queries) AS num_queries, 
																		page 
																 FROM 
																 		site_performance_logs
																 WHERE
																 		page != "" 
																 GROUP BY page 
																 ORDER BY num_queries DESC');
		
		$stats_mem_usage = $wpdb->get_results('SELECT 
																		AVG(memory_usage) AS memory_usage, 
																		page 
																 FROM 
																 		site_performance_logs
																 WHERE
																 		page != "" 
																 GROUP BY page 
																 ORDER BY memory_usage DESC');
		
		$stats_render_time = $wpdb->get_results('SELECT 
																		AVG(render_time) AS render_time, 
																		page 
																 FROM 
																 		site_performance_logs
																 WHERE
																 		page != "" 
																 GROUP BY page 
																 ORDER BY render_time DESC');
		
		require('views/admin.php');
	}
	
}
$test = new SitePerformance( $plugin_url, $plugin_title, $plugin_menu );

$start_time = 0;
$finish_time = 0;

function SitePerformanceTimerStart() {
	if ( is_admin() )
		return FALSE;
		
	global $start_time;
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start_time = $time;
}
add_action('init', 'SitePerformanceTimerStart');

function SitePerformanceTimerEnd() {
	if ( is_admin() )
		return FALSE;
		
	global $finish_time;
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish_time = $time;
}
add_action('shutdown', 'SitePerformanceTimerEnd');

function SitePerformanceTimerStoreResults() {
	if ( is_admin() )
		return FALSE;
	
	wp_reset_query();
	
	global $start_time, $finish_time, $wpdb, $post;
	
	$total_time = round(($finish_time - $start_time), 4);
	
	if ( is_category() )
		$data['page'] = single_cat_title('', false);
	else
		$data['page'] = $post->post_title;
		
	$data['memory_usage'] = round(memory_get_peak_usage() / 1048576, 4);
	$data['num_queries'] = $wpdb->num_queries;
	$data['render_time'] = $total_time;
	
	$wpdb->insert('site_performance_logs', $data);
}
add_action('shutdown', 'SitePerformanceTimerStoreResults');

function SitePerformance_activation() {
	global $wpdb;
	$wpdb->query('CREATE TABLE IF NOT EXISTS `site_performance_logs` (
	  `id` bigint(20) unsigned NOT NULL auto_increment,
	  `page` text character set utf8 NOT NULL,
	  `memory_usage` float NOT NULL,
	  `num_queries` int(10) unsigned NOT NULL,
	  `render_time` float NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;');
}
register_activation_hook(__FILE__, 'SitePerformance_activation');

function SitePerformance_deactivation() {
	global $wpdb;
	$wpdb->query('DROP TABLE site_performance_logs');
}
register_deactivation_hook(__FILE__, 'SitePerformance_deactivation');
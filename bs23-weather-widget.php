<?php
/*
Plugin Name: BS23 Weather Widget
Description: Display Weather Info, sample shortcode [show_weather unit="F" location="Dhaka" height="400" width="400"]
Version: 1.0.0
Author: samrat
License: GPL2
*/

if( !defined('ABSPATH') ) die();

define('BS23_API_VERSION', 2.5);
define('BS23_APPID', '4c795233988bcae5bcbc128546dee61e');
define('BS23_API_END_POINT', 'http://api.openweathermap.org/data');
define('BS23_ICON', 'http://openweathermap.org/themes/openweathermap/assets/vendor/owm/img/widgets/');
define('DS', '/');

$bs23PluginsURI = plugins_url('/bs23-weather-widget');

add_action('init', 'bs23_init_script');

add_shortcode('show_weather', 'bs23_show_weather_fn');

function bs23_init_script() {
	global $bs23PluginsURI;
	wp_enqueue_script( 'jquery' );
	
	wp_register_script('bs23_js', $bs23PluginsURI . '/js/script.js', array(), '1.0' );
	wp_enqueue_script( 'bs23_js' );	

	wp_register_style('bs23_css', $bs23PluginsURI . '/css/style.css', array(), '1.0' );
	wp_enqueue_style( 'bs23_css' );	
}

function bs23_getApiUrl($method, $location, $units)
{
	return BS23_API_END_POINT.DS.BS23_API_VERSION.DS.$method.'?appid='.BS23_APPID.'&q='.$location.'&units='.$units;
}

function bs23_show_weather_fn($attr = array()) {

	global $bs23PluginsURI;
	
	$units = array(
		'F'=>'imperial',
		'C'=>'metric',
		'K'=>'',
	);

	$symbol = array(
		'F'=>'F',
		'C'=>'o',
		'K'=>'K',
	);

	$cities = array(
		'Dhaka', 'New York', 'London', 'Paris'
	);

	$width = isset($attr['width']) && is_numeric($attr['width']) ? $attr['width'] : 400;

	$height = isset($attr['height']) && is_numeric($attr['height']) ? $attr['height'] : 400;

	$location = isset($attr['location']) && is_string($attr['location']) ? $attr['location'] : 'Dhaka';

	$location = isset($_GET['location']) && is_string($attr['location']) ? $_GET['location'] : $location;

	$unit = isset($attr['unit']) && array_key_exists($attr['unit'], $units) ? $attr['unit'] : 'C';

	$data = bs23_getForecastData($location, $units[$unit]);

	if (! $data) {
		return 'Some thing wrong with API!';
	}

	ob_start(); 

	require_once 'render.php';
	
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}

function bs23_getIconUrl($code)
{
	return BS23_ICON.$code.'.png';
}

function bs23_getForecastData($location, $units)
{
	if ($location=='') {
		return null;
	}

	$apiUrl = bs23_getApiUrl('forecast', $location, $units);

	$jsonData = bs23_getData($apiUrl);

	$arrayData = bs23_converArray($jsonData);

	if (! bs23_isSuccess($arrayData)) {
		return $arrayData['cod'].':'.$arrayData['message'];
	}

	$data = bs23_getFiveDaysData($arrayData['list']);
		
	return $data;

}

function bs23_getFiveDaysData(array $data)
{
	$output = [];

	foreach ($data as $key => $item) {
		if ($key % 8 != 0) {
			continue;
		}

		$output[] = $item;
	}

	return $output;
}

function bs23_isSuccess($data)
{
	if ( ! is_array($data)) {
		return false;
	}

	return $data['cod'] == 200 && $data['cnt'] == 40;
}

function bs23_converArray($data)
{
	if ($data=='') {
		return null;
	}

	return json_decode($data, true);
}

function bs23_getData($url)
{
	$ch = curl_init();
    $timeout = 300;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0');
    
    $responseData = curl_exec($ch);
    curl_close($ch);
    return $responseData;
}
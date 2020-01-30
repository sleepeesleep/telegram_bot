<?php
/**
 * Created by PhpStorm.
 * User: zhubanov_d
 * Date: 31.01.2019
 * Time: 18:27
 */


$memcached = new Memcached();
$memcached->addServer('localhost', 11211);

function getMemcacheLogin($chat_id){
    $arr = $memcached->get($chat_id . '_login');
    return $arr;
}


// функции для работы с камерами в memcached
function getMemcachedCams($chat_id){

	$memcache_array_cams = $memcached->get($chat_id . '_cams');
	return $memcache_array_cams;
}

function setMemcachedCams($chat_id,$query,$live_time){

	$memcached->set($chat_id . '_cams', $query, $live_time);
	return ('ok');

}


// функции для работы с Cable Diag в memcached
function getMemcachedCableDiag($chat_id){

	$memcache_array_cable = $memcached->get($chat_id . '_cable');
	return $memcache_array_cable;
}

function setMemcachedCableDiag($chat_id,$query,$live_time){
	
	$memcached->set($chat_id . '_cable', $query, $live_time);
	
}



<?php namespace SaltedHerring;

class SaltedCache {
    public static function read($factory, $cache_key) {
		$cache			=	\SS_Cache::factory($factory);
		$cached			=	$cache->load($cache_key);

		if (!empty($cached)) {
			$cached		=	unserialize($cached);
			return $cached;
		}

		return false;
	}

	public static function delete($factory, $cache_key = null) {
		$cache            =	\SS_Cache::factory($factory);
        if (!empty($cache_key)) {
    		$cached       =	$cache->load($cache_key);
    		if (!empty($cached)) {
    			$cache->remove($cache_key);
    		}
        } else {
            $cache->clean(\Zend_Cache::CLEANING_MODE_ALL);
        }
	}

	public static function save($factory, $cache_key, $result) {
		$cache			=	\SS_Cache::factory($factory);
		$cache_object	=	serialize($result);
		$cache->save($cache_object, $cache_key);
	}
}

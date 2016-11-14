# API

## SaltedCache
SaltedCache provides you 3 simple functions that ease the manipulation of SilveStripe/Memcache's cache

### public static function read($factory, $cache_key)
**$factory** - String. The factory name of which you store the cache
**$cache_key** - String. The key that is used to retrieve the cache. Recommend use with ***Utilities::stringify(HTTP_REQUEST)***


### public static function delete($factory, $cache_key)
**$factory** - String. The factory name of which you store the cache
**$cache_key** - String. The key that is used to retrieve the cache. Recommend use with ***Utilities::stringify(HTTP_REQUEST)***

### public static function save($factory, $cache_key, $result)
**$factory** - String. The factory name of which you store the cache
**$cache_key** - String. The key that is used to store the cache. You will need the same key to retrieve the corresponding result. Recommend use with ***Utilities::stringify(HTTP_REQUEST)***
**$result** - Array | DataList. The result that you pull out from the DB or sewed  up multiple datalist manually.

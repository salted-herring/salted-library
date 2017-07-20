<?php
/**
 * @file Utilities
 *
 * Generic utility functions
 * */
namespace SaltedHerring;

class Utilities {

    public static function css_browser_selector($ua=null) {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return ' unknown-browser';
        }

        $ua = ($ua) ? strtolower($ua) : strtolower($_SERVER['HTTP_USER_AGENT']);

        $g = 'gecko';
        $w = 'webkit';
        $s = 'safari';
        $b = array();

        // browser
        if(!preg_match('/opera|webtv/i', $ua) && preg_match('/msie\s(\d)/', $ua, $array)) {
                $b[] = 'ie ie' . $array[1];
        }	else if(strstr($ua, 'firefox/2')) {
                $b[] = $g . ' ff2';
        }	else if(strstr($ua, 'firefox/3.5')) {
                $b[] = $g . ' ff3 ff3_5';
        }	else if(strstr($ua, 'firefox/3')) {
                $b[] = $g . ' ff3';
        } else if(strstr($ua, 'gecko/')) {
                $b[] = $g;
        } else if(preg_match('/opera(\s|\/)(\d+)/', $ua, $array)) {
                $b[] = 'opera opera' . $array[2];
        } else if(strstr($ua, 'konqueror')) {
                $b[] = 'konqueror';
        } else if(strstr($ua, 'chrome')) {
                $b[] = $w . ' ' . $s . ' chrome';
        } else if(strstr($ua, 'iron')) {
                $b[] = $w . ' ' . $s . ' iron';
        } else if(strstr($ua, 'applewebkit/')) {
                $b[] = (preg_match('/version\/(\d+)/i', $ua, $array)) ? $w . ' ' . $s . ' ' . $s . $array[1] : $w . ' ' . $s;
        } else if(strstr($ua, 'mozilla/')) {
                $b[] = $g;
        }

        // platform
        if(strstr($ua, 'j2me')) {
                $b[] = 'mobile';
        } else if(strstr($ua, 'iphone')) {
                $b[] = 'iphone phone mobile';
        } else if(strstr($ua, 'ipod')) {
                $b[] = 'ipod phone mobile';
        } else if(strstr($ua, 'android')) {
                $b[] = 'android phone mobile';
        } else if(strstr($ua, 'ipad')) {
                $b[] = 'ipad mobile';
        } else if(strstr($ua, 'mac')) {
                $b[] = 'mac';
        } else if(strstr($ua, 'darwin')) {
                $b[] = 'mac';
        } else if(strstr($ua, 'webtv')) {
                $b[] = 'webtv';
        } else if(strstr($ua, 'win')) {
                $b[] = 'win';
        } else if(strstr($ua, 'freebsd')) {
                $b[] = 'freebsd';
        } else if(strstr($ua, 'x11') || strstr($ua, 'linux')) {
                $b[] = 'linux';
        }

        return join(' ', $b);
    }

    public static function to_utf($kanji_chars)
    {
        //split word
        preg_match_all('/./u', $kanji_chars, $matches);

        $c = "";
        foreach($matches[0] as $m){
                $c .= "&#".base_convert(bin2hex(iconv('UTF-8',"UCS-4",$m)),16,10);
        }
        return $c;
    }

    public static function EmailGravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() )
    {
        if (self::valid_email($email)) {
            $url = 'https://www.gravatar.com/avatar/';
            $url .= md5( strtolower( trim( $email ) ) );
            $url .= "?s=$s&d=$d&r=$r";
            if ( $img ) {
                $url = '<img src="' . $url . '"';
                foreach ( $atts as $key => $val )
                    $url .= ' ' . $key . '="' . $val . '"';
                $url .= ' />';
            }
            return $url;
        }

        return null;
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    public static function Gravatar( $member_id, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
        if ($member = Member::get()->byID($member_id)) {
            $email = $member->Email;
            $url = 'https://www.gravatar.com/avatar/';
            $url .= md5( strtolower( trim( $email ) ) );
            $url .= "?s=$s&d=$d&r=$r";
            if ( $img ) {
                $url = '<img src="' . $url . '"';
                foreach ( $atts as $key => $val )
                    $url .= ' ' . $key . '="' . $val . '"';
                $url .= ' />';
            }
            return $url;
        }

        return null;
    }

    public static function stripTags($strip_list, $html) {
        foreach ($strip_list as $tag)
        {
            $html = preg_replace('/<\/?' . $tag . '(.|\s)*?>/', '', $html);
        }
        return $html;
    }

    public static function SlagGen($type, $slag, $ID = null) {
        $test = self::sanitise($slag);
        $existing = $type::get()->filter(array('Slag' => $test));
        if (!empty($ID)) {
            $existing = $existing->exclude(array('ID' => $ID));
        }
        $n = $existing->count();

        $slag = ($test . $n == 0 ? '' : '-' . $n);
        return $slag;
    }

    public static function endsWith($haystack, $needle) {
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    public static function startsWith($haystack, $needle) {
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    public static function sanitise($string, $space_replacement = '-', $replacement = '') {
        return self::sanitiseClassName($string, $space_replacement, $replacement);
    }

    public static function sanitiseClassName($string, $space_replacement = '-', $replacement = '') {

        $words = explode(' ', trim(strtolower($string)));
        $new_words = array();
        foreach($words as $word) {
            $word = preg_replace('/[^A-Za-z0-9]/', $replacement, trim($word));
            if (strlen($word) > 0) {
                $new_words[] = $word;
            }
        }

        return implode($space_replacement, $new_words);
    }

    public static function LinkThis($all_vars, $var_name, $var_value = null) {
        $attach = true;
        if (empty($var_value)) {
            unset($all_vars[$var_name]);
        } elseif (!empty($all_vars[$var_name]) && !empty($var_value)) {
            $all_vars[$var_name] = $var_value;
            $attach = false;
        }

        $link = $all_vars['url'] . '?';
        unset($all_vars['url']);
        foreach ($all_vars as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $value_item) {
                    $link .= $key . '[]=' . $value_item . '&';
                }
            } else {
                $link .= ($key . '=' . $value . '&');
            }
        }

        if (!empty($var_value) && $attach) {
            $link .= $var_name . '=' .$var_value;
        }

        $link = rtrim(rtrim($link, '&'), '?');
        return $link;
    }

    public static function stringify($query) {
        unset($query['url']);
        unset($query['SecurityID']);

        if (empty($query['start'])) {
            $query['start'] = '0';
        }

        ksort($query);
        $str = '';
        foreach ($query as $key => $value) {
            $key = self::sanitise($key);
            if (is_array($value)) {
                sort($value);
                foreach ($value as $value_item) {
                    $str .= $key . '_' . self::sanitise($value_item) . '__';
                }
            } else {
                $str .= ($key . '_' . self::sanitise($value)) . '__';
            }
        }
        return rtrim($str, '_');
    }

    public static function paramStringify($params, $prefix = '') {
        $str = '';
        if (count($params) > 0) {
            foreach ($params as $name => $value) {
                $value = str_replace(' ', '+', $value);
                $str .= $name . '=' . $value . '&';
            }

            $str = $prefix . rtrim($str, '&');
        }
        return $str;
    }

    public static function get_emails($groupCode) {
        $group = \DataObject::get_one('Group', "Code = '" . $groupCode . "'");

        if (!empty($group)) {
            return $group->Members()->column('Email');
        }

        return array();
    }

    public static function valid_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function member_exist($email) {
        $member = \DataObject::get_one("Member",  "Email = '".$email."'");
        return !empty($member);
    }

    public static function isBrowser() {
        // Regular expression to match common browsers
        $browserlist = '/(opera|aol|msie|firefox|chrome|konqueror|safari|netscape|navigator|mosaic|lynx|amaya|omniweb|avant|camino|flock|seamonkey|mozilla|gecko)+/i';

        $validBrowser = preg_match($browserlist, strtolower($_SERVER['HTTP_USER_AGENT'])) === 1;

        return $validBrowser;// && !empty($_SERVER['HTTP_REFERER']);
    }

    public static function match_string($pattern, $str) {
        return fnmatch($pattern, $str);
    }

    public static function truncate_html($s, $l, $e = '&hellip;', $isHTML = true) {
        $s = trim($s);
        $e = (strlen(strip_tags($s)) > $l) ? $e : '';
        $i = 0;
        $tags = array();

        if($isHTML) {
            preg_match_all('/<[^>]+>([^<]*)/', $s, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            foreach($m as $o) {
                if($o[0][1] - $i >= $l) {
                    break;
                }
                $t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
                if($t[0] != '/') {
                    $tags[] = $t;
                }
                elseif(end($tags) == substr($t, 1)) {
                    array_pop($tags);
                }
                $i += $o[1][1] - $o[0][1];
            }
        }
        $output = substr($s, 0, $l = min(strlen($s), $l + $i)) . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '') . $e;
        return $output;
    }


    /**
     * Take a string with new line feeds & create paragraphs.
     * */
    public static function nl2p($string, $viewer) {
        $items = new ArrayList();

        foreach(explode(PHP_EOL, $string) as $item) {
            if (trim($item)) {
                $items->push(new ArrayData(array(
                    'line'	=> $item
                )));
            }
        }

        return $viewer->customise(new ArrayData(array(
            'Paragraphs' => $items
        )))->renderWith('Paragraphs');
    }

    /**
     * find the key that matches a specific pattern.
     * Used primarily with dbo field tags.
     *
     * e.g. UtilityFunctions::getValidKey('/*Description/', $this->db);
     * */
    public static function getValidKey($pattern, $arr) {
        $keys = array();
        foreach($arr as $key => $value) {
            if (preg_match($pattern, $key)){
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * Get $count words from a piece of text.
     * */
    public static function getWords($sentence, $count = 10) {
        $sentence = str_replace("\r", '', str_replace("\n", '', trim(strip_tags($sentence))));
        $words = explode(' ', $sentence);

        if (count($words) <= $count) {
            return $sentence;
        }

        $trimmed = '';
        for ($i = 0; $i < $count; $i++) {
            $trimmed .= $words[$i] . ' ';
        }

        $trimmed = trim($trimmed);

        return $trimmed;
    }

    /**
     * Get max number of words within a character limit.
     * */
    public static function getWordsWithinCharLimit($sentence, $limit = 150) {
        $str = '';
        $i = 1;

        if(strlen($sentence) < $limit) {
            return $sentence;
        }

        while (strlen($current = self::getWords($sentence, $i++)) < $limit) {
            $str = $current;
        }

        return $str;
    }

    public static function parse_raw_http_request($input)
    {
        $a_data = array();
        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];

        // split content by boundary and get rid of last -- element
        $a_blocks = preg_split("/-+$boundary/", $input);
        array_pop($a_blocks);

        // loop data blocks
        foreach ($a_blocks as $id => $block)
        {
            if (empty($block))
              continue;

            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

            // parse uploaded files
            if (strpos($block, 'application/octet-stream') !== FALSE)
            {
                // match "name", then everything after "stream" (optional) except for prepending newlines
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            }
            // parse all other fields
            else
            {
                // match "name" and optional value in between newline sequences
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }

            $a_data[$matches[1]] = $matches[2];
        }

        return $a_data;
    }

    public static function shorten_number($n, $from = 9999)
    {
        if ($n > $from && $n <= 999999) {
            $n = (round($n / 100) * 0.1) . 'K';
        } elseif ($n > 999999) {
            $n = (round($n / 100000) * 0.1) . 'M';
        } else {
            $n = number_format($n);
        }

        return $n;
    }
}

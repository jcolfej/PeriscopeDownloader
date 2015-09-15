<?php

class Periscope {

    const URL_MATCHING = '#https:\/\/www\.periscope\.tv\/w\/(?P<token>[a-zA-Z0-9_-]*)#';
    const CHUNCK_MATCHING = '#chunk_(.*)\.ts#';

    const BROADCAST_INFO_URL = 'https://api.periscope.tv/api/v2/getBroadcastPublic?token=';
    const ACCESS_COOKIE_URL = 'https://api.periscope.tv/api/v2/getAccessPublic?token=';

    public static function isValidUrl($url) {

        preg_match(self::URL_MATCHING, $url, $m);

        if (!isset($m['token']) || empty($m['token'])) {
            return false;
        }

        return true;

    }

    public static function download($url) {

        preg_match(self::URL_MATCHING, $url, $m);
        $token = $m['token'];

        $output = self::getInfos($token);

        $access = self::getAccessCookie($token);
        $parts = self::getParts($access['replay_url'], $access['cookies']);

        self::downloadParts($parts, $access['base_url'], $access['cookies'], $output);

    }

    public static function getInfos($token) {

        $url = self::BROADCAST_INFO_URL.$token;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $infos = curl_exec($curl);
        curl_close($curl);

        $infos = @json_decode($infos, true);

        if (!isset($infos['broadcast']) || !isset($infos['broadcast']['available_for_replay']) || !isset($infos['broadcast']['state']) || !$infos['broadcast']['available_for_replay'] || $infos['broadcast']['state'] != 'TIMED_OUT') {
            echo 'Only replay can be download ...'.PHP_EOL;
            exit();
        }

        $title = $infos['broadcast']['status'];

        echo 'Name : '.$title.PHP_EOL;

        $special = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
        $normal = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
        $title = str_replace($special, $normal, $title);

        $title = preg_replace("([^a-zA-Z0-9])", '_', $title);

        $title = preg_replace("([\_]{2,})", '_', $title);

        echo 'Output : '.$title.'.ts'.PHP_EOL;

        return getcwd().'/'.$title.'.ts';

    }

    public static function getAccessCookie($token) {

        echo 'Get access cookie ...'.PHP_EOL;

        $url = self::ACCESS_COOKIE_URL.$token;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $infos = curl_exec($curl);
        curl_close($curl);

        $infos = @json_decode($infos, true);

        if (!isset($infos['replay_url']) || !isset($infos['cookies'])) {
            echo 'Error during access cookie request ...'.PHP_EOL;
            exit();
        }

        $replayUrl = $infos['replay_url'];
        $baseUrl = str_replace('/playlist.m3u8', '', $replayUrl);

        $cookies = array();

        foreach ($infos['cookies'] as $cookie) {
            $cookies[] = $cookie['Name'].'='.$cookie['Value'];
        }

        $cookies = implode(', ', $cookies);

        return array(
            'replay_url'    =>  $replayUrl,
            'base_url'      =>  $baseUrl,
            'cookies'       =>  $cookies
        );

    }

    public static function getParts($replayUrl, $cookies) {

        echo 'Get all parts ...'.PHP_EOL;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $replayUrl);
        curl_setopt($curl, CURLOPT_COOKIE, $cookies);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($curl);
        curl_close($curl);

        preg_match_all(self::CHUNCK_MATCHING, $content, $m);

        $parts = array();

        if (isset($m[0]) && !empty($m[0])) {
            foreach ($m[0] as $part) {
                $parts[] = $part;
            }
        }

        echo count($parts).' parts found !'.PHP_EOL;

        return $parts;

    }

    public static function downloadParts($parts, $baseUrl, $cookies, $output) {

        echo 'Download all parts ...'.PHP_EOL;

        $tmpFile = tempnam(sys_get_temp_dir(), md5($output));

        echo 'Tmp file : '.$tmpFile.PHP_EOL;

        $totalParts = count($parts);
        $current = 0;

        $lastDownload = -1;

        foreach ($parts as $part) {

            $now = ceil(($current / $totalParts) * 100);

            if ($lastDownload != $now && ($now % 10 == 0)) {
                $lastDownload = $now;
                echo 'Download ... '.$now.'%'.PHP_EOL;
            }

            if (strpos($part, '.ts') !== false) {

                $curl = curl_init($baseUrl.'/'.$part);
                curl_setopt($curl, CURLOPT_COOKIE, $cookies);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $content = curl_exec($curl);
                curl_close($curl);

                file_put_contents($tmpFile, $content, FILE_APPEND | LOCK_EX);

            }

            $current++;

        }

        echo 'All files downloaded, wait moving ...'.PHP_EOL;

        rename($tmpFile, $output);

        echo 'Finish !!!'.PHP_EOL;

    }

}
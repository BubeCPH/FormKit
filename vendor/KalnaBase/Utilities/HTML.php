<?php

namespace KalnaBase\Utilities;

class HTML {

    public static function shortenUrls($data) {
        $data = preg_replace_callback('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', array(get_class($this), '_fetchTinyUrl'), $data);
        return $data;
    }

    private function _fetchTinyUrl($url) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url[0]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return '<a href="' . $data . '" target = "_blank" >' . $data . '</a>';
    }

    public static function sanitize($data) {
        return mysql_real_escape_string($data);
    }

    public static function link($text, $path, $prompt = null, $confirmMessage = "Are you sure?") {
        $path = str_replace(' ', '-', $path);
        if ($prompt) {
            $data = '<a href="javascript:void(0);" onclick="javascript:jumpTo(\'' . BASEPATH . '/' . $path . '\',\'' . $confirmMessage . '\')">' . $text . '</a>';
        } else {
            $data = '<a href="' . PUBLICHTMLPATH . '/' . $path . '">' . $text . '</a>';
        }
        return $data;
    }

    public static function includeJs($fileName) {
        $data = '<script src="' . PUBLICHTMLPATH . 'JSLibraries/' . $fileName . '.js"></script>' . PHP_EOL;
        return $data;
    }

    public static function includeCompiledJs($fileName) {
        $data = '<script src="' . PUBLICHTMLPATH . 'JSLibraries/Compiled/' . $fileName . '.js"></script>' . PHP_EOL;
        return $data;
    }

    public static function includeCss($fileName) {
        $data = '<link rel="stylesheet" href="' . PUBLICHTMLPATH . 'Stylesheets/' . $fileName . '.css" />' . PHP_EOL;
        return $data;
    }

    public static function includeCompiledCss($fileName) {
        $data = '<link rel="stylesheet" href="' . PUBLICHTMLPATH . 'Stylesheets/Compiled/' . $fileName . '.css" />' . PHP_EOL;
        return $data;
    }

    public static function includeLess($fileName) {
        //require LIBPATH . 'lessphp/lessc.inc.php';
        $less = new lessc();
        $less->checkedCompile('public/Stylesheets/' . $fileName . '.less', 'public/Stylesheets/' . $fileName . '.css');
        $data = '<link rel="stylesheet" href="' . PUBLICHTMLPATH . 'Stylesheets/' . $fileName . '.css" type="text/css" />';
        return $data;
    }

    public static function LoadImage($fileName, $altText = NULL, $class = NULL) {
        if (!is_null($class)) {
            $class = 'class="'.$class.'"';
        }
        if (!is_null($altText)) {
            $altText = 'alt="'.$altText.'"';
        }
        $data = '<img src="' . PUBLICHTMLPATH . '/Images/' . $fileName . '" ' . $altText . ' '. $class.'/>' . PHP_EOL;
        return $data;
    }

}

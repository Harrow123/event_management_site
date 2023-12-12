<?php
namespace Utils;

class UrlHelper {
    public static function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];

        // Determine the subdirectory dynamically
        $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

        return $protocol . $domainName . $path . '/';
    }
}
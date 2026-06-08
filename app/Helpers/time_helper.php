<?php

if (!function_exists('time_ago')) {
    function time_ago($datetime) {
        if (empty($datetime)) return '';
        $now = time();
        $then = strtotime($datetime);
        $diff = $now - $then;

        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('M d', $then);
    }
}

<?php

if (!function_exists('vite_assets')) {
    /**
     * Helper to load Vite assets in CodeIgniter 4
     * 
     * @param string|array $entry
     * @return string
     */
    function vite_assets($entry): string
    {
        $devServerUrl = 'http://localhost:5173';
        $isDev = false;

        // Check if dev server is running
        $handle = @curl_init($devServerUrl);
        if ($handle) {
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_NOBODY, true);
            curl_setopt($handle, CURLOPT_TIMEOUT, 1);
            $response = curl_exec($handle);
            if ($response !== false) {
                $isDev = true;
            }
            curl_close($handle);
        }

        if ($isDev) {
            $tags = "<script type=\"module\" src=\"{$devServerUrl}/@vite/client\"></script>";
            if (is_array($entry)) {
                foreach ($entry as $e) {
                    $tags .= "<script type=\"module\" src=\"{$devServerUrl}/{$e}\"></script>";
                }
            } else {
                $tags .= "<script type=\"module\" src=\"{$devServerUrl}/{$entry}\"></script>";
            }
            return $tags;
        }

        // Production logic - read manifest.json
        $manifestPath = FCPATH . 'build/.vite/manifest.json';
        if (!file_exists($manifestPath)) {
            return '';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $tags = '';

        if (is_array($entry)) {
            foreach ($entry as $e) {
                $tags .= render_production_tags($manifest, $e);
            }
        } else {
            $tags .= render_production_tags($manifest, $entry);
        }

        return $tags;
    }

    function render_production_tags($manifest, $entry)
    {
        $tags = '';
        if (isset($manifest[$entry])) {
            $file = $manifest[$entry]['file'];
            if (str_ends_with($file, '.css')) {
                $tags .= "<link rel=\"stylesheet\" href=\"" . base_url("build/{$file}") . "\">";
            } else {
                $tags .= "<script type=\"module\" src=\"" . base_url("build/{$file}") . "\"></script>";
            }

            if (isset($manifest[$entry]['css'])) {
                foreach ($manifest[$entry]['css'] as $cssFile) {
                    $tags .= "<link rel=\"stylesheet\" href=\"" . base_url("build/{$cssFile}") . "\">";
                }
            }
        }
        return $tags;
    }
}

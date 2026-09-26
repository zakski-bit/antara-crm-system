<?php

if (!function_exists('template_menu_url')) {
    function template_menu_url(string $path, string $baseUrl = ''): string
    {
        $path = trim($path);
        if ($path === '') {
            $baseUrl = trim($baseUrl, '/');
            return $baseUrl === '' ? '/' : '/' . $baseUrl;
        }

        if (preg_match('#^(https?://|mailto:|tel:|javascript:)#i', $path)) {
            return $path;
        }

        $fragment = '';
        if (strpos($path, '#') !== false) {
            [$path, $fragment] = explode('#', $path, 2);
            $fragment = '#' . $fragment;
        }

        $query = '';
        if (strpos($path, '?') !== false) {
            [$path, $query] = explode('?', $path, 2);
            $query = '?' . $query;
        }

        $path = ltrim($path, '/');
        $path = preg_replace('/\.php$/i', '', $path);

        $baseUrl = trim($baseUrl, '/');
        $url = $baseUrl === '' ? '/' . $path : '/' . $baseUrl . '/' . $path;

        return $url . $query . $fragment;
    }
}

if (!function_exists('template_rewrite_links')) {
    function template_rewrite_links(string $html, string $baseUrl = ''): string
    {
        if ($html === '') {
            return $html;
        }

        return preg_replace_callback(
            '/\b(href|action)\s*=\s*(["\'])([^"\']+)\2/i',
            function (array $matches) use ($baseUrl): string {
                $attr = $matches[1];
                $quote = $matches[2];
                $url = $matches[3];

                if (strpos($url, '<?') !== false) {
                    return $matches[0];
                }

                if (preg_match('#^(https?://|mailto:|tel:|javascript:|data:)#i', $url)) {
                    return $matches[0];
                }

                if (!preg_match('/\.php($|[?#])/', $url) && stripos($url, '/php/') !== 0) {
                    return $matches[0];
                }

                $rewritten = template_menu_url($url, $baseUrl);

                return $attr . '=' . $quote . $rewritten . $quote;
            },
            $html
        );
    }
}

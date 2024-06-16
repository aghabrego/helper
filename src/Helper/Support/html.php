<?php

use Illuminate\Support\Str;
use Weirdo\Helper\BaseClass;
use Illuminate\Foundation\Application;

if (function_exists('route') && !function_exists('beautiful_route_with_token')) {
    /**
     * @param  mixed  $routeName
     * @param  mixed  $params
     * @param  boolean $name
     * @return string
     */
    function beautiful_route_with_token($routeName, $params = [], $default = false)
    {
        if (is_array($params) && !isset($params['token'])) {
            $params['token'] = request()->get('token');
        }

        return route($routeName, $params, $default);
    }
}

if (!function_exists('storage_link')) {
    /**
     * @param string $link
     * @param string $search
     * @param string $replace
     * @return string
     */
    function storage_link(string $link = null, string $search = 'public/', string $replace = '')
    {
        if (is_null($link)) {
            return '';
        }
        /** @var int|false */
        $match = preg_match("/\bpublic\/img\b|\bpublic\/images\b/i", $link);
        if ($match !== false && $match !== 0) {
            return asset(str_replace($search, $replace, $link));
        }

        return asset('/storage/' . str_replace($search, $replace, $link));
    }
}

if (!function_exists('title_with_parameters')) {
    /**
     * @param  mixed  $titulo
     * @param  mixed  $key
     * @return string
     */
    function title_with_parameters($titulo, $key = 'camp')
    {
        $sub = "";
        if (is_array($key)) {
            foreach ($key as $value) {
                $sub .= (!is_null(request()->get($value))) ? (' ' . request()->get($value)) : '';
            }
        } else {
            $sub .= (!is_null(request()->get($key))) ? (' ' . request()->get($key)) : '';
        }

        return Str::of($titulo)->append($sub);
    }
}

if (!function_exists('storage_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string $path
     * @param  bool $withBase64
     * @return string
     */
    function storage_asset(string $path, bool $withBase64 = false)
    {
        /** @var \Weirdo\Helper\BaseClass $helper */
        $helper = app()->make(BaseClass::class);
        /** @var string $root */
        $root = base_path();
        /** @var string|false $resultPath */
        $resultPath = $helper->getSystemRoute($root, "/{$path}");
        if (!file_exists($root . "/{$path}") && file_exists(storage_path('app/' . $path))) {
            $resultPath = storage_path('app/' . $path);
        }
        if ($withBase64 === true) {
            /** @var string|false $contenidoBinario */
            $contenidoBinario = file_get_contents($resultPath);
            if ($contenidoBinario === false) {
                throw new Exception("Error Processing Request file_get_contents: {$resultPath}");
            }

            /** @var string $imagenComoBase64 */
            $imagenComoBase64 = base64_encode($contenidoBinario);
            /** @var string $ext */
            $ext = $helper->getClientOriginalExtension($resultPath);
            /** @var array $types */
            $types = $helper->getFileFormatsGroup("/{$ext}/i");
            /** @var string|null $result */
            $result = $helper->findFirstMatch($types, "/{$ext}/i");
            if (!empty($result)) {
                return "data:{$result};base64,{$imagenComoBase64}";
            }

            if (empty($result)) {
                throw new Exception("Error Processing Request getFileFormatsGroup, findFirstMatch");
            }
        }

        return $resultPath;
    }
}

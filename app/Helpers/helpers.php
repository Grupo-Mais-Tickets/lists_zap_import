<?php

if (!function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app('path') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if(!function_exists('public_path'))
{

        /**
        * Return the path to public dir
        * @param null $path
        * @return string
        */
        function public_path($path=null)
        {
                return rtrim(app()->basePath('public/'.$path), '/');
        }
}
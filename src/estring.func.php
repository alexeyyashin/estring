<?php
if ( ! function_exists('estring'))
{
    function estring($string = '', $charset = null)
    {
        return new \AlexeyYashin\EString\EString($string, $charset);
    }
}

<?php
if ( ! function_exists('estring'))
{
    function estring($string = '')
    {
        return new \AlexeyYashin\EString\EString($string);
    }
}

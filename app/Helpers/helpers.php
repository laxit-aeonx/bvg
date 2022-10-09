<?php

/**
 * Test Function
 *
 * @return response()
 */
if (!function_exists('testFunction')) {
    function testFunction($arg)
    {
        return "on yo face " . $arg;
    }
}

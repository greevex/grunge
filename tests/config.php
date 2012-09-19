<?php
/**
 * @author greevex
 * @date: 9/19/12 5:49 PM
 */

\mpr\config::$common = [
    "first_name" => "vasya",
    "last_name" => "pupkin"
];

\mpr\config::$package['mpr_db_mongoDb'] = [
    'host' => 'mongo01.sdstream.ru'
];

\mpr\config::$package['mpr_view_smarty'] = [
    'templates_cache_dir' => '/tmp/smarty'
];
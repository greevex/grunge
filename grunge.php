<?php
/**
* Grunge init
*
* @author GreeveX <greevex@gmail.com>
*/

namespace grunge;

define("GRUNGE", "grunge");
define("GRUNGE_PATH", realpath(dirname(__FILE__)));
if (!defined("GRUNGE_DEBUG")) {
    define("GRUNGE_DEBUG", 0);
}
if (!defined("GRUNGE_CONSOLE_SHOW_DEBUG")) {
    define("GRUNGE_CONSOLE_SHOW_DEBUG", 0);
}
require __DIR__ . '/system/debug/debug.php';
system\debug\debug::init();
system\debug\debug::put("Loading system", __FILE__);
system\debug\debug::put("Loading fileLoader", __FILE__);

require realpath(GRUNGE_PATH . '/mprf-sds/load.php');
require realpath(GRUNGE_PATH . '/system/service/fileLoader.php');
\grunge\system\service\fileLoader::registerSpl();
set_error_handler(array('\\grunge\\system\\exceptions\\gException', 'errorHandler'), E_ALL);
\grunge\system\core\core::loadSystem();
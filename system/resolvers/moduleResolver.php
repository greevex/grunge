<?php
namespace grunge\system\resolvers;

/**
 * Description of moduleResolver
 *
 * @author GreeveX <greevex@gmail.com>
 */
class moduleResolver extends gResolver {

    public function resolve($file)
    {
        $file = str_replace(GRUNGE . '/', '', $file);
        $filepath = \grunge\system\systemConfig::$pathToApp . "/{$file}.php";
        \grunge\system\debug\debug::put(
            "Searching: {$filepath}", __METHOD__, 2);
        if(file_exists($filepath)) {
            return $filepath;
        }
        $filepath = GRUNGE_PATH . DIRECTORY_SEPARATOR . "{$file}.php";
        \grunge\system\debug\debug::put(
            "Searching: {$filepath}", __METHOD__, 2);
        if(file_exists($filepath)) {
            return $filepath;
        }
        return false;
    }
}
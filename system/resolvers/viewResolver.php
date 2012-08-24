<?php
namespace grunge\system\resolvers;

/**
 * Description of doctrineResolver
 *
 * @author GreeveX <greevex@gmail.com>
 */
class viewResolver extends gResolver {

    public function resolve($file)
    {
        $file_array = explode('/', $file);
        $module = array_shift($file_array);
        $tpl_path = implode('/', $file_array);

        $filepath = \grunge\system\systemConfig::$pathToApp .
                "/system/modules/$module/view/$tpl_path";
        if(file_exists($filepath)) {
            return $filepath;
        }
        $filepath = GRUNGE_PATH .
                "/system/modules/$module/view/$tpl_path";
        if(file_exists($filepath)) {
            return $filepath;
        }
        return false;
    }

}
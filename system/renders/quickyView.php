<?php
namespace grunge\system\renders;

use \grunge\system\io\response;
use \grunge\system\systemConfig;

\grunge\system\service\fileLoader::
        load(GRUNGE_PATH . '/libs/quicky/Quicky.class.php');

class quickyView
extends \Quicky
implements \grunge\system\interfaces\view
{
    private function checkFolder($folder)
    {
        if(!file_exists($folder) || !is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
    }

    public function __construct($configName)
    {
        parent::__construct($configName);
        $this->caching          = 1;
        $this->cache_lifetime   = 180;
        $this->compile_dir      = systemConfig::$view[$configName]['cache_path'] .
                '/templates_c/';
        $this->checkFolder($this->compile_dir);
        $this->config_dir       = systemConfig::$view[$configName]['cache_path'] .
                '/configs/';
        $this->checkFolder($this->config_dir);
        $this->cache_dir        = systemConfig::$view[$configName]['cache_path'] .
                '/templates_cache/';
        $this->checkFolder($this->cache_dir);
    }

    public function render($templateName = '', $asString = true, $cache_id = '')
    {
        $path = \grunge\system\service\fileLoader::getResolverByName('view')
            ->resolve($templateName);
        if(empty($path)) {
            $path = \grunge\system\service\fileLoader::resolve($templateName);
        }
        $rendered = $this->fetch($path, $cache_id);
        if ($asString) {
            return $rendered;
        }

        return response::getInstance()->write($rendered);
    }

    public function assign($var, $value = null, $scope = null)
    {
        if(is_array($var)) {
            return parent::assign($var);
        }
        return parent::assign($var, $value, $scope);
    }
}
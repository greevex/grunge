<?php
namespace grunge\system\renders;

use \grunge\system\systemConfig;
use \grunge\system\service\fileLoader;

fileLoader::load(GRUNGE_PATH . '/libs/Smarty/libs/Smarty.class.php');

class smartyView
extends \Smarty
implements \grunge\system\interfaces\view
{
    public function __construct($configName)
    {
        parent::__construct();
        define(SMARTY_MBSTRING, true);
        if(GRUNGE_DEBUG) {
            $this->smarty->force_compile = false;
            $this->smarty->debugging = true;
        } else {
            $this->smarty->force_compile = false;
            $this->smarty->debugging = false;
        }
        $this->smarty->caching          = true;
        $this->smarty->cache_lifetime   = 180;
        $this->smarty->setCompileDir(systemConfig::$view[$configName]['cache_path'] . '/templates_c/');
        $this->smarty->setConfigDir(systemConfig::$view[$configName]['cache_path'] . '/configs/');
        $this->smarty->setCacheDir(systemConfig::$view[$configName]['cache_path'] . '/templates_cache/');
    }

    public function render($templateName = '', $asString = true, $cache_id = '')
    {
        $template = fileLoader::getResolverByName('view')->resolve($templateName);
        if(!$template) {
            $template = $templateName;
        }
        if($asString) {
            return $this->smarty->fetch($template, $cache_id);
        } else {
            return $this->smarty->display($template, $cache_id);
        }
    }

    public function assign($var, $value = null, $scope = null)
    {
        return parent::assign($var, $value, false);
    }
}
<?php
namespace grunge\system\interfaces;

/**
 * Description of module
 *
 * @author GreeveX <greevex@gmail.com>
 */
interface module {

    public function checkRequirements();

    public function load();
}
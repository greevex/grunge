<?php
namespace grunge\system\modules\simple;

/**
 * Description of simpleMapper
 *
 * @author GreeveX <greevex@gmail.com>
 */
abstract class simpleMapper
implements \grunge\system\interfaces\mapper {

    private $map = array();

    public function getMap() {
        return $this->map;
    }

}
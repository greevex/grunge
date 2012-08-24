<?php
namespace grunge\system\modules\simple;

/**
 * Description of simpleModule
 *
 * @author GreeveX <greevex@gmail.com>
 */
abstract class simpleModule
extends simpleHelper
implements \grunge\system\interfaces\module {

    public function __construct()
    {
        \grunge\system\debug\debug::put(
                "Constructing...", __METHOD__, 10);
        if(!$this->checkRequirements()) {
            throw new \grunge\system\exceptions\gException(
                    "Requirements of " . __CLASS__ . " failure!",
                    null, null, __FILE__
                    );
        }
    }

}
<?php
namespace grunge\system\modules\simple;

/**
 * Description of model
 *
 * @author GreeveX <greevex@gmail.com>
 */
abstract class model {

    public function __construct()
    {
        \grunge\system\debug\debug::put(
                "Constructing...", __METHOD__, 10);
    }

}
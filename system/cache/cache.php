<?php
namespace grunge\system\cache;

class cache
    extends \mpr\cache
    implements \grunge\system\interfaces\cache
{

    public function delete($key)
    {
        return self::remove($key);
    }
}
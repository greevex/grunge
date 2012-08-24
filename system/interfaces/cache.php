<?php
namespace grunge\system\interfaces;

/**
 * Description of cache
 *
 * @author GreeveX <greevex@gmail.com>
 */
interface cache {

    public function set($key, $value, $expire = '600');

    public function get($key);

    public function delete($key);

    public function exists($key);

    public function clear();

    public function enableAutoCommit();

    public function disableAutoCommit();

    public function commit();
/*/
    public function block($key, $expire = 3);

    public function unblock($key);

    public function isBlocked($key);
//*/
}
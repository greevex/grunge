<?php
namespace grunge\system\interfaces;

/**
 * Description of ormdb
 *
 * @author GreeveX <greevex@gmail.com>
 */
interface ormdb {

    public function __construct($configName);

    public function delete($object);

    public function insert($object);

    public function select($criteria);

    public function selectDb($dbname);

    public function update($object);
}
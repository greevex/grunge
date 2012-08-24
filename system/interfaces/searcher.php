<?php
namespace grunge\system\interfaces;

/**
 * Description of searcher
 *
 * @author GreeveX <greevex@gmail.com>
 */
interface searcher
{
    public function search($query, $offset = 0, $limit = 10, $search_type = 'text');
    public function indexThis($document);
    public function getStats();
}
<?php
/**
 * Implements the terms facet.
 *
 * @category Xodoa
 * @package Elastica
 * @author Nicolas Ruflin <spam@ruflin.com>
 * @author Jasper van Wanrooy <jasper@vanwanrooy.net>
 * @link http://www.elasticsearch.org/guide/reference/api/search/facets/terms-facet.html
 */
class Elastica_Facet_Terms extends Elastica_Facet_Abstract
{
    /**
     * Holds the types of ordering which are allowed
     * by ElasticSearch.
     *
     * @var array
     */
    protected $_orderTypes = array('count', 'term', 'reverse_count', 'reverse_term');

    /**
     * Sets the field for the terms.
     *
     * @param string $field The field name for the terms.
     * @return Elastica_Facet_Terms
     */
    public function setField($field) {
        return $this->setParam('field', $field);
    }

    /**
     * Sets multiple fields for the terms.
     *
     * @param array $fields Numerical array with the fields for the terms.
     * @return Elastica_Facet_Terms
     */
    public function setFields(array $fields) {
        return $this->setParam('fields', $fields);
    }

    /**
     * Sets the flag to return all available terms. When they
     * don't have a hit, they have a count of zero.
     *
     * @param bool $allTerms Flag to fetch all terms.
     * @return Elastica_Facet_Terms
     */
    public function setAllTerms($allTerms) {
        return $this->setParam('all_terms', (bool) $allTerms);
    }

    /**
     * Sets the ordering type for this facet. ElasticSearch
     * internal default is count.
     *
     * @param string $type The order type to set use for sorting of the terms.
     * @throws Elastica_Exception_Invalid When an invalid order type was set.
     * @return Elastica_Facet_Terms
     */
    public function setOrder($type) {
        if (!in_array($type, $this->_orderTypes)) {
            throw new Elastica_Exception_Invalid('Invalid order type: ' . $type);
        }
        return $this->setParam('order', $type);
    }

    /**
     * Set an array with terms which are omitted in the search.
     *
     * @param array $exclude Numerical array which includes all terms which needs to be ignored.
     * @return Elastica_Facet_Terms
     */
    public function setExclude(array $exclude) {
        return $this->setParam('exclude', $exclude);
    }

    /**
     * Sets the amount of terms to be returned.
     *
     * @param int $size The amount of terms to be returned.
     * @return Elastica_Facet_Terms
     */
    public function setSize($size) {
        return $this->setParam('size', (int) $size);
    }

    /**
     * Creates the full facet definition, which includes the basic
     * facet definition of the parent.
     *
     * @see Elastica_Facet_Abstract::toArray()
     * @return array
     */
    public function toArray() {
        $this->_setFacetParam('terms', $this->_params);
        return parent::toArray();
    }
}

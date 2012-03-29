<?php 

namespace PHPUi\Cache\Storage;

/**
 * Base class for cache driver implementations.
 *
 * @since 2.0
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 * @author  Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author  Jonathan Wage <jonwage@gmail.com>
 * @author  Roman Borschel <roman@code-factory.org>
 */
abstract class AbstractStorage 
{
    /** @var string The cache id to store the index of cache ids under */
    private $_cacheIdsIndexId = 'phpui_cache_ids';

    /** @var string The namespace to prefix all cache ids with */
    private $_namespace = null;

    /**
     * Set the namespace to prefix all cache ids with.
     *
     * @param string $namespace
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return $this->_doFetch($this->_getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return $this->_doContains($this->_getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return $this->_doSave($this->_getNamespacedId($id), $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $id = $this->_getNamespacedId($id);

        if (strpos($id, '*') !== false) {
            return $this->deleteByRegex('/' . str_replace('*', '.*', $id) . '/');
        }

        return $this->_doDelete($id);
    }
    
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->_doClear();
    }

    /**
     * Delete all cache entries.
     *
     * @return array $deleted  Array of the deleted cache ids
     */
    public function deleteAll()
    {
        $ids = $this->getIds();

        foreach ($ids as $id) {
            $this->delete($id);
        }

        return $ids;
    }

    /**
     * Delete cache entries where the id matches a PHP regular expressions
     *
     * @param string $regex
     * @return array $deleted  Array of the deleted cache ids
     */
    public function deleteByRegex($regex)
    {
        $deleted = array();

        $ids = $this->getIds();

        foreach ($ids as $id) {
            if (preg_match($regex, $id)) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    /**
     * Delete cache entries where the id has the passed prefix
     *
     * @param string $prefix
     * @return array $deleted  Array of the deleted cache ids
     */
    public function deleteByPrefix($prefix)
    {
        $deleted = array();

        $prefix = $this->_getNamespacedId($prefix);
        $ids = $this->getIds();

        foreach ($ids as $id) {
            if (strpos($id, $prefix) === 0) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }

    /**
     * Delete cache entries where the id has the passed suffix
     *
     * @param string $suffix
     * @return array $deleted  Array of the deleted cache ids
     */
    public function deleteBySuffix($suffix)
    {
        $deleted = array();

        $ids = $this->getIds();

        foreach ($ids as $id) {
            if (substr($id, -1 * strlen($suffix)) === $suffix) {
                $this->delete($id);
                $deleted[] = $id;
            }
        }

        return $deleted;
    }
    
    /**
     * Simulate direct access for ids
     * 
     * @param string $name
     */
    public function __get($id)
    {       
        return $this->fetch($id);
    }

    /**
     * Prefix the passed id with the configured namespace value
     *
     * @param string $id  The id to namespace
     * @return string $id The namespaced id
     */
    private function _getNamespacedId($id)
    {
        if ( ! $this->_namespace || strpos($id, $this->_namespace) === 0) {
            return $id;
        } else {
            return $this->_namespace . $id;
        }
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id cache id The id of the cache entry to fetch.
     * @return string The cached data or FALSE, if no cache entry exists for the given id.
     */
    abstract protected function _doFetch($id);

    /**
     * Test if an entry exists in the cache.
     *
     * @param string $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    abstract protected function _doContains($id);

    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param string $data The cache entry/data.
     * @param int $lifeTime The lifetime. If != false, sets a specific lifetime for this cache entry (null => infinite lifeTime).
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    abstract protected function _doSave($id, $data, $lifeTime = false);

    /**
     * Deletes a cache entry.
     *
     * @param string $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    abstract protected function _doDelete($id);
    
    /**
     * Clears all cache entries.
     * 
     * @return boolean TRUE if the cache was successfully cleared, FALSE otherwise.
     */
    abstract protected function _doClear();

    /**
     * Get an array of all the cache ids stored
     *
     * @return array $ids
     */
    abstract public function getIds();
}
<?php

namespace PHPUi\Cache\Storage;

/*
 * From Doctrine 2.0
 * <http://www.doctrine-project.org>
 */

/**
 * Memcache cache driver.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   2.0
 * @version $Revision: 3938 $
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 * @author  Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author  Jonathan Wage <jonwage@gmail.com>
 * @author  Roman Borschel <roman@code-factory.org>
 * @author  David Abdemoulaie <dave@hobodave.com>
 */
class MemcacheStorage extends AbstractStorage
{
    /**
     * @var Memcache
     */
    private $_memcache;

    /**
     * Sets the memcache instance to use.
     *
     * @param Memcache $memcache
     */
    public function setMemcache(Memcache $memcache)
    {
        $this->_memcache = $memcache;
    }

    /**
     * Gets the memcache instance used by the cache.
     *
     * @return Memcache
     */
    public function getMemcache()
    {
        return $this->_memcache;
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        $keys = array();
        $allSlabs = $this->_memcache->getExtendedStats('slabs');

        foreach ($allSlabs as $server => $slabs) {
            if (is_array($slabs)) {
                foreach (array_keys($slabs) as $slabId) {
                    $dump = $this->_memcache->getExtendedStats('cachedump', (int) $slabId);

                    if ($dump) {
                        foreach ($dump as $entries) {
                            if ($entries) {
                                $keys = array_merge($keys, array_keys($entries));
                            }
                        }
                    }
                }
            }
        }
        return $keys;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        return $this->_memcache->get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        return (bool) $this->_memcache->get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        return $this->_memcache->set($id, $data, 0, (int) $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        return $this->_memcache->delete($id);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _doClear() 
    {
        return $this->_memcache->flush();
    }
    
}
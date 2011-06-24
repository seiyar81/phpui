<?php

/*
 * From Doctrine 2.0
 * <http://www.doctrine-project.org>
 */

/**
 * Xcache cache driver.
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
class PHPUi_Cache_Storage_Xcache extends PHPUi_Cache_Storage_Abstract
{
    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        $this->_checkAuth();
        $keys = array();

        for ($i = 0, $count = xcache_count(XC_TYPE_VAR); $i < $count; $i++) {
            $entries = xcache_list(XC_TYPE_VAR, $i);

            if (is_array($entries['cache_list'])) {
                foreach ($entries['cache_list'] as $entry) {
                    $keys[] = $entry['name'];
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
        return $this->_doContains($id) ? unserialize(xcache_get($id)) : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        return xcache_isset($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        return xcache_set($id, serialize($data), (int) $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        return xcache_unset($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doClear() 
    {
        foreach($this->getIds() as $id) {
              if(!xcache_unset($id))
                  return false;
        }
        return true;
    }

    /**
     * Checks that xcache.admin.enable_auth is Off
     *
     * @throws BadMethodCallException When xcache.admin.enable_auth is On
     * @return void
     */
    protected function _checkAuth()
    {
        if (ini_get('xcache.admin.enable_auth')) {
            throw new BadMethodCallException('To use all features of PHPUi_Cache_Storage_Xcache, you must set "xcache.admin.enable_auth" to "Off" in your php.ini.');
        }
    }
}
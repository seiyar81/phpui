<?php

namespace PHPUi\Cache\Storage;

/*
 * From Doctrine 2.0
 * <http://www.doctrine-project.org>
 */

/**
 * APC cache driver.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   2.0
 * @version $Revision$
 * @author  Benjamin Eberlei <kontakt@beberlei.de>
 * @author  Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author  Jonathan Wage <jonwage@gmail.com>
 * @author  Roman Borschel <roman@code-factory.org>
 * @author  David Abdemoulaie <dave@hobodave.com>
 * @todo Rename: APCCache
 */
class ApcStorage extends AbstractStorage
{
    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        $ci = apc_cache_info('user');
        $keys = array();

        foreach ($ci['cache_list'] as $entry) {
            $keys[] = $entry['info'];
        }

        return $keys;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        return apc_fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        $found = false;

        apc_fetch($id, $found);

        return $found;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        return (bool) apc_store($id, $data, (int) $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        return apc_delete($id);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _doClear() 
    {
        return apc_clear_cache();
    }
}
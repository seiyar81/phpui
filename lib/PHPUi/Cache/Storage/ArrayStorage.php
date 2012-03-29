<?php

namespace PHPUi\Cache\Storage;

/**
 * From Doctrine 2.0
 * <http://www.doctrine-project.org>
 */

/**
 * Array cache driver.
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

class ArrayStorage extends AbstractStorage
{
    /**
     * @var array $data
     */
    private $data = array();

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        return array_keys($this->data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doFetch($id)
    {
        if (isset($this->data[$id])) {
            return $this->data[$id];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doContains($id)
    {
        return isset($this->data[$id]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _doSave($id, $data, $lifeTime = 0)
    {
        $this->data[$id] = $data;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function _doDelete($id)
    {
        unset($this->data[$id]);
        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _doClear() 
    {
        $this->data = array();
        
        return true;
    }
}
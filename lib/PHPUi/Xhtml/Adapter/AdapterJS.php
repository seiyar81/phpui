<?php

/**
 * @see PHPUi_Xhtml_Adapter_Abstract
 */
namespace PHPUi\Xhtml\Adapter;

use PHPUi\Exception,
    PHPUi\Xhtml;

class AdapterJS extends AdapterAbstract
{
    
    /**
     * Adapter ID
     * @var string
     */
    protected $_id = 'js';
    
    /**
     * Element selector
     * @var string
     */
    private $_elementSelector;
    
    /**
     * Constructor.
     *
     * @param array $config 
     * @throws PHPUi_Exception_InvalidArgument
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
    }
    
    /**
     * Print the current root element
     */
    public function __toString()
    {
		return "";
    }

	public function __get($param)
	{
		return new self(array('method' => $param));
	}
    
    public function __call($method, $args)
    {
		if(is_array($args))
		{
			if(count($args) == 1)
				$args = '"'.$args[0].'"';
			else
				$args = \PHPUi\Utils::encodeJSON($args); 
		}

		if(isset($this->_config['method']))
			$ret = $this->_config['method'] . '.';
		else $ret = '';

		return $ret . $method . '(' . $args . ');';
    }
     
}

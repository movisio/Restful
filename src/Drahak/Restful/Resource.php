<?php
namespace Drahak\Restful;

use ArrayAccess;
use Nette\SmartObject;
use Serializable;
use ArrayIterator;
use IteratorAggregate;
use Nette\Utils\Json;
use Nette\MemberAccessException;

/**
 * REST resource
 * @package Drahak\Restful
 * @author Drahomír Hanák
 *
 * @property string $contentType Allowed result content type
 * @property-read array $data
 */
class Resource implements ArrayAccess, Serializable, IteratorAggregate, IResource
{

    use SmartObject {
        SmartObject::__get as SO__get;
        SmartObject::__set as SO__set;
        SmartObject::__isset as SO__isset;
        SmartObject::__unset as SO__unset;
    }


	/** @var array */
	private $data = array();

	/**
	 * @param array $data
	 */
	public function __construct(array $data = array())
	{
		$this->data = $data;
	}

	/**
	 * Get result set data
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * get info if the resource has some data set or is empty
	 * @return boolean
	 */
	public function hasData()
	{
		return !empty($this->data);
	}

	/******************** Serializable ********************/

    /**
     * Serialize result set
     * @return string
     */
    public function __serialize()
    {
        return $this->serialize();
    }

    /**
     * Unserialize Resource
     * @param string $serialized
     */
    public function __unserialize($serialized)
    {
       $this->unserialize($serialized);
    }

	/**
	 * Serialize result set
	 * @return string
	 */
	public function serialize()
	{
		return Json::encode($this->data);
	}

	/**
	 * Unserialize Resource
	 * @param string $serialized
	 */
	public function unserialize($serialized)
	{
		$this->data = Json::decode($serialized);
	}

	/******************** ArrayAccess interface ********************/

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset) : bool
	{
		return isset($this->data[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset) : mixed
	{
		return $this->data[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) : void
	{
		if ($offset === NULL) {
			$offset = count($this->data);
		}
		$this->data[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) : void
	{
		unset($this->data[$offset]);
	}

	/******************** Iterator aggregate interface ********************/

	/**
	 * Get resource data iterator
	 * @return ArrayIterator
	 */
	public function getIterator() : ArrayIterator
	{
		return new ArrayIterator($this->getData());
	}

	/******************** Magic methods ********************/

	/**
	 * Magic getter from $this->data
	 * @param string $name
	 *
	 * @throws \Exception|\Nette\MemberAccessException
	 * @return mixed
	 */
	public function &__get($name)
	{
		try {
		    return $this->SO__get($name);
		} catch (MemberAccessException $e) {
			if (isset($this->data[$name])) {
				return $this->data[$name];
			}
			throw $e;
		}

	}

	/**
	 * Magic setter to $this->data
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		try {
            $this->SO__set($name, $value);
		} catch (MemberAccessException $e) {
			$this->data[$name] = $value;
		}
	}

	/**
	 * Magic isset to $this->data
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return !$this->SO__isset($name) ? isset($this->data[$name]) : TRUE;
	}

	/**
	 * Magic unset from $this->data
	 * @param string $name
	 * @throws \Exception|\Nette\MemberAccessException
	 */
	public function __unset($name)
	{
		try {
		    $this->SO__unset($name);
		} catch (MemberAccessException $e) {
			if (isset($this->data[$name])) {
				unset($this->data[$name]);
				return;
			}
			throw $e;
		}
	}


}

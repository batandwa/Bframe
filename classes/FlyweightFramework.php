<?php
/**
 * Flyweight Framework for PHP 5.2 and higher
 *
 * Flyweight is a simple conceptual framework which aims to
 * present the application as a set of event-driven loose 
 * coupled components, which are acting in the same context.
 * The components are easy to develop, configure, test, deploy,
 * run and maintain.
 * All components are grouped into modules, all modules are
 * grouped into the application.
 * Besides this kind of division, the reusable components can be
 * joined into packages (reusable logical units of the code which
 * provides some kind of functionality like Data Access, Templating
 * or, say, Model-View-Controller).
 *
 * @author Stanislav Shramko <stanislav.shramko@gmail.com>
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt
 * @link http://www.flyweight.ru Flyweight Website
 */

define("FLYWEIGHT_FRAMEWORK", true);

/**
 * Visits the structures
 */
interface iVisitor
{

	/**
	 * Visits the structure's value
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function visit($key, $value);

}

/**
 * Useful wrapper for PHP native arrays, which prevents us from a lot of notices
 */
interface iHashtable
{

	/**
	 * Sets a value for the key
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return Hashtable
	 */
	public function set($key, $value);

	/**
	 * Sets the values
	 *
	 * @param array $array
	 * @return Hashtable
	 */
	public function setArray($array);

	/**
	 * Retrieves a value for the key
	 *
	 * @param mixed $key
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($key, $defaultValue = false);

	/**
	 * Returns all keys
	 *
	 * @return array
	 */
	public function keys();

	/**
	 * Returns keys and values as array
	 *
	 * @return array
	 */
	public function getArray();

	/**
	 * Removes the value if such key exists
	 *
	 * @param mixed $key
	 * @return Hashtable
	 */
	public function remove($key);

	/**
	 * Clears all values
	 *
	 * @return Hashtable
	 */
	public function clear();

	/**
	 * Checks if the value with this key is contained in the hashtable
	 *
	 * @param mixed $key
	 * @return bool
	 */
	public function has($key);

}

/**
 * Canonical FIFO structure
 */
interface iQueue
{

	/**
	 * Returns the value of the current element if it exists.
	 * NULL otherwise
	 *
	 * @return mixed
	 */
	public function peek();

	/**
	 * Returns the value of the current element if it exists,
	 * removes it from the queue,
	 * raises NoElementException otherwise
	 *
	 * @throws NoElementException
	 * @return mixed
	 */
	public function element();

	/**
	 * Pokes the $value into the queue
	 *
	 * @param mixed $value
	 * @return iQueue
	 */
	public function poke($value);

	/**
	 * Checks if the $value is acceptable and pokes it into queue.
	 *
	 * @param mixed $value
	 * @return boolean true if the value was accepted, false if not
	 */
	public function offer($value);

}

/**
 * Canonical LIFO structure
 */
interface iStack
{

	/**
	 * Pushes the value into stack
	 *
	 * @param mixed $value
	 * @return void
	 */
	public function push($value);

	/**
	 * Pops the value from the stack
	 *
	 * @throws NoElementException in case if the stack is empty
	 * @return mixed
	 */
	public function pop();

	/**
	 * Returns the values in the stack as array
	 *
	 * @return array
	 */
	public function getArray();

}

/**
 * Useful wrapper for PHP native arrays, which prevents us from a lot of notices
 *
 * @see Hashtable
 * @see Stack
 * @see Queue
 */
abstract class aArrayWrapper implements Countable
{

	/**
	 * Contents of the given array
	 *
	 * @var array
	 */
	protected $elements = array ();

	/**
	 * Sets the values
	 *
	 * @param array $array
	 * @return ArrayWrapper
	 */
	public function setArray($array)
	{
		if (!is_array($array))
		{
			throw new InvalidArgumentException('The given property is not an array');
		}
		$this->elements = $array;
		return $this;
	}

	/**
	 * Returns keys and values as array
	 *
	 * @return array
	 */
	public function getArray()
	{
		return $this->elements;
	}

	/**
	 * Searches for a value exactly like array_search does
	 *
	 * @param mixed $needle
	 * @param boolean $compareTypes
	 * @return mixed
	 */
	public function search($needle, $compareTypes = false)
	{
		return array_search($needle, $this->elements, $compareTypes);
	}

	/**
	 * Returns the count of the values in the hashtable
	 *
	 * @return unknown
	 */
	public function count()
	{
		return count($this->elements);
	}

	/**
	 * Clears all values
	 *
	 * @return ArrayWrapper
	 */
	public function clear()
	{
		$this->elements = array ();
		return $this;
	}

	/**
	 * Accepts the visitor
	 *
	 * @param iVisitor $visitor
	 * @return mixed
	 */
	public function accept(iVisitor $visitor)
	{
		foreach ($this as $key => $variable)
		{
			$visitor->visit($key, $variable);
		}
	}

}

/**
 * Canonical FIFO structure
 */
class Queue extends aArrayWrapper implements iQueue, Iterator
{

	/**
	 * Iterator counter
	 *
	 * @var int
	 */
	protected $counter = 0;
	
	/**
	 * Returns the data in the Queue 
	 *
	 * @return array
	 */
	public function get_elements()
	{
		return $this->elements;
	}

	/**
	 * Returns the value of the current element if it exists.
	 * NULL otherwise
	 *
	 * @return mixed
	 */
	public function peek()
	{
		if (!empty ($this->elements[0]))
		{
			return $this->elements[0];
		}
		return null;
	}

	/**
	 * Returns the value of the current element if it exists,
	 * removes it from the queue,
	 * raises NoElementException otherwise
	 *
	 * @throws NoElementException
	 * @return mixed
	 */
	public function element()
	{
		if (!empty ($this->elements[0]))
		{
			if ($this->counter)
			{
				$this->counter--;
			}
			return array_shift($this->elements);
		}
		throw new NoElementException("There's no elements in the queue");
	}

	/**
	 * Pokes the $value into the queue
	 *
	 * @param mixed $value
	 * @return iQueue
	 */
	public function poke($value)
	{
		array_push($this->elements, $value);
		return $this;
	}

	/**
	 * Checks if the $value is acceptable and pokes it into queue.
	 *
	 * @param mixed $value
	 * @return boolean true if the value was accepted, false if not
	 */
	public function offer($value)
	{
		$this->poke($value);
		return true;
	}

	/**
	 * @see SPL Iterator
	 */
	public function current($default = false)
	{
		if(isset($this->elements[$this->counter]))
		{
			return $this->elements[$this->counter];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * @see SPL Iterator
	 */
	public function key()
	{
		return $this->counter;
	}

	/**
	 * @see SPL Iterator
	 */
	public function next($default = false)
	{
		$this->counter++;
		if(isset($this->elements[$this->counter]))
		{
			return $this->elements[$this->counter];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * @see SPL Iterator
	 */
	public function rewind()
	{
		$this->counter = 0;
	}

	/**
	 * @see SPL Iterator
	 */
	public function valid()
	{
		return ($this->counter < $this->count());
	}

}

/**
 * Canonical LIFO structure
 */
class Stack extends aArrayWrapper implements iStack, Iterator
{

	/**
	 * Iterator counter
	 *
	 * @var int
	 */
	protected $counter = 0;

	/**
	 * Pushes the value into stack
	 *
	 * @param mixed $value
	 * @return void
	 */
	public function push($value)
	{
		$this->counter++;
		array_unshift($this->elements, $value);
	}

	/**
	 * Pops the value from the stack
	 *
	 * @throws NoElementException in case if the stack is empty
	 * @return mixed
	 */
	public function pop()
	{
		if ($this->count())
		{
			$this->counter--;
			return array_shift($this->elements);
		}
		throw new NoElementException("The stack is empty");
	}

	/**
	 * Returns the values in the stack as array
	 *
	 * @return array
	 */
	public function getArray()
	{
		return $this->elements;
	}

	/**
	 * @see SPL Iterator
	 */
	public function current()
	{
		return $this->elements[$this->counter];
	}

	/**
	 * @see SPL Iterator
	 */
	public function key()
	{
		return $this->counter;
	}

	/**
	 * @see SPL Iterator
	 */
	public function next()
	{
		$this->counter++;
	}

	/**
	 * @see SPL Iterator
	 */
	public function rewind()
	{
		$this->counter = 0;
	}

	/**
	 * @see SPL Iterator
	 */
	public function valid()
	{
		return ($this->counter < count($this->elements));
	}

}

/**
 * Canonical LIFO structure but it is reversed in the time of iteration
 */
class ReversedStack extends Stack
{

	/**
	 * Pushes the value into stack
	 *
	 * @param mixed $value
	 * @return void
	 */
	public function push($value)
	{
		array_push($this->elements, $value);
	}

	/**
	 * Pops the value from the stack
	 *
	 * @throws NoElementException in case if the stack is empty
	 * @return mixed
	 */
	public function pop()
	{
		if ($this->count())
		{
			return array_pop($this->elements);
		}
		throw new NoElementException("The stack is empty");
	}

}

/**
 * Useful wrapper for PHP native hashes, which prevents us from a lot of notices
 *
 * @see ArrayWrapper
 */
class Hashtable extends aArrayWrapper implements iHashtable, ArrayAccess, IteratorAggregate
{

	/**
	 * Sets a value for the key
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return Hashtable
	 */
	public function set($key, $value)
	{
		$this->elements[$key] = $value;
		return $this;
	}

	/**
	 * Retrieves a value for the key
	 *
	 * @param mixed $key
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get($key, $defaultValue = false)
	{
		if (isset ($this->elements[$key]))
		{
			return $this->elements[$key];
		} else
		{
			return $defaultValue;
		}
	}

	/**
	 * Returns all keys
	 *
	 * @return array
	 */
	public function keys()
	{
		return array_keys($this->getArray());
	}

	/**
	 * Removes the value if such key exists
	 *
	 * @param mixed $key
	 * @return Hashtable
	 */
	public function remove($key)
	{
		if (isset ($this->elements[$key]))
		{
			unset ($this->elements[$key]);
		}
		return $this;
	}

	/**
	 * Clears all values
	 *
	 * @return Hashtable
	 */
	public function clear()
	{
		foreach ($this->elements as $key => $value)
		{
			$this->remove($key);
		}
		return $this;
	}

	/**
	 * Checks if the value with this key is contained in the hashtable
	 *
	 * @param mixed $key
	 * @return bool
	 */
	public function has($key)
	{
		return isset ($this->elements[$key]);
	}

	/**
	 * Creates the instance of the Hashtable
	 *
	 * @param array $array
	 */
	public function __construct($array = array ())
	{
		if (!is_array($array))
		{
			$array = array ();
		}
		$this->setArray($array);
	}

	/**
	 * @see ArrayAccess::offsetExists()
	 *
	 * @param string $offset
	 */
	public function offsetExists($offset)
	{
		return isset ($this->elements[$offset]);
	}

	/**
	 * @see ArrayAccess::offsetGet()
	 *
	 * @param string $offset
	 */
	public function offsetGet($offset)
	{
		if (isset ($this->elements[$offset]))
		{
			return $this->get($offset);
		}
		//throw new NoElementException("The value of $offset is not defined");
	}

	/**
	 * @see ArrayAccess::offsetSet()
	 *
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
	 * @see ArrayAccess::offsetUnset()
	 *
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

	/**
	 * Returns the iterator of the current values in the hashtable
	 *
	 * @return Iterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->getArray());
	}

}

/**
 * Extension of Hashtable class which allows to keep only
 * unique objects
 *
 * @see Hashtable
 * @see SplObjectStorage
 */
class ObjectHashtable extends Hashtable
{

	/**
	 * Sets a value for the key
	 *
	 * @param mixed $key
	 * @param object $value
	 * @return UniqueHashtable
	 */
	public function set($key, $value)
	{
		if (!is_object($value))
		{
			throw new InvalidArgumentException("The passed value is not an object");
		}
		if (array_search($value, $this->elements, true) === FALSE)
		{
			parent :: set($key, $value);
		}
		return $this;
	}

}

/**
 * Extension of Queue which allows to keep only unique objects
 *
 * @see Queue
 * @see SplObjectStorage
 */
class ObjectQueue extends Queue
{

	/**
	 * Pokes the $value into the queue
	 *
	 * @param mixed $value
	 * @return iQueue
	 */
	public function poke($value)
	{
		if (!is_object($value))
		{
			throw new InvalidArgumentException("The passed value is not an object");
		}
		if (array_search($value, $this->elements, true) === FALSE)
		{
			array_push($this->elements, $value);
		}
		return $this;
	}

	/**
	 * Checks if the $value is acceptable and pokes it into queue.
	 *
	 * @param mixed $value
	 * @return boolean true if the value was accepted, false if not
	 */
	public function offer($value)
	{
		if (!is_object($value))
		{
			throw new InvalidArgumentException("The passed value is not an object");
		}
		if (array_search($value, $this->elements, true) === FALSE)
		{
			parent :: poke($value);
			return true;
		}
		return false;
	}

}

/**
 * Exception of this class should be thrown when the data structure is empty
 */
class NoElementException extends LogicException
{
}
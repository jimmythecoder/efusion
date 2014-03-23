<?php

/**
 * Singleton. Every instatiation returns the "same" object.
 *
 * for this to work, ALWAYS create objects with $object = new class().
 * The reference operator doesn't do any harm in general and is
 * necessary for singletons.
 *
 * You also need to declare all the class' variables with "var" for
 * a singleton subclass. But that should always be a rule of good
 * design :)
 *
 * call parent::singleton() first in your subclass constructor.
 *
 * Of course, the subclass constructor should check if the object is already
 * initialized before doing any initialization.
 * 
 * @package efusion
 * @subpackage core
 */

class singleton
{
	function singleton()
  	{
   		/**
   		 * static associative array containing the real objects, key is classname
   		 * @staticvar array
   		 */
   		static $instances = array();

   		$class = get_class($this);

		//Check if this object already exists, if not create it
   		if (!array_key_exists($class, $instances))
     		$instances[$class] = $this;

   		// PHP doesn't allow us to assign a reference to $this, so we do this
   		// little trick and fill our new object with references to the original
   		// class' variables:
   		foreach (get_class_vars($class) as $var => $value)
     		$this->$var = $instances[$class]->$var;
  	}
}

?>
<?php

namespace Zefire\Core;

trait Serializable
{
    public function __sleep()
    {
    	$properties = get_object_vars($this);
    	$resources = \App::config('services.resources');
    	$saved = [];
    	foreach ($properties as $key => $value) {
    		if ($value != null) {
    			if (is_scalar($value)) {
	    			$saved[] = $key;
	    		} else if (is_array($value)) {
	    			$saved[] = $key;
	    		} else {
	    			if (!in_array(get_class($value), $resources)) {
						$saved[] = $key;
	    			}
	    		}	
    		}    		
    	}
        return $saved;
    }

    public function __wakeup()
    {
        if (method_exists($this, 'connect')) {
        	if (property_exists($this, 'connection')) {
        		$this->connect($this->connection);
        	} else {
        		$this->connect();
        	}        	
        }        
    }
}

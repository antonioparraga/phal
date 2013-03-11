<?php

/**
 * A resource is basically a I18n string. This component allow us to print whatever resource within a page content.
 * <br>
 * Resources associated to a given controller are automatically set to the template used by the controller's view. <br>
 * <br>
 * i.e., a resource with a key "my_resource" associated to a controller will be shown in a placeholder with the same name within the correspondent view template:<br>
 * <code>
 * 
 *   Here is my resource content: {$my_resource}
 * 
 * </code>
 * <br>  
 * Other resources, even if they are persisted during the session but not directly associated to the executed controller, are not set to the template automatically.<br>
 * To render them, we have the <b>resource</b> component.<br>
 * <br>
 * Resource tag is <b>resource</b><br>
 * <br>
 * i.e.  
 * <code>
 * 
 *   Here is our session content:  <comp:resource key="my_session_resource"/>
 * 
 * </code>
 * <br>
 * We just specify the <b>key</b> the resource has in order to render it within the template content<br>
 * <br>
 * Another interesting usage of this component is to render parameterized resources.<br>
 * i.e.
 * <code>
 * 
 * my_parameterized_resource = "Dear {0}, this is a parameterized I18n resource"
 * 
 * </code>
 * 
 * As we can see, the resource above has a parameter just after the "Dear" word. So we need to specify the value that will be replaced instead of the placeholder.<br>
 * To do that, the resource component has the attribute <b>parameters</b>, which allow us to specify a comma-separate list of values to be replaced by.<br>
 * i.e.
 * <code>
 * 
 *   <comp:resource key="my_parameterized_resource" parameters="0=Antuan"/>
 * 
 * </code>
 * Which will produce the following result:
 * <code>
 * 
 *   Dear Antuan, this is a parameterized I18n resource
 * 
 * </code>
 * 
 * Take into account that you must apply a comma-separated list of pair key,value as parameters.<br>
 * <br>
 * With template tags we are not able to specify parameters to resources, so this is another usage of this component even if resources belong to the current in execution controller.
 * 
 */
class __ResourceComponent extends __UIComponent {
	
    private $_key = null;
    private $_parameters = array();
    
    /**
     * Set the key of the resource to render to
     *
     * @param string $key
     */
    public function setKey($key) {
        $this->_key = $key;
    }
    
    /**
     * Get the key of the resource to render to
     *
     * @return string
     */
    public function getKey() {
        return $this->_key;
    }
    
    /**
     * Set either a comma-separated list of parameters or an array of them
     *
     * @param mixed $parameters
     */
    public function setParameters($parameters) {
        if(is_string($parameters)) {
            parse_str($parameters, $this->_parameters);
        }
        if(is_array($parameters)) {
            $this->_parameters = $parameters;
        }
    }
    
    /**
     * Get the array of parameters to use with the current resource
     *
     * @return array
     */
    public function getParameters() {
        return $this->_parameters;
    }
    
    /**
     * Magic method representing the result of rendering the current resource
     *
     * @return string
     */
    public function __toString() {
        $return_value = __ResourceManager::getInstance()->getResource($this->getKey())->setParameters($this->_parameters)->getValue();
        return $return_value;
    }    
    
}

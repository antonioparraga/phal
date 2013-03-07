<?php

/**
 * Optionbox represents the typical radiobutton, a component that can be checked or unchecked.
 * 
 * Optionboxes are contained in groups. Only one optionbox can be checked at the same time within the same group.<br>
 * <br>
 * The OptionBox tag is "optionbox"
 * <br>
 * i.e.
 * <code>
 * 
 *   Sex:
 *   <comp:optionbox group="sex" name="male" caption="Male"/>
 *   <comp:optionbox group="sex" name="female" caption="Female"/>
 * 
 * </code>
 *
 * The optionbox is a valueholder containing a boolean value. When the optionbox is checked, value is true, otherwise value is false.
 * <br>
 * <br>
 * See the optionbox component in action here: {@link http://www.phalframework.org/components/optionbox.html}
 * 
 */
class __OptionBoxComponent extends __InputBoxComponent {

	protected $_value   = false;	
	protected $_group   = null;
	protected $_caption = null;
	
	/**
	 * Set the caption to be shown within the optionbox
	 *
	 * @param string $caption
	 */
	public function setCaption($caption) {
	    $this->_caption = $caption;
	}
	
	/**
	 * Get the caption to be shown within the optionbox
	 *
	 * @return unknown
	 */
	public function getCaption() {
	    return $this->_caption;
	}
	
    /**
     * Set an identifier for the group in which the current optionbox belong to
     *
     * @param string $group
     */
	public function setGroup($group) {
	    $this->_group = $group;
	}
	
	/**
	 * Get the group identifier in which the current optionbox belong to
	 *
	 * @return string
	 */
	public function getGroup() {
	    if($this->_group != null) {
	        $return_value = $this->_group;
	    }
	    else {
    	    $return_value = $this->getName();
	    }
	    return $return_value;
	}
	
	/**
	 * Set a value for current optionbox. Value is boolean, representing checked optionbox as true.
	 *
	 * @param bool $value
	 */
    public function setValue($value) {
        $value = $this->_toBool($value);
	    if($this->_value !== $value) {
    	    $this->_value = $value;
    	    if($value == true && $this->_view_code != null) {
                //will set as false all the option boxes within the same group:
                $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
                $view_components = $component_handler->getComponents();
                foreach($view_components as &$view_component) {
                    if( $view_component instanceof __OptionBoxComponent && 
                        $view_component->getId() != $this->getId() &&
                        $view_component->getGroup() == $this->getGroup() ) {
                        $view_component->setValue(false);
                    }
                }
    	    }
        }
    }
    
    /**
     * Get the value for current optionbox. True if checked, otherwise false.
     *
     * @return bool
     */
    public function getValue() {
        return $this->_value;
    }
    
}
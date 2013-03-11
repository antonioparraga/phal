<?php

/**
 * Represents the typical text box as a one single line rectangle to write text inside it.
 * 
 * An inputbox is one of the most basic valueholders, where the value is the text written by the user<br>
 * 
 * Inputbox tag is <b>inputbox</b><br>
 * 
 * i.e.
 * <code>
 * 
 *   First name: <comp:inputbox name="first_name"/>
 * 
 * </code>
 * 
 * To retrieve the input value, use the {@link __InputComponent::getValue()} method, while to set the text use the {@link __InputComponent::setValue()}.<br>
 * The event that is raised when the user change the focus (the most typical use case for inputboxes), is the <b>blur</b> event.<br>
 * <br>
 * <br>
 *  
 */
class __InputBoxComponent extends __InputComponent {                                        
    
    protected $_value   = null;
    protected $_example_value = null;
    
    public function setExampleValue($example_value) {
        $this->_example_value = $example_value;
    }
    
    public function getExampleValue() {
        return $this->_example_value;
    }  
        
}

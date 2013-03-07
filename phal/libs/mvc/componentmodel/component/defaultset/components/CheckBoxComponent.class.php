<?php

/**
 * Checkbox represents the typical checkbox.
 * <br>
 * The CheckBox tag is "checkbox"
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:checkbox name="accept_terms" caption="I have read and accept terms and conditions"/>
 * 
 * </code>
 *
 * The checkbox is a valueholder containing a boolean value. When the checkbox is checked, value is true, otherwise value is false.<br>
 * <br>
 * A checkbox component raises the <b>click</b> event when the user check or uncheck the component.<br>
 * See the {@tutorial View/Components/View.Events.pkg} section for more information about events handled by phal components.<br>  
 * 
 * <br>
 * <br>
 * See the checkbox component in action here: {@link http://www.phalframework.org/components/checkbox.html}
 * 
 */
class __CheckBoxComponent extends __InputBoxComponent {

	protected $_value   = false;
	protected $_caption = null;
	
	/**
	 * Set a caption to be show within the checkbox
	 *
	 * @param string $caption
	 */
	public function setCaption($caption) {
	    $this->_caption = $caption;
	}
	
	/**
	 * Get the caption associated to the current checkbox
	 *
	 * @return unknown
	 */
	public function getCaption() {
	    return $this->_caption;
	}
    
	/**
	 * Set the value associated to the current checkbox (true as checked, false as unchecked)
	 *
	 * @param bool $value
	 */
    public function setValue($value) {
        $this->_value = $this->_toBool($value);
    }
    
    public function getValue() {
        return $this->_value;
    }
    
}
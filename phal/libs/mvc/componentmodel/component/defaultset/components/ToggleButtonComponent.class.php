<?php

/**
 * A toggle button represents a button like a checkbox, with 2 possible status: pressed and not pressed.
 * <br>
 * <br>
 * A toggle button tag is "togglebutton"
 * 
 * i.e.
 * <code>
 *   
 *   <comp:togglebutton name="play_button" caption="Play" pressedCaption="Pause"/>
 * 
 * </code>
 * 
 * As a checkbox, the toggle button is a valueholder containing a boolean value. When the toggle button is pressed, value is true, otherwise value is false.<br>
 * 
 * A toggle button has a <b>caption</b>, which is the text shown within the button.<br>
 * A toggle button has a <b>pressedCaption</b>, which is the text shown within the button when it is pressed.<br>
 * <br>
 * <br>
 * See the togglebutton component in action here: {@link http://www.phalframework.org/components/togglebutton.html}
 * 
 * @see __FormComponent, __CommandButtonComponent
 *
 */
class __ToggleButtonComponent extends __InputBoxComponent {

    protected $_value   = false;
    protected $_caption = null;
    protected $_pressed_caption = null;
    protected $_width = null;
    protected $_height = '20px';
    
    /**
     * Set a caption to be show within the toggle button
     *
     * @param string $caption
     */
    public function setCaption($caption) {
        $this->_caption = $caption;
    }
    
    /**
     * Get the caption associated to the current toggle button
     *
     * @return string
     */
    public function getCaption() {
        return $this->_caption;
    }
    
    /**
     * Set the caption associated to the current toggle button when it's pressed
     * 
     * @param $pressed_caption
     * 
     */    
    public function setPressedCaption($pressed_caption) {
        $this->_pressed_caption = $pressed_caption;
    }
    
    /**
     * Get the caption associated to the current toggle button when it's pressed
     * 
     * @return string
     */
    public function getPressedCaption() {
        $return_value = $this->_pressed_caption;
        if($return_value === null) {
            $return_value = $this->_caption;
        }
        return $return_value;
    }
    
    /**
     * Set the value associated to the current toggle button (true as pressed, false as unpressed)
     *
     * @param bool $value
     */
    public function setValue($value) {
        $this->_value = $this->_toBool($value);
    }
    
    public function getValue() {
        return $this->_value;
    }
    
    public function setWidth($width) {
        $this->_width = $width;
    }
    
    public function setHeight($height) {
        $this->_height = $height;
    }
    
    public function getWidth() {
        return $this->_width;
    }
    
    public function getHeight() {
        return $this->_height;
    }
    
}
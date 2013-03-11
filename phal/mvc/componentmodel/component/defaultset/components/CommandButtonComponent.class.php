<?php

/**
 * A command button represents a button.
 * <br>
 * The main purpose of a command button is:<br>
 *  - To submit a form (in case type="submit" and the command button is contained within a form component) or<br>
 *  - To redirect the navigation to another page or<br>
 *  - To execute a portion of code when the user click on it<br>
 * <br>
 * A command button tag is "commandbutton"
 * 
 * i.e.
 * <code>
 *   
 *   <comp:commandbutton name="submit_info" type="submit" caption="Submit your info"/>
 * 
 * </code>
 * 
 * A command button has a <b>caption</b>, which is the text shown within the button.<br>
 * A command button also has a <b>type</b> (which is the same type as set in a html button)<br>
 * A command button has a <b>src</b> property to set the location of the image to show instead of the button itself.<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:commandbutton name="submit_info" src="url_to_image.jpg" type="submit"/>
 * 
 * </code> 
 * 
 * It's important to note that a command button of type="submit" and embedded within a form component will raise the <b>submit</b> event associated to that form when clicked.<br>
 * Of course, it will also raise the <b>click</b> event, but the better way to associate code to a form submit is within the submit event.<br>
 * <br>
 * <br>
 * 
 * @see __FormComponent, __CommandLinkComponent
 *
 */
class __CommandButtonComponent extends __UIComponent implements __IPoolable {
    
    protected $_caption = null;
    protected $_on_click_submit = false;
    protected $_type = null;
    protected $_src = null;
    
    /**
     * Set the caption shown within the button
     *
     * @param string $caption
     */
    public function setCaption($caption) {
        $this->_caption = $caption;
    }
    
    /**
     * Get the caption assocaited to the current button
     *
     * @return string
     */
    public function getCaption() {
        return $this->_caption;
    }
    
    /**
     * Set the image location associated to the current button (in case the button is type="image"
     *
     * @param string $src
     */
    public function setSrc($src) {
        $this->_src = $src;
    }
    
    /**
     * Get the image location associated to the current button
     *
     * @return string
     */
    public function getSrc() {
        return $this->_src;
    }
    
    /**
     * Set the type attribute associated to the button (same as the HTML button type attribute)
     *
     * @param string $type
     */
    public function setType($type) {
        if(strtoupper($type) == 'SUBMIT') {
            $this->_on_click_submit = true;
        }
        else {
            $this->_on_click_submit = false;
        }
        $this->_type = $type;
    }
    
    /**
     * Get the type attribute associated to the current button
     *
     * @return string
     */
    public function getType() {
        return $this->_type;
    }
    
    /**
     * Check if the submit event must be raised or not when the button is clicked
     *
     * @return bool
     */
    public function getOnClickSubmit() {
        return $this->_on_click_submit;
    }
    
}

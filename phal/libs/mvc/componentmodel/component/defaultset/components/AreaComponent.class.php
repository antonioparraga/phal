<?php

/**
 * Area can be used to enclose one of more components.
 * <br>
 * Area components are usefull to show or hidden all the enclosed components
 * at the same time by just making the area visible or not.<br>
 * <br>
 * Area tag is "area"<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:area name="my_area">
 *    
 *     <comp:label name="your_name_label" text="Your name:"/>
 *     <comp:inputbox name="your_name"/>
 *     <br>
 *     <comp:label name="your_email" text="Your email:"/>
 *     
 *   </comp:area>
 * 
 * </code>
 * 
 * As in this example, we can show or hidden all the enclosed components by just making visible the "my_area" component:
 * 
 * <code>
 * 
 *   //hidden all the components enclosed in my_area:
 *   $my_area = $this->getComponent('my_area');
 *   $my_area->setVisible(false);
 * 
 * </code>
 * <br>
 * <br>
 *
 */
class __AreaComponent extends __UIContainer implements __IPoolable {

    protected $_text = null;
    
    /**
     * Set the text to be shown by the label component
     *
     * @param string $text
     */
    public function setText($text) {
        $this->_text = $text;
    }
    
    /**
     * Get the text to be shown by the label component
     *
     * @return string
     */
    public function getText() {
        return $this->_text;
    }
        
    /**
     * Magic string method, which returns the text property value
     *
     * @return string
     */
    public function __toString() {
        return $this->getText();
    }    
    
}


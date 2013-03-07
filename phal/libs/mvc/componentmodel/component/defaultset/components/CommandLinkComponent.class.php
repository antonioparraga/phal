<?php

/**
 * A command link represents a link.
 * <br>
 * The purpose of a command link is:<br>
 *  - To redirect the navigation to another page or<br>
 *  - To execute a portion of code when the user click on it<br>
 * <br>
 * A command link tag is "commandlink"
 * 
 * i.e.
 * <code>
 *   
 *   <comp:commandlink name="see_invoice_detail" caption="See invoice detail"/>
 * 
 * </code>
 * 
 * A command link has a <b>caption</b>, which is the text shown within the link.<br>
 * <br>
 * When a command link is clicked, the <b>click</b> event is raised, allowing to execute a portion of code associated to that event<br>
 * i.e.
 * <code>
 * 
 *   public function see_invoice_detail_click(__UIEvent &$event) {
 *       //your code here to be executed when the user click the link
 *   }
 * 
 * </code>
 * <br>
 * <br>
 * See the commandlink component in action here: {@link http://www.phalframework.org/components/combobox.html}<br>
 * <br>
 * @see __FormComponent, __CommandLinkComponent
 *
 */
class __CommandLinkComponent extends __UIComponent implements __IPoolable {

    protected $_caption = null;
    
    /**
     * Set the caption to be shown within the link
     *
     * @param string $caption
     */
    public function setCaption($caption) {
        $this->_caption = $caption;
    }
    
    /**
     * Get the caption associated to the current link
     *
     * @return string
     */
    public function getCaption() {
        return $this->_caption;
    }
    
}

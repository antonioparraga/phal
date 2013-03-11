<?php

/**
 * The label component is used to show a text.
 * 
 * Labels are usefull to control text shown as the result of event executions (i.e. to change a text as the result of an ajax call)<br>
 * <br>
 * Labels tag is <b>label</b>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:label name="client_name" text="Antonio Parraga"/>
 * 
 * </code>
 * <br>
 * We can alter the label's text by setting the text property, i.e.
 * <code>
 * 
 *   $label = $this->getComponent('client_name');
 *   $label->setText('Carolina Kop');
 * 
 * </code>
 *
 */
class __LabelComponent extends __UIComponent implements __IPoolable {

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

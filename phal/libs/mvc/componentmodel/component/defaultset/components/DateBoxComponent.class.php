<?php

/**
 * Datebox is an inputbox with a calendar image at right, showing a calendar to pick a date when click on it.
 * 
 * Datebox tag is <b>datebox</b>
 * 
 * i.e.
 * <code>
 * 
 *   Please select a date: <comp:datebox name="invoice_date"/>
 * 
 * </code>
 *
 * Datebox component is very similar to an inputbox component. Date picked by the user can be retrieved by asking the {@link __InputComponent::getValue()} method.<br>
 * <br>
 * <br>
 * 
 * @see __InputBox, __InputComponent 
 * 
 */
class __DateBoxComponent extends __InputComponent {
        
    protected $_date_format = '%Y-%m-%d';
    
    public function setDateFormat($date_format) {
        $this->_date_format = $date_format;
    }
    
    public function getDateFormat() {
        return $this->_date_format;
    }
    
}
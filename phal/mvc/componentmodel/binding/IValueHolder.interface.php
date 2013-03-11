<?php

/**
 * This interfaz is implemented by form components containing user input values (i.e. as a textbox)
 *
 */
interface __IValueHolder {

    /**
     * Sets a value
     *
     * @param mixed $value
     */
    public function setValue($value);
    
    /**
     * Gets the value
     *
     * @return mixed
     */
    public function getValue();
    
    /**
     * Reset the value to the default one (usually null)
     *
     */
    public function reset();
    
}
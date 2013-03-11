<?php

/**
 * Combobox is a combination of a drop-down list or list box and a single-line textbox, allowing the user either to choose from the list of existing options.
 * <br>
 * Combobox tag is "combobox"<br>
 * <br>
 * i.e.
 * <code>
 *   
 *   <comp:combobox name="number_of_employees"/>
 * 
 * </code>
 * 
 * A combobox is a subclass of {@link __ItemListComponent}, which is compound by a list of {@link __ItemComponent} instances.<br>
 * A __ItemComponent is a component containing a pair text, value.<br>
 * <br>
 * We can setup the combobox items by ussing the "item" tag:<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:combobox name="number_of_employees">
 *     <comp:item value="1" text="1 to 10"/>
 *     <comp:item value="2" text="11 to 20"/>
 *     <comp:item value="3" text="21 to 50"/>
 *     <comp:item value="4" text="more than 50"/>
 *   </comp:combobox>
 * </code>
 * 
 * We can also setup a combobox within the eventhandler code (typically within the beforeRender (each time the view is rendered) or within the component create event (once the component is created).<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   public function beforeRender() {
 *       $combobox = $this->getComponent('number_of_employees');
 *       $combobox->addItem(new __Item(1, '1 to 10'));
 *       $combobox->addItem(new __Item(2, '11 to 20'));
 *       $combobox->addItem(new __Item(3, '21 to 50'));
 *       $combobox->addItem(new __Item(4, 'more than 50'));
 *   }
 * 
 * </code>
 * 
 * We can select only one item within a combobox, which represents the value for that component. For that purpose, we can:<br>
 *  - Use the {@link __ItemListComponent::setSelectedIndex()} method or<br>
 *  - Set as selected the item previous to add to the combobox (by ussing the {@link __ItemComponent::setSelected()} method) or<br>
 *  - Use the selected property within the item to be selected to
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:combobox name="number_of_employees">
 *     <comp:item value="" text="Please select" selected="true"/>
 *     <comp:item value="1" text="1 to 10"/>
 *     <comp:item value="2" text="11 to 20"/>
 *     <comp:item value="3" text="21 to 50"/>
 *     <comp:item value="4" text="more than 50"/>
 *   </comp:combobox>
 * 
 * </code>
 * 
 * A combobox, as a value holder, returns the value of the selected item as a result of call to the getValue.
 * 
 * i.e.
 * <code>
 * 
 *   public function number_of_employees_change(__UIEvent &$event) {
 *      //get the value of the selected item within the combo:
 *      $combo_value = $event->getComponent()->getValue();
 *  
 *      //...
 *   }
 *      
 * </code>
 *   }
 * 
 * Default events associated to a combobox are the same as associated to a html select element. Most usefull is the <b>change</b> event, raised when the user change the selection.<br> 
 * <br>
 * <br>
 *  
 * @see __ItemListComponent, __ListBoxComponent
 * 
 */
class __ComboBoxComponent extends __ItemListComponent {

}

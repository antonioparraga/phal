<?php

/**
 * Data collector is a component designed to collect data while rendering the UI.
 * It's usefull to set information rendered to the view in order to be retrieved from the event handler.<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   <comp:datacollector name="invoice_info">
 *     <comp-property name="invoice_id">{$invoice_id}</comp-property>
 *     <comp-property name="client_id">{$client_id}</comp-property>
 *   </comp:datacollector>
 * 
 * </code>
 * 
 * Once we have stored information from the view to the data collector, we can retrieve it from the event handler.<br>
 * <br>
 * i.e.
 * <code>
 * 
 *   public function beforeRender() {
 *       $invoice_data_collector = $this->getComponent('invoice_info');
 *       //get the invoice id as well as the client id:
 *       $invoice_id = $invoice_data_collector->invoice_id;
 *       $client_id  = $invoice_data_collector->client_id;
 *   }
 * 
 * </code>
 * 
 * The data collector is not poolable, which means that can be retrieved only in create and beforeRender events.<br>
 * <br>
 *
 */
class __DataCollectorComponent extends __UIComponent {

    
}


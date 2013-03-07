<?php


/**
 * Represents the typical text area (more like an inputbox allowing to edit in multiple lines) as a rectangle to write text inside it.
 * 
 * An text area is one of the most basic valueholders, where the value is the text written by the user<br>
 * 
 * Text area tag is <b>textarea</b><br>
 * 
 * i.e.
 * <code>
 * 
 *   Description: <comp:textarea name="description"/>
 * 
 * </code>
 * 
 * To retrieve the input value, use the {@link __InputComponent::getValue()} method, while to set the text use the {@link __InputComponent::setValue()}.<br>
 * The event that is raised when the user change the focus (the most typical use case for text areas), is the <b>blur</b> event.<br>
 * <br>
 * <br>
 * See the text area component in action here: {@link http://www.phalframework.org/components/textarea.html}
 *  
 */
class __TextAreaComponent extends __InputComponent {

    
}

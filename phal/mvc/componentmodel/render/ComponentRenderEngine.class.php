<?php

/**
 * Class in charge of orchest the components rendering process.
 * 
 */
abstract class __ComponentRenderEngine implements __IComponentRenderEngine {

    protected $_view_code         = null;
    protected $_child_renderers   = array();
    protected $_renderers_stack   = null;
    protected $_properties_stack  = null;
    protected $_created_components = array();
    
    /**
     * true if this is the first time rendering the components, which means that the component handler has been created within this render process
     *
     * @var bool
     */
    private $_first_time_execution = true;
    
    final public function __construct($view_code) {
        $this->_view_code = $view_code;
        $this->_renderers_stack  = new __Stack();
        $this->_properties_stack = new __Stack();
        $this->_event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
    }
    
    public function startRender() {
        //the first element on the renderers stack will be the render engine itself
        //Note: take into account that the stack cound not be empty, otherwise there is an inconsistence problem 
        //like an unexpected closed tag or similar
        $this->_renderers_stack->push($this); 
        ob_start(array($this, 'addOutputContent'));
    }

    public function endRender() {
        ob_end_flush();
        if($this->_renderers_stack->count() == 1) {
            $this->render();
        }
        else {
        	
            throw __ExceptionFactory::getInstance()->createException('ERR_INCONSISTENCE_RENDERING_ERROR');
        }
    }

    public function markComponentSingleTag(__ComponentSpec $component_spec) {
        //flush output content to the current ob callback
        ob_end_flush();
        $component = $this->_createComponent($component_spec);
        if($component != null) {
            $component_writer = $component_spec->getWriter();
            $renderer  = new __Renderer($component, $this->_event_handler, $component_writer);
            $current_renderer = $this->_renderers_stack->peek();
            $current_renderer->addRenderer($renderer);
        }
        ob_start(array($current_renderer, 'addOutputContent'));
    }
    
    public function markRunAtServerHtmlElement(__ComponentSpec $component_spec) {
        //runAtServer html elements just create and bind components with the html elements associated to
        $component = $this->_createComponent($component_spec);
        if($component != null) {
        }
    }
    
    public function markComponentBeginTag(__ComponentSpec $component_spec) {
        //flush output content to the current ob callback
        ob_end_flush();
        //get the component and setup his renderer
        $component = $this->_createComponent($component_spec);
        if($component != null) {
            $component_writer = $component_spec->getWriter();
            $renderer  = new __Renderer($component, $this->_event_handler, $component_writer);
            //add a reference to the new renderer in the current one:
            $this->_renderers_stack->peek()->addRenderer($renderer);
            //push the new renderer on the stack to act as the current one:
            $this->_renderers_stack->push($renderer);
            //set the ob callback to the new renderer
        }
        ob_start(array($renderer, 'addOutputContent'));
    }
    
    public function markComponentEndTag(__ComponentSpec $component_spec) {
        //send output buffer to the current ob callback
        ob_end_flush();
        //pop the current renderer from the renderers stack
        $renderer = $this->_renderers_stack->pop();
        //set the ob callback to the previous renderer in the stack
        if($this->_renderers_stack->count() > 0) {
            $previous_renderer = $this->_renderers_stack->peek();
            ob_start(array($previous_renderer, 'addOutputContent'));
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_INCONSISTENCE_RENDERING_ERROR');
        }
    }

    public function markPropertyBeginTag($property) {
        $this->_properties_stack->push($property);
        ob_start(array($this, 'setProperty'));
    }
    
    public function markPropertyEndTag() {
        $component_property = $this->_properties_stack->peek();
        if($component_property != null) {
            ob_end_flush();
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_INCONSISTENCE_RENDERING_ERROR');
        }
    }
    
    public function setProperty($buffer = null) {
        $component      = $this->_renderers_stack->peek()->getComponent();
        $property       = $this->_properties_stack->pop();
        $component->$property = $buffer;
        return '';
    }

    private function &_createComponent(__ComponentSpec $component_spec) {
        $component = __ComponentFactory::getInstance()->createComponent($component_spec);
        if($this->_renderers_stack->count() > 1) {
            $parent_component = $this->_renderers_stack->peek()->getComponent();
        }
        else {
            $parent_component = null;
        }
        if($parent_component != null) {
            $component->setContainer($parent_component);
        }
        $component->setViewCode($this->_view_code);
        return $component;
    }

    public function addRenderer(__IRenderer &$renderer) {
        $this->_child_renderers[] =& $renderer;
    }
    
    public function addOutputContent($buffer) {
        $plain_content_renderer = new __PlainContentRenderer($buffer);
        $this->_child_renderers[] =& $plain_content_renderer;
        return '';
    }

    public function render() {
        $return_value = '';
        //do the render
        foreach($this->_child_renderers as &$renderer) {
            $return_value .= $renderer->render();
        }
        $this->_exposeEventHandlerMethods();
        //print the result:
        echo $return_value;
    }
    
    protected function _exposeEventHandlerMethods() {
        if($this->_event_handler != null) {
            $event_handler_class = get_class($this->_event_handler);
            $annotations_collection = __AnnotationParser::getInstance()->getAnnotations($event_handler_class);
            $annotations = $annotations_collection->toArray();
            foreach($annotations as $annotation) {
                switch (strtoupper($annotation->getName())) {
                    case 'REMOTESERVICE':
                        $this->_generateRemoteServiceCode($annotation->getMethod(), $annotation->getArguments());
                        break;
                    default:
                        break; 
                }
            }
        }
    }
    
    protected function _generateRemoteServiceCode($method_name, $arguments = array()) {
        $component_name = $method_name;
        //get the remote_service_writer:
        $remote_service_spec = __ComponentSpecFactory::getInstance()->createComponentSpec('remoteservice');
        $remote_service_spec->setId(substr(md5($this->_view_code . ':' . $method_name), 0, 8));
        $remote_service_writer = $remote_service_spec->getWriter();
        $remote_service = __ComponentFactory::getInstance()->createComponent($remote_service_spec);
        $remote_service->setName($component_name);
        $remote_service->setViewCode($this->_view_code);
        foreach($arguments as $argument_name => $argument_value) {
            $remote_service->$argument_name = $argument_value;
        }
        $remote_service_writer->startRender($remote_service);
    }
    
    
}

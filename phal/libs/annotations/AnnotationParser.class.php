<?php

/**
 * This is the class in charge of parsing classes by detecting comment-based annotation
 *
 */
class __AnnotationParser {

    static private $_instance = null;
    private $_annotations = null;
    
    private function __construct() {
    }
    
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __AnnotationParser();
        }
        return self::$_instance;
    }
    
    public function _loadAnnotations() {
        if(__Phal::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE') == false) {
            $cache = __ApplicationContext::getInstance()->getCache();
            $cached_annotations = $cache->getData('annotations');
            if(is_array($cached_annotations)) {
                $this->_annotations = $cached_annotations;
            }
            else {
                $this->_annotations = array();
            }
        }
        else {
            $this->_annotations = array();
        }
    }
    
    protected function _saveAnnotations() {
        if(__Phal::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE') == false) {
            $cache = __ApplicationContext::getInstance()->getCache();
            $cache->setData('annotations', $this->_annotations);
        }
    }
    
    public function getAnnotations($class_name, $annotation_id = 'phal') {
        //annotations lazy initialization:
        if($this->_annotations == null) {
            $this->_loadAnnotations();
        }
        $class_key = strtoupper($class_name);
        if(key_exists($class_key, $this->_annotations)) {
            $return_value = $this->_annotations[$class_key];
        }
        else {
            $return_value = new __AnnotationsCollection();
            $class = new ReflectionClass($class_name);
            $methods = $class->getMethods();
            foreach($methods as $method) {
                $method_name = $method->getName();
                $method_doc_comments = $method->getDocComment();
                $phal_annotations_matched = array();
                if(preg_match_all('/\@' . $annotation_id . '\s+([^\(\s]+)(\((.+)\))?/', $method_doc_comments, $phal_annotations_matched)) {
                    $annotations_count = count($phal_annotations_matched[0]);
                    for($i = 0; $i < $annotations_count; $i++) {
                        $annotation_name = $phal_annotations_matched[1][$i];
                        if(count($phal_annotations_matched) >= 4) {
                            $annotation_arguments = __ParameterSplitter::splitParameters($phal_annotations_matched[3][$i]);
                        }
                        else {
                            $annotation_arguments = array();
                        }
                        $annotation = new __Annotation($class_name, $method_name, $annotation_name, $annotation_arguments);
                        $return_value->add($annotation);
                        unset($annotation);
                    }
                }
            }
            $this->_annotations[$class_key] = $return_value;
            $this->_saveAnnotations();
        }
        return $return_value;
    }
    
    
}

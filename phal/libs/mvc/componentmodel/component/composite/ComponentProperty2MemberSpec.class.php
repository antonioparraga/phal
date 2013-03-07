<?php

class __ComponentProperty2MemberSpec extends __ComponentPropertySpec {

    protected $_receiver_member = null;
    
    public function setMember($member) {
        $this->_receiver_member = $member;
    }
    
    public function getMember() {
        return $this->_receiver_member;
    }
    
    public function resolveReceiver(__ICompositeComponent &$component) {
        $return_value = null;
        if($this->_receiver_member != null) {
            $receiver_member = $this->_receiver_member;
            if(property_exists($component, $receiver_member)) {
                $return_value = $component->$member;
            }
            else {
                $getter = 'get' . ucfirst($receiver_member);
                if(method_exists($component, $getter)) {
                    $return_value = $component->$getter();
                }
                else {
                    throw __ExceptionFactory::getInstance()->createException('Can not resolve receiver');
                }
            }
        }
        return $return_value;
    }
    
}

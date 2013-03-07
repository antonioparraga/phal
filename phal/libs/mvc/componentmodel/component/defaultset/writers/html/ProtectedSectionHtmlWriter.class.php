<?php


class __ProtectedSectionHtmlWriter extends __ComponentWriter {

    public function canRenderChildrenComponents(__IComponent &$component) {
        $return_value = false;
        $permission_id = $component->getPermission();
        $condition = $component->getCondition();
        if(__PermissionManager::getInstance()->hasPermission($permission_id)) {
            $permission = __PermissionManager::getInstance()->getPermission($permission_id);
            if($condition == __ProtectedSectionComponent::IF_HAS_PERMISSION &&
               __AuthorizationManager::getInstance()->hasPermission($permission)) {
                $return_value = true;
            }
            else if($condition == __ProtectedSectionComponent::IF_NOT_HAS_PERMISSION &&
               !__AuthorizationManager::getInstance()->hasPermission($permission)) {
                $return_value = true;
            }
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Unknow permission id: ' . $permission_id);
        }
        return $return_value;
    }
    
}
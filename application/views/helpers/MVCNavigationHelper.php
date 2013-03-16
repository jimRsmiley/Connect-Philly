<?php

/**
 * create a url given an address
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class Zend_View_Helper_MVCNavigationHelper extends Zend_View_Helper_Abstract
{   
    public $view;
    
    public function mVCNavigationHelper() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controllerName = $request->getControllerName();
        $moduleName = $request->getModuleName();
        $actionName = $request->getActionName();
        
        
        
        $moduleUrl = $this->view->url( 
                    array( 
                        'module'        => $moduleName,
                        'controller'    => 'index',
                        'action'        => 'index' 
                    ) 
                );
        
        $controllerUrl = $this->view->url( 
                    array( 
                        'module'        => $moduleName,
                        'controller'    => $controllerName,
                        'action'        => 'index' 
                    ) 
                );
        
        $actionUrl = $this->view->url( 
                    array( 
                        'module'        => $moduleName,
                        'controller'    => $controllerName,
                        'action'        => $actionName 
                    ) 
                );
        
        $html  = '<div>'
            . '<a href="'.$moduleUrl.'">'.$moduleName.'</a>'
            . ' > '
            . '<a href="'.$controllerUrl.'">'.$controllerName.'</a>'
            . ' > '
            . '<a href="'.$actionUrl.'">'.$actionName.'</a>'
                . '</div>'
                ;
        return $html;
    }
    
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}

?>

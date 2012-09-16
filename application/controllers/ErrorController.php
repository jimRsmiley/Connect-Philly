<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message .': ' . $errors->exception);
        }

        if( class_exists( 'Connect_FileLogger' ) ) {
            Connect_FileLogger::crit( $this->view->message .': ' . $errors->exception );
        }
		
        /*
         * email errors
         */
        $sendEmails = Zend_Registry::get("configuration")->connect->sendEmails;
        if( $sendEmails ) {
            $mailOptions = Connect_Mail_MessageBuilder::errorMessageOptions(
                    $this->view->message .': ' . $errors->exception);
            Connect_Mail::send($mailOptions);
        }        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $controller = Zend_Controller_Front::getInstance();
        $request = $controller->getRequest();
        $module = $request->getModuleName();
        $layout = Zend_Layout::getMvcInstance();

        // check module and automatically set layout
        $layoutsDir = $layout->getLayoutPath();
        
        // check if module layout exists else use default
        if(file_exists($layoutsDir . DIRECTORY_SEPARATOR . $module . ".phtml")) {
            $layout->setLayout($module);
        } else {
            $layout->setLayout("subsite");
        }
        
        $this->view->request = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
		
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
		
        $log = $bootstrap->getResource('Log');
        return $log;
    }
}


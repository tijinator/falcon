<?php
/**
 * Menu controller
 * @package Controllers
 * @author 	KBedi
 * @version	1.0
 */
class MenuController extends Zend_Controller_Action
{
	/**
	 * Main / Default action
	 * @return void
	 */
    public function indexAction()
    {
        // we don't want to append the menu to the end
        // of the layout content, so:
        $this->_helper->viewRenderer->setResponseSegment('menu');
        $this->view->menu = array('x', 'y', 'z');
    }
   
}

?>

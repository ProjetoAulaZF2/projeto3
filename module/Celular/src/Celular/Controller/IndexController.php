<?php
namespace Celular\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Celular\Model\Celular;          // <-- adicione essa linha
use Celular\Form\CelularForm;       // <-- adicione essa linha

class IndexController extends AbstractActionController
{
    protected $celularTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
            'celulares' => $this->getCelularTable()->fetchAll(),
        ));
    }
    
    public function getCelularTable()
    {
    	if (!$this->celularTable) {
    		$sm = $this->getServiceLocator();
    		$this->celularTable = $sm->get('Celular\Model\CelularTable');
    	}
    	return $this->celularTable;
    }
    
    public function addAction()
    {
    	$form = new CelularForm();
    
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$celular = new Celular();
    		$form->setInputFilter($celular->getInputFilter());
    		$form->setData($request->getPost());
    	
    		if ($form->isValid()) {
    			$celular->exchangeArray($form->getData());
    			$this->getCelularTable()->salvarCelular($celular);
    
    			return $this->redirect()->toRoute('celular');
    		}
    	}
    	return array('form' => $form);
    }
}

Terceiro projeto das aulas de Zend Framework 2 com Nataniel Paiva
=======================

Introdução
------------

Esse terceiro projeto contempla os seguintes tópicos:

* Criar um formulário básico com o Zend\Form\Form
* CRUD da primeira tabela que criamos, no caso a tb_celular
* Validadores e filtros


Criação do formulário para adicionar registros
-----------------------------------------------

Primeiro vamos criar a nossa classe de formulário no arquivo projeto3/module/Celular/src/Celular/Form/CelularForm.php 
com o seguinte código:

	<?php
	namespace Celular\Form;

	use Zend\Form\Form;

	class CelularForm extends Form
	{
	    public function __construct($name = null)
	    {
		parent::__construct('celular');
		$this->setAttribute('method', 'post');
		$this->add(array(
		    'name' => 'id',
		    'type' => 'Hidden',
		));
		$this->add(array(
		    'name' => 'marca',
		    'type' => 'Text',
		    'options' => array(
		        'label' => 'Marca',
		    ),
		));
		$this->add(array(
		    'name' => 'modelo',
		    'type' => 'Modelo',
		    'options' => array(
		        'label' => 'Modelo',
		    ),
		));
		$this->add(array(
		    'name' => 'submit',
		    'type' => 'Submit',
		    'attributes' => array(
		        'value' => 'Salvar',
		        'id' => 'submitbutton',
		    ),
		));
	    }
	}


Confira se no seu arquivo de model que fica no caminho projeto3/module/Celular/src/Celular/Model/Celular.php 
tem as seguintes namespaces:

	use Zend\InputFilter\Factory as InputFactory;
	use Zend\InputFilter\InputFilter;
	use Zend\InputFilter\InputFilterAwareInterface;
	use Zend\InputFilter\InputFilterInterface;

Se não tiver, adicione juntamente com o seguinte código:

	public function setInputFilter(InputFilterInterface $inputFilter)
          {
		throw new \Exception("Não validado");
	   }

Logo após coloquei o código abaixo:

	    public function getInputFilter()
	    {
		if (!$this->inputFilter) {
		    $inputFilter = new InputFilter();
		    $factory     = new InputFactory();

		    $inputFilter->add($factory->createInput(array(
		        'name'     => 'id',
		        'required' => true,
		        'filters'  => array(
		            array('name' => 'Int'),
		        ),
		    )));

		    $inputFilter->add($factory->createInput(array(
		        'name'     => 'marca',
		        'required' => true,
		        'filters'  => array(
		            array('name' => 'StripTags'),
		            array('name' => 'StringTrim'),
		        ),
		        'validators' => array(
		            array(
		                'name'    => 'StringLength',
		                'options' => array(
		                    'encoding' => 'UTF-8',
		                    'min'      => 1,
		                    'max'      => 100,
		                ),
		            ),
		        ),
		    )));

		    $inputFilter->add($factory->createInput(array(
		        'name'     => 'modelo',
		        'required' => true,
		        'filters'  => array(
		            array('name' => 'StripTags'),
		            array('name' => 'StringTrim'),
		        ),
		        'validators' => array(
		            array(
		                'name'    => 'StringLength',
		                'options' => array(
		                    'encoding' => 'UTF-8',
		                    'min'      => 1,
		                    'max'      => 100,
		                ),
		            ),
		        ),
		    )));

		    $this->inputFilter = $inputFilter;
		}

		return $this->inputFilter;
	    }

Verifique também se sua classe possui o seguinte atributo:

	protected $inputFilter; 

Perfeito, com isso seu formulário já está meio caminho andado, agora vamos para a controller e depois para a view.
Na controller adicione o seguinte código, lembrando que o caminho da sua controller é
projeto3/module/Celular/src/Celular/Controller/IndexController.php.

	use Celular\Model\Celular;          // <-- adicione essa linha
	use Celular\Form\CelularForm;       // <-- adicione essa linha


E depois no mesmo arquivo, ou seja, na sua classe de controller coloque a sua Action add:

	public function addAction()
	    {
	    	$form = new CelularForm();
	    	$form->get('submit')->setValue('Add');
	    
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

Agora vamos criar um método de salvar o celular em nossa classe CelularTable que está no caminho
projeto3/module/Celular/src/Celular/Model/CelularTable.php vamos criar o método abaixo:

	public function salvarCelular(Celular $celular)
	    {
		$data = array(
		    'marca' => $celular->marca,
		    'modelo' => $celular->modelo,
		    'ativo' => CelularTable::ATIVO,
		);
		
		$id = (int) $celular->id;
		if ($id == 0) {
		    $this->tableGateway->insert($data);
		} else {
		    if ($this->getCelular($id)) {
		        $this->tableGateway->update($data, array(
		            'id' => $id
		        ));
		    } else {
		        throw new \Exception('Não existe registro com esse ID' . $id);
		    }
		}
	    }

Por último em seu add.phtml vamos colocar o código:

	<?php

	$title = 'Cadastrar um novo celular';
	$this->headTitle($title);
	?>
	<h1><?php echo $this->escapeHtml($title); ?></h1>
	<?php
	$form = $this->form;
	$form->setAttribute('action', $this->basePath('celular/index/add'));
	$form->prepare();

	echo $this->form()->openTag($form);
	echo $this->formHidden($form->get('id'));
	echo $this->formRow($form->get('marca'));
	echo $this->formRow($form->get('modelo'));
	echo $this->formSubmit($form->get('submit'));
	echo $this->form()->closeTag();
Pronto! Criamos nosso primeiro formulário de cadastro de celulares.





















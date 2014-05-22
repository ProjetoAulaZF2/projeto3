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
            'type' => 'Text',
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
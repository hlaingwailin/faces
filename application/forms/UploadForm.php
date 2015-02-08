<?php
class Form_UploadForm extends Zend_Form
{
    public function __construct($option = null)
    {
        parent::__construct($option);
        $this->setName('uploadform');
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setMethod('post');

        $masterData = new Zend_Form_Element_File('Master_Data_File');
        $masterData->setAttrib('multiple', 'false');
        $masterData->setIsArray(true);
        $masterData->setRequired(false);
        $masterData->addValidator('Extension', false, 'xls');
        $masterData->setValueDisabled(true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');

        $this->addElements(array($masterData,  $submit));
    }

    public function loadDefaultDecorators()
    {
        $script_path = 'form/uploadform.phtml';
        $this->setDecorators(array(array('ViewScript', array('viewScript' => $script_path))));
    }
}

?>
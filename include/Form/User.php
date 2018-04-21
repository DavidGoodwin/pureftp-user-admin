<?php

namespace PureFTPAdmin\Form;

class User implements Form {

    /**
     * @param \Zend_Form $form
     */
    protected $form;

    public function __construct(array $data = []) {

        $this->form = new \Zend_Form();

        $this->form->setMethod('POST');
        $this->form->setAction('');

        $username = new \Zend_Form_Element_Text('username');
        $username->setRequired(True);
        $username->setLabel('Username');
        $username->setDescription('FTP Username');
        $username->addValidator(new \Zend_Validate_StringLength(1,30));


        $dir = new \Zend_Form_Element_Text('dir');
        $dir->setRequired(True);
        $dir->setLabel('Home Directory');
        $dir->setDescription('Filesystem path');
        $dir->addValidator(new \Zend_Validate_StringLength(1,100));


        $email = new \Zend_Form_Element_Text('email');
        $email->setRequired(false);
        $email->setLabel('Email address');
        $email->addValidator(new \Zend_Validate_StringLength(1, 100));
        $email->addValidator(new \Zend_Validate_EmailAddress());



        $password = new \Zend_Form_Element_Text('password');
        if(empty($data)) {
            $password->setRequired(true);
        }
        $password->setLabel('Password');
        $password->addValidator(new \Zend_Validate_StringLength(1,100));


        $uid_select = new \Zend_Form_Element_Select('uid');
        $uid_select->setRequired(true);
        $uid_select->setLabel('User ID');

        $gid_select = new \Zend_Form_Element_Select('gid');
        $gid_select->setRequired(true);
        $gid_select->setLabel("Group ID");

        $this->form->addElement($username);
        $this->form->addElement($password);

        $this->form->addElement($dir);
        $this->form->addElement($email);

        $this->form->addElement($uid_select);
        $this->form->addElement($gid_select);

        $submit = new \Zend_Form_Element_Submit('Save');

        $this->form->addElement($submit);

        if(!empty($data)) {
            $this->form->populate($data);
        }

        $this->form->setElementFilters(array('StringTrim', 'StripTags'));
    }

    /**
     * @return string (html, presumably)
     */
    public function render() {

        // see http://blog.kosev.net/2010/06/tutorial-create-zend-framework-form/
        $this->form->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));
        $this->form->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));


        return $this->form->render(new \Zend_View());
    }

    /**
     * @return array
     */
    public function getValues() {
        return $this->form->getValues();
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data) {
        return $this->form->isValid($data);
    }

    /**
     * @param array $list
     * @return User
     */
    public function setGidList(array $list) {
        /* @var \Zend_Form_Element_Select $select */
        $select = $this->form->getElement('uid');
        $select->setMultiOptions($list);
        return $this;
    }

    /**
     * @param array $list
     * @return User
     */
    public function setUidList(array $list) {
        /* @var \Zend_Form_Element_Select $select */
        $select = $this->form->getElement('gid');
        $select->setMultiOptions($list);
        return $this;

    }
}

<?php
class AuthenticationController extends Zend_Controller_Action
{

   public function init(){
       $this->getHelper('layout')->setLayout('admin');
   }

    public function loginAction()
    {
        //if already logged in, redirect to index page
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('admin/index');
        }
        $form = new Form_LoginForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $authAdapter = $this->getAuthAdapter();

                $email = $form->getValue('email');
                $pass = $form->getValue('password');

                $authAdapter->setIdentity($email)->setCredential($pass);

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $identity = $authAdapter->getResultRowObject();
                    //check to see if user is confirmed as a user or broker
                    if (!empty($identity->Usr_Role)) {
                        $authStorage = $auth->getStorage();
                        $authStorage->write($identity);
                        $id = $auth->getStorage()->read()->Usr_Id;
                        if ($auth->getStorage()->read()->Usr_Role == 'admin') {
                            $this->_redirect('admin/index');
                        }
                    } else { //user don't have role yet
                        $this->view->errorMessage = "Your account is not confirmed yet.";
                        $auth->clearIdentity();
                    }
                } else {
                    $this->view->errorMessage = "Invalid Email and Password Combination.";
                    //echo "invalid";
                }
            }
        }

        $this->view->form = $form;
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        unset($_SESSION);
        $this->_redirect('admin/index');
    }

    private function getAuthAdapter()
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('tbl_users')
            ->setIdentityColumn('Usr_Email')
            ->setCredentialColumn('Usr_Password');
        $authAdapter->setCredentialTreatment('md5(?)');
        return $authAdapter;
    }
}

?>
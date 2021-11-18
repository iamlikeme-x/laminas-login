<?php

namespace User\Controller;

use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Form\UserForm;
use User\Model\User;
use User\Model\UserTable;

class UserController extends AbstractActionController
{
    protected $table;

    /**
     * Constructor function to inject the table model
     */
    public function __construct(UserTable $table)
    {
        $this->table = $table;
    }

    /**
     * Index action for the user
     * Route: /user
     * 
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'users' => $this->table->fetchAll(),
        ]);
    }

    /**
     * Add action for the user
     * Route: /user/add
     * 
     * @return Response|UserForm[]
     */
    public function addAction()
    {
        $form = new UserForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (!$request->isPost()) {
            return ['form' => $form];
        }

        $user = new User();
        $form->setInputFilter($user->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $user->exchangeArray($form->getData());
        $this->table->saveUser($user);

        return $this->redirect()->toRoute('users');
    }

    /**
     * Edit user action
     * Route: /user/edit/[:id]
     */
    public function editAction()
    {
        $user = $this->fetchUser();

        if (!$user) {
            $this->getResponse()->setStatusCode(404);
            $this->getResponse()->setReasonPhrase("Cannot find user");
            return;
        }
        
        $form = new UserForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        $viewData = ['id' => $user->id, 'form' => $form];

        if (!$request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($user->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return $viewData;
        }

        try {
            $this->table->saveUser($user);
        } catch (\Exception $ex) {
            $this->getResponse()->setStatusCode(400);
            $this->getResponse()->setReasonPhrase("Could not save user: {$ex->getMessage()}");
            return $viewData;
        }

        return $this->redirect()->toRoute('users', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $user = $this->fetchUser();
        
        if (!$user) {
            $this->getResponse()->setStatusCode(404);
            $this->getResponse()->setReasonPhrase("Cannot find user");
            return;
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $del = $request->getPost('delete', 'No');

            if ($del === 'Yes') {
                $this->table->deleteUser($user);
            }

            return $this->redirect()->toRoute('users', ['action' => 'index']);
        }

        return [
            'id' => $user->id,
            'user' => $user,
        ];
    }

    /**
     * Fix docblocks for this function
     * 
     * @return Request
     */
    public function getRequest(): Request
    {
        return parent::getRequest();
    }

    /**
     * Fix docblocks for this function
     * 
     * @return Response
     */
    public function getResponse(): Response
    {
        return parent::getResponse();
    }

    protected function fetchUser(): ?User
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        try {
            return $id !== 0 ? $this->table->getUser($id) : null;
        } catch (\Exception $ex) {
            return null;
        }
    }
}
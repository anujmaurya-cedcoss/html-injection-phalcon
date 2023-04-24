<?php
use Phalcon\Mvc\Controller;
use Phalcon\Escaper;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class RegisterController extends Controller
{
    public function IndexAction()
    {
        // nothing here
    }
    public function registerAction()
    {
        // redirected to register action
    }

    public function processAction()
    {
        // creating a new user, with name and email obtained by post method
        $user = new Users();
        $escaper = new Escaper();
        $arr = array(
            'name' => $escaper->escapeHtml($this->request->getPost('name')),
            'email' => $escaper->escapeHtml($this->request->getPost('email')),
            'password' => $escaper->escapeHtml($this->request->getPost('password'))
        );
        $attack = ($this->request->getPost('name') != $arr['name']
            || $this->request->getPost('email') != $arr['email']
            || $this->request->getPost('password') != $arr['password']
        );

        $user->assign(
            $arr,
            [
                'name',
                'email',
                'password'
            ]
        );
        if ($attack) {
            $adapter = new Stream(APP_PATH . '/logs/attack.log');
            $logger = new Logger(
                'messages',
                [
                    'main' => $adapter,
                ]
            );
            $logger->error('HTML injection detected : name => \'' . $this->request->getPost('name') . '\'
             email => \'' . $this->request->getPost('email') . '\' password => \''
                . $this->request->getPost('password') . '\'');

        }
        // if the user details is saved, then return success
        $success = $user->save();

        $this->view->success = $success;
        if ($success) {
            $this->view->message = "Register succesfully";
        } else {
            $this->view->message = "Not Register due to following reason: <br>" . implode("<br>", $user->getMessages());
        }
    }
}

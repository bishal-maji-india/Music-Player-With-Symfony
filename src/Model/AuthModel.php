<?php

namespace App\Model;
use App\Entity\UserTable;
use App\Model\MainModel;

use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthModel extends  AbstractController
{

    public $servername;
    public $dbname;
    public $username;
    public $password_sql;

    public function __construct()
    {

        $this->servername =$_ENV['APP_SERVERNAME'] ;
        $this->dbname =$_ENV['APP_DB_NAME'] ;
        $this->username = $_ENV['APP_USERNAME'] ;
        $this->password_sql =$_ENV['APP_PASSWORD'] ;
    }

    //this function returns true if user exist in db.
    public function isUserExist($mail,$password,$em)
    {
        $user_repo = $em->getRepository(UserTable::class);
        $user_arr=$user_repo->findAll();
        foreach ($user_arr as $row) {
            if ($row->getEmail() == $mail &&  $row->getPassword() == $password) {
                $session = new Session();
                $session->start();
                $session->set('uid', $row->getId());
                $session->set('login',1);
                return true;
            }
        }
        return false;
    }
    public function isRegisterDone($article,$em)
    {
        $em->persist($article);
        if($em->flush()==null){
            $session = new Session();
            $session->start();
            $session->set('uid', $article->getId());
            $session->set('login', 1);
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * @throws GuzzleException
     */
    public function verifyMail($email)
    {
        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', "https://emailvalidation.abstractapi.com/v1/?api_key=b9fbc7b61bd24a69819ce7a628bdf666&email=$email");
        return json_decode($res->getBody(), true);
    }
}
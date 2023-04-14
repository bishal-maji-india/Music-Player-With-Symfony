<?php

namespace App\Model;

use App\Entity\UserTable;
use GuzzleHttp;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthModel extends  AbstractController
{

  public $servername;
  public $dbname;
  public $username;
  public $password_sql;

  // Constructor to inililize the data used for this class.
  public function __construct()
  {

    $this->servername = $_ENV['APP_SERVERNAME'];
    $this->dbname = $_ENV['APP_DB_NAME'];
    $this->username = $_ENV['APP_USERNAME'];
    $this->password_sql = $_ENV['APP_PASSWORD'];
  }

  /**
   * Function returns true if user with the following data exist in db.
   * 
   * @param $mail
   * Contains user email address.
   * 
   * @param $password
   * Contains password of the user.
   * 
   * @param $em
   * Contains the instance of entity manager variable.
   * 
   */
  public function isUserExist($mail, $password, $em)
  {
    $user_repo = $em->getRepository(UserTable::class);
    $user_arr = $user_repo->findAll();
    foreach ($user_arr as $row) {
      if ($row->getEmail() == $mail &&  $row->getPassword() == $password) {
        $session = new Session();
        $session->start();
        $session->set('uid', $row->getId());
        $session->set('login', 1);
        return true;
      }
    }
    return false;
  }

  /**
   * Function to add new user in db and return true on success addition.
   * 
   * @param $data
   * Contains instance of user form data.
   * 
   * @param $em
   * Contains the instance of entity manager variable.
   * 
   */
  public function isRegisterDone($data, $em)
  {
    $em->persist($data);
    if ($em->flush() == null) {
      $session = new Session();
      $session->set('uid', $data->getId());
      $session->set('login', 1);
      return true;
    } else {
      return false;
    }
  }

  /**
   * Checks for valid mail address and return true if valid.
   * 
   * @param $email
   * Instance of user email.
   * 
   * @throws GuzzleException
   */
  public function verifyMail($email)
  {
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET', "https://emailvalidation.abstractapi.com/v1/?api_key=b9fbc7b61bd24a69819ce7a628bdf666&email=$email");
    return json_decode($res->getBody(), true);
  }
}

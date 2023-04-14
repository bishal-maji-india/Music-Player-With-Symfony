<?php

namespace App\Controller;

use App\Entity\UserTable;
use App\Model\AuthModel;
use App\Model\MainModel;
use GuzzleHttp;


use Doctrine\ORM\EntityManagerInterface;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
  /**
   * @var object $model
   * Instance of object of auth model.
   */
  public $model = null;


  /**
   * @var EntityManagerInterface $em
   * Instance of Entity manager Interface.
   */
  public EntityManagerInterface $em;


  /**
   * Constructor to inilize values required.
   * 
   * @param EntityManagerInterface $em
   * Instance of Entity manager.
   * 
   */
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
    $this->model = new AuthModel();
  }


  /**
   * Navigate user to the login page.
   * 
   * @Route("/login_page", name="login_page_route")
   * 
   * @param Request $request
   * Instance of request variable.
   * 
   * @param EntityManagerInterface $entityMangaer
   * Instance of entity manager interface.
   *
   */
  public function loginPage(Request $request)
  {
    //create form with email, and password fields.
    $form_login = $this->createFormBuilder([])
      ->add('email', EmailType::class, ['label' => 'Email', 'required' => true])
      ->add('password', PasswordType::class, ['label' => 'Password', 'required' => true])
      ->add('save', SubmitType::class, ['label' => 'Login'])
      ->getForm();
    $form_login->handleRequest($request);

    // Handle form submission request.
    if ($form_login->isSubmitted() and $form_login->isValid()) {
      $data = $form_login->getData();
      $param_email = $data['email'];
      $param_password = $data['password'];

      //Navigate to homepage if user exist in
      //data-base with matching password.        
      if ($this->model->isUserExist($param_email, $param_password, $this->em)) {
        return $this->redirectToRoute('home_page_route');
      } else {
        return $this->redirectToRoute('error_route', ['message' => 'No User Found with give fields', 'nav_route' => 'login_page_route']);
      }
    }
    return $this->render('auth/login.html.twig', ['login_form' => $form_login->createView()]);
  }


  /**
   * Register the user and add user data in db.
   * 
   * @Route("/register_page", name="register_page_route")
   * 
   * @param Request $request
   * Instance of request variable.
   *  
   * @param ValidatorInterface $validator
   * Instance of pre-defined symfony Validator class for user data validation.
   * 
   * @throws GuzzleException
   */
  public function registerPage(Request $request, ValidatorInterface $validator)
  {
    /**
     * @param $mail_err
     * Contain error message in email field after validation.
     * 
     * @param error_arr
     * Contain error message of all the other fields.
     * 
     */
    $mail_err = "";
    $error_arr = array();


    // Create form to take user data for registration.
    $form = $this->createFormBuilder([])
      ->add('name', TextType::class, ['label' => 'Name', 'required' => true])
      ->add('email', EmailType::class, ['label' => 'Email', 'required' => true])
      ->add('password', PasswordType::class, ['label' => 'Password', 'required' => true])
      ->add('interest', ChoiceType::class, ['choices' => ['Rock' => 1, 'Melody' => 2, 'Soft' => 3], 'label' => 'Genre', 'required' => true])
      ->add('contact', NumberType::class, ['label' => 'Contact', 'required' => true])
      ->add('save', SubmitType::class, ['label' => 'Register'])
      ->getForm();

    $form->handleRequest($request);

    // Handle form submission and form validation.
    if ($form->isSubmitted() and $form->isValid()) {
      $data = $form->getData();

      // Instance of auth model.
      $model = new AuthModel();

      /**
       * Verify the mail using api and return true or 
       * false in case of valid or invalid mail.
       * */
      $body = $model->verifyMail($data['email']);

      if (!$body['is_valid_format']['value']) {
        $mail_err = "Invalid Email Address";
      } else {
        $model = new AuthModel();

        // Instance of user table.
        $user_table = new UserTable();
        $user_table->setName($data['name']);
        $user_table->setEmail($data['email']);
        $user_table->setPassword($data['password']);
        $user_table->setContact($data['contact']);
        $user_table->setInterest($data['interest']);

        // Validate other fields of the form data.
        $errors = $validator->validate($user_table);

        if (count($errors) > 0) {
          $i = 0;
          foreach ($errors as $error) {
            $error_arr[$i] = $error->getMessage();
            $i = $i + 1;
          }
        } else {
          if ($model->isRegisterDone($user_table, $this->em)) {
            return $this->redirectToRoute('home_page_route');
          } else {
            return $this->redirectToRoute('error_route', ['message' => 'Registration Unsuccessful', 'nav_route' => 'index']);
          }
        }
      }
    }
    return $this->render('auth/register.html.twig', ['user_form' => $form->createView(), 'mail_err' => $mail_err, 'errors' => $error_arr]);
  }

  /**
   * Destroy the user session and move the user to the home page.
   * 
   * @Route("/logout", name="logout_route")
   * 
   */
  public function logout()
  {
    // Instance of session variable.
    $session = new Session();
    // Logout the user.
    $session->set('login', 0);
    // Move the user to the auth page.
    return $this->redirectToRoute('index');
  }
}

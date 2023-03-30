<?php

namespace App\Controller;

use App\Entity\UserTable;
use App\Model\AuthModel;
use App\Model\MainModel;
use AppBundle\Entity\UserForm;

use Doctrine\ORM\EntityManagerInterface;
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

class AuthController extends  AbstractController {
    /**
     * @Route("/login_page", name="login_page_route")
     * 
     */
    public function login_page(EntityManagerInterface $em,Request $request)
    {
    $form_login=$this->createFormBuilder([])
            ->add('email',EmailType::class,['label'=>'Email','required'=>true])
            ->add('password',PasswordType::class,['label'=>'Password','required'=>true])
            ->add('save',SubmitType::class,['label'=>'Login'])
            ->getForm();
        $form_login->handleRequest($request);
        if($form_login->isSubmitted() and $form_login->isValid()){
            $data = $form_login->getData();
            $model=new AuthModel();
            $param_email=$data['email'];
            $param_password=$data['password'];
            if($model->isUserExist($param_email,$param_password,$em))
            {
                return $this->redirectToRoute('home_page_route');
            }
            else {
                return $this->redirectToRoute('error_route',['message'=>'No User Found with give fields','nav_route'=>'login_page_route']);

            }
        }
        return $this->render('home/login.html.twig',['login_form'=>$form_login->createView()]);
    }


    /**
     * @Route("/register_page", name="register_page_route")
     */
    public function register_page(EntityManagerInterface $em,Request $request)
    {
        $form=$this->createFormBuilder([])
            ->add('name',TextType::class,['label'=>'Name','required'=>true])
            ->add('email',EmailType::class,['label'=>'Email','required'=>true])
            ->add('password',PasswordType::class,['label'=>'Password','required'=>true])

            ->add('intrest',ChoiceType::class,['choices'=> ['one'=>1,'two'=>2,'three'=>3],'label'=>'Genre','required'=>true])
            ->add('contact',NumberType::class,['label'=>'Contact','required'=>true])
            ->add('save',SubmitType::class,['label'=>'Register'])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $data = $form->getData();
            $model=new AuthModel();
            $article = new UserTable();
            $article->setName($data['name']);
            $article->setEmail($data['email']);
            $article->setPassword($data['password']);
            $article->setContact($data['contact']);
            $article->setIntrest($data['intrest']);

            if($model->isRegisterDone($article, $em))
            {
                return $this->redirectToRoute('home_page_route');
            }
            else {
                return $this->redirectToRoute('error_page_route');

                }
        }
        return $this->render('home/register.html.twig',['user_form'=>$form->createView()]);
    }

  /**
   * @Route("/logout", name="logout_route")
   */
  public function logout(){
      $session=new Session();
      $session->set('login',0);
      return $this->redirectToRoute('index');

  }


}

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
     * @Route("/login_page", name="login_page_route")
     *
     */
    public function login_page(EntityManagerInterface $em, Request $request, ValidatorInterface $validator)
    {
        $error_arr = array();
        $form_login = $this->createFormBuilder([])
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => true])
            ->add('password', PasswordType::class, ['label' => 'Password', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'Login'])
            ->getForm();
        $form_login->handleRequest($request);

        if ($form_login->isSubmitted() and $form_login->isValid()) {
            $data = $form_login->getData();
            $param_email = $data['email'];
            $param_password = $data['password'];
            $model = new AuthModel();

            if ($model->isUserExist($param_email, $param_password, $em)) {
                return $this->redirectToRoute('home_page_route');
            } else {
                return $this->redirectToRoute('error_route', ['message' => 'No User Found with give fields', 'nav_route' => 'login_page_route']);

            }

        }
        return $this->render('auth/login.html.twig', ['login_form' => $form_login->createView()]);
    }


    /**
     * @Route("/register_page", name="register_page_route")
     * @throws GuzzleException
     */
    public function register_page(EntityManagerInterface $em, Request $request, ValidatorInterface $validator)
    {
        $mail_err = "";
        $error_arr = array();
        $form = $this->createFormBuilder([])
            ->add('name', TextType::class, ['label' => 'Name', 'required' => true])
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => true])
            ->add('password', PasswordType::class, ['label' => 'Password', 'required' => true])
            ->add('interest', ChoiceType::class, ['choices' => ['Rock' => 1, 'Melody' => 2, 'Soft' => 3], 'label' => 'Genre', 'required' => true])
            ->add('contact', NumberType::class, ['label' => 'Contact', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'Register'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $data = $form->getData();
            $model = new AuthModel();
            $body = $model->verifyMail($data['email']);//email validation
            if (!$body['is_valid_format']['value']) {
                $mail_err = "Invalid Email Address";
            } else {
                $model = new AuthModel();
                $article = new UserTable();
                $article->setName($data['name']);
                $article->setEmail($data['email']);
                $article->setPassword($data['password']);
                $article->setContact($data['contact']);
                $article->setInterest($data['interest']);

                $errors = $validator->validate($article);//form other field validation
                if (count($errors) > 0) {
                    $i = 0;
                    foreach ($errors as $error) {
                        $error_arr[$i] = $error->getMessage();
                        $i = $i + 1;
                    }

                } else {
                    if ($model->isRegisterDone($article, $em)) {
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
     * @Route("/logout", name="logout_route")
     */
    public function logout()
    {
        $session = new Session();
        $session->set('login', 0);
        return $this->redirectToRoute('index');

    }

}

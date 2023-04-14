<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Used to control the entry route of the application.
 */
class MainController extends AbstractController
{
    /**
     * Navigates the user to auth page or home page as per session.
     * 
     * @Route("/", name="index")
     */
    public function index()
    {
        $session=new Session();
        
        if ($session->get('login')==1){
            return $this->redirectToRoute('home_page_route');
        }else{
            return $this->render('home/index.html.twig');
        }
      
    }


}

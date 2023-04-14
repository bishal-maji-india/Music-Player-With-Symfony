<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\FavouriteTable;
use App\Entity\MusicTable;
use App\Entity\UserTable;
use App\Model\HomeModel;

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class DialogController extends AbstractController
{
    /**
     * Navigates user to success page with message and navigation route as paramater.
     * 
     * @Route("/succcess/{message}/{nav_route}", name="success_route")
     * 
     * @param $message
     * Contains instance of success message.
     * 
     * @param $nav_route
     * Contains instance of path to redirect after user see the success page and click ok button.
     *
     *
     */
    public function showSuccessDialog($message, $nav_route)
    {
        return $this->render('dialog/success_page.html.twig', ['message' => $message, 'nav_route' => $nav_route]);
    }

    /**
     * Navigates user to error page with message and navigation route as paramater.
     * 
     * @Route("/error/{message}/{nav_route}", name="error_route")
     * 
     * @param $message
     * Contains instance of error message.
     * 
     * @param $nav_route
     * Contains instance of path to redirect after user see the error page and click ok button.
     *
     */
    public function showErrorDialog($message, $nav_route)
    {
        return $this->render('dialog/error_page.html.twig', ['message' => $message, 'nav_route' => $nav_route]);
    }

}
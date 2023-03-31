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
     * @Route("/succcess/{message}/{nav_route}", name="success_route")
     * @param $message
     * @param $nav_route
     *
     */
    public function showSuccessDialog($message, $nav_route)
    {
        return $this->render('home/success_page.html.twig', ['message' => $message, 'nav_route' => $nav_route]);
    }

    /**
     * @Route("/error/{message}/{nav_route}", name="error_route")
     * @param $message
     * @param $nav_route
     *
     */
    public function showErrorDialog($message, $nav_route)
    {
        return $this->render('home/error_page.html.twig', ['message' => $message, 'nav_route' => $nav_route]);
    }

}
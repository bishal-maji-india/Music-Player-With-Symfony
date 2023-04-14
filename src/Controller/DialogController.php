<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function numberAction()
    {
        // if(is_numeric($number))
            return $this->render('admin/index.html.twig', array(
                // 'number' => $number,
            ));
        // else
            // return $this->redirectToRoute('lucky_number_generate');
    }

}

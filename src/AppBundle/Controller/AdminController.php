<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    
    /**
     * @Route("/github-repo-redirect", name="github_repo")
     */
    public function githubRedirectAction()
    {
        return $this->redirect('https://github.com/prabhakar267/dquip-task');
    }

    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction()
    {
        // if(is_numeric($number))
            return $this->render('admin/index.html.twig', array(
                // 'number' => $number,
            ));
        // else
            // return $this->redirectToRoute('lucky_number_generate');
    }

}

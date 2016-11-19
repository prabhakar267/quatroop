<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/documentation", name="docs")
     */
    public function documentationAction()
    {
        // if(is_numeric($number))
            return $this->render('admin/doc.html.twig', array(
                // 'number' => $number,
            ));
        // else
            // return $this->redirectToRoute('lucky_number_generate');
    }

    /**
     * @Route("/user", name="viewuser")
     */
    public function viewUserAction()
    {
        // if(is_numeric($number))
            return $this->render('admin/view-user.html.twig', array(
                // 'number' => $number,
            ));
        // else
            // return $this->redirectToRoute('lucky_number_generate');
    }

}

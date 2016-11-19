<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller{
    
    private function _getAllUsers(){
        $repository = $this->getDoctrine()->getRepository("AppBundle\Entity\User");

        $users_array = array();
        $users = $repository->findBy(array(), array('user_name' => 'ASC'));

        foreach($users as $user){
            $user_temp = array(
                'id' => $user->user_id,
                'name' => $user->user_name,
            );
            array_push($users_array, $user_temp);
        }

        return $users_array;
    }

    /**
     * @Route("/add-user", name="adduser")
     */
    public function addUserAction(){

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $request = Request::createFromGlobals();

        if($request->getMethod() == 'GET'){

            return $this->render('admin/add-user.html.twig', array(
                "all_users" => $this->_getAllUsers(),
            ));
        } else {
            $name = $request->request->get('name');
            $parent_id = (int)$request->request->get('parent');

            $user = new User();
            $user->setUserName($name);

            if($parent_id != 0){
                $parents_json = [$parent_id];
                $user->setParents(json_encode($parents_json));
                
                $parent_user = $repository->find($parent_id);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();


            $new_user_id = $user->getUserId();

            if(isset($parent_user)){
                $parent_children = $parent_user->children;
                $parent_children = json_decode($parent_children);

                if(is_null($parent_children)){
                    $parent_children = [$new_user_id];
                } else {
                    array_push($parent_children, $new_user_id);
                    sort($parent_children);
                }
                $parent_user->setChildren(json_encode($parent_children));
                $em->flush();
            }

            return $this->render('admin/add-user.html.twig', array(
                "success" => true,
                "message" => "User successfully added!<br>User ID : <strong>" . $new_user_id . "</strong>",
                "all_users" => $this->_getAllUsers(),
            ));
        }
    }

    /**
     * @Route("/user", name="viewuser")
     */
    public function viewUserAction(){
        return $this->render('admin/view-user.html.twig', array());
    }

}

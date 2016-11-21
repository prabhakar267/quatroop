<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller{
    
    private function _getAllUsers($mode=false){
        $repository = $this->getDoctrine()->getRepository("AppBundle\Entity\User");

        $users_array = array();
        $users = $repository->findBy(array(), array('user_name' => 'ASC'));

        foreach($users as $user){
            if($mode){
                $user_temp = array(
                    'id' => $user->user_id,
                    'name' => $user->user_name,
                    'parents' => json_decode($user->parents),
                    'children' => json_decode($user->children),
                );
                array_push($users_array, $user_temp);
            } else {
                $user_temp = array(
                    'id' => $user->user_id,
                    'name' => $user->user_name,
                );
                array_push($users_array, $user_temp);
            }
        }

        return $users_array;
    }

    private function _getUserObjectsFromIDs($all_users, $users_array){
        $output_array = [];
        if(empty($users_array))
            return $output_array;

        foreach($users_array as $user_id){
            $random_num = rand(1,7);
            $temp_user = array(
                'id' => $user_id,
                'name' => $all_users[$user_id],
                'image' => "img/random_users/user" . $random_num . "-128x128.jpg",
            );

            array_push($output_array, $temp_user);
        }
        return $output_array;
    }

    private function _getAllNodes($root, $adjacency_list){
        $ans = [];
        $stack = [];
        $visited = [];

        foreach($adjacency_list as $node_id => $node)
            $visited[$node_id] = false;

        // initialization of stack
        array_push($stack, $root);

        while(!empty($stack)){
            $curr_node = array_pop($stack);
            $visited[$curr_node] = true;
            array_push($ans, $curr_node);

            if(!is_null($adjacency_list[$curr_node]))
                foreach($adjacency_list[$curr_node] as $node)
                    if(!$visited[$node])
                        array_push($stack, $node);
        }
        sort($ans);
        
        return $ans;
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
        $request = Request::createFromGlobals();

        if($request->getMethod() == 'GET'){
            return $this->render('admin/view-user.html.twig', array(
                "all_users" => $this->_getAllUsers(),
            ));
        } else {
            $user_id = (int)$request->request->get('user');
            
            return $this->redirectToRoute('view_one_user', array(
                'user_id' => $user_id,
            ));
        }
    }


    /**
     * @Route("/user/{user_id}", name="view_one_user")
     */
    public function viewOneUserAction($user_id){
        // check if the user id is a postitive integer or not
        $user_id = (int)$user_id;
        if($user_id <= 0){
            return $this->redirectToRoute('viewuser');
        }
        
        $all_users_information = $this->_getAllUsers($mode=true);

        $immediate_parents = [];
        $immediate_children = [];
        $all_parents = [];
        $all_children = [];

        $parents = [];
        $children = [];
        $names = [];
        $user_existence_flag = false;

        foreach($all_users_information as $temp_user){
            $id = $temp_user["id"];
            
            if($id == $user_id)
                $user_existence_flag = true;

            $parents[$id] = $temp_user['parents'];
            $children[$id] = $temp_user['children'];
            $names[$id] = $temp_user['name'];
        }


        if(!$user_existence_flag)
            return $this->render('admin/view-user.html.twig', array(
                "user_success" => true,
                "info_success" => false,
                "message" => "Sorry, we could not find a user with ID : <strong>" . $user_id . "</strong>",
                "all_users" => $all_users_information,
            ));
        else {
            $immediate_children = $this->_getUserObjectsFromIDs($names, $children[$user_id]);
            $immediate_parents = $this->_getUserObjectsFromIDs($names, $parents[$user_id]);

            $all_children = $this->_getAllNodes($user_id, $children);
            $all_parents = $this->_getAllNodes($user_id, $parents);

            $all_children = $this->_getUserObjectsFromIDs($names, $all_children);
            $all_parents = $this->_getUserObjectsFromIDs($names, $all_parents);

            return $this->render('admin/view-user-success.html.twig', array(
                "user_name" => $names[$user_id],
                "all_parents" => $all_parents,
                "all_children" => $all_children,
                "immediate_parents" => $immediate_parents,
                "immediate_children" => $immediate_children,
            ));
        }

    }

}

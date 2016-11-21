<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller{    
    /**
     * Returns a list of all the users in the system
     * If the mode is set to true, it also sends the
     * lists of all the children and parents
     */
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


    /**
     * It returns a list of the objects (user names and IDs) of 
     * all the users passed in the function as a parameters
     */
    private function _getUserObjectsFromIDs($all_users, $users_array){
        $output_array = [];
        if(empty($users_array))
            return $output_array;

        foreach($users_array as $user_id){
            $random_num = rand(1,7);
            $temp_user = array(
                'id' => $user_id,
                'name' => $all_users[$user_id],
            );

            array_push($output_array, $temp_user);
        }
        return $output_array;
    }


    /**
     * breadth-first search algorithm
     * ref : https://www.khanacademy.org/computing/computer-science/algorithms/breadth-first-search/a/the-breadth-first-search-algorithm
     */
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

            if($curr_node != $root)
                array_push($ans, $curr_node);

            if(!is_null($adjacency_list[$curr_node]))
                foreach($adjacency_list[$curr_node] as $node)
                    if(!$visited[$node])
                        array_push($stack, $node);
        }
        sort($ans);
        $ans = array_unique($ans);        
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


        if(!$user_existence_flag){
            return $this->render('admin/view-user.html.twig', array(
                "user_success" => true,
                "info_success" => false,
                "message" => "Sorry, we could not find a user with ID : <strong>" . $user_id . "</strong>",
                "all_users" => $all_users_information,
            ));
        } else {
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


    /**
     * @Route("/edit-user", name="edituser")
     */
    public function addUserConnection(){
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();
        $all_users_information = $this->_getAllUsers($mode=true);
        $response_parameters = [];

        if($request->getMethod() == 'GET'){
            $response_parameters = array(
                "all_users" => $all_users_information,
            );
        } else {
            $new_parent_id = (int)$request->request->get('parent');
            $new_child_id = (int)$request->request->get('child');

            // check if the selected child parent pair is invalid or not
            if($new_parent_id == $new_child_id){
                $response_parameters = array(
                    "all_users" => $all_users_information,
                    "success" => false,
                    "message" => "You cannot add same user as both parent and child",
                );
            } else {
                $connection_exists = false;
                $parent_children = [];
                $child_parents = [];

                foreach($all_users_information as $node){
                    if($node['id'] == $new_child_id){
                        $child_parents = $node['parents'];
                    }

                    if($node['id'] == $new_parent_id){
                        $parent_children = $node['children'];
                        if(!is_null($parent_children))
                            if(in_array($new_child_id, $parent_children))
                                $connection_exists = true;
                    }
                }

                // check if the child and parent are already connected or not
                if($connection_exists){
                    $response_parameters = array(
                        "all_users" => $all_users_information,
                        "success" => false,
                        "message" => "The selected Child-Parent connection already exists",
                    );
                } else {
                    // insert new child for the parent node
                    if(is_null($parent_children)){
                        $parent_children = [$new_child_id];
                    } else {
                        array_push($parent_children, $new_child_id);
                        sort($parent_children);
                    }
                    $new_parent_user = $repository->find($new_parent_id);
                    $new_parent_user->setChildren(json_encode($parent_children));
                    $em->flush();
                    
                    // insert new parent for the child node
                    if(is_null($child_parents)){
                        $child_parents = [$new_parent_id];
                    } else {
                        array_push($child_parents, $new_parent_id);
                        sort($child_parents);
                    }
                    $new_child_user = $repository->find($new_child_id);
                    $new_child_user->setParents(json_encode($child_parents));
                    $em->flush();

                    $response_parameters = array(
                        "all_users" => $all_users_information,
                        "success" => true,
                        "message" => "The selected Child-Parent connection successfully added.",
                    );
                }
            }
        }
     
        return $this->render('admin/edit-user.html.twig', $response_parameters);
    }
}

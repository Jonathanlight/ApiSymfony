<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserController extends FOSRestController
{

    /**
     * @ApiDoc(
     *    description="Crée un lieu dans l'application",
     *    input={"class"=UserType::class, "name"=""},
     *    statusCodes = {
     *        200 = "Géneration des données avec succès",
     *        500 = "Error https"
     *    },
     *    responseMap={
     *         200 = {"class"=User::class, "groups"={"User"}},
     *         500 = { "class"=UserType::class, "form_errors"=true, "name" = ""}
     *    }
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"User"})
     * @Rest\Get("/user")
     */
    public function getAction()
    {
      $restresult = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        if ($restresult === null) {
          return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $restresult;
    }


    /**
      * @ApiDoc(
      *    description="Crée un lieu dans l'application",
      *    input={"class"=UserType::class, "name"=""},
      *    statusCodes = {
      *        200 = "Géneration des données avec succès",
      *        500 = "Error https"
      *    }
      * )
      *
      * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"User"})
      * @Rest\Get("/user/{id}")
     */
     public function idAction($id)
     {
       $singleresult = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
       if ($singleresult === null) {
         return new View("user not found", Response::HTTP_NOT_FOUND);
       }
       return $singleresult;
     }


    /**
     * @Rest\Post("/user")
     */
     public function postAction(Request $request)
     {
       $data = new User;
       $name = $request->get('name');
       $role = $request->get('role');

       if(empty($name) || empty($role)) {
         return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
       }

         $data->setName($name);
         $data->setRole($role);
         $em = $this->getDoctrine()->getManager();
         $em->persist($data);
         $em->flush();

         return new View("User Added Successfully", Response::HTTP_OK);

     }

     /**
     * @Rest\Put("/user/{id}")
     */
     public function updateAction($id,Request $request)
     {
       $data = new User;
       $name = $request->get('name');
       $role = $request->get('role');
       $sn = $this->getDoctrine()->getManager();
       $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
          if (empty($user)) {
             return new View("user not found", Response::HTTP_NOT_FOUND);
           }elseif(!empty($name) && !empty($role)){
             $user->setName($name);
             $user->setRole($role);
             $sn->flush();
             return new View("User Updated Successfully", Response::HTTP_OK);
           }elseif(empty($name) && !empty($role)){
             $user->setRole($role);
             $sn->flush();
             return new View("role Updated Successfully", Response::HTTP_OK);
          }elseif(!empty($name) && empty($role)){
           $user->setName($name);
           $sn->flush();
           return new View("User Name Updated Successfully", Response::HTTP_OK);
          }else{
            die($request);
           return new View("User name or role cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
         }

     }


    /**
     * @Rest\Delete("/user/{id}")
     */
     public function deleteAction($id)
     {
      $data = new User;
      $sn = $this->getDoctrine()->getManager();
      $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
      if (empty($user)) {
        return new View("user not found", Response::HTTP_NOT_FOUND);
       }else {
        $sn->remove($user);
        $sn->flush();
       }
      return new View("deleted successfully", Response::HTTP_OK);
     }


     /**
     * @Rest\Get("/datas")
     */
    public function getCurlAction()
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://reddit.com/r/gifs/top/.json?limit=10&sort=hot');
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $restresult = curl_exec($ch);
        curl_close($ch);

        echo "<pre>";
        var_dump($restresult);
        die();
        //echo curl_errno($ch) . '-' . curl_error($ch);

        curl_close($ch);
        if ($restresult === null) {
          return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $restresult;
    }



}

<?php

namespace StatusBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

class RestController extends FOSRestController
{
    /**
     * @author Leonardo Rotundo <leonardorotundo@gmail.com>
     * @Get("/api/status.{_format}", name="status_rest", options={ "method_prefix" = false })
     */
    public function getStatusAction(){
       $repository = $this->getDoctrine()->getRepository('StatusBundle:Status');

       $data = $repository->findAll();

       $view = $this->view($data, 202);

        return $this->handleView($view);
    }
    
    /**
     * 
     * @param type $id
     * @Get("/api/status/{id}.{_format}", name="status_rest_id", options={ "method_prefix" = false })
     */
    public function getStatusIdAction($id){
        $repository = $this->getDoctrine()->getRepository('StatusBundle:Status');
        $data = $repository->find($id);
        if(!$data){
            $datos =array("code"=>"400000","message"=>"status messge not found","link"=>"http://some.url/docs");
            $view = $this->view($datos, 404); 
            return $this->handleView($view);
        }
        $view = $this->view($data, 202);

        return $this->handleView($view);
    }
    
    /**
     * 
     * @param type $id
     * @Post("/api/status/delete/{id}.{_format}", name="status_rest_delete", options={ "method_prefix" = false })
     */
    public function deleteStatus($id){
        $repository = $this->getDoctrine()->getRepository('StatusBundle:Status');
        $data = $repository->find($id);
        if(!$data){
            $datos =array("code"=>"400000","message"=>"status messge not found","link"=>"http://some.url/docs");
            $view = $this->view($datos, 404); 
            return $this->handleView($view);
        }
        $message = \Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('leonardorotundoarg@gmail.com')
        ->setTo('leonardorotundo@gmail.com');
        $this->get('mailer')->send($message);
                
        $view = $this->view($data, 202);

        return $this->handleView($view);
        
    }
    
    
}

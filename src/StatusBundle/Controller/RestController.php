<?php

namespace StatusBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use StatusBundle\Form\StatusType;

class RestController extends FOSRestController
{
    /**
     * @author Leonardo Rotundo <leonardorotundo@gmail.com>
     * @Get("/api/status", name="status_rest")
     * @ApiDoc(
     *  resource=true,
     *  description="List all status registered",
     *  filters={
     *      {"name"="a-filter", "dataType"="integer"},
     *      {"name"="another-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}
     *  }
     * )
     */
    public function getStatusAction(){

       $repository = $this->getDoctrine()->getRepository('StatusBundle:Status');

       $data = $repository->findAll();

       $view = $this->view($data, 202);

       return $this->handleView($view);
    }
    
    /**
     * @Post("/api/status/create", name="status_rest_create")
     * @ApiDoc(
     *  resource=true,
     *  description="Create a status",
     *  input="StatusBundle\Form\StatusType",
     *  output="StatusBundle\Entity\Status"
     * )
     */
    public function createAction(\Symfony\Component\HttpFoundation\Request $request) {
        $entity = new \StatusBundle\Entity\Status();
        $form = $this->createForm("StatusBundle\Form\StatusType",$entity);
        $view = $this->view();
        $form->handleRequest($request);
        
        if($form->isValid()){
            
            $em = $this->getDoctrine()->getManager();
        
            $em->persist($entity);
            $em->flush();
            $view->setData($entity);
        }else{
            $view->setData($form);
        }
        return $this->handleView($view);
    }
        
    

    /**
     * 
     * @param type $id
     * @Get("/api/status/{id}.{_format}", name="status_rest_get")
     * * @ApiDoc(
     *  resource=true,
     *  description="Create a status by id",
     *  input="StatusBundle\Form\StatusType",
     *  output="StatusBundle\Entity\Status"
     * )
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
     * @Post("/api/status/delete/{id}.{_format}", name="status_rest_delete")
     * * @ApiDoc(
     *  resource=true,
     *  description="Delete a status",
     *  input="StatusBundle\Form\StatusType",
     *  output="StatusBundle\Entity\Status"
     * )
     */
    public function deleteStatus($id){
        $repository = $this->getDoctrine()->getManager()->getRepository('StatusBundle:Status');
        $data = $repository->find($id);
        if(!$data){
            $datos =array("code"=>"400000","message"=>"status messge not found","link"=>"http://some.url/docs");
            $view = $this->view($datos, 404); 
            return $this->handleView($view);
        }
        $message = \Swift_Message::newInstance()
        ->setSubject('Código de confirmación')
        ->setFrom('intrawayApi@gmail.com')
        ->setTo($data->getEmail())
        ->setBody('El código de confirmación es: '.$data->getCode());
        $this->get('mailer')->send($message);
                
        $view = $this->view($data, 202);

        return $this->handleView($view);
        
    }
    
    /**
     * 
     * @param type $code
     * @Get("/api/status/confirmation/{code}", name="status_rest_confirmation")
     * * @ApiDoc(
     *  resource=true,
     *  description="Confirm a code and Delete a status",
     *  input="StatusBundle\Form\StatusType",
     *  output="StatusBundle\Entity\Status"
     * )
     */
    public function confirmation($code){
        
        $repository = $this->getDoctrine()->getRepository('StatusBundle:Status');
        $entity = $repository->findOneBy(array('code'=>$code));
        
        if($entity == null){
            $datos =array("code"=>"400000","message"=>"status messge not found","link"=>"http://some.url/docs");
            $view = $this->view($datos,404);
            return $this->handleView($view);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        
        $view = $this->view(array("message"=>"The record has been deleted"), 202);

        return $this->handleView($view);
    }
    
    
    
    
}

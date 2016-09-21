<?php

namespace StatusBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\FOSRestBundle as Rest;

class RestController extends FOSRestController
{
    /**
     * @author Leonardo Rotundo <leonardorotundo@gmail.com>
     * @Route("/status",name="status")
     */
    public function getStatusAction(){
        
       $data = $this->getDoctrine();
       $data->getRepository("StatusBundle:Status")->findAll();
       $container = $this->container;
       $serializer = $container->get('jms_serializer');
$datos = $serializer->serialize($data, 'json');
var_dump($datos);die;
//$data = $serializer->deserialize($inputStr, $typeName, $format);
       
       $view = $this->view($datos,200)
            ->setTemplate("StatusBundle:Rest:getStatus.html.twig")
            ->setTemplateVar('status');
       
    return array("hello"=>"World");
        
    }
}

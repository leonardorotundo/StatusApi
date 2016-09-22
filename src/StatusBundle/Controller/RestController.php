<?php

namespace StatusBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\FOSRestBundle as Rest;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\Get;
use StatusBundle\Entity\Status;
use JMS\Serializer\SerializationContext;

class RestController extends FOSRestController
{
    /**
     * @author Leonardo Rotundo <leonardorotundo@gmail.com>
     * @Get("/api/status.{_format}", name="status_rest", options={ "method_prefix" = false })
     * @Annotations\View()
     */
    public function getStatusAction(){
       $object = $this->get('status.manager');

       $serializer = $this->get('jms_serializer');
       $data = $serializer->serialize($object,'json');

       $view = $this->view($data, 200)
            ->setTemplate("StatusBundle:Rest:getStatus.html.twig")
            ->setTemplateVar('status');

        return $this->handleView($view);
    }
    
    
}

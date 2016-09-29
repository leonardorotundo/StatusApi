<?php

namespace StatusBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use StatusBundle\Form\StatusType;
use \Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
class RestController extends FOSRestController
{
    /**
     * @author Leonardo Rotundo <leonardorotundo@gmail.com>
     * @Get("/status.{_format}", name="status_rest", defaults={"_format"="json"})
     * @ApiDoc(
     *  resource=true,
     *  description="List all status registered",
     *  filters={
     *      {"name"="p", "dataType"="integer"},
     *      {"name"="r", "dataType"="integer"},
     *      {"name"="q", "dataType"="string"},
     *  }
     * )
     * @QueryParam(name="p", default="1", description="Page of the overview.")
     * Will look for a page query parameter, ie. ?page=XX
     * If not passed it will be automatically be set to the default of "1"
     * If passed but doesn't match the requirement "\d+" it will be also be set to the default of "1"
     * Note that if the value matches the default then no validation is run.
     * So make sure the default value really matches your expectations.
     * 
     * @QueryParam(name="r", strict=true, default="2", description="Item count limit")
     * In some case you also want to have a strict requirements but accept a null value, this is possible
     * thanks to the nullable option.
     * If ?count= parameter is set, the requirements will be checked strictly, if not, the null value will be used.
     * If you set the strict parameter without a nullable option, this will result in an error if the parameter is
     * missing from the query.
     * 
     * @QueryParam(name="q" ,nullable=true, description="search")
     * 
     */
    public function getStatusAction(ParamFetcher $paramFetcher)
    {
        
        // ParamFetcher params can be dynamically added during runtime instead of only compile time annotations.
        $dynamicRequestParam = new RequestParam();
        $dynamicRequestParam->name = "dynamic_request";
        $dynamicRequestParam->requirements = "\d+";
        $paramFetcher->addParam($dynamicRequestParam);
        
        $dynamicQueryParam = new QueryParam();
        $dynamicQueryParam->name = "dynamic_query";
        $dynamicQueryParam->requirements="[a-z]+";
        $paramFetcher->addParam($dynamicQueryParam);
        
        $count = $paramFetcher->get('r');
        $page = $paramFetcher->get('p');
        $q = $paramFetcher->get('q');

        if(!is_numeric($count)){
            $view = $this->view(array('code'=>'400001','message'=>'invalid number of rows'), 400);
        } elseif(!is_numeric($page)) {
            $view = $this->view(array('code'=>'400001','message'=>'invalid number of pagination'), 400);
        } else {
            $repository = $this->getDoctrine()->getRepository('StatusBundle:Status');
            $data = $repository->findBy(!is_null($q) ? array('status'=>$q):array(),null,$count,$count * ($page - 1));
            
            if(!$data){
                $datos =array("code"=>"400000","message"=>"status messge not found","link"=>"http://some.url/docs");
                $view = $this->view($datos, 404); 
            } else {
                $view = $this->view(array($data), 200);
            }
        }
            return $this->handleView($view);
    }
    
    /**
     * @Post("/create.{_format}", name="status_rest_create", defaults={"_format"="json"})
     * @ApiDoc(
     *  resource=true,
     *  description="Create a status",
     *  input="StatusBundle\Form\StatusType",
     *  output="StatusBundle\Entity\Status"
     * )
     */
    public function createAction(Request $request)
    {
        $entity = new \StatusBundle\Entity\Status();
        $form = $this->createForm("StatusBundle\Form\StatusType",$entity);
        $view = $this->view();
        $form->handleRequest($request);
        
        if($form->isValid()){
            
            $em = $this->getDoctrine()->getManager();
        
            $em->persist($entity);
            $em->flush();
            $view = $this->view($entity, 200); 
        } else {
            $view->setData($form);
        }
        return $this->handleView($view);
    }
        
    

    /**
     * 
     * @param type $id
     * @Get("{id}.{_format}", name="status_rest_get", defaults={"_format"="json"})
     * * @ApiDoc(
     *  resource=true,
     *  description="Create a status by id",
     *  input="StatusBundle\Form\StatusType",
     *  output="StatusBundle\Entity\Status"
     * )
     */
    public function getStatusIdAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('StatusBundle:Status');
        $data = $repository->find($id);
        if(!$data){
            $datos =array("code"=>"400000","message"=>"status messge not found","link"=>"http://some.url/docs");
            $view = $this->view($datos, 404); 
            return $this->handleView($view);
        }
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
    
    /**
     * 
     * @param type $id
     * @Post("/delete/{id}.{_format}", name="status_rest_delete", defaults={"_format"="json"})
     * * @ApiDoc(
     *  resource=true,
     *  description="Delete a status",
     *  input="StatusBundle\Form\StatusType",
     *  output="StatusBundle\Entity\Status"
     * )
     */
    public function deleteStatus($id)
    {
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
                
        $view = $this->view($data, 200);

        return $this->handleView($view);
        
    }
    
    /**
     * 
     * @param type $code
     * @Delete("/confirmation/{code}.{_format}", name="status_rest_confirmation", defaults={"_format"="json"})
     * * @ApiDoc(
     *  resource=true,
     *  description="Confirm a code and Delete a status",
     *  input="StatusBundle\Form\StatusType",
     *  output="StatusBundle\Entity\Status"
     * )
     */
    public function confirmation($code)
    {
        
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
        
        $view = $this->view(array("message"=>"The record has been deleted"), 200);

        return $this->handleView($view);
    }
    
}

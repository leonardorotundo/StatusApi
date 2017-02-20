<?php

namespace StatusBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/**
 * @author Leonardo Rotundo <leonardorotundo@gmail.com>
 */
class StatusControllerTest extends WebTestCase
{
    public function testStatusList()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/status');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testStatusCreate()
    {
        $client = static::createClient();
        $mail = $client->getContainer()->getParameter('mailer_user');
        $crawler = $client->request('POST', '/create',
                array(
                    'status'=>array(
                        'status'=>'Mensaje de estatus de prueba'
                        ,'email'=>  is_null($mail) ? null: $mail
                                )
                    )
                );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testStatusDelete()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/delete/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    
    public function testStatusConfirmation()
    {
        $client = static::createClient();
        $crawler = $client->request('DELETE', '/confirmation/40091320');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
    }
    
}

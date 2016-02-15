<?php

namespace SuperShoesBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

class ArticleControllerTest extends WebTestCase
{
    public function testGetArticles()
    {
        $client = $this->getClient(true);
        // head request
        $client->request('HEAD', '/articles.json');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        // empty list
        $client->request('GET', '/articles.json');
        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $this->assertEquals('{"notes":[],"limit":5,"_links":{"self":{"href":"http:\/\/localhost\/notes"},"note":{"href":"http:\/\/localhost\/notes\/{id}","templated":true}}}', $response->getContent());
        // list
        $this->createNote($client, 'my note for list');
        $client->request('GET', '/notes.json');
        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $contentWithoutSecret = preg_replace('/"secret":"[^"]*"/', '"secret":"XXX"', $response->getContent());
        $this->assertEquals('{"notes":[{"secret":"XXX","message":"my note for list","version":"1","_links":{"self":{"href":"http:\/\/localhost\/notes\/0"}}}],"limit":5,"_links":{"self":{"href":"http:\/\/localhost\/notes"},"note":{"href":"http:\/\/localhost\/notes\/{id}","templated":true}}}', $contentWithoutSecret);
    }

    public function testGetNote()
    {
        $client = $this->getClient(true);
        $client->request('GET', '/notes/0.json');
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode(), $response->getContent());
        $this->assertEquals('{"code":404,"message":"Note does not exist."}', $response->getContent());
        $this->createNote($client, 'my note for get');
        $client->request('GET', '/notes/0.json');
        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $contentWithoutSecret = preg_replace('/"secret":"[^"]*"/', '"secret":"XXX"', $response->getContent());
        $this->assertEquals('{"secret":"XXX","message":"my note for get","version":"1","_links":{"self":{"href":"http:\/\/localhost\/notes\/0"}}}', $contentWithoutSecret);
        $client->request('GET', '/notes/0', array(), array(), array('HTTP_ACCEPT' => 'application/json'));
        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $contentWithoutSecret = preg_replace('/"secret":"[^"]*"/', '"secret":"XXX"', $response->getContent());
        $this->assertEquals('{"secret":"XXX","message":"my note for get","version":"1","_links":{"self":{"href":"http:\/\/localhost\/notes\/0"}}}', $contentWithoutSecret);
    }
}

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
}

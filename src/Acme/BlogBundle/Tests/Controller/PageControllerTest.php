<?php

namespace Acme\BlogBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;
use Acme\BlogBundle\Tests\Fixtures\Entity\LoadPageData;

class PageControllerTest extends WebTestCase
{
    public function customSetUp($fixtures)
    {
        $this->client = static::createClient();
        $this->loadFixtures($fixtures);
    }

    public function testJsonGetPageAction()
    {
        $fixtures = array('Acme\BlogBundle\Tests\Fixtures\Entity\LoadPageData');
        $this->customSetUp($fixtures);
        $pages = LoadPageData::$pages;
        $page = array_pop($pages);

        $route =  $this->getUrl('api_1_get_page', array('id' => $page->getId(), '_format' => 'json'));

        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['id']));
    }

    public function testHeadRoute()
    {
        $fixtures = array('Acme\BlogBundle\Tests\Fixtures\Entity\LoadPageData');
        $this->customSetUp($fixtures);
        $pages = LoadPageData::$pages;
        $page = array_pop($pages);

        $this->client->request('HEAD',  sprintf('/api/v1/pages/%d.json', $page->getId()), array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200, false);
    }

    public function testJsonNewPageAction()
    {
        $this->client = static::createClient();
        $this->client->request(
            'GET',
            '/api/v1/pages/new.json',
            array(),
            array()
        );

        $this->assertJsonResponse($this->client->getResponse(), 200, true);
        $this->assertEquals(
            '{"children":{"title":[],"body":[]}}',
            $this->client->getResponse()->getContent(),
            $this->client->getResponse()->getContent());
    }

    public function testJsonPostPageAction()
    {
        $this->client = static::createClient();
        $this->client->request(
            'POST',
            '/api/v1/pages.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"title":"title1","body":"body1"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    public function testJsonPostPageActionShouldReturn400WithBadParameters()
    {
        $this->client = static::createClient();
        $this->client->request(
            'POST',
            '/api/v1/pages.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"titles":"title1","bodys":"body1"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 400, false);
    }

    public function testJsonPutPageActionShouldModify()
    {
        $fixtures = array('Acme\BlogBundle\Tests\Fixtures\Entity\LoadPageData');
        $this->customSetUp($fixtures);
        $pages = LoadPageData::$pages;
        $page = array_pop($pages);

        $this->client = static::createClient();
        $this->client->request(
            'PUT',
            sprintf('/api/v1/pages/%d.json', $page->getId()),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"title":"abc","body":"def"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 204, false);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Location',
                sprintf('http://localhost/api/v1/pages/%d.json', $page->getId())
            ),
            $this->client->getResponse()->headers
        );
    }

    public function testJsonPutPageActionShouldCreate()
    {
        $this->client = static::createClient();
        $id = 0;

        $this->client->request('GET', sprintf('/api/v1/pages/%d.json', $id), array('ACCEPT' => 'application/json'));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());

        $this->client->request(
            'PUT',
            sprintf('/api/v1/pages/%d.json', $id),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"title":"abc","body":"def"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    public function testJsonPatchPageAction()
    {
        $fixtures = array('Acme\BlogBundle\Tests\Fixtures\Entity\LoadPageData');
        $this->customSetUp($fixtures);
        $pages = LoadPageData::$pages;
        $page = array_pop($pages);

        $this->client = static::createClient();
        $this->client->request(
            'PATCH',
            sprintf('/api/v1/pages/%d.json', $page->getId()),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"body":"def"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 204, false);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Location',
                sprintf('http://localhost/api/v1/pages/%d.json', $page->getId())
            ),
            $this->client->getResponse()->headers
        );
    }





    protected function assertJsonResponse($response, $statusCode = 200, $checkValidJson =  true, $contentType = 'application/json')
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', $contentType),
            $response->headers
        );

        if ($checkValidJson) {
            $decode = json_decode($response->getContent());
            $this->assertTrue(($decode != null && $decode != false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
    }
}

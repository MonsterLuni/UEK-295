<?php

namespace App\Tests;

use App\DTO\CreateUpdateStory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class StoryControllerTest extends WebTestCase
{
    protected static $application;
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client([
            "base_uri" => "http://localhost:8000"
        ]);

        $client = self::createClient();
        self::$application = new Application($client->getKernel());
        self::$application->setAutoExit(false);

        self::$application->run(new StringInput("doctrine:database:drop --if-exists --force"));
        self::$application->run(new StringInput("doctrine:database:create --quiet"));
        self::$application->run(new StringInput("doctrine:schema:create"));
        self::$application->run(new StringInput("doctrine:fixtures:load"));
    }
    //erkennt es dadurch, dass es mit test anfängt
    public function testGet(){
        $request = self::$client->request("GET","/api/story");
        $this->assertTrue($request->getStatusCode() == 200);
    }
    public function testPost(){
        $dto = new CreateUpdateStory();
        $dto->title = "";
        $dto->author = "Luca Moser";
        $dto->storie = "Die mutter ist Schlimm";
        /*
        //$this->expectException(ClientException::class);

        //build request for post method
        $request = self::$client->request("POST","/api/story",[
            "body" => json_encode($dto)
        ]);

        //get response for post method
        $response = json_decode($request->getBody());

        //assert methods for actual test case
        $this->assertTrue($request->getStatusCode() == 200);
        $this->assertTrue($response->author == "Luca Moser");
        */


        $response = null;
        try {
            $request = self::$client->request("POST","/api/story",[
                "body" => json_encode($dto)
            ]);
        }
        catch (ClientException $ex){
            $response = $ex->getResponse();
        }

        $responseBody = json_decode($response->getBody());

        $this->assertTrue($response->getStatusCode() == 400);
        $this->assertContains("Titel darf nicht leer sein.",$responseBody);

    }

    public function testDelete(){
        $id = 1;

        $request = self::$client->request("DELETE","api/story/6");

        $responseBody = json_decode($request->getBody());

        dump($responseBody);

        $this->assertContains("Story with ID 6 Succesfully Deleted", $responseBody);
    }

    public function testSomething(): void
    {
        $this->assertTrue(true);
    }
}

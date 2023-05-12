<?php

namespace App\Tests;

use App\DTO\CreateUpdateStory;
use App\Entity\Story;
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
        self::$application->run(new StringInput("doctrine:fixtures:load --group=fakedata"));
    }
    //erkennt es dadurch, dass es mit test anfängt
    public function testGet(){
        $request = self::$client->request("GET","/index_test.php/api/story");
        $this->assertTrue($request->getStatusCode() == 200);
    }
    public function testPost(){
        $dto = new CreateUpdateStory();
        $dto->title = "";
        $dto->author = "Luca Moser";
        $dto->storie = "Die mutter ist Schlimm";

        $response = null;
        try {
            $request = self::$client->request("POST","/index_test.php/api/story",[
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
        $request = self::$client->request("DELETE","/index_test.php/api/story/1");

        $responseBody = json_decode($request->getBody());

        $this->assertTrue($responseBody == "Story with ID 1 Succesfully Deleted");
    }
    public function testUpdate(){
        $dto = new Story();
        $dto->setTitle("NeuerTitel");
        $dto->setstorie("NeueStorie");

        $request = self::$client->request("PUT", "/index_test.php/api/story/2",[
            "body" => json_encode($dto)
        ]);

        $response = json_decode($request->getBody());

        $this->assertTrue($response == "Story with ID 2 Succesfully Changed");
    }
}

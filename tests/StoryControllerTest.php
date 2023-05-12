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

        $requestLogin = self::$client->request("POST","/index_test.php/api/login_check",[
            "body" => json_encode([
                "username" => "TestUser",
                "password" => "1234"
            ])
        ]);
        global $token;
        $token = json_decode($requestLogin->getBody())->token;

        $request = self::$client->request("GET","/index_test.php/api/story",[
            "headers" => [
                "Authorization" => "Bearer " . $token
            ]
        ]);
        $this->assertTrue($request->getStatusCode() == 200);
    }
    public function testPost(){
        global $token;
        $dto = new CreateUpdateStory();
        $dto->title = "";
        $dto->author = "Luca Moser";
        $dto->storie = "Die mutter ist Schlimm";

        $response = null;
        try {
            $request = self::$client->request("POST","/index_test.php/api/story",[
                "body" => json_encode($dto),
                "headers" => [
                "Authorization" => "Bearer " . $token
            ]
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
        global $token;
        $request = self::$client->request("DELETE","/index_test.php/api/story/1",[
            "headers" => [
                "Authorization" => "Bearer " . $token
            ]
        ]);

        $responseBody = json_decode($request->getBody());

        $this->assertTrue($responseBody == "Story with ID 1 Succesfully Deleted");
    }
    public function testUpdate(){
        global $token;
        $dto = new Story();
        $dto->setTitle("NeuerTitel");
        $dto->setstorie("NeueStorie");

        $request = self::$client->request("PUT", "/index_test.php/api/story/2",[
            "body" => json_encode($dto),
            "headers" => [
                "Authorization" => "Bearer " . $token
            ]
        ]);

        $response = json_decode($request->getBody());

        $this->assertTrue($response == "FilterMethode hat keine Fehler entdeckt");
    }
}

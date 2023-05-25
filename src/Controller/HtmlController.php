<?php

namespace App\Controller;

use App\DTO\FilterStory;
use App\Entity\User;
use App\Repository\StoryRepository;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/ArchiveOfMyself', name: 'ArchiveOfMyself_')]
class HtmlController extends AbstractController
{
    #[Rest\Get('/home', name: 'twig_home')]
    public function home(StoryRepository $repository, Request $request, SerializerInterface $serializer, LoggerInterface $logger): Response
    {
        $name = 'Luca Moser';
        $filter = new FilterStory();
        try {
            $filter->author = $request->get('author');
            $filter->likes = intval($request->get('likes'));
            $filter->dislikes = intval($request->get('dislikes'));
            if (null != $request->get('sortby')) {
                $filter->orderby = $request->get('sortby');
                if ('on' == $request->get('sortdirection')) {
                    $filter->orderdirection = 'DESC';
                }
            }
        } catch (\Exception $ex) {
            $dtoFilter = new FilterStory();
        }
        $kommentar = $repository->filterAll($filter);
        if (null == $kommentar) {
            return $this->render('html/index.html.twig', [
                'message' => 'Es wurden keine Storys mit diesem Filter gefunden',
                'storys' => $kommentar,
                'user_name' => $name,
            ]);
        } else {
            return $this->render('html/index.html.twig', [
                'message' => 'Es wurden '.count($kommentar).' Storys gefunden',
                'storys' => $kommentar,
                'user_name' => $name,
            ]);
        }
    }

    #[Rest\Get('/register', name: 'twig_register')]
    public function register(Request $request, UserRepository $repository, UserPasswordHasherInterface $passwordHasher): Response
    {

        if($request->get('username') != null){
            $user = new User();
            $user->setUsername($request->get('username'));
            $password = $passwordHasher->hashPassword($user, $request->get('password'));
            $user->setPassword($password);

            $repository->save($user, true);

            return $this->redirect('http://127.0.0.1:8000/ArchiveOfMyself/register');
        }
        else{
            return $this->render('html/register.html.twig', []);
        }
    }

    #[Rest\Get('/login', name: 'twig_login')]
    public function login(Request $request): Response
    {
        if($request->get('username_r') != null){
            $user = new User();
            $user->setUsername($request->get('username_l'));
            $user->setPassword($request->get('password_l'));

            $client = new Client();
            $response = $client->post("http://localhost:8000/api/login_check",
                [
                    "json" => json_encode(
                        [
                            "username" => $user->getUsername(),
                            "password" => $user->getPassword()
                        ]),
                    'timeout' => 10
                ]
            );
            return $this->redirect('http://127.0.0.1:8000/ArchiveOfMyself/home');
        }
        else{
            return $this->render('html/login.html.twig');
        }
    }
}

<?php

namespace App\Controller;

use App\Repository\StoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

#[Route("/ArchiveOfMyself", name: "ArchiveOfMyself_")]
class HtmlController extends AbstractController
{
    #[Rest\Get('/home', name: 'twig_home')]
    public function home(StoryRepository $repository) : Response
    {
        $name = "Luca Moser";

        $kommentar = $repository->findAll();

        return $this->render('html/index.html.twig', [
            'storys' => $kommentar,
            'user_name' => $name,
        ]);
    }

    #[Rest\Get('/login', name: 'twig_html')]
    public function login(StoryRepository $repository) : Response
    {
        $name = "Luca Moser";

        $kommentar = $repository->findAll();

        return $this->render('html/index.html.twig', [
            'storys' => $kommentar,
            'user_name' => $name,
        ]);
    }
}

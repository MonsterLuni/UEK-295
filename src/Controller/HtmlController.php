<?php

namespace App\Controller;

use App\Repository\StoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

#[Route("/api", name: "api_")]
class HtmlController extends AbstractController
{
    #[Rest\Get('/html', name: 'twig_html')]
    public function html(StoryRepository $repository) : Response
    {
        $name = "Luca Moser";

        $kommentar = $repository->findAll();

        return $this->render('html/index.html.twig', [
            'storys' => $kommentar,
            'user_name' => $name,
        ]);
    }

    public function test(string $nummer){
        return $nummer;
}
}

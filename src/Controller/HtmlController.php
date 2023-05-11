<?php

namespace App\Controller;

use App\DTO\FilterStory;
use App\Repository\StoryRepository;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

#[Route("/ArchiveOfMyself", name: "ArchiveOfMyself_")]
class HtmlController extends AbstractController
{
    #[Rest\Get('/home', name: 'twig_home')]
    public function home(StoryRepository $repository, Request $request, SerializerInterface $serializer, LoggerInterface $logger) : Response
    {
        $name = "Luca Moser";
        $filter = new FilterStory();
        try {
             $filter->author = $request->get("author");
             $filter->likes = intval($request->get("likes"));
             $filter->dislikes = intval($request->get("dislikes"));
             if(!($request->get("sortby") == null)){
                 $filter->orderby = $request->get("sortby");
                 if($request->get("sortdirection") == "on"){
                     $filter->orderdirection = "DESC";
                 }

             }


        }
        catch(\Exception $ex){
            $dtoFilter = new FilterStory();
        }
        $kommentar = $repository->filterAll($filter);

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

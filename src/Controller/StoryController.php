<?php

namespace App\Controller;

use App\DTO\CreateUpdateStory;
use App\DTO\FilterStory;
use App\DTO\Mapper\ShowStoryMapper;
use App\Entity\Story;
use App\Repository\StoryRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api", name: "api_")]
class StoryController extends AbstractFOSRestController
{

    public function __construct(private SerializerInterface $serializer,
                                private StoryRepository $repository,
                                private ShowStoryMapper $mapper){}

    #[Rest\Get('/story', name: 'app_story_get')]
    public function story_get(Request $request): JsonResponse
    {
        $dtoFilter = null;

        try {
            $dtoFilter = $this->serializer->deserialize($request->getContent(), FilterStory::class, "json"
            );
        }
        catch(\Exception $ex){
        $dtoFilter = new FilterStory();
    }


        $story = $this->repository->filterAll($dtoFilter) ?? [];

        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntitiesToDTOs($story), "json"
            )
        );
    }
    #[Rest\Post('/story', name: 'app_story_create')]
    public function story_create(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateStory::class, "json");

        $entity = new Story();
        $entity->setTitle($dto->title);
        $entity->setstorie($dto->storie);
        $entity->setLikes($dto->likes);
        $entity->setDislikes($dto->dislikes);
        $entity->setAuthor($dto->author);

        $this->repository->save($entity, true);

        return (new JsonResponse())->setContent(
            $this->serializer->serialize($this->mapper->mapEntityToDTO($entity),"json")
        );
    }
    #[Rest\Delete('/story/{id}', name: 'app_story_delete')]
    public function story_delete(Request $request, $id): JsonResponse
    {
        $entitystory = $this->repository->find($id);
        if(!$entitystory) {
            return $this->json("Haustier mit ID {$id} existiert nicht!", status: 403);
        }
        $this->repository->remove($entitystory, true);
        return $this->json("Story with ID " . $id . " Succesfully Deleted");
    }
    #[Rest\Put('/story/{id}', name: 'app_story_update')]
    public function story_update(Request $request, $id): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateStory::class, "json");
        $entitystory = $this->repository->find($id);

        $entitystory->setTitle($dto->title);
        $entitystory->setstorie($dto->storie);
        $entitystory->setLikes($dto->likes);
        $entitystory->setDislikes($dto->dislikes);
        $entitystory->setAuthor($dto->author);

        if(!$entitystory) {
            return $this->json("Story with ID " . $id . " doesn't exist!", status: 403);
        }
        $this->repository->save($entitystory, true);
        return $this->json("Story with ID " . $id . " Succesfully Changed");
    }
}

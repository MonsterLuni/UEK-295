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
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api", name: "api_")]
class StoryController extends AbstractFOSRestController
{

    public function __construct(private SerializerInterface $serializer,
                                private StoryRepository $repository,
                                private ShowStoryMapper $mapper,
                                private ValidatorInterface $validator){}

    /**
     * Validates stuff
     * @param $dto. the dto gets validated.
     * @return JsonResponse returns an array of error messages (can be empty)
     */
    private function validateDTO($dto, $groups = ["create"]) : JsonResponse{
        $errors = $this->validator->validate($dto, groups: $groups);

        if($errors->count() > 0){
            $errorStringArray = [];
            foreach($errors as $error){
                $errorStringArray[] = $error->getMessage();
            }
            return $this->json($errorStringArray, status: 400);
        }
    }

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


        //$story = $this->repository->filterAll($dtoFilter) ?? [];
        $story = $this->repository->filterAuthor($dtoFilter) ?? [];

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

        //Zum Validieren vom $dto in der Datei CreateUpdateStory.php (bezieht sich auf Function validateDTO)
        $errorResponse = $this->validateDTO($dto, ["create"]);
        if($errorResponse){return $errorResponse;}

        //Kreirt ein objekt Story als $entity, und füllt es dann mit dem $dto
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
            return $this->json("Story with ID {$id} does not exist!", status: 403);
        }
        $this->repository->remove($entitystory, true);
        return $this->json("Story with ID " . $id . " Succesfully Deleted");
    }
    #[Rest\Put('/story/{id}', name: 'app_story_update')]
    public function story_update(Request $request, $id): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateStory::class, "json");
        $entitystory = $this->repository->find($id);

        if(!$entitystory) {
            return $this->json("Story with ID " . $id . " does not exist! ", status: 403);
        }

        //Zum Validieren vom $dto in der Datei CreateUpdateStory.php (bezieht sich auf Function validateDTO)
        $errorResponse = $this->validateDTO($dto, ["create"]);
        if($errorResponse){return $errorResponse;}

        $entitystory->setTitle($dto->title);
        $entitystory->setstorie($dto->storie);
        $entitystory->setLikes($dto->likes);
        $entitystory->setDislikes($dto->dislikes);
        //Checkt ob im Author etwas drin ist, wenn nicht ändert es ihn nicht (unnötig deswegen auskommentiert)
        //if($dto->author){
        //   $entitystory->setAuthor($dto->author);
        //}

        $this->repository->save($entitystory, true);
        return $this->json("Story with ID " . $id . " Succesfully Changed");
    }
}

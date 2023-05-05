<?php

namespace App\Controller;

use App\DTO\CreateUpdateStory;
use App\DTO\FilterStory;
use App\DTO\Mapper\ShowStoryMapper;
use App\DTO\ShowStory;
use App\Entity\Story;
use App\Repository\StoryRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
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
    private function validateDTO($dto, $groups = ["create"]){
        $errors = $this->validator->validate($dto, groups: $groups);

        if($errors->count() > 0){
            $errorStringArray = [];
            foreach($errors as $error){
                $errorStringArray[] = $error->getMessage();
            }
            return $this->json($errorStringArray, status: 400);
        }
    }
    #[Get(requestBody: new RequestBody(
        content: new JsonContent(
            ref: new Model(
                type: FilterStory::class
            )
        )
    ))]
    #[Response(
        response: 200,
        description: "Wenn man die Felder leer lässt, gibt es alle Storys an, man kann Filter hinzufügen wie: mindestanzahl Likes/Dislikes und nach dem Autor Filtern.",
        content: new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: ShowStory::class)
            )
        )
    )]
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

            if($story == []){
                return $this->json("Keine Storys mit diesem Filter gefunden!");
            }
            else{
                return (new JsonResponse())->setContent(
                    $this->serializer->serialize(
                        $this->mapper->mapEntitiesToDTOs($story), "json"
                    )
                );
            }

    }
    #[Post(
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(
                    type: CreateUpdateStory::class,
                    groups: ["create"]
                )
            )
        )
    )]
    #[Response(
        response: 200,
        description: "Man kann alles Kreieren ausser Likes&Dislikes",
        content: new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: ShowStory::class)
            )
        )
    )]
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

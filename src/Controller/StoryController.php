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
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * For POST with the Entity Story
 */
#[Route("/api", name: "api_")]
class StoryController extends AbstractFOSRestController
{

    public function __construct(private SerializerInterface $serializer,
                                private StoryRepository $repository,
                                private ShowStoryMapper $mapper,
                                private ValidatorInterface $validator,
                                private LoggerInterface $logger){}

    /**
     * Validates a dto
     * @param $dto. the dto gets validated.
     * @param string[] $groups. Used to choose which Validation should be used.
     * @return JsonResponse returns an array of error messages (can be empty)
     */
    private function validateDTO($dto, $groups = ["create"])
    {
        $this->logger->info("Validate Methode für Story wurde aufgerufen");
        $errors = $this->validator->validate($dto, groups: $groups);

        if ($errors->count() > 0) {
            $errorStringArray = [];
            foreach ($errors as $error) {
                $errorStringArray[] = $error->getMessage();
            }
            $this->logger->info("Filtermethode für Story hat {errors} Fehler gefunden", ['errors' => $errors->count()]);
            $this->logger->debug("Die Fehler sind: {fehler}", ['fehler' => $errorStringArray]);
            return $this->json($errorStringArray, status: 400);
        }
        $this->logger->info("Filtermethode für Story hat keine Fehler entdeckt");
        return $this->json("FilterMethode hat keine Fehler entdeckt");
    }
    /**
     * Shows all Storys and the related comments that correspond to the Filters set
     * @param Request $request. allows to set Filters (available Filters are Likes,Dislikes and Author)
     * @return JsonResponse Returns all Storys + comments that have been found with the Filter
     */
    #[Get(requestBody: new RequestBody(
        content: new JsonContent(
            ref: new Model(
                type: FilterStory::class
            )
        )
    ))]
    #[Response(
        response: 200,
        description: "Can give out every Story. When you want to see every Story or don't want to filter anything, you can just keep the Request-Body empty",
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
        $this->logger->info("GET Methode für Story wurde aufgerufen");
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

    /**
     * To Create a new Story.
     * @param Request $request. Used to set the specific values of Title,Storie,Author,Likes and Dislikes
     * @return JsonResponse. Returns the newly created entry.
     */
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
        description: "If the Request-Body is valid you get your Story as the Response, else you get an Error-message",
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
        $this->logger->info("POST Methode für Story wurde aufgerufen");
        //Deserializiert den Requestbody im Typ der Klasse CreateUpdateStory, welches im Format "json" ist. Dies speichert es dann in eine VAriable die $dto heisst
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateStory::class, "json");

        //Zum Validieren vom $dto in der Datei CreateUpdateStory.php (bezieht sich auf Function validateDTO)
        $errorResponse = $this->validateDTO($dto, ["create"]);
        if($errorResponse){return $errorResponse;}

        //Kreirt eine iteration des objektes Story als $entity, und füllt es dann mit dem $dto
        $entity = new Story();
        $entity->setTitle($dto->title);
        $entity->setstorie($dto->storie);
        $entity->setAuthor($dto->author);
        $entity->setLikes($dto->likes);
        $entity->setDislikes($dto->dislikes);

        $this->repository->save($entity, true);

        return (new JsonResponse())->setContent(
            $this->serializer->serialize($this->mapper->mapEntityToDTO($entity),"json")
        );
    }

    /**
     * To Delete a Story
     * @param $id. to specify which entry should be deleted
     * @return JsonResponse. Says if the Deletion was successful or not
     */
    #[Delete(
        description: "To Delete an Entry via ID in url"
    )]
    #[Response(
        response: 200,
        description: "You will see an Response Message if the Story with id (id) was successfully deleted or not.",
        content: new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: JsonResponse::class)
            )
        )
    )]
    #[Rest\Delete('/story/{id}', name: 'app_story_delete')]
    public function story_delete($id): JsonResponse
    {
        $this->logger->info("DELETE Methode für Story wurde aufgerufen");
        $entitystory = $this->repository->find($id);
        if(!$entitystory) {
            return $this->json("Story with ID {$id} does not exist!", status: 403);
        }
        $this->repository->remove($entitystory, true);
        return $this->json("Story with ID " . $id . " Succesfully Deleted");
    }

    /**
     * To Change a Story
     * @param Request $request. You can Change Title, Storie.
     * @param $id. To Choose witch Entry you would like to change.
     * @return JsonResponse. Returns the Updated Entry.
     */
    #[Put(
        description: "To Change the Title,Storie of an Entry via the ID in the url",
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(
                    type: CreateUpdateStory::class,
                    groups: ["update"]
                )
            )
        )
    )]
    #[Response(
        response: 200,
        description: "If the Request-Body & the ID in the url is valid you get the message, that the change on your entity is complete, else you get an Error-message",
        content: new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: JsonContent::class)
            )
        )
    )]
    #[Rest\Put('/story/{id}', name: 'app_story_update')]
    public function story_update(Request $request, $id): JsonResponse
    {
        $this->logger->info("PUT Methode für Story wurde aufgerufen");
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateStory::class, "json");
        $entitystory = $this->repository->find($id);

        if(!$entitystory) {
            return $this->json("Story with ID " . $id . " does not exist! ", status: 403);
        }

        //Zum Validieren vom $dto in der Datei CreateUpdateStory.php (bezieht sich auf Function validateDTO)
        $errorResponse = $this->validateDTO($dto, ["update"]);
        if($errorResponse){return $errorResponse;}
        if ($dto->title == null){
            if($dto->likes == 0 && $dto->dislikes == 0){
                $entitystory->setLikes($entitystory->getLikes()+1);
            }
            else{
                $entitystory->setDislikes($entitystory->getDislikes()+1);
            }
        }
        else{
            $entitystory->setTitle($dto->title);
            $entitystory->setstorie($dto->storie);
        }


        //Checkt ob im Author etwas drin ist, wenn nicht ändert es ihn nicht (unnötig deswegen auskommentiert)
        //if($dto->author){
        //   $entitystory->setAuthor($dto->author);
        //}

        $this->repository->save($entitystory, true);
        return $this->json("Story with ID " . $id . " Succesfully Changed");
    }
}

<?php

namespace App\Controller;

use App\DTO\CreateUpdateComment;
use App\DTO\FilterStory;
use App\DTO\Mapper\ShowCommentMapper;
use App\DTO\ShowComment;
use App\DTO\ShowStory;
use App\Entity\Comments;
use App\Repository\CommentsRepository;
use App\Repository\StoryRepository;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api", name: "api_")]
class CommentController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer,
                                private CommentsRepository $repository,
                                private StoryRepository $srepository,
                                private ShowCommentMapper $mapper,
                                private ValidatorInterface $validator){}

    private function validateDTO($dto, $groups = ["create"]){
        $errors = $this->validator->validate($dto, groups: $groups);

        if($errors->count() > 0){
            $errorStringArray = [];
            foreach($errors as $error){
                $errorStringArray[] = $error->getMessage();
            }
            return $this->json($errorStringArray, status: 400);
        }
        return null;
    }

    /**
     * Shows all Comments
     * @return JsonResponse. Returns all Comments there are
     */
    #[Get(
        requestBody: new RequestBody(
        content: new JsonContent(
            ref: new Model(
                type: FilterStory::class
            )
        )
    ))]
    #[Response(
        response: 200,
        description: "Gives out Every Comment there is.",
        content: new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: ShowComment::class)
            )
        )
    )]
    #[Rest\Get('/comment', name: 'app_comment_get')]
    public function comment_get(): JsonResponse
    {
        $comment = $this->repository->findAll();

        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntitiesToDTOs($comment), "json"
            )
        );
    }

    /**
     * To create a new Comment
     * @param Request $request. To declare the Text,Likes,Dislikes of a Comment.
     * @return JsonResponse. Returns the newly created Comment
     */
    #[Post(
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(
                    type: FilterStory::class
                )
            )
        ))]
    #[Response(
        response: 200,
        description: "Gives out the newly created Comment",
        content: new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: ShowComment::class)
            )
        )
    )]
    #[Rest\Post('/comment', name: 'app_comment_create')]
    public function comment_create(Request $request, StoryRepository $storyrepository): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateComment::class, "json");

        $story = $this->srepository->find($dto->refstory);

        //Zum Validieren vom $dto in der Datei CreateUpdateComment.php (bezieht sich auf Function validateDTO)
        $errorResponse = $this->validateDTO($dto, ["create"]);
        if($errorResponse){return $errorResponse;}

        //Hier checke ich, ob es eine Story mit der gewünschten ID gibt, wenn nicht wird es sofort abgebrochen, da es sont einen Fehler gibt
        $entitystory = $storyrepository->find($dto->refstory);

        $entity = new Comments();
        $entity->setRefstory($entitystory);
        $entity->setText($dto->text);
        $entity->setLikes($dto->likes);
        $entity->setDislikes($dto->dislikes);

        $this->repository->save($entity, true);

        return (new JsonResponse())->setContent(
            $this->serializer->serialize($this->mapper->mapEntityToDTO($entity),"json")
        );
    }

    /**
     * To Delete a Comment
     * @param $id. To find the Specific entry that is searched for
     * @return JsonResponse. Returns a Message if the Deletion was Succesful or not
     */
    #[Delete(
        description: "ID in the url is used to find an Entry"
    )]
    #[Response(
        response: 200,
        description: "Gives out a Message if the Deletion was Successful or not",
        content: new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: JsonResponse::class)
            )
        )
    )]
    #[Rest\Delete('/comment/{id}', name: 'app_comment_delete')]
    public function comment_delete($id): JsonResponse
    {
        $entitystory = $this->repository->find($id);
        if(!$entitystory) {
            return $this->json("Comment with ID {$id} does not exist!", status: 403);
        }
        $this->repository->remove($entitystory, true);
        return $this->json("Comment with ID " . $id . " Succesfully Deleted");
    }

    /**
     * To Change a Comment
     * @param Request $request. To give Text a new Value
     * @param $id. To find the exact Entry that is wanted
     * @return JsonResponse
     */
    #[Put(
        description: "ID in the url is used to find an Entry"
    )]
    #[Response(
        response: 200,
        description: "Gives out a Message if the Change was Successful or not",
        content: new JsonContent(
            type: 'array',
            items: new Items(
                ref: new Model(
                    type: JsonResponse::class)
            )
        )
    )]
    #[Rest\Put('/comment/{id}', name: 'app_comment_update')]
    public function comment_update(Request $request, $id): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateComment::class, "json");
        $entitystory = $this->repository->find($id);

        if(!$entitystory) {
            return $this->json("Comment with ID " . $id . " does not exist! ", status: 403);
        }

        $errorResponse = $this->validateDTO($dto, ["update"]);
        if($errorResponse){return $errorResponse;}

        if ($dto->text == null){
            if($dto->likes == 0){
                $entitystory->setLikes($entitystory->getLikes()+1);
            }
            else{
                $entitystory->setDislikes($entitystory->getDislikes()+1);
            }
        }
        else{
            $entitystory->setText($dto->text);
        }

        $this->repository->save($entitystory, true);
        return $this->json("Comment with ID " . $id . " Succesfully Changed");
    }
}

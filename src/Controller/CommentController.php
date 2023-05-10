<?php

namespace App\Controller;

use App\DTO\CreateUpdateComment;
use App\DTO\CreateUpdateStory;
use App\DTO\Mapper\ShowCommentMapper;
use App\Entity\Comments;
use App\Entity\Story;
use App\Repository\CommentsRepository;
use App\Repository\StoryRepository;
use JMS\Serializer\SerializerInterface;
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

    #[Rest\Get('/comment', name: 'app_comment_get')]
    public function comment_get(Request $request): JsonResponse
    {
        $comment = $this->repository->findAll();

        return (new JsonResponse())->setContent(
            $this->serializer->serialize(
                $this->mapper->mapEntitiesToDTOs($comment), "json"
            )
        );
    }

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

    #[Rest\Delete('/comment/{id}', name: 'app_comment_delete')]
    public function comment_delete(Request $request, $id): JsonResponse
    {
        $entitystory = $this->repository->find($id);
        if(!$entitystory) {
            return $this->json("Comment with ID {$id} does not exist!", status: 403);
        }
        $this->repository->remove($entitystory, true);
        return $this->json("Comment with ID " . $id . " Succesfully Deleted");
    }

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

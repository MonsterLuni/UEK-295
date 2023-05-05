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
    }


    #[Rest\Post('/comment', name: 'app_comment_create')]
    public function comment_create(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateUpdateComment::class, "json");

        $story = $this->srepository->find($dto->refstory);

        //Zum Validieren vom $dto in der Datei CreateUpdateStory.php (bezieht sich auf Function validateDTO)
        $errorResponse = $this->validateDTO($dto, ["create"]);
        if($errorResponse){return $errorResponse;}

        $entity = new Comments();
        $entity->setRefstory($story);
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
}

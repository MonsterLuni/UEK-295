<?php

namespace App\Controller;

use App\DTO\CreateUser;
use App\Entity\User;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractFOSRestController
{
    public function __construct(private SerializerInterface $serializer,
                                private UserRepository $repository){

    }

    #[Post('/api/user/register', name: 'api_user_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse{
        $dto = $this->serializer->deserialize($request->getContent(), CreateUser::class, 'json');

        $user = new User();
        $user->setUsername($dto->username);
        $hashedPassword = $passwordHasher->hashPassword($user,$dto->password);
        $user->setPassword($hashedPassword);
        if($dto->is_admin){
            $user->setRoles(["ROLE_ADMIN", "ROLE_USER"]);
        }

        $this->repository->save($user,true);

        return $this->json("User erstellt.");
    }
}

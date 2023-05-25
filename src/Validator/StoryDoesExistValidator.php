<?php

namespace App\Validator;

use App\Repository\StoryRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StoryDoesExistValidator extends ConstraintValidator
{
    public function __construct(private StoryRepository $repository)
    {
    }

        public function validate($idStory, Constraint $constraint): void
        {
            $story = $this->repository->find($idStory);

            if (!$story) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('{{StoryId}}', $idStory)
                    ->addViolation();
            }
        }
}

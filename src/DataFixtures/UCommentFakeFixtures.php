<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UCommentFakeFixtures extends Fixture implements FixtureGroupInterface
{
    protected $faker;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        for ($story = 0; $story < 10; ++$story) {
            for ($comment = 0; $comment < 10; ++$comment) {
                $commententity = new Comments();
                $commententity->setText($this->faker->text());
                $refstory = 'Story'.$story;
                $commententity->setRefstory($this->getReference($refstory));
                $manager->persist($commententity);
            }
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['fakedata'];
    }
}

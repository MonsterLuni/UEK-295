<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\Story;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $story = new Story();
        $story->setTitle("Hallo");
        $story->setstorie("Gute Idee");
        $story->setAuthor("Luca Moser");

        $manager->persist($story);

        $comment = new Comments();
        $comment->setRefstory($story);
        $comment->setText("Hallo");
        $manager->persist($comment);

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}

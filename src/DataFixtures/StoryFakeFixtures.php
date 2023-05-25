<?php

namespace App\DataFixtures;

use App\Entity\Story;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class StoryFakeFixtures extends Fixture implements FixtureGroupInterface
{
    protected $faker;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create(); // "de_DE");
        // why no working? :(
        for ($story = 0; $story < 10; ++$story) {
            $storyentity = new Story();
            $storyentity->setAuthor($this->faker->firstNameMale());
            $storyentity->setstorie($this->faker->paragraph());
            $storyentity->setTitle($this->faker->sentence());
            $manager->persist($storyentity);
            $this->addReference('Story'.$story, $storyentity);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['fakedata'];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixture extends Fixture implements DependentFixtureInterface
{

    public function __construct(
        private readonly SluggerInterface $slugger
    )
    {

    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $categories = ['Plat Chaud', 'Dessert', 'Entrée', 'Gouter'];
        foreach ($categories as $c) {
            $category = new Category();
            $category
                ->setName($c)
                ->setSlug(strtolower($this->slugger->slug($c)))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()));
            $manager->persist($category);
            $this->addReference($c, $category);
        }

        for($i = 0; $i <= 10; $i++) {
            $title = $faker->foodName();
            $recipe = new Recipe();
            $recipe
                ->setTitle($title)
                ->setSlug(strtolower($this->slugger->slug($title)))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setContent($faker->paragraphs(10, true))
                ->setCategory($this->getReference($faker->randomElement($categories), Category::class))
                ->setAuthor($this->getReference('USER' . $faker->numberBetween(1, 10), User::class))
                ->setDuration($faker->numberBetween(2, 70))
                ;
            $manager->persist($recipe);
        }
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}

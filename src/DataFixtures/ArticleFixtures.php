<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $article = new Article();

        $categoryTechnology = $this->getReference(CategoryFixtures::TECHNOLOGY_CATEGORY_REFERENCE);

        $article->setCategory($categoryTechnology);
        $article->setTitle("Netflix back up for most users in US after outage, Downdetector shows");
        $article->setContent(
            "Nov 16 (Reuters) - Video streaming platform Netflix (NFLX.O), opens new tab was back up on Saturday after an outage that lasted roughly six hours in the United States, according to the outage tracking website Downdetector.com.\nDowndetector, which collates status reports from a number of sources, showed that there were fewer than 100 user reports of problems accessing Netflix as of 5 a.m. ET (1000 GMT)."
        );
        $article->setCreatedAt(new \DateTimeImmutable());
        $article->setAuthor("Reuters");
        $article->setImage('build/images/netflix.496b7ad9.png');

        $manager->persist($article);
        $manager->flush();

        //ARTICLE 2
        $article2 = new Article();

        $article2->setCategory($categoryTechnology);
        $article2->setTitle("Crypto enforcement seen slowing as Trump shifts priorities");
        $article2->setContent(
            "NEW YORK, Nov 15 (Reuters) - Less enforcement in the cryptocurrency sector is on the horizon, as Republican President-elect Donald Trump prepares to reset policy at the Justice Department and regulatory agencies, current and former senior government lawyers said on Friday.\nSpeaking at a conference in New York, the lawyers said financial fraud cases would still be brought, but that the new administration's Justice Department would prioritize other areas such as enforcing immigration laws - a major focus of Trump's campaign."
        );
        $article2->setCreatedAt(new \DateTimeImmutable());
        $article2->setAuthor("Luc Cohen");
        $article2->setImage('build/images/crypto.f679bdfe.png');

        $manager->persist($article2);
        $manager->flush();

        //ARTICLE 3

        $article3 = new Article();

        $categoryFashion = $this->getReference(CategoryFixtures::FASHION_CATEGORY_REFERENCE);

        $article3->setCategory($categoryFashion);
        $article3->setTitle("The Brooklyn Museum’s New Exhibition Is a Gold Lover’s Fever Dream");
        $article3->setContent("
                The museum had more than enough items to choose from in its vast collection—in fact, it was difficult to narrow down exactly which golden goods to focus on.\n“After reviewing most of them, I created a chronological selection of about 250 works and enriched it with loans of fashion, jewelry, and art to spark cross-disciplinary dialogues and juxtapositions,” says Yokobosky, who combined the old (the oldest item being a large sarcophagus lid from the 22nd Dynasty, which is on view for the first time in over a century)."
        );
        $article3->setCreatedAt(new \DateTimeImmutable());
        $article3->setAuthor("Christian Allaire");
        $article3->setImage('build/images/fashion.5703663a.jpg');

        $manager->persist($article3);
        $manager->flush();

        //ARTICLE 4

        $article4 = new Article();
        $categoryTravel= $this->getReference(CategoryFixtures::TRAVEL_CATEGORY_REFERENCE);
        $article4->setCategory($categoryTravel);
        $article4->setTitle("Great Dane: a tour of Denmark’s culture, countryside and coast");
        $article4->setContent("Don’t make small talk with strangers. Or talk about the weather. Or even ask people you know how they are… I read these social etiquette tips in a Denmark travel guide – four days into a 10-day trip. I’d already blown it on day one. “Look at that!”\nI said to the couple opposite me in the hotel’s below-ground sauna."
        );
        $article4->setCreatedAt(new \DateTimeImmutable());
        $article4->setAuthor("Alamy");
        $article4->setImage('build/images/denmark.4bf2987b.jpg');

        $manager->persist($article4);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

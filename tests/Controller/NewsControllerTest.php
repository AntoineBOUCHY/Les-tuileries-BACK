<?php

namespace App\Test\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private NewsRepository $repository;
    private string $path = '/event/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(News::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('News index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'news[title]' => 'Testing',
            'news[content]' => 'Testing',
            'news[isHomeEvent]' => 'Testing',
            'news[sliderPosition]' => 'Testing',
            'news[publishedAt]' => 'Testing',
        ]);

        self::assertResponseRedirects('/event/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new News();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setIsHomeEvent('My Title');
        $fixture->setSliderPosition('My Title');
        $fixture->setPublishedAt('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('News');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new News();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setIsHomeEvent('My Title');
        $fixture->setSliderPosition('My Title');
        $fixture->setPublishedAt('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'news[title]' => 'Something New',
            'news[content]' => 'Something New',
            'news[isHomeEvent]' => 'Something New',
            'news[sliderPosition]' => 'Something New',
            'news[publishedAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/event/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getIsHomeEvent());
        self::assertSame('Something New', $fixture[0]->getSliderPosition());
        self::assertSame('Something New', $fixture[0]->getPublishedAt());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new News();
        $fixture->setTitle('My Title');
        $fixture->setContent('My Title');
        $fixture->setIsHomeEvent('My Title');
        $fixture->setSliderPosition('My Title');
        $fixture->setPublishedAt('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/event/');
    }
}

<?php

namespace App\Test\Controller;

use App\Entity\Card;
use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CardControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CardRepository $repository;
    private string $path = '/card/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Card::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Card index');

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
            'card[sold]' => 'Testing',
            'card[boughtAt]' => 'Testing',
            'card[limitedDate]' => 'Testing',
            'card[usedAt]' => 'Testing',
            'card[giftTo]' => 'Testing',
            'card[user]' => 'Testing',
        ]);

        self::assertResponseRedirects('/card/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Card();
        $fixture->setSold('My Title');
        $fixture->setBoughtAt('My Title');
        $fixture->setLimitedDate('My Title');
        $fixture->setUsedAt('My Title');
        $fixture->setGiftTo('My Title');
        $fixture->setUser('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Card');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Card();
        $fixture->setSold('My Title');
        $fixture->setBoughtAt('My Title');
        $fixture->setLimitedDate('My Title');
        $fixture->setUsedAt('My Title');
        $fixture->setGiftTo('My Title');
        $fixture->setUser('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'card[sold]' => 'Something New',
            'card[boughtAt]' => 'Something New',
            'card[limitedDate]' => 'Something New',
            'card[usedAt]' => 'Something New',
            'card[giftTo]' => 'Something New',
            'card[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/card/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getSold());
        self::assertSame('Something New', $fixture[0]->getBoughtAt());
        self::assertSame('Something New', $fixture[0]->getLimitedDate());
        self::assertSame('Something New', $fixture[0]->getUsedAt());
        self::assertSame('Something New', $fixture[0]->getGiftTo());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Card();
        $fixture->setSold('My Title');
        $fixture->setBoughtAt('My Title');
        $fixture->setLimitedDate('My Title');
        $fixture->setUsedAt('My Title');
        $fixture->setGiftTo('My Title');
        $fixture->setUser('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/card/');
    }
}

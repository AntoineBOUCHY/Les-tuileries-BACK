<?php

namespace App\Test\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PictureControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PictureRepository $repository;
    private string $path = '/gallery/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Picture::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Picture index');

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
            'picture[path]' => 'Testing',
            'picture[alt]' => 'Testing',
            'picture[pageContents]' => 'Testing',
            'picture[news]' => 'Testing',
        ]);

        self::assertResponseRedirects('/gallery/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Picture();
        $fixture->setPath('My Title');
        $fixture->setAlt('My Title');
        $fixture->setPageContents('My Title');
        $fixture->setNews('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Picture');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Picture();
        $fixture->setPath('My Title');
        $fixture->setAlt('My Title');
        $fixture->setPageContents('My Title');
        $fixture->setNews('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'picture[path]' => 'Something New',
            'picture[alt]' => 'Something New',
            'picture[pageContents]' => 'Something New',
            'picture[news]' => 'Something New',
        ]);

        self::assertResponseRedirects('/gallery/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPath());
        self::assertSame('Something New', $fixture[0]->getAlt());
        self::assertSame('Something New', $fixture[0]->getPageContents());
        self::assertSame('Something New', $fixture[0]->getNews());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Picture();
        $fixture->setPath('My Title');
        $fixture->setAlt('My Title');
        $fixture->setPageContents('My Title');
        $fixture->setNews('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/gallery/');
    }
}

<?php

namespace PiedWeb\CMSBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManager;
use Twig_Environment;

/**
 * Inspired by https://github.com/eko/FeedBundle.
 */
class FeedDumpService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $webDir;

    /**
     * @var string
     */
    private $page_class;

    public function __construct(EntityManager $em, Twig_Environment $twig, string $webDir, string $page_class)
    {
        $this->em = $em;
        $this->filesystem = new Filesystem();
        $this->twig = $twig;
        $this->webDir = $webDir;
        $this->page_class = $page_class;
    }

    /**
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function dump()
    {
        if (!method_exists($this->filesystem, 'dumpFile')) {
            throw new \RuntimeException('Method dumpFile() is not available on your Filesystem component version, you should upgrade it.');
        }

        $dump = $this->render();
        $filepath = $this->webDir.'/feed.xml';

        $this->filesystem->dumpFile($filepath, $dump);
    }

    protected function render()
    {
        $qb = $this->em->getRepository($this->page_class)->getQueryToFindPublished('p');
        $qb->andWhere('p.metaRobots IS NULL OR p.metaRobots NOT LIKE :noi')->setParameter('noi', '%no-index%');
        $qb->andWhere('p.mainContent NOT LIKE :noi')->setParameter('noi', 'Location:%');
        $qb->setMaxResults(5);
        $pages = $qb->getQuery()->getResult();

        return $this->twig->render('@PiedWebCMS/page/rss.xml.twig', ['pages' => $pages]);
    }
}

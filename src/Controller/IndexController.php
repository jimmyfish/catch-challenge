<?php

namespace App\Controller;

use App\Controller\Data\DataManipulation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private $dataManipulation;
    private $filesystem;

    public function __construct(
        DataManipulation $dataManipulation,
        Filesystem $filesystem
    )
    {
        $this->dataManipulation = $dataManipulation;
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $finder = new Finder();

        $temporaryFolder = $this->dataManipulation->getTemporaryDir();

        $files = $finder->in($temporaryFolder)->name('*.jsonl');

        $this->dataManipulation->convertToCsv($files, $this->dataManipulation->getResultDir());

        return new JsonResponse(["message" => "DONE"]);
    }
}

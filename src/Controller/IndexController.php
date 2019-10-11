<?php

namespace App\Controller;

use App\Controller\Data\DataManipulation;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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
     * @Route("/convert", name="index")
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if (is_null($request->get('src'))) {
            return new JsonResponse(["error" => ['message' => 'src param required']]);
        }

        $filename = $this->getFile($request);

        $temporaryFolder = $this->dataManipulation->getTemporaryDir();

        if ($request->get('filetype') === "csv" || is_null($request->get('filetype'))) {
            $this->dataManipulation
                ->convertToCsv(
                    $temporaryFolder . "/inputs/" . $filename . ".jsonl",
                    $this->dataManipulation->getResultDir() . "/" . $filename . ".csv"
                );
        } else {
            return new JsonResponse(['error' => ['message' => 'filetype not supported']]);
        }

        return new JsonResponse([
            "message" => "success",
            "filename" => $this->dataManipulation->getResultDir() . "/" . $filename . ".csv"
        ]);
    }

    public function getFile(Request $request)
    {
        if (!file_exists($this->dataManipulation->getTemporaryDir() . "/inputs")) {
            $this->filesystem->mkdir($this->dataManipulation->getTemporaryDir() . "/inputs");
        }

        $client = HttpClient::create();

        try {
            $response = $client->request('GET', $request->get('src'));

            $filename = md5(random_bytes(10));

            $fileHandler = fopen($this->dataManipulation->getTemporaryDir() . "/inputs/" . $filename . ".jsonl", "w+");

            foreach ($client->stream($response) as $chunk) {
                fwrite($fileHandler, $chunk->getContent());
            }
        } catch (TransportExceptionInterface $exception) {
            return new JsonResponse(["message" => $exception->getMessage()]);
        } catch (Exception $exception) {
            return new JsonResponse(["message" => $exception->getMessage()]);
        }

        return $filename;
    }
}

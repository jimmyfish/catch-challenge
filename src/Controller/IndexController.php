<?php

namespace App\Controller;

use App\Controller\Data\DataInput;
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
    private $dataInput;
    private $supportedFiletype = [
        "csv",
        "yaml",
    ];

    public function __construct(
        DataManipulation $dataManipulation,
        DataInput $dataInput,
        Filesystem $filesystem
    )
    {
        $this->dataManipulation = $dataManipulation;
        $this->filesystem = $filesystem;
        $this->dataInput = $dataInput;
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

        if (!is_null($request->get('filetype'))) {
            if (!in_array($request->get('filetype'), $this->supportedFiletype)) {
                return new JsonResponse(['error' => ['message' => 'filetype not supported']]);
            }
        }

        $response = $this->dataInput
            ->{$request->get('filetype')}(
                $temporaryFolder . "/inputs/" . $filename . ".jsonl",
                $this->dataManipulation->getResultDir() . "/" . $filename . "." . $request->get('filetype')
            );

        if (!is_null($request->get('db')) && $request->get('db') !== 0) {
            if ($request->get('db') !== 1) {
                return new JsonResponse(["error" => ["message" => "parameter not valid"]]);
            }
        }

        if ($response) {
            return new JsonResponse([
                "message" => "success",
                "filename" => $this->dataManipulation->getResultDir() . "/" . $filename . "." . $request->get('filetype'),
            ]);
        } else {
            return new JsonResponse([
                "message" => "Something went wrong",
            ]);
        }
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

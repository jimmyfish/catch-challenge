<?php

namespace App\Controller\Data;

use DateTime;
use Exception;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DataManipulation extends AbstractController
{
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getKernelProjectDir()
    {
        return $this->getParameter('kernel.project_dir');
    }

    public function getTemporaryDir()
    {
        return $this->getKernelProjectDir() . "/var/tmp";
    }

    public function getResultDir()
    {
        return $this->getTemporaryDir() . "/results";
    }

    public function generateNewName($file)
    {
        return substr($file->getFileName(), 0, strlen($file->getFilename()) - strlen($file->getExtension()) - 1);
    }

    public function convertToCsv($files, $outputDir)
    {
        $filename = $outputDir . "/challenge-1-in.csv";

        $ff = fopen($this->getTemporaryDir()."/challenge-1-in.jsonl", "r");
        $w = fopen($filename, "w+");

        if (!$this->filesystem->exists($outputDir)) {
            $this->filesystem->mkdir($outputDir);
        }

        $csvkeys = [
            "order_id",
            "order_datetime",
            "total_order_value",
            "average_unit_price",
            "distinct_unit_count",
            "total_units_count",
            "customer_state"
        ];

        $csvitem = [];

        fputcsv($w, $csvkeys, $delimiter = ";");

        while ($json = fgets($ff)) {

            $items = json_decode($json, false);

            $data = [
                'order_id' => $items->order_id,
                'order_datetime' => $this->orderDate($items),
                'total_order_value' => $this->calculateTotalOrderValue($items),
                'average_unit_price' => $this->averageUnitPrice($items),
                'distinct_unit_count' => $this->distinctUnitCount($items),
                'total_unit_count' => $this->totalUnit($items),
                'customer_state' => str_replace('"', '', $items->customer->shipping_address->state),
            ];

            array_push($csvitem, $data);
        }

        foreach ($csvitem as $item) {
            $last = end($item);
            foreach ($item as $itm) {
                fwrite($w, "$itm");
                if ($itm !== $last) {
                    fwrite($w, ";");
                }
            }
            fwrite($w, "\n");
        }
    }

    public function orderDate($items)
    {
        $date = new DateTime($items->order_date);

        return $date->format("c");
    }

    public function totalUnit($items)
    {
        return array_sum(array_column($items->items, 'quantity'));
    }

    public function distinctUnitCount($items)
    {
        $unique_array = [];
        foreach ($items->items as $item) {
            $hash = $item->product->product_id;
            $unique_array[$hash] = $item;
        }

        return count($unique_array);
    }

    public function averageUnitPrice($items)
    {
        $result = $this->calculateTotalOrderValue($items);

        $result = round($result / array_sum(array_column($items->items, 'quantity')), 2);

        return $result;
    }

    public function calculateTotalOrderValue($items)
    {
        $result = 0;

        foreach ($items->items as $item) {
            $result += $item->unit_price * $item->quantity;
        }

        if (!empty($items->discounts)) {
            usort($items->discounts, function ($a, $b) {
                return strcmp($a->priority, $b->priority);
            });

            foreach ($items->discounts as $discount) {
                if ($discount->type === "PERCENTAGE") {
                    $result = $result - (($discount->value / 100) * $result);
                } else {
                    $result = $result - $discount->value;
                }
            }
        }

        return round($result, 2);
    }
}

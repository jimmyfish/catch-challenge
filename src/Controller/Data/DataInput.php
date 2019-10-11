<?php


namespace App\Controller\Data;

use App\Entity\OrderDetail;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Yaml\Yaml;

class DataInput extends AbstractController
{
    private $dataManipulation;
    private $filesystem;
    private $keys = [
        "order_id",
        "order_datetime",
        "total_order_value",
        "average_unit_price",
        "distinct_unit_count",
        "total_units_count",
        "customer_state"
    ];

    public function __construct(
        DataManipulation $dataManipulation,
        Filesystem $filesystem
    )
    {
        $this->dataManipulation = $dataManipulation;
        $this->filesystem = $filesystem;
    }

    public function csv($files, $output, $db = 0)
    {
        $w = fopen($output, "w+");

        $csvItems = $this->dataManipulation->getData($files);

        fputcsv($w, $this->keys, $delimiter = ",");

        foreach ($csvItems as $item) {
            $last = end($item);
            foreach ($item as $itm) {
                fwrite($w, "$itm");
                if ($itm !== $last) {
                    fwrite($w, ",");
                }
            }
            fwrite($w, "\n");
        }

        if ((int)$db === 1) {
            $result = $this->insertToDB($csvItems);

            return $result;
        }

        return TRUE;
    }

    public function yaml($files, $output, $db = 0)
    {
        $result = $this->dataManipulation->getData($files);

        $yaml = Yaml::dump($result);

        file_put_contents($output, $yaml);

        if ((int)$db === 1) {
            $result = $this->insertToDB($result);

            return $result;
        }

        return TRUE;
    }

    public function insertToDB($data)
    {
        $manager = $this->getDoctrine()->getManager();

        $batchNumber = md5(random_bytes(10));

        foreach ($data as $datum) {
            $collections = new OrderDetail();
            $collections->setOrderId($datum['order_id']);
            $collections->setOrderDatetime(new DateTime($datum['order_datetime']));
            $collections->setTotalOrderValue($datum['total_order_value']);
            $collections->setAverageUnitPrice($datum['average_unit_price']);
            $collections->setDistinctUnitCount($datum['distinct_unit_count']);
            $collections->setTotalUnitsCount($datum['total_unit_count']);
            $collections->setCustomerState($datum['customer_state']);
            $collections->setBatchNumber($batchNumber);

            $manager->persist($collections);
        }

        $manager->flush();

        return $batchNumber;
    }
}
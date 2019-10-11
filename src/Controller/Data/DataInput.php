<?php


namespace App\Controller\Data;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
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

    public function csv($files, $output)
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

        return TRUE;
    }

    public function yaml($files, $output)
    {
        $result = $this->dataManipulation->getData($files);

        $yaml = Yaml::dump($result);

        file_put_contents($output, $yaml);

        return TRUE;
    }
}
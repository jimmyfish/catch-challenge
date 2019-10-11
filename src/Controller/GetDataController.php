<?php

namespace App\Controller;

use App\Entity\OrderDetail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetDataController extends AbstractController
{
    /**
     * @Route("/get/{batch_number}", name="get_data")
     */
    public function index(string $batch_number)
    {
        $orderDetail = $this->getDoctrine()
            ->getRepository(OrderDetail::class)
            ->createQueryBuilder('u')
            ->where('u.batch_number = :batch_number')
            ->setParameter('batch_number', $batch_number)
            ->getQuery()->getArrayResult();

        return new JsonResponse($orderDetail);
    }
}

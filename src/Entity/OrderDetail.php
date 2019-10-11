<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderDetailRepository")
 */
class OrderDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $order_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $order_datetime;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_order_value;

    /**
     * @ORM\Column(type="string")
     */
    private $average_unit_price;

    /**
     * @ORM\Column(type="integer")
     */
    private $distinct_unit_count;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_units_count;

    /**
     * @ORM\Column(type="string")
     */
    private $batch_number;

    /**
     * @return mixed
     */
    public function getBatchNumber()
    {
        return $this->batch_number;
    }

    /**
     * @param mixed $batch_number
     */
    public function setBatchNumber($batch_number): void
    {
        $this->batch_number = $batch_number;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customer_state;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->order_id;
    }

    public function setOrderId(int $order_id): self
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getOrderDatetime(): ?\DateTimeInterface
    {
        return $this->order_datetime;
    }

    public function setOrderDatetime(\DateTimeInterface $order_datetime): self
    {
        $this->order_datetime = $order_datetime;

        return $this;
    }

    public function getTotalOrderValue(): ?int
    {
        return $this->total_order_value;
    }

    public function setTotalOrderValue(int $total_order_value): self
    {
        $this->total_order_value = $total_order_value;

        return $this;
    }

    public function getAverageUnitPrice(): ?float
    {
        return $this->average_unit_price;
    }

    public function setAverageUnitPrice(float $average_unit_price): self
    {
        $this->average_unit_price = $average_unit_price;

        return $this;
    }

    public function getDistinctUnitCount(): ?int
    {
        return $this->distinct_unit_count;
    }

    public function setDistinctUnitCount(int $distinct_unit_count): self
    {
        $this->distinct_unit_count = $distinct_unit_count;

        return $this;
    }

    public function getTotalUnitsCount(): ?int
    {
        return $this->total_units_count;
    }

    public function setTotalUnitsCount(int $total_units_count): self
    {
        $this->total_units_count = $total_units_count;

        return $this;
    }

    public function getCustomerState(): ?string
    {
        return $this->customer_state;
    }

    public function setCustomerState(string $customer_state): self
    {
        $this->customer_state = $customer_state;

        return $this;
    }
}

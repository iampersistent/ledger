<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Entity;

use Money\Money;

final class Item
{
    /** @var \Money\Money */
    protected $amount;
    /** @var string */
    protected $description;
    /** @var mixed */
    protected $id;
    /** @var mixed */
    protected $productId;
    /** @var string|null */
    protected $referenceNumber;
    /** @var \Money\Money */
    protected $taxes;
    /** @var \Money\Money */
    protected $total;

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function setAmount(Money $amount): Item
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Item
    {
        $this->description = $description;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(?string $referenceNumber): Item
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    public function getTaxes(): Money
    {
        return $this->taxes;
    }

    public function setTaxes(Money $taxes): Item
    {
        $this->taxes = $taxes;

        return $this;
    }

    public function getTotal(): Money
    {
        return $this->total;
    }

    public function setTotal(Money $total): Item
    {
        $this->total = $total;

        return $this;
    }
}

<?php
declare(strict_types=1);

namespace IamPersistent\Ledger\Entity;

use DateTime;

abstract class Entry
{
    /** @var DateTime */
    protected $date;
    /** @var string */
    protected $description;
    /** @var mixed */
    protected $id;
    /** @var int */
    protected $line;
    /** @var string */
    protected $referenceNumber;

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): Entry
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Entry
    {
        $this->description = $description;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Entry
    {
        $this->id = $id;

        return $this;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    public function setLine(int $line): Entry
    {
        $this->line = $line;

        return $this;
    }

    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): Entry
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    public function isCredit(): bool
    {
        return false;
    }

    public function isDebit(): bool
    {
        return false;
    }
}
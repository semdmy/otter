<?php

namespace SwiftOtter\OrderExport\Model;

use DateTime;

class HeaderData
{
    protected ?DateTime $shipDate;
    protected string $merchantNotes;

    public function getShipDate(): ?DateTime
    {
        return $this->shipDate;
    }

    public function setShipDate(?DateTime $shipDate): HeaderData
    {
        $this->shipDate = $shipDate;
        return $this;
    }

    public function getMerchantNotes(): string
    {
        return (string) $this->merchantNotes;
    }

    public function setMerchantNotes(string $merchantNotes): HeaderData
    {
        $this->merchantNotes = $merchantNotes;
        return $this;
    }
}

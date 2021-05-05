<?php

declare(strict_types=1);

namespace Tavy315\SyliusInvoicesPlugin\Context;

use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceInterface;

interface InvoiceContextInterface
{
    public function getInvoice(string $documentNo): CustomerInvoiceInterface;
}

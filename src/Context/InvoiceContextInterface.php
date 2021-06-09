<?php

declare(strict_types=1);

namespace Tavy315\SyliusInvoicesPlugin\Context;

use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceInterface;
use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceProduct;

interface InvoiceContextInterface
{
    public function getCustomerInvoice(string $documentNo): CustomerInvoiceInterface;

    /**
     * @return array<CustomerInvoiceProduct>
     */
    public function getInvoiceProducts(CustomerInvoiceInterface $invoice, ?int $limit = null): array;
}

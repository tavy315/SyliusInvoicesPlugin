<?php

declare(strict_types=1);

namespace Tavy315\SyliusInvoicesPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceInterface;

interface CustomerInvoiceRepositoryInterface extends RepositoryInterface
{
    public function findOneByCustomerAndDocument(CustomerInterface $customer, string $documentNo): ?CustomerInvoiceInterface;

    public function createByCustomerQueryBuilder($customerId): QueryBuilder;
}

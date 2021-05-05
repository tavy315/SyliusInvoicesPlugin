<?php

declare(strict_types=1);

namespace Tavy315\SyliusInvoicesPlugin\Context;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceInterface;
use Tavy315\SyliusInvoicesPlugin\Repository\CustomerInvoiceRepositoryInterface;

final class InvoiceContext implements InvoiceContextInterface
{
    private CustomerInvoiceRepositoryInterface $customerInvoiceRepository;

    private TokenStorageInterface $tokenStorage;

    public function __construct(CustomerInvoiceRepositoryInterface $customerInvoiceRepository, TokenStorageInterface $tokenStorage)
    {
        $this->customerInvoiceRepository = $customerInvoiceRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function getInvoice(string $documentNo): CustomerInvoiceInterface
    {
        $invoice = $this->customerInvoiceRepository->findOneByCustomerAndDocument($this->getCustomer(), $documentNo);

        if ($invoice === null) {
            throw new NotFoundHttpException();
        }

        return $invoice;
    }

    private function getCustomer(): CustomerInterface
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            throw new AuthenticationCredentialsNotFoundException('The token storage contains no authentication token. One possible reason may be that there is no firewall configured for this URL.');
        }

        $user = $token ? $token->getUser() : null;

        if (!($user instanceof ShopUserInterface)) {
            throw new AccessDeniedHttpException();
        }

        $customer = $user->getCustomer();

        if (!($customer instanceof CustomerInterface)) {
            throw new AccessDeniedHttpException();
        }

        return $customer;
    }
}

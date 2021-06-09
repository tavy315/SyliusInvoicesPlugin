<?php

declare(strict_types=1);

namespace Tavy315\SyliusInvoicesPlugin\Context;

use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceInterface;
use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceProduct;
use Tavy315\SyliusInvoicesPlugin\Repository\CustomerInvoiceRepositoryInterface;
use Tavy315\SyliusInvoicesPlugin\Repository\ProductRepositoryInterface;

final class InvoiceContext implements InvoiceContextInterface
{
    private CustomerInvoiceRepositoryInterface $customerInvoiceRepository;

    private ProductRepositoryInterface $productRepository;

    private ShopperContextInterface $shopperContext;

    private TokenStorageInterface $tokenStorage;

    public function __construct(
        CustomerInvoiceRepositoryInterface $customerInvoiceRepository,
        ProductRepositoryInterface $productRepository,
        ShopperContextInterface $shopperContext,
        TokenStorageInterface $tokenStorage
    ) {
        $this->customerInvoiceRepository = $customerInvoiceRepository;
        $this->productRepository = $productRepository;
        $this->shopperContext = $shopperContext;
        $this->tokenStorage = $tokenStorage;
    }

    public function getCustomerInvoice(string $documentNo): CustomerInvoiceInterface
    {
        $invoice = $this->customerInvoiceRepository->findOneByCustomerAndDocument($this->getCustomer(), $documentNo);

        if ($invoice === null) {
            throw new NotFoundHttpException();
        }

        return $invoice;
    }

    /**
     * @return array<CustomerInvoiceProduct>
     */
    public function getInvoiceProducts(CustomerInvoiceInterface $invoice, ?int $limit = null): array
    {
        $products = [];

        $invoiceProducts = \array_slice($invoice->getProducts(), 0, $limit);

        foreach ($invoiceProducts as $product) {
            $customerInvoiceProduct = CustomerInvoiceProduct::fromArray($product);

            if ($product['no'] !== '') {
                $customerInvoiceProduct->product = $this->productRepository
                    ->createShopListQueryBuilder(
                        $this->shopperContext->getChannel(),
                        $this->shopperContext->getLocaleCode(),
                        [ $product['no'] ]
                    )
                    ->getQuery()
                    ->getOneOrNullResult();
            }

            $products[] = $customerInvoiceProduct;
        }

        return $products;
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

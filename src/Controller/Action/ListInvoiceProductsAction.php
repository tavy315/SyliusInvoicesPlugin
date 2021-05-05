<?php

declare(strict_types=1);

namespace Tavy315\SyliusInvoicesPlugin\Controller\Action;

use Sylius\Component\Core\Context\ShopperContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tavy315\SyliusInvoicesPlugin\Context\InvoiceContextInterface;
use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceInterface;
use Tavy315\SyliusInvoicesPlugin\Entity\CustomerInvoiceProduct;
use Tavy315\SyliusInvoicesPlugin\Repository\ProductRepositoryInterface;
use Twig\Environment;

final class ListInvoiceProductsAction
{
    private InvoiceContextInterface $invoiceContext;

    private ProductRepositoryInterface $productRepository;

    private ShopperContextInterface $shopperContext;

    private Environment $twig;

    public function __construct(
        InvoiceContextInterface $invoiceContext,
        ProductRepositoryInterface $productRepository,
        Environment $twig,
        ShopperContextInterface $shopperContext
    ) {
        $this->invoiceContext = $invoiceContext;
        $this->productRepository = $productRepository;
        $this->shopperContext = $shopperContext;
        $this->twig = $twig;
    }

    public function __invoke(string $document, Request $request): Response
    {
        $invoice = $this->invoiceContext->getInvoice($document);

        return new Response($this->twig->render('@Tavy315SyliusInvoicesPlugin/Account/CustomerInvoice/Grid/products.html.twig', [
            'invoice'  => $invoice,
            'products' => $this->getProducts($invoice),
        ]));
    }

    /**
     * @return array<CustomerInvoiceProduct>
     */
    private function getProducts(CustomerInvoiceInterface $invoice): array
    {
        $products = [];

        foreach ($invoice->getProducts() as $product) {
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
}

<?php

declare(strict_types=1);

namespace Tavy315\SyliusInvoicesPlugin\Controller\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tavy315\SyliusInvoicesPlugin\Context\InvoiceContextInterface;
use Twig\Environment;

final class ListInvoiceProductsAction
{
    private InvoiceContextInterface $invoiceContext;

    private ?int $productLimit;

    private Environment $twig;

    public function __construct(InvoiceContextInterface $invoiceContext, Environment $twig, ?int $productLimit)
    {
        $this->invoiceContext = $invoiceContext;
        $this->productLimit = $productLimit;
        $this->twig = $twig;
    }

    public function __invoke(string $document, Request $request): Response
    {
        $invoice = $this->invoiceContext->getCustomerInvoice($document);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'html' => $this->twig->render('@Tavy315SyliusInvoicesPlugin/Account/CustomerInvoice/Grid/_products.html.twig', [
                    'invoice'  => $invoice,
                    'products' => $this->invoiceContext->getInvoiceProducts($invoice),
                ]),
            ]);
        }

        return new Response($this->twig->render('@Tavy315SyliusInvoicesPlugin/Account/CustomerInvoice/Grid/show.html.twig', [
            'invoice'  => $invoice,
            'products' => $this->invoiceContext->getInvoiceProducts($invoice, $this->productLimit),
        ]));
    }
}

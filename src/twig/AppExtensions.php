<?php

namespace App\twig;

use App\Class\Cart;
use Twig\TwigFilter;
use Twig\Extension\GlobalsInterface;
use Twig\Extension\AbstractExtension;
use App\Repository\CategoryRepository;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    public function __construct(private CategoryRepository $categoryRepository, private Cart $cart)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cart = $cart;
    }
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this,'formatPrice'])
        ];
    }

    public function formatPrice($number)
    {
        return number_format($number, decimals:'2', decimal_separator:','). '€';
    }

    public function getGlobals(): array 
    {
        return [
            'allCategories' =>  $this->categoryRepository->findAll(),
            'fullCartQuantity' => $this->cart->fullQuantity()
        ];
    }
}
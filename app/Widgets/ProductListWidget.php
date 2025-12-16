<?php

namespace App\Widgets;

use App\Models\Product;

class ProductListWidget extends BaseWidget
{
    public function render(): string
    {
        $data = $this->getData();
        $styles = $this->buildStyles();
        
        $html = "<div class=\"product-list-widget\" {$styles}>";
        $html .= "<h3>{$this->getConfig('title', 'Products')}</h3>";
        $html .= "<div class=\"products\">";
        
        foreach ($data['products'] as $product) {
            $html .= "<div class=\"product-item\">";
            $html .= "<h4>{$product->name}</h4>";
            $html .= "<p>{$product->price}</p>";
            $html .= "</div>";
        }
        
        $html .= "</div></div>";
        
        return $html;
    }

    public function getData(): array
    {
        $categoryId = $this->getConfig('category_id');
        $limit = $this->getConfig('limit', 10);
        
        $query = Product::with(['category', 'brand']);
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        $products = $query->limit($limit)->get();
        
        return [
            'products' => $products,
        ];
    }
}


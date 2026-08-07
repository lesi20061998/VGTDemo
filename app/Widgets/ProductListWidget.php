<?php

namespace App\Widgets;

use App\Models\Post;

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
            $priceValue = $product->meta_data['price'] ?? null;
            $price = $priceValue ? number_format($priceValue) . 'đ' : 'Liên hệ';
            
            $html .= "<div class=\"product-item\">";
            $html .= "<h4>{$product->title}</h4>";
            $html .= "<p>{$price}</p>";
            $html .= "</div>";
        }
        
        $html .= "</div></div>";
        
        return $html;
    }

    public function getData(): array
    {
        $categoryId = $this->getConfig('category_id');
        $limit = $this->getConfig('limit', 10);
        
        $query = Post::where('post_type', 'product')->with(['taxonomies']);
        
        if ($categoryId) {
            $query->whereHas('taxonomies', function($q) use ($categoryId) {
                $q->where('taxonomies.id', $categoryId);
            });
        }
        
        $products = $query->limit($limit)->get();
        
        return [
            'products' => $products,
        ];
    }
}


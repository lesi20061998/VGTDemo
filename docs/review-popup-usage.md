# Hướng dẫn sử dụng Review Popup

## Tổng quan

Review Popup là một component cho phép khách hàng đánh giá sản phẩm thông qua popup modal. Component này tự động lấy cấu hình từ admin settings và áp dụng các quy tắc phù hợp.

## Cài đặt

### 1. Include component trong layout hoặc trang

```blade
{{-- Trong layout chính hoặc trang sản phẩm --}}
<x-review-popup 
    :product-id="$product->id" 
    :product-name="$product->name" 
    :product-image="$product->image_url" 
/>
```

### 2. Thêm meta CSRF token (nếu chưa có)

```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

## Cách sử dụng

### 1. Mở popup bằng JavaScript

```javascript
// Cách đơn giản nhất
openReviewPopup(productId, 'Tên sản phẩm', 'URL hình ảnh');

// Hoặc sử dụng helper
ReviewPopupHelper.openForProduct(productId, 'Tên sản phẩm', 'URL hình ảnh');
```

### 2. Thêm button đánh giá

```html
<!-- Button đơn giản -->
<button onclick="openReviewPopup(1, 'iPhone 15', '/images/iphone15.jpg')">
    Viết đánh giá
</button>

<!-- Sử dụng data attributes (tự động khởi tạo) -->
<button 
    data-review-trigger
    data-product-id="1"
    data-product-name="iPhone 15"
    data-product-image="/images/iphone15.jpg">
    Viết đánh giá
</button>
```

### 3. Thêm button đánh giá cho nhiều sản phẩm

```javascript
// Tự động thêm button cho tất cả product cards
ReviewPopupHelper.addReviewButtons('.product-card', {
    buttonText: 'Đánh giá',
    buttonClass: 'btn btn-primary btn-sm',
    insertSelector: '.product-actions'
});
```

### 4. Thêm floating button

```javascript
// Thêm floating button ở góc màn hình
ReviewPopupHelper.addFloatingButton({
    productId: currentProductId,
    productName: 'Sản phẩm hiện tại',
    hideOnScroll: true
});
```

### 5. Hiển thị prompt sau khi mua hàng

```javascript
// Sau khi khách hàng hoàn thành đơn hàng
ReviewPopupHelper.showPostPurchasePrompt({
    productId: orderedProduct.id,
    productName: orderedProduct.name,
    productImage: orderedProduct.image
});
```

## Tùy chỉnh giao diện

### CSS Classes có sẵn

```css
/* Popup container */
#review-popup { }

/* Form sections */
.popup-star-btn { }
#popup-images-section { }
#popup-error-messages { }

/* Template styles */
.review-template-template1 { }
.review-template-template2 { }
.review-template-template3 { }

/* State classes */
.reviews-loading { }
.reviews-error { }
.reviews-success { }
```

### Tùy chỉnh template

Popup sẽ tự động áp dụng template được chọn trong admin settings:

- `template1`: Classic - giao diện truyền thống
- `template2`: Modern - giao diện hiện đại với gradient
- `template3`: Minimal - giao diện tối giản

## API Endpoints

### Lấy cấu hình reviews

```
GET /api/reviews/config
```

Response:
```json
{
    "success": true,
    "data": {
        "enabled": true,
        "require_login": true,
        "require_purchase": false,
        "allow_images": true,
        "max_images": 5,
        "min_rating": 1,
        "show_verified": true,
        "reward_points": 10,
        // ... other settings
    }
}
```

### Gửi đánh giá

```
POST /api/reviews
```

Form data:
- `product_id`: ID sản phẩm
- `rating`: Điểm đánh giá (1-5)
- `comment`: Nội dung đánh giá
- `reviewer_name`: Tên người đánh giá (nếu không đăng nhập)
- `reviewer_email`: Email người đánh giá (nếu không đăng nhập)
- `images[]`: Mảng file hình ảnh

## Validation Rules

Popup tự động validate dựa trên cấu hình admin:

1. **Đăng nhập**: Kiểm tra `require_login`
2. **Mua hàng**: Kiểm tra `require_purchase` 
3. **Rating tối thiểu**: Kiểm tra `min_rating`
4. **Số lượng ảnh**: Kiểm tra `max_images` và `allow_images`

## Events

### JavaScript Events

```javascript
// Lắng nghe khi config được load
window.addEventListener('reviewsConfigLoaded', function(e) {
    console.log('Reviews config loaded:', e.detail);
});

// Tùy chỉnh validation
window.addEventListener('beforeReviewSubmit', function(e) {
    // Thêm validation tùy chỉnh
    if (!customValidation(e.detail.formData)) {
        e.preventDefault();
    }
});
```

## Ví dụ hoàn chỉnh

### Trang sản phẩm

```blade
@extends('layouts.app')

@section('content')
<div class="product-page">
    <div class="product-info">
        <h1>{{ $product->name }}</h1>
        <p>{{ $product->description }}</p>
        
        <!-- Button đánh giá -->
        <button 
            onclick="openReviewPopup({{ $product->id }}, '{{ $product->name }}', '{{ $product->image_url }}')"
            class="btn btn-primary">
            Viết đánh giá
        </button>
    </div>
    
    <!-- Danh sách đánh giá hiện có -->
    <div class="reviews-section">
        <!-- Reviews list here -->
    </div>
</div>

<!-- Include popup component -->
<x-review-popup 
    :product-id="$product->id" 
    :product-name="$product->name" 
    :product-image="$product->image_url" 
/>
@endsection
```

### Trang danh sách sản phẩm

```blade
@extends('layouts.app')

@section('content')
<div class="products-grid">
    @foreach($products as $product)
    <div class="product-card" 
         data-product-id="{{ $product->id }}"
         data-product-name="{{ $product->name }}"
         data-product-image="{{ $product->image_url }}">
        
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
        <h3>{{ $product->name }}</h3>
        <p>{{ $product->price }}</p>
        
        <div class="product-actions">
            <button class="btn btn-primary">Mua ngay</button>
            <!-- Button review sẽ được thêm tự động -->
        </div>
    </div>
    @endforeach
</div>

<!-- Include popup component -->
<x-review-popup />

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự động thêm button đánh giá cho tất cả sản phẩm
    ReviewPopupHelper.addReviewButtons('.product-card', {
        buttonText: 'Đánh giá',
        buttonClass: 'btn btn-outline btn-sm',
        insertSelector: '.product-actions'
    });
});
</script>
@endsection
```

## Troubleshooting

### Popup không hiển thị
- Kiểm tra đã include component `<x-review-popup />`
- Kiểm tra JavaScript console có lỗi không
- Đảm bảo đã có CSRF token

### Validation lỗi
- Kiểm tra cấu hình admin settings
- Xem API response trong Network tab
- Kiểm tra user permissions

### Hình ảnh không upload được
- Kiểm tra `allow_images` trong settings
- Kiểm tra `max_images` limit
- Đảm bảo server hỗ trợ file upload

## Tích hợp với hệ thống khác

### Laravel Livewire

```php
// Trong Livewire component
public function openReviewPopup($productId)
{
    $this->dispatchBrowserEvent('open-review-popup', [
        'productId' => $productId,
        'productName' => $this->product->name,
        'productImage' => $this->product->image_url
    ]);
}
```

```javascript
// Lắng nghe event từ Livewire
window.addEventListener('open-review-popup', event => {
    const { productId, productName, productImage } = event.detail;
    openReviewPopup(productId, productName, productImage);
});
```

### Vue.js/React

```javascript
// Vue component method
openReview(product) {
    window.openReviewPopup(product.id, product.name, product.image);
}

// React component method
const openReview = (product) => {
    window.openReviewPopup(product.id, product.name, product.image);
};
```
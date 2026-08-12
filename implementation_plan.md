# Kế hoạch: Xây dựng lại Widget System

## Tổng quan

Xây dựng lại toàn bộ hệ thống Widget từ template mẫu. Mỗi section trên trang được chuyển thành một **Widget** độc lập, có thể cấu hình hoàn toàn qua Admin CMS, không lộ bất kỳ thông tin công nghệ hay tên theme nào ra ngoài.

---

## Các Section sẽ được chuyển thành Widget

### A. Trang Home

| # | Section | Widget Key | Mô tả |
|---|---|---|---|
| 1 | Top Banner | `shop_top_banner` | Banner khuyến mãi, đếm ngược, có thể đóng |
| 2 | Hero Slideshow | `shop_hero_slider` | Slider banner chính: ảnh nền, tiêu đề, CTA |
| 3 | Product Slider | `shop_product_slider` | Slider sản phẩm theo danh mục |
| 4 | Category Banner | `shop_category_banner` | Grid danh mục với ảnh nền và link |
| 5 | Featured Products | `shop_featured_products` | Grid/Slider sản phẩm nổi bật |
| 6 | Promo Banner | `shop_promo_banner` | Banner quảng cáo 1/2/3 cột với ảnh |
| 7 | Best Seller | `shop_bestseller_products` | Tabs sản phẩm bán chạy theo danh mục |
| 8 | Brand Carousel | `shop_brand_slider` | Thanh cuộn thương hiệu/đối tác |
| 9 | Testimonial | `shop_testimonial` | Đánh giá khách hàng slider/grid |
| 10 | Latest Blog | `shop_latest_blog` | Lưới bài viết blog mới nhất |
| 11 | Instagram Feed | `shop_instagram_feed` | Hiển thị ảnh Instagram |
| 12 | Newsletter | `shop_newsletter` | Đăng ký nhận tin với nền màu/ảnh |
| 13 | Service Features | `shop_service_features` | Icon dịch vụ: Free Ship, Return, Support... |
| 14 | Flash Sale | `shop_countdown_deal` | Sản phẩm kèm đồng hồ đếm ngược flash sale |
| 15 | Video Banner | `shop_video_banner` | Banner với nền video autoplay |
| 16 | Lookbook | `shop_lookbook` | Ảnh lookbook với tag sản phẩm |

### B. Các trang con cần hỗ trợ

| Trang | Mô tả |
|---|---|
| **Shop** | Danh sách sản phẩm với filter sidebar |
| **Product Detail** | Chi tiết sản phẩm, swatch, gallery |
| **Cart** | Giỏ hàng |
| **Checkout** | Trang thanh toán |
| **Blog Grid** | Danh sách bài viết dạng lưới |
| **Blog Post** | Chi tiết bài viết |

---

## Danh sách Widget đã XÓA (cũ)

```
✅ app/Widgets/Hero/HeroWidget.php          — Đã xóa
✅ app/Widgets/Hero/FeaturesWidget.php      — Đã xóa
✅ app/Widgets/Hero/BentoGridHomeWidget.php — Đã xóa
✅ app/Widgets/Marketing/CtaWidget.php      — Đã xóa
✅ app/Widgets/Marketing/NewsletterWidget.php — Đã xóa
✅ app/Widgets/Marketing/TestimonialWidget.php — Đã xóa
✅ app/Widgets/Slider/PostSliderWidget.php  — Đã xóa
✅ app/Widgets/Victorious/                  — Đã xóa toàn bộ
```

**Giữ lại:**
- `Product/` (5 widgets product hiện có)
- `News/` widgets
- `Post/PostListWidget.php`
- `Analytics/AnalyticsWidget.php`
- `Category/HomeCateWidget.php`

---

## Cấu trúc thư mục mới

```
app/Widgets/
├── Shop/                        ← Thư mục widget mới
│   ├── TopBannerWidget.php      ✅ Đã tạo
│   ├── HeroSliderWidget.php
│   ├── CategoryBannerWidget.php
│   ├── FeaturedProductsWidget.php
│   ├── PromoBannerWidget.php
│   ├── BestsellerProductsWidget.php
│   ├── BrandSliderWidget.php
│   ├── TestimonialWidget.php
│   ├── LatestBlogWidget.php
│   ├── InstagramFeedWidget.php
│   ├── NewsletterWidget.php
│   ├── ServiceFeaturesWidget.php
│   ├── CountdownDealWidget.php
│   ├── VideoBannerWidget.php
│   └── LookbookWidget.php

public/
└── theme/                       ← CSS/JS assets (đã copy)
    ├── css/
    ├── js/
    ├── libs/
    └── fonts/
```

---

## Chi tiết Settings từng Widget

### 1. `shop_top_banner` — Top Banner
- `text` (text): Nội dung banner
- `link` / `link_text` (url/text)
- `show_countdown` (checkbox)
- `countdown_target` (date)
- `bg_color` / `text_color` (color)

### 2. `shop_hero_slider` — Hero Slider
- `slides` (repeatable):
  - `image` (image), `badge` (text), `title` (text), `subtitle` (text)
  - `btn_text` (text), `btn_link` (url)
  - `content_align` (select): left/center/right
  - `text_color` (color)
- `autoplay` (checkbox), `autoplay_speed` (number)
- `show_arrows` / `show_dots` (checkbox)
- `height` (select): small/medium/large/fullscreen

### 3. `shop_category_banner` — Category Banner Grid
- `title` (text)
- `layout` (select): 2-col/3-col/4-col
- `categories` (repeatable):
  - `image` (image), `label` (text), `link` (url)
  - `category_id` (taxonomy)

### 4. `shop_featured_products` — Featured Products
- `title` / `subtitle` (text)
- `source` (select): featured/new/sale/category
- `category_id` (taxonomy)
- `limit` (number)
- `layout` (select): grid-2/grid-3/grid-4/slider
- `show_quick_view` / `show_wishlist` / `show_quick_shop` (checkbox)

### 5. `shop_promo_banner` — Promo Banner
- `layout` (select): 1-col/2-col/3-col/1-2/2-1
- `banners` (repeatable):
  - `image` (image), `title` / `subtitle` (text)
  - `btn_text` (text), `btn_link` (url)
  - `overlay_color` (color)
  - `content_position` (select)

### 6. `shop_bestseller_products` — Best Seller Tabs
- `title` (text)
- `tabs` (repeatable):
  - `tab_label` (text), `category_id` (taxonomy), `limit` (number)
- `layout` (select): grid/slider

### 7. `shop_service_features` — Service Features Bar
- `items` (repeatable): `icon` (text), `title` (text), `desc` (text)
- `style` (select): horizontal/vertical/card
- `columns` (select): 3/4/5

### 8. `shop_testimonial` — Testimonial
- `title` (text)
- `items` (repeatable): `avatar` (image), `name` / `role` / `content` (text), `rating` (number)
- `layout` (select): slider/grid

### 9. `shop_newsletter` — Newsletter
- `title` / `subtitle` / `placeholder` / `btn_text` (text)
- `bg_type` (select): color/image
- `bg_color` (color), `bg_image` (image)

### 10. `shop_countdown_deal` — Flash Sale Countdown
- `title` (text)
- `end_date` (date)
- `product_ids` (relationship)
- `limit` (number), `bg_color` (color)

### 11. `shop_brand_slider` — Brand Carousel
- `title` (text)
- `brands` (repeatable): `logo` (image), `link` (url), `name` (text)
- `speed` (number)

### 12. `shop_latest_blog` — Latest Blog
- `title` (text), `limit` (number)
- `layout` (select): grid-2/grid-3/grid-4/slider
- `show_excerpt` / `show_date` (checkbox)
- `category_id` (taxonomy)

### 13. `shop_lookbook` — Lookbook
- `image` (image)
- `tags` (repeatable): `x_pos` (number), `y_pos` (number), `product_id` (post_object)

### 14. `shop_video_banner` — Video Banner
- `video_url` (url), `poster_image` (image)
- `title` / `subtitle` / `btn_text` (text), `btn_link` (url)
- `overlay_opacity` (range)

---

## Thứ tự triển khai

### Phase 1: Chuẩn bị ✅ Hoàn thành
- [x] Xóa các Widget cũ không cần thiết
- [x] Tạo thư mục `app/Widgets/Shop/`
- [x] Copy CSS/JS assets sang `public/theme/`
- [ ] Cập nhật layout Blade frontend để load theme assets

### Phase 2: Core Widgets
- [x] `shop_top_banner`
- [ ] `shop_hero_slider`
- [ ] `shop_service_features`
- [ ] `shop_category_banner`
- [ ] `shop_promo_banner`

### Phase 3: Product Widgets
- [ ] `shop_featured_products`
- [ ] `shop_bestseller_products`
- [ ] `shop_countdown_deal`
- [ ] `shop_brand_slider`

### Phase 4: Content Widgets
- [ ] `shop_testimonial`
- [ ] `shop_newsletter`
- [ ] `shop_latest_blog`
- [ ] `shop_lookbook`
- [ ] `shop_video_banner`

### Phase 5: Đăng ký và kiểm tra
- [ ] Đăng ký tất cả widget vào `WidgetRegistry.php`
- [ ] Xóa entries cũ khỏi WidgetRegistry
- [ ] Kiểm tra `/admin/widgets`
- [ ] Test từng widget trên frontend

---

## Verification Plan

1. Kiểm tra Admin `/admin/widgets` — thấy đầy đủ danh sách 14 widget mới
2. Tạo thử từng widget, cấu hình settings, lưu và preview
3. Kiểm tra render đúng HTML trên frontend
4. Test responsive (mobile/tablet/desktop)
5. Kiểm tra source HTML trang không có từ khóa tên theme

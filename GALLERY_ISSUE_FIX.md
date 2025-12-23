# Sửa lỗi Gallery hiển thị sai chỗ

## Vấn đề
Khi chọn ảnh cho Gallery sản phẩm:
- Ảnh hiển thị ở phần "Ảnh đại diện" thay vì Gallery
- Chỉ hiển thị 1 hình thay vì nhiều hình
- Gallery vẫn trống `[]`

## Nguyên nhân
1. **Logic xử lý media selection sai**: `handleMediaSelected` không phân biệt đúng giữa gallery mode và featured image mode
2. **Gallery mode flags bị reset**: `currentGalleryMode` có thể bị reset bởi media manager trước khi `handleMediaSelected` được gọi
3. **Data structure không đúng**: Event từ media manager có structure khác với expected

## Các thay đổi đã thực hiện

### 1. Cải thiện logic `handleMediaSelected`
```javascript
// Trước (SAI):
const items = e.detail.files || e.detail.items || [];

// Sau (ĐÚNG):
const items = (e.detail.files && e.detail.files.length > 0) ? e.detail.files : 
             (e.detail.items && e.detail.items.length > 0) ? e.detail.items : 
             [];
```

### 2. Ưu tiên `pendingGalleryMode`
```javascript
// Ưu tiên pendingGalleryMode trước - đây là flag chính cho gallery
if (this.pendingGalleryMode || this.currentGalleryMode) {
    // Xử lý gallery
} else {
    // Xử lý featured image
}
```

### 3. Reset gallery mode khi chọn featured image
```javascript
@click="
    currentGalleryMode = false; 
    pendingGalleryMode = false;
    console.log('Featured image button clicked - Gallery mode reset');
"
```

### 4. Thêm validation và logging
- Kiểm tra `items.length === 0` trước khi xử lý
- Log chi tiết các bước xử lý
- Thêm debug buttons để test

## Cách test

### Bước 1: Kiểm tra trạng thái ban đầu
1. Mở trang edit sản phẩm
2. Mở browser console (F12)
3. Click button "Debug Gallery State"
4. Kiểm tra output:
```javascript
Current gallery state: []
Gallery modes: {currentGalleryMode: false, pendingGalleryMode: false}
```

### Bước 2: Test chọn ảnh cho Gallery
1. Click "+ Thêm ảnh gallery"
2. Kiểm tra console log: `Gallery button clicked - Mode set to: {currentGalleryMode: true, pendingGalleryMode: true}`
3. Chọn một hoặc nhiều ảnh từ media library
4. Click "Chọn ảnh"
5. Kiểm tra console logs:
   - `handleMediaSelected called with:`
   - `pendingGalleryMode: true`
   - `Adding to gallery, current gallery:`
   - `Added to gallery: [URL]`
   - `Gallery after adding:`

**Kết quả mong đợi:**
- Ảnh xuất hiện trong phần Gallery (không phải Featured Image)
- Gallery count tăng lên
- Có thể chọn nhiều ảnh cùng lúc

### Bước 3: Test chọn ảnh đại diện
1. Scroll lên phần "Ảnh đại diện"
2. Click vào media manager trong phần này
3. Kiểm tra console: `Featured image button clicked - Gallery mode reset`
4. Chọn 1 ảnh
5. Kiểm tra console: `Setting featured image:`

**Kết quả mong đợi:**
- Ảnh xuất hiện trong phần "Ảnh đại diện"
- Gallery không bị thay đổi

### Bước 4: Test manual add
1. Click button "Test Gallery Add"
2. Kiểm tra console và UI
3. Ảnh test màu đỏ sẽ xuất hiện trong gallery

## Nếu vẫn có lỗi

### Lỗi 1: Ảnh vẫn hiển thị sai chỗ
**Kiểm tra:**
- Console log `pendingGalleryMode` có `true` không khi chọn gallery?
- Event data có đúng structure không?

**Debug:**
```javascript
// Trong browser console
console.log('Current Alpine data:', Alpine.store('productForm') || window.Alpine.data);
```

### Lỗi 2: Không có ảnh nào được thêm
**Kiểm tra:**
- `items` array có data không?
- `item.url` có tồn tại không?

**Debug:**
```javascript
// Thêm vào handleMediaSelected
console.log('Event detail structure:', JSON.stringify(e.detail, null, 2));
```

### Lỗi 3: Gallery không save
**Kiểm tra:**
- Form có submit đúng data không?
- Controller có nhận được gallery array không?

**Debug:**
- Click "Debug Gallery State" trước khi submit
- Kiểm tra Laravel logs: `Get-Content storage/logs/laravel.log -Tail 20`

## Temporary workaround

Nếu vẫn không hoạt động, thêm code này vào browser console:

```javascript
// Force set gallery mode
window.forceGalleryMode = function() {
    const component = Alpine.store('productForm');
    component.pendingGalleryMode = true;
    component.currentGalleryMode = true;
    console.log('Gallery mode forced to true');
};

// Manual add image to gallery
window.addToGallery = function(url) {
    const component = Alpine.store('productForm');
    if (!component.gallery.includes(url)) {
        component.gallery.push(url);
        console.log('Added to gallery:', url);
    }
};
```

Sau đó:
1. Gọi `forceGalleryMode()` trước khi chọn ảnh
2. Hoặc dùng `addToGallery('URL_CỦA_ẢNH')` để thêm trực tiếp
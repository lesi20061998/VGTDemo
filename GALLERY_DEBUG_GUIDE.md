# Gallery Debug Guide - FIXED

## Vấn đề đã được khắc phục: Gallery sản phẩm không hiển thị preview khi chọn media

### Root Cause:
- `currentGalleryMode` bị reset về `false` trước khi `handleMediaSelected` được gọi
- Media Manager component close modal và reset state trước khi event được xử lý

### Solution Applied:
- Thêm `pendingGalleryMode` để lưu trạng thái gallery mode
- Cập nhật `handleMediaSelected` để kiểm tra cả `currentGalleryMode` và `pendingGalleryMode`
- Reset cả hai biến sau khi xử lý xong

### Expected Console Output (FIXED):

```
Gallery mode set to true, pending: true
Media selected event received: {files: [{...}]}
handleMediaSelected called with: {files: [{...}]}
currentGalleryMode: false (có thể bị reset bởi media manager)
pendingGalleryMode: true (được giữ nguyên)
Processing for main product
Adding to gallery, current gallery: [...]
Added to gallery: https://...
Gallery after adding: [...]
```

### Test Steps:

1. **Mở trang edit sản phẩm**
2. **Click "Thêm ảnh gallery"** 
   - Should see: `Gallery mode set to true, pending: true`
3. **Chọn ảnh từ media manager**
   - Should see: `pendingGalleryMode: true`
   - Should see: `Adding to gallery`
4. **Kiểm tra gallery hiển thị**
   - Images should appear in gallery grid
   - Gallery count should increase

### Debug Commands:

```javascript
// Check both gallery modes
console.log('Gallery modes:', {
    current: this.currentGalleryMode,
    pending: this.pendingGalleryMode,
    gallery: this.gallery
});

// Manual test
this.pendingGalleryMode = true;
// Then select media
```

### Status: ✅ FIXED
- Gallery mode persistence issue resolved
- Media selection now properly adds to gallery
- Debug logging enhanced for better troubleshooting
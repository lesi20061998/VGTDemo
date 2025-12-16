@props(['contentType' => 'product'])

<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Phân tích SEO
        </h2>
        <div class="flex items-center gap-2">
            <span class="text-2xl font-bold" :class="{
                'text-red-600': seoScore < 50,
                'text-yellow-600': seoScore >= 50 && seoScore < 80,
                'text-green-600': seoScore >= 80
            }" x-text="seoScore">0</span>
            <span class="text-xs text-gray-500">/100</span>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="mb-6">
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="h-3 rounded-full transition-all duration-500" :style="'width: ' + seoScore + '%'" :class="{
                'bg-red-500': seoScore < 50,
                'bg-yellow-500': seoScore >= 50 && seoScore < 80,
                'bg-green-500': seoScore >= 80
            }"></div>
        </div>
        <p class="text-xs text-gray-500 mt-1" x-text="seoScore < 50 ? 'Cần cải thiện' : (seoScore < 80 ? 'Khá tốt' : 'Xuất sắc')"></p>
    </div>

    <!-- SEO Checklist -->
    <div class="space-y-3 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
            </svg>
            Danh sách kiểm tra
        </h3>
        
        <div class="space-y-2">
            <template x-for="(check, index) in seoChecks" :key="index">
                <div class="flex items-start p-2 rounded-lg hover:bg-gray-50 transition" :class="check.status === 'success' ? 'bg-green-50' : (check.status === 'warning' ? 'bg-yellow-50' : 'bg-red-50')">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" :class="check.status === 'success' ? 'text-green-600' : (check.status === 'warning' ? 'text-yellow-600' : 'text-red-600')" fill="currentColor" viewBox="0 0 20 20">
                        <path x-show="check.status === 'success'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                        <path x-show="check.status === 'warning'" fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path>
                        <path x-show="check.status === 'error'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium" :class="check.status === 'success' ? 'text-green-800' : (check.status === 'warning' ? 'text-yellow-800' : 'text-red-800')" x-text="check.title"></p>
                        <p class="text-xs mt-0.5" :class="check.status === 'success' ? 'text-green-600' : (check.status === 'warning' ? 'text-yellow-600' : 'text-red-600')" x-text="check.message"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- SEO Fields -->
    <div class="space-y-4 border-t pt-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề SEO</label>
            <input type="text" name="meta_title" x-model="metaTitle" maxlength="60"
                   @input="analyzeSeo()"
                   placeholder="Để trống để dùng tiêu đề {{ $contentType }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            <div class="flex justify-between items-center mt-1">
                <p class="text-xs text-gray-500"><span x-text="metaTitle.length">0</span>/60 ký tự</p>
                <span x-show="metaTitle.length >= 50 && metaTitle.length <= 60" class="text-xs text-green-600">✓ Độ dài tốt</span>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả SEO</label>
            <textarea name="meta_description" x-model="metaDesc" rows="3" maxlength="160"
                      @input="analyzeSeo()"
                      placeholder="Mô tả ngắn gọn, hấp dẫn cho kết quả tìm kiếm" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></textarea>
            <div class="flex justify-between items-center mt-1">
                <p class="text-xs text-gray-500"><span x-text="metaDesc.length">0</span>/160 ký tự</p>
                <span x-show="metaDesc.length >= 120 && metaDesc.length <= 160" class="text-xs text-green-600">✓ Độ dài tốt</span>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Từ khóa chính</label>
            <input type="text" name="focus_keyword" x-model="keyword"
                   @input="analyzeSeo()"
                   placeholder="VD: áo thun nam" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">Từ khóa bạn muốn xếp hạng trên Google</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Schema Markup</label>
            <select name="schema_type" x-model="schemaType" @change="analyzeSeo()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Không sử dụng</option>
                <option value="Product">Product (Sản phẩm)</option>
                <option value="Article">Article (Bài viết)</option>
                <option value="Organization">Organization (Tổ chức)</option>
                <option value="LocalBusiness">LocalBusiness (Doanh nghiệp địa phương)</option>
                <option value="FAQPage">FAQPage (Trang FAQ)</option>
                <option value="HowTo">HowTo (Hướng dẫn)</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Giúp Google hiểu rõ hơn về nội dung</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Canonical URL</label>
            <input type="url" name="canonical_url" x-model="canonicalUrl" @input="analyzeSeo()"
                   placeholder="https://example.com/san-pham" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">URL chính thức để tránh nội dung trùng lặp</p>
        </div>

        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center">
                <input type="checkbox" name="noindex" x-model="noindex" @change="analyzeSeo()" class="rounded border-gray-300 text-blue-600">
                <label class="ml-2 text-sm text-gray-700">Không cho phép index (noindex)</label>
            </div>
        </div>
    </div>
</div>

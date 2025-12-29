

<?php $__env->startSection('title', 'Quản lý bài viết'); ?>
<?php $__env->startSection('page-title', 'Bài viết & Trang'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header với nút thêm mới -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Bài viết & Trang</h1>
                <p class="text-sm text-gray-500">Quản lý nội dung website</p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(isset($currentProject) ? route('project.admin.posts.create', $currentProject->code) : '#'); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    + Thêm bài viết
                </a>
                <a href="<?php echo e(isset($currentProject) ? route('project.admin.pages.create', $currentProject->code) : '#'); ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    + Thêm trang
                </a>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm kiếm..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="post_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả loại</option>
                    <option value="post" <?php echo e(request('post_type') === 'post' ? 'selected' : ''); ?>>Bài viết</option>
                    <option value="page" <?php echo e(request('post_type') === 'page' ? 'selected' : ''); ?>>Trang</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="published" <?php echo e(request('status') === 'published' ? 'selected' : ''); ?>>Đã xuất bản</option>
                    <option value="draft" <?php echo e(request('status') === 'draft' ? 'selected' : ''); ?>>Nháp</option>
                    <option value="archived" <?php echo e(request('status') === 'archived' ? 'selected' : ''); ?>>Lưu trữ</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách bài viết -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tác giả</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->featured_image): ?>
                                <img src="<?php echo e($post->featured_image); ?>" alt="" class="w-10 h-10 rounded object-cover mr-3">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($post->title); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($post->slug); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo e($post->post_type === 'page' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'); ?>">
                                <?php echo e($post->post_type === 'page' ? 'Trang' : 'Bài viết'); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo e($post->author->name ?? 'N/A'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                <?php echo e($post->status === 'published' ? 'bg-green-100 text-green-800' : 
                                   ($post->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')); ?>">
                                <?php echo e($post->status === 'published' ? 'Đã xuất bản' : 
                                   ($post->status === 'draft' ? 'Nháp' : 'Lưu trữ')); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($post->created_at->format('d/m/Y')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?php echo e(isset($currentProject) ? route('project.admin.posts.edit', [$currentProject->code, $post]) : '#'); ?>" class="text-blue-600 hover:text-blue-900">Sửa</a>
                                <form method="POST" action="<?php echo e(isset($currentProject) ? route('project.admin.posts.destroy', [$currentProject->code, $post]) : '#'); ?>" class="inline" 
                                      onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-medium">Chưa có bài viết nào</p>
                                <p class="text-sm">Bắt đầu tạo bài viết đầu tiên của bạn</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($posts->links()); ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('cms.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/cms/posts/index.blade.php ENDPATH**/ ?>
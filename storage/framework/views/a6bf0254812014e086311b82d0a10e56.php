

<?php $__env->startSection('title', 'Sửa Widget Template'); ?>
<?php $__env->startSection('page-title', 'Sửa Widget Template'); ?>

<?php $__env->startSection('content'); ?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.widget-template-builder', ['id' => $id]);

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-29915935-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('cms.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\core_laravel\Core_system\resources\views/cms/widget-templates/edit.blade.php ENDPATH**/ ?>
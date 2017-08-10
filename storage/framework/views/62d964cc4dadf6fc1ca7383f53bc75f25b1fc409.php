<html>
    <head>
        <title>五常大米-<?php echo e($app_name); ?></title>
        <meta charset="utf-8">
        <meta name="keywords" content="五常大米">
        <meta name="description" content="五常大米专卖">
        <script src="<?php echo e(URL::asset('js/jquery.js')); ?>"></script>
        <script src="<?php echo e(URL::asset('js/show.js')); ?>"></script>
        <link rel="stylesheet" href="<?php echo e(URL::asset('css/style.css')); ?>">
        <?php echo $__env->yieldContent('css'); ?>
        <?php echo $__env->yieldContent('js'); ?>
    </head>
    <body>
        <?php echo $__env->make('component.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
        <?php echo $__env->make('component.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </body>
</html>
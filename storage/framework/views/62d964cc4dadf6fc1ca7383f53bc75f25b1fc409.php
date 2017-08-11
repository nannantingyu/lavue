<html>
    <head>
        <title>
            <?php if(defined("seo_title")): ?>
            <?php echo e($seo_title); ?>

            <?php else: ?>
            <?php echo e($default_seo_title); ?>

            <?php endif; ?>
        </title>
        <meta charset="utf-8">
        <?php if(defined("seo_keywords")): ?>
        <meta name="keywords" content="<?php echo e($seo_keywords); ?>">
        <?php else: ?>
        <meta name="keywords" content="<?php echo e($default_seo_title); ?>">
        <?php endif; ?>
        <?php if(defined("seo_description")): ?>
        <meta name="description" content="<?php echo e($seo_description); ?>">
        <?php else: ?>
        <meta name="description" content="<?php echo e($default_seo_description); ?>">
        <?php endif; ?>
        <script src="<?php echo e(URL::asset('js/jquery.js')); ?>"></script>
        <script src="<?php echo e(URL::asset('js/show.js')); ?>"></script>
        <link rel="stylesheet" href="<?php echo e(URL::asset('css/style.css')); ?>">
        <?php echo $__env->yieldContent('css'); ?>
        <?php echo $__env->yieldContent('js'); ?>
    </head>
    <body>
    <?php 
     ?>
        <?php echo $__env->make('public.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
        <?php echo $__env->make('public.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </body>
</html>
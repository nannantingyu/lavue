<?php $__env->startSection('content'); ?>
    <div class="main-content">
        <div class="con-list">
            <ul>
                <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <div class="title"><a href="<?php echo e($base_url); ?>blog_<?php echo e($vo->id); ?>"><?php echo e($vo->title); ?></a></div>
                        <div class="time"><?php echo e(date("Y-m-d H:i:s", strtotime($vo->publish_time))); ?></div>
                        <div class="clear"></div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div class="page">
            <?php echo e($articles->links()); ?>

        </div>
    </div>
 <?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
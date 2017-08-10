<?php $__env->startSection('content'); ?>
    <div class="main-content detail-main">
        <div class="content-left">
            <h2><?php echo e($article->title); ?></h2>
            <p class="right-con">
                <span>lavue</span>
                <span><?php echo e(date("Y-m-d H:i", strtotime($article->publish_time))); ?></span>
                <span><?php echo e($article->hits); ?></span>
            </p>
            <?php echo $article->body; ?>

        </div>
        <div class="content-right">
            <h2 class="sub-title left-con">热门阅读</h2>
            <ul class="news">
                <?php $__currentLoopData = $hot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e($base_url); ?>blog_<?php echo e($art->id); ?>"><?php echo e($art->title); ?></a><span><?php echo e(date("H:i", strtotime($art->publish_time))); ?></span></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div class="content-right">
            <h2 class="sub-title left-con">相关推荐</h2>
            <ul class="news">
                <?php $__currentLoopData = $hot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e($base_url); ?>blog_<?php echo e($art->id); ?>"><?php echo e($art->title); ?></a><span><?php echo e(date("H:i", strtotime($art->publish_time))); ?></span></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div class="content-right">
            <h2 class="sub-title left-con">猜你喜欢</h2>
            <ul class="news">
                <?php $__currentLoopData = $hot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e($base_url); ?>blog_<?php echo e($art->id); ?>"><?php echo e($art->title); ?></a><span><?php echo e(date("H:i", strtotime($art->publish_time))); ?></span></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div class="content-right">
            <h2 class="sub-title left-con">最新进展</h2>
            <ul class="news">
                <?php $__currentLoopData = $hot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $art): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e($base_url); ?>blog_<?php echo e($art->id); ?>"><?php echo e($art->title); ?></a><span><?php echo e(date("H:i", strtotime($art->publish_time))); ?></span></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
    <script>
        $.get("/incre_<?php echo e($article->id); ?>", {}, function(){});
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
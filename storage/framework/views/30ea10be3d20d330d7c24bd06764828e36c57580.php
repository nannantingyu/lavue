<?php $__env->startSection('js'); ?>
	<script src="<?php echo e(asset('js/jquery.nivo.slider.js')); ?>"></script>
	<script src="<?php echo e(asset('js/index.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
	<link rel="stylesheet" href="<?php echo e(asset('css/nivo-slider.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
	<?php echo $__env->make('public.navi', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<div class="main-content">
		<div class="main-left">
			<section class="carousel">
				<a href="#"><img src="<?php echo e(asset('images/lunbo1.jpg')); ?>" title="This is an example of a caption" ></a>
				<img src="<?php echo e(asset('images/lunbo6.jpg')); ?>" title="This is an example of a caption" />
				<a href="http://dev7studios.com"><img src="<?php echo e(asset('images/lunbo7.jpg')); ?>" title="This is an example of a caption" /></a>
				<img src="<?php echo e(asset('images/lunbo4.jpg')); ?>" title="This is an example of a caption" />
				<img src="<?php echo e(asset('images/lunbo5.jpg')); ?>" title="This is an example of a caption" />
			</section>
			<h2 class="sub-title">即时资讯</h2>
			<ul class="news">
				<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<li>
						<a href="<?php echo e($base_url); ?>watch_<?php echo e($article->id); ?>"><?php echo e($article->title); ?></a>
						<span><?php echo e($article->publish_time); ?></span>
					</li>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>

			<h2 class="sub-title">种植技术</h2>
			<ul class="news">
				<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<li>
						<a href="<?php echo e($base_url); ?>watch_<?php echo e($article->id); ?>"><?php echo e($article->title); ?></a>
						<span><?php echo e($article->publish_time); ?></span>
					</li>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>

			<h2 class="sub-title">搞笑中场</h2>
			<div class="sub-img">
				<dl>
					<dd><img src="<?php echo e(asset('images/lunbo4.jpg')); ?>"></dd>
					<dt>
						去练车的时候，有个美女把安全带系到了副驾驶上
						教练问她没感觉不对啊
						姑娘说没有啊
						教练指着安全带的卡带说
					</dt>
				</dl>
				<dl>
					<dd><img src="<?php echo e(asset('images/lunbo4.jpg')); ?>"></dd>
					<dt>
						去练车的时候，有个美女把安全带系到了副驾驶上
						教练问她没感觉不对啊
						姑娘说没有啊
						教练指着安全带的卡带说
					</dt>
				</dl>
			</div>

			<ul class="news">
				<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<li>
						<a href="<?php echo e($base_url); ?>watch_<?php echo e($article->id); ?>"><?php echo e($article->title); ?></a>
						<span><?php echo e($article->publish_time); ?></span>
					</li>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>

			<h2 class="sub-title">健康讲堂</h2>
			<ul class="news">
				<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<li>
						<a href="<?php echo e($base_url); ?>watch_<?php echo e($article->id); ?>"><?php echo e($article->title); ?></a>
						<span><?php echo e($article->publish_time); ?></span>
					</li>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>

			<h2 class="sub-title">微博微言</h2>
			<ul class="news">
				<li><a href="#">恒大官宣穆里奇自由身回归签半年</a><span>10:00</span></li>
				<li><a href="#">AC米兰热身赛多将发挥抢眼！中场补腰势在必行</a><span>11:00</span></li>
				<li><a href="#">《极限》导演揭秘张艺兴为何节目录了还没签约</a><span>12:00</span></li>
				<li><a href="#">温网场边太太团闪耀！众星女伴大盘点</a><span>13:00</span></li>
				<li><a href="#">印度山地军后方出现3万游击队：袭击印军缴获武器</a><span>14:00</span></li>
				<li><a href="#">印度山地军后方出现3万游击队：袭击印军缴获武器</a><span>14:00</span></li>
				<li><a href="#">印度山地军后方出现3万游击队：袭击印军缴获武器</a><span>14:00</span></li>
			</ul>
		</div>

		<div class="main-right">
			<ul class="tab-title">
				<li class='current'><a href="#">热门博文</a></li>
				<li><a href="#">种植技术</a></li>
				<li><a href="#">天下猎奇</a></li>
				<div class="clear"></div>
			</ul>
			<section class="hot-blog clear">
				<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="one-blog">
						<p class="blog-title"><a href="<?php echo e($base_url); ?>blog_<?php echo e($article->id); ?>"><?php echo e($article->title); ?></a></p>
						<p class="blog-desc"><?php echo e($article->description); ?></p>
						<div class="blog-info">
							<span class="blog-time"><?php echo e(date("Y-m-d", strtotime($article->publish_time))); ?></span>
							<span class="blog-author"><a href="#">别山举水</a></span>
						</div>
					</div>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</section>
			<section class="hot-blog clear none">
				<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="one-blog">
						<p class="blog-title"><a href="<?php echo e($base_url); ?>blog_<?php echo e($article->id); ?>"><?php echo e($article->title); ?></a></p>
						<p class="blog-desc"><?php echo e($article->description); ?></p>
						<div class="blog-info">
							<span class="blog-time"><?php echo e(date("Y-m-d", strtotime($article->publish_time))); ?></span>
							<span class="blog-author"><a href="#">别山举水</a></span>
						</div>
					</div>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</section>
			<section class="hot-blog clear none">
				<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="one-blog">
						<p class="blog-title"><a href="<?php echo e($base_url); ?>blog_<?php echo e($article->id); ?>"><?php echo e($article->title); ?></a></p>
						<p class="blog-desc"><?php echo e($article->description); ?></p>
						<div class="blog-info">
							<span class="blog-time"><?php echo e(date("Y-m-d", strtotime($article->publish_time))); ?></span>
							<span class="blog-author"><a href="#">别山举水</a></span>
						</div>
					</div>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</section>
			<img src="<?php echo e(asset('images/ad1.jpg')); ?>">
			<h2 class="sub-title">热卖单品</h2>
			<section class="hot-goods">
				<dl>
					<dd><a href="#"><img src="<?php echo e(asset('images/lunbo1.jpg')); ?>"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="#"><img src="<?php echo e(asset('images/lunbo2.jpg')); ?>"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="#"><img src="<?php echo e(asset('images/lunbo3.jpg')); ?>"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="#"><img src="<?php echo e(asset('images/lunbo4.jpg')); ?>"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
				<dl>
					<dd><a href="#"><img src="<?php echo e(asset('images/lunbo5.jpg')); ?>"></a></dd>
					<dt>
					<p>¥20</p>
					<a href="#">五常稻花香</a>
					</dt>
				</dl>
			</section>

			<h2 class="sub-title">健康频道</h2>
			<section class="healthy">
				<ul>
					<?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<li>
							<div class="info-text">
								<h3><a href="/blog_<?php echo e($article->id); ?>"><?php echo e($article->title); ?></a></h3>
								<p><?php echo e($article->description); ?></p>
								<?php 
									$keywords = explode(",", $article->keywords); $length = count($keywords)-1;
								 ?>
								<span>
									<?php $__currentLoopData = $keywords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keys): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<a href="/search_<?php echo e($keys); ?>_1"><?php echo e($keys); ?></a>，
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</span>
								<span><?php echo e(date("Y-m-d H:i:s", strtotime($article->publish_time))); ?></span>
							</div>
							<div class="info-img">
								<img src="<?php echo e(asset('images/lunbo4.jpg')); ?>">
							</div>
							<div class="clear"></div>
						</li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</ul>
			</section>
		</div>
	</div>

	<!-- 友情链接 -->
	<?php echo $__env->make("public.friendlink", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
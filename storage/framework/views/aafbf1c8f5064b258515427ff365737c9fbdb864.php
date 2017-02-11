<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col s12">
            <h1 class="topic-list-header">Department of Economics</h1>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <div class="row subtopic-cards">
                <div class="col s12">

                    <div class="row">

                        <?php $__currentLoopData = $taxonomy; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $config => $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <div class="col s3">
                                <a class="topic-container" href="<?php echo e(route('lti.config', $config)); ?>">
                                    <img src="<?php echo e((array_key_exists('img', $item) && $item['img'] != '') ? ((strpos($item['img'], 'http') !== false) ? $item['img'] : '/vendor/storyline/core/images/' . $item['img']) : 'http://placehold.it/240x200'); ?>" alt="" class="img responsive-img">
                                    <div class="card-title">
                                        <?php echo e($item['title']); ?>

                                    </div>
                                    <!-- Hover description -->
                                    <!-- <div class="card-description">
                                        
                                    </div> -->
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('eon.lti::app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
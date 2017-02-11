<?php
/**
 * Consume all the API requests
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Consume</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <a href="<?php echo e(url()->previous()); ?>">Back to list</a>

    <br /><br />

    <?php $__currentLoopData = $responses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $response_key => $response): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>

        <h4><?php echo e($response_key); ?></h4>

        <?php if(!is_null($response['template'])): ?>

            <?php $str = 'templates.' . $response['key'] . '.' . $response['template']; ?>

            <?php for($i = 0; $i < count($response['response']); $i++): ?>
                <?php echo $__env->make($str, ['response' => $response['response'][$i]], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php endfor; ?>

        <?php else: ?>
            <pre>
                <?php print_r($response['response']) ?>
            </pre>
        <?php endif; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
</div>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>
</html>
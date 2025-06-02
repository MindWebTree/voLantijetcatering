<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500" rel="stylesheet" type="text/css">
    </head>

    <body style="font-family: montserrat, sans-serif;">
        <div style="max-width: 1000px; margin-left: auto; margin-right: auto;">
            <div style="text-align: center;">
                <?php echo e($header ?? ''); ?>

            </div>

            <div>
                <?php echo e($slot); ?>


                <?php echo e($subcopy ?? ''); ?>

            </div>
        </div>
    </body>
</html>
<?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Shop/src/Providers/../Resources/views/emails/layouts/master.blade.php ENDPATH**/ ?>
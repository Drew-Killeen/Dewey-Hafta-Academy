<?php if($_SESSION['msg']['err']): ?>

<script src="../templates/notificationFx.js"></script>
<script>
    // create the notification
    var notification = new NotificationFx({
        message : <?php echo "'".$_SESSION['msg']['err']."'"; ?>,
        layout : 'attached',
        effect : 'flip',
        type : 'notice', // notice, warning or error
        ttl : 30000
    });

    // show the notification
    notification.show();
</script>

<?php   unset($_SESSION['msg']['err']);
        elseif($_SESSION['msg']['success']): ?>

<script src="../templates/notificationFx.js"></script>
<script>
    var notification = new NotificationFx({
                message : <?php echo "'".$_SESSION['msg']['success']."'"; ?>,
                layout : 'attached',
                effect : 'flip',
                type : 'notice', // notice, warning or error
                ttl : 30000
            });

            // show the notification
            notification.show();
</script>

<?php unset($_SESSION['msg']['success']);
      endif; ?>
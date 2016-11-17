<?php if($_SESSION['msg']['err']): ?>

<script src="../templates/classie.js"></script>
<script src="../templates/notificationFx.js"></script>
<script>
    var notification = new NotificationFx({
                message : <?php echo "'".$_SESSION['msg']['err']."'"; ?>,
                effect : 'flip',
                type : 'error', // notice, warning or error
                ttl : 30000
            });

            // show the notification
            notification.show();
</script>

<?php   unset($_SESSION['msg']['err']);
        elseif($_SESSION['msg']['success']): ?>

<script src="../templates/classie.js"></script>
<script src="../templates/notificationFx.js"></script>
<script>
    var notification = new NotificationFx({
                message : <?php echo "'".$_SESSION['msg']['success']."'"; ?>,
                effect : 'flip',
                type : 'notice', // notice, warning or error
                ttl : 30000
            });

            // show the notification
            notification.show();
</script>

<?php unset($_SESSION['msg']['success']);
      endif; ?>
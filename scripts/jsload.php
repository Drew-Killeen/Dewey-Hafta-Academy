<!--I'm here-->
<?php if($_SESSION['msg']['err']): ?>

<script src="../scripts/notificationFx.js"></script>
<script>
    console.log('error');
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

<script src="../scripts/notificationFx.js"></script>
<script>
    console.log('success');
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
      endif; 

    mysqli_close($link);
?>
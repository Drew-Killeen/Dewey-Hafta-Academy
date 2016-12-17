<?php if($_SESSION['id']) {
    echo 
    '<header> <span class="toggle-button">
                <div class="menu-bar menu-bar-top"></div>
                <div class="menu-bar menu-bar-middle"></div>
                <div class="menu-bar menu-bar-bottom"></div>
            </span>
        <div class="menu-wrap">
            <div class="menu-sidebar">
                <ul class="menu">
                    <li><a href="../main">Home</a></li>
                    <li><a href="../enroll">Enroll</a></li>
                    <li><a href="../course">View course</a></li>
                    <li><a href="../progress">Current progress</a></li>';
                    
                    if($_SESSION['privilege'] == 'admin' || $_SESSION['privilege'] == 'sysop') {
                    echo '<li class="menu-item-has-children"><a href="../admin/index">Admin</a> <span class="sidebar-menu-arrow"></span>
                            <ul class="sub-menu">
                                <li><a href="../admin/edit_course">Courses</a></li>
                                <li><a href="../admin/edit_exam">Exams</a></li>
                                <li><a href="../admin/users">Manage users</a></li>
                                <li><a href="../admin/mail">Send mail</a></li>
                            </ul>
                        </li>';
                    }
                    echo 
                '</ul>
            </div>
        </div>
    </header>';
}
?>
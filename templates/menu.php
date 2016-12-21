<?php if($_SESSION['id']) {
    echo 
    '<header> 
        <span class="toggle-button">
            <div class="menu-bar menu-bar-top"></div>
            <div class="menu-bar menu-bar-middle"></div>
            <div class="menu-bar menu-bar-bottom"></div>
        </span>
    <div class="menu-wrap">
        <div class="menu-sidebar">
            <ul class="menu nolist">
                <li><a href="../enroll/"><img src="../images/pencil-64.png" alt="Enroll" class="menu-icon"></a> <a href="../enroll" class="line">Enroll</a></li>
                <li><a href="../course/"><img src="../images/notebook-56.png" alt="Course" class="menu-icon"></a> <a href="../course" class="line">Course</a></li>
                <li><a href="../grade/"><img src="../images/checklist-56.png" alt="Progress" class="menu-icon"></a> <a href="../grade" class="line">Progress</a></li>';
                if($_SESSION['privilege'] == 'admin' || $_SESSION['privilege'] == 'sysop') {
                echo '<li class="menu-item-has-children"><a href="../admin/"><img src="../images/gear-64.png" alt="Admin" class="menu-icon"></a> <a href="../admin/index" class="line">Admin</a> <span class="sidebar-menu-arrow"></span>
                    <ul class="sub-menu nolist" style="line-height:1.2;">
                        <li><a href="../admin/edit_course" class="line">Courses</a></li>
                        <li><a href="../admin/edit_exam" class="line">Exams</a></li>
                        <li><a href="../admin/users" class="line">Manage users</a></li>
                        <li><a href="../admin/mail" class="line">Send mail</a></li>
                    </ul>
                </li>';
                }
                echo 
                '<li><a href="../about"><img src="../images/info-56.png" alt="View course" class="menu-icon"></a> <a href="../about" class="line">About</a></li>
                </ul>
            </div>
        </div>
    </header>';
}
?>
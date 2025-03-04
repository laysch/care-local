<aside id="sidebar" style="text-align: center;">
    <div id="sb-image">
        <svg id="svg_circle_text" viewBox="0 0 180 180">
            <text><textPath xlink:href="#circleTextPath">CareLocal</textPath></text>
        </svg>
        <svg id="svg_circle_line" viewBox="0 0 180 180">
            <use xlink:href="#circleLinePath"/>
        </svg>
        <a href="/" class="sbimage">
            <img src="https://example.com/sidebar-image.jpg" alt="Sidebar Image">
        </a>
    </div>
    <div id="sb-title">
        <div class="title-text"><b>Welcome to CareLocal</b></div>
        <div class="title-tail"></div>
    </div>
    <div id="sb-infobox" class="bothdisplay has--desc">
        <input type="checkbox" id="menutoggle" name="menutoggle">
        <div class="ib-toolbar">
            <a href="/" class="home_button" title="home">
                <i class="fi fi-rr-home"></i>
            </a>
            <a href="/ask" class="mail_button" title="ask">
                <i class="fi fi-rr-envelope"></i>
            </a>
            <form action="/search" method="get" id="searchbar">
                <input type="text" name="q" class="searchquery" placeholder="Search...">
            </form>
            <label for="menutoggle" class="menu_button">
                <i class="fi fi-rr-menu-burger"></i>
                <i class="fi fi-rr-user"></i>
            </label>
        </div>
        <div id="desc">
            <div class="desc-inner">Where Local Talent Meets Local Needs</div>
        </div>
        <nav id="menu">
            <a href="/">Home</a>
            <a href="/login.php">Login</a>
            <a href="/add-job.php">Add Job</a>
            <a href="/search-jobs.php">Search Jobs</a>
            <a href="/calendar.php">Calendar</a>
            <a href="/helpcenter.php">Help Center</a>
            <a href="/profile.php">Profile</a>
            <a href="/logout.php">log out</a>
        </nav>
    </div>
</aside>

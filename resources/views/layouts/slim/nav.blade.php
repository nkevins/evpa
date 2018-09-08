<ul class="nav">
  <li class="nav-item">
    <a class="nav-link" href="{{ url('/dashboard') }}">
      <i class="icon fas fa-home"></i>
      <span>Dashboard</span>
    </a>
  </li>
  <li class="nav-item with-sub">
    <a class="nav-link" href="#">
      <i class="icon fas fa-users"></i>
      <span>Emirates Alpha</span>
    </a>
    <div class="sub-item">
      <ul>
        <li><a href="{{ url('/users') }}">Crew List</a></li>
        <li><a href="{{ url('/promotions') }}">Crew Promotion</a></li>
        <li><a href="{{ url('/last_landings') }}">Last Landing</a></li>
        <li><a href="#">Skyward Corner</a></li>
        <li><a href="#">Emirates Media</a></li>
      </ul>
    </div><!-- dropdown-menu -->
  </li>
  <li class="nav-item with-sub">
    <a class="nav-link" href="#">
      <i class="icon fas fa-plane"></i>
      <span>Flight Center</span>
    </a>
    <div class="sub-item">
      <ul>
        <li><a href="{{ url('/flights') }}">Flight Assignment</a></li>
        <li><a href="{{ url('/flights/bids') }}">Briefing Room</a></li>
        <li><a href="#">Route Map</a></li>
        <li><a href="{{ url('/pireps') }}">PIREP</a></li>
      </ul>
    </div><!-- dropdown-menu -->
  </li>
  <li class="nav-item with-sub">
    <a class="nav-link" href="#">
      <i class="icon fas fa-globe"></i>
      <span>Operation CC</span>
    </a>
    <div class="sub-item">
      <ul>
        <li><a href="{{ url('/livemap') }}">Live Map</a></li>
      </ul>
    </div><!-- dropdown-menu -->
  </li>
  <li class="nav-item with-sub">
    <a class="nav-link" href="#">
      <i class="icon fas fa-chart-line"></i>
      <span>Coorporate Statistic</span>
    </a>
    <div class="sub-item">
      <ul>
        <li><a href="{{ url('/statistics') }}">Statistic Center</a></li>
      </ul>
    </div><!-- dropdown-menu -->
  </li>
  <li class="nav-item with-sub">
    <a class="nav-link" href="#">
      <i class="icon fa fa-desktop"></i>
      <span>Service Center</span>
    </a>
    <div class="sub-item">
      <ul>
        <li><a href="page-profile.html">Profile Page</a></li>
      </ul>
    </div><!-- dropdown-menu -->
  </li>
</ul>
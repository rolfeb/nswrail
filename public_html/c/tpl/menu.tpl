<nav class="navbar navbar-expand-sm bg-nsw navbar-light">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <!--
      <li class="nav-item">
        <a class="nav-link" href="/">
          <img src="/media/images/rail_profile.svg" style="width: 22px; height: 22px;">
        </a>
      </li>
      -->
      <!-- BEGIN ITEM-OR-DROPDOWN -->
      <!-- BEGIN ITEM -->
      <li class="nav-item">
        <a class="nav-link" href="{ITEM-URL}">{ITEM-NAME}</a>
      </li>
      <!-- END ITEM -->
      <!-- BEGIN DROPDOWN -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">{DROPDOWN-NAME}</a>
        <div class="dropdown-menu">
           <!-- BEGIN DROPDOWN-ITEM -->
           <a class="dropdown-item" href="{ITEM-URL}">{ITEM-NAME}</a>
           <!-- END DROPDOWN-ITEM -->
        </div>
      </li>
      <!-- END DROPDOWN -->
      <!-- END ITEM-OR-DROPDOWN -->
    </ul>
  </div>
  <!--
  <div class="float-right">
    <form class="form-inline" action="/search/search.php" method="get">
    <input class="form-control" type="text" size="16" placeholder="Search">
    <span class="input-group-addon"><i class="material-icons">search</i></span>
    </form>
  </div>
  -->
</nav>

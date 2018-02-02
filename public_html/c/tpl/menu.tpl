<nav class="navbar navbar-expand-sm bg-nsw navbar-light">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
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
</nav>

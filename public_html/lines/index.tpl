<!-- BEGIN CONTENT -->
<h1>{TITLE}</h1>

<!-- nav tabs -->
<ul class="nav nav-tabs">
  <!-- BEGIN NAV-TAB -->
  <li class="nav-item">
    <a class="nav-link {ACTIVE}" data-toggle="tab" href="#{ID}">{LABEL}</a>
  </li>
  <!-- END NAV-TAB -->
</ul>

<p>
<b>Key:</b>
<img src="/c/images/bullet_green.png" />&nbsp;Open&nbsp;&nbsp;
<img src="/c/images/bullet_red.png" />&nbsp;Closed&nbsp;&nbsp;
<img src="/c/images/bullet_blue.png" />&nbsp;Tourist&nbsp;&nbsp;
<img src="/c/images/bullet_grey.png" />&nbsp;Lifted&nbsp;&nbsp;
<img src="/c/images/bullet_light_grey.png" />&nbsp;Uncompleted&nbsp;&nbsp;
</p>

<!-- tab panes -->
<div class="tab-content">
  <!-- BEGIN TAB-PANE -->
  <div class="tab-pane {ACTIVE} container" id="{ID}">
    <div class="row">
      <!-- BEGIN COLUMN -->
      <div class="col-lg-4">
        <!-- BEGIN CATEGORY -->
        <h3>{CATEGORY-NAME}</h3>
        <ul>
          <!-- BEGIN LINE -->
          <li class="{LINE-CLASS}">
            <a href="{LINE-URL}">{LINE-NAME}</a>{LINE-EXTRA}
          </li>
          <!-- END LINE -->
        </ul>
        <!-- END CATEGORY -->
      </div>
      <!-- END COLUMN -->
    </div>
  </div>
  <!-- END TAB-PANE -->
</div>

<!-- END CONTENT -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified ">
      <li class="nav-item">
        <a href="#control-sidebar-home-tab" data-toggle="tab">
          <i class="fa fa-home"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="#control-sidebar-stats-tab" data-toggle="tab">
          <i class="fa fa-plus"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="#control-sidebar-settings-tab1" data-toggle="tab">
          <i class="fa fa-cog"></i>
        </a>
      </li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content" >
      <!-- Home tab content -->
      <div class="tab-pane" id="control-sidebar-home-tab">
        <h4 class="control-sidebar-heading">Recent Activity</h4>
      </div>
      <!-- /Home .tab-pane -->

      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">
        <h4 class="control-sidebar-heading">Stats Tab Content</h4>
      </div>
      <!-- /Stats .tab-pane -->

      <?php if(getLoggedAdmin()){ ?>
        <!-- Settings tab content -->
        <div class="tab-pane" id="control-sidebar-settings-tab1">
          <h4 class="control-sidebar-heading">General Settings</h4>
          <div class="col-xs-6 text-center">
              <a href="<?php echo PREPEND_PATH; ?>LTE/config_edit.php" class="btn btn-app" title="Open app anviroment options"><i class="fa fa-cogs"></i>&nbsp;App anviroment</a>
          </div>
        </div>
        <!-- /Settings .tab-pane -->
      <?php } ?>
      
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>


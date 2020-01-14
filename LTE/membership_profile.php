<?php
	define("PREPEND_PATH", "../");
	$currDir=dirname(__FILE__)."/..";
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");

	$adminConfig = config('adminConfig');

	/* no access for guests */
	$mi = getMemberInfo();
	if(!$mi['username'] || $mi['group'] == $adminConfig['anonymousGroup']) {
		@header('Location: index.php'); exit;
	}

	/* save profile */
	if($_POST['action'] == 'saveProfile') {
		if(!csrf_token(true)) {
			echo $Translation['error:'];
			exit;
		}

		/* process inputs */
		$email=isEmail($_POST['email']);
		$custom1=makeSafe($_POST['custom1']);
		$custom2=makeSafe($_POST['custom2']);
		$custom3=makeSafe($_POST['custom3']);
		$custom4=makeSafe($_POST['custom4']);

		/* validate email */
		if(!$email) {
			echo "{$Translation['error:']} {$Translation['email invalid']}";
			echo "<script>$$('label[for=\"email\"]')[0].pulsate({ pulses: 10, duration: 4 }); $j('#email').focus();</script>";
			exit;
		}

		/* update profile */
		$updateDT = date($adminConfig['PHPDateTimeFormat']);
		sql("UPDATE `membership_users` set email='$email', custom1='$custom1', custom2='$custom2', custom3='$custom3', custom4='$custom4', comments=CONCAT_WS('\\n', comments, 'member updated his profile on $updateDT from IP address {$mi[IP]}') WHERE memberID='{$mi['username']}'", $eo);

		// hook: member_activity
		if(function_exists('member_activity')) {
			$args=array();
			member_activity($mi, 'profile', $args);
		}

		exit;
	}

	/* change password */
	if($_POST['action'] == 'changePassword' && $mi['username'] != $adminConfig['adminUsername']) {
		if(!csrf_token(true)) {
			echo $Translation['error:'];
			exit;
		}

		/* process inputs */
		$oldPassword=$_POST['oldPassword'];
		$newPassword=$_POST['newPassword'];

		/* validate password */
		$hash = sqlValue("SELECT `passMD5` FROM `membership_users` WHERE memberID='{$mi['username']}'");
		if(!password_match($oldPassword, $hash)) {
			echo "{$Translation['error:']} {$Translation['Wrong password']}";
			?>
			<script>
				$j(function() {
					$j('#old-password').focus();
				})
			</script>
			<?php
			exit;
		}
		if(strlen($newPassword) < 4) {
			echo "{$Translation['error:']} {$Translation['password invalid']}";
			?>
			<script>
				$j(function() {
					$j('#new-password').focus();
				})
			</script>
			<?php

			exit;      
		}

		/* update password */
		$updateDT = date($adminConfig['PHPDateTimeFormat']);
		sql("UPDATE `membership_users` set `passMD5`='" . password_hash($newPassword, PASSWORD_DEFAULT) . "', `comments`=CONCAT_WS('\\n', comments, 'member changed his password on $updateDT from IP address {$mi[IP]}') WHERE memberID='{$mi['username']}'", $eo);

		// hook: member_activity
		if(function_exists('member_activity')) {
			$args=array();
			member_activity($mi, 'password', $args);
		}

		exit;
	}

	/* get profile info */
	/* 
		$mi already contains the profile info, as documented at: 
		https://bigprof.com/appgini/help/working-with-generated-web-database-application/hooks/memberInfo

		custom field names are stored in $adminConfig['custom1'] to $adminConfig['custom4']
	*/
	$permissions = array();
	$userTables = getTableList();
	if(is_array($userTables))  foreach($userTables as $tn => $tc) {
		$permissions[$tn] = getTablePermissions($tn);
	}

	/* the profile page view */
	include_once("$currDir/header.php"); ?>

	<div id="notify" class="alert alert-success" style="display: none;"></div>
	<div id="loader" style="display: none;"><i class="glyphicon glyphicon-refresh"></i> <?php echo $Translation['Loading ...']; ?></div>

	<?php echo csrf_token(); ?>


	<div class="row">
  <div class="col-md-3">

    <!-- Profile Image -->
    <div class="card card-primary card-outline">
      <div class="card-body box-profile">
        <div class="text-center">
          <img class="profile-user-img img-fluid img-circle user-image" src="<?php echo PREPEND_PATH; ?>images/<?php echo $mpi->thumb; ?>" alt="User profile picture">
        </div>

        <h3 class="profile-username text-center"><?php echo $mi['custom'][0]; ?></h3>

        <p class="text-muted text-center"><?php echo sprintf($Translation['Hello user'], $mi['username']); ?></p>

        <ul class="list-group list-group-unbordered mb-3">
          <li class="list-group-item">
            <b>Orders</b> <a class="float-right">1,322</a>
          </li>
          <li class="list-group-item">
            <b>Companies</b> <a class="float-right">543</a>
          </li>
          <li class="list-group-item">
            <b>Contacts</b> <a class="float-right">13,287</a>
          </li>
        </ul>
		
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <!-- About Me Box -->
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">About Me</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <strong><i class="fas fa-book mr-1"></i> Education</strong>

        <p class="text-muted">
          B.S. in Computer Science from the University of Tennessee at Knoxville
        </p>

        <hr>

        <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

        <p class="text-muted">Malibu, California</p>

        <hr>

        <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

        <p class="text-muted">
          <span class="tag tag-danger">UI Design</span>
          <span class="tag tag-success">Coding</span>
          <span class="tag tag-info">Javascript</span>
          <span class="tag tag-warning">PHP</span>
          <span class="tag tag-primary">Node.js</span>
        </p>

        <hr>

        <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>

        <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
  <div class="col-md-9">
    <div class="card">
      <div class="card-header p-2">
        <ul class="nav nav-pills">
          <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Activity</a></li>
          <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
          <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
          <li class="nav-item">
            <a class="nav-link" href="#permissions" data-toggle="tab">
              <i class="glyphicon glyphicon-lock"></i>
              <?php echo $Translation['Your access permissions']; ?>
            </a></li>
        </ul>
      </div><!-- /.card-header -->
      <div class="card-body">
        <div class="tab-content">
          <div class="active tab-pane" id="activity">
            <!-- Post -->
            <div class="post">
              <div class="user-block">
                <img class="img-circle img-bordered-sm" src="<?php echo PREPEND_PATH; ?>LTE/dist/img/user1-128x128.jpg" alt="user image">
                <span class="username">
                  <a href="#">Jonathan Burke Jr.</a>
                  <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                </span>
                <span class="description">Shared publicly - 7:30 PM today</span>
              </div>
              <!-- /.user-block -->
              <p>
                Lorem ipsum represents a long-held tradition for designers,
                typographers and the like. Some people hate it and argue for
                its demise, but others ignore the hate as they create awesome
                tools to help create filler text for everyone from bacon lovers
                to Charlie Sheen fans.
              </p>

              <p>
                <a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
                <a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
                <span class="float-right">
                  <a href="#" class="link-black text-sm">
                    <i class="far fa-comments mr-1"></i> Comments (5)
                  </a>
                </span>
              </p>

              <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
            </div>
            <!-- /.post -->

            <!-- Post -->
            <div class="post clearfix">
              <div class="user-block">
                <img class="img-circle img-bordered-sm" src="<?php echo PREPEND_PATH; ?>LTE/dist/img/user7-128x128.jpg" alt="User Image">
                <span class="username">
                  <a href="#">Sarah Ross</a>
                  <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                </span>
                <span class="description">Sent you a message - 3 days ago</span>
              </div>
              <!-- /.user-block -->
              <p>
                Lorem ipsum represents a long-held tradition for designers,
                typographers and the like. Some people hate it and argue for
                its demise, but others ignore the hate as they create awesome
                tools to help create filler text for everyone from bacon lovers
                to Charlie Sheen fans.
              </p>

              <form class="form-horizontal">
                <div class="input-group input-group-sm mb-0">
                  <input class="form-control form-control-sm" placeholder="Response">
                  <div class="input-group-append">
                    <button type="submit" class="btn btn-danger">Send</button>
                  </div>
                </div>
              </form>
            </div>
            <!-- /.post -->

            <!-- Post -->
            <div class="post">
              <div class="user-block">
                <img class="img-circle img-bordered-sm" src="<?php echo PREPEND_PATH; ?>LTE/dist/img/user6-128x128.jpg" alt="User Image">
                <span class="username">
                  <a href="#">Adam Jones</a>
                  <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                </span>
                <span class="description">Posted 5 photos - 5 days ago</span>
              </div>
              <!-- /.user-block -->
              <div class="row mb-3">
                <div class="col-sm-6">
                  <img class="img-fluid" src="<?php echo PREPEND_PATH; ?>LTE/dist/img/photo1.png" alt="Photo">
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                  <div class="row">
                    <div class="col-sm-6">
                      <img class="img-fluid mb-3" src="<?php echo PREPEND_PATH; ?>LTE/dist/img/photo2.png" alt="Photo">
                      <img class="img-fluid" src="<?php echo PREPEND_PATH; ?>LTE/dist/img/photo3.jpg" alt="Photo">
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                      <img class="img-fluid mb-3" src="<?php echo PREPEND_PATH; ?>LTE/dist/img/photo4.jpg" alt="Photo">
                      <img class="img-fluid" src="<?php echo PREPEND_PATH; ?>LTE/dist/img/photo1.png" alt="Photo">
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <p>
                <a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
                <a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
                <span class="float-right">
                  <a href="#" class="link-black text-sm">
                    <i class="far fa-comments mr-1"></i> Comments (5)
                  </a>
                </span>
              </p>

              <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
            </div>
            <!-- /.post -->
          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="timeline">
            <!-- The timeline -->
            <div class="timeline timeline-inverse">
              <!-- timeline time label -->
              <div class="time-label">
                <span class="bg-danger">
                  10 Feb. 2014
                </span>
              </div>
              <!-- /.timeline-label -->
              <!-- timeline item -->
              <div>
                <i class="fas fa-envelope bg-primary"></i>

                <div class="timeline-item">
                  <span class="time"><i class="far fa-clock"></i> 12:05</span>

                  <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                  <div class="timeline-body">
                    Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                    weebly ning heekya handango imeem plugg dopplr jibjab, movity
                    jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                    quora plaxo ideeli hulu weebly balihoo...
                  </div>
                  <div class="timeline-footer">
                    <a href="#" class="btn btn-primary btn-sm">Read more</a>
                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                  </div>
                </div>
              </div>
              <!-- END timeline item -->
              <!-- timeline item -->
              <div>
                <i class="fas fa-user bg-info"></i>

                <div class="timeline-item">
                  <span class="time"><i class="far fa-clock"></i> 5 mins ago</span>

                  <h3 class="timeline-header border-0"><a href="#">Sarah Young</a> accepted your friend request
                  </h3>
                </div>
              </div>
              <!-- END timeline item -->
              <!-- timeline item -->
              <div>
                <i class="fas fa-comments bg-warning"></i>

                <div class="timeline-item">
                  <span class="time"><i class="far fa-clock"></i> 27 mins ago</span>

                  <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                  <div class="timeline-body">
                    Take me to your leader!
                    Switzerland is small and neutral!
                    We are more like Germany, ambitious and misunderstood!
                  </div>
                  <div class="timeline-footer">
                    <a href="#" class="btn btn-warning btn-flat btn-sm">View comment</a>
                  </div>
                </div>
              </div>
              <!-- END timeline item -->
              <!-- timeline time label -->
              <div class="time-label">
                <span class="bg-success">
                  3 Jan. 2014
                </span>
              </div>
              <!-- /.timeline-label -->
              <!-- timeline item -->
              <div>
                <i class="fas fa-camera bg-purple"></i>

                <div class="timeline-item">
                  <span class="time"><i class="far fa-clock"></i> 2 days ago</span>

                  <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                  <div class="timeline-body">
                    <img src="http://placehold.it/150x100" alt="...">
                    <img src="http://placehold.it/150x100" alt="...">
                    <img src="http://placehold.it/150x100" alt="...">
                    <img src="http://placehold.it/150x100" alt="...">
                  </div>
                </div>
              </div>
              <!-- END timeline item -->
              <div>
                <i class="far fa-clock bg-gray"></i>
              </div>
            </div>
          </div>
          <!-- /.tab-pane -->

          <div class="tab-pane" id="settings">
            <fieldset id="profile">
              <div class="form-group">
                <label for="email"><?php echo $Translation['email']; ?></label>
                <input type="email" id="email" name="email" value="<?php echo $mi['email']; ?>" class="form-control">
              </div>

              <?php for ($i = 1; $i < 5; $i++) { ?>
                <div class="form-group">
                  <label for="custom<?php echo $i; ?>"><?php echo $adminConfig['custom' . $i]; ?></label>
                  <input type="text" id="custom<?php echo $i; ?>" name="custom<?php echo $i; ?>" value="<?php echo $mi['custom'][$i - 1]; ?>" class="form-control">
                </div>
              <?php } ?>

              <div class="row">
                <div class="col-md-4 col-md-offset-4">
                  <button id="update-profile" class="btn btn-success btn-block" type="button"><i class="glyphicon glyphicon-ok"></i> <?php echo $Translation['Update profile']; ?></button>
                </div>
              </div>
			</fieldset>
			<br>
			<form action="submit">
				<div class="form-group">
					<label for="email">Select profile image</label>
					<div class="input-group mb-3">
						<div class="custom-file">
							<input class="custom-file-input" name="mpi" id="mpi" type="file">
							<label class="custom-file-label" for="mpi" aria-describedby="mpi-update">Choose file</label>
						</div>
						<div class="input-group-append">
							<button type="submit" id="mpi-update" class="btn btn-primary btn-block"><b>Upload</b></button>
						</div>
					</div>
				</div>
			</form>
          </div>

          <div class="tab-pane" id="permissions">
            <p><strong><?php echo $Translation['Legend']; ?></strong></p>
            <div class="row">
              <div class="col-xs-2 col-md-1 text-right"><img src="<?php echo PREPEND_PATH; ?>admin/images/stop_icon.gif"></div>
              <div class="col-xs-10 col-md-5"><?php echo $Translation['Not allowed']; ?></div>
              <div class="col-xs-2 col-md-1 text-right"><img src="<?php echo PREPEND_PATH; ?>admin/images/member_icon.gif"></div>
              <div class="col-xs-10 col-md-5"><?php echo $Translation['Only your own records']; ?></div>
            </div>
            <div class="row">
              <div class="col-xs-2 col-md-1 text-right"><img src="<?php echo PREPEND_PATH; ?>admin/images/members_icon.gif"></div>
              <div class="col-xs-10 col-md-5"><?php echo $Translation['All records owned by your group']; ?></div>
              <div class="col-xs-2 col-md-1 text-right"><img src="<?php echo PREPEND_PATH; ?>admin/images/approve_icon.gif"></div>
              <div class="col-xs-10 col-md-5"><?php echo $Translation['All records']; ?></div>
            </div>

            <p class="vspacer-lg"></p>

            <div class="table-responsive">
              <table class="table table-striped table-hover table-bordered" id="permissions">
                <thead>
                  <tr>
                    <th><?php echo $Translation['Table']; ?></th>
                    <th class="text-center"><?php echo $Translation['View']; ?></th>
                    <th class="text-center"><?php echo $Translation['Add New']; ?></th>
                    <th class="text-center"><?php echo $Translation['Edit']; ?></th>
                    <th class="text-center"><?php echo $Translation['Delete']; ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($permissions as $tn => $perm) { ?>
                    <tr>
                      <td><img src="<?php echo PREPEND_PATH; ?><?php echo $userTables[$tn][2]; ?>"> <a href="<?php echo $tn; ?>_view.php"><?php echo $userTables[$tn][0]; ?></a></td>
                      <td class="text-center"><img src="<?php echo PREPEND_PATH; ?>admin/images/<?php echo permIcon($perm[2]); ?>" /></td>
                      <td class="text-center"><img src="<?php echo PREPEND_PATH; ?>admin/images/<?php echo ($perm[1] ? 'approve' : 'stop'); ?>_icon.gif" /></td>
                      <td class="text-center"><img src="<?php echo PREPEND_PATH; ?>admin/images/<?php echo permIcon($perm[3]); ?>" /></td>
                      <td class="text-center"><img src="<?php echo PREPEND_PATH; ?>admin/images/<?php echo permIcon($perm[4]); ?>" /></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
      </div><!-- /.card-body -->
    </div>
    <!-- /.nav-tabs-custom -->
  </div>
  <!-- /.col -->
</div>


	<script>
		$j(function() {
			<?php
				/* Is there a notification to display? */
				$notify = '';
				if(isset($_GET['notify'])) $notify = addslashes(strip_tags($_GET['notify']));
			?>
			<?php if($notify) { ?> notify('<?php echo $notify; ?>'); <?php } ?>
			
			getMpi({cmd:'u'},false);
			
			$j("form").submit(function(e) {
				e.preventDefault();    
				var formData = new FormData(this);
				getMpi(formData);
			});
			$j('#mpi').on('change',function(){
                //get the file name
                var fileName = $j(this).val();
                //replace the "Choose a file" label
                $j(this).next('.custom-file-label').html(fileName);
			})
			
			$j('#update-profile').on('click', function() {
				post2(
					'<?php echo basename(__FILE__); ?>',
					{ action: 'saveProfile', email: $j('#email').val(), custom1: $j('#custom1').val(), custom2: $j('#custom2').val(), custom3: $j('#custom3').val(), custom4: $j('#custom4').val(), csrf_token: $j('#csrf_token').val() },
					'notify', 'profile', 'loader', 
					'<?php echo basename(__FILE__); ?>?notify=<?php echo urlencode($Translation['Your profile was updated successfully']); ?>'
				);
			});

			<?php if($mi['username'] != $adminConfig['adminUsername']) { ?>
				$('update-password').observe('click', function() {
					/* make sure passwords match */
					if($j('#new-password').val() != $j('#confirm-password').val()) {
						$j('#notify').addClass('alert-danger');
						notify('<?php echo "{$Translation['error:']} ".addslashes($Translation['password no match']); ?>');
						$$('label[for="confirm-password"]')[0].pulsate({ pulses: 10, duration: 4 });
						$j('#confirm-password').focus();
						return false;
					}

					post2(
						'<?php echo basename(__FILE__); ?>',
						{ action: 'changePassword', oldPassword: $j('#old-password').val(), newPassword: $j('#new-password').val(), csrf_token: $j('#csrf_token').val() },
						'notify', 'password-change-form', 'loader', 
						'<?php echo basename(__FILE__); ?>?notify=<?php echo urlencode($Translation['Your password was changed successfully']); ?>'
					);
				});

				/* password strength feedback */
				$j('#new-password').on('keyup', function() {
					var ps = passwordStrength($j('#new-password').val(), '<?php echo addslashes($mi['username']); ?>');

					if(ps == 'strong')
						$j('#password-strength').html('<?php echo $Translation['Password strength: strong']; ?>').css({color: 'Green'});
					else if(ps == 'good')
						$j('#password-strength').html('<?php echo $Translation['Password strength: good']; ?>').css({color: 'Gold'});
					else
						$j('#password-strength').html('<?php echo $Translation['Password strength: weak']; ?>').css({color: 'Red'});
				});

				/* inline feedback of confirm password */
				$j('#confirm-password').on('keyup', function() {
					if($j('#confirm-password').val() != $j('#new-password').val() || !$j('#confirm-password').val().length) {
						$j('#confirm-status').html('<img align="top" src="Exit.gif"/>');
					}else{
						$j('#confirm-status').html('<img align="top" src="update.gif"/>');
					}
				});
			<?php } ?>
		});

		function notify(msg) {
			$j('#notify').html(msg).fadeIn();
			window.setTimeout(function() { /* */ $j('#notify').fadeOut(); }, 15000);
		}
	</script>

	<?php
		/* return icon file name based on given permission value */
		function permIcon($perm) {
			switch($perm) {
				case 1:
					return 'member_icon.gif';
				case 2:
					return 'members_icon.gif';
				case 3:
					return 'approve_icon.gif';
				default:
					return 'stop_icon.gif';
			}
		}
	?>

	<?php include_once("$currDir/footer.php"); ?>

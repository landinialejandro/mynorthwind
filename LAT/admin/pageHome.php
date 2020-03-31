<?php
define("PREPEND_PATH", "../../");

$currDir = dirname(__FILE__);
$adminDir = $currDir . "/../../admin";
$latDir = $currDir . "/..";

require("{$adminDir}/incCommon.php");
$GLOBALS['page_title'] = $Translation['membership management homepage'];
$ADMINAREA = true;
include("{$latDir}/config_lat.php");
include("{$latDir}/header_lat.php");
?>
<script>
    // VALIDATION FUNCTIONS FOR VARIOUS PAGES

    function jsValidateEmail(address) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (reg.test(address) == false) {
            modal_window({
                message: '<div class="alert alert-danger">' + "<?php echo $Translation['invalid email']; ?>" + '</div>',
                title: "<?php echo $Translation['error']; ?>"
            });
            return false;
        } else {
            return true;
        }
    }

    function jsShowWait() {
        return window.confirm("<?php echo $Translation['sending mails']; ?>");
    }

    function jsValidateAdminSettings() {
        var p1 = document.getElementById('adminPassword').value;
        var p2 = document.getElementById('confirmPassword').value;
        if (p1 == '' || p1 == p2) {
            return jsValidateEmail(document.getElementById('senderEmail').value);
        } else {
            modal_window({
                message: '<div class="alert alert-error">' + "<?php echo $Translation['password mismatch']; ?>" + '</div>',
                title: "<?php echo $Translation['error']; ?>"
            });
            return false;
        }
    }

    function jsConfirmTransfer() {
        var confirmMessage;
        var sg = document.getElementById('sourceGroupID').options[document.getElementById('sourceGroupID').selectedIndex].text;
        var sm = document.getElementById('sourceMemberID').value;
        var dg = document.getElementById('destinationGroupID').options[document.getElementById('destinationGroupID').selectedIndex].text;
        if (document.getElementById('destinationMemberID')) {
            var dm = document.getElementById('destinationMemberID').value;
        }
        if (document.getElementById('dontMoveMembers')) {
            var dmm = document.getElementById('dontMoveMembers').checked;
        }
        if (document.getElementById('moveMembers')) {
            var mm = document.getElementById('moveMembers').checked;
        }

        //confirm('sg='+sg+'\n'+'sm='+sm+'\n'+'dg='+dg+'\n'+'dm='+dm+'\n'+'mm='+mm+'\n'+'dmm='+dmm+'\n');

        if (dmm && !dm) {
            modal_window({
                message: '<div>' + "<?php echo $Translation['complete step 4']; ?>" + '</div>',
                title: "<?php echo $Translation['info']; ?>",
                close: function() {
                    /* */
                    jQuery('#destinationMemberID').focus();
                }
            });
            return false;
        }

        if (mm && sm != '-1') {

            confirmMessage = "<?php echo $Translation['sure move member']; ?>";
            confirmMessage = confirmMessage.replace(/<MEMBER>/, sm).replace(/<OLDGROUP>/, sg).replace(/<NEWGROUP>/, dg);
            return window.confirm(confirmMessage);

        }
        if ((dmm || dm) && sm != '-1') {

            confirmMessage = "<?php echo $Translation['sure move data of member']; ?>";
            confirmMessage = confirmMessage.replace(/<OLDMEMBER>/, sm).replace(/<OLDGROUP>/, sg).replace(/<NEWMEMBER>/, dm).replace(/<NEWGROUP>/, dg);
            return window.confirm(confirmMessage);
        }

        if (mm) {

            confirmMessage = "<?php echo $Translation['sure move all members']; ?>";
            confirmMessage = confirmMessage.replace(/<OLDGROUP>/, sg).replace(/<NEWGROUP>/, dg);
            return window.confirm(confirmMessage);
        }

        if (dmm) {


            confirmMessage = "<?php echo $Translation['sure move data of all members']; ?>";
            confirmMessage = confirmMessage.replace(/<OLDGROUP>/, sg).replace(/<MEMBER>/, dm).replace(/<NEWGROUP>/, dg);
            return window.confirm(confirmMessage);
        }
    }

    function showDialog(dialogId) {
        $j('.dialog-box').addClass('hidden-block');
        $j('#' + dialogId).removeClass('hidden-block');
        return false
    };

    function hideDialogs() {
        $j('.dialog-box').addClass('hidden-block');
        return false
    };


    $j(function() {
        $j('input[type=submit],input[type=button]').each(function() {
            var label = $j(this).val();
            var onclick = $j(this).attr('onclick') || '';
            var name = $j(this).attr('name') || '';
            var type = $j(this).attr('type');

            $j(this).replaceWith('<button class="btn btn-primary" type="' + type + '" onclick="' + onclick + '" name="' + name + '" value="' + label + '">' + label + '</button>');
        });

        /* fix links inside alerts */
        $j('.alert a:not(.btn)').addClass('alert-link');
    });
</script>

<?php
if (!sqlValue("select count(1) from membership_groups where allowSignup=1")) {
    $noSignup = TRUE;
?>
    <div class="alert alert-info">
        <i><?php echo $Translation["attention"]; ?></i>
        <br><?php echo $Translation["visitor sign up"]; ?>
    </div>
<?php
}
?>

<?php
// get the count of records having no owners in each table
$arrTables = getTableList();

foreach ($arrTables as $tn => $tc) {
    $countOwned = sqlValue("select count(1) from membership_userrecords where tableName='$tn' and not isnull(groupID)");
    $countAll = sqlValue("select count(1) from `$tn`");

    if ($countAll > $countOwned) {
?>
        <div class="alert alert-info">
            <?php echo $Translation["table data without owner"]; ?>
        </div>
<?php
        break;
    }
}
?>
<div class="row theme-compact" id="outer-row">
    <div class="page-header">
        <h1><?php echo $Translation['membership management homepage']; ?></h1>
    </div>

    <?php if (!$adminConfig['hide_twitter_feed']) { ?>
        <div class="col-12">
        <?php } ?>

        <div class="row" id="inner-row">

            <!-- ################# Maintenance mode ###################### -->
            <?php
            if (maintenance_mode()) {
                $off_classes = 'btn-default locked_inactive';
                $on_classes = 'btn-danger unlocked_active';
            } else {
                $off_classes = 'btn-success locked_active';
                $on_classes = 'btn-default unlocked_inactive';
            }
            ?>
            <div class="col-12 text-right vspacer-lg">
                <label><?php echo $Translation['maintenance mode']; ?></label>
                <div class="btn-group" id="toggle_maintenance_mode">
                    <button type="button" class="btn <?php echo $off_classes; ?>"><?php echo $Translation['OFF']; ?></button>
                    <button type="button" class="btn <?php echo $on_classes; ?>"><?php echo $Translation['ON']; ?></button>
                </div>
            </div>
            <script>
                $j('#toggle_maintenance_mode button').click(function() {
                    if ($j(this).hasClass('locked_active') || $j(this).hasClass('unlocked_inactive')) {
                        if (confirm('<?php echo html_attr($Translation['enable maintenance mode?']); ?>')) {
                            $j.ajax({
                                url: 'ajax-maintenance-mode.php?status=on',
                                complete: function() {
                                    location.reload();
                                }
                            });
                        }
                    } else {
                        if (confirm('<?php echo html_attr($Translation['disable maintenance mode?']); ?>')) {
                            $j.ajax({
                                url: 'ajax-maintenance-mode.php?status=off',
                                complete: function() {
                                    location.reload();
                                }
                            });
                        }
                    }
                });
            </script>

            <!-- ################# Newest Updates ######################## -->
            <div class="col-6">
                <div class="card card-info">
                    <div class="card-heading">
                        <h3 class="card-title"><?php echo $Translation["newest updates"]; ?> <a class="btn btn-default btn-sm" href="pageViewRecords.php?sort=dateUpdated&sortDir=desc"><i class="glyphicon glyphicon-chevron-right"></i></a></h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <?php
                            $res = sql("select tableName, pkValue, dateUpdated, recID from membership_userrecords order by dateUpdated desc limit 5", $eo);
                            while ($row = db_fetch_row($res)) {
                            ?>
                                <tr>
                                    <th style="min-width: 13em;"><?php echo @date($adminConfig['PHPDateTimeFormat'], $row[2]); ?></th>
                                    <td class="remaining-width">
                                        <div class="clipped"><a href="pageEditOwnership.php?recID=<?php echo $row[3]; ?>"><img src="images/data_icon.gif" border="0" alt="<?php echo $Translation["view record details"]; ?>" title="<?php echo $Translation["view record details"]; ?>"></a> <?php echo getCSVData($row[0], $row[1]); ?></div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
            <!-- ####################################################### -->


            <!-- ################# Newest Entries ######################## -->
            <div class="col-6">
                <div class="card card-info">
                    <div class="card-heading">
                        <h3 class="card-title"><?php echo $Translation["newest entries"]; ?> <a class="btn btn-default btn-sm" href="pageViewRecords.php?sort=dateAdded&sortDir=desc"><i class="glyphicon glyphicon-chevron-right"></i></a></h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <?php
                            $res = sql("select tableName, pkValue, dateAdded, recID from membership_userrecords order by dateAdded desc limit 5", $eo);
                            while ($row = db_fetch_row($res)) {
                            ?>
                                <tr>
                                    <th style="min-width: 13em;"><?php echo @date($adminConfig['PHPDateTimeFormat'], $row[2]); ?></th>
                                    <td class="remaining-width">
                                        <div class="clipped"><a href="pageEditOwnership.php?recID=<?php echo $row[3]; ?>"><img src="images/data_icon.gif" border="0" alt="<?php echo $Translation["view record details"]; ?>" title="<?php echo $Translation["view record details"]; ?>"></a> <?php echo getCSVData($row[0], $row[1]); ?></div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
            <!-- ####################################################### -->




            <!-- ################# Top Members ######################## -->
            <div class="col-6">
                <div class="card card-info">
                    <div class="card-heading">
                        <h3 class="card-title"><?php echo $Translation["top members"]; ?></h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <?php
                            $res = sql("select lcase(memberID), count(1) from membership_userrecords group by memberID order by 2 desc limit 5", $eo);
                            while ($row = db_fetch_row($res)) {
                            ?>
                                <tr>
                                    <th class="" style="max-width: 10em;"><a href="pageEditMember.php?memberID=<?php echo urlencode($row[0]); ?>" title="<?php echo $Translation["edit member details"]; ?>"><i class="glyphicon glyphicon-pencil"></i> <?php echo $row[0]; ?></a></th>
                                    <td class="remaining-width"><a href="pageViewRecords.php?memberID=<?php echo urlencode($row[0]); ?>"><img src="images/data_icon.gif" border="0" alt="<?php echo $Translation["view member records"]; ?>" title="<?php echo $Translation["view member records"]; ?>"></a> <?php echo $row[1]; ?> <?php echo $Translation["records"]; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
            <!-- ####################################################### -->


            <!-- ################# Members Stats ######################## -->
            <div class="col-6">
                <div class="card card-info">
                    <div class="card-heading">
                        <h3 class="card-title"><?php echo $Translation["members stats"]; ?></h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <tr>
                                <th class=""><?php echo $Translation["total groups"]; ?></th>
                                <td class="remaining-width"><a href="pageViewGroups.php" title="<?php echo $Translation['view groups']; ?>"><i class="glyphicon glyphicon-search"></i> <?php echo sqlValue("select count(1) from membership_groups"); ?></a></td>
                            </tr>
                            <tr>
                                <th class=""><?php echo $Translation["active members"]; ?></th>
                                <td class="remaining-width"><a href="pageViewMembers.php?status=2" title="<?php echo $Translation["view active members"]; ?>"><i class="glyphicon glyphicon-search"></i> <?php echo sqlValue("select count(1) from membership_users where isApproved=1 and isBanned=0"); ?></a></td>
                            </tr>
                            <tr>
                                <?php
                                $awaiting = intval(sqlValue("select count(1) from membership_users where isApproved=0"));
                                ?>
                                <th class="" <?php echo ($awaiting ? "style=\"color: red;\"" : ""); ?>><?php echo $Translation["members awaiting approval"]; ?></th>
                                <td class="remaining-width"><a href="pageViewMembers.php?status=1" title="<?php echo $Translation["view members awaiting approval"]; ?>"><i class="glyphicon glyphicon-search"></i> <?php echo $awaiting; ?></a></td>
                            </tr>
                            <tr>
                                <th class=""><?php echo $Translation["banned members"]; ?></th>
                                <td class="remaining-width"><a href="pageViewMembers.php?status=3" title="<?php echo $Translation["view banned members"]; ?>"><i class="glyphicon glyphicon-search"></i> <?php echo sqlValue("select count(1) from membership_users where isApproved=1 and isBanned=1"); ?></a></td>
                            </tr>
                            <tr>
                                <th class=""><?php echo $Translation["total members"]; ?></th>
                                <td class="remaining-width"><a href="pageViewMembers.php" title="<?php echo $Translation["view all members"]; ?>"><i class="glyphicon glyphicon-search"></i> <?php echo sqlValue("select count(1) from membership_users"); ?></a></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- ####################################################### -->

        </div> <!-- /div.row#inner-row -->

        <?php if (!$adminConfig['hide_twitter_feed']) { ?>
        </div> <!-- /div.col-md-8 -->

        <div class="col-12" id="twitter-feed">
            <a class="twitter-timeline" data-height="300" href="https://twitter.com/bigprof?ref_src=twsrc%5Etfw"><?php echo $Translation["BigProf tweets"]; ?></a>
            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

            <div class="text-right hidden" id="remove-feed-link"><a href="pageSettings.php#anonymousMember"><i class="glyphicon glyphicon-remove"></i> <?php echo $Translation["remove feed"]; ?></a></div>

            <script>
                $j(function() {
                    show_remove_feed_link = function() {
                        if (!$j('.twitter-timeline-rendered').length) {
                            setTimeout(function() {
                                /* */
                                show_remove_feed_link();
                            }, 1000);
                        } else {
                            $j('#remove-feed-link').removeClass('hidden');
                        }
                    };
                    show_remove_feed_link();
                });
            </script>
            <style>
                #twitter-feed>iframe {
                    height: 54vh !important;
                }
            </style>
        </div>
    <?php } ?>
</div>
<script>
    $j(function() {
        $j(window).resize(function() {
            $j('.remaining-width').each(function() {
                var card_width = $j(this).parents('.card-body').width();
                var other_cell_width = $j(this).prev().width();

                $j(this).attr('style', 'max-width: ' + (card_width * .9 - other_cell_width) + 'px !important;');
            });
        }).resize();
    })
</script>


<?php
include("{$latDir}/footer_lat.php");
?>
<?php if(!defined('PREPEND_PATH') || PREPEND_PATH === "../" ) define('PREPEND_PATH', ''); 
?>

<script src="<?php echo PREPEND_PATH; ?>LAT/plugins/jquery-jeditable/jquery.jeditable.js"></script>
<script src="<?php echo PREPEND_PATH; ?>LAT/tvedit/tv.edit.js"></script>

<script>
function getMpi(data, nav = true, mpi = true){
                $j.ajax({
                    url: '<?php echo PREPEND_PATH; ?>LAT/profile/mpi_AJAX.php',
                    type: 'POST',
                    data: data,
                    success: function (file) {
                        file=JSON.parse(file);
                         if (file.image !== null && file.image !== ''){
                            $j(".user-image").attr("src","<?php echo PREPEND_PATH; ?>images/"+ file.thumb +"");
                        }
                        $j('.user-image.mpi-header-avatar').show();
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }

</script>
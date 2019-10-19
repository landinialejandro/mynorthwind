/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function getMpi(data, nav = true, mpi = true){
                $j.ajax({
                    url: 'hooks/mpi_AJAX.php',
                    type: 'POST',
                    data: data,
                    success: function (file) {
                        file=JSON.parse(file);
                         if (file.image !== null && file.image !== ''){
                            date = new Date();
                             if (nav){
                                $j('.imagebar').remove();
                                $j('.navbar-right , .nav.navbar-nav.visible-xs').append('<a class="imagebar" href="membership_profile.php"><div style="background-image: url(images/'+ file.thumb +'?m=' + date.getTime() +')" class="mpi-header-avatar"></div></a>');
                             }
                             if (mpi){
                                $j('#mpimage').empty();
                                $j('#mpimage').append('<img src="images/'+ file.image +'?m=' + date.getTime() +'" class="img-thumbnail imagebar">');
                             }
                        }
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }


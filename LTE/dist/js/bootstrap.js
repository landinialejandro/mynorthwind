$j(function(){
    $j('.visible-xs.visible-sm').toggleClass('d-block d-sm-block d-md-none');
    $j('.visible-md.visible-lg').toggleClass('d-md-block d-sm-none d-none');
    $j('.btn-default').toggleClass('btn-secondary');
    $j('.input-group-btn').toggleClass('input-group-append');
    $j('.btn-group-lg').removeClass('btn-group-lg');
    $j('.row > .col-xs-12').toggleClass('col-12');
    $j('.pull-right').toggleClass('float-right');
    $j('.pull-left').toggleClass('float-left');
    $j('.hidden-md').toggleClass('d-md-none');
    $j('.hidden-lg').toggleClass('d-lg-none');
    $j('.panel').toggleClass('card');
    $j('.panel-heading').toggleClass('card-header');
    $j('.panel-body').toggleClass('card-body');
    $j('.panel-title').toggleClass('card-title');
    $j('.hidden-print').toggleClass('d-print-none');
    $j('.btn').removeClass('btn-lg');
})
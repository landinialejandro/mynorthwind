<script>
    $j(function(){
        a=$j('section > div.page-header');
        if(a.length){
            $j('section > div.row').prepend($j('div.page-header'));
        }
    }
    )
</script>
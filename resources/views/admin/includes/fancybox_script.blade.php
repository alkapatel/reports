<script src="{{ asset('/') }}/themes/unify/assets/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
<link href="{{ asset('/') }}/themes/unify/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet">

<script type="text/javascript">
    $(document).ready(function(){        
        $('.fancybox_iframe').fancybox({
            'type': 'iframe',
        });        
        
        $('.fancybox_iframe_full').fancybox({
            'type': 'iframe',
            'width': '90%',
            'height': '90%',
        });        
        
        $('.fancybox').fancybox();        
    });
</script>

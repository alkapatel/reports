<div class="btn-group">
    @if($btnView)
    <a href="{{ url('sent-email/view/'.$row->id) }}" class="btn btn-xs btn-primary fancybox_iframe" title="View"}})">
        <i class="fa fa-list"></i>
    </a>
    @endif                
</div>

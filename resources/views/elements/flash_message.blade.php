@if(Session::has('flash_message'))
<div class="alert bg-{{ session('flash_message')['type'] }} alert-dismissible mt-3" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong class="text-white">{!! nl2br(e(session('flash_message')['message'])) !!}</strong>
</div>
@endif

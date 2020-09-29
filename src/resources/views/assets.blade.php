<script type="text/javascript">
    var media_trans=<?= json_encode(trans('media-manager::media'))?>;
</script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
<link rel="stylesheet" href="{{url('vendor/elsayednofal/image-manager/app.css')}}">
@if(App::isLocale('ar'))
<link rel="stylesheet" href="{{url('vendor/elsayednofal/image-manager/app-ar.css')}}">
@endif
<script defer src="{{url('vendor/elsayednofal/image-manager/vue.min.js')}}"></script>
<script defer src="{{url('vendor/elsayednofal/image-manager/jquery.blockUI.min.js')}}"></script>
<script defer src="{{url('vendor/elsayednofal/image-manager/jquery-ui.min.js')}}"></script>
<script defer src="{{url('vendor/elsayednofal/image-manager/app.js')}}"></script>
<?php

$url = env('API_BASE_URL').$image;
$path = parse_url($url, PHP_URL_PATH);
$extension = pathinfo($path, PATHINFO_EXTENSION);

?>
<h1>{{ isset($title) ? $title : 'Bukti Pegawai Kembali'}}</h1>
<hr>

@if($extension == 'pdf')
    <object class="pdf" 
            data={{ env('API_BASE_URL').$image  }} style="width:100%;height:720px">
    </object>
@else
<img src="{{ env('API_BASE_URL').$image  }}" alt="">
@endif
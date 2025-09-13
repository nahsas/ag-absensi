<?php
$url = env('API_BASE_URL').$image;
$path = parse_url($url, PHP_URL_PATH);
$extension = pathinfo($path, PATHINFO_EXTENSION);
?>

<h1>{{ isset($title) ? $title : 'Bukti Pegawai Kembali'}}</h1>
<hr>

@if($extension == 'pdf')
    <div class="shimmer" id="shimmer-container" style="width:100%;height:720px;">
        <object class="pdf" 
                data="{{ env('API_BASE_URL').$image }}" 
                style="width:100%;height:720px; display:none;"
                onload="this.style.display='block'; document.getElementById('shimmer-container').style.display='none';">
        </object>
    </div>
@else
    <div class="shimmer" id="shimmer-container" style="width:100%; height: auto; min-height: 200px;">
    </div>
    <img src="{{ $image }}" 
             alt="" 
             style="display:none; width: 100%;" 
             onload="this.style.display='block'; document.getElementById('shimmer-container').style.display='none';">
@endif
<style>
.shimmer {
    /* Base styles for the shimmer box */
    background: #f6f7f8;
    background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
    background-repeat: no-repeat;
    background-size: cover; /* Adjust the size as needed */
    animation: shimmer 1s linear infinite forwards;
}

@keyframes shimmer {
    0% {
        background-position: -468px 0;
    }
    100% {
        background-position: 468px 0;
    }
}
</style>
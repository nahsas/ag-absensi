<span style="font-family: 'Segoe UI', Arial, sans-serif; font-weight: bolder; font-size: 1.2rem;text-align:center">
    <span style="color: #1a237e;">AG</span>
    <span style="color: #b71c1c;">Konsultan</span><br>
    <div style="margin-top:-10px;font-size:12px;font-weight:normal"><?php
        $dt = new DateTime();
        $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $formatter->setPattern('EEEE, d MMMM');
        echo $formatter->format($dt);   
    ?></div>

</span>

<script>
    if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
    console.log('Service Worker terdaftar');
    // Minta izin dan dapatkan token
    Notification.requestPermission().then(permission => {
        let service_key = ""
      if (permission === 'granted') {
        registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: service_key
        }).then(subscription => {
            console.log(subscription)
        });
      }
    });
  });
}
</script>
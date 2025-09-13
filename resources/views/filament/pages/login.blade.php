<x-filament-panels::page>

</x-filament-panels::page>
<script>
    if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
    console.log('Service Worker terdaftar');
    // Minta izin dan dapatkan token
    Notification.requestPermission().then(permission => {
      if (permission === 'granted') {
        registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: 'VAPID_PUBLIC_KEY_DARI_SERVER'
        }).then(subscription => {
            console.log(subscription)
        });
      }
    });
  });
}
</script>
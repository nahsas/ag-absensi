<script src="{{ asset('service-worker.js') }}"></script>
<span style="font-family: 'Segoe UI', Arial, sans-serif; font-weight: bolder; font-size: 1rem">
    <span style="color: #1a237e;">AG</span>
    <span style="color: #b71c1c;">Konsultan</span>
</span>
<script>
    if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
    console.log('Service Worker terdaftar');
    // Minta izin dan dapatkan token
    Notification.requestPermission().then(permission => {
      if (permission === 'granted') {
        registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: 'BIggEg_N7Z5KvWR14t7gVcOVXySsJO9n5dSvsHzYpk8A4OQ_Uw32PaFqbY-0EIoY93JflT8bQAZHENPw5cfE-YA'
        }).then(subscription => {
            console.log(subscription)
        });
      }
    });
  });
}
</script>
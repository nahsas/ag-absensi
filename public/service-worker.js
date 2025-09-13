// public/js/app.js

// Cek apakah browser mendukung Service Worker
if ('serviceWorker' in navigator) {
  // Tunggu sampai halaman selesai dimuat
  window.addEventListener('load', () => {
    // Daftarkan Service Worker
    navigator.serviceWorker.register('/service-worker.js')
      .then(registration => {
        // Jika pendaftaran berhasil
        console.log('Service Worker berhasil didaftarkan dengan scope:', registration.scope);
      })
      .catch(error => {
        // Jika pendaftaran gagal
        console.log('Pendaftaran Service Worker gagal:', error);
      });
  });
}

self.addEventListener('push', function(event) {
  const data = event.data.json();
  event.waitUntil(self.registration.showNotification(data.title, {
    body: data.body,
  }));
});
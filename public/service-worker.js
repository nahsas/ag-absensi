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

function pooling(){
  const baseUrl = location.origin;
  // Store the last and latest response
  if (typeof self.lastRes === 'undefined') {
    self.lastRes = null;
  }
  if (typeof self.latestRes === 'undefined') {
    self.latestRes = null;
  }

  fetch(`${baseUrl}/api/test`, {
    method: "GET",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
    },
  })
  .then((response) => response.text())
  .then((res) => {
    console.log(res)
    self.lastRes = self.latestRes;
    self.latestRes = res;

    if (self.lastRes !== null && self.latestRes !== self.lastRes) {
      // Data is different, show notification
      const data = JSON.parse(self.latestRes);
      self.registration.showNotification('Izin baru perlu persetujuan', {
        body: 'Ada izin baru yang perlu di tinjau',
        data: {
          url: `${baseUrl}/sakits`
        },
        // Add a vibration pattern as a "sound-like" effect
        vibrate: [200, 100, 200],
        // Add a tag to avoid stacking notifications
        tag: 'izin-baru'
      });

      self.addEventListener('notificationclick', function(event) {
        event.notification.close();
        const url = event.notification.data && event.notification.data.url;
        if (url) {
          event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
              // Check if the url is already open
              for (let client of windowClients) {
                if (client.url === url && 'focus' in client) {
                  return client.focus();
                }
              }
              // Otherwise, open a new tab
              if (clients.openWindow) {
                return clients.openWindow(url);
              }
            })
          );
        }
      });
    }
  });
  setTimeout(pooling, 1000)
}

pooling()
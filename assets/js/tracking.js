// ============================================================
// TNSTC – tracking.js  (Live bus tracking via Google Maps)
// ============================================================

let map, markersMap = {}, infoWindow;
const POLL_INTERVAL = 10000; // 10 s

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: { lat: 8.7139, lng: 77.7567 },  // Tirunelveli center
    zoom: 11,
    styles: mapStyles(),
    mapTypeControl: false,
    streetViewControl: false,
  });
  infoWindow = new google.maps.InfoWindow();

  // Plot depot markers
  const depots = window.TNSTC_DEPOTS || [];
  depots.forEach(d => {
    new google.maps.Marker({
      position: { lat: parseFloat(d.lat), lng: parseFloat(d.lng) },
      map,
      title: d.name,
      icon: {
        url: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="%231a6b3c" opacity=".9"/><text x="12" y="16" text-anchor="middle" font-size="12" fill="white">D</text></svg>',
        scaledSize: new google.maps.Size(28, 28),
      },
    });
  });

  pollBuses();
  setInterval(pollBuses, POLL_INTERVAL);
}

async function pollBuses() {
  const busId = document.getElementById('busFilter')?.value || '';
  const url = '/TNSTC/api/tracking.php' + (busId ? `?bus_id=${busId}` : '');
  try {
    const buses = await apiGet(url);
    buses.forEach(b => updateBusMarker(b));
    updateBusList(buses);
  } catch (e) {
    console.warn('Tracking poll failed:', e.message);
  }
}

function updateBusMarker(bus) {
  const pos = { lat: parseFloat(bus.latitude), lng: parseFloat(bus.longitude) };
  const color = bus.delay_minutes > 0 ? '%23ffc107' : '%231a6b3c';
  const icon = {
    url: `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="3" fill="${color}"/><text x="12" y="15" text-anchor="middle" font-size="8" fill="white" font-weight="bold">BUS</text></svg>`,
    scaledSize: new google.maps.Size(36, 36),
    anchor: new google.maps.Point(18, 18),
  };

  if (markersMap[bus.bus_id]) {
    markersMap[bus.bus_id].setPosition(pos);
    markersMap[bus.bus_id].setIcon(icon);
  } else {
    const marker = new google.maps.Marker({ position: pos, map, title: bus.bus_number, icon });
    marker.addListener('click', () => {
      const delay = bus.delay_minutes > 0
        ? `<span style="color:#c2410c">⚠ Delayed ${bus.delay_minutes} min</span>` : '<span style="color:#166534">On time</span>';
      infoWindow.setContent(`
        <div style="font-family:Inter,sans-serif;padding:4px;min-width:180px">
          <strong>🚌 ${bus.bus_number}</strong><br>
          <small>${bus.route_number} · ${bus.source} → ${bus.destination}</small><br>
          Speed: ${bus.speed || 0} km/h<br>${delay}
          <br><small style="color:#6c757d">Updated: ${bus.updated_at}</small>
        </div>`);
      infoWindow.open(map, marker);
    });
    markersMap[bus.bus_id] = marker;
  }
}

function updateBusList(buses) {
  const list = document.getElementById('busList');
  if (!list) return;
  list.innerHTML = buses.map(b => `
    <div class="d-flex align-items-center gap-2 p-2 border-bottom" style="cursor:pointer"
         onclick="focusBus(${b.bus_id},${b.latitude},${b.longitude})">
      <span class="badge ${b.delay_minutes > 0 ? 'badge-warning' : 'badge-success'} badge-custom">
        ${b.delay_minutes > 0 ? '⚠ Delayed' : '✓ On Time'}
      </span>
      <div>
        <div style="font-weight:600;font-size:13px">${b.bus_number}</div>
        <div style="font-size:11px;color:#6c757d">${b.source} → ${b.destination}</div>
      </div>
    </div>`).join('') || '<p class="p-3 text-muted">No buses tracked currently.</p>';
}

function focusBus(busId, lat, lng) {
  map.setCenter({ lat: parseFloat(lat), lng: parseFloat(lng) });
  map.setZoom(14);
  if (markersMap[busId]) google.maps.event.trigger(markersMap[busId], 'click');
}

function mapStyles() {
  return [
    { featureType: 'water', stylers: [{ color: '#c8d7f5' }] },
    { featureType: 'landscape', stylers: [{ color: '#f0f4f0' }] },
    { featureType: 'road', stylers: [{ color: '#ffffff' }] },
    { elementType: 'labels.text.fill', stylers: [{ color: '#444' }] },
  ];
}

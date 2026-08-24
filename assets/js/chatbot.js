// ============================================================
// TNSTC – chatbot.js  (Advanced AI Route & Transport Assistant)
// ============================================================

const ROUTES_KB    = window.TNSTC_ROUTES || [];
const SCHEDULES_KB = window.TNSTC_SCHEDULES || [];
const DEPOTS_KB    = window.TNSTC_DEPOTS || [];

function chatbotInit() {
  const msgContainer = document.getElementById('chatMessages');
  const input        = document.getElementById('chatInput');
  const sendBtn      = document.getElementById('chatSend');
  if (!msgContainer) return;

  // Initial welcome greeting
  appendBotMsg(`
    <div class="d-flex align-items-center gap-2 mb-2">
      <span class="badge bg-primary px-2 py-1">TNSTC AI Assistant</span>
      <span class="text-muted" style="font-size:11px">24x7 Live</span>
    </div>
    Hello! 👋 Welcome to <b>TNSTC Tirunelveli Smart Bus Portal</b>.<br>
    I am your AI Assistant. I can help you search bus timings, calculate fares, apply for student & monthly passes, track buses live, file grievances, and find depot helplines.
  `);
  appendInitialSuggestions();

  sendBtn?.addEventListener('click', handleSend);
  input?.addEventListener('keypress', e => { if (e.key === 'Enter') handleSend(); });

  function handleSend() {
    const text = input.value.trim();
    if (!text) return;
    appendUserMsg(text);
    input.value = '';
    showTyping();

    setTimeout(() => {
      removeTyping();
      const reply = getAdvancedReply(text);
      appendBotMsg(reply.html);
      if (reply.suggestions && reply.suggestions.length > 0) {
        appendPills(reply.suggestions);
      }
    }, 600);
  }

  function getAdvancedReply(rawText) {
    const q = rawText.toLowerCase().trim();

    // 1. Greetings & Small talk
    if (/^(hi|hello|hii|heyy|hey|vanakkam|வணக்கம்|good morning|good afternoon|good evening|who are you|help|start)$/i.test(q)) {
      return {
        html: `
          👋 <b>Hello there! How can I assist your travel today?</b><br>
          You can ask me about:
          <ul class="mb-2 ps-3 mt-1" style="font-size:12.5px">
            <li><b>Bus Timings & Routes</b> (e.g. <i>"Bus from Tirunelveli to Valliyoor"</i> or <i>"Route 77"</i>)</li>
            <li><b>Bus Passes</b> (Student Pass, Monthly Pass application process)</li>
            <li><b>Women's Free Travel</b> (Vidiyal Payanam scheme info)</li>
            <li><b>Live Tracking</b> & <b>Ticket Fares</b></li>
            <li><b>Grievances</b> & <b>Lost Property</b></li>
          </ul>`,
        suggestions: ['Bus from Tirunelveli to Valliyoor', 'Apply Student Pass', 'Women Free Travel Scheme', 'Live Bus Tracking', 'Contact Helplines']
      };
    }

    // 2. Women Free Travel Scheme (Vidiyal Payanam)
    if (q.includes('women') || q.includes('lady') || q.includes('ladies') || q.includes('free travel') || q.includes('pink bus') || q.includes('vidiyal') || q.includes('மகளிர்') || q.includes('இலவச')) {
      return {
        html: `
          🚌 <b>Vidiyal Payanam Scheme (மகளிர் இலவச பேருந்து பயணம்)</b><br><br>
          Under the Govt. of Tamil Nadu scheme, <b>100% Free Travel</b> is provided for all women, trans-gender citizens, and persons with disabilities in TNSTC <b>Ordinary Town Buses</b>.<br>
          <div class="mt-2 p-2 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:12px">
            ✓ No ticket fare charged<br>
            ✓ Applies across all ordinary town routes in Tirunelveli District<br>
            ✓ No pass required — simply board any Ordinary Town Bus!
          </div>`,
        suggestions: ['Search Town Buses', 'Apply Student Pass', 'View Bus Routes']
      };
    }

    // 3. Student & Monthly Bus Pass
    if (q.includes('pass') || q.includes('student') || q.includes('monthly') || q.includes('renewal') || q.includes('college')) {
      return {
        html: `
          💳 <b>TNSTC Bus Pass Portal Guide</b><br><br>
          <b>1. Student Bus Pass (Free/Concessional):</b>
          <ol class="ps-3 mb-2" style="font-size:12px">
            <li>Go to <b>Bus Passes → Apply Bus Pass</b></li>
            <li>Select <i>Student Pass</i> & enter your route</li>
            <li>Upload College/School ID & Bonafide Certificate</li>
            <li>Depot Manager will verify & approve within 2 working days.</li>
          </ol>
          <b>2. Monthly Commuter Pass:</b> Select source/destination, pay online & get instant QR pass.<br>
          <a href="/TNSTC/passenger/bus_pass.php" class="btn btn-sm btn-success mt-2 fw-bold" style="border-radius:6px"><i class="fa fa-id-card me-1"></i> Apply for Bus Pass Now →</a>`,
        suggestions: ['Pass Status Check', 'Required Documents', 'Search Bus Routes']
      };
    }

    // 4. Route & Bus Timings Lookup
    const matchedRoute = ROUTES_KB.find(r => 
      q.includes(r.source.toLowerCase()) || 
      q.includes(r.destination.toLowerCase()) || 
      q.includes(r.route_number.toLowerCase())
    );

    if (matchedRoute) {
      const routeSchedules = SCHEDULES_KB.filter(s => s.route_id == matchedRoute.route_id || s.route_number == matchedRoute.route_number);
      let scheduleHtml = '';
      if (routeSchedules.length > 0) {
        scheduleHtml = `<div class="mt-2 mb-2 p-2 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;font-size:12px">
          <b>Available Bus Timings:</b><br>` +
          routeSchedules.map(s => `• 🕒 <b>${s.departure_time}</b> - Bus <code>${s.bus_number}</code> (${s.bus_type.toUpperCase()})`).join('<br>') +
          `</div>`;
      }

      return {
        html: `
          🚌 <b>Route ${matchedRoute.route_number}: ${matchedRoute.source} ↔ ${matchedRoute.destination}</b><br>
          • <b>Distance:</b> ${matchedRoute.distance} km<br>
          • <b>Est. Travel Time:</b> ${matchedRoute.estimated_time} minutes<br>
          ${scheduleHtml}
          <a href="/TNSTC/passenger/search_bus.php?src=${encodeURIComponent(matchedRoute.source)}&dst=${encodeURIComponent(matchedRoute.destination)}" class="btn btn-sm btn-primary mt-2 fw-bold" style="border-radius:6px"><i class="fa fa-ticket me-1"></i> Book QR Ticket for this Route →</a>`,
        suggestions: ['Check Ticket Fares', 'Live Bus Tracking', 'Search Other Routes']
      };
    }

    // 5. General Route Search Request (e.g. "buses", "routes", "timings")
    if (q.includes('route') || q.includes('timing') || q.includes('schedule') || q.includes('bus timing') || q.includes('time table')) {
      const topRoutes = ROUTES_KB.slice(0, 4).map(r => `• <b>Route ${r.route_number}</b>: ${r.source} → ${r.destination} (${r.distance} km)`).join('<br>');
      return {
        html: `
          🗺️ <b>TNSTC Tirunelveli Major Bus Routes:</b><br><br>
          ${topRoutes}<br><br>
          Ask me about a specific destination like <i>"Bus from Tirunelveli to Valliyoor"</i> or <i>"Route 77"</i> to get exact departure timings!<br>
          <a href="/TNSTC/passenger/search_bus.php" class="btn btn-sm btn-primary mt-2 fw-bold" style="border-radius:6px"><i class="fa fa-magnifying-glass me-1"></i> Search All Schedules →</a>`,
        suggestions: ['Tirunelveli to Valliyoor', 'Tirunelveli to Papanasam', 'Palayamkottai to Cheranmahadevi']
      };
    }

    // 6. Fares & Ticket Cost
    if (q.includes('fare') || q.includes('price') || q.includes('cost') || q.includes('rate') || q.includes('how much')) {
      return {
        html: `
          💰 <b>TNSTC Standard Fare Structure (Tirunelveli District):</b><br>
          <ul class="ps-3 mb-2" style="font-size:12px">
            <li><b>Ordinary Town Bus:</b> ~₹0.70 per km <i>(Free for Women & Students)</i></li>
            <li><b>Mofussil Express:</b> ~₹0.90 per km</li>
            <li><b>Super Express:</b> ~₹1.10 per km</li>
            <li><b>AC Deluxe Express:</b> ~₹1.50 per km</li>
          </ul>
          Exact fare is automatically calculated at checkout based on source and destination.<br>
          <a href="/TNSTC/passenger/search_bus.php" class="btn btn-sm btn-outline-primary mt-2 fw-bold" style="border-radius:6px">Calculate Fare & Book →</a>`,
        suggestions: ['Book Ticket', 'Apply Student Pass', 'Live Bus Tracking']
      };
    }

    // 7. Live Bus Tracking
    if (q.includes('track') || q.includes('live') || q.includes('location') || q.includes('gps') || q.includes('where is')) {
      return {
        html: `
          📡 <b>Live Bus GPS Tracking</b><br><br>
          You can track active TNSTC buses in real-time on our interactive live map.<br>
          1. Go to <b>Live Bus Track</b> from sidebar<br>
          2. Select your bus number (e.g., <code>TN72 A 0001</code>)<br>
          3. View real-time GPS coordinates, speed, and delay status.<br>
          <a href="/TNSTC/passenger/live_tracking.php" class="btn btn-sm btn-warning text-dark mt-2 fw-bold" style="border-radius:6px"><i class="fa fa-map-location-dot me-1"></i> Open Live Map Tracker →</a>`,
        suggestions: ['Search Bus Schedules', 'Report Delay', 'Contact Control Room']
      };
    }

    // 8. Complaints & Grievances
    if (q.includes('complaint') || q.includes('complain') || q.includes('grievance') || q.includes('delay') || q.includes('staff') || q.includes('behavior') || q.includes('cleanliness')) {
      return {
        html: `
          📝 <b>Grievance Redressal Portal</b><br><br>
          You can submit official complaints regarding:<br>
          • Bus delays & schedule skips<br>
          • Driver / Conductor behavior<br>
          • Bus cleanliness or maintenance<br>
          • Ticket / Fare discrepancies<br><br>
          Depot Managers review and respond within 48 hours.<br>
          <a href="/TNSTC/passenger/complaints.php" class="btn btn-sm btn-danger mt-2 fw-bold" style="border-radius:6px"><i class="fa fa-comments me-1"></i> File a Grievance →</a>`,
        suggestions: ['Lost & Found Item', 'Contact Depot Manager', 'Tirunelveli Control Room']
      };
    }

    // 9. Lost & Found Items
    if (q.includes('lost') || q.includes('found') || q.includes('bag') || q.includes('wallet') || q.includes('phone') || q.includes('item') || q.includes('luggage')) {
      return {
        html: `
          🧳 <b>Lost & Found Assistance</b><br><br>
          If you forgot an item on a TNSTC bus:<br>
          1. Submit a Lost Item report with bus number & date.<br>
          2. Depot staff verify found property logged by conductors.<br>
          3. Visit the depot office to claim your property.<br>
          <a href="/TNSTC/passenger/lost_found.php" class="btn btn-sm btn-info text-white mt-2 fw-bold" style="border-radius:6px"><i class="fa fa-box-open me-1"></i> Report Lost Property →</a>`,
        suggestions: ['Contact Control Room', 'File Complaint', 'Depot List']
      };
    }

    // 10. Depots & Helplines
    if (q.includes('depot') || q.includes('helpline') || q.includes('phone') || q.includes('number') || q.includes('contact') || q.includes('office') || q.includes('address')) {
      let depotListHtml = DEPOTS_KB.map(d => `• <b>${d.depot_name}</b> – ${d.location}`).join('<br>');
      return {
        html: `
          📞 <b>TNSTC Tirunelveli Depots & Helplines</b><br><br>
          <b>Central Control Room:</b> <a href="tel:04622579801" class="fw-bold">0462-2579801</a><br>
          <b>Email Support:</b> <code>contact@tnstctirunelveli.in</code><br><br>
          <b>Operating Depots (7):</b><br>
          ${depotListHtml}`,
        suggestions: ['Search Bus', 'File Complaint', 'Apply Bus Pass']
      };
    }

    // 11. Tamil queries
    if (/[\u0B80-\u0BFF]/.test(q)) {
      return {
        html: `
          🙏 <b>வணக்கம்! TNSTC திருநெல்வேலி கணினி உதவியாளர்</b><br><br>
          பேருந்து நேரங்கள், வழித்தடங்கள், கட்டணம், மாணவ/மாதாந்திர பாஸ் மற்றும் நேரலை பேருந்து கண்காணிப்பு பற்றிய அனைத்து தகவல்களையும் பெறலாம்.<br>
          <div class="mt-2 p-2 rounded" style="background:#eff6ff;font-size:12px">
            உதாரண கேள்வி: <i>"Tirunelveli to Valliyoor bus timing"</i> அல்லது <i>"Apply Student Pass"</i>
          </div>`,
        suggestions: ['Bus from Tirunelveli to Valliyoor', 'Apply Student Pass', 'Women Free Travel Scheme']
      };
    }

    // 12. Smart Fallback with context suggestions
    return {
      html: `
        🤖 <b>I am here to help with your TNSTC transit questions!</b><br>
        I didn't quite catch that specific query, but here are the most requested services you can ask about:
        <ul class="mb-2 ps-3 mt-2" style="font-size:12.5px">
          <li><b>Route Timings:</b> <i>"Bus from Tirunelveli to Valliyoor"</i></li>
          <li><b>Passes:</b> <i>"How to apply student pass?"</i></li>
          <li><b>Tracking:</b> <i>"Live bus tracking"</i></li>
          <li><b>Schemes:</b> <i>"Women free bus scheme"</i></li>
        </ul>`,
      suggestions: ['Bus from Tirunelveli to Valliyoor', 'How to apply student pass?', 'Women Free Travel Scheme', 'Live Bus Tracking', 'Tirunelveli Control Room']
    };
  }

  function appendUserMsg(text) {
    msgContainer.innerHTML += `
      <div class="chat-msg user mb-3">
        <div class="chat-bubble shadow-sm">${escHtml(text)}</div>
      </div>`;
    scrollBottom();
  }

  function appendBotMsg(html) {
    msgContainer.innerHTML += `
      <div class="chat-msg bot mb-3">
        <div class="chatbot-avatar me-2" style="width:32px;height:32px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">🤖</div>
        <div class="chat-bubble shadow-sm" style="background:#ffffff;border:1px solid #e2e8f0;color:#0f172a;max-width:85%">${html}</div>
      </div>`;
    scrollBottom();
  }

  function appendInitialSuggestions() {
    const suggestions = [
      'Bus from Tirunelveli to Valliyoor',
      'How to apply student pass?',
      'Women Free Travel Scheme',
      'Live Bus Tracking',
      'File a complaint',
      'Tirunelveli Control Room'
    ];
    appendPills(suggestions);
  }

  function appendPills(suggestions) {
    const html = `
      <div class="d-flex flex-wrap gap-2 my-2 ms-4 ps-2">
        ${suggestions.map(s => `<button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold" style="font-size:11.5px;background:#ffffff;box-shadow:0 2px 6px rgba(0,0,0,0.03)" onclick="chatQuick('${escHtml(s)}')"><i class="fa fa-bolt me-1 text-warning"></i>${escHtml(s)}</button>`).join('')}
      </div>`;
    msgContainer.innerHTML += `<div class="chat-msg bot mb-2"><div style="width:100%">${html}</div></div>`;
    scrollBottom();
  }

  function showTyping() {
    msgContainer.innerHTML += `
      <div id="typingIndicator" class="chat-msg bot mb-3">
        <div class="chatbot-avatar me-2" style="width:32px;height:32px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">🤖</div>
        <div class="chat-bubble shadow-sm" style="padding:10px 16px;background:#ffffff;border:1px solid #e2e8f0">
          <span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>
        </div>
      </div>`;
    scrollBottom();
  }

  function removeTyping() {
    document.getElementById('typingIndicator')?.remove();
  }

  function scrollBottom() {
    msgContainer.scrollTop = msgContainer.scrollHeight;
  }
}

function chatQuick(text) {
  const input = document.getElementById('chatInput');
  if (input) { 
    input.value = text; 
    document.getElementById('chatSend')?.click(); 
  }
}

function escHtml(t) {
  return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Typing animation CSS
const typingStyle = document.createElement('style');
typingStyle.textContent = `
.typing-dot { display:inline-block;width:7px;height:7px;border-radius:50%;background:#2563eb;margin:0 2px;animation:bounce .9s infinite; }
.typing-dot:nth-child(2){animation-delay:.2s}
.typing-dot:nth-child(3){animation-delay:.4s}
@keyframes bounce{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-8px)}}`;
document.head.appendChild(typingStyle);

document.addEventListener('DOMContentLoaded', chatbotInit);

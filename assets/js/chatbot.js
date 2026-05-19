// ============================================================
// TNSTC – chatbot.js  (AI Route Assistant)
// ============================================================

const ROUTES_KB = window.TNSTC_ROUTES || [];

const FAQS = [
  { q: ['student pass','student bus pass','how to apply student','apply pass'],
    a: 'To apply for a Student Bus Pass:\n1. Login to your passenger account\n2. Go to <b>Bus Pass</b> → <b>Apply Student Pass</b>\n3. Enter route, upload your college ID card\n4. Submit — Depot Manager will verify within 2 working days.' },
  { q: ['monthly pass','monthly bus pass'],
    a: 'For a Monthly Bus Pass, go to <b>Passenger Dashboard → Bus Pass → Apply Monthly Pass</b>. Select your source and destination, pay online, and it will be approved by the depot.' },
  { q: ['complaint','complain','staff behavior','cleanliness'],
    a: 'You can file a complaint from <b>Passenger Dashboard → Complaints</b>. Categories include delay, staff behavior, cleanliness, ticket issue, and more.' },
  { q: ['lost','lost item','lost and found'],
    a: 'Report lost items from <b>Passenger Dashboard → Lost & Found → Report Lost Item</b>. You can also browse found items there.' },
  { q: ['track','live track','bus location','where is bus'],
    a: 'Track any TNSTC Tirunelveli bus live from <b>Passenger Dashboard → Live Tracking</b>. Select the bus number to see its current location on the map.' },
  { q: ['book ticket','book a ticket','online booking','how to book'],
    a: 'To book a ticket:\n1. Go to <b>Search Bus</b>\n2. Enter source and destination\n3. Select your bus and date\n4. Choose a seat and pay online\n5. Download your QR code ticket.' },
  { q: ['depot','depot list','tirunelveli depot'],
    a: 'TNSTC Tirunelveli operates <b>7 depots</b>:<br>1. Thamirabarani Depot – Vannarpettai<br>2. Bye-Pass Depot – Vannarpettai<br>3. Kattabomman Nagar Depot – KTC Nagar<br>4. Cheranmahadevi Depot<br>5. Valliyoor Depot<br>6. Thisayanvilai Depot<br>7. Papanasam Depot' },
  { q: ['contact','helpline','phone number'],
    a: 'For help contact TNSTC Tirunelveli control room: <b>0462-2579801</b> or email <b>contact@tnstctirunelveli.in</b>' },
];

function chatbotInit() {
  const msgContainer = document.getElementById('chatMessages');
  const input = document.getElementById('chatInput');
  const sendBtn = document.getElementById('chatSend');
  if (!msgContainer) return;

  // Greeting
  appendBotMsg('Hello! 👋 I am the <b>TNSTC Route Assistant</b>. Ask me anything about Tirunelveli district buses — routes, timings, booking, passes, and more!');
  appendSuggestions();

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
      const reply = getReply(text.toLowerCase());
      appendBotMsg(reply);
    }, 700);
  }

  function getReply(q) {
    // FAQ match
    for (const faq of FAQS) {
      if (faq.q.some(kw => q.includes(kw))) return faq.a;
    }

    // Route search
    const routeMatch = ROUTES_KB.find(r =>
      q.includes(r.source.toLowerCase()) || q.includes(r.destination.toLowerCase())
    );
    if (routeMatch) {
      return `🚌 <b>Route ${routeMatch.route_number}</b>: ${routeMatch.source} → ${routeMatch.destination}<br>
Distance: ${routeMatch.distance} km | Est. time: ${routeMatch.estimated_time} min<br>
<a href="/TNSTC/passenger/search_bus.php?src=${encodeURIComponent(routeMatch.source)}&dst=${encodeURIComponent(routeMatch.destination)}" class="text-primary">Book a ticket →</a>`;
    }

    return "I'm sorry, I couldn't find specific info for that query. Try asking about a route like <i>\"Bus from Tirunelveli to Valliyoor\"</i>, or ask about <i>student pass</i>, <i>ticket booking</i>, or <i>live tracking</i>.";
  }

  function appendUserMsg(text) {
    msgContainer.innerHTML += `
      <div class="chat-msg user">
        <div class="chat-bubble">${escHtml(text)}</div>
      </div>`;
    scrollBottom();
  }

  function appendBotMsg(html) {
    msgContainer.innerHTML += `
      <div class="chat-msg bot">
        <div class="chatbot-avatar" style="font-size:16px;flex-shrink:0">🤖</div>
        <div class="chat-bubble">${html}</div>
      </div>`;
    scrollBottom();
  }

  function appendSuggestions() {
    const suggestions = ['Bus from Tirunelveli to Valliyoor','How to apply student pass?','Track my bus','Report lost item','File a complaint'];
    const html = `<div class="d-flex flex-wrap gap-2 mt-1">
      ${suggestions.map(s => `<button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="chatQuick('${escHtml(s)}')">${escHtml(s)}</button>`).join('')}
    </div>`;
    msgContainer.innerHTML += `<div class="chat-msg bot"><div class="chat-bubble">${html}</div></div>`;
    scrollBottom();
  }

  function showTyping() {
    msgContainer.innerHTML += `<div id="typingIndicator" class="chat-msg bot">
      <div class="chatbot-avatar" style="font-size:16px;flex-shrink:0">🤖</div>
      <div class="chat-bubble" style="padding:10px 16px">
        <span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>
      </div></div>`;
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
  if (input) { input.value = text; document.getElementById('chatSend')?.click(); }
}

function escHtml(t) {
  return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Typing animation CSS (injected)
const typingStyle = document.createElement('style');
typingStyle.textContent = `
.typing-dot { display:inline-block;width:7px;height:7px;border-radius:50%;background:#6c757d;margin:0 2px;animation:bounce .9s infinite; }
.typing-dot:nth-child(2){animation-delay:.2s}
.typing-dot:nth-child(3){animation-delay:.4s}
@keyframes bounce{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-8px)}}`;
document.head.appendChild(typingStyle);

document.addEventListener('DOMContentLoaded', chatbotInit);

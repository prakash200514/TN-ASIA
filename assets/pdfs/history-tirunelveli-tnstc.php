<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History of TNSTC Tirunelveli District</title>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Noto Serif', Georgia, serif;
      background: #fff;
      color: #1a1a1a;
      line-height: 1.75;
    }

    /* ── Print Controls ── */
    .print-controls {
      position: fixed;
      top: 16px; right: 16px;
      display: flex;
      gap: 10px;
      z-index: 9999;
    }
    .print-controls button {
      background: #1a3a6b;
      color: #fff;
      border: none;
      padding: 10px 22px;
      border-radius: 8px;
      font-family: 'Outfit', sans-serif;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 12px rgba(26,58,107,.35);
      transition: background .2s;
    }
    .print-controls button:hover { background: #142d52; }
    .print-controls .btn-close-pdf {
      background: #64748b;
    }
    .print-controls .btn-close-pdf:hover { background: #475569; }

    @media print {
      .print-controls { display: none; }
    }

    /* ── Document ── */
    .doc-page {
      max-width: 860px;
      margin: 0 auto;
      padding: 60px 70px;
      min-height: 100vh;
    }

    /* ── Header ── */
    .doc-header {
      text-align: center;
      border-bottom: 3px double #1a3a6b;
      padding-bottom: 28px;
      margin-bottom: 36px;
    }
    .doc-header .emblem-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      margin-bottom: 16px;
    }
    .doc-header .emblem-row img {
      width: 70px;
      height: 70px;
      object-fit: contain;
    }
    .doc-header .gov-title {
      text-align: left;
    }
    .doc-header .gov-title h2 {
      font-family: 'Outfit', sans-serif;
      font-size: 15px;
      font-weight: 700;
      color: #1a3a6b;
      letter-spacing: .5px;
    }
    .doc-header .gov-title p {
      font-family: 'Outfit', sans-serif;
      font-size: 12px;
      color: #64748b;
      margin-top: 2px;
    }
    .doc-header h1 {
      font-family: 'Outfit', sans-serif;
      font-size: 26px;
      font-weight: 800;
      color: #1a3a6b;
      margin: 12px 0 6px;
      letter-spacing: -.4px;
    }
    .doc-header .subtitle {
      font-family: 'Outfit', sans-serif;
      font-size: 13.5px;
      color: #64748b;
      font-weight: 500;
    }
    .doc-header .tag-row {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-top: 14px;
      flex-wrap: wrap;
    }
    .doc-header .tag {
      background: #eef2ff;
      color: #1a3a6b;
      font-family: 'Outfit', sans-serif;
      font-size: 11px;
      font-weight: 600;
      padding: 4px 14px;
      border-radius: 20px;
      letter-spacing: .3px;
      border: 1px solid #c7d2fe;
    }

    /* ── Sections ── */
    .section {
      margin-bottom: 36px;
    }
    .section-title {
      font-family: 'Outfit', sans-serif;
      font-size: 17px;
      font-weight: 800;
      color: #1a3a6b;
      border-left: 5px solid #FF671F;
      padding-left: 14px;
      margin-bottom: 14px;
      letter-spacing: -.2px;
    }
    .section p {
      font-size: 14.5px;
      color: #2d3748;
      margin-bottom: 12px;
      text-align: justify;
    }

    /* ── Timeline ── */
    .timeline {
      border-left: 3px solid #1a3a6b;
      padding-left: 24px;
      margin: 8px 0 0 10px;
    }
    .timeline-item {
      position: relative;
      margin-bottom: 22px;
      padding-bottom: 6px;
    }
    .timeline-item::before {
      content: '';
      position: absolute;
      left: -31px;
      top: 5px;
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: #FF671F;
      border: 3px solid #fff;
      box-shadow: 0 0 0 2px #FF671F;
    }
    .timeline-year {
      font-family: 'Outfit', sans-serif;
      font-size: 13px;
      font-weight: 700;
      color: #FF671F;
      letter-spacing: .5px;
      margin-bottom: 3px;
    }
    .timeline-desc {
      font-size: 14px;
      color: #374151;
      line-height: 1.6;
    }

    /* ── Stats Grid ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-top: 16px;
    }
    .stat-box {
      background: #f0f4ff;
      border: 1px solid #c7d2fe;
      border-radius: 10px;
      padding: 18px 16px;
      text-align: center;
    }
    .stat-box .stat-num {
      font-family: 'Outfit', sans-serif;
      font-size: 28px;
      font-weight: 800;
      color: #1a3a6b;
      display: block;
    }
    .stat-box .stat-lbl {
      font-family: 'Outfit', sans-serif;
      font-size: 11.5px;
      color: #64748b;
      font-weight: 600;
      margin-top: 4px;
      text-transform: uppercase;
      letter-spacing: .5px;
    }

    /* ── Depot Table ── */
    .depot-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 14px;
      font-size: 13.5px;
    }
    .depot-table th {
      background: #1a3a6b;
      color: #fff;
      font-family: 'Outfit', sans-serif;
      font-weight: 600;
      padding: 10px 14px;
      text-align: left;
      font-size: 13px;
    }
    .depot-table td {
      padding: 10px 14px;
      border-bottom: 1px solid #e2e8f0;
      color: #374151;
    }
    .depot-table tr:nth-child(even) td { background: #f8fafc; }

    /* ── Quote block ── */
    .blockquote {
      background: linear-gradient(135deg, #eef2ff, #fff7ed);
      border-left: 5px solid #FF671F;
      border-radius: 0 10px 10px 0;
      padding: 18px 22px;
      margin: 16px 0;
      font-style: italic;
      font-size: 14.5px;
      color: #1e293b;
    }
    .blockquote cite {
      display: block;
      margin-top: 8px;
      font-style: normal;
      font-family: 'Outfit', sans-serif;
      font-size: 12px;
      font-weight: 600;
      color: #1a3a6b;
    }

    /* ── Footer ── */
    .doc-footer {
      margin-top: 48px;
      padding-top: 20px;
      border-top: 2px solid #e2e8f0;
      text-align: center;
      font-family: 'Outfit', sans-serif;
      font-size: 11.5px;
      color: #94a3b8;
    }
    .doc-footer strong { color: #1a3a6b; }

    @media print {
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .doc-page { padding: 20px 30px; }
    }
  </style>
</head>
<body>

<div class="print-controls">
  <button onclick="window.print()">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
      <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
      <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
    </svg>
    Download / Print PDF
  </button>
  <button class="btn-close-pdf" onclick="window.close()">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
    </svg>
    Close
  </button>
</div>

<div class="doc-page">

  <!-- Header -->
  <div class="doc-header">
    <div class="emblem-row">
      <img src="http://localhost/TNSTC/assets/images/logo.svg" alt="Tamil Nadu Government Emblem">
      <div class="gov-title">
        <h2>Tamil Nadu State Transport Corporation</h2>
        <h2>Tirunelveli District</h2>
        <p>A Government of Tamil Nadu Undertaking</p>
      </div>
    </div>
    <h1>History of TNSTC – Tirunelveli District</h1>
    <div class="subtitle">An Official Historical Document — Transport Corporation Heritage</div>
    <div class="tag-row">
      <span class="tag">📍 Tirunelveli, Tamil Nadu</span>
      <span class="tag">🏛️ Established 1972</span>
      <span class="tag">🚌 7 Depots Operational</span>
      <span class="tag">📅 Document Year: 2026</span>
    </div>
  </div>

  <!-- Section 1: Introduction -->
  <div class="section">
    <div class="section-title">1. Introduction to Tirunelveli District</div>
    <p>
      Tirunelveli, historically known as <em>Nellai</em>, is one of the oldest and most culturally significant districts in Tamil Nadu, India. 
      Situated in the southernmost part of the state, it is bounded by the Western Ghats to the north and west, and the Gulf of Mannar to the east. 
      The district is watered by the Tamirabarani River — the only perennial river in southern Tamil Nadu — which is known for its crystal-clear waters and remarkable biodiversity.
    </p>
    <p>
      The name "Tirunelveli" is derived from the Tamil words <em>Thiru</em> (sacred), <em>Nel</em> (paddy), and <em>Veli</em> (fence/boundary), meaning "Sacred Land of Paddy Fields." 
      The district has been a major administrative and commercial center since ancient times, with references found in Sangam literature and early medieval inscriptions.
    </p>
    <div class="blockquote">
      "Tirunelveli is a land of warriors, scholars, and saints. Its history is as deep as the Tamirabarani and as vast as the Western Ghats that guard it."
      <cite>— Regional Heritage Documentation, Government of Tamil Nadu</cite>
    </div>
  </div>

  <!-- Section 2: Ancient & Medieval History -->
  <div class="section">
    <div class="section-title">2. Ancient & Medieval History</div>
    <p>
      Tirunelveli has been inhabited since prehistoric times, with evidence of Megalithic culture discovered in several archaeological sites across the district. 
      The region formed part of the ancient Pandya Kingdom, one of the three great Tamil kingdoms alongside Chola and Chera. 
      Pandyan rulers made Tirunelveli a significant administrative division, with strong religious and cultural institutions.
    </p>
    <p>
      During the Sangam age (300 BCE – 300 CE), the region was a prosperous trade center known for its agricultural produce, especially rice (nel), 
      which was exported through the Tamirabarani river delta. Poets of the classical Sangam period celebrated the lush paddy fields and the Tamirabarani river.
    </p>
    <p>
      In the medieval period, Tirunelveli came under the rule of the Pandya dynasty, later the Chola rulers, and then the Nayaks of Madurai. 
      The Nayak period (16th–17th century) saw significant architectural developments including the famous Nellaiappar Temple complex — 
      one of the largest and most ornate temples in Tamil Nadu, built in the traditional Dravidian style.
    </p>
  </div>

  <!-- Section 3: Colonial Era -->
  <div class="section">
    <div class="section-title">3. Colonial Era & Modern Development</div>
    <p>
      The British East India Company gained control of Tirunelveli in 1801 following the cession of the region after the defeat of the Polygar chiefs. 
      The district became a critical administrative unit under the Madras Presidency. 
      The British introduced several infrastructure improvements including roads, railways, and telegraph systems.
    </p>
    <p>
      Tirunelveli was a hotbed of the Indian Independence Movement. 
      The legendary Kattabomman (Veerapandiya Kattabomman) staged a valiant resistance against British rule in 1799 and became a symbol of Tamil pride and anti-colonial resistance. 
      Cheranmahadevi and Sankarankovil were centers of peasant revolts. 
      The Tuticorin (Thoothukudi) district was carved out of Tirunelveli in 1986.
    </p>
  </div>

  <!-- Section 4: Transport History Timeline -->
  <div class="section">
    <div class="section-title">4. History of Public Transport in Tirunelveli</div>
    <p>
      The evolution of public transportation in Tirunelveli mirrors the development of the broader Tamil Nadu transport network. 
      From bullock-cart services to a modern fleet of air-conditioned buses, the journey spans over a century of progress.
    </p>

    <div class="timeline">

      <div class="timeline-item">
        <div class="timeline-year">Early 1900s – Pre-Independence Era</div>
        <div class="timeline-desc">
          The first mechanized transport services in the Tirunelveli region began with private operators running motor-car-based 
          services between major towns. These were rudimentary operations covering routes like Tirunelveli–Palayamkottai and Tirunelveli–Tuticorin.
        </div>
      </div>

      <div class="timeline-item">
        <div class="timeline-year">1947 – Post-Independence Nationalization Moves</div>
        <div class="timeline-desc">
          After Indian Independence, the government of Madras State began considering nationalization of bus transport services. 
          Private operators in Tirunelveli, though numerous, lacked uniformity in service standards, fare structures, and coverage of rural routes.
        </div>
      </div>

      <div class="timeline-item">
        <div class="timeline-year">1950–1960 – State Road Transport Corporation Act</div>
        <div class="timeline-desc">
          The Central Road Transport Corporation Act (1950) provided the legal framework for state-owned transport corporations. 
          Tamil Nadu began progressively nationalizing bus routes. Tirunelveli routes were brought under state control in phases throughout this decade.
        </div>
      </div>

      <div class="timeline-item">
        <div class="timeline-year">1972 – Formation of TNSTC Tirunelveli</div>
        <div class="timeline-desc">
          The Tamil Nadu State Transport Corporation (Tirunelveli) Limited was formally constituted under the Companies Act. 
          Initially, the corporation operated with a small fleet of approximately 200 buses covering major inter-city and rural routes across the southern districts.
        </div>
      </div>

      <div class="timeline-item">
        <div class="timeline-year">1975–1985 – Rapid Fleet Expansion</div>
        <div class="timeline-desc">
          The corporation underwent rapid expansion under state government investment. 
          New depots were established at Cheranmahadevi, Valliyoor, and Thisayanvilai to serve the growing population and expanding road network. 
          The fleet grew to over 700 buses by 1985.
        </div>
      </div>

      <div class="timeline-item">
        <div class="timeline-year">1986 – District Reorganization</div>
        <div class="timeline-desc">
          When Tuticorin (Thoothukudi) was carved out as a separate district, the TNSTC Tirunelveli network was reorganized. 
          Routes, depots, and staff were redistributed to maintain seamless connectivity across both new districts. 
          Tirunelveli retained its status as the primary operational hub.
        </div>
      </div>

      <div class="timeline-item">
        <div class="timeline-year">1990–2000 – Service Diversification</div>
        <div class="timeline-desc">
          Introduction of express and super-express services to Chennai, Coimbatore, Madurai, and Kanyakumari. 
          Night services were launched to serve passengers on long-distance routes. 
          The Kattabomman Nagar depot was upgraded to a major maintenance center.
        </div>
      </div>

      <div class="timeline-item">
        <div class="timeline-year">2005–2010 – Modernization Phase</div>
        <div class="timeline-desc">
          Introduction of semi-luxury and air-conditioned buses on key routes. 
          Computer-based ticketing systems were piloted in Tirunelveli. 
          The Thamirabarani and Bye-Pass depots underwent modernization with new workshop equipment.
        </div>
      </div>

      <div class="timeline-item">
        <div class="timeline-year">2015–2020 – Digital Transformation</div>
        <div class="timeline-desc">
          Online bus pass and reservation systems were introduced. 
          GPS-based bus tracking systems were installed on select buses. 
          The fleet transitioned progressively toward BS-IV and BS-VI compliant vehicles, 
          reducing carbon emissions significantly across all seven depots.
        </div>
      </div>

      <div class="timeline-item">
        <div class="timeline-year">2022–Present – Smart Mobility Era</div>
        <div class="timeline-desc">
          TNSTC Tirunelveli embraced the Smart Mobility initiative with digital ticketing, real-time bus tracking via mobile apps, 
          QR-code-based ticketing, AI-powered complaint management, and online seat reservation. 
          Electric bus trials commenced on select urban routes. The TNSTC Smart Bus System platform was launched for Tirunelveli District.
        </div>
      </div>

    </div>
  </div>

  <!-- Section 5: Stats -->
  <div class="section">
    <div class="section-title">5. Current Operations – Key Statistics</div>
    <p>As of 2026, TNSTC Tirunelveli is one of the most extensive regional transport networks in Southern Tamil Nadu:</p>
    <div class="stats-grid">
      <div class="stat-box">
        <span class="stat-num">7</span>
        <div class="stat-lbl">Operational Depots</div>
      </div>
      <div class="stat-box">
        <span class="stat-num">800+</span>
        <div class="stat-lbl">Buses in Fleet</div>
      </div>
      <div class="stat-box">
        <span class="stat-num">2,00,000+</span>
        <div class="stat-lbl">Daily Passengers</div>
      </div>
      <div class="stat-box">
        <span class="stat-num">500+</span>
        <div class="stat-lbl">Routes Operated</div>
      </div>
      <div class="stat-box">
        <span class="stat-num">5,000+</span>
        <div class="stat-lbl">Employees</div>
      </div>
      <div class="stat-box">
        <span class="stat-num">100+</span>
        <div class="stat-lbl">Bus Stops</div>
      </div>
    </div>
  </div>

  <!-- Section 6: Depots -->
  <div class="section">
    <div class="section-title">6. Seven Depots of TNSTC Tirunelveli</div>
    <p>The TNSTC Tirunelveli Corporation operates through seven strategically located depots across the district:</p>
    <table class="depot-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Depot Name</th>
          <th>Location</th>
          <th>Established</th>
          <th>Key Routes</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>1</td><td>Thamirabarani Depot</td><td>Vannarpettai, Tirunelveli</td><td>1972</td><td>City & Inter-District</td></tr>
        <tr><td>2</td><td>Bye-Pass Depot</td><td>Vannarpettai, Tirunelveli</td><td>1978</td><td>Long-Distance Express</td></tr>
        <tr><td>3</td><td>Kattabomman Nagar Depot</td><td>KTC Nagar, Tirunelveli</td><td>1982</td><td>Urban & Suburban</td></tr>
        <tr><td>4</td><td>Cheranmahadevi Depot</td><td>Cheranmahadevi</td><td>1975</td><td>Rural Connectivity</td></tr>
        <tr><td>5</td><td>Valliyoor Depot</td><td>Valliyoor</td><td>1980</td><td>Southern District Routes</td></tr>
        <tr><td>6</td><td>Thisayanvilai Depot</td><td>Thisayanvilai</td><td>1983</td><td>Coastal & Rural Routes</td></tr>
        <tr><td>7</td><td>Papanasam Depot</td><td>Papanasam</td><td>1990</td><td>Ghat Routes & Hill Stations</td></tr>
      </tbody>
    </table>
  </div>

  <!-- Section 7: Vision -->
  <div class="section">
    <div class="section-title">7. Vision for the Future</div>
    <p>
      TNSTC Tirunelveli is committed to transforming public transport in the region through sustainable, technology-driven, 
      and passenger-friendly initiatives:
    </p>
    <p>
      <strong>Electric Bus Fleet:</strong> Plans to introduce 100 electric buses by 2028, reducing carbon footprint by over 40%. 
      Solar-powered charging stations are being planned at Thamirabarani and Kattabomman Nagar depots.
    </p>
    <p>
      <strong>Smart Connectivity:</strong> Expansion of real-time bus tracking, AI-based scheduling, and integrated digital 
      payment systems across all routes by 2027.
    </p>
    <p>
      <strong>Rural Inclusion:</strong> Launch of last-mile connectivity services to 150+ remote villages in the district 
      that currently lack regular public transport access.
    </p>
    <div class="blockquote">
      "Our mission is to make public transport the first choice for every citizen of Tirunelveli — safe, affordable, reliable, and modern."
      <cite>— TNSTC Tirunelveli, Corporate Vision Statement 2026</cite>
    </div>
  </div>

  <!-- Footer -->
  <div class="doc-footer">
    <strong>Tamil Nadu State Transport Corporation (Tirunelveli) Limited</strong><br>
    A Government of Tamil Nadu Undertaking | Registered under the Companies Act<br>
    Head Office: Tirunelveli, Tamil Nadu – 627 002 | Tel: 0462-2335678<br><br>
    Document prepared for official reference. © <?php echo date('Y'); ?> TNSTC Tirunelveli. All Rights Reserved.
  </div>

</div>
</body>
</html>

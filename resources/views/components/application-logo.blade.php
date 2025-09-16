<svg xmlns="http://www.w3.org/2000/svg" width="1024" height="1024" viewBox="0 0 1024 1024" role="img" aria-labelledby="title desc" {{ $attributes }}>
  <title>macOS Update Notifier — Square Logo</title>
  <desc>A modern, glassy square icon showing an envelope with a circular update arrow and a small notification badge, representing email-based macOS update notifications.</desc>

  <!-- Background with subtle gradient -->
  <defs>
    <linearGradient id="bg" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0%" stop-color="#0ea5e9"/>
      <stop offset="55%" stop-color="#6366f1"/>
      <stop offset="100%" stop-color="#8b5cf6"/>
    </linearGradient>

    <!-- Soft inner glow -->
    <radialGradient id="glow" cx="50%" cy="35%" r="65%">
      <stop offset="0%" stop-color="white" stop-opacity="0.35"/>
      <stop offset="60%" stop-color="white" stop-opacity="0.08"/>
      <stop offset="100%" stop-color="white" stop-opacity="0"/>
    </radialGradient>

    <!-- Envelope gradient -->
    <linearGradient id="env" x1="0" x2="0" y1="0" y2="1">
      <stop offset="0%" stop-color="#ffffff" stop-opacity="0.95"/>
      <stop offset="100%" stop-color="#e5e7eb" stop-opacity="0.9"/>
    </linearGradient>

    <!-- Arrow gradient -->
    <linearGradient id="arr" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0%" stop-color="#2563eb"/>
      <stop offset="100%" stop-color="#22d3ee"/>
    </linearGradient>

    <!-- Badge gradient -->
    <linearGradient id="badge" x1="0" x2="0" y1="0" y2="1">
      <stop offset="0%" stop-color="#ef4444"/>
      <stop offset="100%" stop-color="#b91c1c"/>
    </linearGradient>

    <!-- Shadow filter -->
    <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="8" stdDeviation="18" flood-color="#0f172a" flood-opacity="0.28"/>
    </filter>

    <!-- Inner shadow for depth -->
    <filter id="inner" x="-50%" y="-50%" width="200%" height="200%">
      <feOffset dx="0" dy="2"/>
      <feGaussianBlur stdDeviation="6" result="blur"/>
      <feComposite in="SourceGraphic" in2="blur" operator="arithmetic" k2="-1" k3="2"/>
    </filter>
  </defs>

  <!-- Rounded square app tile -->
  <rect x="64" y="64" width="896" height="896" rx="200" fill="url(#bg)" />

  <!-- Glassy soft light -->
  <ellipse cx="512" cy="360" rx="420" ry="300" fill="url(#glow)" />

  <!-- Envelope container -->
  <g transform="translate(170, 300)" filter="url(#shadow)">
    <!-- Envelope body -->
    <rect x="90" y="140" width="604" height="320" rx="28" fill="url(#env)" filter="url(#inner)"/>
    <!-- Envelope flap -->
    <path d="M90,180 L392,350 L694,180 L694,140 L90,140 Z" fill="#f8fafc" opacity="0.95"/>
    <!-- Envelope fold highlights -->
    <path d="M90,140 L392,360 L694,140" fill="none" stroke="#d1d5db" stroke-width="3" opacity="0.7"/>
  </g>

  <!-- Circular update arrow around the envelope -->
  <g transform="translate(0, 0)">
    <!-- Outer ring -->
    <circle cx="512" cy="480" r="210" fill="none" stroke="white" stroke-opacity="0.45" stroke-width="16"/>
    <!-- Progress arc with arrow head -->
    <path d="M682,480
             A170,170 0 1 1 420,360" fill="none" stroke="url(#arr)" stroke-width="28" stroke-linecap="round"/>
    <!-- Arrow head -->
    <path d="M420,360 L450,370 L438,400 Z" fill="url(#arr)"/>
  </g>

  <!-- Check mark badge to imply 'ready/ok' -->
  <g transform="translate(650, 610)">
    <circle cx="0" cy="0" r="70" fill="white" opacity="0.98" filter="url(#shadow)"/>
    <path d="M-28,4 L-6,26 L32,-20" fill="none" stroke="url(#arr)" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>
  </g>

  <!-- Notification badge (new update) -->
  <g transform="translate(720, 260)" filter="url(#shadow)">
    <circle cx="0" cy="0" r="46" fill="url(#badge)"/>
    <circle cx="0" cy="0" r="22" fill="white" opacity="0.9"/>
  </g>

  <!-- Subtle highlights -->
  <path d="M164,180 C320,110 704,100 860,180" fill="none" stroke="white" stroke-opacity="0.25" stroke-width="18" stroke-linecap="round"/>
  <path d="M164,840 C340,900 684,910 860,840" fill="none" stroke="black" stroke-opacity="0.12" stroke-width="14" stroke-linecap="round"/>

  <!-- Safe area guide (hidden by default) -->
  <!-- <rect x="144" y="144" width="736" height="736" rx="160" fill="none" stroke="white" stroke-dasharray="10 12" opacity="0.25"/> -->
</svg>

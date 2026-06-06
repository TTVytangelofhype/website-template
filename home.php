/*

* Designed and owned by TTVytangelofhype
* Copyright (c) 2026 TTVytangelofhype
*
* You may edit and use this code for personal/private use.
* You are not allowed to redistribute, re-upload, resell, or claim this code as your own.
*
* Full licence terms are available in the LICENSE file.
  */
<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="UTF-8">
<title>Website Name Goes Here</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root {
    --neon-pink:#ff2ad4;
    --neon-blue:#18ffff;
    --glass-bg:rgba(10,0,30,.72);
    --glass-border:rgba(255,255,255,.15);
}

* { box-sizing:border-box; }

html, body {
    margin:0;
    padding:0;
    width:100%;
    min-height:100%;
    overflow-x:hidden;
    overflow-y:auto;
    background:transparent;
    font-family:'Segoe UI', Roboto, sans-serif;
    color:#fff;
    user-select:none;
}

.real-space-bg {
    position:fixed;
    inset:0;
    background:url('images/Trancescape_Radio_background.png') no-repeat center center;
    background-size:cover;
    z-index:-10;
    animation:universePulse 10s ease-in-out infinite alternate;
}

@keyframes universePulse {
    0% { transform:scale(1); }
    100% { transform:scale(1.05); }
}

#vortexCanvas {
    position:fixed;
    inset:0;
    z-index:-5;
    mix-blend-mode:screen;
    pointer-events:none;
}

@keyframes neonBreathe {
    0% {
        border-color:var(--neon-blue);
        box-shadow:0 0 15px rgba(24,255,255,.2), inset 0 0 15px rgba(24,255,255,.1), 0 0 30px var(--neon-blue);
    }
    100% {
        border-color:var(--neon-pink);
        box-shadow:0 0 20px rgba(255,42,212,.3), inset 0 0 20px rgba(255,42,212,.1), 0 0 50px var(--neon-pink);
    }
}

header {
    display:none;
}

.logo-corner {
    width:150px;
    max-width:42vw;
    filter:drop-shadow(0 0 15px var(--neon-blue));
}

.floating-logo-link {
    position:fixed;
    top:16px;
    left:70px;
    z-index:2500;
    background:var(--glass-bg);
    backdrop-filter:blur(18px);
    border:2px solid var(--neon-blue);
    animation:neonBreathe 4s ease-in-out infinite alternate;
    border-radius:18px;
    box-shadow:0 0 25px rgba(24,255,255,.25);
    text-decoration:none;
    padding:8px;
}

.floating-logo-link img {
    width:96px;
    display:block;
    filter:drop-shadow(0 0 12px var(--neon-blue));
}

.amazon-space-sign {
    position:fixed;
    right:24px;
    top:50%;
    transform:translateY(-50%);
    z-index:1800;
    width:190px;
    background:var(--glass-bg);
    backdrop-filter:blur(18px);
    border:2px solid var(--neon-blue);
    animation:neonBreathe 4s ease-in-out infinite alternate, spaceFloat 4.5s ease-in-out infinite;
    border-radius:22px;
    box-shadow:0 0 25px rgba(24,255,255,.25);
    text-decoration:none;
    padding:10px;
    color:#fff;
    text-align:center;
}

.amazon-space-sign img {
    width:100%;
    display:block;
    border-radius:16px;
    margin-bottom:8px;
}

.amazon-space-sign span {
    display:block;
    color:var(--neon-blue);
    text-shadow:0 0 10px var(--neon-blue);
    font-size:.8rem;
    font-weight:900;
    letter-spacing:1px;
    text-transform:uppercase;
}

@keyframes spaceFloat {
    0%, 100% {
        transform:translateY(-50%) translateX(0);
    }
    50% {
        transform:translateY(calc(-50% - 18px)) translateX(0);
    }
}

.amazon-space-sign:hover {
    animation-play-state:paused;
    transform:translateY(-50%) scale(1.03);
}

.side-nav {
    position:fixed;
    left:0;
    top:50%;
    transform:translateY(-50%) translateX(-100%);
    width:220px;
    background:var(--glass-bg);
    backdrop-filter:blur(20px);
    border:2px solid var(--neon-blue);
    border-left:none;
    border-radius:0 30px 30px 0;
    z-index:2000;
    transition:transform .4s cubic-bezier(.175,.885,.32,1.275);
    padding:30px 15px;
    animation:neonBreathe 4s ease-in-out infinite alternate;
}

@media (min-width:1101px) {
    .side-nav { transform:translateY(-50%) translateX(-180px); }
    .side-nav:hover { transform:translateY(-50%) translateX(0); }
}

.side-nav.active { transform:translateY(-50%) translateX(0); }

.nav-link {
    display:block;
    text-decoration:none;
    color:#fff;
    font-family:monospace;
    font-size:.8rem;
    padding:15px;
    margin:10px 0;
    border-radius:10px;
    transition:.3s;
    text-align:center;
    background:rgba(255,255,255,.03);
    border:1px solid transparent;
}

.nav-link:hover {
    border-color:var(--neon-blue);
    background:rgba(24,255,255,.1);
    color:var(--neon-blue);
}

#nav-toggle {
    position:fixed;
    left:10px;
    top:20px;
    z-index:2100;
    background:var(--glass-bg);
    border:1px solid var(--neon-blue);
    color:var(--neon-blue);
    padding:10px;
    border-radius:5px;
    font-size:.7rem;
    letter-spacing:2px;
    display:none;
    cursor:pointer;
}

#cockpit-hud {
    position:fixed;
    inset:0;
    z-index:1100;
    pointer-events:none;
}

.strut {
    position:absolute;
    background:rgba(10,10,16,.95);
    border:2px solid var(--neon-blue);
    width:400px;
    height:30px;
    animation:neonBreathe 5s ease-in-out infinite alternate;
}

.top-left {
    top:-15px;
    left:-120px;
    transform:rotate(45deg);
}

.top-right {
    top:-15px;
    right:-120px;
    transform:rotate(-45deg);
}

.main-container {
    position:relative;
    z-index:40;
    width:100%;
    max-width:1240px;
    margin:0 auto;
    padding:34px 22px 40px;
    display:grid;
    grid-template-columns:minmax(260px,1fr) minmax(0,1.45fr) minmax(220px,.85fr);
    gap:18px;
    align-items:stretch;
}

.bottom-grid {
    position:relative;
    z-index:40;
    width:100%;
    max-width:1240px;
    margin:0 auto;
    padding:0 22px 70px;
    display:grid;
    grid-template-columns:minmax(260px,1fr) minmax(280px,1fr) minmax(280px,1.15fr);
    gap:18px;
    align-items:stretch;
}

.hero-card,
.player-card,
.quick-card,
.instruction-box,
.request-name-box,
.request-panel,
.cbox-box {
    background:var(--glass-bg);
    backdrop-filter:blur(25px);
    border:2px solid var(--neon-blue);
    animation:neonBreathe 4s ease-in-out infinite alternate;
    border-radius:25px;
    box-shadow:0 24px 70px rgba(0,0,0,.35);
    overflow:hidden;
    min-width:0;
}

.hero-card,
.player-card,
.quick-card {
    min-height:360px;
    padding:22px;
}

.hero-card {
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.status-pill {
    display:flex;
    align-items:center;
    gap:8px;
    font-size:.8rem;
    font-weight:900;
    color:#d9f7ff;
}

.dot {
    width:9px;
    height:9px;
    border-radius:50%;
    background:#25ff72;
    box-shadow:0 0 14px #25ff72;
}

.hero-title {
    font-size:3.2rem;
    line-height:.95;
    margin:20px 0 14px;
    font-weight:900;
}

.hero-title span {
    color:var(--neon-blue);
    text-shadow:0 0 18px var(--neon-blue);
}

.hero-text {
    color:#cbd4e8;
    line-height:1.45;
}

.stats {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:10px;
}

.stat {
    background:rgba(255,255,255,.055);
    border:1px solid rgba(255,255,255,.08);
    border-radius:12px;
    padding:13px;
}

.stat strong {
    display:block;
    font-size:1.25rem;
}

.stat span {
    color:#aab3c8;
    font-size:.75rem;
}

.player-card h2,
.quick-card h2 {
    margin:0 0 14px;
}

.track-marquee {
    width:100%;
    overflow:hidden;
    white-space:nowrap;
    min-height:2.2rem;
    display:flex;
    align-items:center;
    justify-content:center;
}

#track-name {
    display:block;
    width:100%;
    margin:12px 0;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    font-size:.95rem;
    line-height:1.25;
}

#track-name.animate-scroll {
    display:inline-block;
    width:auto;
    max-width:none;
    overflow:visible;
    text-overflow:clip;
    animation:scrollText 18s linear infinite;
}

@keyframes scrollText {
    0% { transform:translateX(0); }
    100% { transform:translateX(-50%); }
}

.next-track {
    font-size:.78rem;
    color:rgba(255,255,255,.72);
    margin:8px 0 0;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.progress {
    width:100%;
    height:7px;
    background:rgba(255,255,255,.25);
    border-radius:999px;
    overflow:hidden;
    margin:12px 0 15px;
}

.progress span {
    display:block;
    width:0%;
    height:100%;
    background:linear-gradient(90deg,var(--neon-blue),var(--neon-pink));
    border-radius:999px;
    transition:width .4s linear;
}

.player-actions {
    display:flex;
    gap:8px;
    justify-content:flex-start;
    flex-wrap:wrap;
    margin-top:10px;
}

.mini-btn {
    border:1px solid var(--glass-border);
    background:rgba(255,255,255,.08);
    color:#fff;
    border-radius:10px;
    padding:10px 13px;
    font-weight:800;
    cursor:pointer;
    text-decoration:none;
    font-size:.78rem;
    white-space:nowrap;
}

.mini-btn:hover {
    color:var(--neon-blue);
    border-color:var(--neon-blue);
}

.play-circle {
    background:rgba(255,255,255,.1);
    border:2px solid #fff;
    width:55px;
    height:55px;
    border-radius:50%;
    margin:18px 0;
    cursor:pointer;
    display:flex;
    justify-content:center;
    align-items:center;
}

.quick-link {
    display:block;
    width:100%;
    text-align:center;
    padding:12px 13px;
    margin-bottom:10px;
    border-radius:10px;
    text-decoration:none;
    color:#000;
    font-weight:900;
    background:linear-gradient(135deg,var(--neon-blue),#2f66ff);
}

.quick-link.pink {
    color:#fff;
    background:linear-gradient(135deg,var(--neon-pink),#7b2fff);
}

.quick-link.ghost {
    color:#fff;
    background:rgba(255,255,255,.07);
    border:1px solid rgba(255,255,255,.12);
}

.quick-link:hover {
    transform:translateY(-1px);
    box-shadow:0 0 16px rgba(24,255,255,.25);
}

.server-box {
    margin-top:14px;
    background:rgba(255,255,255,.05);
    border-radius:13px;
    padding:14px;
    color:#aab3c8;
}

.instruction-box,
.request-name-box,
.request-panel,
.cbox-box {
    height:360px;
    padding:20px;
}

.instruction-box {
    font-size:.85rem;
    line-height:1.5;
    border-color:var(--neon-pink);
    text-align:center;
}

.instruction-box h3,
.request-name-box h3,
.cbox-box h3 {
    margin:0 0 12px;
    color:var(--neon-pink);
    font-size:1rem;
    letter-spacing:1px;
    text-transform:uppercase;
    text-align:center;
}

.request-form-wrap {
    height:100%;
    overflow-y:auto;
    padding-right:4px;
}

.request-form input,
.request-form select {
    width:100%;
    margin-bottom:8px;
    padding:10px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,.15);
    background:rgba(0,0,0,.35);
    color:#fff;
    font-size:.78rem;
}

.request-form select option {
    background:#150020;
    color:#fff;
}

.request-form button {
    width:100%;
}

.request-list-box {
    margin-top:12px;
    background:rgba(255,255,255,.05);
    border-radius:13px;
    padding:10px;
    max-height:115px;
    overflow-y:auto;
}

.request-item {
    padding:8px;
    margin-bottom:7px;
    border-radius:10px;
    background:rgba(0,0,0,.25);
    border:1px solid rgba(255,255,255,.08);
    font-size:.76rem;
}

.request-item strong {
    color:var(--neon-blue);
}

.request-item span {
    display:block;
    color:#dcecff;
    margin-top:3px;
}

.request-help {
    font-size:.72rem;
    color:#aab3c8;
    line-height:1.35;
    margin:6px 0 10px;
    text-align:center;
}

.cbox-box {
    border-color:var(--neon-pink);
}

.cbox-frame-wrap {
    width:100%;
    height:calc(100% - 32px);
    border-radius:14px;
    overflow:hidden;
    background:rgba(0,0,0,.35);
}

.cbox-frame-wrap iframe {
    width:100%;
    height:100%;
    border:none;
    display:block;
}

.hud-data-layer {
    position:fixed;
    bottom:30px;
    left:0;
    width:100%;
    display:flex;
    justify-content:space-between;
    padding:0 40px;
    box-sizing:border-box;
    z-index:1200;
    pointer-events:none;
}

.hud-box {
    background:rgba(10,10,16,.85);
    padding:10px 15px;
    border-left:3px solid var(--neon-blue);
    backdrop-filter:blur(12px);
    color:var(--neon-blue);
    font-family:monospace;
    font-size:.8rem;
    animation:neonBreathe 5s ease-in-out infinite alternate;
    min-width:170px;
}

footer {
    width:100%;
    text-align:center;
    padding:0 0 60px;
    background:transparent;
    z-index:50;
    position:relative;
    font-size:.7rem;
    letter-spacing:1px;
    color:rgba(255,255,255,.6);
    text-shadow:0 2px 4px rgba(0,0,0,.5);
}

footer a {
    color:var(--neon-blue);
    text-decoration:none;
    transition:.3s;
}

footer a:hover {
    color:var(--neon-pink);
    text-shadow:0 0 10px var(--neon-pink);
}

@media(max-width:1400px) {
    .amazon-space-sign {
        right:10px;
        width:150px;
    }
}

@media(max-width:1100px) {
    #nav-toggle { display:block; }

    .main-container,
    .bottom-grid {
        grid-template-columns:1fr;
        max-width:560px;
    }

    .hero-card,
    .player-card,
    .quick-card,
    .instruction-box,
    .request-name-box,
    .request-panel,
    .cbox-box {
        height:auto;
        min-height:360px;
    }

    .floating-logo-link {
        left:58px;
    }

    .amazon-space-sign {
        position:relative;
        right:auto;
        top:auto;
        transform:none;
        width:100%;
        max-width:360px;
        margin:12px auto 0;
        animation:neonBreathe 4s ease-in-out infinite alternate, mobileSpaceFloat 4.5s ease-in-out infinite;
    }

    @keyframes mobileSpaceFloat {
        0%, 100% {
            transform:translateY(0);
        }
        50% {
            transform:translateY(-10px);
        }
    }

    .strut { display:none; }

    .hud-data-layer {
        position:relative;
        bottom:0;
        flex-direction:column;
        gap:10px;
        padding:20px;
        pointer-events:auto;
    }

    .hud-box {
        width:100%;
        text-align:center !important;
    }

    footer {
        padding:20px 0;
    }
}

@media(max-width:520px) {
    .main-container,
    .bottom-grid {
        padding-left:12px;
        padding-right:12px;
    }

    .hero-title {
        font-size:2.4rem;
    }

    .stats {
        grid-template-columns:1fr;
    }

    .mini-btn {
        width:100%;
        text-align:center;
    }

    .floating-logo-link {
        position:relative;
        top:auto;
        left:auto;
        display:block;
        width:max-content;
        margin:12px auto 0;
    }
}
</style>
</head>

<body oncontextmenu="return false;">

<div class="real-space-bg"></div>
<canvas id="vortexCanvas"></canvas>

<a href="home.php" class="floating-logo-link" aria-label="Trancescape Home">
    <img src="images/png image goes here" alt="Trancescape Radio Logo">
</a>

<a href="link goes here"
   target="_blank"
   rel="noopener noreferrer"
   class="amazon-space-sign"
   aria-label="Open Trancescape on Amazon">
    <img src="images/alexa-skill-trancescape.jpg" alt="Alexa Skill Trancescape Radio">
    <span>Open on Amazon</span>
</a>

<div id="nav-toggle" onclick="toggleNav()">NETWORKS</div>

<nav class="side-nav" id="sideNav">
    <h4 style="color:var(--neon-pink); font-size:0.7rem; letter-spacing:3px; text-align:center;">name</h4>
    <a href="link goes here" target="_blank" rel="noopener noreferrer" class="nav-link">name</a>
    <a href="link goes here" target="_blank" rel="noopener noreferrer" class="nav-link">name</a>
    <a href="link goes here" target="_blank" rel="noopener noreferrer" class="nav-link">name</a>
    <a href="link goes here" target="_blank" rel="noopener noreferrer" class="nav-link">name</a>
</nav>

<header>
    <img src="images/image goes here" alt="Logo" class="logo-corner">
</header>

<div id="cockpit-hud">
    <div class="strut top-left"></div>
    <div class="strut top-right"></div>
</div>

<div class="main-container">

    <section class="hero-card">
        <div>
            <div class="status-pill"><span class="dot"></span> Online</div>
            <h1 class="hero-title">unkown<br><span>Scape</span> Hub</h1>
            <p class="hero-text">
                unkown
            </p>
        </div>

        <div class="stats">
            <div class="stat">
                <strong id="listeners">0</strong>
                <span>blank</span>
            </div>
            <div class="stat">
                <strong>320</strong>
                <span>KBPS</span>
            </div>
            <div class="stat">
                <strong>LIVE</strong>
                <span>Status</span>
            </div>
        </div>
    </section>

    <section class="player-card">
        <h2>Listen Live</h2>

        <div style="color:var(--neon-pink); font-size:0.65rem; letter-spacing:3px; font-weight:bold;">VORTEX CORE</div>

        <div class="track-marquee">
            <h2 id="track-name">add something here</h2>
        </div>

        <div class="next-track">
            Up next: <span id="next-track">Scanning queue...</span>
        </div>

        <div class="play-circle" onclick="togglePlayback()">
            <svg id="ctrlIcon" viewBox="0 0 24 24" width="24" height="24" fill="white">
                <path d="M8 5v14l11-7z"/>
            </svg>
        </div>

        <div style="margin: 15px 0; display: flex; align-items: center; gap: 10px;">
            <input type="range" id="volSlider" min="0" max="100" value="70" style="flex-grow: 1; accent-color: var(--neon-blue);" oninput="setVolume(this.value)">
        </div>

        <div style="font-size:.8rem; color:rgba(255,255,255,.75);">
            Time remaining: <strong id="countdown">00:00</strong>
        </div>

        <div class="progress">
            <span id="progress-fill"></span>
        </div>

        <div class="player-actions">
            <button class="mini-btn" onclick="togglePlayback()">Play / Pause</button>
            <button class="mini-btn" onclick="toggleMute()" id="muteBtn">Mute</button>
            <a href="https://radio link here" target="_blank" rel="noopener noreferrer" class="mini-btn">Open Stream</a>
        </div>
    </section>

    <aside class="quick-card">
        <h2>Trancescape Portal</h2>

        <a href="https://radio link here" target="_blank" rel="noopener noreferrer" class="quick-link pink">name</a>
        <a href="https://link goes here" target="_blank" rel="noopener noreferrer" class="quick-link ghost">name</a>
        <a href="https://link goes here" target="_blank" rel="noopener noreferrer" class="quick-link ghost">name</a>

        <div class="server-box">
            <strong>Station Status</strong>
            <p><span class="dot" style="display:inline-block;margin-right:8px;"></span>signal online.</p>
        </div>
    </aside>

</div>

<div class="bottom-grid">

    <section class="instruction-box">
        <h3>Instructions For something</h3>
        <p>add something here</p>
        <p>add something here</p>
        <div style="margin-top:10px; color:var(--neon-blue); font-weight:bold; font-size:0.8rem;">Thank you for your understanding</div>
    </section>

    <section class="request-name-box">
        <div class="request-form-wrap">
            <h3>Named Request</h3>
            <p class="request-help">add something here</p>

            <form class="request-form" onsubmit="submitAzuraRequest(event)">
                <input type="text" id="requesterName" placeholder="Your name" required>
                <input type="text" id="songSearch" placeholder="Search song or artist..." oninput="filterSongs()">

                <select id="azuraSongSelect" required>
                    <option value="">Loading songs...</option>
                </select>

                <button type="submit" class="mini-btn">Request Song</button>
            </form>

            <div class="request-list-box" id="requestList">
                Loading requests...
            </div>
        </div>
    </section>

    <section class="cbox-box">
        <h3>Chat</h3>

        <div class="cbox-frame-wrap">
            <iframe src="https://chat box link goes here"
                width="100%"
                height="450"
                allowtransparency="yes"
                allow="autoplay"
                frameborder="0"
                marginheight="0"
                marginwidth="0"
                scrolling="auto">
            </iframe>
        </div>
    </section>

</div>

<footer>
    &copy; Designed by <a href="https://github.com/TTVytangelofhype" target="_blank" rel="noopener noreferrer">TTVytangelofhype</a>
</footer>

<div class="hud-data-layer">
    <div class="hud-box">
        CORE TEMP: <span class="dyn-num" data-min="40" data-max="45">42</span>&deg;C<br>
        STABILITY: <span class="dyn-num" data-min="98" data-max="100">99</span>.8%
    </div>

    <div class="hud-box" style="text-align: right; border-left: 0; border-right: 3px solid var(--neon-blue);">
        VELOCITY: <span class="dyn-num" data-min="1000" data-max="1200">1104</span> u/s<br>
        SIGNAL: 320KBPS
    </div>
</div>

<audio id="audioEngine" preload="none" src="https://radio link goes here"></audio>

<script>
const canvas = document.getElementById('vortexCanvas');
const ctx = canvas.getContext('2d');
const audio = document.getElementById('audioEngine');
const ctrlIcon = document.getElementById('ctrlIcon');
const sideNav = document.getElementById('sideNav');

let w, h, stars = [], playing = false;
let remainingTime = 0;
let countdownInterval = null;
let currentSongKey = "";
let azuraSongs = [];
let savedRequesterName = localStorage.getItem('trancescape_request_name') || "";

function toggleNav() {
    sideNav.classList.toggle('active');
}

document.querySelector('.main-container').addEventListener('click', () => {
    sideNav.classList.remove('active');
});

function initVortex() {
    w = canvas.width = window.innerWidth;
    h = canvas.height = window.innerHeight;
    stars = [];

    for (let i = 0; i < 400; i++) {
        stars.push({
            x: Math.random() * w - w / 2,
            y: Math.random() * h - h / 2,
            z: Math.random() * w,
            color: ["#fff", "#ff2ad4", "#18ffff"][Math.floor(Math.random() * 3)]
        });
    }
}

function drawVortex() {
    ctx.clearRect(0, 0, w, h);
    ctx.save();
    ctx.translate(w / 2, h / 2);

    stars.forEach(s => {
        s.z -= 4;
        if (s.z <= 0) s.z = w;

        let x = s.x * (w / s.z);
        let y = s.y * (h / s.z);
        let size = (1 - s.z / w) * 2.5;

        ctx.beginPath();
        ctx.fillStyle = s.color;
        ctx.globalAlpha = 1 - (s.z / w);
        ctx.arc(x, y, size, 0, Math.PI * 2);
        ctx.fill();
    });

    ctx.restore();
    requestAnimationFrame(drawVortex);
}

function updateHUDNumbers() {
    document.querySelectorAll('.dyn-num').forEach(el => {
        const min = parseInt(el.getAttribute('data-min'));
        const max = parseInt(el.getAttribute('data-max'));
        el.innerText = Math.floor(Math.random() * (max - min + 1)) + min;
    });
}

function formatTime(sec) {
    sec = Math.floor(sec || 0);
    if (isNaN(sec) || sec < 0) return "00:00";
    return `${String(Math.floor(sec / 60)).padStart(2, "0")}:${String(sec % 60).padStart(2, "0")}`;
}

async function updateNowPlaying() {
    try {
        const response = await fetch("https://radio link goes here" + Date.now());
        const data = await response.json();

        const trackEl = document.getElementById('track-name');
        const nextEl = document.getElementById('next-track');

        if (data.now_playing && data.now_playing.song) {
            const song = data.now_playing.song;
            const fullTitle = song.text || ((song.artist ? song.artist + " - " : "") + song.title);
            const songKey = song.id || fullTitle;

            if (songKey !== currentSongKey) {
                currentSongKey = songKey;

                trackEl.textContent = fullTitle;
                clearPlayedRequest(fullTitle);
                trackEl.classList.remove("animate-scroll");

                setTimeout(() => {
                    if (trackEl.scrollWidth > trackEl.parentElement.clientWidth + 20) {
                        trackEl.innerHTML = `${fullTitle} &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; ${fullTitle} &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;`;
                        trackEl.classList.add("animate-scroll");
                    }
                }, 100);
            }

            remainingTime = data.now_playing.remaining || 0;

            const elapsed = data.now_playing.elapsed || 0;
            const duration = data.now_playing.duration || 0;
            const percent = duration > 0 ? Math.min(100, Math.max(0, (elapsed / duration) * 100)) : 0;
            document.getElementById('progress-fill').style.width = percent + "%";
        }

        if (data.playing_next && data.playing_next.song) {
            nextEl.textContent = data.playing_next.song.text || "--";
        }

        if (data.listeners) {
            document.getElementById('listeners').innerText = data.listeners.total || 0;
        }

        if (countdownInterval) clearInterval(countdownInterval);

        document.getElementById('countdown').textContent = formatTime(remainingTime);

        countdownInterval = setInterval(() => {
            if (remainingTime > 0) {
                remainingTime--;
                document.getElementById('countdown').textContent = formatTime(remainingTime);
            } else {
                clearInterval(countdownInterval);
                updateNowPlaying();
            }
        }, 1000);

    } catch (e) {
        document.getElementById('track-name').innerText = "Trancescape Radio - Live Stream";
    }
}

function togglePlayback() {
    if (audio.paused) {
        audio.src = "https://radio link goes here" + Date.now();
        audio.load();

        audio.play().then(() => {
            playing = true;
            ctrlIcon.innerHTML = '<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>';
        }).catch(() => {
            alert("Radio could not start. Click Open Stream or press Play again.");
        });
    } else {
        audio.pause();
        playing = false;
        ctrlIcon.innerHTML = '<path d="M8 5v14l11-7z"/>';
    }
}

function toggleMute() {
    audio.muted = !audio.muted;
    document.getElementById('muteBtn').innerText = audio.muted ? "Unmute" : "Mute";
}

function setVolume(val) {
    audio.volume = val / 100;
}

async function loadAzuraSongs() {
    const select = document.getElementById('azuraSongSelect');

    try {
        const res = await fetch('https:/radio request link goes here' + Date.now());
        const songs = await res.json();

        azuraSongs = Array.isArray(songs) ? songs : [];
        renderSongOptions(azuraSongs);
    } catch (err) {
        select.innerHTML = '<option value="">Could not load songs</option>';
    }
}

function renderSongOptions(songs) {
    const select = document.getElementById('azuraSongSelect');

    if (!songs || !songs.length) {
        select.innerHTML = '<option value="">No songs found</option>';
        return;
    }

    select.innerHTML = '<option value="">Choose a song...</option>' + songs.map(item => {
        const songText = item.song?.text || item.song?.title || 'Unknown Song';
        const safeSong = songText.replace(/"/g, '&quot;');

        return `<option value="${item.request_id}" data-song="${safeSong}">${songText}</option>`;
    }).join('');
}

function filterSongs() {
    const search = document.getElementById('songSearch').value.toLowerCase();

    const filtered = azuraSongs.filter(item => {
        const songText = item.song?.text || item.song?.title || '';
        return songText.toLowerCase().includes(search);
    });

    renderSongOptions(filtered);
}

async function submitAzuraRequest(e) {
    e.preventDefault();

    const name = document.getElementById('requesterName').value.trim();
    const select = document.getElementById('azuraSongSelect');
    const requestId = select.value;
    const selectedSong = select.options[select.selectedIndex]?.dataset.song || '';

    if (!name || !requestId) {
        alert('Please enter your name and choose a song.');
        return;
    }

    localStorage.setItem('something_request_name', name);

    try {
        const azuraRes = await fetch(`https:/radio request link goes here${requestId}`, {
            method: 'POST'
        });

        if (!azuraRes.ok) {
            alert('AzuraCast refused this request. It may already be queued or recently played.');
            return;
        }

        const formData = new FormData();
        formData.append('name', name);
        formData.append('song', selectedSong);

        await fetch('save_request.php', {
            method: 'POST',
            body: formData
        });

        document.getElementById('songSearch').value = '';
        document.getElementById('azuraSongSelect').value = '';

        loadSongRequests();

        alert('Request sent to Trancescape.');

    } catch (err) {
        alert('Could not send request.');
    }
}

async function loadSongRequests() {
    try {
        const res = await fetch('get_requests.php?_=' + Date.now());
        const requests = await res.json();

        const box = document.getElementById('requestList');

        if (!Array.isArray(requests) || !requests.length) {
            box.innerHTML = '<div class="request-item">No named requests yet.</div>';
            return;
        }

        box.innerHTML = requests.map(r => `
            <div class="request-item">
                <strong>${r.name}</strong>
                <span>${r.song}</span>
                <small>${r.time}</small>
            </div>
        `).join('');
    } catch (err) {
        document.getElementById('requestList').innerHTML = '<div class="request-item">Could not load requests.</div>';
    }
}

window.addEventListener('resize', initVortex);

initVortex();
drawVortex();
updateNowPlaying();
loadAzuraSongs();
loadSongRequests();

document.getElementById('requesterName').value = savedRequesterName;

setInterval(updateNowPlaying, 15000);
setInterval(updateHUDNumbers, 2500);
setInterval(loadSongRequests, 15000);
let lastClearCheckSong = "";

async function clearPlayedRequest(nowPlayingSong) {
    if (!nowPlayingSong) return;

    if (nowPlayingSong === lastClearCheckSong) return;
    lastClearCheckSong = nowPlayingSong;

    try {
        const formData = new FormData();
        formData.append('song', nowPlayingSong);

        const res = await fetch('clear_played_request.php?_=' + Date.now(), {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        if (data.success && data.removed > 0) {
            loadSongRequests();
        }
    } catch (err) {
        console.log('Could not clear played request', err);
    }
}
</script>

</body>
</html>
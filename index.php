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
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Radio | </title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
    :root {
        --neon-pink: #ff2ad4;
        --neon-purple: #9437ff;
        --neon-blue: #18ffff;
    }

    body, html {
        margin: 0; padding: 0; width: 100%; height: 100%;
        overflow: hidden; background: #000;
        font-family: 'Segoe UI', sans-serif;
    }

    /* ===== COCKPIT & HUD SYSTEM ===== */
    #cockpit {
        position: fixed; inset: 0; z-index: 50; 
        pointer-events: none; opacity: 0; visibility: hidden;
        transition: opacity 1s ease;
    }

    .cockpit-frame {
        position: absolute; inset: 0;
        border: 40px solid #0a0a10;
        border-image: linear-gradient(to bottom, #1a1a25, #050508) 1;
        box-shadow: inset 0 0 150px rgba(0,0,0,1);
    }

    .strut {
        position: absolute; background: #0f0f15;
        border: 2px solid var(--neon-blue);
        box-shadow: 0 0 20px rgba(24, 255, 255, 0.3);
        width: 400px; height: 50px;
    }
    .top-left { top: -20px; left: -150px; transform: rotate(45deg); }
    .top-right { top: -20px; right: -150px; transform: rotate(-45deg); }
    .bottom-left { bottom: -20px; left: -150px; transform: rotate(-45deg); }
    .bottom-right { bottom: -20px; right: -150px; transform: rotate(45deg); }

    /* HUD Elements */
    .hud-display {
        position: absolute; bottom: 12%; left: 50%;
        transform: translateX(-50%); width: 85%;
        display: flex; justify-content: space-between;
        color: var(--neon-blue); font-family: monospace;
        font-size: 13px; text-shadow: 0 0 10px var(--neon-blue);
    }

    .hud-box {
        border: 1px solid var(--neon-blue); padding: 12px;
        background: rgba(10, 10, 20, 0.8); backdrop-filter: blur(8px);
        min-width: 180px;
    }

    .hud-scanline {
        position: absolute; inset: 0;
        background: linear-gradient(to bottom, transparent 50%, rgba(24, 255, 255, 0.05) 50%);
        background-size: 100% 4px; pointer-events: none;
    }

    /* ===== SPACE & VORTEX ENGINE ===== */
    #splash-container { position: fixed; inset: 0; z-index: 1; background: #000; }

    .real-space-bg {
        position: absolute; background: url('images/Trancescape_Radio_background.jpg') no-repeat center center;
        background-size: cover; inset: -10%; opacity: 0; 
        filter: brightness(1.1) contrast(1.1); transition: opacity 3s ease-in-out;
    }

    .animate-space { animation: spaceFloat 25s ease-in-out infinite; opacity: 1 !important; }

    @keyframes spaceFloat {
        0%, 100% { transform: scale(1.1) translate(0, 0); }
        50% { transform: scale(1.05) translate(-1%, 1%); }
    }

    canvas { position: absolute; inset: 0; z-index: 5; mix-blend-mode: screen; transition: opacity 2s; }

    /* ===== DOORS ===== */
    .door {
        position: fixed; top: 0; width: 50.5%; height: 100%;
        background: #050010; z-index: 120; 
        transition: transform 2.8s cubic-bezier(0.7, 0, 0.3, 1);
        display: flex; align-items: center; pointer-events: none;
    }
    .door.left { left: 0; border-right: 2px solid var(--neon-pink); box-shadow: 20px 0 80px rgba(255, 42, 212, 0.5); }
    .door.right { right: 0; border-left: 2px solid var(--neon-blue); box-shadow: -20px 0 80px rgba(24, 255, 255, 0.5); }
    .door-label { width: 100%; text-align: center; color: white; opacity: 0.1; font-size: 8vw; font-weight: 900; letter-spacing: 20px; }
    .open-left { transform: translateX(-100%); }
    .open-right { transform: translateX(100%); }

    /* ===== UI ===== */
    .content-overlay {
        position: absolute; inset: 0; display: flex; flex-direction: column;
        justify-content: center; align-items: center; z-index: 130; padding: 20px;
    }
    .logo { width: 250px; max-width: 80%; filter: drop-shadow(0 0 20px var(--neon-pink)); transition: all 3.5s ease-in; }
    .enter-btn {
        margin-top: 40px; background: transparent; border: 1px solid white; color: white;
        padding: 12px 40px; font-size: 1rem; letter-spacing: 6px; cursor: pointer;
        text-transform: uppercase; transition: 0.4s; pointer-events: auto;
    }
    .enter-btn:hover { background: white; color: black; box-shadow: 0 0 40px white; }
    .white-out { position: fixed; inset: 0; background: white; z-index: 200; opacity: 0; pointer-events: none; transition: opacity 1.5s; }

    /* ===== MOBILE RESPONSIVE FIXES ===== */
    @media (max-width: 768px) {
        .cockpit-frame { border-width: 15px; }
        .strut { width: 180px; height: 25px; }
        .top-left { left: -70px; } .top-right { right: -70px; }
        .bottom-left { left: -70px; } .bottom-right { right: -70px; }
        .door-label { font-size: 12vw; letter-spacing: 10px; }
        .hud-display { flex-direction: column; gap: 10px; bottom: 40px; width: 90%; align-items: center; }
        .hud-box { text-align: center !important; font-size: 11px; padding: 8px; width: 100%; min-width: unset; }
        .enter-btn { padding: 12px 20px; font-size: 0.8rem; letter-spacing: 3px; }
    }
</style>
</head>
<body>

<div class="white-out" id="flash"></div>

<div class="door left" id="leftDoor"><div class="door-label">name</div></div>
<div class="door right" id="rightDoor"><div class="door-label">sake</div></div>

<div id="cockpit">
    <div class="cockpit-frame"></div>
    <div class="strut top-left"></div>
    <div class="strut top-right"></div>
    <div class="strut bottom-left"></div>
    <div class="strut bottom-right"></div>
    <div class="hud-scanline"></div>
    
    <div class="hud-display">
        <div class="hud-box">
            SYSTEMS: <span id="vort-stat">OFFLINE</span><br>
            STABILITY: <span id="stab-val">100.0</span>%<br>
            COORDS: 51.5074&deg; N
        </div>
        <div class="hud-box" style="text-align: right;">
            VELOCITY: <span id="velo-stat">0</span> u/s<br>
            SYNC: <span id="sync-val">TRANCESCAPE</span><br>
            SECTOR: <span id="sector-val">--</span>
        </div>
    </div>
</div>

<div id="splash-container">
    <div class="real-space-bg" id="spaceImg"></div>
    <canvas id="warpCanvas"></canvas>
</div>

<div class="content-overlay" id="uiWrapper">
    <img src="images/image link goes here" class="logo" id="logo">
    <button class="enter-btn" id="enterBtn" onclick="initiateHyperdrive()">Engage Vortex</button>
</div>

<script>
const canvas = document.getElementById("warpCanvas");
const ctx = canvas.getContext("2d");

let w, h, stars = [];
let speed = 0.5; 
let isWarping = false;

function initCanvas() {
    w = canvas.width = window.innerWidth;
    h = canvas.height = window.innerHeight;
    stars = [];
    for (let i = 0; i < 400; i++) {
        stars.push({
            x: Math.random() * w - w / 2,
            y: Math.random() * h - h / 2,
            z: Math.random() * w,
            color: ["#ff2ad4", "#18ffff", "#ffffff"][Math.floor(Math.random() * 3)]
        });
    }
}

function draw() {
    ctx.clearRect(0, 0, w, h);
    for (let s of stars) {
        s.z -= speed;
        if (s.z <= 0) s.z = w;
        let k = 128 / s.z;
        let px = s.x * k + w / 2;
        let py = s.y * k + h / 2;
        if (px >= 0 && px <= w && py >= 0 && py <= h) {
            let size = (1 - s.z / w) * 2.5;
            ctx.globalAlpha = 1 - (s.z / w);
            ctx.fillStyle = s.color;
            ctx.beginPath();
            ctx.arc(px, py, size, 0, Math.PI * 2);
            ctx.fill();
            if(isWarping && speed > 20) {
                ctx.strokeStyle = s.color;
                ctx.lineWidth = size * 0.5;
                ctx.beginPath();
                ctx.moveTo(px, py);
                ctx.lineTo(px + (px - w/2) * (speed * 0.01), py + (py - h/2) * (speed * 0.01));
                ctx.stroke();
            }
        }
    }
    requestAnimationFrame(draw);
}

function activeHUD() {
    if(!isWarping) return;
    document.getElementById('stab-val').innerText = (98 + Math.random() * 2).toFixed(1);
    
    // Sector Randomizer (Always ends in G)
    if(Math.random() > 0.8) {
        const randNum = Math.floor(Math.random() * 9) + 1;
        document.getElementById('sector-val').innerText = randNum + 'G';
    }
}

function initiateHyperdrive() {
    const cockpit = document.getElementById('cockpit');
    const spaceImg = document.getElementById('spaceImg');
    const logo = document.getElementById('logo');
    const flash = document.getElementById('flash');
    const enterBtn = document.getElementById('enterBtn');
    const vortStat = document.getElementById('vort-stat');
    const veloStat = document.getElementById('velo-stat');

    document.getElementById('leftDoor').classList.add('open-left');
    document.getElementById('rightDoor').classList.add('open-right');
    
    cockpit.style.visibility = 'visible';
    cockpit.style.opacity = '1';
    vortStat.innerText = "ONLINE";
    vortStat.style.color = "var(--neon-blue)";
    
    enterBtn.style.opacity = '0';
    enterBtn.style.pointerEvents = 'none';
    isWarping = true;
    
    setInterval(activeHUD, 200);
    
    // Velocity building from 0
    let currentVelo = 0;
    let accel = setInterval(() => {
        speed += 1.5;
        currentVelo = Math.floor(speed * 125);
        veloStat.innerText = currentVelo.toLocaleString();
        if (speed > 95) clearInterval(accel);
    }, 50);

    setTimeout(() => {
        logo.style.transform = "scale(25) translateZ(800px)";
        logo.style.opacity = "0";
    }, 400);

    setTimeout(() => {
        spaceImg.classList.add('animate-space'); 
        canvas.style.opacity = "0"; 
        cockpit.style.opacity = "0"; 
    }, 6500);

    setTimeout(() => { flash.style.opacity = "1"; }, 11000);
    setTimeout(() => { window.location.href = "home.php"; }, 12500);
}

window.addEventListener("resize", initCanvas);
initCanvas();
draw();
</script>

</body>
</html>
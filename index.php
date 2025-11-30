<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link rel="shortcut icon" href="https://dihu.fr/appgithub/iconedihu/9.png" type="image/png">
<link rel="icon" href="https://dihu.fr/appgithub/iconedihu/9.png" type="image/png">
<title>Kanban Trello</title>

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<!-- LZ-String for compression -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lz-string/1.4.4/lz-string.min.js"></script>

<style>
  :root{
    --bg:#0b0b0f;
    --panel:#0f1220;
    --muted:#9aa1b2;
    --neon-cyan:#00fff7;
    --neon-pink:#ff4dff;
    --neon-amber:#ffd166;
    --glass: rgba(255,255,255,0.03);
  }
  /* Themes presets */
  body[data-theme="neon"]{ --bg:#0b0b0f; --panel:#0f1220; --muted:#9aa1b2; --neon-cyan:#00fff7; --neon-pink:#ff4dff; --neon-amber:#ffd166; }
  body[data-theme="minimal"]{ --bg:#0c0c0c; --panel:#121212; --muted:#a0a0a0; --neon-cyan:#9bdcff; --neon-pink:#f3f3f3; --neon-amber:#d6d6d6; }
  body[data-theme="material"]{ --bg:#0e1113; --panel:#141a1f; --muted:#9fb0c0; --neon-cyan:#4dd0e1; --neon-pink:#ff4081; --neon-amber:#ffd54f; }
  body[data-theme="pastel"]{ --bg:#0f1116; --panel:#131824; --muted:#a8b3c5; --neon-cyan:#b4f5f0; --neon-pink:#ffb3e6; --neon-amber:#ffe3a3; }
  body[data-theme="noir"]{ --bg:#000000; --panel:#0a0a0a; --muted:#8a8a8a; --neon-cyan:#9ae6ff; --neon-pink:#ffffff; --neon-amber:#d0d0d0; }
  html,body{height:100%;}
  body{
    background: radial-gradient(1200px 600px at 10% 10%, rgba(0,255,231,0.03), transparent),
                radial-gradient(900px 400px at 90% 80%, rgba(255,77,255,0.03), transparent),
                var(--bg);
    color: #e6eef8;
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  }
  /* neon accents */
  .accent-cyan{ color:var(--neon-cyan); }
  .accent-pink{ color:var(--neon-pink); }
  .accent-amber{ color:var(--neon-amber); }

  .glass { background: linear-gradient(135deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border:1px solid rgba(255,255,255,0.04); backdrop-filter: blur(6px); }
  .col-card { background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border:1px solid rgba(255,255,255,0.03); }
  .card { background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.00)); border:1px solid rgba(255,255,255,0.03); }
  .btn-ghost { background:transparent; border:1px solid rgba(255,255,255,0.04); }
  .small-muted { color:var(--muted); font-size:12px; }

  /* responsive tweaks */
  @media (max-width:900px){
    #columnsWrap { gap:12px; padding-bottom:80px; }
    aside#sidebar { display:none; }
    aside#rightbar { display:none; }
    header .desktop-toolbar{ display:none; }
    #mobileMenuBtn{ display:inline-flex !important; }
  }
  /* mobile menu base */
  #mobileMenuBtn{ display:none; }
  #mobileMenu .menu-group input,#mobileMenu .menu-group select,#mobileMenu .menu-group button{ width:100%; }
  /* drag placeholder */
  .drag-placeholder { outline: 2px dashed rgba(255,255,255,0.06); min-height:60px; border-radius:6px; }
  .priority-high { border-left:4px solid #ff6b6b; }
  .priority-normal { border-left:4px solid #ffd166; }
  .priority-low { border-left:4px solid #4ad29a; }
  /* context menu */
  .ctx-menu { z-index:90; min-width:180px; box-shadow:0 8px 30px rgba(0,0,0,0.6); }
  /* modal */
  .modal-backdrop { background: linear-gradient(180deg, rgba(10,10,12,0.8), rgba(10,10,12,0.95)); backdrop-filter: blur(8px); }
  /* smooth scroll and transitions */
  .smooth { transition: all .18s cubic-bezier(.2,.9,.3,1); }
  .no-select { user-select:none; -webkit-user-select:none; }
  /* progress bar */
  .progress{height:6px;border-radius:4px;background:rgba(255,255,255,0.08);overflow:hidden}
  .progress>span{display:block;height:100%;background:linear-gradient(90deg, var(--neon-cyan), var(--neon-pink));}
  /* locked */
  .locked{ outline:1px dashed rgba(255,255,255,0.2); cursor:not-allowed; }
  /* collapsed column */
  .col-collapsed .cards{ display:none; }
  /* orientation */
  /* orientation hint retained; grid handles both */
  body[data-orientation="vertical"] #columnsWrap{ grid-auto-flow: row; }
  body[data-orientation="horizontal"] #columnsWrap{ grid-auto-flow: column; }

  /* responsive grid tweaks */
  @media (max-width: 1200px){ #columnsWrap{ grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); } }
  @media (max-width: 900px){ #columnsWrap{ grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); } }
</style>
</head>
<body class="antialiased">

<!-- TOP BAR -->
<header class="flex items-center justify-between px-4 py-3 glass sticky top-0 z-50">
  <div class="flex items-center gap-3">
    <img src="https://dihu.fr/appgithub/iconedihu/9.png" alt="logo" class="w-11 h-11 rounded-lg shadow-lg" />
    <div>
      <div id="appTitle" class="text-lg font-semibold accent-cyan">Kanban Trello</div>
      <div id="appSubtitle" class="text-xs small-muted">Local • Offline • Encrypted export possible</div>
    </div>
  </div>
  <!-- Desktop toolbar -->
  <div class="desktop-toolbar flex items-center flex-wrap gap-2">
    <input id="search" class="px-3 py-2 rounded-md bg-transparent border border-white/5 text-sm outline-none w-48" placeholder="Search cards, tags, titles..." />
    <select id="filterTag" class="px-3 py-2 rounded-md bg-transparent border border-white/5 text-sm"><option value="">Filter tag</option></select>
    <select id="filterPriority" class="px-3 py-2 rounded-md bg-transparent border border-white/5 text-sm"><option value="">Priority</option><option value="high">High</option><option value="normal">Normal</option><option value="low">Low</option></select>
    <button id="undoBtn" title="Undo" class="px-3 py-2 rounded-md btn-ghost small-muted"><i class="fa-solid fa-rotate-left"></i></button>
    <button id="orientationBtn" title="Toggle orientation" class="px-3 py-2 rounded-md btn-ghost small-muted"><i class="fa-solid fa-arrows-up-down-left-right"></i></button>
    <button id="readOnlyBtn" title="Read-only" class="px-3 py-2 rounded-md btn-ghost small-muted"><i class="fa-solid fa-lock"></i></button>
    <button id="themeBtn" title="Themes" class="px-3 py-2 rounded-md btn-ghost small-muted"><i class="fa-solid fa-palette"></i></button>
    <button id="exportBtn" title="Export" class="px-3 py-2 rounded-md bg-cyan-600 hover:brightness-110 text-black"><i class="fa-solid fa-download"></i></button>
    <button id="importBtn" title="Import" class="px-3 py-2 rounded-md bg-amber-500 hover:brightness-110 text-black"><i class="fa-solid fa-upload"></i></button>
    <button id="newBoardBtn" title="New board" class="px-3 py-2 rounded-md bg-pink-600 hover:brightness-110"><i class="fa-solid fa-plus"></i></button>
    <div class="flex items-center gap-2 ml-2">
      <button id="langFrBtn" title="Français" class="w-9 h-9 rounded-full overflow-hidden border border-white/10 flex items-center justify-center bg-white/10">🇫🇷</button>
      <button id="langEnBtn" title="English" class="w-9 h-9 rounded-full overflow-hidden border border-white/10 flex items-center justify-center bg-white/10">🇬🇧</button>
    </div>
  </div>
  <!-- Mobile hamburger -->
  <button id="mobileMenuBtn" class="px-3 py-2 rounded-md btn-ghost small-muted"><i class="fa-solid fa-bars"></i></button>
</header>

<!-- LAYOUT -->
<div class="flex h-[calc(100vh-72px)]">
  <!-- LEFT SIDEBAR -->
  <aside id="sidebar" class="w-72 p-4 glass border-r border-white/4 overflow-auto">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold">Boards</h3>
      <button id="addBoardSmall" class="px-2 py-1 rounded-md bg-pink-600"><i class="fa-solid fa-plus"></i></button>
    </div>
    <div id="boardsList" class="space-y-2"></div>

    <hr class="my-3 border-white/4" />

    <div class="space-y-3 text-sm">
      <div><strong>Quick</strong></div>
      <button id="snapBtn" class="w-full text-left px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 small-muted"><i class="fa-solid fa-camera"></i> Snapshot</button>
      <button id="snapListBtn" class="w-full text-left px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 small-muted"><i class="fa-solid fa-folder-open"></i> Snapshots</button>
      <button id="snapCompareBtn" class="w-full text-left px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 small-muted"><i class="fa-solid fa-not-equal"></i> Compare snaps</button>
      <button id="archiveBtn" class="w-full text-left px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 small-muted"><i class="fa-solid fa-archive"></i> Archive</button>
      <button id="statsBtn" class="w-full text-left px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 small-muted"><i class="fa-solid fa-chart-line"></i> Stats</button>
      <button id="listBtn" class="w-full text-left px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 small-muted"><i class="fa-solid fa-list"></i> All tasks</button>
      <button id="calendarBtn" class="w-full text-left px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 small-muted"><i class="fa-regular fa-calendar"></i> Calendar</button>
      <button id="settingsBtn" class="w-full text-left px-3 py-2 rounded-md bg-slate-800 hover:bg-slate-700 small-muted"><i class="fa-solid fa-gear"></i> Settings</button>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="flex-1 overflow-auto p-4">
    <div class="flex items-center gap-3 mb-4">
      <h2 id="boardTitle" class="text-2xl font-bold accent-pink"></h2>
      <input id="renameBoardInput" class="hidden px-2 py-1 rounded-md bg-transparent border border-white/5" />
      <div id="boardMeta" class="small-muted"></div>
    </div>

    <!-- columns (grid responsive) -->
    <div id="columnsWrap" class="grid gap-4 items-start pb-8" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));"></div>

    <div class="mt-6">
      <button id="addColumnBtn" class="px-4 py-2 rounded-md bg-cyan-600 text-black"><i class="fa-solid fa-plus"></i> Add column</button>
      <button id="resetBtn" class="px-3 py-2 rounded-md btn-ghost ml-3"><i class="fa-solid fa-trash"></i> Reset</button>
      <span class="small-muted ml-4">Drag & drop works on desktop & mobile</span>
    </div>
  </main>

  <!-- RIGHT SIDEBAR -->
  <aside id="rightbar" class="w-96 p-4 glass border-l border-white/4 overflow-auto">
    <div id="rightContent"></div>
  </aside>
</div>

<!-- CARD MODAL -->
<div id="modal" class="fixed inset-0 hidden items-center justify-center z-50">
  <div class="modal-backdrop absolute inset-0"></div>
  <div class="relative w-[94%] max-w-3xl p-4 rounded-lg glass z-60">
    <div class="flex items-center justify-between mb-3">
      <h3 id="modalTitle" class="font-bold accent-cyan"></h3>
      <div class="flex items-center gap-2">
        <button id="modalSave" class="px-3 py-1 bg-emerald-400 rounded text-black">Save</button>
        <button id="modalClose" class="px-3 py-1 btn-ghost rounded">Close</button>
      </div>
    </div>

    <div class="space-y-3 text-sm">
      <input id="cardTitle" class="w-full px-3 py-2 rounded-md bg-transparent border border-white/5" placeholder="Title" />
      <textarea id="cardDesc" rows="4" class="w-full px-3 py-2 rounded-md bg-transparent border border-white/5" placeholder="Description"></textarea>

      <div class="grid grid-cols-2 gap-2">
        <input id="cardTags" placeholder="tags,comma,separated" class="px-3 py-2 rounded-md bg-transparent border border-white/5" />
        <select id="cardPriority" class="px-3 py-2 rounded-md bg-transparent border border-white/5">
          <option value="">Priority</option><option value="high">High</option><option value="normal">Normal</option><option value="low">Low</option>
        </select>
      </div>

      <div class="flex gap-2">
        <input id="cardDue" type="datetime-local" class="px-3 py-2 rounded-md bg-transparent border border-white/5" />
        <input id="cardImage" placeholder="Image URL or paste base64" class="px-3 py-2 rounded-md bg-transparent border border-white/5 flex-1" />
        <button id="addImageBtn" class="px-3 py-2 bg-pink-600 rounded">Add</button>
      </div>

      <div>
        <h4 class="font-semibold mb-2">Checklist</h4>
        <div id="checklist" class="space-y-2"></div>
        <div class="flex gap-2 mt-2">
          <input id="checkInput" placeholder="New item" class="px-3 py-2 rounded-md bg-transparent border border-white/5 flex-1" />
          <button id="addCheckBtn" class="px-3 py-2 bg-cyan-600 rounded text-black">Add</button>
        </div>
      </div>

      <div>
        <h4 class="font-semibold mb-2">Subtasks & Timer</h4>
        <div id="subtasks" class="space-y-2"></div>
        <div class="flex gap-2 mt-2">
          <input id="subInput" placeholder="New subtask" class="px-3 py-2 rounded-md bg-transparent border border-white/5 flex-1" />
          <button id="addSubBtn" class="px-3 py-2 bg-emerald-400 rounded text-black">Add</button>
        </div>
        <div class="mt-3 flex items-center gap-3">
          <button id="toggleTimer" class="px-3 py-2 rounded-md btn-ghost"><i class="fa-regular fa-clock"></i> Start/Stop</button>
          <div id="timerDisplay" class="font-mono">00:00:00</div>
        </div>
      </div>

      <div class="flex gap-2">
        <button id="deleteCardBtn" class="px-3 py-2 bg-red-600 rounded">Delete</button>
        <button id="archiveCardBtn" class="px-3 py-2 bg-amber-600 rounded">Archive</button>
        <button id="duplicateCardBtn" class="px-3 py-2 btn-ghost rounded">Duplicate</button>
        <button id="copyTitleBtn" class="px-3 py-2 btn-ghost rounded"><i class="fa-regular fa-copy"></i> Copy title</button>
        <button id="copyDescBtn" class="px-3 py-2 btn-ghost rounded"><i class="fa-regular fa-copy"></i> Copy desc</button>
      </div>

      <div class="mt-4">
        <h4 class="font-semibold mb-2">Versions</h4>
        <div id="versions" class="space-y-2 text-xs small-muted"></div>
      </div>
    </div>
  </div>
</div>

<!-- MOBILE MENU POPUP -->
<div id="mobileMenu" class="fixed inset-0 hidden z-50">
  <div class="modal-backdrop absolute inset-0"></div>
  <div class="absolute top-3 left-3 right-3 bottom-3 rounded-lg glass overflow-auto p-4">
    <div class="flex items-center justify-between mb-4">
      <div class="text-lg font-semibold accent-cyan">Menu</div>
      <button id="mobileMenuClose" class="px-3 py-2 btn-ghost rounded"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="space-y-4 menu-group text-sm" id="mobileMenuHome">
      <div class="flex gap-3 mb-2 justify-center">
        <button id="langFrBtn_m" title="Français" class="w-12 h-12 rounded-full overflow-hidden border border-white/10 flex items-center justify-center bg-white/10 text-xl">🇫🇷</button>
        <button id="langEnBtn_m" title="English" class="w-12 h-12 rounded-full overflow-hidden border border-white/10 flex items-center justify-center bg-white/10 text-xl">🇬🇧</button>
      </div>
      <div>
        <input id="search_m" class="px-3 py-2 rounded-md bg-transparent border border-white/5 text-sm w-full" placeholder="Search cards..." />
      </div>
      <div class="flex gap-2">
        <select id="filterTag_m" class="px-3 py-2 rounded-md bg-transparent border border-white/5 text-sm flex-1"><option value="">Tag</option></select>
        <select id="filterPriority_m" class="px-3 py-2 rounded-md bg-transparent border border-white/5 text-sm flex-1"><option value="">Priority</option><option value="high">High</option><option value="normal">Normal</option><option value="low">Low</option></select>
      </div>
      <div class="grid grid-cols-2 gap-2">
        <button id="undoBtn_m" class="px-3 py-2 rounded-md btn-ghost small-muted"><i class="fa-solid fa-rotate-left"></i> Undo</button>
        <button id="orientationBtn_m" class="px-3 py-2 rounded-md btn-ghost small-muted"><i class="fa-solid fa-arrows-up-down-left-right"></i> Orientation</button>
        <button id="readOnlyBtn_m" class="px-3 py-2 rounded-md btn-ghost small-muted"><i class="fa-solid fa-lock"></i> Read-only</button>
        <button id="themeBtn_m" class="px-3 py-2 rounded-md btn-ghost small-muted"><i class="fa-solid fa-palette"></i> Theme</button>
        <button id="exportBtn_m" class="px-3 py-2 rounded-md bg-cyan-600 text-black"><i class="fa-solid fa-download"></i> Export</button>
        <button id="importBtn_m" class="px-3 py-2 rounded-md bg-amber-500 text-black"><i class="fa-solid fa-upload"></i> Import</button>
        <button id="newBoardBtn_m" class="px-3 py-2 rounded-md bg-pink-600"><i class="fa-solid fa-plus"></i> New board</button>
        <button id="settingsBtn_m" class="px-3 py-2 rounded-md bg-slate-700"><i class="fa-solid fa-gear"></i> Settings</button>
      </div>
      <div class="grid grid-cols-2 gap-2">
        <button id="snapBtn_m" class="px-3 py-2 rounded-md bg-slate-800 small-muted"><i class="fa-solid fa-camera"></i> Snapshot</button>
        <button id="snapListBtn_m" class="px-3 py-2 rounded-md bg-slate-800 small-muted"><i class="fa-solid fa-folder-open"></i> Snaps</button>
        <button id="snapCompareBtn_m" class="px-3 py-2 rounded-md bg-slate-800 small-muted"><i class="fa-solid fa-not-equal"></i> Compare</button>
        <button id="archiveBtn_m" class="px-3 py-2 rounded-md bg-slate-800 small-muted"><i class="fa-solid fa-archive"></i> Archive</button>
        <button id="statsBtn_m" class="px-3 py-2 rounded-md bg-slate-800 small-muted"><i class="fa-solid fa-chart-line"></i> Stats</button>
        <button id="listBtn_m" class="px-3 py-2 rounded-md bg-slate-800 small-muted"><i class="fa-solid fa-list"></i> Tasks</button>
        <button id="calendarBtn_m" class="px-3 py-2 rounded-md bg-slate-800 small-muted"><i class="fa-regular fa-calendar"></i> Calendar</button>
        <button id="boardsBtn_m" class="px-3 py-2 rounded-md bg-slate-800 small-muted"><i class="fa-solid fa-layer-group"></i> Boards</button>
      </div>
    </div>
    <div id="mobileView" class="hidden text-sm"></div>
  </div>
</div>
<!-- CONTEXT MENU -->
<ul id="ctxMenu" class="ctx-menu fixed hidden rounded-md bg-[#0b0b0f] p-1 border border-white/6">
  <li data-act="edit" class="px-3 py-2 hover:bg-white/2 cursor-pointer">Edit</li>
  <li data-act="dup" class="px-3 py-2 hover:bg-white/2 cursor-pointer">Duplicate</li>
  <li data-act="archive" class="px-3 py-2 hover:bg-white/2 cursor-pointer">Archive</li>
  <li data-act="del" class="px-3 py-2 hover:bg-white/2 cursor-pointer">Delete</li>
  <li data-act="lock" class="px-3 py-2 hover:bg-white/2 cursor-pointer">Lock</li>
  <li data-act="unlock" class="px-3 py-2 hover:bg-white/2 cursor-pointer">Unlock</li>
  <li data-act="copy" class="px-3 py-2 hover:bg-white/2 cursor-pointer">Copy JSON</li>
  <li data-act="paste" class="px-3 py-2 hover:bg-white/2 cursor-pointer">Paste JSON here</li>
</ul>

<script>
/* -------------------------
  Mini Trello Neon — FULL
  - Offline, single file
  - Compressed storage with LZ-String
  - Multi-board, columns, cards, DnD, export/import, snapshots, archive
  - Responsive & optimized
--------------------------*/

/* -------------------------
  Utilities
--------------------------*/
const UID = n => Math.random().toString(36).slice(2, 2 + (n||8));
const now = ()=> new Date().toISOString();
const storageKey = "mt_neon_v2";
const snapsKey = "mt_neon_snapshots_v2";
const archiveKey = "mt_neon_archive_v2";

const compress = (o)=> LZString.compressToUTF16(JSON.stringify(o));
const decompress = (s)=> s ? JSON.parse(LZString.decompressFromUTF16(s)) : null;
const saveRaw = (o) => localStorage.setItem(storageKey, compress(o));
const loadRaw = ()=> { try { const s = localStorage.getItem(storageKey); return s?decompress(s):null;} catch(e){console.error(e); return null;} }

/* Minimal throttle */
function throttle(fn, wait){ let last=0, t=null; return (...a)=>{ const nowt=Date.now(); if(nowt-last>wait){ last=nowt; fn(...a);} else { clearTimeout(t); t=setTimeout(()=>{ last=Date.now(); fn(...a); }, wait - (nowt-last)); } } }

/* -------------------------
  Default data model
--------------------------*/
const DEFAULT = {
  meta: { created: now(), theme: 'neon', autosave: true, autosaveIntervalSec: 5, readOnly: false, orientation: 'horizontal', lang: 'en', virtualization: true },
  boards: [
    { id: 'b-'+UID(6), title:'My Board', columns: [
      { id:'c-'+UID(5), title:'To Do', cards:[], nonDraggable:false, limit:0 },
      { id:'c-'+UID(5), title:'Doing', cards:[], nonDraggable:false, limit:0 },
      { id:'c-'+UID(5), title:'Done', cards:[], nonDraggable:true, limit:0 }
    ]}
  ],
  activeBoard: null,
  undo: []
};

let state = loadRaw() || (()=>{ const s=JSON.parse(JSON.stringify(DEFAULT)); s.activeBoard = s.boards[0].id; return s; })();
/* -------------------------
  i18n dictionaries
--------------------------*/
const translations = {
  en: {
    appTitle: 'Kanban Trello',
    appSubtitle: 'Local • Offline • Optional encrypted export',
    searchPlaceholder: 'Search cards, tags, titles...',
    filterTag: 'Filter tag',
    tag: 'Tag',
    priority: 'Priority', priorityHigh:'High', priorityNormal:'Normal', priorityLow:'Low',
    undo:'Undo', orientation:'Orientation', readOnly:'Read-only', theme:'Theme', export:'Export', import:'Import', newBoard:'New board',
    snapshot:'Snapshot', snapshots:'Snapshots', compare:'Compare snaps', archive:'Archive', stats:'Stats', allTasks:'All tasks', calendar:'Calendar', settings:'Settings', boards:'Boards',
    addColumn:'Add column', reset:'Reset', dragHint:'Drag & drop works on desktop & mobile',
    edit:'Edit', del:'Del', collapse:'Collapse', add:'Add', duplicate:'Duplicate', locked:'lock', deleteColumnConfirm:'Delete column?', deleteCardConfirm:'Delete card?', completeResetConfirm:'Complete reset? This will erase local data.', columnLimitReached:'Column limit reached', destinationLocked:'Destination column is locked', snapshotNamePrefix:'snap ', snapshotSaved:'Snapshot saved', restored:'Restored', restoreSnapshotConfirm:'Restore snapshot? current state will be replaced', needTwoSnapshots:'Need at least 2 snapshots', restore:'Restore', download:'Download', compareTitle:'Compare snapshots', archiveTitle:'Archive', statsTitle:'Stats', allTasksTitle:'All tasks', calendarTitle:'Calendar', settingsTitle:'Settings', boardsTitle:'Boards', themeCycle:'Cycle', readOnlyOn:'ON', readOnlyOff:'OFF', orientationVertical:'Vertical', orientationHorizontal:'Horizontal', autosaveInterval:'Autosave interval (sec):', apply:'Apply', cardsA:'Cards A', cardsB:'Cards B', noVersions:'No versions yet', versions:'Versions', copyTitle:'Copy title', copyDesc:'Copy desc', startStop:'Start/Stop', checklist:'Checklist', subtasksTimer:'Subtasks & Timer'
  },
  fr: {
    appTitle: 'Kanban Trello',
    appSubtitle: 'Local • Hors ligne • Export chiffré optionnel',
    searchPlaceholder: 'Rechercher cartes, tags, titres…',
    filterTag: 'Tag filtre',
    tag: 'Tag',
    priority: 'Priorité', priorityHigh:'Haute', priorityNormal:'Normale', priorityLow:'Basse',
    undo:'Annuler', orientation:'Orientation', readOnly:'Lecture seule', theme:'Thème', export:'Exporter', import:'Importer', newBoard:'Nouveau board',
    snapshot:'Instantané', snapshots:'Instantanés', compare:'Comparer snaps', archive:'Archive', stats:'Stats', allTasks:'Toutes les tâches', calendar:'Calendrier', settings:'Paramètres', boards:'Boards',
    addColumn:'Ajouter colonne', reset:'Réinitialiser', dragHint:'Drag & drop bureau & mobile',
    edit:'Éditer', del:'Suppr', collapse:'Plier', add:'Ajouter', duplicate:'Dupliquer', locked:'verrou', deleteColumnConfirm:'Supprimer colonne ?', deleteCardConfirm:'Supprimer carte ?', completeResetConfirm:'Réinitialiser ? Efface les données locales.', columnLimitReached:'Limite de colonne atteinte', destinationLocked:'Colonne destination verrouillée', snapshotNamePrefix:'snap ', snapshotSaved:'Instantané enregistré', restored:'Restauré', restoreSnapshotConfirm:'Restaurer snapshot ? état remplacé', needTwoSnapshots:'Besoin de 2 instantanés', restore:'Restaurer', download:'Télécharger', compareTitle:'Comparer instantanés', archiveTitle:'Archive', statsTitle:'Stats', allTasksTitle:'Toutes les tâches', calendarTitle:'Calendrier', settingsTitle:'Paramètres', boardsTitle:'Boards', themeCycle:'Cycle', readOnlyOn:'ON', readOnlyOff:'OFF', orientationVertical:'Vertical', orientationHorizontal:'Horizontal', autosaveInterval:'Intervalle autosave (sec):', apply:'Appliquer', cardsA:'Cartes A', cardsB:'Cartes B', noVersions:'Aucune version', versions:'Versions', copyTitle:'Copier titre', copyDesc:'Copier desc', startStop:'Démarrer/Stop', checklist:'Checklist', subtasksTimer:'Sous-tâches & Minuteur'
  }
};
function t(key){ const lang = state.meta?.lang || 'en'; return (translations[lang] && translations[lang][key]) || translations.en[key] || key; }
function setLang(lang){ state.meta.lang = (lang==='fr')?'fr':'en'; schedulePersist(); applyTranslations(); renderAll(); }
function applyTranslations(){
  // header
  const appTitleEl = document.getElementById('appTitle'); if(appTitleEl) appTitleEl.textContent = t('appTitle');
  const appSubtitleEl = document.getElementById('appSubtitle'); if(appSubtitleEl) appSubtitleEl.textContent = t('appSubtitle');
  const searchEl = document.getElementById('search'); if(searchEl) searchEl.placeholder = t('searchPlaceholder');
  const searchM = document.getElementById('search_m'); if(searchM) searchM.placeholder = t('searchPlaceholder');
  // toolbar buttons titles
  const mapTitles = {
    undoBtn:'undo', orientationBtn:'orientation', readOnlyBtn:'readOnly', themeBtn:'theme', exportBtn:'export', importBtn:'import', newBoardBtn:'newBoard', addColumnBtn:'addColumn', resetBtn:'reset'
  };
  Object.entries(mapTitles).forEach(([id,k])=>{ const el=$(id); if(el) el.title = t(k); });
  // sidebar buttons
  const sideIds = { snapBtn:'snapshot', snapListBtn:'snapshots', snapCompareBtn:'compare', archiveBtn:'archive', statsBtn:'stats', listBtn:'allTasks', calendarBtn:'calendar', settingsBtn:'settings' };
  Object.entries(sideIds).forEach(([id,k])=>{ const el=$(id); if(el){ const icon = el.innerHTML.match(/<i[^>]*><\/i>/)?el.innerHTML:''; const firstIcon = el.querySelector('i'); if(firstIcon){ const icHtml = firstIcon.outerHTML; el.innerHTML = icHtml + ' ' + t(k); } else { el.textContent = t(k); } }});
  // mobile menu buttons short text
  const mobIds = { undoBtn_m:'undo', orientationBtn_m:'orientation', readOnlyBtn_m:'readOnly', themeBtn_m:'theme', exportBtn_m:'export', importBtn_m:'import', newBoardBtn_m:'newBoard', settingsBtn_m:'settings', snapBtn_m:'snapshot', snapListBtn_m:'snapshots', snapCompareBtn_m:'compare', archiveBtn_m:'archive', statsBtn_m:'stats', listBtn_m:'allTasks', calendarBtn_m:'calendar', boardsBtn_m:'boards' };
  Object.entries(mobIds).forEach(([id,k])=>{ const el=$(id); if(el){ const ic = el.querySelector('i'); if(ic){ el.innerHTML = ic.outerHTML + ' ' + t(k); } else el.textContent = t(k); }});
}
let autosaveInterval = null;
let currentEdit = null;
let timers = {}; // cardId -> interval
let deadlineInterval = null;

/* -------------------------
  Persistence & snapshots
--------------------------*/
// Compression déportée via Web Worker + requestIdleCallback
let persistWorker = null;
let __persistBusy = false;
let __persistQueued = false;
let __lastCompressed = null; // debug / dernière taille
function initPersistWorker(){
  try{
    persistWorker = new Worker('persist-worker.js');
    persistWorker.onmessage = (e)=>{
      const msg = e.data;
      if(msg.type==='compressed'){
        __persistBusy = false;
        __lastCompressed = msg.payload?.length || 0;
        try{ localStorage.setItem(storageKey, msg.payload); }catch(err){ console.error('ls write',err); }
        if(__persistQueued){ __persistQueued=false; schedulePersist(); }
      }
    };
  }catch(e){ console.warn('Worker init failed, fallback sync', e); persistWorker=null; }
}
if(typeof Worker!=='undefined'){ initPersistWorker(); }
function persistSync(){
  try{
    if(!state.undo) state.undo = [];
    localStorage.setItem(storageKey, compress(state));
  }catch(e){ console.error('persistSync',e); }
}
function schedulePersist(force=false){
  if(force){
    if(persistWorker){
      __persistBusy=true;
      persistWorker.postMessage({ type:'compress', state });
    } else {
      persistSync();
    }
    return;
  }
  // file d'attente
  if(__persistBusy){ __persistQueued=true; return; }
  __persistBusy = true;
  const run = ()=>{
    if(persistWorker){
      persistWorker.postMessage({ type:'compress', state });
    } else {
      persistSync();
      __persistBusy=false;
      if(__persistQueued){ __persistQueued=false; schedulePersist(); }
    }
  };
  if(typeof requestIdleCallback==='function'){
    requestIdleCallback(run, { timeout:300 });
  } else {
    setTimeout(run, 80);
  }
}

function pushUndo(){
  try{
    state.undo = state.undo || [];
    state.undo.push(compress(state));
    if(state.undo.length>40) state.undo.shift();
  }catch(e){}
}

function snapshot(name){
  const snaps = JSON.parse(localStorage.getItem(snapsKey) || "[]");
  snaps.push({ id:UID(6), name: name || ('snap '+new Date().toLocaleString()), time: now(), data: compress(state) });
  localStorage.setItem(snapsKey, JSON.stringify(snaps));
}

function loadSnapshots(){ return JSON.parse(localStorage.getItem(snapsKey) || "[]"); }

function saveArchiveCard(card){
  const a = JSON.parse(localStorage.getItem(archiveKey) || "[]");
  a.push(Object.assign({ archivedAt: now() }, card));
  localStorage.setItem(archiveKey, JSON.stringify(a));
}
function loadArchive(){ return JSON.parse(localStorage.getItem(archiveKey) || "[]"); }

/* -------------------------
  Basic helpers
--------------------------*/
function findBoard(id){ return state.boards.find(b=>b.id===id); }
function findCol(b, cid){ return b.columns.find(c=>c.id===cid); }
function findCard(col, cardId){ return col.cards.find(c=>c.id===cardId); }
function formatTime(s){ s = s||0; const h=Math.floor(s/3600), m=Math.floor((s%3600)/60), r=s%60; return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(r).padStart(2,'0')}`; }
function isOverdue(d){ if(!d) return false; return new Date(d) < new Date(); }

function updateFilters(){ renderActiveBoard(); populateFilterTags(); }

/* visuals/helpers */
function hashToHsl(str){ let h=0; for(let i=0;i<str.length;i++) h=(h*31 + str.charCodeAt(i))>>>0; return `hsl(${h%360} 70% 40% / .35)`; }
function tagBadgeHtml(t){ const bg = hashToHsl(t); return `<span class="text-xs px-2 py-1 rounded mr-1" style="background:${bg}">${escape(t)}</span>`; }
function isReadOnly(){ return !!state.meta?.readOnly; }
function setReadOnly(v){ state.meta.readOnly = !!v; schedulePersist(); renderAll(); }
function applyTheme(name){ document.body.dataset.theme = name; state.meta.theme = name; schedulePersist(); }
function updateOrientation(){ const o = state.meta.orientation==='vertical'?'vertical':'horizontal'; document.body.dataset.orientation = o; }
function cycleTheme(){ const list=['neon','minimal','material','pastel','noir']; const i=list.indexOf(state.meta.theme||'neon'); const nxt=list[(i+1)%list.length]; applyTheme(nxt); }
function undo(){ if(!state.undo || !state.undo.length){ alert('Nothing to undo'); return; } try{ const snap = state.undo.pop(); state = decompress(snap); schedulePersist(true); renderAll(); }catch(e){ console.error(e);} }

/* -------------------------
  Render functions (optimized)
--------------------------*/
const $ = id => document.getElementById(id);

function renderBoards(){
  const el = $('boardsList'); el.innerHTML = '';
  state.boards.forEach(b=>{
    const btn = document.createElement('button');
    btn.className = `w-full text-left px-3 py-2 rounded-md smooth ${b.id===state.activeBoard?'bg-white/5':'hover:bg-white/3'}`;
    btn.innerHTML = `<div class="flex justify-between items-center"><div><strong>${escape(b.title)}</strong><div class="text-xs small-muted">${b.columns.length} cols</div></div><div class="flex gap-2"><i class="fa-solid fa-ellipsis-vertical" title="${t('boards')}"></i></div></div>`;
    btn.addEventListener('click', ()=>{ state.activeBoard = b.id; schedulePersist(); renderAll(); });
    el.appendChild(btn);
  });
}

function renderAll(){
  renderBoards();
  renderActiveBoard();
  renderRightbarEmpty();
  populateFilterTags();
}

// Carte réutilisable (factorisée) pour limiter duplication HTML
function buildCard(card, col){
  const c = document.createElement('div');
  c.className = `card p-3 rounded-md shadow-sm smooth ${card.priority?('priority-'+card.priority):''} ${card.locked?'locked':''}`;
  c.dataset.cardId = card.id;
  c.dataset.colId = col.id;
  let tagsHtml = (card.tags||[]).slice(0,4).map(t=> tagBadgeHtml(t)).join('');
  let progHtml = '';
  if(card.checklist && card.checklist.length){ const done = card.checklist.filter(x=>x.done).length; const pct = Math.round((done/card.checklist.length)*100); progHtml = `<div class="progress mt-2"><span style="width:${pct}%"></span></div>`; }
  if(card.images && card.images.length) tagsHtml += `<div class="mt-2"><img src="${escape(card.images[0])}" alt="" loading="lazy" class="w-full max-h-28 object-cover rounded"/></div>`;
  c.innerHTML = `<div class="flex justify-between items-start gap-2">
      <div>
        <div class="font-semibold">${card.locked?'<i class="fa-solid fa-lock text-xs mr-1"></i>':''}${escape(card.title)}</div>
        <div class="text-xs small-muted line-clamp-3">${escape(card.description||'')}</div>
        <div class="mt-2">${tagsHtml}</div>
        ${progHtml}
      </div>
      <div class="text-right">
        ${card.priority?`<div class="px-2 py-1 rounded text-xs ${card.priority==='high'?'bg-red-700':'bg-yellow-600'}">${escape(card.priority)}</div>`:''}
        ${card.due?`<div class="text-xs mt-2 ${isOverdue(card.due)?'text-red-400':'small-muted'}">${(new Date(card.due)).toLocaleString()}</div>`:''}
        <div class="text-xs mt-2 small-muted">${formatTime(card.time||0)}</div>
      </div>
    </div>`;
  // Événements délégués (dblclick, contextmenu) gérés globalement
  if(!card.locked && !isReadOnly() && !col.nonDraggable){
    c.setAttribute('draggable', true);
    c.addEventListener('dragstart', (ev)=> { ev.dataTransfer.setData('text/plain', JSON.stringify({ cardId: card.id, from: col.id })); setTimeout(()=>c.classList.add('opacity-60'),50); });
    c.addEventListener('dragend', ()=> c.classList.remove('opacity-60') );
  }
  return c;
}

function renderActiveBoard(){
  const board = findBoard(state.activeBoard) || state.boards[0];
  if(!board) return;
  $('boardTitle').textContent = board.title;
  $('boardMeta').textContent = `${board.columns.length} columns`;
  const wrap = $('columnsWrap');
  // reuse DOM where possible: simple full re-render but optimized card rendering
  // Cancel previous in-flight batch renders using a token
  if(!window.__renderTokenSeq) window.__renderTokenSeq = 0;
  const myToken = ++window.__renderTokenSeq;
  wrap.innerHTML = '';

  const q = ($('search').value||'').toLowerCase();
  const fPriority = $('filterPriority').value;
  const fTag = $('filterTag').value;

  // Chunked rendering to avoid freeze with many columns/cards
  const cols = board.columns.slice();
  const perFrame = 4; // render 4 columns per frame
  const totalCards = cols.reduce((acc,c)=> acc + c.cards.length, 0);
  function renderOneColumn(col){
    const colEl = document.createElement('div');
    colEl.className = 'col-card rounded-md p-3';
    colEl.dataset.colId = col.id;

    const hdr = document.createElement('div'); hdr.className = 'flex items-center justify-between mb-3';
    hdr.innerHTML = `<div><strong>${escape(col.title)}</strong> <div class="text-xs small-muted inline-block ml-2">(${col.cards.length}${col.limit&&col.limit>0?` / ${col.limit}`:''})${col.nonDraggable?` • ${t('locked')}`:''}</div></div>
      <div class="flex gap-1"><button class="text-xs btn-ghost px-2 py-1">${t('edit')}</button><button class="text-xs btn-ghost px-2 py-1">${t('del')}</button><button class="text-xs btn-ghost px-2 py-1">${t('collapse')}</button></div>`;
    colEl.appendChild(hdr);

    const cardsWrap = document.createElement('div'); cardsWrap.className = 'space-y-3 min-h-[30px] cards';
    cardsWrap.dataset.colId = col.id;
    if(col.collapsed) colEl.classList.add('col-collapsed');

    // Filtered cards
    const filtered = (col.cards||[]).filter(card=>{
      const hay = (card.title + ' ' + (card.description||'') + ' ' + (card.tags||[]).join(' ')).toLowerCase();
      if(q && !hay.includes(q)) return false;
      if(fPriority && card.priority !== fPriority) return false;
      if(fTag && !(card.tags||[]).includes(fTag)) return false;
      return true;
    });

    const virtThreshold = totalCards > 500 ? 60 : 180;
    const VIRT = state.meta.virtualization && filtered.length > virtThreshold;
    let cardHeightEstimate = 110;

    if(!VIRT){
      if(filtered.length > 40){
        let ci=0;
        function batch(){
          const end = Math.min(ci+25, filtered.length);
          const frag = document.createDocumentFragment();
          for(let k=ci;k<end;k++) frag.appendChild(buildCard(filtered[k], col));
          cardsWrap.appendChild(frag);
          ci=end;
          if(ci<filtered.length) requestAnimationFrame(batch);
        }
        batch();
      } else {
        const frag = document.createDocumentFragment();
        filtered.forEach(card=> frag.appendChild(buildCard(card, col)));
        cardsWrap.appendChild(frag);
      }
    } else {
      cardsWrap.style.position = 'relative';
      const totalHeight = filtered.length * cardHeightEstimate;
      cardsWrap.style.height = totalHeight + 'px';
      const visibleLayer = document.createElement('div');
      visibleLayer.style.position='absolute'; visibleLayer.style.left='0'; visibleLayer.style.top='0'; visibleLayer.style.right='0';
      cardsWrap.appendChild(visibleLayer);
      function renderVirtual(){
        const scrollTop = cardsWrap.scrollTop;
        const viewHeight = cardsWrap.clientHeight;
        const startIndex = Math.max(0, Math.floor(scrollTop / cardHeightEstimate)-5);
        const endIndex = Math.min(filtered.length, startIndex + Math.ceil(viewHeight / cardHeightEstimate) + 10);
        visibleLayer.innerHTML='';
        visibleLayer.style.transform = `translateY(${startIndex*cardHeightEstimate}px)`;
        const frag = document.createDocumentFragment();
        for(let i=startIndex;i<endIndex;i++){ frag.appendChild(buildCard(filtered[i], col)); }
        visibleLayer.appendChild(frag);
      }
      cardsWrap.addEventListener('scroll', renderVirtual);
      setTimeout(()=>{
        if(filtered[0]){
          const temp = buildCard(filtered[0], col); temp.style.visibility='hidden'; temp.style.position='absolute'; temp.style.top='0'; cardsWrap.appendChild(temp); requestAnimationFrame(()=>{ cardHeightEstimate = temp.getBoundingClientRect().height || cardHeightEstimate; temp.remove(); cardsWrap.style.height = (filtered.length*cardHeightEstimate)+'px'; renderVirtual(); });
        }
      },0);
      renderVirtual();
    }

    if(!col.nonDraggable && !isReadOnly()){
      cardsWrap.addEventListener('dragover', (ev)=> ev.preventDefault());
      cardsWrap.addEventListener('drop', (ev)=> {
        ev.preventDefault();
        try {
          const d = JSON.parse(ev.dataTransfer.getData('text/plain'));
          if(d && d.cardId) moveCard(d.cardId, d.from, col.id);
        } catch(e){}
      });
    }

    const addRow = document.createElement('div'); addRow.className = 'mt-3';
    addRow.innerHTML = `<div class="flex gap-2"><input placeholder="${t('add')} card..." class="px-2 py-1 rounded-md bg-transparent border border-white/5 flex-1 newCardInput"/><button class="px-3 py-1 rounded-md bg-cyan-500 text-black addCardBtn">${t('add')}</button></div>`;
    addRow.querySelector('.addCardBtn').addEventListener('click', ()=>{
      const val = addRow.querySelector('.newCardInput').value.trim();
      if(!val) return;
      createCard(col.id, { title: val });
      addRow.querySelector('.newCardInput').value = '';
      // Rerender only this column to reduce work
      renderActiveBoard();
    });

    colEl.appendChild(cardsWrap);
    colEl.appendChild(addRow);

    hdr.querySelectorAll('button')[0].addEventListener('click', ()=>{
      if(isReadOnly()) return;
      const t = prompt("Column title", col.title);
      if(t!==null){ col.title = t; }
      const lim = prompt("Card limit (0 = unlimited)", String(col.limit||0));
      if(lim!==null){ const n = parseInt(lim||'0',10); if(!Number.isNaN(n)) col.limit = Math.max(0,n); }
      if(confirm('Toggle non-draggable for this column? OK=yes / Cancel=no')) col.nonDraggable = !col.nonDraggable;
      pushUndo(); schedulePersist(); renderActiveBoard();
    });
    hdr.querySelectorAll('button')[1].addEventListener('click', ()=>{
      if(!confirm('Delete column?')) return;
      const b = findBoard(state.activeBoard); b.columns = b.columns.filter(c=>c.id!==col.id); pushUndo(); schedulePersist(); renderActiveBoard();
    });
    hdr.querySelectorAll('button')[2].addEventListener('click', ()=>{
      col.collapsed = !col.collapsed; pushUndo(); schedulePersist(); renderActiveBoard();
    });

    // Column reorder disabled in grid? Keep drag if desired.
    if(!col.nonDraggable && !isReadOnly()){
      colEl.setAttribute('draggable', true);
      colEl.addEventListener('dragstart', (ev)=> { ev.dataTransfer.setData('text/col', col.id); colEl.classList.add('opacity-60'); });
      colEl.addEventListener('dragend', ()=> colEl.classList.remove('opacity-60'));
      colEl.addEventListener('dragover', (ev)=> ev.preventDefault());
      colEl.addEventListener('drop', (ev)=> {
        ev.preventDefault();
        const colId = ev.dataTransfer.getData('text/col');
        if(colId) reorderColumns(colId, col.id);
        else {
          try {
            const d = JSON.parse(ev.dataTransfer.getData('text/plain'));
            if(d && d.cardId) moveCard(d.cardId, d.from, col.id);
          } catch(e){}
        }
      });
    }

    // Column lazy population: if virtualization and many columns, only attach cards when intersecting
    const COL_VIRT = state.meta.virtualization && cols.length > 25;
    if(COL_VIRT){
      // remove cards content until visible to reduce DOM
      const content = colEl.querySelector('.cards');
      const addRowRef = addRow;
      const placeholderLoaded = { loaded:false };
      const obs = new IntersectionObserver((entries)=>{
        entries.forEach(ent=>{
          if(ent.isIntersecting && !placeholderLoaded.loaded){
            placeholderLoaded.loaded=true; // cards already built in virtualization above; keep
          } else if(!ent.isIntersecting && placeholderLoaded.loaded){
            // optional: clear when far out of view to free nodes (skip for card virtualization layer)
          }
        });
      }, { root: wrap, rootMargin:'200px' });
      obs.observe(colEl);
    }
    wrap.appendChild(colEl);
  }

  let idx = 0;
  function renderBatch(){
    // Abort if a newer render started
    if(myToken !== window.__renderTokenSeq) return;
    const end = Math.min(idx+perFrame, cols.length);
    for(let i=idx; i<end; i++){
      // Double-check token inside loop for very large batches
      if(myToken !== window.__renderTokenSeq) return;
      renderOneColumn(cols[i]);
    }
    idx = end;
    if(idx < cols.length){
      requestAnimationFrame(renderBatch);
    }
  }
  requestAnimationFrame(renderBatch);

}

// Délégation d'événements pour cartes (réduit nombre de listeners)
document.addEventListener('DOMContentLoaded', ()=>{
  const wrap = document.getElementById('columnsWrap');
  if(!wrap) return;
  wrap.addEventListener('dblclick', (e)=>{
    const cardEl = e.target.closest('.card');
    if(cardEl){ openModal(cardEl.dataset.cardId, cardEl.dataset.colId); }
  });
  wrap.addEventListener('contextmenu', (e)=>{
    const cardEl = e.target.closest('.card');
    if(cardEl){ e.preventDefault(); showCtxMenu(e.pageX, e.pageY, cardEl); }
  });
});

/* -------------------------
  Core operations
--------------------------*/
function createCard(colId, partial={}){
  if(isReadOnly()) return;
  const b = findBoard(state.activeBoard);
  const col = findCol(b, colId);
  if(!col) return;
  if(col.limit && col.limit>0 && col.cards.length>=col.limit){ alert('Column limit reached'); return; }
  const card = {
    id:'card-'+UID(6),
    title: partial.title || 'New card',
    description: partial.description || '',
    tags: partial.tags || [],
    priority: partial.priority || '',
    due: partial.due || '',
    checklist: partial.checklist || [],
    subtasks: partial.subtasks || [],
    images: partial.images || [],
    time: 0,
    created: now(),
    updated: now()
  };
  col.cards.push(card);
  pushUndo(); schedulePersist();
  return card;
}

function moveCard(cardId, fromCol, toCol){
  if(isReadOnly()) return;
  if(fromCol===toCol) return;
  const b = findBoard(state.activeBoard);
  const src = findCol(b, fromCol);
  const dst = findCol(b, toCol);
  if(!src||!dst) return;
  if(dst.nonDraggable){ alert(t('destinationLocked')); return; }
  if(dst.limit && dst.limit>0 && dst.cards.length>=dst.limit){ alert(t('columnLimitReached')); return; }
  const idx = src.cards.findIndex(c=>c.id===cardId);
  if(idx===-1) return;
  const [card]=src.cards.splice(idx,1);
  if(card.locked){ src.cards.splice(idx,0,card); return; }
  dst.cards.push(card);
  pushUndo(); schedulePersist();
  // Mise à jour partielle des deux colonnes pour éviter un rerender complet
  rebuildColumnCards(fromCol);
  rebuildColumnCards(toCol);
}

function deleteCard(cardId, fromCol){
  if(isReadOnly()) return;
  const b = findBoard(state.activeBoard);
  const c = findCol(b, fromCol);
  if(!c) return;
  c.cards = c.cards.filter(x=>x.id!==cardId);
  pushUndo(); schedulePersist(); renderActiveBoard();
}

function duplicateCard(cardId, fromCol){
  if(isReadOnly()) return;
  const b = findBoard(state.activeBoard);
  const c = findCol(b, fromCol);
  if(!c) return;
  const orig = c.cards.find(x=>x.id===cardId); if(!orig) return;
  const copy = JSON.parse(JSON.stringify(orig)); copy.id = 'card-'+UID(6); copy.title += ' (copy)';
  c.cards.push(copy); pushUndo(); schedulePersist(); rebuildColumnCards(fromCol);
}

// --- Rebuild / mise à jour partielle d'une colonne (cartes seulement) ---
function rebuildColumnCards(colId){
  const colEl = document.querySelector(`.col-card[data-col-id="${colId}"]`);
  if(!colEl){ return; }
  const b = findBoard(state.activeBoard);
  const col = findCol(b, colId); if(!col) return;
  // Filtres actifs identiques à renderActiveBoard
  const q = ($('search').value||'').toLowerCase();
  const fPriority = $('filterPriority').value;
  const fTag = $('filterTag').value;
  const filtered = (col.cards||[]).filter(card=>{
    const hay = (card.title + ' ' + (card.description||'') + ' ' + (card.tags||[]).join(' ')).toLowerCase();
    if(q && !hay.includes(q)) return false;
    if(fPriority && card.priority !== fPriority) return false;
    if(fTag && !(card.tags||[]).includes(fTag)) return false;
    return true;
  });
  const cardsWrap = colEl.querySelector('.cards'); if(!cardsWrap) return;
  // Si virtualisation lourde nécessaire, on retombe sur le render global pour garder cohérence
  const totalCards = b.columns.reduce((acc,c)=> acc + c.cards.length, 0);
  const virtThreshold = totalCards > 500 ? 60 : 180;
  const VIRT = state.meta.virtualization && filtered.length > virtThreshold;
  cardsWrap.innerHTML='';
  if(!VIRT){
    if(filtered.length > 40){
      let ci=0;
      function batch(){
        const end = Math.min(ci+25, filtered.length);
        const frag = document.createDocumentFragment();
        for(let k=ci;k<end;k++) frag.appendChild(buildCard(filtered[k], col));
        cardsWrap.appendChild(frag);
        ci=end;
        if(ci<filtered.length) requestAnimationFrame(batch);
      }
      batch();
    } else {
      const frag = document.createDocumentFragment();
      filtered.forEach(card=> frag.appendChild(buildCard(card, col)));
      cardsWrap.appendChild(frag);
    }
  } else {
    // Virtualisation partielle: même logique que renderOneColumn
    let cardHeightEstimate = 110;
    cardsWrap.style.position = 'relative';
    const totalHeight = filtered.length * cardHeightEstimate;
    cardsWrap.style.height = totalHeight + 'px';
    const visibleLayer = document.createElement('div');
    visibleLayer.style.position='absolute'; visibleLayer.style.left='0'; visibleLayer.style.top='0'; visibleLayer.style.right='0';
    cardsWrap.appendChild(visibleLayer);
    function renderVirtual(){
      const scrollTop = cardsWrap.scrollTop;
      const viewHeight = cardsWrap.clientHeight;
      const startIndex = Math.max(0, Math.floor(scrollTop / cardHeightEstimate)-5);
      const endIndex = Math.min(filtered.length, startIndex + Math.ceil(viewHeight / cardHeightEstimate) + 10);
      visibleLayer.innerHTML='';
      visibleLayer.style.transform = `translateY(${startIndex*cardHeightEstimate}px)`;
      const frag = document.createDocumentFragment();
      for(let i=startIndex;i<endIndex;i++) frag.appendChild(buildCard(filtered[i], col));
      visibleLayer.appendChild(frag);
    }
    cardsWrap.addEventListener('scroll', renderVirtual);
    setTimeout(()=>{
      if(filtered[0]){
        const temp = buildCard(filtered[0], col); temp.style.visibility='hidden'; temp.style.position='absolute'; temp.style.top='0'; cardsWrap.appendChild(temp); requestAnimationFrame(()=>{ cardHeightEstimate = temp.getBoundingClientRect().height || cardHeightEstimate; temp.remove(); cardsWrap.style.height = (filtered.length*cardHeightEstimate)+'px'; renderVirtual(); });
      }
    },0);
    renderVirtual();
  }
}

function reorderColumns(colId, beforeColId){
  if(isReadOnly()) return;
  const b = findBoard(state.activeBoard);
  const idxFrom = b.columns.findIndex(c=>c.id===colId);
  const idxTo = b.columns.findIndex(c=>c.id===beforeColId);
  if(idxFrom===-1||idxTo===-1) return;
  if(b.columns[idxFrom].nonDraggable || b.columns[idxTo].nonDraggable) return;
  const [col]=b.columns.splice(idxFrom,1);
  b.columns.splice(idxTo,0,col);
  pushUndo(); schedulePersist(); renderActiveBoard();
}

/* -------------------------
  Modal editing
--------------------------*/
function openModal(cardId, colId){
  const b = findBoard(state.activeBoard);
  const col = findCol(b, colId);
  const card = findCard(col, cardId);
  if(!card) return;
  currentEdit = { cardId, colId };
  $('modalTitle').textContent = 'Edit: ' + card.title;
  $('cardTitle').value = card.title;
  $('cardDesc').value = card.description || '';
  $('cardTags').value = (card.tags||[]).join(',');
  $('cardPriority').value = card.priority || '';
  $('cardDue').value = card.due ? (new Date(card.due)).toISOString().slice(0,16) : '';
  $('checklist').innerHTML = ''; $('subtasks').innerHTML = '';
  (card.checklist||[]).forEach((it, i)=> renderCheckItem(i, it.text, it.done));
  (card.subtasks||[]).forEach((st,i)=> renderSubItem(i, st.text, st.done, st.time));
  $('timerDisplay').textContent = formatTime(card.time||0);
  // versions
  const ver = card.versions||[]; const vEl = $('versions'); if(vEl){ vEl.innerHTML = ver.length? '': '<div class="small-muted">No versions yet</div>'; }
  ver.slice().reverse().forEach((v,idx)=>{
    const row = document.createElement('div');
    row.className='flex items-center justify-between gap-2';
    row.innerHTML = `<div>${escape(v.at)}</div><div class="flex gap-2"><button class="px-2 py-1 btn-ghost restoreVer">Restore</button></div>`;
    row.querySelector('.restoreVer').addEventListener('click', ()=>{
      if(!confirm('Restore this version?')) return;
      const d = v.data;
      card.title = d.title; card.description = d.description; card.tags = (d.tags||[]); card.priority = d.priority; card.due = d.due; card.checklist = JSON.parse(JSON.stringify(d.checklist||[])); card.subtasks = JSON.parse(JSON.stringify(d.subtasks||[])); card.images = (d.images||[]).slice(); card.updated = now();
      pushUndo(); persist(); openModal(cardId,colId); renderActiveBoard();
    });
    vEl?.appendChild(row);
  });
  $('modal').classList.remove('hidden');
}
function closeModal(){ $('modal').classList.add('hidden'); currentEdit = null; }
function saveModal(){
  if(!currentEdit) return;
  const {cardId, colId} = currentEdit;
  const b = findBoard(state.activeBoard);
  const col = findCol(b, colId);
  const card = findCard(col, cardId);
  // versioning snapshot
  card.versions = card.versions||[];
  card.versions.push({ at: now(), data: { title: card.title, description: card.description, tags: (card.tags||[]).slice(), priority: card.priority, due: card.due, checklist: JSON.parse(JSON.stringify(card.checklist||[])), subtasks: JSON.parse(JSON.stringify(card.subtasks||[])), images: (card.images||[]).slice() } });
  card.title = $('cardTitle').value || card.title;
  card.description = $('cardDesc').value;
  card.tags = $('cardTags').value.split(',').map(s=>s.trim()).filter(Boolean);
  card.priority = $('cardPriority').value;
  card.due = $('cardDue').value ? new Date($('cardDue').value).toISOString() : '';
  card.updated = now();
  // checklist & subtasks already updated via render functions
  pushUndo(); persist(); closeModal(); renderActiveBoard();
}

/* checklist render helpers */
function renderCheckItem(i, text, done){
  const wrap = $('checklist');
  const row = document.createElement('div'); row.className='flex items-center gap-2';
  row.innerHTML = `<input type="checkbox" data-i="${i}" ${done?'checked':''}/> <div class="flex-1">${escape(text)}</div> <button data-i="${i}" class="text-red-400">x</button>`;
  row.querySelector('input').addEventListener('change', (e)=> {
    const v = e.target.checked;
    updateCardChecklist(i, v, null, true);
  });
  row.querySelector('button').addEventListener('click', ()=> {
    updateCardChecklist(i, null, null, false);
  });
  wrap.appendChild(row);
}
function updateCardChecklist(idx, checked=null, text=null, keepIf=true){
  if(!currentEdit) return;
  const {cardId, colId} = currentEdit; const b = findBoard(state.activeBoard);
  const card = findCard(findCol(b,colId), cardId);
  if(!card) return;
  if(checked!==null) card.checklist[idx].done = checked;
  if(text!==null) card.checklist[idx].text = text;
  if(!keepIf) card.checklist.splice(idx,1);
  pushUndo(); persist(); renderCheckItems(card);
}
function renderCheckItems(card){ $('checklist').innerHTML=''; (card.checklist||[]).forEach((it,i)=> renderCheckItem(i,it.text,it.done)); }

/* subtasks */
function renderSubItem(i, text, done, time){
  const wrap = $('subtasks');
  const row = document.createElement('div'); row.className = 'flex items-center gap-2';
  row.innerHTML = `<input type="checkbox" data-i="${i}" ${done?'checked':''}/> <div class="flex-1">${escape(text)}</div> <div class="text-xs small-muted">${formatTime(time||0)}</div>`;
  row.querySelector('input').addEventListener('change', (e)=> {
    const v = e.target.checked;
    updateSubtask(i, v, null, true);
  });
  wrap.appendChild(row);
}
function updateSubtask(idx, checked=null, text=null, keepIf=true){
  if(!currentEdit) return;
  const {cardId, colId} = currentEdit; const b = findBoard(state.activeBoard);
  const card = findCard(findCol(b,colId), cardId);
  if(!card) return;
  if(checked!==null) card.subtasks[idx].done = checked;
  if(text!==null) card.subtasks[idx].text = text;
  if(!keepIf) card.subtasks.splice(idx,1);
  pushUndo(); persist(); renderSubtasks(card);
}
function renderSubtasks(card){ $('subtasks').innerHTML=''; (card.subtasks||[]).forEach((s,i)=> renderSubItem(i,s.text,s.done,s.time)); }

/* add image */
function addImageToCard(url){
  if(!currentEdit) return;
  const {cardId,colId} = currentEdit; const b = findBoard(state.activeBoard);
  const card = findCard(findCol(b,colId), cardId);
  card.images = card.images||[]; card.images.push(url);
  pushUndo(); persist(); renderActiveBoard();
}

/* timer per card */
function toggleTimer(){
  if(!currentEdit) return;
  const {cardId,colId} = currentEdit; const b = findBoard(state.activeBoard);
  const card = findCard(findCol(b,colId), cardId);
  if(!card) return;
  if(timers[card.id]){ clearInterval(timers[card.id]); delete timers[card.id]; }
  else {
    timers[card.id] = setInterval(()=> {
      card.time = (card.time||0) + 1;
      if(card.time % 5 === 0) schedulePersist();
      $('timerDisplay').textContent = formatTime(card.time||0);
    }, 1000);
  }
}

/* deadlines notifications */
function checkDeadlines(){
  try{
    if(!('Notification' in window)) return;
    const b = findBoard(state.activeBoard);
    b.columns.forEach(c=> c.cards.forEach(card=>{
      if(card.due && !card.notified){
        const due = new Date(card.due);
        if(due <= new Date()){
          try{ new Notification('Deadline', { body: card.title }); }catch(e){}
          card.notified = true; schedulePersist();
        }
      }
    }));
  }catch(e){}
}

/* -------------------------
  Archive / snapshots / stats
--------------------------*/
function showArchive(){
  const a = loadArchive();
  let html = `<h3 class="font-semibold mb-2">Archive</h3>`;
  a.slice().reverse().forEach(it=>{
    html += `<div class="p-2 border-b border-white/4"><strong>${escape(it.title||it.id)}</strong><div class="text-xs small-muted">${it.archivedAt}</div></div>`;
  });
  $('rightContent').innerHTML = html;
}

function showSnapshots(){
  const snaps = loadSnapshots();
  let html = `<h3 class="font-semibold mb-2">Snapshots</h3>`;
  snaps.slice().reverse().forEach(s=>{
    html += `<div class="p-2 border-b border-white/4 flex justify-between items-center"><div><strong>${escape(s.name)}</strong><div class="text-xs small-muted">${s.time}</div></div><div class="flex gap-2"><button data-id="${s.id}" class="restoreBtn px-2 py-1 bg-emerald-600 rounded text-black">Restore</button><button data-id="${s.id}" class="dlBtn px-2 py-1 bg-cyan-600 rounded text-black">Download</button></div></div>`;
  });
  $('rightContent').innerHTML = html;
  document.querySelectorAll('.restoreBtn').forEach(b=> b.addEventListener('click', ()=>{
    const id = b.dataset.id;
    const snaps = loadSnapshots(); const s = snaps.find(x=>x.id===id);
    if(s && confirm('Restore snapshot? current state will be replaced')){ state = decompress(s.data); schedulePersist(true); renderAll(); alert('Restored'); }
  }));
}

function showSnapshotCompare(){
  const snaps = loadSnapshots();
  if(snaps.length<2){ $('rightContent').innerHTML = '<div class="small-muted">Need at least 2 snapshots</div>'; return; }
  const opts = snaps.map(s=>`<option value="${s.id}">${escape(s.name)} — ${s.time}</option>`).join('');
  $('rightContent').innerHTML = `<h3 class="font-semibold mb-2">Compare snapshots</h3>
    <div class="flex gap-2 mb-2"><select id="cmpA" class="px-2 py-1 bg-transparent border border-white/5">${opts}</select><select id="cmpB" class="px-2 py-1 bg-transparent border border-white/5">${opts}</select><button id="runCmp" class="px-2 py-1 bg-cyan-600 text-black rounded">Compare</button></div>
    <div id="cmpOut" class="text-sm"></div>`;
  $('runCmp').addEventListener('click', ()=>{
    const a = snaps.find(x=>x.id===$('cmpA').value), b = snaps.find(x=>x.id===$('cmpB').value);
    if(!a||!b) return;
    const sa = decompress(a.data), sb = decompress(b.data);
    const count = x=> x.boards.reduce((t,b)=> t + b.columns.reduce((tc,c)=> tc + (c.cards?.length||0),0),0);
    const ca=count(sa), cb=count(sb);
    $('cmpOut').innerHTML = `<div>Cards A: ${ca} / Cards B: ${cb} (Δ ${cb-ca})</div>`;
  });
}

/* stats */
function showStats(){
  const b = findBoard(state.activeBoard);
  let total=0, done=0, time=0;
  b.columns.forEach(c=> c.cards.forEach(card=> { total++; if(c.title.toLowerCase().includes('done')||c.title.toLowerCase().includes('finished')) done++; time += card.time||0; }));
  $('rightContent').innerHTML = `<h3 class="font-semibold mb-2">Stats</h3><div>Total cards: ${total}</div><div>Done: ${done}</div><div>Total tracked time: ${formatTime(time)}</div>`;
}

function showList(){
  const b = findBoard(state.activeBoard);
  let html = `<h3 class="font-semibold mb-2">All tasks</h3>`;
  b.columns.forEach(c=>{
    html += `<div class="mt-2"><strong>${escape(c.title)}</strong></div>`;
    c.cards.forEach(card=>{
      html += `<div class="text-sm small-muted border-b border-white/5 py-1">${escape(card.title)} ${card.due?`• ${(new Date(card.due)).toLocaleString()}`:''}</div>`;
    });
  });
  $('rightContent').innerHTML = html;
}

function showCalendar(){
  const b = findBoard(state.activeBoard);
  const arr=[]; b.columns.forEach(c=> c.cards.forEach(card=>{ if(card.due) arr.push({col:c.title,card}); }));
  arr.sort((a,b)=> new Date(a.card.due)-new Date(b.card.due));
  let html = `<h3 class="font-semibold mb-2">Calendar</h3>`;
  arr.forEach(x=>{ html += `<div class="text-sm border-b border-white/5 py-1">${(new Date(x.card.due)).toLocaleString()} — <strong>${escape(x.card.title)}</strong> <span class="small-muted">(${escape(x.col)})</span></div>`; });
  $('rightContent').innerHTML = html;
}

function showSettings(){
  $('rightContent').innerHTML = `<h3 class="font-semibold mb-2">${t('settingsTitle')}</h3>
    <div class="text-sm space-y-2">
      <div>${t('theme')}: <span class="small-muted">${escape(state.meta.theme||'neon')}</span> <button id="cycleTheme" class="px-2 py-1 bg-slate-700 rounded">${t('themeCycle')}</button></div>
      <div>${t('autosaveInterval')} <input id="asInt" type="number" min="1" class="px-2 py-1 bg-transparent border border-white/5 w-24" value="${Number(state.meta.autosaveIntervalSec||5)}"/> <button id="asSave" class="px-2 py-1 bg-cyan-600 rounded text-black">${t('apply')}</button></div>
      <div>${t('readOnly')}: <button id="toggleRO" class="px-2 py-1 ${state.meta.readOnly?'bg-emerald-500 text-black':'bg-slate-700'} rounded">${state.meta.readOnly?t('readOnlyOn'):t('readOnlyOff')}</button></div>
      <div>${t('orientation')}: <button id="toggleOri" class="px-2 py-1 bg-slate-700 rounded">${state.meta.orientation==='vertical'?t('orientationVertical'):t('orientationHorizontal')}</button></div>
      <div>Performance: <button id="toggleVirt" class="px-2 py-1 ${state.meta.virtualization?'bg-emerald-500 text-black':'bg-slate-700'} rounded">${state.meta.virtualization?'Virtualization ON':'Virtualization OFF'}</button></div>
    </div>`;
  $('cycleTheme').addEventListener('click', ()=>{ cycleTheme(); showSettings(); });
  $('asSave').addEventListener('click', ()=>{ const v = parseInt($("asInt").value||'5',10); if(!Number.isNaN(v)&&v>0){ state.meta.autosaveIntervalSec=v; if(autosaveInterval) clearInterval(autosaveInterval); autosaveInterval=setInterval(()=> persist(), v*1000); persist(); alert(t('apply')); }});
  $('toggleRO').addEventListener('click', ()=>{ setReadOnly(!state.meta.readOnly); showSettings(); });
  $('toggleOri').addEventListener('click', ()=>{ state.meta.orientation = state.meta.orientation==='vertical'?'horizontal':'vertical'; updateOrientation(); persist(); showSettings(); renderActiveBoard(); });
  $('toggleVirt').addEventListener('click', ()=>{ state.meta.virtualization = !state.meta.virtualization; persist(); showSettings(); renderActiveBoard(); });
}

/* -------------------------
  Export / Import
--------------------------*/
function exportJSON(){
  const data = JSON.stringify(state, null, 2);
  downloadFile(data, `mini-trello-${new Date().toISOString().replace(/[:.]/g,'-')}.json`, 'application/json');
}
function exportCompressed(){
  const comp = compress(state);
  downloadFile(comp, `mini-trello-${new Date().toISOString().replace(/[:.]/g,'-')}.txt`, 'text/plain;charset=utf-16');
}
function downloadFile(content, filename, mime){
  const blob = new Blob([content], { type: mime });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a'); a.href = url; a.download = filename; a.click(); URL.revokeObjectURL(url);
}
function importFile(){
  const input = document.createElement('input'); input.type='file'; input.accept='.json,.txt';
  input.onchange = e => {
    const f = e.target.files[0]; if(!f) return;
    const r = new FileReader();
    r.onload = ev => {
      const txt = ev.target.result;
      // try compressed first
      try {
        const maybe = decompress(txt);
        if(maybe){ if(confirm('Import compressed snapshot? replace state?')){ state = maybe; persist(); renderAll(); alert('Imported'); } return; }
      } catch(e){}
      try {
        const obj = JSON.parse(txt);
        if(obj){ if(confirm('Import JSON? replace state?')){ state = obj; persist(); renderAll(); alert('Imported'); } return; }
      } catch(e){ alert('Invalid file'); }
    };
    r.readAsText(f);
  };
  input.click();
}

/* -------------------------
  Context menu
--------------------------*/
let ctxTarget = null;
function showCtxMenu(x,y,target){
  const menu = $('ctxMenu');
  ctxTarget = target;
  menu.style.left = x+'px'; menu.style.top = y+'px'; menu.classList.remove('hidden');
}
function hideCtx(){ $('ctxMenu').classList.add('hidden'); ctxTarget = null; }

document.addEventListener('click', ()=> hideCtx());
document.getElementById('ctxMenu').addEventListener('click', (e)=>{
  e.stopPropagation();
  const a = e.target.dataset.act;
  if(!ctxTarget) return;
  const cardId = ctxTarget.dataset.cardId, colId = ctxTarget.dataset.colId;
  if(a==='edit') openModal(cardId,colId);
  if(a==='dup') duplicateCard(cardId,colId);
  if(a==='archive'){ const b = findBoard(state.activeBoard); const col=findCol(b,colId); const card=findCard(col,cardId); saveArchiveCard(card); deleteCard(cardId,colId); alert('Archived'); }
  if(a==='del'){ if(confirm('Delete card?')) deleteCard(cardId,colId); }
  if(a==='lock'){ const b=findBoard(state.activeBoard); const col=findCol(b,colId); const card=findCard(col,cardId); card.locked=true; pushUndo(); persist(); renderActiveBoard(); }
  if(a==='unlock'){ const b=findBoard(state.activeBoard); const col=findCol(b,colId); const card=findCard(col,cardId); card.locked=false; pushUndo(); persist(); renderActiveBoard(); }
  if(a==='copy'){ const b=findBoard(state.activeBoard); const col=findCol(b,colId); const card=findCard(col,cardId); try{ navigator.clipboard.writeText(JSON.stringify(card)); alert('Copied'); }catch(err){ alert('Clipboard not available'); } }
  if(a==='paste'){ try{ navigator.clipboard.readText().then(txt=>{ try{ const obj=JSON.parse(txt); if(obj && obj.title){ obj.id='card-'+UID(6); createCard(colId, obj); renderActiveBoard(); } }catch(e){ alert('Invalid JSON'); } }); }catch(e){ alert('Clipboard not available'); }
  }
  hideCtx();
});

/* -------------------------
  UI binding
--------------------------*/
function bindUI(){
  $('addBoardSmall').addEventListener('click', ()=> {
    const id = 'b-'+UID(6); state.boards.push({ id, title:'Board '+(state.boards.length+1), columns:[] }); state.activeBoard=id; pushUndo(); persist(); renderAll();
  });
  $('newBoardBtn')?.addEventListener('click', ()=> $('addBoardSmall').click());
  $('addColumnBtn').addEventListener('click', ()=> {
    const title = prompt(t('addColumn'),'New Column'); if(title===null) return;
    const b = findBoard(state.activeBoard); b.columns.push({ id:'c-'+UID(6), title, cards:[], nonDraggable:false, limit:0 }); pushUndo(); persist(); renderActiveBoard();
  });
  $('resetBtn').addEventListener('click', ()=> {
    if(!confirm(t('completeResetConfirm'))) return;
    localStorage.removeItem(storageKey); localStorage.removeItem(snapsKey); localStorage.removeItem(archiveKey); state = JSON.parse(JSON.stringify(DEFAULT)); state.activeBoard=state.boards[0].id; persist(); renderAll();
  });

  $('exportBtn').addEventListener('click', ()=> {
    const mode = confirm('OK = compressed export (fast), Cancel = JSON export (readable)') ? 'comp' : 'json';
    if(mode==='comp') exportCompressed(); else exportJSON();
  });
  $('importBtn').addEventListener('click', ()=> importFile());

  $('snapBtn').addEventListener('click', ()=> { const nm = prompt(t('snapshot'), t('snapshotNamePrefix')+new Date().toLocaleString()); if(nm) { snapshot(nm); alert(t('snapshotSaved')); } });
  $('snapListBtn').addEventListener('click', ()=> showSnapshots());
  $('snapCompareBtn').addEventListener('click', ()=> showSnapshotCompare());
  $('archiveBtn').addEventListener('click', ()=> showArchive());
  $('statsBtn').addEventListener('click', ()=> showStats());
  $('listBtn').addEventListener('click', ()=> showList());
  $('calendarBtn').addEventListener('click', ()=> showCalendar());
  $('settingsBtn').addEventListener('click', ()=> showSettings());

  $('modalClose').addEventListener('click', ()=> closeModal());
  $('modalSave').addEventListener('click', ()=> saveModal());
  $('addCheckBtn').addEventListener('click', ()=> {
    const v = $('checkInput').value.trim(); if(!v||!currentEdit) return; const {cardId,colId}=currentEdit;
    const card = findCard(findCol(findBoard(state.activeBoard),colId), cardId); card.checklist = card.checklist||[]; card.checklist.push({ id:'ch-'+UID(4), text:v, done:false }); $('checkInput').value=''; pushUndo(); persist(); renderCheckItems(card);
  });
  $('addSubBtn').addEventListener('click', ()=> {
    const v=$('subInput').value.trim(); if(!v||!currentEdit) return; const {cardId,colId}=currentEdit;
    const card = findCard(findCol(findBoard(state.activeBoard),colId), cardId); card.subtasks=card.subtasks||[]; card.subtasks.push({ id:'st-'+UID(4), text:v, done:false, time:0 }); $('subInput').value=''; pushUndo(); persist(); renderSubtasks(card);
  });
  $('addImageBtn').addEventListener('click', ()=> { const v = $('cardImage').value.trim(); if(!v||!currentEdit) return; addImageToCard(v); $('cardImage').value=''; });

  $('toggleTimer').addEventListener('click', ()=> toggleTimer());
  $('deleteCardBtn').addEventListener('click', ()=> { if(!currentEdit) return; if(!confirm(t('deleteCardConfirm'))) return; const {cardId,colId}=currentEdit; deleteCard(cardId,colId); closeModal(); });
  $('archiveCardBtn').addEventListener('click', ()=> { if(!currentEdit) return; const {cardId,colId}=currentEdit; const card = findCard(findCol(findBoard(state.activeBoard),colId), cardId); saveArchiveCard(card); deleteCard(cardId,colId); closeModal(); alert(t('archive')); });
  $('duplicateCardBtn').addEventListener('click', ()=> { if(!currentEdit) return; const {cardId,colId}=currentEdit; duplicateCard(cardId,colId); closeModal(); });
  $('copyTitleBtn').addEventListener('click', ()=> { if(!currentEdit) return; const {cardId,colId}=currentEdit; const card = findCard(findCol(findBoard(state.activeBoard),colId), cardId); navigator.clipboard?.writeText(card.title||''); });
  $('copyDescBtn').addEventListener('click', ()=> { if(!currentEdit) return; const {cardId,colId}=currentEdit; const card = findCard(findCol(findBoard(state.activeBoard),colId), cardId); navigator.clipboard?.writeText(card.description||''); });

  $('undoBtn').addEventListener('click', ()=> undo());
  $('themeBtn').addEventListener('click', ()=> cycleTheme());
  $('orientationBtn').addEventListener('click', ()=> { state.meta.orientation = state.meta.orientation==='vertical'?'horizontal':'vertical'; updateOrientation(); persist(); renderActiveBoard(); });
  $('readOnlyBtn').addEventListener('click', ()=> { setReadOnly(!state.meta.readOnly); });

  $('search').addEventListener('input', throttle(()=> renderActiveBoard(), 180));
  $('filterPriority').addEventListener('change', ()=> renderActiveBoard());
  $('filterTag').addEventListener('change', ()=> renderActiveBoard());

  // context menu close helpers
  document.addEventListener('keydown', (e)=> { if(e.key==='Escape') closeModal(); });
  document.addEventListener('click', ()=> hideCtx());

  // Language buttons
  $('langFrBtn')?.addEventListener('click', ()=> setLang('fr'));
  $('langEnBtn')?.addEventListener('click', ()=> setLang('en'));
  $('langFrBtn_m')?.addEventListener('click', ()=> setLang('fr'));
  $('langEnBtn_m')?.addEventListener('click', ()=> setLang('en'));

  /* Mobile menu bindings */
  const mmBtn = $('mobileMenuBtn');
  const mm = $('mobileMenu');
  const mmClose = $('mobileMenuClose');
  function syncMobileToDesktop(){
    const s_m = $('search_m'); const ft_m = $('filterTag_m'); const fp_m = $('filterPriority_m');
    if(s_m) $('search').value = s_m.value;
    if(ft_m) $('filterTag').value = ft_m.value;
    if(fp_m) $('filterPriority').value = fp_m.value;
    renderActiveBoard();
  }
  function openMobileMenu(){ mm.classList.remove('hidden'); populateFilterTags(); if($('search_m')) $('search_m').value = $('search').value; if($('filterTag_m')) $('filterTag_m').value = $('filterTag').value; if($('filterPriority_m')) $('filterPriority_m').value = $('filterPriority').value; }
  function closeMobileMenu(){ mm.classList.add('hidden'); }
  mmBtn?.addEventListener('click', openMobileMenu);
  mmClose?.addEventListener('click', closeMobileMenu);
  $('search_m')?.addEventListener('input', throttle(syncMobileToDesktop, 180));
  $('filterTag_m')?.addEventListener('change', syncMobileToDesktop);
  $('filterPriority_m')?.addEventListener('change', syncMobileToDesktop);
  $('undoBtn_m')?.addEventListener('click', ()=> { undo(); closeMobileMenu(); });
  $('orientationBtn_m')?.addEventListener('click', ()=> { state.meta.orientation = state.meta.orientation==='vertical'?'horizontal':'vertical'; updateOrientation(); persist(); renderActiveBoard(); closeMobileMenu(); });
  $('readOnlyBtn_m')?.addEventListener('click', ()=> { setReadOnly(!state.meta.readOnly); closeMobileMenu(); });
  $('themeBtn_m')?.addEventListener('click', ()=> { cycleTheme(); closeMobileMenu(); });
  $('exportBtn_m')?.addEventListener('click', ()=> { const mode = confirm('OK = compressed export, Cancel = JSON export') ? 'comp':'json'; if(mode==='comp') exportCompressed(); else exportJSON(); closeMobileMenu(); });
  $('importBtn_m')?.addEventListener('click', ()=> { importFile(); closeMobileMenu(); });
  $('newBoardBtn_m')?.addEventListener('click', ()=> { $('addBoardSmall').click(); closeMobileMenu(); });
  $('settingsBtn_m')?.addEventListener('click', ()=> { showSettings(); closeMobileMenu(); });
  $('snapBtn_m')?.addEventListener('click', ()=> { const nm = prompt(t('snapshot'), t('snapshotNamePrefix')+new Date().toLocaleString()); if(nm){ snapshot(nm); alert(t('snapshotSaved')); } closeMobileMenu(); });
  // Dynamic views without closing menu
  const mv = $('mobileView'); const mh = $('mobileMenuHome');
  function mobileShow(html){ mv.innerHTML = `<div class=\"flex items-center justify-between mb-3\"><button id=\"mobileBack\" class=\"px-2 py-1 btn-ghost rounded\"><i class=\"fa-solid fa-arrow-left\"></i></button><button id=\"mobileCloseInner\" class=\"px-2 py-1 btn-ghost rounded\"><i class=\"fa-solid fa-xmark\"></i></button></div>` + html; mh.classList.add('hidden'); mv.classList.remove('hidden');
    mv.querySelector('#mobileBack').addEventListener('click', ()=>{ mv.classList.add('hidden'); mh.classList.remove('hidden'); });
    mv.querySelector('#mobileCloseInner').addEventListener('click', ()=> closeMobileMenu());
  }
  function viewSnapshots(){ const snaps = loadSnapshots(); let html = `<h3 class=\"font-semibold mb-2\">Snapshots</h3>`; snaps.slice().reverse().forEach(s=>{ html += `<div class=\"p-2 border-b border-white/4 flex justify-between items-center\"><div><strong>${escape(s.name)}</strong><div class=\"text-xs small-muted\">${s.time}</div></div><div class=\"flex gap-2\"><button data-id=\"${s.id}\" class=\"restoreBtn px-2 py-1 bg-emerald-600 rounded text-black\">Restore</button><button data-id=\"${s.id}\" class=\"dlBtn px-2 py-1 bg-cyan-600 rounded text-black\">Download</button></div></div>`; }); mobileShow(html); mv.querySelectorAll('.restoreBtn').forEach(b=> b.addEventListener('click', ()=>{ const id=b.dataset.id; const snaps=loadSnapshots(); const s=snaps.find(x=>x.id===id); if(s&&confirm('Restore snapshot?')){ state=decompress(s.data); persist(); renderAll(); alert('Restored'); } })); mv.querySelectorAll('.dlBtn').forEach(b=> b.addEventListener('click', ()=>{ const id=b.dataset.id; const snaps=loadSnapshots(); const s=snaps.find(x=>x.id===id); if(s){ downloadFile(s.data, `snapshot-${s.id}.txt`, 'text/plain'); } })); }
  function viewCompare(){ const snaps=loadSnapshots(); if(snaps.length<2){ mobileShow('<div class=\"small-muted\">Need at least 2 snapshots</div>'); return; } const opts=snaps.map(s=>`<option value=\"${s.id}\">${escape(s.name)} — ${s.time}</option>`).join(''); let html = `<h3 class=\"font-semibold mb-2\">Compare snapshots</h3><div class=\"flex gap-2 mb-2\"><select id=\"cmpA_m\" class=\"px-2 py-1 bg-transparent border border-white/5\">${opts}</select><select id=\"cmpB_m\" class=\"px-2 py-1 bg-transparent border border-white/5\">${opts}</select><button id=\"runCmp_m\" class=\"px-2 py-1 bg-cyan-600 text-black rounded\">Compare</button></div><div id=\"cmpOut_m\" class=\"text-sm\"></div>`; mobileShow(html); mv.querySelector('#runCmp_m').addEventListener('click', ()=>{ const a=snaps.find(x=>x.id===mv.querySelector('#cmpA_m').value), b=snaps.find(x=>x.id===mv.querySelector('#cmpB_m').value); if(!a||!b) return; const sa=decompress(a.data), sb=decompress(b.data); const count = x=> x.boards.reduce((t,b)=> t + b.columns.reduce((tc,c)=> tc + (c.cards?.length||0),0),0); mv.querySelector('#cmpOut_m').innerHTML = `Cards A: ${count(sa)} / Cards B: ${count(sb)} (Δ ${count(sb)-count(sa)})`; }); }
  function viewArchive(){ const a=loadArchive(); let html='<h3 class=\"font-semibold mb-2\">Archive</h3>'; a.slice().reverse().forEach(it=>{ html+=`<div class=\"p-2 border-b border-white/4\"><strong>${escape(it.title||it.id)}</strong><div class=\"text-xs small-muted\">${it.archivedAt}</div></div>`; }); mobileShow(html); }
  function viewStats(){ const b=findBoard(state.activeBoard); let total=0,done=0,time=0; b.columns.forEach(c=> c.cards.forEach(card=>{ total++; if(c.title.toLowerCase().includes('done')||c.title.toLowerCase().includes('finished')) done++; time+=card.time||0; })); mobileShow(`<h3 class=\"font-semibold mb-2\">Stats</h3><div>Total cards: ${total}</div><div>Done: ${done}</div><div>Total tracked time: ${formatTime(time)}</div>`); }
  function viewList(){ const b=findBoard(state.activeBoard); let html='<h3 class=\"font-semibold mb-2\">All tasks</h3>'; b.columns.forEach(c=>{ html+=`<div class=\"mt-2\"><strong>${escape(c.title)}</strong></div>`; c.cards.forEach(card=>{ html+=`<div class=\"text-sm small-muted border-b border-white/5 py-1\">${escape(card.title)} ${card.due?`• ${(new Date(card.due)).toLocaleString()}`:''}</div>`; }); }); mobileShow(html); }
  function viewCalendar(){ const b=findBoard(state.activeBoard); const arr=[]; b.columns.forEach(c=> c.cards.forEach(card=>{ if(card.due) arr.push({col:c.title,card}); })); arr.sort((a,b)=> new Date(a.card.due)-new Date(b.card.due)); let html='<h3 class=\"font-semibold mb-2\">Calendar</h3>'; arr.forEach(x=>{ html+=`<div class=\"text-sm border-b border-white/5 py-1\">${(new Date(x.card.due)).toLocaleString()} — <strong>${escape(x.card.title)}</strong> <span class=\"small-muted\">(${escape(x.col)})</span></div>`; }); mobileShow(html); }
  function viewSettings(){ mobileShow(`<h3 class=\"font-semibold mb-2\">Settings</h3><div class=\"space-y-2\"><div>Theme: <span class=\"small-muted\">${escape(state.meta.theme||'neon')}</span> <button id=\"cycleTheme_m\" class=\"px-2 py-1 bg-slate-700 rounded\">Cycle</button></div><div>Autosave: <span class=\"small-muted\">${state.meta.autosave?'ON':'OFF'}</span></div><div>Orientation: <span class=\"small-muted\">${escape(state.meta.orientation)}</span></div><div>Read-only: <span class=\"small-muted\">${state.meta.readOnly?'ON':'OFF'}</span></div></div>`); mv.querySelector('#cycleTheme_m')?.addEventListener('click', ()=>{ cycleTheme(); viewSettings(); }); }
  function viewBoards(){ const html = state.boards.map(b=>`<button data-b=\"${b.id}\" class=\"w-full text-left px-3 py-2 rounded-md smooth ${b.id===state.activeBoard?'bg-white/10':'hover:bg-white/5'}\"><strong>${escape(b.title)}</strong><div class=\"text-xs small-muted\">${b.columns.length} cols</div></button>`).join(''); mobileShow(`<h3 class=\"font-semibold mb-2\">Boards</h3>${html}`); mv.querySelectorAll('button[data-b]')?.forEach(btn=> btn.addEventListener('click', ()=>{ state.activeBoard = btn.dataset.b; persist(); renderAll(); viewBoards(); })); }
  $('snapListBtn_m')?.addEventListener('click', viewSnapshots);
  $('snapCompareBtn_m')?.addEventListener('click', viewCompare);
  $('archiveBtn_m')?.addEventListener('click', viewArchive);
  $('statsBtn_m')?.addEventListener('click', viewStats);
  $('listBtn_m')?.addEventListener('click', viewList);
  $('calendarBtn_m')?.addEventListener('click', viewCalendar);
  $('settingsBtn_m')?.addEventListener('click', viewSettings);
  $('boardsBtn_m')?.addEventListener('click', viewBoards);
}

/* -------------------------
  Helpers UI small
--------------------------*/
function escape(s){ return String(s||'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;'); }
function renderRightbarEmpty(){ $('rightContent').innerHTML = `<div class="text-sm small-muted">Open snapshots, archive or stats</div>`; }
function populateFilterTags(){
  const sel = $('filterTag'); const set = new Set();
  state.boards.forEach(b=> b.columns.forEach(c=> c.cards.forEach(card=> (card.tags||[]).forEach(t=> set.add(t)))));
  sel.innerHTML = '<option value="">Filter tag</option>';
  Array.from(set).forEach(t=> { const o=document.createElement('option'); o.value=t; o.textContent=t; sel.appendChild(o); });
  const selm = document.getElementById('filterTag_m');
  if(selm){
    selm.innerHTML = '<option value="">Tag</option>';
    Array.from(set).forEach(t=> { const o=document.createElement('option'); o.value=t; o.textContent=t; selm.appendChild(o); });
  }
}

/* -------------------------
  Mini API
--------------------------*/
window.MiniTrello = {
  createCard: (colId, partial) => createCard(colId, partial),
  moveCard,
  deleteCard,
  addColumn: (title)=> { const b=findBoard(state.activeBoard); b.columns.push({ id:'c-'+UID(6), title: title||'Column', cards:[] }); pushUndo(); persist(); renderActiveBoard(); },
  getState: ()=> JSON.parse(JSON.stringify(state)),
  importCompressed: (s)=> { const obj = decompress(s); if(obj){ state = obj; persist(); renderAll(); } }
};

/* -------------------------
  Init
--------------------------*/
(function init(){
  if(!state.activeBoard) state.activeBoard = state.boards[0]?.id;
  bindUI();
  document.body.dataset.theme = state.meta.theme || 'neon';
  updateOrientation();
  renderAll();
  if(state.meta.autosave){ if(autosaveInterval) clearInterval(autosaveInterval); autosaveInterval = setInterval(()=> persist(), (state.meta.autosaveIntervalSec||5)*1000); }
  // auto-scroll near edges during drag
  const wrap = document.getElementById('columnsWrap');
  wrap.addEventListener('dragover', (e)=>{
    const rect = wrap.getBoundingClientRect();
    const margin = 60;
    if(state.meta.orientation==='vertical'){
      if(e.clientY < rect.top + margin) wrap.scrollTop -= 20;
      else if(e.clientY > rect.bottom - margin) wrap.scrollTop += 20;
    } else {
      if(e.clientX < rect.left + margin) wrap.scrollLeft -= 20;
      else if(e.clientX > rect.right - margin) wrap.scrollLeft += 20;
    }
  });
  // request notification permission for deadlines
  if("Notification" in window && Notification.permission !== "granted") {
    try{ Notification.requestPermission(); } catch(e){}
  }
  if(!deadlineInterval) deadlineInterval = setInterval(checkDeadlines, 60000);
  checkDeadlines();
})();

/* -------------------------
  End
--------------------------*/
</script>

</body>
</html>

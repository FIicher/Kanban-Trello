<DIV align="center">
  <img src="https://dihu.fr/appgithub/iconedihu/9.png" width="120" style="border-radius:20px; margin-bottom:15px;">
  <H3>🧩 Kanban Trello (Offline)</H3>
  <h4>Kanban / Productivité 100% local — multi‑board, snapshots, versioning, export chiffré</h4>
</DIV>

---

<b>Utilité :</b><br>
<i>Kanban Trello est un gestionnaire Kanban ultra‑léger et autonome : une seule page HTML/JS pouvant être ouverte dans n'importe quel navigateur moderne. Il fonctionne sans serveur, stocke tout dans <code>localStorage</code> (avec compression LZ‑String), gère plusieurs tableaux (boards), colonnes, cartes, historique (Undo), snapshots, versioning, timer par carte, vues calendrier & liste, export/import (JSON ou compressé), modes de visualisation (thèmes, orientation), lecture seule et verrouillage granulaire. Idéal pour : prototypage, gestion personnelle de tâches, planification offline, démonstrations pédagogiques, usage en contexte isolé (air‑gapped), embarqué dans d'autres apps ou WebViews.</i><br><br>

<b>Philosophie :</b><br>
<i>Tout est local, aucune requête réseau hors CDN CSS/ICÔNES facultatifs. Les données ne quittent jamais votre machine (sauf si vous exportez). Le code privilégie la lisibilité, des structures plates et des opérations synchrone rapides.</i><br><br>

<b>Résumé rapide des capacités :</b><br>
• Multi‑boards dans un seul fichier (workspace local)<br>
• Colonnes dynamiques (titre, limite de cartes, verrouillage déplacement, collapse)<br>
• Cartes avec : titre, description, tags colorés auto, priorité, deadline, images (URL/base64), checklist, sous‑tâches, temps passé (timer), versioning, verrouillage individuel<br>
• Drag & drop cartes + colonnes (desktop + mobile basique) avec auto‑scroll de confort<br>
• Filtrage par tag / priorité, recherche temps réel (<i>throttle</i>)<br>
• Barre de progression (checklist → %)<br>
• Notifications locales des deadlines (si autorisées)<br>
• Snapshots multiples + comparaison différentielle simplifiée<br>
• Undo multi‑états (pile compressée, limite configurable)<br>
• Export / import JSON lisible ou bloc compressé UTF‑16<br>
• Thèmes pré‑définis (Neon, Minimal, Material, Pastel, Noir total) + orientation horizontale/verticale<br>
• Mode lecture seule global (verrouille actions & drag)<br>
• Contexte menu (clic droit) : Edit, Duplicate, Archive, Delete, Lock/Unlock, Copy/Paste JSON<br>
• Versioning de cartes (historique interne avec restauration)<br>
• Vues secondaires : Liste globale des tâches, Calendrier des échéances, Stats agrégées, Settings<br>
• API interne JS accessible via <code>window.MiniTrello</code><br>
• Compression LZ‑String transparente (performance & stockage optimisés)<br>

---

<b>Modèle de données (structure principale) :</b><br>
<pre><code>{
  meta: {
    created: ISOString,
    theme: 'neon'|'minimal'|'material'|'pastel'|'noir',
    autosave: true,
    autosaveIntervalSec: 5,
    readOnly: false,
    orientation: 'horizontal'|'vertical'
  },
  boards: [
    {
      id, title,
      columns: [
        {
          id, title, cards: [], limit: Number, nonDraggable: Boolean, collapsed: Boolean
        }
      ]
    }
  ],
  activeBoard: boardId,
  undo: [CompressedStateUTF16,...]
}
</code></pre>

<b>Carte (card) enrichie :</b><br>
<pre><code>{
  id, title, description,
  tags: [String,...],
  priority: 'high'|'normal'|'low'|'',
  due: ISODateString|'',
  checklist: [{id,text,done},...],
  subtasks: [{id,text,done,time},...],
  images: [urlOrBase64,...],
  time: Number(seconds),
  locked: Boolean,
  versions: [ { at: ISOString, data: { ...snapshotFields } }, ... ],
  notified: Boolean (deadline déjà notifiée),
  created, updated
}
</code></pre>

---

<b>Fonctionnement général :</b><br>
1- <i>Sélection du board actif (sidebar)</i><br>
2- <i>Ajout / édition colonnes (titre, limite, verrouillage, collapse)</i><br>
3- <i>Création rapide de cartes (input bas de colonne) ou duplication</i><br>
4- <i>Double clic carte → ouverture modale détaillée (édition complète + versioning)</i><br>
5- <i>Drag & drop pour réorganiser colonnes et déplacer cartes (limites & locks respectés)</i><br>
6- <i>Snapshots ponctuels (archive d'état) + comparaison Δ nombre de cartes</i><br>
7- <i>Undo pour revenir à un état précédent (pile compressée)</i><br>
8- <i>Export compressé ou JSON lisible → Import remplace l'état courant</i><br>
9- <i>Notifications deadlines (background interval 60s)</i><br>
10- <i>Vue Liste / Calendrier / Stats / Settings pour autres perspectives</i><br><br>

<b>Boutons & UI principaux :</b><br>
• Header: <code>Undo</code>, <code>Orientation</code>, <code>Read-only</code>, <code>Theme</code>, <code>Export</code>, <code>Import</code>, <code>New board</code><br>
• Sidebar (Quick): Snapshots, Compare snaps, Archive, Stats, All tasks, Calendar, Settings<br>
• Column header: Edit (titre/limite/verrouillage), Del, Collapse<br>
• Carte (clic droit): Edit / Duplicate / Archive / Delete / Lock / Unlock / Copy JSON / Paste JSON<br>
• Modale carte: Save, Checklist + sous‑tâches, Timer, Versions (restore), Copy title/desc<br>
• Settings: Intervalle autosave, orientation, thème, lecture seule<br>

---

<b>Fonctions internes clés :</b><br>
<pre><code>createCard(colId, partial)
moveCard(cardId, fromColId, toColId)
deleteCard(cardId, colId)
addColumn(title)
MiniTrello.getState()
MiniTrello.importCompressed(string)
</code></pre>

<b>API interne (via window.MiniTrello) :</b><br>
• <code>MiniTrello.createCard(colId, {title, ...})</code> → crée carte<br>
• <code>MiniTrello.moveCard(cardId, fromCol, toCol)</code> → déplace carte (respecte locks/limites)<br>
• <code>MiniTrello.deleteCard(cardId, colId)</code> → supprime carte<br>
• <code>MiniTrello.addColumn(title)</code> → ajoute colonne au board actif<br>
• <code>MiniTrello.getState()</code> → snapshot JS cloné de l'état<br>
• <code>MiniTrello.importCompressed(comp)</code> → remplace l'état à partir d'une chaîne compressée<br>

<b>Exemples Console :</b><br>
<pre><code>// Ajouter une carte programmatiquement
MiniTrello.createCard(document.querySelector('[data-col-id]').dataset.colId, {
  title: 'Tâche rapide', priority: 'high', tags: ['urgent','build']
});

// Lister toutes les cartes
const st = MiniTrello.getState();
st.boards.forEach(b => b.columns.forEach(c => c.cards.forEach(card => console.log(card.title))));

// Export compressé manuel (copier dans presse‑papier)
const compressed = LZString.compressToUTF16(JSON.stringify(MiniTrello.getState()));
</code></pre>

---

<b>Performance & stockage :</b><br>
• Compression UTF‑16 réduit taille moyenne 40–65% selon contenu.<br>
• Pile Undo limitée (40 états) pour éviter gonflement localStorage.<br>
• Rendu: ré‑génération complète de colonnes avec opérations DOM légères.<br>
• Throttle recherche (180 ms) pour ne pas surcharger en frappe rapide.<br><br>

<b>Sécurité & confidentialité :</b><br>
• Aucune fuite réseau (hors CDN si conservés — remplaçables en local).<br>
• Export compressé non chiffré → chiffrer manuellement si besoin (ex AES avant partage).<br>
• Lecture seule pour protéger un état en démonstration ou audit interne.<br>
• Verrouillage carte / colonne empêche déplacement accidentel.<br><br>

<b>Limitations actuelles :</b><br>
• Pas de multi‑utilisateur / synchronisation en temps réel.<br>
• Notifications deadlines non persistantes (pas de re‑programmation si onglet fermé).<br>
• Mobile iOS: drag & drop HTML5 peut être partiel (nécessiter polyfill si critique).<br>
• Comparaison snapshots: métrique simplifiée (Δ cartes) — pas de diff profond par champs.<br>
• Pas de cryptage natif du stockage local.<br>
• Aucun tri avancé (manuel ou auto) dans colonnes (hors reorder DnD).<br><br>

<b>Améliorations possibles (roadmap personnelle) :</b><br>
• Diff snapshots détaillé (ajouts / suppressions / modifications par champ).<br>
• Vue calendrier mensuelle + drag réordonnancement par date.<br>
• Focus mode plein écran carte + navigation séquentielle.<br>
• IA locale (génération checklist à partir du titre).<br>
• Barre de productivité (scores, badges gamification).<br>
• Cryptage côté client + mot de passe (AES + PBKDF2).<br>
• Import partiel (merge sélectif).<br>
• Stats avancées (temps moyen par colonne, goulots).<br><br>

<b>Codes de retour / messages (principaux) :</b><br>
• Alertes limites colonne: "Column limit reached".<br>
• Verrouillage destination: "Destination column is locked".<br>
• Undo pile vide: "Nothing to undo".<br>
• Notifications deadlines: permission demandée au chargement si nécessaire.<br><br>

<b>Mini Guide d’utilisation rapide :</b><br>
<pre><code>1. Ouvrir le fichier dans votre navigateur.
2. Cliquer "Add column" pour créer une nouvelle colonne.
3. Saisir le titre d’une carte dans l’input rapide puis Add.
4. Double clic sur carte → éditer, ajouter checklist / sous‑tâches / image.
5. Définir une date limite (deadline) pour activer notification locale.
6. Utiliser Snapshot pour capturer état; Compare snaps pour voir évolution.
7. Export (JSON lisible ou compressé) pour backup externe.
8. Toggle Read-only avant présentation pour éviter modifications accidentelles.
</code></pre>

---

<b>Badges / Indicateurs :</b><br>
<DIV align="center">

![Offline](https://img.shields.io/badge/Offline-100%25-blueviolet?style=for-the-badge)
![Compression](https://img.shields.io/badge/Compressed-LZ--String-0B8FEA?style=for-the-badge)
![MultiBoard](https://img.shields.io/badge/Multi--Boards-Yes-success?style=for-the-badge)
![Versioning](https://img.shields.io/badge/Versioning-Cards-orange?style=for-the-badge)
![Theming](https://img.shields.io/badge/Themes-5-lightgrey?style=for-the-badge)
![DragDrop](https://img.shields.io/badge/Drag%20%26%20Drop-HTML5-informational?style=for-the-badge)
![Export](https://img.shields.io/badge/Export-JSON%20%2B%20Compressed-green?style=for-the-badge)
![License](https://img.shields.io/badge/Server-Not%20Required-critical?style=for-the-badge)

<h5>Organisez, itérez, sauvegardez… sans dépendances serveur. 🚀</h5>
</DIV>

---

<b>FAQ courte :</b><br>
• Q: "Puis‑je partager un board ?" → Export JSON et envoyer le fichier, l’autre personne importe.<br>
• Q: "Sauvegarde cloud ?" → Non native. Utilisez script externe (ex: cron qui récupère localStorage via WebDriver).<br>
• Q: "Pourquoi UTF‑16 pour compression ?" → Méthode <code>compressToUTF16</code> évite caractères problématiques et maximise compatibilité copy/paste.<br>
• Q: "Puis‑je ajouter un champ custom ?" → Étendre l’objet carte et ajuster rendus (zones marquées par <code>renderActiveBoard</code>).<br>
• Q: "Compat navigateur ?" → Moderne (Chrome, Firefox, Edge). Safari iOS: drag éventuellement partiel.<br>

---

<br><br>
<div align="center">| ENGLISH |</div>
<br>
<h4>Ultra‑Light Offline Kanban — Multi‑Board, Snapshots, Versioning, Compressed State</h4>
</DIV>

<b>Purpose:</b><br>
<i>kanban Trello is a single‑file offline Kanban board: pure HTML/JS, no backend needed. It stores everything in <code>localStorage</code> (LZ‑String compression), supports multiple boards, columns, cards, undo history, snapshots, versioning, timers, calendar & list views, theme/orientation toggles, read‑only mode, granular locking, JSON import/export, deadline notifications, and an internal JS API — perfect for personal productivity, air‑gapped environments, demos or embedding.</i><br><br>

<b>Key Capabilities:</b><br>
• Multi boards in one page<br>
• Dynamic columns (title, card limit, lock movement, collapse)<br>
• Cards: title, description, auto colored tags, priority, deadline, images, checklist, subtasks, tracked time, versioning, lock<br>
• Drag & drop (cards + columns) with auto‑scroll comfort<br>
• Tag / priority filters + real‑time search (throttled)<br>
• Progress bar from checklist completion<br>
• Local deadline notifications (if granted)<br>
• Snapshots + simple compare (Δ cards)<br>
• Undo stack (compressed states)<br>
• Export/import (readable JSON or compressed UTF‑16 blob)<br>
• Themes (Neon, Minimal, Material, Pastel, Noir) + horizontal/vertical orientation<br>
• Global Read‑Only mode<br>
• Context menu: Edit / Duplicate / Archive / Delete / Lock / Unlock / Copy / Paste JSON<br>
• Card version history + restore<br>
• Secondary views: List, Calendar, Stats, Settings<br>
• Internal API via <code>window.MiniTrello</code><br>

<b>Data Model (simplified):</b><br>
<pre><code>{ meta:{theme,autosaveIntervalSec,readOnly,orientation}, boards:[{id,title,columns:[{id,title,cards,limit,nonDraggable,collapsed}]}], activeBoard, undo:[] }</code></pre>

<b>Card structure:</b><br>
<pre><code>{id,title,description,tags,priority,due,checklist,subtasks,images,time,locked,versions:[{at,data}],notified,created,updated}</code></pre>

<b>Workflow:</b><br>
1- Select active board<br>
2- Add/edit columns (limit, lock, collapse)<br>
3- Quick add cards or duplicate<br>
4- Double click card → modal edit (checklist, subtasks, images, version restore)<br>
5- Drag & drop for ordering and moving (respects limits & locks)<br>
6- Take snapshots, compare evolution (Δ cards)<br>
7- Undo previous state (compressed history)<br>
8- Export / Import for backup & transfer<br>
9- Deadline notifications when time reached<br>
10- Explore List / Calendar / Stats / Settings views<br><br>

<b>Internal Functions:</b><br>
<pre><code>MiniTrello.createCard(colId, partial)
MiniTrello.moveCard(cardId, fromCol, toCol)
MiniTrello.deleteCard(cardId, colId)
MiniTrello.addColumn(title)
MiniTrello.getState()
MiniTrello.importCompressed(string)</code></pre>

<b>Console Examples:</b><br>
<pre><code>// Add a quick card
MiniTrello.createCard(document.querySelector('[data-col-id]').dataset.colId, {
  title: 'Quick task', priority: 'high', tags: ['urgent']
});

// List all card titles
MiniTrello.getState().boards.forEach(b=>b.columns.forEach(c=>c.cards.forEach(card=>console.log(card.title))));

// Manual compressed export
const comp = LZString.compressToUTF16(JSON.stringify(MiniTrello.getState()));
</code></pre>

<b>Performance & Storage:</b><br>
• LZ‑String reduces size ~40–65%<br>
• Undo capped (40 states)<br>
• Full re‑render with lean DOM creation<br>
• Search throttled (180ms)<br><br>

<b>Security & Privacy:</b><br>
• Pure local operation (replace CDNs for full isolation)<br>
• Compressed exports are NOT encrypted; add client crypto if required<br>
• Read‑Only mode prevents accidental changes during demos<br>
• Locks prevent unwanted drag/drop<br><br>

<b>Limitations:</b><br>
• No real‑time multi‑user sync<br>
• Deadline notifications only while tab stays open<br>
• iOS Safari drag & drop may need polyfill<br>
• Snapshot diff limited (card count only)<br>
• No built‑in encryption or advanced sorting<br><br>

<b>Potential Improvements:</b><br>
• Deep snapshot diff (field changes)
• Monthly calendar grid + drag by date
• Focus full‑screen card mode
• Local AI (generate checklist suggestions)
• Productivity scoring / badges
• Client encryption (AES + PBKDF2)
• Partial merge import
• Advanced metrics (avg time per column)

<b>Quick Usage Guide:</b><br>
<pre><code>1. Open the file in your browser.
2. Click "Add column".
3. Type a card title and click Add.
4. Double click card to edit details.
5. Set deadlines for notification.
6. Use Snapshot & Compare to track history.
7. Export JSON for backup.
8. Toggle Read-only before presenting.
</code></pre>

<DIV align="center">

![Offline](https://img.shields.io/badge/Offline-100%25-blueviolet?style=for-the-badge)
![Compressed](https://img.shields.io/badge/Compressed-LZ--String-0B8FEA?style=for-the-badge)
![MultiBoard](https://img.shields.io/badge/Multi--Boards-Yes-success?style=for-the-badge)
![Versioning](https://img.shields.io/badge/Versioning-Cards-orange?style=for-the-badge)
![Themes](https://img.shields.io/badge/Themes-5-lightgrey?style=for-the-badge)
![DragDrop](https://img.shields.io/badge/Drag%20%26%20Drop-HTML5-informational?style=for-the-badge)
![Export](https://img.shields.io/badge/Export-JSON%20%2B%20Compressed-green?style=for-the-badge)
![Serverless](https://img.shields.io/badge/Server-Not%20Required-critical?style=for-the-badge)

<h5>Organize, iterate, preserve — fully local freedom. 🎯</h5>
</DIV>

<b>FAQ:</b><br>
• Share a board? → Export JSON, send, import on target.<br>
• Cloud sync? → Not built‑in; use external automation/WebDriver to extract & push.<br>
• Why UTF‑16 compression? → Ensures safe copy/paste, avoids control chars issues.<br>
• Custom field? → Extend card object & update renderActiveBoard.<br>
• Browser support? → Modern browsers; partial drag on iOS Safari.<br>

---

<small>Kanban Trello — Single file productivity. Adapt, extend, fork librement.</small>

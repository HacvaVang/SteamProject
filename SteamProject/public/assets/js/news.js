"use strict";

/* Sample data for the frontend-only submission.
   Later replace with data from the News and Post models (MySQL). */
var GAME = { name: "Critical Strike", price: "$14.99", inGame: "22,039", chat: "45,246",
  desc: "Critical Strike is a co-op movement rhythm fighter. Pick a class, move to the beat and duel waves of enemies and bosses with your squad, with every dash, strike and dodge locked to the rhythm." };
var TABS = [
  { k: "All", l: "All" }, { k: "Discussion", l: "Discussions" }, { k: "Screenshot", l: "Screenshots" }, { k: "Artwork", l: "Artwork" },
  { k: "Video", l: "Videos" }, { k: "News", l: "News" }, { k: "Guide", l: "Guides" }, { k: "Review", l: "Reviews" }
];
var KIND_LABEL = { News: "NEWS", Discussion: "DISCUSSION", Artwork: "ARTWORK", Guide: "GUIDE", Review: "REVIEW", Screenshot: "SCREENSHOT", Video: "VIDEO" };

function item(id, kind, title, date, author, replies, lastBy, lastDate, hue, body, extra) {
  var o = { id: id, kind: kind, title: title, date: date, author: author, replies: replies,
            lastBy: lastBy, lastDate: lastDate, hue: hue, body: body, excerpt: body[0] };
  return Object.assign(o, extra || {});
}

function sec(h, p, ul) { return { h: h, p: p, ul: ul || [] }; }

var ITEMS = [
  item(1, "News", "Season 3: The Neon Siege is live", "2026-11-18", "Critical Strike Team", 12, "pixel_pete", "2026-11-20", 200,
    ["Season 3 adds ten new songs, a new boss gauntlet and a ranked co-op ladder.", "Every wave now scales to your squad's combined combo, so teamwork matters more than ever.", "The season runs until the end of February."]),
  item(2, "News", "New class: the Sentinel joins the roster", "2026-11-12", "Critical Strike Team", 5, "gabe_fan", "2026-11-15", 260,
    ["The Sentinel is a shield-and-pulse support class built to protect teammates between beats.", "Perfect parries on the beat reflect projectiles and refill the squad's rhythm meter."]),
  item(3, "News", "Community charts highlights", "2026-11-05", "Community Team", 3, "modder99", "2026-11-09", 140,
    ["The best player-made charts and song packs from the past month.", "Thank you to every producer who shared their work with the community."]),
  item(4, "News", "Scheduled maintenance on Tuesday", "2026-10-29", "Support Team", 0, "Support Team", "2026-10-29", 30,
    ["Servers will be offline for a short time while we deploy the new matchmaking system.", "Maintenance should take about 30 minutes. Your progress and ranks are not affected."], { pinned: true }),
  item(5, "News", "Improved audio sync for wireless headphones", "2026-10-21", "Critical Strike Team", 7, "saigon_gamer", "2026-10-25", 320,
    ["A new latency calibration step helps wireless headphones stay on the beat.", "Run the calibration from Settings, then Audio, after installing the update."]),
  item(6, "News", "Patch 1.4 notes: boss rebalance", "2026-10-07", "Critical Strike Team", 4, "safe_user", "2026-10-10", 0,
    ["Boss health and phase timings were retuned for squads of one to four players.", "The Bass Titan's second phase now telegraphs its attacks one beat earlier."]),
  item(7, "News", "Gamepad support expanded", "2026-09-30", "Critical Strike Team", 1, "padmaster", "2026-10-01", 230,
    ["More controllers are now recognized out of the box.", "Open Settings, then Controller, to remap dash, strike and parry."]),
  item(9, "Discussion", "Best class pairing for co-op waves 30 and up?", "2026-11-20", "pixel_pete", 18, "gabe_fan", "2026-11-21", 210,
    ["Curious what everyone is running at the high waves.", "My squad is trying Sentinel plus Blademaster and it feels very stable. Thoughts?"]),
  item(10, "Discussion", "Looking for co-op partners (SEA servers)", "2026-11-19", "saigon_gamer", 9, "modder99", "2026-11-20", 150,
    ["Looking for chill players to run boss rush in the evenings.", "I am on the Southeast Asia servers, voice chat optional."]),
  item(11, "Discussion", "Audio desync after the latest update", "2026-11-17", "safe_user", 6, "Support Team", "2026-11-18", 10,
    ["Since the last patch the hit sounds drift behind the beat on my wired headset.", "I already ran the calibration. Any other fixes?"]),
  item(12, "Artwork", "Blademaster under neon rain", "2026-11-20", "indie_lover", 14, "pixel_pete", "2026-11-21", 280,
    ["Digital painting of the Blademaster mid-dash, made in about eight hours."]),
  item(13, "Artwork", "Pixel art Medic class", "2026-11-18", "padmaster", 6, "gabe_fan", "2026-11-19", 190,
    ["A 64x64 pixel art version of the Medic idle animation."]),
  item(14, "Artwork", "Concept sketch: Bass Titan boss", "2026-11-14", "modder99", 3, "indie_lover", "2026-11-15", 40,
    ["Early concept sketch for a boss built around a giant speaker stack."]),
  item(15, "Artwork", "Gunslinger poster", "2026-11-10", "gabe_fan", 11, "pixel_pete", "2026-11-12", 330,
    ["Poster-style portrait of the Gunslinger with a halftone background."]),
  item(16, "Guide", "Movement guide: dashes, slides and cancels", "2026-11-16", "pixel_pete", 22, "safe_user", "2026-11-20", 120,
    ["Everything you need to move cleanly and keep your combo alive.", "Good movement is mostly timing: dash on the beat, slide on the off-beat and cancel into attacks."],
    { rating: 4.7, cat: "Movement", sections: [
      sec("Dash basics", ["Dash on the beat to gain a speed bonus and a short invulnerability window."], ["Tap early for a soft dash", "Tap exactly on the beat for a perfect dash"]),
      sec("Slide cancels", ["Cancel a slide into an attack to keep your combo alive and reset the dash cooldown."], ["Slide, then strike on the next beat", "Use slides to pass under boss sweeps"]),
      sec("Common mistakes", ["Dashing off the beat breaks your multiplier and leaves you exposed."])] }),
  item(17, "Guide", "Complete achievement guide", "2026-11-08", "gabe_fan", 15, "modder99", "2026-11-13", 60,
    ["A checklist of every achievement with tips for the hard ones.", "Start with the story achievements, then move on to combos and bosses."],
    { rating: 4.4, cat: "Achievements", sections: [
      sec("Combo achievements", ["Reach a 200 combo on any song, then a 500 combo on a hard song."], ["Perfect Streak", "Unbroken", "Metronome"]),
      sec("Boss achievements", ["Defeat every boss without taking damage during its final phase."]),
      sec("Co-op achievements", ["Finish a full wave set with four different classes in one squad."])] }),
  item(18, "Guide", "Audio latency, keybinds and config files", "2026-11-01", "padmaster", 8, "saigon_gamer", "2026-11-05", 250,
    ["How to set up your audio offset and controls so the game feels perfectly on beat.", "Most timing problems come from latency, not from your hands."],
    { rating: 4.1, cat: "Modding or Config", sections: [
      sec("Audio latency calibration", ["Run the calibration tool and apply the suggested offset in milliseconds."]),
      sec("Keybinds", ["Put dash and strike on keys that sit under your strongest fingers."], ["Avoid keys that share a keyboard ghosting group", "Bind parry to a thumb button on gamepads"]),
      sec("Config file", ["Advanced users can edit the config file to change the judgement window display."])] }),
  item(19, "Review", "Best rhythm fighter I have played in years", "2026-11-19", "pixel_pete", 7, "gabe_fan", "2026-11-20", 205,
    ["Landing a perfect dash into a combo feels incredible. The movement is tight, the songs are excellent and co-op boss fights are pure adrenaline.", "After 140 hours I am still finding new ways to chain moves on the beat. Easily worth the full price."],
    { recommended: true, hours: 142.5, helpful: 48, funny: 6, products: 187, reviews: 23 }),
  item(20, "Review", "Great ideas, but solo mode drags", "2026-11-15", "modder99", 4, "saigon_gamer", "2026-11-17", 35,
    ["Co-op is fantastic, but solo waves feel repetitive after a while.", "Still recommended, especially if you like making custom charts. The producer tools are great."],
    { recommended: true, hours: 63.2, helpful: 19, funny: 2, products: 92, reviews: 11 }),
  item(21, "Review", "Too many desyncs since the last patch", "2026-11-12", "safe_user", 9, "Support Team", "2026-11-14", 5,
    ["I wanted to like this, but hit sounds drift off the beat every few minutes after the latest update.", "I will change this review once the audio sync is fixed."],
    { recommended: false, hours: 8.4, helpful: 31, funny: 1, products: 41, reviews: 3 }),
  item(22, "Discussion", "Season 3 feedback thread", "2026-11-20", "Steve", 163, "Steve", "2026-11-20", 205,
    ["Official thread for feedback on Season 3: the new songs, bosses and ranked ladder.", "Please keep bug reports in the dedicated tracking thread."], { pinned: true, sub: "General Discussions", ago: "20 Nov @ 1:14am" }),
  item(23, "Discussion", "Support resources and bug tracking", "2026-11-18", "Rayman1103", 0, "Rayman1103", "2026-11-18", 120,
    ["Use this thread to find support links and report bugs."], { pinned: true, locked: true, sub: "General Discussions", ago: "2 Jan, 2026 @ 12:35am" }),
  item(24, "Discussion", "Is the Gunslinger too strong in Boss Rush?", "2026-11-21", "Purple Francis", 8, "pixel_pete", "2026-11-21", 300,
    ["Ranged damage seems to trivialize the Bass Titan. Is this intended?"], { sub: "General Discussions", ago: "8 minutes ago" }),
  item(25, "Discussion", "How do I import custom charts?", "2026-11-21", "unkindled1", 17, "gabe_fan", "2026-11-21", 20,
    ["I exported a chart from the editor but it does not show up in the song list."], { sub: "Charts and Modding Help", ago: "9 minutes ago" }),
  item(26, "Discussion", "Anyone up for ranked duels tonight?", "2026-11-21", "Isaaa", 2, "modder99", "2026-11-21", 170,
    ["Looking for a duel partner, any skill level."], { sub: "Community Servers/Groups", ago: "5 hours ago" }),
  item(27, "Discussion", "Trading 3 rare skins for tokens", "2026-11-20", "mazilla3", 5, "safe_user", "2026-11-21", 90,
    ["Offering three rare class skins. Send offers in the comments."], { sub: "Trading", ago: "2 hours ago" }),
  item(28, "Screenshot", "Perfect combo on Neon Pulse", "2026-11-20", "21notorious", 230, "pixel_pete", "2026-11-21", 35,
    ["A 612 combo, no misses."], { likes: 472 }),
  item(29, "Screenshot", "Boss phase three at 180 BPM", "2026-11-19", "MoeDe", 189, "gabe_fan", "2026-11-20", 215,
    ["The Bass Titan's last phase is brutal."], { likes: 294 }),
  item(30, "Screenshot", "Four-player squad on wave 40", "2026-11-18", "DimacAkiyama", 97, "modder99", "2026-11-19", 5,
    ["Blademaster, Gunslinger, Sentinel and Medic all alive."], { likes: 218 }),
  item(31, "Screenshot", "Duel arena at sunset", "2026-11-15", "indie_lover", 44, "padmaster", "2026-11-17", 25,
    ["Golden hour in the ranked duel arena."], { likes: 131 }),
  item(32, "Screenshot", "Victory screen, all S ranks", "2026-11-11", "padmaster", 21, "safe_user", "2026-11-13", 275,
    ["Squad clear with an S rank for everyone."], { likes: 87 }),
  item(33, "Video", "Neon Pulse perfect run, Blademaster solo", "2026-11-19", "PS_FatCat", 41, "pixel_pete", "2026-11-20", 160,
    ["A full perfect run of Neon Pulse with commentary on every dash."], { likes: 640 }),
  item(34, "Video", "Co-op boss rush highlights, week 46", "2026-11-14", "Andrei", 12, "gabe_fan", "2026-11-16", 340,
    ["Best moments from this week's co-op boss rush runs."], { likes: 205 }),
  item(35, "Video", "Patch 1.4 changes explained", "2026-11-09", "saigon_gamer", 18, "modder99", "2026-11-12", 100,
    ["Everything that changed in the last patch in five minutes."], { likes: 156 }),
  item(36, "Guide", "Co-op strategy for four-player squads", "2026-11-07", "modder99", 12, "gabe_fan", "2026-11-10", 130,
    ["Split roles before the wave starts and keep voice chat open.", "Call out boss telegraphs one beat early so everyone dodges together."], { rating: 4.5, cat: "Co-op" }),
  item(37, "Guide", "Class tier overview and playstyles", "2026-11-03", "indie_lover", 6, "pixel_pete", "2026-11-05", 300,
    ["A short look at the strengths of the Blademaster, Gunslinger, Sentinel and Medic.", "Pick the class that matches how you like to move, not just the one with the best damage."], { rating: 4.0, cat: "Classes" }),
  item(38, "Guide", "Secret rooms and hidden songs", "2026-10-28", "safe_user", 9, "saigon_gamer", "2026-10-30", 75,
    ["Every hidden room and unlockable song, with the steps to reach them.", "Check the map edges after finishing the main story."], { rating: 4.8, cat: "Secrets" }),
  item(39, "Guide", "Solo survival tips", "2026-10-25", "gabe_fan", 10, "padmaster", "2026-10-27", 190,
    ["How to survive waves alone without a healer.", "Use slides to avoid damage and save your parry for elite enemies."], { rating: 4.2, cat: "Solo" }),
  item(40, "Guide", "Enemy types and how to counter them", "2026-10-22", "modder99", 14, "pixel_pete", "2026-10-24", 15,
    ["A quick reference to every enemy type and its attack pattern.", "Most enemies attack on strong beats, so watch the rhythm to read them."], { rating: 4.6, cat: "Enemy" }),
  item(41, "Guide", "Boss fight breakdowns", "2026-10-18", "pixel_pete", 19, "safe_user", "2026-10-21", 335,
    ["Phase-by-phase strategies for every boss in the game.", "Learn each boss's telegraph before trying for a perfect run."], { rating: 4.9, cat: "Bosses" }),
  item(42, "Guide", "Gameplay basics for new players", "2026-10-15", "indie_lover", 24, "gabe_fan", "2026-10-19", 175,
    ["The basic loop: move, strike, parry and keep your combo alive.", "Start on the easy songs to learn how the judgement windows feel."], { rating: 4.3, cat: "Gameplay" }),
  item(43, "Guide", "Combo and multiplier mechanics", "2026-10-12", "padmaster", 11, "modder99", "2026-10-14", 55,
    ["How combos, multipliers and critical windows are calculated.", "A perfect hit on a strong beat counts double toward the multiplier."], { rating: 4.4, cat: "Mechanic" }),
  item(44, "Guide", "Arena maps and hazards", "2026-10-09", "saigon_gamer", 5, "pixel_pete", "2026-10-11", 105,
    ["A tour of the arenas with the hazards to watch for.", "Some hazards pulse with the beat and can be used against enemies."], { rating: 3.9, cat: "Map" }),
  item(45, "Guide", "Song list and difficulty ratings", "2026-10-05", "gabe_fan", 16, "indie_lover", "2026-10-08", 245,
    ["All songs sorted by BPM and difficulty.", "Great for picking your next practice track."], { rating: 4.6, cat: "Songs" }),
  item(46, "Guide", "Producer guide: making your first chart", "2026-10-01", "modder99", 13, "padmaster", "2026-10-04", 85,
    ["How to use the chart editor to place notes on beat.", "Start with a simple song and add enemy patterns once the rhythm feels right."], { rating: 4.7, cat: "Producer" }),
  item(47, "Guide", "The story so far: lore explained", "2026-09-28", "safe_user", 7, "gabe_fan", "2026-09-30", 310,
    ["A summary of the world and the story behind the beat-powered war.", "Contains spoilers for the main campaign."], { rating: 4.1, cat: "Story or Lore" }),
  item(48, "Guide", "Game modes: wave survival, boss rush and duel", "2026-09-24", "indie_lover", 9, "modder99", "2026-09-26", 145,
    ["What each game mode offers and which one to start with.", "Wave survival is best for learning, boss rush for practice and duel for competition."], { rating: 4.2, cat: "Gamemode" }),
  item(49, "Guide", "How to master the relentless combos of the Dancer", "2026-11-21", "pixel_pete", 14, "gabe_fan", "2026-11-21", 320,
    ["One of the strongest classes in the game in the hands of a master, yet unbelievably fragile if played wrong.",
     "The Dancer embodies the philosophy Critical Strike strives towards: a relentless and unyielding barrage of attacks that scales off the BPM and intensity of the song, and the player's accuracy, to chain multiple fast-moving, precise movement skills."],
    { rating: 4.8, cat: "Classes" }),
  item(50, "Guide", "Co-op: the importance of staying together", "2026-11-20", "modder99", 9, "safe_user", "2026-11-21", 140,
    ["A lot of special enemies in the game punish you for being singled out.",
     "Even if you play an assassin, it is important to keep some line of sight with your teammates so you are not caught out and killed. On higher difficulties, one mistake could spell disaster for the whole team."],
    { rating: 4.5, cat: "Co-op" }),
  item(51, "Guide", "Advanced movement tech", "2026-11-20", "gabe_fan", 11, "pixel_pete", "2026-11-21", 190,
    ["The game allows bunny hopping and air strafing, similar to other Source engine games."],
    { rating: 4.6, cat: "Movement" }),
  item(52, "Guide", "Secret track in Dark Carnival", "2026-11-19", "safe_user", 17, "modder99", "2026-11-21", 285,
    ["A hidden song called MopeMope is tucked away in the map Dark Carnival."],
    { rating: 4.9, cats: ["Gameplay", "Mechanic", "Map", "Secrets"], sections: [
      sec("Finding the jukebox", ["If you are savvy enough, you can find a jukebox in the backstage area of Dark Carnival, hidden away in one of the randomized rooms."]),
      sec("The music disc", ["Find the music disc that spawns somewhere on the main stage and insert it into the jukebox to play the hidden song."]),
      sec("The song: MopeMope", ["MopeMope has a unique rhythm of slow lulls and high intensity. It suits an erratic, almost insane playstyle that swings from extreme brutality to extreme pacifism."])] })
];

var EXTRA = {
  9: { sub: "General Discussions", ago: "7 minutes ago" }, 10: { sub: "Community Servers/Groups", ago: "46 minutes ago" },
  11: { sub: "Beta Feedback", ago: "2 hours ago" }
};
ITEMS.forEach(function (i) {
  i.likes = i.likes || i.replies * 37 + 20;
  Object.assign(i, EXTRA[i.id] || {});
});

var PAGE_SIZE = 8;
var SEED_COMMENTS = [
  { user: "gabe_fan", date: "2026-11-19", text: "Great post, thanks for sharing!" },
  { user: "pixel_pete", date: "2026-11-20", text: "Looking forward to more of this." }
];

/* Safe DOM helper: always uses textContent, so user input can never inject HTML. */
function el(tag, className, text) {
  var node = document.createElement(tag);
  if (className) { node.className = className; }
  if (text !== undefined) { node.textContent = text; }
  return node;
}
function gradient(hue) {
  return "linear-gradient(135deg, hsl(" + hue + ",55%,38%), hsl(" + ((hue + 40) % 360) + ",60%,20%))";
}
function hueOf(name) {
  var h = 0;
  for (var i = 0; i < name.length; i++) { h = (h * 31 + name.charCodeAt(i)) % 360; }
  return h;
}
function byDate(a, b) { return a.date < b.date ? 1 : (a.date > b.date ? -1 : 0); }
function ofKind(k) { return ITEMS.filter(function (i) { return i.kind === k; }).sort(byDate); }
function detailUrl(i) { return "/news/detail?id=" + i.id; }

function buildPost(name, role, date, paragraphs) {
  var post = el("div", "post");
  var author = el("div", "postAuthor");
  var av = el("div", "avatar", name.charAt(0).toUpperCase());
  av.style.background = gradient(hueOf(name));
  author.appendChild(av);
  var info = el("div");
  info.appendChild(el("span", "name", name));
  if (role) { info.appendChild(el("span", "role", role)); }
  author.appendChild(info);
  var body = el("div", "postBody");
  body.appendChild(el("div", "postDate", "Posted: " + date));
  paragraphs.forEach(function (t) { body.appendChild(el("p", "", t)); });
  post.appendChild(author);
  post.appendChild(body);
  return post;
}

/* ---------- Hub page (modeled on a Steam community hub) ---------- */
var state = { type: "All", q: "", page: 1, sort: "", sub: "", cat: "", browse: "", rec: "" };
var SUBS = ["General Discussions", "Community Servers/Groups", "Charts and Modding Help", "Beta Feedback", "Trading", "Events & Announcements"];
var GUIDE_CATS = ["Classes", "Co-op", "Solo", "Enemy", "Bosses", "Gameplay", "Mechanic", "Map", "Movement", "Songs", "Producer", "Modding or Config", "Story or Lore", "Gamemode", "Secrets", "Achievements"];
var MEDIA_SORTS = [["popular", "Most Popular (All Time)"], ["recent", "Most Recent"]];
var MAG = '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>';
var BUBBLE = '<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"><path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-4 6V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10c.55 0 1-.45 1-1z"/></svg>';

function link(over) {
  var s2 = Object.assign({}, state, { page: 1 }, over);
  var sp = new URLSearchParams();
  ["type", "q", "sort", "sub", "cat", "browse", "rec"].forEach(function (k) {
    if (s2[k] && !(k === "type" && s2[k] === "All")) { sp.set(k, s2[k]); }
  });
  if (s2.page > 1) { sp.set("page", s2.page); }
  var qs = sp.toString();
  return "/news" + (qs ? "?" + qs : "");
}
function tabUrl(k) { return "/news" + (k === "All" ? "" : "?type=" + k); }
function go(over) { window.location.href = link(over); }

function icon(svg, cls) {
  var sp = el("span", "ic" + (cls ? " " + cls : ""));
  sp.innerHTML = svg; /* static trusted markup, no user input */
  return sp;
}
function counts(a) {
  var c = el("div", "counts");
  var l = el("span", "likeCount");
  l.appendChild(icon(THUMB, "blue"));
  l.appendChild(document.createTextNode(" " + a.likes.toLocaleString("en-US")));
  var b = el("span", "cmtCount");
  b.appendChild(icon(BUBBLE));
  b.appendChild(document.createTextNode(" " + a.replies));
  c.appendChild(l);
  c.appendChild(b);
  return c;
}
function miniAvatar(name) {
  var av = el("div", "miniAv", name.charAt(0).toUpperCase());
  av.style.background = gradient(hueOf(name));
  return av;
}
function selectNode(opts, cur, cb, disabled) {
  var sel = el("select", "dsel");
  opts.forEach(function (o) {
    var op = el("option", "", o[1]);
    op.value = o[0];
    if (o[0] === cur) { op.selected = true; }
    sel.appendChild(op);
  });
  sel.addEventListener("change", function () { cb(sel.value); });
  if (disabled) { sel.disabled = true; }
  return sel;
}
function searchForm(placeholder, cls) {
  var f = el("form", "dsearch" + (cls ? " " + cls : ""));
  f.setAttribute("role", "search");
  var i = el("input");
  i.type = "search"; i.maxLength = 100; i.placeholder = placeholder; i.value = state.q;
  i.setAttribute("aria-label", placeholder);
  var b = el("button");
  b.type = "submit"; b.setAttribute("aria-label", "Search");
  b.innerHTML = MAG;
  f.appendChild(i);
  f.appendChild(b);
  f.addEventListener("submit", function (e) { e.preventDefault(); go({ q: i.value.trim().slice(0, 100) }); });
  return f;
}
function openPost(kind) {
  var sel = document.getElementById("postKind");
  sel.value = kind;
  sel.dispatchEvent(new Event("change"));
  if (window.jQuery) { window.jQuery("#postModal").modal("show"); }
}
function actionButton(label, kind) {
  var b = el("button", "btn btn-dlink", label);
  b.type = "button";
  b.addEventListener("click", function () { openPost(kind); });
  return b;
}
function mediaToolbar(sorts, extraLabel, extraKind) {
  var bar = el("div", "dbar");
  bar.appendChild(el("span", "dlabel", "SHOW"));
  bar.appendChild(selectNode(sorts, state.sort || sorts[0][0], function (v) { go({ sort: v }); }));
  bar.appendChild(searchForm("Search"));
  if (extraLabel) { bar.appendChild(actionButton(extraLabel, extraKind)); }
  return bar;
}
function sortItems(arr, mode) {
  var a = arr.slice();
  if (mode === "recent") { a.sort(byDate); }
  else if (mode === "helpful") { a.sort(function (x, y) { return (y.helpful || 0) - (x.helpful || 0); }); }
  else if (mode === "top") { a.sort(function (x, y) { return (y.rating || 0) - (x.rating || 0); }); }
  else { a.sort(function (x, y) { return y.likes - x.likes; }); }
  return a;
}
function matches(a) {
  var needle = state.q.toLowerCase();
  return !needle || (a.title + " " + a.excerpt).toLowerCase().indexOf(needle) !== -1;
}
function paginate(arr, size) {
  var pages = Math.max(1, Math.ceil(arr.length / size));
  if (state.page > pages) { state.page = pages; }
  var start = (state.page - 1) * size;
  return { pages: pages, start: start, total: arr.length, shown: arr.slice(start, start + size) };
}
function pagerNode(pages) {
  var box = el("span", "pager");
  if (pages < 2) { return box; }
  function add(label, target, off, cur) {
    var n;
    if (off || cur) { n = el("span", cur ? "current" : "off", label); }
    else { n = el("a", "", label); n.href = link({ page: target }); }
    box.appendChild(n);
  }
  add("<", state.page - 1, state.page === 1, false);
  for (var i = 1; i <= pages; i++) { add(String(i), i, false, i === state.page); }
  add(">", state.page + 1, state.page === pages, false);
  return box;
}
function hasCat(g, name) { return [].concat(g.cats || g.cat || []).indexOf(name) !== -1; }
function section(title, href, node) {
  var box = el("section", "hubSection");
  var head = el("div", "sectionHead");
  head.appendChild(el("h2", "", title));
  var more = el("a", "", "See all");
  more.href = href;
  head.appendChild(more);
  box.appendChild(head);
  box.appendChild(node);
  return box;
}
function emptyNode() { return el("div", "emptyRow", "Nothing here yet."); }

/* --- cards --- */
function mediaCard(a, h) {
  var card = el("div", "mediaCard");
  var img = el("a", "mediaImg");
  img.href = detailUrl(a);
  img.style.background = gradient(a.hue);
  img.style.height = h + "px";
  img.setAttribute("aria-label", a.title);
  if (a.kind === "Video") { img.appendChild(el("span", "playBtn", "\u25B6")); }
  card.appendChild(img);
  var act = el("div", "mediaActions");
  var btns = el("div", "miniBtns");
  var up = el("button", "", "\u25B2"), down = el("button", "", "\u25BC");
  up.type = "button"; down.type = "button";
  up.setAttribute("aria-label", "Like"); down.setAttribute("aria-label", "Dislike");
  up.addEventListener("click", function () { up.classList.toggle("on"); down.classList.remove("on"); });
  down.addEventListener("click", function () { down.classList.toggle("on"); up.classList.remove("on"); });
  btns.appendChild(up);
  btns.appendChild(down);
  act.appendChild(btns);
  act.appendChild(counts(a));
  card.appendChild(act);
  var cap = el("a", "mediaCaption", a.title);
  cap.href = detailUrl(a);
  card.appendChild(cap);
  var by = el("div", "mediaBy");
  by.appendChild(miniAvatar(a.author));
  var t = el("div");
  t.appendChild(el("div", "byName", a.author));
  t.appendChild(el("div", "byLink", "View " + PLURAL[a.kind].toLowerCase()));
  by.appendChild(t);
  card.appendChild(by);
  return card;
}
var PATTERN = [[6], [4, 2], [2, 4], [3, 3]];
function masonryNode(items) {
  var wrap = el("div", "masonry");
  if (!items.length) { wrap.appendChild(emptyNode()); }
  var i = 0, r = 0;
  while (i < items.length) {
    var pat = PATTERN[r % PATTERN.length];
    var n = Math.min(pat.length, items.length - i);
    var weights = n === pat.length ? pat : [6];
    var row = el("div", "mRow");
    for (var k = 0; k < n; k++) {
      var w = weights[k];
      var cell = el("div", "mCell");
      cell.style.flex = w + " 1 0";
      cell.appendChild(mediaCard(items[i + k], w === 6 ? 380 : (w === 3 ? 250 : 300)));
      row.appendChild(cell);
    }
    wrap.appendChild(row);
    i += n; r++;
  }
  return wrap;
}
function newsCard(a) {
  var c = el("div", "newsCard2");
  if (a.pinned) { c.appendChild(el("span", "ncPin", "PINNED")); }
  var t = el("a", "ncTitle", a.title);
  t.href = detailUrl(a);
  c.appendChild(t);
  c.appendChild(el("div", "ncDate", fmtDate(a.date)));
  c.appendChild(el("p", "ncText", a.body.join("\n\n")));
  var foot = el("div", "ncFoot");
  var g = el("div", "ncGame");
  g.appendChild(el("span", "gameIcon"));
  g.appendChild(document.createTextNode(GAME.name));
  foot.appendChild(g);
  foot.appendChild(counts(a));
  c.appendChild(foot);
  return c;
}
function reviewCard(a) {
  var c = el("div", "reviewTile");
  c.appendChild(el("div", "", a.helpful + " people found this review helpful"));
  c.appendChild(el("div", "", a.funny + (a.funny === 1 ? " person" : " people") + " found this review funny"));
  var v = el("div", "rtVerdict");
  var th = el("span", "thumb " + (a.recommended ? "up" : "down"));
  th.innerHTML = THUMB; /* static trusted markup */
  var vt = el("div");
  var vl = el("a", "verdictLink", a.recommended ? "Recommended" : "Not Recommended");
  vl.href = detailUrl(a);
  vt.appendChild(vl);
  vt.appendChild(el("div", "rtHours", a.hours.toFixed(1) + " hrs on record"));
  v.appendChild(th);
  v.appendChild(vt);
  c.appendChild(v);
  c.appendChild(el("div", "rtPosted", "Posted: " + fmtDate(a.date)));
  c.appendChild(el("div", "rtText", a.body.join("\n\n")));
  var foot = el("div", "rtFoot");
  var by = el("div", "mediaBy");
  by.appendChild(miniAvatar(a.author));
  by.appendChild(el("div", "byName", a.author));
  foot.appendChild(by);
  var cc = el("span", "cmtCount");
  cc.appendChild(icon(BUBBLE));
  cc.appendChild(document.createTextNode(" " + a.replies));
  foot.appendChild(cc);
  c.appendChild(foot);
  return c;
}
function guideRow(a) {
  var row = el("div", "guideRow");
  var th = el("div", "gThumb");
  th.style.background = gradient(a.hue);
  var body = el("div");
  var top = el("div", "gTop");
  var t = el("a", "gTitle", a.title);
  t.href = detailUrl(a);
  top.appendChild(t);
  var stars = el("span", "stars");
  for (var i = 0; i < 5; i++) { stars.appendChild(el("span", i < Math.round(a.rating || 0) ? "star on" : "star", "\u2605")); }
  top.appendChild(stars);
  body.appendChild(top);
  body.appendChild(el("div", "gBy", "by " + a.author));
  body.appendChild(el("div", "gDesc", a.excerpt));
  row.appendChild(th);
  row.appendChild(body);
  return row;
}

/* --- tab views --- */
function clip(t, n) { return t.length > n ? t.slice(0, n - 1).trim() + "\u2026" : t; }

function discussionCard(a) {
  var c = el("div", "fCard");
  var h = el("h3", "fTitle");
  var l = el("a", "", a.title);
  l.href = detailUrl(a);
  h.appendChild(l);
  c.appendChild(h);
  c.appendChild(el("div", "quoteBox", clip(a.body.join(" "), 230)));
  var who = el("div", "fWho");
  who.appendChild(miniAvatar(a.author));
  var t = el("div", "fWhoText");
  t.appendChild(el("div", "byName", a.author));
  t.appendChild(el("div", "byLink", a.ago || fmtDate(a.lastDate)));
  who.appendChild(t);
  var cc = el("span", "cmtCount");
  cc.appendChild(icon(BUBBLE));
  cc.appendChild(document.createTextNode(" " + a.replies));
  who.appendChild(cc);
  c.appendChild(who);
  c.appendChild(el("div", "spacer"));
  c.appendChild(el("div", "fFoot", a.sub || "General Discussions"));
  return c;
}

function guideCard(a) {
  var c = el("div", "fCard");
  c.appendChild(el("h3", "fTitle", "Guide"));
  var it = el("div", "featItem");
  var th = el("div", "gThumb");
  th.style.background = gradient(a.hue);
  var l = el("a", "", a.title);
  l.href = detailUrl(a);
  it.appendChild(th);
  it.appendChild(l);
  c.appendChild(it);
  c.appendChild(el("p", "fDesc", clip(a.excerpt, 170)));
  c.appendChild(el("div", "spacer"));
  var foot = el("div", "fStatRow");
  var stars = el("span", "stars");
  for (var i = 0; i < 5; i++) { stars.appendChild(el("span", i < Math.round(a.rating || 0) ? "star on" : "star", "\u2605")); }
  foot.appendChild(stars);
  foot.appendChild(counts(a));
  c.appendChild(foot);
  return c;
}

function feedCard(a, cols) {
  if (a.kind === "Discussion") { return discussionCard(a); }
  if (a.kind === "Guide") { return guideCard(a); }
  if (a.kind === "Review") { return reviewCard(a); }
  if (a.kind === "News") { return newsCard(a); }
  return mediaCard(a, cols === 2 ? 260 : 190);
}

/* Endless scroll: rows of 2 or 3 cards are appended whenever the sentinel nears the viewport. */
var FEED_PATTERN = [2, 3, 2, 2, 3, 2];
function startFeed(content, items) {
  var feed = el("div", "feed");
  var sentinel = el("div", "feedSentinel");
  var status = el("div", "feedStatus");
  content.appendChild(feed);
  content.appendChild(sentinel);
  content.appendChild(status);
  var idx = 0, row = 0, obs = null;

  function loadRows(n) {
    for (var r = 0; r < n && idx < items.length; r++) {
      var cols = FEED_PATTERN[row % FEED_PATTERN.length];
      row++;
      var take = Math.min(cols, items.length - idx);
      var line = el("div", "fRow");
      for (var k = 0; k < take; k++) {
        var cell = el("div", "fCell");
        cell.appendChild(feedCard(items[idx + k], take === 1 ? 2 : cols));
        line.appendChild(cell);
      }
      feed.appendChild(line);
      idx += take;
    }
    status.textContent = idx < items.length ? "Loading more\u2026"
      : (items.length ? "You have reached the end. There is no more content to show." : "Nothing to show yet.");
  }

  loadRows(3);
  if (idx >= items.length) { return; }
  if (!("IntersectionObserver" in window)) { loadRows(items.length); return; }
  obs = new IntersectionObserver(function (entries) {
    if (!entries[entries.length - 1].isIntersecting) { return; }
    loadRows(2);
    obs.unobserve(sentinel);
    if (idx < items.length) { obs.observe(sentinel); } else { obs.disconnect(); }
  }, { rootMargin: "300px" });
  obs.observe(sentinel);
}

function renderAll(content) {
  var banner = el("div", "gameBanner");
  var stats = el("div", "bannerStats");
  stats.appendChild(el("span", "green", GAME.inGame + " In-Game"));
  stats.appendChild(el("span", "", GAME.chat + " in Group Chat"));
  banner.appendChild(stats);
  var body = el("div", "bannerBody");
  var cap = el("div", "capsuleBig", GAME.name);
  cap.style.background = gradient(215);
  var txt = el("div", "bannerText");
  txt.appendChild(el("p", "", GAME.desc));
  var price = el("div", "priceRow");
  price.appendChild(el("span", "priceTag", GAME.price));
  var store = el("a", "btn btn-dlink", "Visit the Store Page");
  store.href = "/products";
  price.appendChild(store);
  price.appendChild(actionButton("Post to the hub", "Discussion"));
  txt.appendChild(price);
  body.appendChild(cap);
  body.appendChild(txt);
  banner.appendChild(body);
  content.appendChild(banner);

  /* 1. Latest news, directly under the store box */
  var latest = ofKind("News").slice(0, 3);
  var ng = el("div", "nGrid n3");
  latest.forEach(function (a) { ng.appendChild(newsCard(a)); });
  content.appendChild(section("Latest news", tabUrl("News"), ng));

  /* 2. Most popular content, endless scroll */
  content.appendChild(el("p", "popularNote", "Most popular community and official content for the past week."));
  content.appendChild(el("hr", "blueRule"));
  var skip = latest.map(function (a) { return a.id; });
  startFeed(content, sortItems(ITEMS.filter(function (i) { return skip.indexOf(i.id) === -1; }), "popular"));
}

function renderDiscussions(content) {
  var list = ITEMS.filter(function (i) { return i.kind === "Discussion" && (!state.sub || i.sub === state.sub) && matches(i); });
  list.sort(function (a, b) { return (b.pinned ? 1 : 0) - (a.pinned ? 1 : 0) || byDate(a, b); });
  var pg = paginate(list, PAGE_SIZE);
  var two = el("div", "twoCol");
  var main = el("div");
  var bar = el("div", "topicBar");
  bar.appendChild(el("span", "", pg.total ? "Showing " + (pg.start + 1) + "-" + (pg.start + pg.shown.length) + " of " + pg.total + " active topics" : "No topics found"));
  bar.appendChild(pagerNode(pg.pages));
  main.appendChild(bar);
  pg.shown.forEach(function (a) {
    var row = el("div", "dRow");
    row.appendChild(el("span", "dIcon", a.locked ? "\u26BF" : (a.pinned ? "\u2605" : "\u2709")));
    var mid = el("div", "dMid");
    var t = el("div");
    if (a.pinned) { t.appendChild(el("span", "pinned", "PINNED: ")); }
    var l = el("a", "dTitle", a.title);
    l.href = detailUrl(a);
    t.appendChild(l);
    mid.appendChild(t);
    mid.appendChild(el("div", "dAuthor", a.author));
    var rc = el("div", "dReplies");
    rc.appendChild(icon(BUBBLE));
    rc.appendChild(document.createTextNode(" " + a.replies));
    row.appendChild(mid);
    row.appendChild(el("div", "dWhen", a.ago || fmtDate(a.lastDate)));
    row.appendChild(rc);
    main.appendChild(row);
  });
  if (!pg.shown.length) { main.appendChild(emptyNode()); }
  var bottom = el("div", "topicBar");
  bottom.appendChild(el("span"));
  bottom.appendChild(pagerNode(pg.pages));
  main.appendChild(bottom);

  var side = el("aside", "sideCol");
  side.appendChild(searchForm("Search discussions", "big"));
  var start = el("button", "btn btn-start btn-block", "Start a New Discussion");
  start.type = "button";
  start.addEventListener("click", function () { openPost("Discussion"); });
  side.appendChild(start);
  var box = el("div", "subBox");
  var head = el("div", "subHead");
  head.appendChild(el("span", "", "SUB FORUMS"));
  head.appendChild(el("span", "", "THREADS"));
  box.appendChild(head);
  [""].concat(SUBS).forEach(function (name) {
    var n = ofKind("Discussion").filter(function (i) { return !name || i.sub === name; }).length;
    var a = el("a", "subItem" + (state.sub === name ? " active" : ""));
    a.href = link({ sub: name });
    a.appendChild(el("span", "", name || "All Discussions"));
    a.appendChild(el("span", "", String(n)));
    box.appendChild(a);
  });
  side.appendChild(box);
  two.appendChild(main);
  two.appendChild(side);
  content.appendChild(two);
}

function renderMedia(content, kind, extraLabel) {
  content.appendChild(mediaToolbar(MEDIA_SORTS, extraLabel, kind));
  var list = sortItems(ofKind(kind).filter(matches), state.sort || "popular");
  var pg = paginate(list, PAGE_SIZE);
  content.appendChild(masonryNode(pg.shown));
  var bar = el("div", "topicBar");
  bar.appendChild(el("span", "", pg.total + " " + PLURAL[kind].toLowerCase()));
  bar.appendChild(pagerNode(pg.pages));
  content.appendChild(bar);
}

function renderNews(content) {
  var bar = el("div", "dbar");
  bar.appendChild(el("span", "dlabel", "SHOW"));
  bar.appendChild(selectNode([["recent", "All News"], ["popular", "Most Popular"]], state.sort || "recent", function (v) { go({ sort: v }); }));
  bar.appendChild(searchForm("Search news"));
  content.appendChild(bar);
  var list = ofKind("News").filter(matches);
  list = state.sort === "popular" ? sortItems(list, "popular") : list.sort(function (a, b) { return (b.pinned ? 1 : 0) - (a.pinned ? 1 : 0) || byDate(a, b); });
  var pg = paginate(list, PAGE_SIZE);
  var grid = el("div", "nGrid");
  pg.shown.forEach(function (a) { grid.appendChild(newsCard(a)); });
  if (!pg.shown.length) { grid.appendChild(emptyNode()); }
  content.appendChild(grid);
  var foot = el("div", "topicBar");
  foot.appendChild(el("span", "", pg.total + " articles"));
  foot.appendChild(pagerNode(pg.pages));
  content.appendChild(foot);
}

function renderGuides(content) {
  var intro = el("div", "guideIntro");
  intro.appendChild(el("span", "", "Browse and rate player-created guides for this game. Or create your own and share your tips with the community."));
  var create = el("button", "btn btn-start", "Create your own");
  create.type = "button";
  create.addEventListener("click", function () { openPost("Guide"); });
  intro.appendChild(create);
  content.appendChild(intro);

  var home = !state.q && !state.cat && !state.browse;
  var guides = ofKind("Guide").filter(function (g) { return (!state.cat || hasCat(g, state.cat)) && matches(g); });
  var two = el("div", "twoCol guides");
  var main = el("div");
  if (home) {
    main.appendChild(el("h2", "gHeading", "Popular Categories (" + GUIDE_CATS.length + ")"));
    var tiles = el("div", "tileGrid");
    GUIDE_CATS.forEach(function (name) {
      var n = ofKind("Guide").filter(function (g) { return hasCat(g, name); }).length;
      var t = el("a", "catTile");
      t.href = link({ cat: name, browse: "popular" });
      var ic = el("div", "catIcon");
      ic.style.background = gradient(hueOf(name));
      var tx = el("div");
      tx.appendChild(el("div", "catName", name));
      tx.appendChild(el("div", "catCount", n + (n === 1 ? " Guide" : " Guides")));
      t.appendChild(ic);
      t.appendChild(tx);
      tiles.appendChild(t);
    });
    main.appendChild(tiles);
  }
  main.appendChild(el("h2", "gHeading", home ? "Popular Steam Guides" : (state.cat ? state.cat + " Guides" : "Guides")));
  var mode = state.browse === "recent" ? "recent" : (state.browse === "top" ? "top" : "popular");
  var sorted = sortItems(guides, mode);
  var pg = paginate(sorted, PAGE_SIZE);
  (home ? sorted.slice(0, 5) : pg.shown).forEach(function (g) { main.appendChild(guideRow(g)); });
  if (!sorted.length) { main.appendChild(emptyNode()); }
  if (!home) {
    var bar = el("div", "topicBar");
    bar.appendChild(el("span", "", pg.total + " guides"));
    bar.appendChild(pagerNode(pg.pages));
    main.appendChild(bar);
  }

  var side = el("aside", "sideCol");
  side.appendChild(searchForm("Search guides", "big"));
  var filt = el("div", "subBox");
  filt.appendChild(el("div", "subHead", "FILTER BY CATEGORY"));
  var fsel = selectNode([["", "All categories"]].concat(GUIDE_CATS.map(function (c) { return [c, c]; })), state.cat,
    function (v) { go({ cat: v, browse: v ? "popular" : "" }); });
  fsel.className = "dsel wide";
  filt.appendChild(fsel);
  side.appendChild(filt);
  var browse = el("div", "subBox");
  browse.appendChild(el("div", "subHead", "BROWSE BY:"));
  [["", "Home"], ["popular", "Most Popular"], ["top", "Top Rated All Time"], ["recent", "Most Recent"]].forEach(function (b) {
    var a = el("a", "subItem" + (state.browse === b[0] && !state.cat ? " active" : ""), b[1]);
    a.href = link({ browse: b[0], cat: "", q: "" });
    browse.appendChild(a);
  });
  side.appendChild(browse);
  var more = el("div", "subBox");
  more.appendChild(el("div", "subHead", "MORE ABOUT GUIDES"));
  more.appendChild(el("div", "promo", "Click here to learn more about Guides"));
  side.appendChild(more);
  two.appendChild(main);
  two.appendChild(side);
  content.appendChild(two);
}

function renderReviews(content) {
  var bar = el("div", "dbar");
  bar.appendChild(el("span", "dlabel", "SHOW"));
  bar.appendChild(selectNode([["helpful", "Most Helpful (All Time)"], ["recent", "Most Recent"]], state.sort || "helpful", function (v) { go({ sort: v }); }));
  bar.appendChild(selectNode([["", "All"], ["yes", "Recommended"], ["no", "Not Recommended"]], state.rec, function (v) { go({ rec: v }); }));
  bar.appendChild(el("span", "dlabel", "LANGUAGE"));
  bar.appendChild(selectNode([["en", "English"]], "en", function () {}, true));
  bar.appendChild(searchForm("Search reviews"));
  content.appendChild(bar);
  var list = ofKind("Review").filter(function (a) {
    return matches(a) && (!state.rec || (state.rec === "yes") === a.recommended);
  });
  var pg = paginate(sortItems(list, state.sort || "helpful"), PAGE_SIZE);
  var grid = el("div", "rvGrid");
  pg.shown.forEach(function (a) { grid.appendChild(reviewCard(a)); });
  if (!pg.shown.length) { grid.appendChild(emptyNode()); }
  content.appendChild(grid);
  var foot = el("div", "topicBar");
  foot.appendChild(el("span", "", pg.total + " reviews"));
  foot.appendChild(pagerNode(pg.pages));
  content.appendChild(foot);
}

function render() {
  var content = document.getElementById("hubContent");
  content.textContent = "";
  switch (state.type) {
    case "Discussion": renderDiscussions(content); break;
    case "Screenshot": renderMedia(content, "Screenshot", "Share a screenshot"); break;
    case "Artwork": renderMedia(content, "Artwork", "Upload artwork"); break;
    case "Video": renderMedia(content, "Video", "Upload a video"); break;
    case "News": renderNews(content); break;
    case "Guide": renderGuides(content); break;
    case "Review": renderReviews(content); break;
    default: renderAll(content);
  }
}

function initPostForm() {
  var form = document.getElementById("postForm");
  var err = document.getElementById("postError");
  var kindSel = document.getElementById("postKind");
  var rev = document.getElementById("reviewFields");
  kindSel.addEventListener("change", function () { rev.hidden = kindSel.value !== "Review"; });
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var kind = kindSel.value;
    var title = document.getElementById("postTitle").value.trim();
    var body = document.getElementById("postBody").value.trim();
    var file = document.getElementById("postImage").files[0];
    var extra = {};
    if (["Discussion", "Artwork", "Screenshot", "Video", "Guide", "Review"].indexOf(kind) === -1) { err.textContent = "Choose a valid type."; return; }
    if (title.length < 5 || title.length > 100) { err.textContent = "Title must be 5-100 characters."; return; }
    if (body.length < 10 || body.length > 1000) { err.textContent = "Description must be 10-1000 characters."; return; }
    if (file && (["image/png", "image/jpeg", "image/gif"].indexOf(file.type) === -1 || file.size > 2 * 1024 * 1024)) {
      err.textContent = "Image must be PNG, JPG or GIF and at most 2 MB."; return;
    }
    if (kind === "Review") {
      var hrs = parseFloat(document.getElementById("postHours").value);
      if (isNaN(hrs) || hrs < 0 || hrs > 99999) { err.textContent = "Hours played must be between 0 and 99999."; return; }
      extra = { recommended: document.getElementById("postRec").value === "yes", hours: hrs, helpful: 0, funny: 0, products: 1, reviews: 1 };
    }
    err.textContent = "";
    var nextId = ITEMS.reduce(function (m, i) { return Math.max(m, i.id); }, 0) + 1;
    var today = new Date().toISOString().slice(0, 10);
    ITEMS.push(item(nextId, kind, title, today, "You", 0, "You", today, hueOf(title), [body], extra));
    form.reset();
    rev.hidden = true;
    if (window.jQuery) { window.jQuery("#postModal").modal("hide"); }
    render();
  });
}

function initList() {
  var params = new URLSearchParams(window.location.search);
  var t = params.get("type");
  state.type = TABS.some(function (x) { return x.k === t; }) ? t : "All";
  state.q = (params.get("q") || "").trim().slice(0, 100);
  var p = parseInt(params.get("page"), 10);
  state.page = isNaN(p) || p < 1 ? 1 : p;
  ["sort", "sub", "cat", "browse", "rec"].forEach(function (k) { state[k] = (params.get(k) || "").slice(0, 40); });

  document.getElementById("gameTitle").textContent = GAME.name;
  document.title = "Steam Community :: " + GAME.name;
  var tabs = document.getElementById("catTabs");
  TABS.forEach(function (x) {
    var li = el("li", x.k === state.type ? "active" : "");
    var a = el("a", "", x.l);
    a.href = tabUrl(x.k);
    li.appendChild(a);
    tabs.appendChild(li);
  });
  initPostForm();
  render();

  var top = el("button", "backTop", "Back to top");
  top.type = "button";
  top.addEventListener("click", function () { window.scrollTo({ top: 0, behavior: "smooth" }); });
  document.body.appendChild(top);
  window.addEventListener("scroll", function () { top.classList.toggle("show", window.scrollY > 600); });
}

/* ---------- Review layout (modeled on a Steam review page) ---------- */
var THUMB = '<svg viewBox="0 0 24 24" width="44" height="44" aria-hidden="true"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/></svg>';

function renderReview(it, box) {
  var layout = el("div", "reviewLayout");
  var card = el("div", "reviewCard");

  var top = el("div", "reviewTop");
  var capsule = el("div", "capsule");
  capsule.style.background = gradient(it.hue);
  capsule.appendChild(el("span", "", GAME.name));
  var info = el("div");
  var verdict = el("div", "verdict");
  var thumb = el("span", "thumb " + (it.recommended ? "up" : "down"));
  thumb.innerHTML = THUMB; /* static trusted markup, no user input */
  verdict.appendChild(thumb);
  var vt = el("div");
  vt.appendChild(el("div", "verdictText " + (it.recommended ? "up" : "down"), it.recommended ? "Recommended" : "Not Recommended"));
  vt.appendChild(el("div", "mutedText", it.hours.toFixed(1) + " hrs on record"));
  verdict.appendChild(vt);
  info.appendChild(verdict);
  top.appendChild(capsule);
  top.appendChild(info);
  card.appendChild(top);

  var body = el("div", "reviewBody");
  body.appendChild(el("div", "postDate", "POSTED: " + it.date));
  it.body.forEach(function (t) { body.appendChild(el("p", "", t)); });
  card.appendChild(body);

  var help = el("div", "reviewHelp");
  help.appendChild(el("p", "", "Was this review helpful?"));
  var helpful = it.helpful, funny = it.funny;
  var hLine = el("div", "mutedText"), fLine = el("div", "mutedText");
  function counts() {
    hLine.textContent = helpful + " people found this review helpful";
    fLine.textContent = funny + " people found this review funny";
  }
  var buttons = [];
  [["Yes", function () { helpful++; }], ["No", function () {}], ["Funny", function () { funny++; }]].forEach(function (b) {
    var btn = el("button", "btn btn-steam", b[0]);
    btn.type = "button";
    btn.addEventListener("click", function () {
      b[1]();
      counts();
      buttons.forEach(function (x) { x.disabled = true; });
    });
    buttons.push(btn);
    help.appendChild(btn);
  });
  counts();
  help.appendChild(hLine);
  help.appendChild(fLine);
  card.appendChild(help);

  var side = el("aside", "reviewSide");
  var av = el("div", "avatar", it.author.charAt(0).toUpperCase());
  av.style.background = gradient(hueOf(it.author));
  side.appendChild(av);
  side.appendChild(el("span", "name", it.author));
  side.appendChild(el("div", "mutedText", it.products + " products in account"));
  side.appendChild(el("div", "mutedText", it.reviews + " reviews"));
  var all = el("a", "", "See all reviews");
  all.href = "/news?type=Review";
  side.appendChild(all);

  layout.appendChild(card);
  layout.appendChild(side);
  box.appendChild(layout);
}

/* ---------- Detail page (modeled on a Steam guide page) ---------- */
var PLURAL = { News: "News", Discussion: "Discussions", Artwork: "Artwork", Guide: "Guides", Review: "Reviews", Screenshot: "Screenshots", Video: "Videos" };
var MONTHS = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
var comments = [], cmtPage = 1, CMT_SIZE = 5, postAuthor = "", favs = 0;

function fmtDate(d) {
  var p = d.split("-");
  return parseInt(p[2], 10) + " " + MONTHS[parseInt(p[1], 10) - 1];
}

function initHead(it) {
  document.getElementById("gameTitle").textContent = GAME.name;
  var tabs = document.getElementById("detailTabs");
  TABS.forEach(function (x) {
    var li = el("li", x.k === it.kind ? "active" : "");
    var a = el("a", "", x.l);
    a.href = x.k === "All" ? "/news" : "/news?type=" + x.k;
    li.appendChild(a);
    tabs.appendChild(li);
  });
  var c = document.getElementById("crumbs");
  c.textContent = "";
  var g = el("a", "", GAME.name); g.href = "/news";
  var k = el("a", "", PLURAL[it.kind]); k.href = "/news?type=" + it.kind;
  c.appendChild(g);
  c.appendChild(document.createTextNode(" > "));
  c.appendChild(k);
  c.appendChild(document.createTextNode(" > " + (it.kind === "News" ? it.title : it.author + "'s " + PLURAL[it.kind])));
}

function actionBar() {
  var bar = el("div", "actionBar");
  var chips = el("div", "chips");
  var chipEls = [["\u2605", 4], ["\u2665", 3], ["\u2726", 2]].map(function (a) {
    var c = el("span", "chip", a[0] + " " + a[1]);
    chips.appendChild(c);
    return { node: c, sym: a[0], n: a[1] };
  });
  var row = el("div", "actionRow");
  function btn(label, onClick) {
    var b = el("button", "btn btn-act", label);
    b.type = "button";
    b.addEventListener("click", function () { onClick(b); });
    row.appendChild(b);
    return b;
  }
  var like, dislike;
  like = btn("\u25B2 Like", function (b) { b.classList.toggle("on"); dislike.classList.remove("on"); });
  dislike = btn("\u25BC Dislike", function (b) { b.classList.toggle("on"); like.classList.remove("on"); });
  btn("\u2605 Award", function (b) {
    chipEls[0].n++;
    chipEls[0].node.textContent = chipEls[0].sym + " " + chipEls[0].n;
    b.disabled = true;
  });
  btn("\u2665 Favorite", function (b) {
    var on = b.classList.toggle("on");
    favs += on ? 1 : -1;
    var f = document.getElementById("favCount");
    if (f) { f.textContent = favs; }
    b.textContent = on ? "\u2665 Favorited" : "\u2665 Favorite";
  });
  btn("\u2197 Share", function (b) {
    if (navigator.clipboard) { navigator.clipboard.writeText(window.location.href); }
    b.textContent = "Link copied";
    setTimeout(function () { b.textContent = "\u2197 Share"; }, 2000);
  });
  btn("\u2691 Report", function (b) { b.textContent = "Reported"; b.disabled = true; });
  bar.appendChild(chips);
  bar.appendChild(row);
  return bar;
}

function sideBox(title, className) {
  var b = el("div", "sideBlock" + (className ? " " + className : ""));
  if (title) { b.appendChild(el("h3", "", title)); }
  return b;
}
function kv(k, v, id) {
  var r = el("div", "kv");
  r.appendChild(el("span", "k", k));
  var val = el("span", "v", v);
  if (id) { val.id = id; }
  r.appendChild(val);
  return r;
}

function renderPost(it, box) {
  var isGuide = it.kind === "Guide";
  var isMedia = ["Artwork", "Screenshot", "Video"].indexOf(it.kind) !== -1;
  var hasDesc = isGuide || isMedia;
  favs = Math.round(it.replies * 1.5) + 3;

  var head = el("div", "postHead");
  head.id = "overview";
  var top = el("div", "postHeadTop");
  var thumb = el("div", "postThumb");
  thumb.style.background = gradient(it.hue);
  var info = el("div", "postHeadInfo");
  info.appendChild(el("h1", "postTitle", it.title));
  info.appendChild(el("div", "byline", "By " + it.author));
  top.appendChild(thumb);
  top.appendChild(info);
  if (it.rating) {
    var rate = el("div", "rateBox");
    var stars = el("div", "stars");
    for (var i = 0; i < 5; i++) { stars.appendChild(el("span", i < Math.round(it.rating) ? "star on" : "star", "\u2605")); }
    rate.appendChild(stars);
    rate.appendChild(el("div", "mutedText", (it.replies * 2 + 10) + " ratings"));
    top.appendChild(rate);
  }
  head.appendChild(top);
  if (hasDesc) { head.appendChild(el("p", "postDesc", it.body[0])); }
  box.appendChild(head);
  box.appendChild(actionBar());

  var grid = el("div", "postGrid");
  var main = el("div", "postMain");
  var side = el("aside", "postSide");

  if (isMedia) {
    var hero = el("div", "hero");
    hero.style.background = gradient(it.hue);
    hero.style.height = "320px";
    hero.style.position = "relative";
    if (it.kind === "Video") { hero.appendChild(el("span", "playBtn", "\u25B6")); }
    main.appendChild(hero);
  }
  var anchors = [{ label: "Overview", target: "#overview" }];
  if (it.sections) {
    it.sections.forEach(function (sc, n) {
      var wrap = el("section", "sec");
      wrap.id = "sec-" + n;
      wrap.appendChild(el("h2", "", sc.h));
      sc.p.forEach(function (t) { wrap.appendChild(el("p", "", t)); });
      if (sc.ul.length) {
        var ul = el("ul");
        sc.ul.forEach(function (t) { ul.appendChild(el("li", "", t)); });
        wrap.appendChild(ul);
      }
      main.appendChild(el("hr"));
      main.appendChild(wrap);
      anchors.push({ label: sc.h, target: "#sec-" + n });
    });
  } else {
    var wrap2 = el("section", "sec");
    (hasDesc ? it.body.slice(1) : it.body).forEach(function (t) { wrap2.appendChild(el("p", "", t)); });
    main.appendChild(wrap2);
  }
  anchors.push({ label: "Comments", target: "#comments" });
  main.appendChild(document.getElementById("comments"));

  var by = sideBox("Created by");
  var creator = el("div", "creator");
  var av = el("div", "cmtAvatar", it.author.charAt(0).toUpperCase());
  av.style.background = gradient(hueOf(it.author));
  var cn = el("div");
  cn.appendChild(el("div", "creatorName", it.author));
  cn.appendChild(el("div", "mutedText", hueOf(it.author) % 2 ? "Online" : "Offline"));
  creator.appendChild(av);
  creator.appendChild(cn);
  by.appendChild(creator);
  side.appendChild(by);

  var det = sideBox("");
  det.appendChild(kv("Category:", it.kind === "News" ? "Announcement" : (it.kind === "Guide" ? ([].concat(it.cats || it.cat || []).join(", ") || "Guide") : it.kind)));
  det.appendChild(kv("Language:", "English"));
  det.appendChild(kv("Posted", fmtDate(it.date)));
  det.appendChild(kv("Updated", fmtDate(it.lastDate)));
  side.appendChild(det);

  var stats = sideBox("");
  stats.appendChild(kv("Unique Visitors", String(it.replies * 14 + 120)));
  stats.appendChild(kv("Current Favorites", String(favs), "favCount"));
  side.appendChild(stats);

  if (isGuide) {
    var idx = sideBox("Guide index", "guideIndex");
    var ul2 = el("ul");
    var links = anchors.map(function (an) {
      var li = el("li");
      var a = el("a", "", an.label);
      a.href = an.target;
      li.appendChild(a);
      ul2.appendChild(li);
      return { li: li, target: an.target };
    });
    idx.appendChild(ul2);
    side.appendChild(idx);
    function spy() {
      var cur = 0;
      links.forEach(function (l, n) {
        var t = document.querySelector(l.target);
        if (t && t.getBoundingClientRect().top < 140) { cur = n; }
      });
      links.forEach(function (l, n) { l.li.className = n === cur ? "active" : ""; });
    }
    window.addEventListener("scroll", spy);
    spy();
  }

  grid.appendChild(main);
  grid.appendChild(side);
  box.appendChild(grid);
}

/* ---------- Comments (shared by every post type) ---------- */
function renderComments() {
  var list = document.getElementById("commentList");
  list.textContent = "";
  var pages = Math.max(1, Math.ceil(comments.length / CMT_SIZE));
  if (cmtPage > pages) { cmtPage = pages; }
  comments.slice((cmtPage - 1) * CMT_SIZE, cmtPage * CMT_SIZE).forEach(function (c) {
    var row = el("div", "cmt");
    var av = el("div", "cmtAvatar", c.user.charAt(0).toUpperCase());
    av.style.background = gradient(hueOf(c.user));
    var body = el("div", "cmtBody");
    var name = el("div", "cmtName", c.user);
    if (c.user === postAuthor) { name.appendChild(el("span", "authorTag", "[author]")); }
    body.appendChild(name);
    body.appendChild(el("div", "cmtDate", fmtDate(c.date)));
    body.appendChild(el("div", "cmtText", c.text));
    row.appendChild(av);
    row.appendChild(body);
    list.appendChild(row);
  });
  document.getElementById("commentCount").textContent = comments.length;
  var pg = document.getElementById("commentPager");
  pg.textContent = "";
  if (pages < 2) { return; }
  function add(label, target, off, cur) {
    var n;
    if (off || cur) { n = el("span", cur ? "current" : "off", label); }
    else {
      n = el("a", "", label);
      n.href = "#comments";
      n.addEventListener("click", function (e) { e.preventDefault(); cmtPage = target; renderComments(); });
    }
    pg.appendChild(n);
  }
  add("<", cmtPage - 1, cmtPage === 1, false);
  for (var i = 1; i <= pages; i++) { add(String(i), i, false, i === cmtPage); }
  add(">", cmtPage + 1, cmtPage === pages, false);
}

function addComment(c) {
  comments.push(c);
  cmtPage = Math.ceil(comments.length / CMT_SIZE);
  renderComments();
}

function initComments(it) {
  var users = ["gabe_fan", "pixel_pete", "modder99", "saigon_gamer", "indie_lover", "padmaster"];
  var texts = ["Great post, thanks for sharing!", "Looking forward to more of this.", "This helped a lot, thank you.",
               "Does this still work after the latest patch?", "Bookmarked for later.", "Nicely written!", "Thanks everyone for the feedback!"];
  postAuthor = it.author;
  texts.forEach(function (t, n) {
    comments.push({ user: n === texts.length - 1 ? it.author : users[n], date: it.lastDate, text: t });
  });
  renderComments();

  var form = document.getElementById("commentForm");
  var text = document.getElementById("commentText");
  var error = document.getElementById("commentError");
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    var value = text.value.trim();
    if (value.length < 3) { error.textContent = "Comment must be at least 3 characters."; return; }
    if (value.length > 500) { error.textContent = "Comment must be 500 characters or fewer."; return; }
    error.textContent = "";
    addComment({ user: "You", date: new Date().toISOString().slice(0, 10), text: value });
    text.value = "";
  });
}

function initArticle() {
  var id = parseInt(new URLSearchParams(window.location.search).get("id"), 10);
  var it = ITEMS.filter(function (a) { return a.id === id; })[0];
  var box = document.getElementById("article");
  box.textContent = "";
  document.getElementById("gameTitle").textContent = GAME.name;

  if (!it) {
    box.appendChild(el("h1", "topicHeading", "Post not found"));
    box.appendChild(el("p", "mutedText", "The post you are looking for does not exist."));
    document.getElementById("comments").hidden = true;
    return;
  }
  document.title = "Steam Community :: " + it.title;
  initHead(it);
  if (it.kind === "Review") { renderReview(it, box); } else { renderPost(it, box); }
  initComments(it);
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("hubContent")) { initList(); }
  if (document.getElementById("article")) { initArticle(); }
});

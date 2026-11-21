"use strict";

/* Sample data for the frontend-only submission.
   Later replace with data from the News and Post models (MySQL). */
var GAME = { name: "Starfall Odyssey" };
var TABS = [
  { k: "All", l: "All" }, { k: "Discussion", l: "Discussions" }, { k: "Artwork", l: "Artwork" },
  { k: "Guide", l: "Guides" }, { k: "Review", l: "Reviews" }, { k: "News", l: "News" }
];
var KIND_LABEL = { News: "NEWS", Discussion: "DISCUSSION", Artwork: "ARTWORK", Guide: "GUIDE", Review: "REVIEW" };

function item(id, kind, title, date, author, replies, lastBy, lastDate, hue, body, extra) {
  var o = { id: id, kind: kind, title: title, date: date, author: author, replies: replies,
            lastBy: lastBy, lastDate: lastDate, hue: hue, body: body, excerpt: body[0] };
  return Object.assign(o, extra || {});
}

function sec(h, p, ul) { return { h: h, p: p, ul: ul || [] }; }

var ITEMS = [
  item(1, "News", "Autumn Sale starts this week", "2026-11-18", "Steam Team", 12, "pixel_pete", "2026-11-20", 200,
    ["Thousands of games are discounted for the next seven days.", "Wishlisted games will show a banner on the store page so you never miss a deal.", "The sale ends next Thursday at 10 AM Pacific."]),
  item(2, "News", "Library update adds custom collections", "2026-11-12", "Steam Team", 5, "gabe_fan", "2026-11-15", 260,
    ["Organize your games with dynamic and manual collections.", "Dynamic collections update automatically based on filters such as tags and play time."]),
  item(3, "News", "Community workshop highlights", "2026-11-05", "Community Team", 3, "modder99", "2026-11-09", 140,
    ["The best player-made content from the past month.", "This month's picks include new maps, skins and quality-of-life mods."]),
  item(4, "News", "Scheduled maintenance on Tuesday", "2026-10-29", "Support Team", 0, "Support Team", "2026-10-29", 30,
    ["Store and community will be unavailable for a short time.", "Maintenance should take about 30 minutes. Downloads resume automatically afterwards."], { pinned: true }),
  item(5, "News", "Improved download speeds in Asia", "2026-10-21", "Steam Team", 7, "saigon_gamer", "2026-10-25", 320,
    ["New regional servers reduce latency for players in Southeast Asia.", "Most players will see faster downloads with no action required."]),
  item(6, "News", "Privacy and security tips", "2026-10-07", "Support Team", 4, "safe_user", "2026-10-10", 0,
    ["Keep your account safe with these simple steps.", "Use a unique password and enable two-factor authentication."]),
  item(7, "News", "Controller support expanded", "2026-09-30", "Steam Team", 1, "padmaster", "2026-10-01", 230,
    ["More controllers are now recognized out of the box.", "Open Settings, then Controller, to customize button layouts."]),
  item(9, "Discussion", "Best loadout for the new ranked season?", "2026-11-20", "pixel_pete", 18, "gabe_fan", "2026-11-21", 210,
    ["Curious what everyone is running this season.", "I have been trying the heavy shield build and it feels strong. Thoughts?"]),
  item(10, "Discussion", "Looking for co-op partners (SEA servers)", "2026-11-19", "saigon_gamer", 9, "modder99", "2026-11-20", 150,
    ["Looking for chill players to run missions in the evenings.", "I am on the Southeast Asia servers, voice chat optional."]),
  item(11, "Discussion", "Game crashes on launch after the update", "2026-11-17", "safe_user", 6, "Support Team", "2026-11-18", 10,
    ["Since the last patch the game crashes right after the logo.", "Verifying files did not help. Any fixes?"]),
  item(12, "Artwork", "Nebula over the outer rim", "2026-11-20", "indie_lover", 14, "pixel_pete", "2026-11-21", 280,
    ["Digital painting of the Outer Rim nebula, made in about eight hours."]),
  item(13, "Artwork", "Pixel art starship", "2026-11-18", "padmaster", 6, "gabe_fan", "2026-11-19", 190,
    ["A 64x64 pixel art version of the starter ship."]),
  item(14, "Artwork", "Concept sketch: desert planet", "2026-11-14", "modder99", 3, "indie_lover", "2026-11-15", 40,
    ["Early concept sketch for a desert planet map idea."]),
  item(15, "Artwork", "Captain portrait", "2026-11-10", "gabe_fan", 11, "pixel_pete", "2026-11-12", 330,
    ["Character study of the captain in charcoal style."]),
  item(16, "Guide", "Beginner's guide to trading", "2026-11-16", "pixel_pete", 22, "safe_user", "2026-11-20", 120, 
    ["Everything you need to start earning credits quickly.", "Buy low at mining stations, sell high at the core worlds, and always keep a fuel reserve."], { rating: 4.7, sections: [
      sec("Getting started", ["Start at a mining station and buy ore while prices are low.", "Keep at least 20% of your fuel in reserve."], ["Check prices before every jump", "Never carry cargo you cannot defend"]),
      sec("Best trade routes", ["The Outer Rim to Core World loop pays best for beginners."], ["Ore: mining stations to core refineries", "Food: farm moons to hub stations"]),
      sec("Common mistakes", ["Overloading the cargo hold slows your ship and burns more fuel."])] }),
  item(17, "Guide", "Complete achievement guide", "2026-11-08", "gabe_fan", 15, "modder99", "2026-11-13", 60,
    ["A checklist of every achievement with tips for the hard ones.", "Start with the story achievements, then move on to the collection ones."], { rating: 4.4, sections: [
      sec("Story achievements", ["Finish the campaign once on any difficulty to unlock all of these."], ["First Contact", "Rim Runner", "The Last Signal"]),
      sec("Collection achievements", ["Scan every planet type and visit each station at least once."]),
      sec("Hard challenges", ["Use the shield build for the no-damage run and save before each boss."])] }),
  item(18, "Guide", "Best controller settings", "2026-11-01", "padmaster", 8, "saigon_gamer", "2026-11-05", 250,
    ["My recommended deadzone and sensitivity settings for gamepads."], { rating: 4.1, sections: [
      sec("Deadzone", ["Set the inner deadzone to 8% to remove drift without losing precision."]),
      sec("Sensitivity", ["Use a response curve of 1.4 on the right stick for aiming."], ["Turn off acceleration", "Lower sensitivity in menus"]),
      sec("Button layout", ["Put boost on the left bumper and the scanner on the d-pad."])] }),
  item(19, "Review", "Best space sim I have played in years", "2026-11-19", "pixel_pete", 7, "gabe_fan", "2026-11-20", 205,
    ["Flying through the Outer Rim for the first time gave me chills. The trading loop is relaxing, combat is tight, and every system feels hand-made.", "After 140 hours I am still finding new routes and secrets. Easily worth the full price."],
    { recommended: true, hours: 142.5, helpful: 48, funny: 6, products: 187, reviews: 23 }),
  item(20, "Review", "Good ideas, but the late game drags", "2026-11-15", "modder99", 4, "saigon_gamer", "2026-11-17", 35,
    ["The first 30 hours are fantastic. Later the grind for upgrades slows everything down.", "Still recommended, especially if you enjoy modding. The workshop support is great."],
    { recommended: true, hours: 63.2, helpful: 19, funny: 2, products: 92, reviews: 11 }),
  item(21, "Review", "Too many crashes since the last patch", "2026-11-12", "safe_user", 9, "Support Team", "2026-11-14", 5,
    ["I wanted to like this, but the game crashes every 20 minutes after the latest update.", "I will change this review once the stability issues are fixed."],
    { recommended: false, hours: 8.4, helpful: 31, funny: 1, products: 41, reviews: 3 })
];

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
function detailUrl(i) { return "detail.php?id=" + i.id; }

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

/* ---------- Hub page ---------- */
var state = { type: "All", q: "", page: 1 };

function url(p, type) {
  var sp = new URLSearchParams();
  if (state.q) { sp.set("q", state.q); }
  if (type && type !== "All") { sp.set("type", type); }
  if (p > 1) { sp.set("page", p); }
  var s = sp.toString();
  return "index.php" + (s ? "?" + s : "");
}

function rowsNode(items) {
  var list = el("div", "topicList");
  var head = el("div", "topicHead");
  head.appendChild(el("div", "", "Topic"));
  head.appendChild(el("div", "colReplies", "Replies"));
  head.appendChild(el("div", "colLast", "Last post"));
  list.appendChild(head);
  if (!items.length) { list.appendChild(el("div", "emptyRow", "Nothing here yet.")); }
  items.forEach(function (a) {
    var row = el("div", "topicRow");
    var main = el("div", "topicMain");
    var icon = el("div", "topicIcon");
    icon.style.background = gradient(a.hue);
    var text = el("div");
    var line = el("div");
    if (a.pinned) { line.appendChild(el("span", "badge badge-pin", "PINNED")); }
    line.appendChild(el("span", "badge badge-kind", KIND_LABEL[a.kind]));
    var link = el("a", "topicTitle", a.title);
    link.href = detailUrl(a);
    line.appendChild(link);
    text.appendChild(line);
    var sub = "by " + a.author + " on " + a.date;
    if (a.kind === "Review") { sub = (a.recommended ? "Recommended" : "Not recommended") + " | " + a.hours.toFixed(1) + " hrs | " + sub; }
    text.appendChild(el("div", "topicSub", sub));
    main.appendChild(icon);
    main.appendChild(text);
    var last = el("div", "colLast");
    last.appendChild(el("div", "", a.lastBy));
    last.appendChild(el("div", "when", a.lastDate));
    row.appendChild(main);
    row.appendChild(el("div", "colReplies", String(a.replies)));
    row.appendChild(last);
    list.appendChild(row);
  });
  return list;
}

function gridNode(items) {
  var grid = el("div", "artGrid");
  if (!items.length) { grid.appendChild(el("div", "emptyRow", "Nothing here yet.")); }
  items.forEach(function (a) {
    var card = el("a", "artCard");
    card.href = detailUrl(a);
    var thumb = el("div", "artThumb");
    thumb.style.background = gradient(a.hue);
    var meta = el("div", "artMeta", a.title);
    meta.appendChild(el("small", "", "by " + a.author + " | " + a.replies + " replies"));
    card.appendChild(thumb);
    card.appendChild(meta);
    grid.appendChild(card);
  });
  return grid;
}

function section(title, type, node) {
  var box = el("section", "hubSection");
  var head = el("div", "sectionHead");
  head.appendChild(el("h2", "", title));
  var more = el("a", "", "See all");
  more.href = url(1, type);
  head.appendChild(more);
  box.appendChild(head);
  box.appendChild(node);
  return box;
}

function featuredNode() {
  var news = ofKind("News").slice(0, 3);
  var wrap = el("div", "featured");
  if (!news.length) { return wrap; }
  var big = el("a", "featBig");
  big.href = detailUrl(news[0]);
  big.style.background = "linear-gradient(to top, rgba(0,0,0,.8), rgba(0,0,0,.05)), " + gradient(news[0].hue);
  big.appendChild(el("h3", "", news[0].title));
  big.appendChild(el("p", "", news[0].excerpt));
  wrap.appendChild(big);
  var side = el("div", "featSide");
  news.slice(1).forEach(function (n) {
    var a = el("a", "featSmall", n.title);
    a.href = detailUrl(n);
    a.appendChild(el("small", "", n.date));
    side.appendChild(a);
  });
  wrap.appendChild(side);
  return wrap;
}

function setPagers(totalPages) {
  ["pagerTop", "pagerBottom"].forEach(function (id) {
    var box = document.getElementById(id);
    if (!box) { return; }
    box.textContent = "";
    if (totalPages <= 1) { return; }
    function add(label, target, off, current) {
      var n;
      if (off || current) { n = el("span", current ? "current" : "off", label); }
      else { n = el("a", "", label); n.href = url(target, state.type); }
      box.appendChild(n);
    }
    add("<", state.page - 1, state.page === 1, false);
    for (var i = 1; i <= totalPages; i++) { add(String(i), i, false, i === state.page); }
    add(">", state.page + 1, state.page === totalPages, false);
  });
}

function render() {
  var content = document.getElementById("hubContent");
  var info = document.getElementById("resultInfo");
  content.textContent = "";

  if (state.type === "All" && !state.q) {
    info.textContent = "";
    setPagers(1);
    content.appendChild(section("Latest news", "News", featuredNode()));
    content.appendChild(section("Discussions", "Discussion", rowsNode(ofKind("Discussion").slice(0, 4))));
    content.appendChild(section("Artwork", "Artwork", gridNode(ofKind("Artwork").slice(0, 4))));
    content.appendChild(section("Guides", "Guide", rowsNode(ofKind("Guide").slice(0, 3))));
    content.appendChild(section("Reviews", "Review", rowsNode(ofKind("Review").slice(0, 3))));
    return;
  }

  var needle = state.q.toLowerCase();
  var results = ITEMS.filter(function (i) {
    var okKind = state.type === "All" || i.kind === state.type;
    var okText = !needle || (i.title + " " + i.excerpt).toLowerCase().indexOf(needle) !== -1;
    return okKind && okText;
  }).sort(function (a, b) { return (b.pinned ? 1 : 0) - (a.pinned ? 1 : 0) || byDate(a, b); });

  var totalPages = Math.max(1, Math.ceil(results.length / PAGE_SIZE));
  if (state.page > totalPages) { state.page = totalPages; }
  var start = (state.page - 1) * PAGE_SIZE;
  var shown = results.slice(start, start + PAGE_SIZE);
  info.textContent = results.length
    ? "Showing " + (start + 1) + "-" + (start + shown.length) + " of " + results.length
    : "0 results";
  content.appendChild(state.type === "Artwork" ? gridNode(shown) : rowsNode(shown));
  setPagers(totalPages);
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
    if (["Discussion", "Artwork", "Guide", "Review"].indexOf(kind) === -1) { err.textContent = "Choose a valid type."; return; }
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
  state.q = (params.get("q") || "").trim().slice(0, 100);
  var t = params.get("type");
  state.type = TABS.some(function (x) { return x.k === t; }) ? t : "All";
  var p = parseInt(params.get("page"), 10);
  state.page = isNaN(p) || p < 1 ? 1 : p;

  document.getElementById("gameName").textContent = GAME.name;
  document.getElementById("q").value = state.q;
  document.getElementById("type").value = state.type === "All" ? "" : state.type;

  var tabs = document.getElementById("catTabs");
  TABS.forEach(function (x) {
    var li = el("li", x.k === state.type ? "active" : "");
    var a = el("a", "", x.l);
    a.href = url(1, x.k);
    li.appendChild(a);
    tabs.appendChild(li);
  });
  initPostForm();
  render();
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
  all.href = "index.php?type=Review";
  side.appendChild(all);

  layout.appendChild(card);
  layout.appendChild(side);
  box.appendChild(layout);
}

/* ---------- Detail page (modeled on a Steam guide page) ---------- */
var PLURAL = { News: "News", Discussion: "Discussions", Artwork: "Artwork", Guide: "Guides", Review: "Reviews" };
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
    a.href = x.k === "All" ? "index.php" : "index.php?type=" + x.k;
    li.appendChild(a);
    tabs.appendChild(li);
  });
  var c = document.getElementById("crumbs");
  c.textContent = "";
  var g = el("a", "", GAME.name); g.href = "index.php";
  var k = el("a", "", PLURAL[it.kind]); k.href = "index.php?type=" + it.kind;
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
  var hasDesc = isGuide || it.kind === "Artwork";
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

  if (it.kind === "Artwork") {
    var hero = el("div", "hero");
    hero.style.background = gradient(it.hue);
    hero.style.height = "320px";
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
  det.appendChild(kv("Category:", it.kind === "News" ? "Announcement" : it.kind));
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
  var page = document.body.getAttribute("data-page");
  if (page === "list") { initList(); }
  if (page === "article") { initArticle(); }
});

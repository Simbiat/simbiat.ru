"use strict";
!function () {
    "use strict";
    var e = function (e) { if (null === e)
        return "null"; if (void 0 === e)
        return "undefined"; var t = typeof e; return "object" === t && (Array.prototype.isPrototypeOf(e) || e.constructor && "Array" === e.constructor.name) ? "array" : "object" === t && (String.prototype.isPrototypeOf(e) || e.constructor && "String" === e.constructor.name) ? "string" : t; }, t = function (e) { return { eq: e }; }, n = t((function (e, t) { return e === t; })), o = function (e) { return t((function (t, n) { if (t.length !== n.length)
        return !1; for (var o = t.length, r = 0; r < o; r++)
        if (!e.eq(t[r], n[r]))
            return !1; return !0; })); }, r = function (e) { return t((function (r, s) { var a = Object.keys(r), i = Object.keys(s); if (!function (e, n) { return function (e, n) { return t((function (t, o) { return e.eq(n(t), n(o)); })); }(o(e), (function (e) { return function (e, t) { return Array.prototype.slice.call(e).sort(t); }(e, n); })); }(n).eq(a, i))
        return !1; for (var l = a.length, d = 0; d < l; d++) {
        var c = a[d];
        if (!e.eq(r[c], s[c]))
            return !1;
    } return !0; })); }, s = t((function (t, n) { if (t === n)
        return !0; var a = e(t); return a === e(n) && (function (e) { return -1 !== ["undefined", "boolean", "number", "string", "function", "xml", "null"].indexOf(e); }(a) ? t === n : "array" === a ? o(s).eq(t, n) : "object" === a && r(s).eq(t, n)); }));
    const a = Object.getPrototypeOf, i = (e, t, n) => { var o; return !!n(e, t.prototype) || (null === (o = e.constructor) || void 0 === o ? void 0 : o.name) === t.name; }, l = e => t => (e => { const t = typeof e; return null === e ? "null" : "object" === t && Array.isArray(e) ? "array" : "object" === t && i(e, String, ((e, t) => t.isPrototypeOf(e))) ? "string" : t; })(t) === e, d = e => t => typeof t === e, c = e => t => e === t, u = (e, t) => f(e) && i(e, t, ((e, t) => a(e) === t)), m = l("string"), f = l("object"), g = e => u(e, Object), p = l("array"), h = c(null), b = d("boolean"), v = c(void 0), y = e => null == e, C = e => !y(e), w = d("function"), E = d("number"), x = (e, t) => { if (p(e)) {
        for (let n = 0, o = e.length; n < o; ++n)
            if (!t(e[n]))
                return !1;
        return !0;
    } return !1; }, _ = () => { }, S = (e, t) => (...n) => e(t.apply(null, n)), k = (e, t) => n => e(t(n)), N = e => () => e, R = e => e, A = (e, t) => e === t;
    function T(e, ...t) { return (...n) => { const o = t.concat(n); return e.apply(null, o); }; }
    const O = e => t => !e(t), B = e => () => { throw new Error(e); }, P = e => e(), D = e => { e(); }, L = N(!1), M = N(!0);
    class I {
        constructor(e, t) { this.tag = e, this.value = t; }
        static some(e) { return new I(!0, e); }
        static none() { return I.singletonNone; }
        fold(e, t) { return this.tag ? t(this.value) : e(); }
        isSome() { return this.tag; }
        isNone() { return !this.tag; }
        map(e) { return this.tag ? I.some(e(this.value)) : I.none(); }
        bind(e) { return this.tag ? e(this.value) : I.none(); }
        exists(e) { return this.tag && e(this.value); }
        forall(e) { return !this.tag || e(this.value); }
        filter(e) { return !this.tag || e(this.value) ? this : I.none(); }
        getOr(e) { return this.tag ? this.value : e; }
        or(e) { return this.tag ? this : e; }
        getOrThunk(e) { return this.tag ? this.value : e(); }
        orThunk(e) { return this.tag ? this : e(); }
        getOrDie(e) { if (this.tag)
            return this.value; throw new Error(null != e ? e : "Called getOrDie on None"); }
        static from(e) { return C(e) ? I.some(e) : I.none(); }
        getOrNull() { return this.tag ? this.value : null; }
        getOrUndefined() { return this.value; }
        each(e) { this.tag && e(this.value); }
        toArray() { return this.tag ? [this.value] : []; }
        toString() { return this.tag ? `some(${this.value})` : "none()"; }
    }
    I.singletonNone = new I(!1);
    const F = Array.prototype.slice, U = Array.prototype.indexOf, z = Array.prototype.push, j = (e, t) => U.call(e, t), H = (e, t) => j(e, t) > -1, $ = (e, t) => { for (let n = 0, o = e.length; n < o; n++)
        if (t(e[n], n))
            return !0; return !1; }, V = (e, t) => { const n = e.length, o = new Array(n); for (let r = 0; r < n; r++) {
        const n = e[r];
        o[r] = t(n, r);
    } return o; }, q = (e, t) => { for (let n = 0, o = e.length; n < o; n++)
        t(e[n], n); }, W = (e, t) => { for (let n = e.length - 1; n >= 0; n--)
        t(e[n], n); }, K = (e, t) => { const n = [], o = []; for (let r = 0, s = e.length; r < s; r++) {
        const s = e[r];
        (t(s, r) ? n : o).push(s);
    } return { pass: n, fail: o }; }, Y = (e, t) => { const n = []; for (let o = 0, r = e.length; o < r; o++) {
        const r = e[o];
        t(r, o) && n.push(r);
    } return n; }, X = (e, t, n) => (W(e, ((e, o) => { n = t(n, e, o); })), n), G = (e, t, n) => (q(e, ((e, o) => { n = t(n, e, o); })), n), Z = (e, t, n) => { for (let o = 0, r = e.length; o < r; o++) {
        const r = e[o];
        if (t(r, o))
            return I.some(r);
        if (n(r, o))
            break;
    } return I.none(); }, Q = (e, t) => Z(e, t, L), J = (e, t) => { for (let n = 0, o = e.length; n < o; n++)
        if (t(e[n], n))
            return I.some(n); return I.none(); }, ee = e => { const t = []; for (let n = 0, o = e.length; n < o; ++n) {
        if (!p(e[n]))
            throw new Error("Arr.flatten item " + n + " was not an array, input: " + e);
        z.apply(t, e[n]);
    } return t; }, te = (e, t) => ee(V(e, t)), ne = (e, t) => { for (let n = 0, o = e.length; n < o; ++n)
        if (!0 !== t(e[n], n))
            return !1; return !0; }, oe = e => { const t = F.call(e, 0); return t.reverse(), t; }, re = (e, t) => Y(e, (e => !H(t, e))), se = (e, t) => { const n = {}; for (let o = 0, r = e.length; o < r; o++) {
        const r = e[o];
        n[String(r)] = t(r, o);
    } return n; }, ae = (e, t) => { const n = F.call(e, 0); return n.sort(t), n; }, ie = (e, t) => t >= 0 && t < e.length ? I.some(e[t]) : I.none(), le = e => ie(e, 0), de = e => ie(e, e.length - 1), ce = w(Array.from) ? Array.from : e => F.call(e), ue = (e, t) => { for (let n = 0; n < e.length; n++) {
        const o = t(e[n], n);
        if (o.isSome())
            return o;
    } return I.none(); }, me = (e, t) => { const n = [], o = w(t) ? e => $(n, (n => t(n, e))) : e => H(n, e); for (let t = 0, r = e.length; t < r; t++) {
        const r = e[t];
        o(r) || n.push(r);
    } return n; }, fe = Object.keys, ge = Object.hasOwnProperty, pe = (e, t) => { const n = fe(e); for (let o = 0, r = n.length; o < r; o++) {
        const r = n[o];
        t(e[r], r);
    } }, he = (e, t) => be(e, ((e, n) => ({ k: n, v: t(e, n) }))), be = (e, t) => { const n = {}; return pe(e, ((e, o) => { const r = t(e, o); n[r.k] = r.v; })), n; }, ve = e => (t, n) => { e[n] = t; }, ye = (e, t, n, o) => { pe(e, ((e, r) => { (t(e, r) ? n : o)(e, r); })); }, Ce = (e, t) => { const n = {}; return ye(e, t, ve(n), _), n; }, we = (e, t) => { const n = []; return pe(e, ((e, o) => { n.push(t(e, o)); })), n; }, Ee = e => we(e, R), xe = (e, t) => _e(e, t) ? I.from(e[t]) : I.none(), _e = (e, t) => ge.call(e, t), Se = (e, t) => _e(e, t) && void 0 !== e[t] && null !== e[t], ke = e => { if (!p(e))
        throw new Error("cases must be an array"); if (0 === e.length)
        throw new Error("there must be at least one case"); const t = [], n = {}; return q(e, ((o, r) => { const s = fe(o); if (1 !== s.length)
        throw new Error("one and only one name per case"); const a = s[0], i = o[a]; if (void 0 !== n[a])
        throw new Error("duplicate key detected:" + a); if ("cata" === a)
        throw new Error("cannot have a case named cata (sorry)"); if (!p(i))
        throw new Error("case arguments must be an array"); t.push(a), n[a] = (...n) => { const o = n.length; if (o !== i.length)
        throw new Error("Wrong number of arguments to case " + a + ". Expected " + i.length + " (" + i + "), got " + o); return { fold: (...t) => { if (t.length !== e.length)
            throw new Error("Wrong number of arguments to fold. Expected " + e.length + ", got " + t.length); return t[r].apply(null, n); }, match: e => { const o = fe(e); if (t.length !== o.length)
            throw new Error("Wrong number of arguments to match. Expected: " + t.join(",") + "\nActual: " + o.join(",")); if (!ne(t, (e => H(o, e))))
            throw new Error("Not all branches were specified when using match. Specified: " + o.join(", ") + "\nRequired: " + t.join(", ")); return e[a].apply(null, n); }, log: e => { console.log(e, { constructors: t, constructor: a, params: n }); } }; }; })), n; }, Ne = e => { let t = e; return { get: () => t, set: e => { t = e; } }; }, Re = e => { const t = t => t(e), n = N(e), o = () => r, r = { tag: !0, inner: e, fold: (t, n) => n(e), isValue: M, isError: L, map: t => Te.value(t(e)), mapError: o, bind: t, exists: t, forall: t, getOr: n, or: o, getOrThunk: n, orThunk: o, getOrDie: n, each: t => { t(e); }, toOptional: () => I.some(e) }; return r; }, Ae = e => { const t = () => n, n = { tag: !1, inner: e, fold: (t, n) => t(e), isValue: L, isError: M, map: t, mapError: t => Te.error(t(e)), bind: t, exists: L, forall: M, getOr: R, or: R, getOrThunk: P, orThunk: P, getOrDie: B(String(e)), each: _, toOptional: I.none }; return n; }, Te = { value: Re, error: Ae, fromOption: (e, t) => e.fold((() => Ae(t)), Re) }, Oe = "undefined" != typeof window ? window : Function("return this;")(), Be = () => window.crypto.getRandomValues(new Uint32Array(1))[0] / 4294967295;
    let Pe = 0;
    const De = e => { const t = (new Date).getTime(), n = Math.floor(1e9 * Be()); return Pe++, e + "_" + n + Pe + String(t); }, Le = e => (...t) => { if (0 === t.length)
        throw new Error("Can't merge zero objects"); const n = {}; for (let o = 0; o < t.length; o++) {
        const r = t[o];
        for (const t in r)
            _e(r, t) && (n[t] = e(n[t], r[t]));
    } return n; }, Me = Le(((e, t) => g(e) && g(t) ? Me(e, t) : t)), Ie = Le(((e, t) => t)), Fe = (e, t, n = A) => e.exists((e => n(e, t))), Ue = (e, t, n = A) => ze(e, t, n).getOr(e.isNone() && t.isNone()), ze = (e, t, n) => e.isSome() && t.isSome() ? I.some(n(e.getOrDie(), t.getOrDie())) : I.none(), je = (e, t) => e ? I.some(t) : I.none(), He = (e, t) => ((e, t) => { let n = null != t ? t : Oe; for (let t = 0; t < e.length && null != n; ++t)
        n = n[e[t]]; return n; })(e.split("."), t);
    ke([{ bothErrors: ["error1", "error2"] }, { firstError: ["error1", "value2"] }, { secondError: ["value1", "error2"] }, { bothValues: ["value1", "value2"] }]);
    const $e = e => { const t = Ne(I.none()), n = () => t.get().each((e => clearInterval(e))); return { clear: () => { n(), t.set(I.none()); }, isSet: () => t.get().isSome(), get: () => t.get(), set: o => { n(), t.set(I.some(setInterval(o, e))); } }; }, Ve = () => { const e = (e => { const t = Ne(I.none()), n = () => t.get().each(e); return { clear: () => { n(), t.set(I.none()); }, isSet: () => t.get().isSome(), get: () => t.get(), set: e => { n(), t.set(I.some(e)); } }; })(_); return { ...e, on: t => e.get().each(t) }; }, qe = (e, t, n) => "" === t || e.length >= t.length && e.substr(n, n + t.length) === t, We = (e, t) => Ye(e, t) ? ((e, t) => e.substring(t))(e, t.length) : e, Ke = (e, t, n = 0, o) => { const r = e.indexOf(t, n); return -1 !== r && (!!v(o) || r + t.length <= o); }, Ye = (e, t) => qe(e, t, 0), Xe = (e, t) => qe(e, t, e.length - t.length), Ge = e => t => t.replace(e, ""), Ze = Ge(/^\s+|\s+$/g), Qe = Ge(/^\s+/g), Je = Ge(/\s+$/g), et = e => e.length > 0, tt = e => !et(e), nt = (e, t = 10) => { const n = parseInt(e, t); return isNaN(n) ? I.none() : I.some(n); }, ot = (e, t) => { let n = null; return { cancel: () => { h(n) || (clearTimeout(n), n = null); }, throttle: (...o) => { h(n) && (n = setTimeout((() => { n = null, e.apply(null, o); }), t)); } }; }, rt = (e, t) => { let n = null; const o = () => { h(n) || (clearTimeout(n), n = null); }; return { cancel: o, throttle: (...r) => { o(), n = setTimeout((() => { n = null, e.apply(null, r); }), t); } }; }, st = e => { let t, n = !1; return (...o) => (n || (n = !0, t = e.apply(null, o)), t); }, at = "\ufeff", it = "\xa0", lt = e => e === at, dt = e => { const t = {}; return q(e, (e => { t[e] = {}; })), fe(t); }, ct = e => void 0 !== e.length, ut = Array.isArray, mt = (e, t, n) => { if (!e)
        return !1; if (n = n || e, ct(e)) {
        for (let o = 0, r = e.length; o < r; o++)
            if (!1 === t.call(n, e[o], o, e))
                return !1;
    }
    else
        for (const o in e)
            if (_e(e, o) && !1 === t.call(n, e[o], o, e))
                return !1; return !0; }, ft = (e, t) => { const n = []; return mt(e, ((o, r) => { n.push(t(o, r, e)); })), n; }, gt = (e, t) => { const n = []; return mt(e, ((o, r) => { t && !t(o, r, e) || n.push(o); })), n; }, pt = (e, t, n, o) => { let r = v(n) ? e[0] : n; for (let n = 0; n < e.length; n++)
        r = t.call(o, r, e[n], n); return r; }, ht = (e, t, n) => { for (let o = 0, r = e.length; o < r; o++)
        if (t.call(n, e[o], o, e))
            return o; return -1; }, bt = e => e[e.length - 1], vt = () => yt(0, 0), yt = (e, t) => ({ major: e, minor: t }), Ct = { nu: yt, detect: (e, t) => { const n = String(t).toLowerCase(); return 0 === e.length ? vt() : ((e, t) => { const n = ((e, t) => { for (let n = 0; n < e.length; n++) {
            const o = e[n];
            if (o.test(t))
                return o;
        } })(e, t); if (!n)
            return { major: 0, minor: 0 }; const o = e => Number(t.replace(n, "$" + e)); return yt(o(1), o(2)); })(e, n); }, unknown: vt }, wt = (e, t) => { const n = String(t).toLowerCase(); return Q(e, (e => e.search(n))); }, Et = /.*?version\/\ ?([0-9]+)\.([0-9]+).*/, xt = e => t => Ke(t, e), _t = [{ name: "Edge", versionRegexes: [/.*?edge\/ ?([0-9]+)\.([0-9]+)$/], search: e => Ke(e, "edge/") && Ke(e, "chrome") && Ke(e, "safari") && Ke(e, "applewebkit") }, { name: "Chromium", brand: "Chromium", versionRegexes: [/.*?chrome\/([0-9]+)\.([0-9]+).*/, Et], search: e => Ke(e, "chrome") && !Ke(e, "chromeframe") }, { name: "IE", versionRegexes: [/.*?msie\ ?([0-9]+)\.([0-9]+).*/, /.*?rv:([0-9]+)\.([0-9]+).*/], search: e => Ke(e, "msie") || Ke(e, "trident") }, { name: "Opera", versionRegexes: [Et, /.*?opera\/([0-9]+)\.([0-9]+).*/], search: xt("opera") }, { name: "Firefox", versionRegexes: [/.*?firefox\/\ ?([0-9]+)\.([0-9]+).*/], search: xt("firefox") }, { name: "Safari", versionRegexes: [Et, /.*?cpu os ([0-9]+)_([0-9]+).*/], search: e => (Ke(e, "safari") || Ke(e, "mobile/")) && Ke(e, "applewebkit") }], St = [{ name: "Windows", search: xt("win"), versionRegexes: [/.*?windows\ nt\ ?([0-9]+)\.([0-9]+).*/] }, { name: "iOS", search: e => Ke(e, "iphone") || Ke(e, "ipad"), versionRegexes: [/.*?version\/\ ?([0-9]+)\.([0-9]+).*/, /.*cpu os ([0-9]+)_([0-9]+).*/, /.*cpu iphone os ([0-9]+)_([0-9]+).*/] }, { name: "Android", search: xt("android"), versionRegexes: [/.*?android\ ?([0-9]+)\.([0-9]+).*/] }, { name: "macOS", search: xt("mac os x"), versionRegexes: [/.*?mac\ os\ x\ ?([0-9]+)_([0-9]+).*/] }, { name: "Linux", search: xt("linux"), versionRegexes: [] }, { name: "Solaris", search: xt("sunos"), versionRegexes: [] }, { name: "FreeBSD", search: xt("freebsd"), versionRegexes: [] }, { name: "ChromeOS", search: xt("cros"), versionRegexes: [/.*?chrome\/([0-9]+)\.([0-9]+).*/] }], kt = { browsers: N(_t), oses: N(St) }, Nt = "Edge", Rt = "Chromium", At = "Opera", Tt = "Firefox", Ot = "Safari", Bt = e => { const t = e.current, n = e.version, o = e => () => t === e; return { current: t, version: n, isEdge: o(Nt), isChromium: o(Rt), isIE: o("IE"), isOpera: o(At), isFirefox: o(Tt), isSafari: o(Ot) }; }, Pt = () => Bt({ current: void 0, version: Ct.unknown() }), Dt = Bt, Lt = (N(Nt), N(Rt), N("IE"), N(At), N(Tt), N(Ot), "Windows"), Mt = "Android", It = "Linux", Ft = "macOS", Ut = "Solaris", zt = "FreeBSD", jt = "ChromeOS", Ht = e => { const t = e.current, n = e.version, o = e => () => t === e; return { current: t, version: n, isWindows: o(Lt), isiOS: o("iOS"), isAndroid: o(Mt), isMacOS: o(Ft), isLinux: o(It), isSolaris: o(Ut), isFreeBSD: o(zt), isChromeOS: o(jt) }; }, $t = () => Ht({ current: void 0, version: Ct.unknown() }), Vt = Ht, qt = (N(Lt), N("iOS"), N(Mt), N(It), N(Ft), N(Ut), N(zt), N(jt), e => window.matchMedia(e).matches);
    let Wt = st((() => ((e, t, n) => { const o = kt.browsers(), r = kt.oses(), s = t.bind((e => ((e, t) => ue(t.brands, (t => { const n = t.brand.toLowerCase(); return Q(e, (e => { var t; return n === (null === (t = e.brand) || void 0 === t ? void 0 : t.toLowerCase()); })).map((e => ({ current: e.name, version: Ct.nu(parseInt(t.version, 10), 0) }))); })))(o, e))).orThunk((() => ((e, t) => wt(e, t).map((e => { const n = Ct.detect(e.versionRegexes, t); return { current: e.name, version: n }; })))(o, e))).fold(Pt, Dt), a = ((e, t) => wt(e, t).map((e => { const n = Ct.detect(e.versionRegexes, t); return { current: e.name, version: n }; })))(r, e).fold($t, Vt), i = ((e, t, n, o) => { const r = e.isiOS() && !0 === /ipad/i.test(n), s = e.isiOS() && !r, a = e.isiOS() || e.isAndroid(), i = a || o("(pointer:coarse)"), l = r || !s && a && o("(min-device-width:768px)"), d = s || a && !l, c = t.isSafari() && e.isiOS() && !1 === /safari/i.test(n), u = !d && !l && !c; return { isiPad: N(r), isiPhone: N(s), isTablet: N(l), isPhone: N(d), isTouch: N(i), isAndroid: e.isAndroid, isiOS: e.isiOS, isWebView: N(c), isDesktop: N(u) }; })(a, s, e, n); return { browser: s, os: a, deviceType: i }; })(window.navigator.userAgent, I.from(window.navigator.userAgentData), qt)));
    const Kt = () => Wt(), Yt = Object.getPrototypeOf, Xt = e => { const t = He("ownerDocument.defaultView", e); return f(e) && ((e => ((e, t) => { const n = ((e, t) => He(e, t))(e, t); if (null == n)
        throw new Error(e + " not available on this browser"); return n; })("HTMLElement", e))(t).prototype.isPrototypeOf(e) || /^HTML\w*Element$/.test(Yt(e).constructor.name)); }, Gt = window.navigator.userAgent, Zt = Kt(), Qt = Zt.browser, Jt = Zt.os, en = Zt.deviceType, tn = -1 !== Gt.indexOf("Windows Phone"), nn = { transparentSrc: "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7", documentMode: Qt.isIE() ? document.documentMode || 7 : 10, cacheSuffix: null, container: null, canHaveCSP: !Qt.isIE(), windowsPhone: tn, browser: { current: Qt.current, version: Qt.version, isChromium: Qt.isChromium, isEdge: Qt.isEdge, isFirefox: Qt.isFirefox, isIE: Qt.isIE, isOpera: Qt.isOpera, isSafari: Qt.isSafari }, os: { current: Jt.current, version: Jt.version, isAndroid: Jt.isAndroid, isChromeOS: Jt.isChromeOS, isFreeBSD: Jt.isFreeBSD, isiOS: Jt.isiOS, isLinux: Jt.isLinux, isMacOS: Jt.isMacOS, isSolaris: Jt.isSolaris, isWindows: Jt.isWindows }, deviceType: { isDesktop: en.isDesktop, isiPad: en.isiPad, isiPhone: en.isiPhone, isPhone: en.isPhone, isTablet: en.isTablet, isTouch: en.isTouch, isWebView: en.isWebView } }, on = /^\s*|\s*$/g, rn = e => y(e) ? "" : ("" + e).replace(on, ""), sn = function (e, t, n, o) { o = o || this, e && (n && (e = e[n]), mt(e, ((e, r) => !1 !== t.call(o, e, r, n) && (sn(e, t, n, o), !0)))); }, an = { trim: rn, isArray: ut, is: (e, t) => t ? !("array" !== t || !ut(e)) || typeof e === t : void 0 !== e, toArray: e => { if (ut(e))
            return e; {
            const t = [];
            for (let n = 0, o = e.length; n < o; n++)
                t[n] = e[n];
            return t;
        } }, makeMap: (e, t, n = {}) => { const o = m(e) ? e.split(t || ",") : e || []; let r = o.length; for (; r--;)
            n[o[r]] = {}; return n; }, each: mt, map: ft, grep: gt, inArray: (e, t) => { if (e)
            for (let n = 0, o = e.length; n < o; n++)
                if (e[n] === t)
                    return n; return -1; }, hasOwn: _e, extend: (e, ...t) => { for (let n = 0; n < t.length; n++) {
            const o = t[n];
            for (const t in o)
                if (_e(o, t)) {
                    const n = o[t];
                    void 0 !== n && (e[t] = n);
                }
        } return e; }, walk: sn, resolve: (e, t = window) => { const n = e.split("."); for (let e = 0, o = n.length; e < o && (t = t[n[e]]); e++)
            ; return t; }, explode: (e, t) => p(e) ? e : "" === e ? [] : ft(e.split(t || ","), rn), _addCacheSuffix: e => { const t = nn.cacheSuffix; return t && (e += (-1 === e.indexOf("?") ? "?" : "&") + t), e; } }, ln = e => { if (null == e)
        throw new Error("Node cannot be null or undefined"); return { dom: e }; }, dn = (e, t) => { const n = (t || document).createElement("div"); if (n.innerHTML = e, !n.hasChildNodes() || n.childNodes.length > 1) {
        const t = "HTML does not have a single root node";
        throw console.error(t, e), new Error(t);
    } return ln(n.childNodes[0]); }, cn = (e, t) => { const n = (t || document).createElement(e); return ln(n); }, un = (e, t) => { const n = (t || document).createTextNode(e); return ln(n); }, mn = ln, fn = (e, t, n) => I.from(e.dom.elementFromPoint(t, n)).map(ln), gn = (e, t, n) => { const o = e.document.createRange(); var r; return r = o, t.fold((e => { r.setStartBefore(e.dom); }), ((e, t) => { r.setStart(e.dom, t); }), (e => { r.setStartAfter(e.dom); })), ((e, t) => { t.fold((t => { e.setEndBefore(t.dom); }), ((t, n) => { e.setEnd(t.dom, n); }), (t => { e.setEndAfter(t.dom); })); })(o, n), o; }, pn = (e, t, n, o, r) => { const s = e.document.createRange(); return s.setStart(t.dom, n), s.setEnd(o.dom, r), s; }, hn = ke([{ ltr: ["start", "soffset", "finish", "foffset"] }, { rtl: ["start", "soffset", "finish", "foffset"] }]), bn = (e, t, n) => t(mn(n.startContainer), n.startOffset, mn(n.endContainer), n.endOffset);
    hn.ltr, hn.rtl;
    const vn = (e, t) => { const n = e.dom; if (1 !== n.nodeType)
        return !1; {
        const e = n;
        if (void 0 !== e.matches)
            return e.matches(t);
        if (void 0 !== e.msMatchesSelector)
            return e.msMatchesSelector(t);
        if (void 0 !== e.webkitMatchesSelector)
            return e.webkitMatchesSelector(t);
        if (void 0 !== e.mozMatchesSelector)
            return e.mozMatchesSelector(t);
        throw new Error("Browser lacks native selectors");
    } }, yn = e => 1 !== e.nodeType && 9 !== e.nodeType && 11 !== e.nodeType || 0 === e.childElementCount, Cn = (e, t) => e.dom === t.dom, wn = (e, t) => { const n = e.dom, o = t.dom; return n !== o && n.contains(o); }, En = (e, t) => { const n = [], o = e => (n.push(e), t(e)); let r = t(e); do {
        r = r.bind(o);
    } while (r.isSome()); return n; }, xn = e => e.dom.nodeName.toLowerCase(), _n = e => e.dom.nodeType, Sn = e => t => _n(t) === e, kn = e => Nn(e) && Xt(e.dom), Nn = Sn(1), Rn = Sn(3), An = Sn(9), Tn = Sn(11), On = e => t => Nn(t) && xn(t) === e, Bn = e => mn(e.dom.ownerDocument), Pn = e => An(e) ? e : Bn(e), Dn = e => mn(Pn(e).dom.defaultView), Ln = e => I.from(e.dom.parentNode).map(mn), Mn = e => I.from(e.dom.parentElement).map(mn), In = (e, t) => { const n = w(t) ? t : L; let o = e.dom; const r = []; for (; null !== o.parentNode && void 0 !== o.parentNode;) {
        const e = o.parentNode, t = mn(e);
        if (r.push(t), !0 === n(t))
            break;
        o = e;
    } return r; }, Fn = e => I.from(e.dom.previousSibling).map(mn), Un = e => I.from(e.dom.nextSibling).map(mn), zn = e => oe(En(e, Fn)), jn = e => En(e, Un), Hn = e => V(e.dom.childNodes, mn), $n = (e, t) => { const n = e.dom.childNodes; return I.from(n[t]).map(mn); }, Vn = e => $n(e, 0), qn = e => $n(e, e.dom.childNodes.length - 1), Wn = e => e.dom.childNodes.length, Kn = e => Tn(e) && C(e.dom.host), Yn = e => mn(e.dom.getRootNode()), Xn = e => Kn(e) ? e : (e => { const t = e.dom.head; if (null == t)
        throw new Error("Head is not available yet"); return mn(t); })(Pn(e)), Gn = e => mn(e.dom.host), Zn = e => { if (C(e.target)) {
        const t = mn(e.target);
        if (Nn(t) && Qn(t) && e.composed && e.composedPath) {
            const t = e.composedPath();
            if (t)
                return le(t);
        }
    } return I.from(e.target); }, Qn = e => C(e.dom.shadowRoot), Jn = (e, t, n, o) => ((e, t, n, o, r) => { const s = ((e, t) => n => { e(n) && t((e => { const t = mn(Zn(e).getOr(e.target)), n = () => e.stopPropagation(), o = () => e.preventDefault(), r = S(o, n); return ((e, t, n, o, r, s, a) => ({ target: e, x: t, y: n, stop: o, prevent: r, kill: s, raw: a }))(t, e.clientX, e.clientY, n, o, r, e); })(n)); })(n, o); return e.dom.addEventListener(t, s, r), { unbind: T(eo, e, t, s, r) }; })(e, t, n, o, !1), eo = (e, t, n, o) => { e.dom.removeEventListener(t, n, o); }, to = M, no = () => mn(document), oo = (e, t = !1) => e.dom.focus({ preventScroll: t }), ro = e => { const t = Yn(e).dom; return e.dom === t.activeElement; }, so = (e = no()) => I.from(e.dom.activeElement).map(mn), ao = (e, t) => { Ln(e).each((n => { n.dom.insertBefore(t.dom, e.dom); })); }, io = (e, t) => { Un(e).fold((() => { Ln(e).each((e => { co(e, t); })); }), (e => { ao(e, t); })); }, lo = (e, t) => { Vn(e).fold((() => { co(e, t); }), (n => { e.dom.insertBefore(t.dom, n.dom); })); }, co = (e, t) => { e.dom.appendChild(t.dom); }, uo = (e, t) => { ao(e, t), co(t, e); }, mo = (e, t) => { q(t, (t => { co(e, t); })); }, fo = (e, t, n) => { if (!(m(n) || b(n) || E(n)))
        throw console.error("Invalid call to Attribute.set. Key ", t, ":: Value ", n, ":: Element ", e), new Error("Attribute value was not simple"); e.setAttribute(t, n + ""); }, go = (e, t, n) => { fo(e.dom, t, n); }, po = (e, t) => { const n = e.dom; pe(t, ((e, t) => { fo(n, t, e); })); }, ho = (e, t) => { const n = e.dom.getAttribute(t); return null === n ? void 0 : n; }, bo = (e, t) => I.from(ho(e, t)), vo = (e, t) => { const n = e.dom; return !(!n || !n.hasAttribute) && n.hasAttribute(t); }, yo = (e, t) => { e.dom.removeAttribute(t); }, Co = e => G(e.dom.attributes, ((e, t) => (e[t.name] = t.value, e)), {}), wo = e => { e.dom.textContent = "", q(Hn(e), (e => { Eo(e); })); }, Eo = e => { const t = e.dom; null !== t.parentNode && t.parentNode.removeChild(t); }, xo = e => { const t = Hn(e); var n, o; t.length > 0 && (n = e, q(o = t, ((e, t) => { const r = 0 === t ? n : o[t - 1]; io(r, e); }))), Eo(e); }, _o = (e, t) => mn(e.dom.cloneNode(t)), So = e => _o(e, !1), ko = e => _o(e, !0), No = e => V(e, mn), Ro = e => e.dom.innerHTML, Ao = (e, t) => { const n = Bn(e).dom, o = mn(n.createDocumentFragment()), r = ((e, t) => { const n = (t || document).createElement("div"); return n.innerHTML = e, Hn(mn(n)); })(t, n); mo(o, r), wo(e), co(e, o); }, To = e => void 0 !== e.style && w(e.style.getPropertyValue), Oo = e => { const t = Rn(e) ? e.dom.parentNode : e.dom; if (null == t || null === t.ownerDocument)
        return !1; const n = t.ownerDocument; return (e => { const t = Yn(e); return Kn(t) ? I.some(t) : I.none(); })(mn(t)).fold((() => n.body.contains(t)), k(Oo, Gn)); }, Bo = (e, t, n) => { if (!m(n))
        throw console.error("Invalid call to CSS.set. Property ", t, ":: Value ", n, ":: Element ", e), new Error("CSS value must be a string: " + n); To(e) && e.style.setProperty(t, n); }, Po = (e, t, n) => { const o = e.dom; Bo(o, t, n); }, Do = (e, t) => { const n = e.dom; pe(t, ((e, t) => { Bo(n, t, e); })); }, Lo = (e, t) => { const n = e.dom, o = window.getComputedStyle(n).getPropertyValue(t); return "" !== o || Oo(e) ? o : Mo(n, t); }, Mo = (e, t) => To(e) ? e.style.getPropertyValue(t) : "", Io = (e, t) => { const n = e.dom, o = Mo(n, t); return I.from(o).filter((e => e.length > 0)); }, Fo = e => { const t = {}, n = e.dom; if (To(n))
        for (let e = 0; e < n.style.length; e++) {
            const o = n.style.item(e);
            t[o] = n.style[o];
        } return t; }, Uo = (e, t) => { ((e, t) => { To(e) && e.style.removeProperty(t); })(e.dom, t), Fe(bo(e, "style").map(Ze), "") && yo(e, "style"); }, zo = (e => { const t = t => { const n = (e => { const t = e.dom; return Oo(e) ? t.getBoundingClientRect().height : t.offsetHeight; })(t); if (n <= 0 || null === n) {
        const n = Lo(t, e);
        return parseFloat(n) || 0;
    } return n; }, n = (e, t) => G(t, ((t, n) => { const o = Lo(e, n), r = void 0 === o ? 0 : parseInt(o, 10); return isNaN(r) ? t : t + r; }), 0); return { set: (t, n) => { if (!E(n) && !n.match(/^[0-9]+$/))
            throw new Error(e + ".set accepts only positive integer values. Value was " + n); const o = t.dom; To(o) && (o.style[e] = n + "px"); }, get: t, getOuter: t, aggregate: n, max: (e, t, o) => { const r = n(e, o); return t > r ? t - r : 0; } }; })("height"), jo = (e, t) => ({ left: e, top: t, translate: (n, o) => jo(e + n, t + o) }), Ho = jo, $o = (e, t) => void 0 !== e ? e : void 0 !== t ? t : 0, Vo = e => { const t = e.dom, n = t.ownerDocument.body; return n === t ? Ho(n.offsetLeft, n.offsetTop) : Oo(e) ? (e => { const t = e.getBoundingClientRect(); return Ho(t.left, t.top); })(t) : Ho(0, 0); }, qo = e => { const t = void 0 !== e ? e.dom : document, n = t.body.scrollLeft || t.documentElement.scrollLeft, o = t.body.scrollTop || t.documentElement.scrollTop; return Ho(n, o); }, Wo = (e, t, n) => { const o = (void 0 !== n ? n.dom : document).defaultView; o && o.scrollTo(e, t); }, Ko = (e, t) => { Kt().browser.isSafari() && w(e.dom.scrollIntoViewIfNeeded) ? e.dom.scrollIntoViewIfNeeded(!1) : e.dom.scrollIntoView(t); }, Yo = (e, t) => { const n = (t || document).createDocumentFragment(); return q(e, (e => { n.appendChild(e.dom); })), mn(n); }, Xo = (e => { const t = t => e(t) ? I.from(t.dom.nodeValue) : I.none(); return { get: n => { if (!e(n))
            throw new Error("Can only get text value of a text node"); return t(n).getOr(""); }, getOption: t, set: (t, n) => { if (!e(t))
            throw new Error("Can only set raw text value of a text node"); t.dom.nodeValue = n; } }; })(Rn), Go = e => Xo.get(e), Zo = (e, t) => Xo.set(e, t), Qo = (e, t) => { const n = ho(e, t); return void 0 === n || "" === n ? [] : n.split(" "); };
    var Jo = (e, t, n, o, r) => e(n, o) ? I.some(n) : w(r) && r(n) ? I.none() : t(n, o, r);
    const er = (e, t, n) => { let o = e.dom; const r = w(n) ? n : L; for (; o.parentNode;) {
        o = o.parentNode;
        const e = mn(o);
        if (t(e))
            return I.some(e);
        if (r(e))
            break;
    } return I.none(); }, tr = (e, t, n) => Jo(((e, t) => t(e)), er, e, t, n), nr = (e, t) => { const n = e => { for (let o = 0; o < e.childNodes.length; o++) {
        const r = mn(e.childNodes[o]);
        if (t(r))
            return I.some(r);
        const s = n(e.childNodes[o]);
        if (s.isSome())
            return s;
    } return I.none(); }; return n(e.dom); }, or = (e, t, n) => er(e, (e => vn(e, t)), n), rr = (e, t) => ((e, t) => { const n = void 0 === t ? document : t.dom; return yn(n) ? I.none() : I.from(n.querySelector(e)).map(mn); })(t, e), sr = (e, t, n) => Jo(((e, t) => vn(e, t)), or, e, t, n), ar = e => void 0 !== e.dom.classList, ir = e => Qo(e, "class"), lr = (e, t) => ((e, t, n) => { const o = Qo(e, t).concat([n]); return go(e, t, o.join(" ")), !0; })(e, "class", t), dr = (e, t) => ((e, t, n) => { const o = Y(Qo(e, t), (e => e !== n)); return o.length > 0 ? go(e, t, o.join(" ")) : yo(e, t), !1; })(e, "class", t), cr = (e, t) => { ar(e) ? e.dom.classList.add(t) : lr(e, t); }, ur = e => { 0 === (ar(e) ? e.dom.classList : ir(e)).length && yo(e, "class"); }, mr = (e, t) => { ar(e) ? e.dom.classList.remove(t) : dr(e, t), ur(e); }, fr = (e, t) => ar(e) && e.dom.classList.contains(t), gr = (e, t = !1) => { return Oo(e) ? e.dom.isContentEditable : (n = e, sr(n, "[contenteditable]")).fold(N(t), (e => "true" === pr(e))); var n; }, pr = e => e.dom.contentEditable, hr = (e, t) => { e.dom.contentEditable = t ? "true" : "false"; }, br = (e, t) => { let n = []; return q(Hn(e), (e => { t(e) && (n = n.concat([e])), n = n.concat(br(e, t)); })), n; }, vr = (e, t) => ((e, t) => { const n = void 0 === t ? document : t.dom; return yn(n) ? [] : V(n.querySelectorAll(e), mn); })(t, e), yr = (e, t, n) => er(e, t, n).isSome(), Cr = (e, t) => ((e, t) => { const n = e.dom; return n.parentNode ? ((e, t) => Q(e.dom.childNodes, (e => t(mn(e)))).map(mn))(mn(n.parentNode), (n => !Cn(e, n) && t(n))) : I.none(); })(e, t).isSome(), wr = (e, t) => nr(e, t).isSome(), Er = e => w(e) ? e : L, xr = (e, t, n) => { const o = t(e), r = Er(n); return o.orThunk((() => r(e) ? I.none() : ((e, t, n) => { let o = e.dom; const r = Er(n); for (; o.parentNode;) {
        o = o.parentNode;
        const e = mn(o), n = t(e);
        if (n.isSome())
            return n;
        if (r(e))
            break;
    } return I.none(); })(e, t, r))); }, _r = ["img", "br"], Sr = e => { return (t = e, Xo.getOption(t)).filter((e => 0 !== e.trim().length || e.indexOf(it) > -1)).isSome() || H(_r, xn(e)) || (e => kn(e) && "false" === ho(e, "contenteditable"))(e); var t; }, kr = (e, t, n, o) => ({ start: e, soffset: t, finish: n, foffset: o }), Nr = ke([{ before: ["element"] }, { on: ["element", "offset"] }, { after: ["element"] }]), Rr = { before: Nr.before, on: Nr.on, after: Nr.after, cata: (e, t, n, o) => e.fold(t, n, o), getStart: e => e.fold(R, R, R) }, Ar = ke([{ domRange: ["rng"] }, { relative: ["startSitu", "finishSitu"] }, { exact: ["start", "soffset", "finish", "foffset"] }]), Tr = { domRange: Ar.domRange, relative: Ar.relative, exact: Ar.exact, exactFromRange: e => Ar.exact(e.start, e.soffset, e.finish, e.foffset), getWin: e => { const t = (e => e.match({ domRange: e => mn(e.startContainer), relative: (e, t) => Rr.getStart(e), exact: (e, t, n, o) => e }))(e); return Dn(t); }, range: kr }, Or = (e, t) => { const n = xn(e); return "input" === n ? Rr.after(e) : H(["br", "img"], n) ? 0 === t ? Rr.before(e) : Rr.after(e) : Rr.on(e, t); }, Br = (e, t) => { const n = e.fold(Rr.before, Or, Rr.after), o = t.fold(Rr.before, Or, Rr.after); return Tr.relative(n, o); }, Pr = (e, t, n, o) => { const r = Or(e, t), s = Or(n, o); return Tr.relative(r, s); }, Dr = e => { const t = Tr.getWin(e).dom, n = (e, n, o, r) => pn(t, e, n, o, r), o = (e => e.match({ domRange: e => { const t = mn(e.startContainer), n = mn(e.endContainer); return Pr(t, e.startOffset, n, e.endOffset); }, relative: Br, exact: Pr }))(e); return ((e, t) => { const n = ((e, t) => t.match({ domRange: e => ({ ltr: N(e), rtl: I.none }), relative: (t, n) => ({ ltr: st((() => gn(e, t, n))), rtl: st((() => I.some(gn(e, n, t)))) }), exact: (t, n, o, r) => ({ ltr: st((() => pn(e, t, n, o, r))), rtl: st((() => I.some(pn(e, o, r, t, n)))) }) }))(e, t); return ((e, t) => { const n = t.ltr(); return n.collapsed ? t.rtl().filter((e => !1 === e.collapsed)).map((e => hn.rtl(mn(e.endContainer), e.endOffset, mn(e.startContainer), e.startOffset))).getOrThunk((() => bn(0, hn.ltr, n))) : bn(0, hn.ltr, n); })(0, n); })(t, o).match({ ltr: n, rtl: n }); }, Lr = (e, t, n) => ((e, t, n) => ((e, t, n) => e.caretPositionFromPoint ? ((e, t, n) => { var o; return I.from(null === (o = e.caretPositionFromPoint) || void 0 === o ? void 0 : o.call(e, t, n)).bind((t => { if (null === t.offsetNode)
        return I.none(); const n = e.createRange(); return n.setStart(t.offsetNode, t.offset), n.collapse(), I.some(n); })); })(e, t, n) : e.caretRangeFromPoint ? ((e, t, n) => { var o; return I.from(null === (o = e.caretRangeFromPoint) || void 0 === o ? void 0 : o.call(e, t, n)); })(e, t, n) : I.none())(e.document, t, n).map((e => kr(mn(e.startContainer), e.startOffset, mn(e.endContainer), e.endOffset))))(e, t, n), Mr = (e, t, n, o) => ({ x: e, y: t, width: n, height: o, right: e + n, bottom: t + o }), Ir = e => { const t = void 0 === e ? window : e, n = t.document, o = qo(mn(n)); return (e => { const t = void 0 === e ? window : e; return Kt().browser.isFirefox() ? I.none() : I.from(t.visualViewport); })(t).fold((() => { const e = t.document.documentElement, n = e.clientWidth, r = e.clientHeight; return Mr(o.left, o.top, n, r); }), (e => Mr(Math.max(e.pageLeft, o.left), Math.max(e.pageTop, o.top), e.width, e.height))); };
    class Fr {
        constructor(e, t) { this.node = e, this.rootNode = t, this.current = this.current.bind(this), this.next = this.next.bind(this), this.prev = this.prev.bind(this), this.prev2 = this.prev2.bind(this); }
        current() { return this.node; }
        next(e) { return this.node = this.findSibling(this.node, "firstChild", "nextSibling", e), this.node; }
        prev(e) { return this.node = this.findSibling(this.node, "lastChild", "previousSibling", e), this.node; }
        prev2(e) { return this.node = this.findPreviousNode(this.node, e), this.node; }
        findSibling(e, t, n, o) { if (e) {
            if (!o && e[t])
                return e[t];
            if (e !== this.rootNode) {
                let t = e[n];
                if (t)
                    return t;
                for (let o = e.parentNode; o && o !== this.rootNode; o = o.parentNode)
                    if (t = o[n], t)
                        return t;
            }
        } }
        findPreviousNode(e, t) { if (e) {
            const n = e.previousSibling;
            if (this.rootNode && n === this.rootNode)
                return;
            if (n) {
                if (!t)
                    for (let e = n.lastChild; e; e = e.lastChild)
                        if (!e.lastChild)
                            return e;
                return n;
            }
            const o = e.parentNode;
            if (o && o !== this.rootNode)
                return o;
        } }
    }
    const Ur = /^[ \t\r\n]*$/, zr = e => Ur.test(e), jr = e => "\n" === e || "\r" === e, Hr = (e, t = 4, n = !0, o = !0) => { const r = ((e, t) => t <= 0 ? "" : new Array(t + 1).join(" "))(0, t), s = e.replace(/\t/g, r), a = G(s, ((e, t) => (e => -1 !== " \f\t\v".indexOf(e))(t) || t === it ? e.pcIsSpace || "" === e.str && n || e.str.length === s.length - 1 && o || ((e, t) => t < e.length && t >= 0 && jr(e[t]))(s, e.str.length + 1) ? { pcIsSpace: !1, str: e.str + it } : { pcIsSpace: !0, str: e.str + " " } : { pcIsSpace: jr(t), str: e.str + t }), { pcIsSpace: !1, str: "" }); return a.str; }, $r = e => t => !!t && t.nodeType === e, Vr = e => !!e && !Object.getPrototypeOf(e), qr = $r(1), Wr = e => qr(e) && kn(mn(e)), Kr = e => { const t = e.toLowerCase(); return e => C(e) && e.nodeName.toLowerCase() === t; }, Yr = e => { const t = e.map((e => e.toLowerCase())); return e => { if (e && e.nodeName) {
        const n = e.nodeName.toLowerCase();
        return H(t, n);
    } return !1; }; }, Xr = (e, t) => { const n = t.toLowerCase().split(" "); return t => { if (qr(t)) {
        const o = t.ownerDocument.defaultView;
        if (o)
            for (let r = 0; r < n.length; r++) {
                const s = o.getComputedStyle(t, null);
                if ((s ? s.getPropertyValue(e) : null) === n[r])
                    return !0;
            }
    } return !1; }; }, Gr = e => qr(e) && e.hasAttribute("data-mce-bogus"), Zr = e => qr(e) && "TABLE" === e.tagName, Qr = e => t => { if (Wr(t)) {
        if (t.contentEditable === e)
            return !0;
        if (t.getAttribute("data-mce-contenteditable") === e)
            return !0;
    } return !1; }, Jr = Yr(["textarea", "input"]), es = $r(3), ts = $r(4), ns = $r(7), os = $r(8), rs = $r(9), ss = $r(11), as = Kr("br"), is = Kr("img"), ls = Kr("a"), ds = Qr("true"), cs = Qr("false"), us = e => Wr(e) && e.isContentEditable && C(e.parentElement) && !e.parentElement.isContentEditable, ms = Yr(["td", "th"]), fs = Yr(["td", "th", "caption"]), gs = Yr(["video", "audio", "object", "embed"]), ps = Kr("li"), hs = Kr("details"), bs = Kr("summary"), vs = { skipBogus: !0, includeZwsp: !1, checkRootAsContent: !1 }, ys = e => qr(e) && e.hasAttribute("data-mce-bookmark");
    const Cs = (e, t, n, o) => es(e) && !((e, t, n) => zr(e.data) && !((e, t, n) => { const o = mn(t), r = mn(e), s = n.getWhitespaceElements(); return yr(r, (e => _e(s, xn(e))), T(Cn, o)); })(e, t, n))(e, t, n) && (!o.includeZwsp || !(e => { for (const t of e)
        if (!lt(t))
            return !1; return !0; })(e.data)), ws = (e, t, n, o) => w(o.isContent) && o.isContent(t) || ((e, t) => qr(e) && _e(t.getNonEmptyElements(), e.nodeName))(t, e) || ys(t) || (e => qr(e) && "A" === e.nodeName && !e.hasAttribute("href") && (e.hasAttribute("name") || e.hasAttribute("id")))(t) || Cs(t, n, e, o) || cs(t) || ds(t) && (e => Mn(mn(e)).exists((e => !gr(e))))(t), Es = (e, t, n) => { const o = { ...vs, ...n }; if (o.checkRootAsContent && ws(e, t, t, o))
        return !1; let r = t.firstChild, s = 0; if (!r)
        return !0; const a = new Fr(r, t); do {
        if (o.skipBogus && qr(r)) {
            const e = r.getAttribute("data-mce-bogus");
            if (e) {
                r = a.next("all" === e);
                continue;
            }
        }
        if (os(r))
            r = a.next(!0);
        else if (as(r))
            s++, r = a.next();
        else {
            if (ws(e, r, t, o))
                return !1;
            r = a.next();
        }
    } while (r); return s <= 1; }, xs = (e, t, n) => Es(e, t.dom, { checkRootAsContent: !0, ...n }), _s = (e, t, n) => ws(e, t, t, { includeZwsp: vs.includeZwsp, ...n }), Ss = e => { const t = e.toLowerCase(); return "svg" === t ? "svg" : "math" === t ? "math" : "html"; }, ks = e => "html" !== Ss(e), Ns = e => ks(e.nodeName), Rs = e => Ss(e.nodeName), As = ["svg", "math"], Ts = "data-mce-block", Os = e => V((e => Y(fe(e), (e => !/[A-Z]/.test(e))))(e), (e => { const t = CSS.escape(e); return `${t}:` + V(As, (e => `not(${e} ${t})`)).join(":"); })).join(","), Bs = (e, t) => C(t.querySelector(e)) ? (t.setAttribute(Ts, "true"), "inline-boundary" === t.getAttribute("data-mce-selected") && t.removeAttribute("data-mce-selected"), !0) : (t.removeAttribute(Ts), !1), Ps = (e, t) => { const n = Os(e.getTransparentElements()), o = Os(e.getBlockElements()); return Y(t.querySelectorAll(n), (e => Bs(o, e))); }, Ds = (e, t, n) => { var o; const r = n ? "lastChild" : "firstChild"; for (let n = t[r]; n; n = n[r])
        if (Es(e, n, { checkRootAsContent: !0 }))
            return void (null === (o = n.parentNode) || void 0 === o || o.removeChild(n)); }, Ls = (e, t, n) => { const o = e.getBlockElements(), r = mn(t), s = e => xn(e) in o, a = e => Cn(e, r); q(No(n), (t => { er(t, s, a).each((n => { const o = (t => Y(Hn(t), (t => s(t) && !e.isValidChild(xn(n), xn(t)))))(t); if (o.length > 0) {
        const t = Mn(n);
        q(o, (t => { er(t, s, a).each((n => { ((e, t, n) => { const o = document.createRange(), r = t.parentNode; if (r) {
            o.setStartBefore(t), o.setEndBefore(n);
            const s = o.extractContents();
            Ds(e, s, !0), o.setStartAfter(n), o.setEndAfter(t);
            const a = o.extractContents();
            Ds(e, a, !1), Es(e, s, { checkRootAsContent: !0 }) || r.insertBefore(s, t), Es(e, n, { checkRootAsContent: !0 }) || r.insertBefore(n, t), Es(e, a, { checkRootAsContent: !0 }) || r.insertBefore(a, t), r.removeChild(t);
        } })(e, n.dom, t.dom); })); })), t.each((t => Ps(e, t.dom)));
    } })); })); }, Ms = (e, t) => { const n = Ps(e, t); Ls(e, t, n), ((e, t, n) => { q([...n, ...js(e, t) ? [t] : []], (t => q(vr(mn(t), t.nodeName.toLowerCase()), (t => { Hs(e, t.dom) && xo(t); })))); })(e, t, n); }, Is = (e, t) => { if (zs(e, t)) {
        const n = Os(e.getBlockElements());
        Bs(n, t);
    } }, Fs = e => e.hasAttribute(Ts), Us = (e, t) => _e(e.getTransparentElements(), t), zs = (e, t) => qr(t) && Us(e, t.nodeName), js = (e, t) => zs(e, t) && Fs(t), Hs = (e, t) => zs(e, t) && !Fs(t), $s = (e, t) => 1 === t.type && Us(e, t.name) && m(t.attr(Ts)), Vs = Kt().browser, qs = e => Q(e, Nn), Ws = (e, t) => e.children && H(e.children, t), Ks = (e, t = {}) => { let n = 0; const o = {}, r = mn(e), s = Pn(r), a = e => { co(Xn(r), e); }, i = e => { const t = Xn(r); rr(t, "#" + e).each(Eo); }, l = e => xe(o, e).getOrThunk((() => ({ id: "mce-u" + n++, passed: [], failed: [], count: 0 }))), d = e => new Promise(((n, r) => { let i; const d = an._addCacheSuffix(e), c = l(d); o[d] = c, c.count++; const u = (e, t) => { q(e, D), c.status = t, c.passed = [], c.failed = [], i && (i.onload = null, i.onerror = null, i = null); }, m = () => u(c.passed, 2), f = () => u(c.failed, 3); if (n && c.passed.push(n), r && c.failed.push(r), 1 === c.status)
        return; if (2 === c.status)
        return void m(); if (3 === c.status)
        return void f(); c.status = 1; const g = cn("link", s.dom); po(g, { rel: "stylesheet", type: "text/css", id: c.id }), t.contentCssCors && go(g, "crossOrigin", "anonymous"), t.referrerPolicy && go(g, "referrerpolicy", t.referrerPolicy), i = g.dom, i.onload = m, i.onerror = f, a(g), go(g, "href", d); })), c = e => { const t = an._addCacheSuffix(e); xe(o, t).each((e => { 0 == --e.count && (delete o[t], i(e.id)); })); }; return { load: d, loadRawCss: (e, t) => { const n = l(e); o[e] = n, n.count++; const r = cn("style", s.dom); po(r, { rel: "stylesheet", type: "text/css", id: n.id, "data-mce-key": e }), r.dom.innerHTML = t, a(r); }, loadAll: e => Promise.allSettled(V(e, (e => d(e).then(N(e))))).then((e => { const t = K(e, (e => "fulfilled" === e.status)); return t.fail.length > 0 ? Promise.reject(V(t.fail, (e => e.reason))) : V(t.pass, (e => e.value)); })), unload: c, unloadRawCss: e => { xe(o, e).each((t => { 0 == --t.count && (delete o[e], i(t.id)); })); }, unloadAll: e => { q(e, (e => { c(e); })); }, _setReferrerPolicy: e => { t.referrerPolicy = e; }, _setContentCssCors: e => { t.contentCssCors = e; } }; }, Ys = (() => { const e = new WeakMap; return { forElement: (t, n) => { const o = Yn(t).dom; return I.from(e.get(o)).getOrThunk((() => { const t = Ks(o, n); return e.set(o, t), t; })); } }; })(), Xs = (e, t) => C(e) && (_s(t, e) || t.isInline(e.nodeName.toLowerCase())), Gs = e => (e => "span" === e.nodeName.toLowerCase())(e) && "bookmark" === e.getAttribute("data-mce-type"), Zs = (e, t, n, o) => { var r; const s = o || t; if (qr(t) && Gs(t))
        return t; const a = t.childNodes; for (let t = a.length - 1; t >= 0; t--)
        Zs(e, a[t], n, s); if (qr(t)) {
        const e = t.childNodes;
        1 === e.length && Gs(e[0]) && (null === (r = t.parentNode) || void 0 === r || r.insertBefore(e[0], t));
    } return (e => ss(e) || rs(e))(t) || _s(n, t) || (e => !!qr(e) && e.childNodes.length > 0)(t) || ((e, t, n) => es(e) && e.data.length > 0 && ((e, t, n) => { const o = new Fr(e, t).prev(!1), r = new Fr(e, t).next(!1), s = v(o) || Xs(o, n), a = v(r) || Xs(r, n); return s && a; })(e, t, n))(t, s, n) || e.remove(t), t; }, Qs = an.makeMap, Js = /[&<>\"\u0060\u007E-\uD7FF\uE000-\uFFEF]|[\uD800-\uDBFF][\uDC00-\uDFFF]/g, ea = /[<>&\u007E-\uD7FF\uE000-\uFFEF]|[\uD800-\uDBFF][\uDC00-\uDFFF]/g, ta = /[<>&\"\']/g, na = /&#([a-z0-9]+);?|&([a-z0-9]+);/gi, oa = { 128: "\u20ac", 130: "\u201a", 131: "\u0192", 132: "\u201e", 133: "\u2026", 134: "\u2020", 135: "\u2021", 136: "\u02c6", 137: "\u2030", 138: "\u0160", 139: "\u2039", 140: "\u0152", 142: "\u017d", 145: "\u2018", 146: "\u2019", 147: "\u201c", 148: "\u201d", 149: "\u2022", 150: "\u2013", 151: "\u2014", 152: "\u02dc", 153: "\u2122", 154: "\u0161", 155: "\u203a", 156: "\u0153", 158: "\u017e", 159: "\u0178" }, ra = { '"': "&quot;", "'": "&#39;", "<": "&lt;", ">": "&gt;", "&": "&amp;", "`": "&#96;" }, sa = { "&lt;": "<", "&gt;": ">", "&amp;": "&", "&quot;": '"', "&apos;": "'" }, aa = (e, t) => { const n = {}; if (e) {
        const o = e.split(",");
        t = t || 10;
        for (let e = 0; e < o.length; e += 2) {
            const r = String.fromCharCode(parseInt(o[e], t));
            if (!ra[r]) {
                const t = "&" + o[e + 1] + ";";
                n[r] = t, n[t] = r;
            }
        }
        return n;
    } }, ia = aa("50,nbsp,51,iexcl,52,cent,53,pound,54,curren,55,yen,56,brvbar,57,sect,58,uml,59,copy,5a,ordf,5b,laquo,5c,not,5d,shy,5e,reg,5f,macr,5g,deg,5h,plusmn,5i,sup2,5j,sup3,5k,acute,5l,micro,5m,para,5n,middot,5o,cedil,5p,sup1,5q,ordm,5r,raquo,5s,frac14,5t,frac12,5u,frac34,5v,iquest,60,Agrave,61,Aacute,62,Acirc,63,Atilde,64,Auml,65,Aring,66,AElig,67,Ccedil,68,Egrave,69,Eacute,6a,Ecirc,6b,Euml,6c,Igrave,6d,Iacute,6e,Icirc,6f,Iuml,6g,ETH,6h,Ntilde,6i,Ograve,6j,Oacute,6k,Ocirc,6l,Otilde,6m,Ouml,6n,times,6o,Oslash,6p,Ugrave,6q,Uacute,6r,Ucirc,6s,Uuml,6t,Yacute,6u,THORN,6v,szlig,70,agrave,71,aacute,72,acirc,73,atilde,74,auml,75,aring,76,aelig,77,ccedil,78,egrave,79,eacute,7a,ecirc,7b,euml,7c,igrave,7d,iacute,7e,icirc,7f,iuml,7g,eth,7h,ntilde,7i,ograve,7j,oacute,7k,ocirc,7l,otilde,7m,ouml,7n,divide,7o,oslash,7p,ugrave,7q,uacute,7r,ucirc,7s,uuml,7t,yacute,7u,thorn,7v,yuml,ci,fnof,sh,Alpha,si,Beta,sj,Gamma,sk,Delta,sl,Epsilon,sm,Zeta,sn,Eta,so,Theta,sp,Iota,sq,Kappa,sr,Lambda,ss,Mu,st,Nu,su,Xi,sv,Omicron,t0,Pi,t1,Rho,t3,Sigma,t4,Tau,t5,Upsilon,t6,Phi,t7,Chi,t8,Psi,t9,Omega,th,alpha,ti,beta,tj,gamma,tk,delta,tl,epsilon,tm,zeta,tn,eta,to,theta,tp,iota,tq,kappa,tr,lambda,ts,mu,tt,nu,tu,xi,tv,omicron,u0,pi,u1,rho,u2,sigmaf,u3,sigma,u4,tau,u5,upsilon,u6,phi,u7,chi,u8,psi,u9,omega,uh,thetasym,ui,upsih,um,piv,812,bull,816,hellip,81i,prime,81j,Prime,81u,oline,824,frasl,88o,weierp,88h,image,88s,real,892,trade,89l,alefsym,8cg,larr,8ch,uarr,8ci,rarr,8cj,darr,8ck,harr,8dl,crarr,8eg,lArr,8eh,uArr,8ei,rArr,8ej,dArr,8ek,hArr,8g0,forall,8g2,part,8g3,exist,8g5,empty,8g7,nabla,8g8,isin,8g9,notin,8gb,ni,8gf,prod,8gh,sum,8gi,minus,8gn,lowast,8gq,radic,8gt,prop,8gu,infin,8h0,ang,8h7,and,8h8,or,8h9,cap,8ha,cup,8hb,int,8hk,there4,8hs,sim,8i5,cong,8i8,asymp,8j0,ne,8j1,equiv,8j4,le,8j5,ge,8k2,sub,8k3,sup,8k4,nsub,8k6,sube,8k7,supe,8kl,oplus,8kn,otimes,8l5,perp,8m5,sdot,8o8,lceil,8o9,rceil,8oa,lfloor,8ob,rfloor,8p9,lang,8pa,rang,9ea,loz,9j0,spades,9j3,clubs,9j5,hearts,9j6,diams,ai,OElig,aj,oelig,b0,Scaron,b1,scaron,bo,Yuml,m6,circ,ms,tilde,802,ensp,803,emsp,809,thinsp,80c,zwnj,80d,zwj,80e,lrm,80f,rlm,80j,ndash,80k,mdash,80o,lsquo,80p,rsquo,80q,sbquo,80s,ldquo,80t,rdquo,80u,bdquo,810,dagger,811,Dagger,81g,permil,81p,lsaquo,81q,rsaquo,85c,euro", 32), la = (e, t) => e.replace(t ? Js : ea, (e => ra[e] || e)), da = (e, t) => e.replace(t ? Js : ea, (e => e.length > 1 ? "&#" + (1024 * (e.charCodeAt(0) - 55296) + (e.charCodeAt(1) - 56320) + 65536) + ";" : ra[e] || "&#" + e.charCodeAt(0) + ";")), ca = (e, t, n) => { const o = n || ia; return e.replace(t ? Js : ea, (e => ra[e] || o[e] || e)); }, ua = { encodeRaw: la, encodeAllRaw: e => ("" + e).replace(ta, (e => ra[e] || e)), encodeNumeric: da, encodeNamed: ca, getEncodeFunc: (e, t) => { const n = aa(t) || ia, o = Qs(e.replace(/\+/g, ",")); return o.named && o.numeric ? (e, t) => e.replace(t ? Js : ea, (e => void 0 !== ra[e] ? ra[e] : void 0 !== n[e] ? n[e] : e.length > 1 ? "&#" + (1024 * (e.charCodeAt(0) - 55296) + (e.charCodeAt(1) - 56320) + 65536) + ";" : "&#" + e.charCodeAt(0) + ";")) : o.named ? t ? (e, t) => ca(e, t, n) : ca : o.numeric ? da : la; }, decode: e => e.replace(na, ((e, t) => t ? (t = "x" === t.charAt(0).toLowerCase() ? parseInt(t.substr(1), 16) : parseInt(t, 10)) > 65535 ? (t -= 65536, String.fromCharCode(55296 + (t >> 10), 56320 + (1023 & t))) : oa[t] || String.fromCharCode(t) : sa[e] || ia[e] || (e => { const t = cn("div").dom; return t.innerHTML = e, t.textContent || t.innerText || e; })(e))) }, ma = (e, t) => (e = an.trim(e)) ? e.split(t || " ") : [], fa = e => new RegExp("^" + e.replace(/([?+*])/g, ".$1") + "$"), ga = e => Object.freeze(["id", "accesskey", "class", "dir", "lang", "style", "tabindex", "title", "role", ..."html4" !== e ? ["contenteditable", "contextmenu", "draggable", "dropzone", "hidden", "spellcheck", "translate", "itemprop", "itemscope", "itemtype"] : [], ..."html5-strict" !== e ? ["xml:lang"] : []]), pa = e => { let t, n; t = "address blockquote div dl fieldset form h1 h2 h3 h4 h5 h6 hr menu ol p pre table ul", n = "a abbr b bdo br button cite code del dfn em embed i iframe img input ins kbd label map noscript object q s samp script select small span strong sub sup textarea u var #text #comment", "html4" !== e && (t += " article aside details dialog figure main header footer hgroup section nav a ins del canvas map", n += " audio canvas command data datalist mark meter output picture progress time wbr video ruby bdi keygen svg"), "html5-strict" !== e && (n = [n, "acronym applet basefont big font strike tt"].join(" "), t = [t, "center dir isindex noframes"].join(" ")); const o = [t, n].join(" "); return { blockContent: t, phrasingContent: n, flowContent: o }; }, ha = e => { const { blockContent: t, phrasingContent: n, flowContent: o } = pa(e), r = e => Object.freeze(e.split(" ")); return Object.freeze({ blockContent: r(t), phrasingContent: r(n), flowContent: r(o) }); }, ba = { html4: st((() => ha("html4"))), html5: st((() => ha("html5"))), "html5-strict": st((() => ha("html5-strict"))) }, va = (e, t) => { const { blockContent: n, phrasingContent: o, flowContent: r } = ba[e](); return "blocks" === t ? I.some(n) : "phrasing" === t ? I.some(o) : "flow" === t ? I.some(r) : I.none(); }, ya = e => I.from(/^(@?)([A-Za-z0-9_\-.\u00b7\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u037d\u037f-\u1fff\u200c-\u200d\u203f-\u2040\u2070-\u218f\u2c00-\u2fef\u3001-\ud7ff\uf900-\ufdcf\ufdf0-\ufffd]+)$/.exec(e)).map((e => ({ preset: "@" === e[1], name: e[2] }))), Ca = {}, wa = an.makeMap, Ea = an.each, xa = an.extend, _a = an.explode, Sa = (e, t = {}) => { const n = wa(e, " ", wa(e.toUpperCase(), " ")); return xa(n, t); }, ka = e => Sa("td th li dt dd figcaption caption details summary", e.getTextBlockElements()), Na = (e, t) => { if (e) {
        const n = {};
        return m(e) && (e = { "*": e }), Ea(e, ((e, o) => { n[o] = n[o.toUpperCase()] = "map" === t ? wa(e, /[, ]/) : _a(e, /[, ]/); })), n;
    } }, Ra = (e = {}) => { var t; const n = {}, o = {}; let r = []; const s = {}, a = {}, i = (t, n, o) => { const r = e[t]; if (r)
        return wa(r, /[, ]/, wa(r.toUpperCase(), /[, ]/)); {
        let e = Ca[t];
        return e || (e = Sa(n, o), Ca[t] = e), e;
    } }, l = null !== (t = e.schema) && void 0 !== t ? t : "html5", d = (e => { const t = ga(e), { phrasingContent: n, flowContent: o } = pa(e), r = {}, s = (e, t, n) => { r[e] = { attributes: se(t, N({})), attributesOrder: t, children: se(n, N({})) }; }, a = (e, n = "", o = "") => { const r = ma(o), a = ma(e); let i = a.length; const l = [...t, ...ma(n)]; for (; i--;)
        s(a[i], l.slice(), r); }, i = (e, t) => { const n = ma(e), o = ma(t); let s = n.length; for (; s--;) {
        const e = r[n[s]];
        for (let t = 0, n = o.length; t < n; t++)
            e.attributes[o[t]] = {}, e.attributesOrder.push(o[t]);
    } }; return "html5-strict" !== e && (q(ma("acronym applet basefont big font strike tt"), (e => { a(e, "", n); })), q(ma("center dir isindex noframes"), (e => { a(e, "", o); }))), a("html", "manifest", "head body"), a("head", "", "base command link meta noscript script style title"), a("title hr noscript br"), a("base", "href target"), a("link", "href rel media hreflang type sizes hreflang"), a("meta", "name http-equiv content charset"), a("style", "media type scoped"), a("script", "src async defer type charset"), a("body", "onafterprint onbeforeprint onbeforeunload onblur onerror onfocus onhashchange onload onmessage onoffline ononline onpagehide onpageshow onpopstate onresize onscroll onstorage onunload", o), a("dd div", "", o), a("address dt caption", "", "html4" === e ? n : o), a("h1 h2 h3 h4 h5 h6 pre p abbr code var samp kbd sub sup i b u bdo span legend em strong small s cite dfn", "", n), a("blockquote", "cite", o), a("ol", "reversed start type", "li"), a("ul", "", "li"), a("li", "value", o), a("dl", "", "dt dd"), a("a", "href target rel media hreflang type", "html4" === e ? n : o), a("q", "cite", n), a("ins del", "cite datetime", o), a("img", "src sizes srcset alt usemap ismap width height"), a("iframe", "src name width height", o), a("embed", "src type width height"), a("object", "data type typemustmatch name usemap form width height", [o, "param"].join(" ")), a("param", "name value"), a("map", "name", [o, "area"].join(" ")), a("area", "alt coords shape href target rel media hreflang type"), a("table", "border", "caption colgroup thead tfoot tbody tr" + ("html4" === e ? " col" : "")), a("colgroup", "span", "col"), a("col", "span"), a("tbody thead tfoot", "", "tr"), a("tr", "", "td th"), a("td", "colspan rowspan headers", o), a("th", "colspan rowspan headers scope abbr", o), a("form", "accept-charset action autocomplete enctype method name novalidate target", o), a("fieldset", "disabled form name", [o, "legend"].join(" ")), a("label", "form for", n), a("input", "accept alt autocomplete checked dirname disabled form formaction formenctype formmethod formnovalidate formtarget height list max maxlength min multiple name pattern readonly required size src step type value width"), a("button", "disabled form formaction formenctype formmethod formnovalidate formtarget name type value", "html4" === e ? o : n), a("select", "disabled form multiple name required size", "option optgroup"), a("optgroup", "disabled label", "option"), a("option", "disabled label selected value"), a("textarea", "cols dirname disabled form maxlength name readonly required rows wrap"), a("menu", "type label", [o, "li"].join(" ")), a("noscript", "", o), "html4" !== e && (a("wbr"), a("ruby", "", [n, "rt rp"].join(" ")), a("figcaption", "", o), a("mark rt rp bdi", "", n), a("summary", "", [n, "h1 h2 h3 h4 h5 h6"].join(" ")), a("canvas", "width height", o), a("data", "value", n), a("video", "src crossorigin poster preload autoplay mediagroup loop muted controls width height buffered", [o, "track source"].join(" ")), a("audio", "src crossorigin preload autoplay mediagroup loop muted controls buffered volume", [o, "track source"].join(" ")), a("picture", "", "img source"), a("source", "src srcset type media sizes"), a("track", "kind src srclang label default"), a("datalist", "", [n, "option"].join(" ")), a("article section nav aside main header footer", "", o), a("hgroup", "", "h1 h2 h3 h4 h5 h6"), a("figure", "", [o, "figcaption"].join(" ")), a("time", "datetime", n), a("dialog", "open", o), a("command", "type label icon disabled checked radiogroup command"), a("output", "for form name", n), a("progress", "value max", n), a("meter", "value min max low high optimum", n), a("details", "open", [o, "summary"].join(" ")), a("keygen", "autofocus challenge disabled form keytype name"), s("svg", "id tabindex lang xml:space class style x y width height viewBox preserveAspectRatio zoomAndPan transform".split(" "), [])), "html5-strict" !== e && (i("script", "language xml:space"), i("style", "xml:space"), i("object", "declare classid code codebase codetype archive standby align border hspace vspace"), i("embed", "align name hspace vspace"), i("param", "valuetype type"), i("a", "charset name rev shape coords"), i("br", "clear"), i("applet", "codebase archive code object alt name width height align hspace vspace"), i("img", "name longdesc align border hspace vspace"), i("iframe", "longdesc frameborder marginwidth marginheight scrolling align"), i("font basefont", "size color face"), i("input", "usemap align"), i("select"), i("textarea"), i("h1 h2 h3 h4 h5 h6 div p legend caption", "align"), i("ul", "type compact"), i("li", "type"), i("ol dl menu dir", "compact"), i("pre", "width xml:space"), i("hr", "align noshade size width"), i("isindex", "prompt"), i("table", "summary width frame rules cellspacing cellpadding align bgcolor"), i("col", "width align char charoff valign"), i("colgroup", "width align char charoff valign"), i("thead", "align char charoff valign"), i("tr", "align char charoff valign bgcolor"), i("th", "axis align char charoff valign nowrap bgcolor width height"), i("form", "accept"), i("td", "abbr axis scope align char charoff valign nowrap bgcolor width height"), i("tfoot", "align char charoff valign"), i("tbody", "align char charoff valign"), i("area", "nohref"), i("body", "background bgcolor text link vlink alink")), "html4" !== e && (i("input button select textarea", "autofocus"), i("input textarea", "placeholder"), i("a", "download"), i("link script img", "crossorigin"), i("img", "loading"), i("iframe", "sandbox seamless allow allowfullscreen loading referrerpolicy")), "html4" !== e && q([r.video, r.audio], (e => { delete e.children.audio, delete e.children.video; })), q(ma("a form meter progress dfn"), (e => { r[e] && delete r[e].children[e]; })), delete r.caption.children.table, delete r.script, r; })(l); !1 === e.verify_html && (e.valid_elements = "*[*]"); const c = Na(e.valid_styles), u = Na(e.invalid_styles, "map"), g = Na(e.valid_classes, "map"), h = i("whitespace_elements", "pre script noscript style textarea video audio iframe object code"), v = i("self_closing_elements", "colgroup dd dt li option p td tfoot th thead tr"), y = i("void_elements", "area base basefont br col frame hr img input isindex link meta param embed source wbr track"), C = i("boolean_attributes", "checked compact declare defer disabled ismap multiple nohref noresize noshade nowrap readonly selected autoplay loop controls allowfullscreen"), w = "td th iframe video audio object script code", E = i("non_empty_elements", w + " pre svg textarea summary", y), x = i("move_caret_before_on_enter_elements", w + " table", y), _ = "h1 h2 h3 h4 h5 h6", S = i("text_block_elements", _ + " p div address pre form blockquote center dir fieldset header footer article section hgroup aside main nav figure"), k = i("block_elements", "hr table tbody thead tfoot th tr td li ol ul caption dl dt dd noscript menu isindex option datalist select optgroup figcaption details summary html body multicol listing", S), R = i("text_inline_elements", "span strong b em i font s strike u var cite dfn code mark q sup sub samp"), A = i("transparent_elements", "a ins del canvas map"), T = i("wrap_block_elements", "pre " + _); Ea("script noscript iframe noframes noembed title style textarea xmp plaintext".split(" "), (e => { a[e] = new RegExp("</" + e + "[^>]*>", "gi"); })); const O = e => { const t = I.from(n["@"]), o = /[*?+]/; q(((e, t) => { const n = /^([#+\-])?([^\[!\/]+)(?:\/([^\[!]+))?(?:(!?)\[([^\]]+)])?$/; return te(ma(t, ","), (t => { const o = n.exec(t); if (o) {
        const t = o[1], n = o[2], r = o[3], s = o[4], a = o[5], i = { attributes: {}, attributesOrder: [] };
        if (e.each((e => ((e, t) => { pe(e.attributes, ((e, n) => { t.attributes[n] = e; })), t.attributesOrder.push(...e.attributesOrder); })(e, i))), "#" === t ? i.paddEmpty = !0 : "-" === t && (i.removeEmpty = !0), "!" === s && (i.removeEmptyAttrs = !0), a && ((e, t) => { const n = /^([!\-])?(\w+[\\:]:\w+|[^=~<]+)?(?:([=~<])(.*))?$/, o = /[*?+]/, { attributes: r, attributesOrder: s } = t; q(ma(e, "|"), (e => { const a = n.exec(e); if (a) {
            const e = {}, n = a[1], i = a[2].replace(/[\\:]:/g, ":"), l = a[3], d = a[4];
            if ("!" === n && (t.attributesRequired = t.attributesRequired || [], t.attributesRequired.push(i), e.required = !0), "-" === n)
                return delete r[i], void s.splice(an.inArray(s, i), 1);
            if (l && ("=" === l ? (t.attributesDefault = t.attributesDefault || [], t.attributesDefault.push({ name: i, value: d }), e.defaultValue = d) : "~" === l ? (t.attributesForced = t.attributesForced || [], t.attributesForced.push({ name: i, value: d }), e.forcedValue = d) : "<" === l && (e.validValues = an.makeMap(d, "?"))), o.test(i)) {
                const n = e;
                t.attributePatterns = t.attributePatterns || [], n.pattern = fa(i), t.attributePatterns.push(n);
            }
            else
                r[i] || s.push(i), r[i] = e;
        } })); })(a, i), r && (i.outputName = n), "@" === n) {
            if (!e.isNone())
                return [];
            e = I.some(i);
        }
        return [r ? { name: n, element: i, aliasName: r } : { name: n, element: i }];
    } return []; })); })(t, null != e ? e : ""), (({ name: e, element: t, aliasName: s }) => { if (s && (n[s] = t), o.test(e)) {
        const n = t;
        n.pattern = fa(e), r.push(n);
    }
    else
        n[e] = t; })); }, B = e => { r = [], q(fe(n), (e => { delete n[e]; })), O(e); }, P = (e, t) => { var r, a; delete Ca.text_block_elements, delete Ca.block_elements; const i = !!t.extends && !oe(t.extends), d = t.extends; if (o[e] = d ? o[d] : {}, s[e] = null != d ? d : e, E[e.toUpperCase()] = {}, E[e] = {}, i || (k[e.toUpperCase()] = {}, k[e] = {}), d && !n[e] && n[d]) {
        const t = (e => { const t = e => p(e) ? V(e, t) : (e => f(e) && e.source && "[object RegExp]" === Object.prototype.toString.call(e))(e) ? new RegExp(e.source, e.flags) : f(e) ? he(e, t) : e; return t(e); })(n[d]);
        delete t.removeEmptyAttrs, delete t.removeEmpty, n[e] = t;
    }
    else
        n[e] = { attributesOrder: [], attributes: {} }; if (p(t.attributes)) {
        const o = e => { s.attributesOrder.push(e), s.attributes[e] = {}; }, s = null !== (r = n[e]) && void 0 !== r ? r : {};
        delete s.attributesDefault, delete s.attributesForced, delete s.attributePatterns, delete s.attributesRequired, s.attributesOrder = [], s.attributes = {}, q(t.attributes, (e => { const t = ga(l); ya(e).each((({ preset: e, name: n }) => { e ? "global" === n && q(t, o) : o(n); })); })), n[e] = s;
    } if (b(t.padEmpty)) {
        const o = null !== (a = n[e]) && void 0 !== a ? a : {};
        o.paddEmpty = t.padEmpty, n[e] = o;
    } if (p(t.children)) {
        const n = {}, r = e => { n[e] = {}; }, s = e => { va(l, e).each((e => { q(e, r); })); };
        q(t.children, (e => { ya(e).each((({ preset: e, name: t }) => { e ? s(t) : r(t); })); })), o[e] = n;
    } d && pe(o, ((t, n) => { t[d] && (o[n] = t = xa({}, o[n]), t[e] = t[d]); })); }, D = e => { f(e) ? pe(e, ((e, t) => P(t, e))) : m(e) && (e => { q((e => { const t = /^(~)?(.+)$/; return te(ma(e, ","), (e => { const n = t.exec(e); return n ? [{ cloneName: "~" === n[1] ? "span" : "div", name: n[2] }] : []; })); })(null != e ? e : ""), (({ name: e, cloneName: t }) => { P(e, { extends: t }); })); })(e); }, L = e => { q((e => { const t = /^([+\-]?)([A-Za-z0-9_\-.\u00b7\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u037d\u037f-\u1fff\u200c-\u200d\u203f-\u2040\u2070-\u218f\u2c00-\u2fef\u3001-\ud7ff\uf900-\ufdcf\ufdf0-\ufffd]+)\[([^\]]+)]$/; return te(ma(e, ","), (e => { const n = t.exec(e); if (n) {
        const e = n[1], t = e ? (e => "-" === e ? "remove" : "add")(e) : "replace";
        return [{ operation: t, name: n[2], validChildren: te(ma(n[3], "|"), (e => ya(e).toArray())) }];
    } return []; })); })(null != e ? e : ""), (({ operation: e, name: t, validChildren: n }) => { const r = "replace" === e ? { "#comment": {} } : o[t], s = t => { "remove" === e ? delete r[t] : r[t] = {}; }; q(n, (({ preset: e, name: t }) => { e ? (e => { va(l, e).each((e => { q(e, s); })); })(t) : s(t); })), o[t] = r; })); }, M = e => { const t = n[e]; if (t)
        return t; let o = r.length; for (; o--;) {
        const t = r[o];
        if (t.pattern.test(e))
            return t;
    } }, F = N(c), U = N(u), z = N(g), j = N(C), H = N(k), $ = N(S), W = N(R), K = N(Object.seal(y)), Y = N(v), X = N(E), G = N(x), Z = N(h), Q = N(A), J = N(T), ee = N(Object.seal(a)), ne = (e, t) => { const n = M(e); if (n) {
        if (!t)
            return !0;
        {
            if (n.attributes[t])
                return !0;
            const e = n.attributePatterns;
            if (e) {
                let n = e.length;
                for (; n--;)
                    if (e[n].pattern.test(t))
                        return !0;
            }
        }
    } return !1; }, oe = e => _e(H(), e), re = e => !Ye(e, "#") && ne(e) && !oe(e), ae = N(s); return e.valid_elements ? (B(e.valid_elements), Ea(d, ((e, t) => { o[t] = e.children; }))) : (Ea(d, ((e, t) => { n[t] = { attributes: e.attributes, attributesOrder: e.attributesOrder }, o[t] = e.children; })), Ea(ma("strong/b em/i"), (e => { const t = ma(e, "/"); n[t[1]].outputName = t[0]; })), Ea(R, ((t, o) => { n[o] && (e.padd_empty_block_inline_children && (n[o].paddInEmptyBlock = !0), n[o].removeEmpty = !0); })), Ea(ma("ol ul blockquote a table tbody"), (e => { n[e] && (n[e].removeEmpty = !0); })), Ea(ma("p h1 h2 h3 h4 h5 h6 th td pre div address caption li summary"), (e => { n[e] && (n[e].paddEmpty = !0); })), Ea(ma("span"), (e => { n[e].removeEmptyAttrs = !0; }))), delete n.svg, D(e.custom_elements), L(e.valid_children), O(e.extended_valid_elements), L("+ol[ul|ol],+ul[ul|ol]"), Ea({ dd: "dl", dt: "dl", li: "ul ol", td: "tr", th: "tr", tr: "tbody thead tfoot", tbody: "table", thead: "table", tfoot: "table", legend: "fieldset", area: "map", param: "video audio object" }, ((e, t) => { n[t] && (n[t].parentsRequired = ma(e)); })), e.invalid_elements && Ea(_a(e.invalid_elements), (e => { n[e] && delete n[e]; })), M("span") || O("span[!data-mce-type|*]"), { type: l, children: o, elements: n, getValidStyles: F, getValidClasses: z, getBlockElements: H, getInvalidStyles: U, getVoidElements: K, getTextBlockElements: $, getTextInlineElements: W, getBoolAttrs: j, getElementRule: M, getSelfClosingElements: Y, getNonEmptyElements: X, getMoveCaretBeforeOnEnterElements: G, getWhitespaceElements: Z, getTransparentElements: Q, getSpecialElements: ee, isValidChild: (e, t) => { const n = o[e.toLowerCase()]; return !(!n || !n[t.toLowerCase()]); }, isValid: ne, isBlock: oe, isInline: re, isWrapper: e => _e(J(), e) || re(e), getCustomElements: ae, addValidElements: O, setValidElements: B, addCustomElements: D, addValidChildren: L }; }, Aa = e => { const t = e.toString(16); return (1 === t.length ? "0" + t : t).toUpperCase(); }, Ta = e => (e => { return { value: (t = e, We(t, "#").toUpperCase()) }; var t; })(Aa(e.red) + Aa(e.green) + Aa(e.blue)), Oa = /^\s*rgb\s*\(\s*(\d+)\s*[,\s]\s*(\d+)\s*[,\s]\s*(\d+)\s*\)\s*$/i, Ba = /^\s*rgba\s*\(\s*(\d+)\s*[,\s]\s*(\d+)\s*[,\s]\s*(\d+)\s*[,\s]\s*((?:\d?\.\d+|\d+)%?)\s*\)\s*$/i, Pa = (e, t, n, o) => ((e, t, n, o) => ({ red: e, green: t, blue: n, alpha: o }))(parseInt(e, 10), parseInt(t, 10), parseInt(n, 10), parseFloat(o)), Da = e => { const t = Oa.exec(e); if (null !== t)
        return I.some(Pa(t[1], t[2], t[3], "1")); const n = Ba.exec(e); return null !== n ? I.some(Pa(n[1], n[2], n[3], n[4])) : I.none(); }, La = e => `rgba(${e.red},${e.green},${e.blue},${e.alpha})`, Ma = e => Da(e).map(Ta).map((e => "#" + e.value)).getOr(e), Ia = (e = {}, t) => { const n = /(?:url(?:(?:\(\s*\"([^\"]+)\"\s*\))|(?:\(\s*\'([^\']+)\'\s*\))|(?:\(\s*([^)\s]+)\s*\))))|(?:\'([^\']+)\')|(?:\"([^\"]+)\")/gi, o = /\s*([^:]+):\s*([^;]+);?/g, r = /\s+$/, s = {}; let a, i; const l = at; t && (a = t.getValidStyles(), i = t.getInvalidStyles()); const d = "\\\" \\' \\; \\: ; : \ufeff".split(" "); for (let e = 0; e < d.length; e++)
        s[d[e]] = l + e, s[l + e] = d[e]; const c = { parse: t => { const a = {}; let i = !1; const d = e.url_converter, u = e.url_converter_scope || c, m = (e, t, n) => { const o = a[e + "-top" + t]; if (!o)
            return; const r = a[e + "-right" + t]; if (!r)
            return; const s = a[e + "-bottom" + t]; if (!s)
            return; const i = a[e + "-left" + t]; if (!i)
            return; const l = [o, r, s, i]; let d = l.length - 1; for (; d-- && l[d] === l[d + 1];)
            ; d > -1 && n || (a[e + t] = -1 === d ? l[0] : l.join(" "), delete a[e + "-top" + t], delete a[e + "-right" + t], delete a[e + "-bottom" + t], delete a[e + "-left" + t]); }, f = e => { const t = a[e]; if (!t)
            return; const n = t.indexOf(",") > -1 ? [t] : t.split(" "); let o = n.length; for (; o--;)
            if (n[o] !== n[0])
                return !1; return a[e] = n[0], !0; }, g = e => (i = !0, s[e]), p = (e, t) => (i && (e = e.replace(/\uFEFF[0-9]/g, (e => s[e]))), t || (e = e.replace(/\\([\'\";:])/g, "$1")), e), h = e => String.fromCharCode(parseInt(e.slice(1), 16)), b = e => e.replace(/\\[0-9a-f]+/gi, h), v = (t, n, o, r, s, a) => { if (s = s || a)
            return "'" + (s = p(s)).replace(/\'/g, "\\'") + "'"; if (n = p(n || o || r || ""), !e.allow_script_urls) {
            const t = n.replace(/[\s\r\n]+/g, "");
            if (/(java|vb)script:/i.test(t))
                return "";
            if (!e.allow_svg_data_urls && /^data:image\/svg/i.test(t))
                return "";
        } return d && (n = d.call(u, n, "style")), "url('" + n.replace(/\'/g, "\\'") + "')"; }; if (t) {
            let s;
            for (t = (t = t.replace(/[\u0000-\u001F]/g, "")).replace(/\\[\"\';:\uFEFF]/g, g).replace(/\"[^\"]+\"|\'[^\']+\'/g, (e => e.replace(/[;:]/g, g))); s = o.exec(t);) {
                o.lastIndex = s.index + s[0].length;
                let t = s[1].replace(r, "").toLowerCase(), d = s[2].replace(r, "");
                if (t && d) {
                    if (t = b(t), d = b(d), -1 !== t.indexOf(l) || -1 !== t.indexOf('"'))
                        continue;
                    if (!e.allow_script_urls && ("behavior" === t || /expression\s*\(|\/\*|\*\//.test(d)))
                        continue;
                    "font-weight" === t && "700" === d ? d = "bold" : "color" !== t && "background-color" !== t || (d = d.toLowerCase()), "rgb" == (E = d, Oa.test(E) ? "rgb" : Ba.test(E) ? "rgba" : "other") && Da(d).each((e => { d = Ma(La(e)).toLowerCase(); })), d = d.replace(n, v), a[t] = i ? p(d, !0) : d;
                }
            }
            m("border", "", !0), m("border", "-width"), m("border", "-color"), m("border", "-style"), m("padding", ""), m("margin", ""), C = "border-style", w = "border-color", f(y = "border-width") && f(C) && f(w) && (a.border = a[y] + " " + a[C] + " " + a[w], delete a[y], delete a[C], delete a[w]), "medium none" === a.border && delete a.border, "none" === a["border-image"] && delete a["border-image"];
        } var y, C, w, E; return a; }, serialize: (e, t) => { let n = ""; const o = (t, o) => { const r = o[t]; if (r)
            for (let t = 0, o = r.length; t < o; t++) {
                const o = r[t], s = e[o];
                s && (n += (n.length > 0 ? " " : "") + o + ": " + s + ";");
            } }; return t && a ? (o("*", a), o(t, a)) : pe(e, ((e, o) => { e && ((e, t) => { if (!i || !t)
            return !0; let n = i["*"]; return !(n && n[e] || (n = i[t], n && n[e])); })(o, t) && (n += (n.length > 0 ? " " : "") + o + ": " + e + ";"); })), n; } }; return c; }, Fa = { keyLocation: !0, layerX: !0, layerY: !0, returnValue: !0, webkitMovementX: !0, webkitMovementY: !0, keyIdentifier: !0, mozPressure: !0 }, Ua = (e, t) => { const n = null != t ? t : {}; for (const t in e)
        _e(Fa, t) || (n[t] = e[t]); return C(e.composedPath) && (n.composedPath = () => e.composedPath()), C(e.getModifierState) && (n.getModifierState = t => e.getModifierState(t)), C(e.getTargetRanges) && (n.getTargetRanges = () => e.getTargetRanges()), n; }, za = (e, t, n, o) => { var r; const s = Ua(t, o); return s.type = e, y(s.target) && (s.target = null !== (r = s.srcElement) && void 0 !== r ? r : n), (e => y(e.preventDefault) || (e => e instanceof Event || w(e.initEvent))(e))(t) && (s.preventDefault = () => { s.defaultPrevented = !0, s.isDefaultPrevented = M, w(t.preventDefault) && t.preventDefault(); }, s.stopPropagation = () => { s.cancelBubble = !0, s.isPropagationStopped = M, w(t.stopPropagation) && t.stopPropagation(); }, s.stopImmediatePropagation = () => { s.isImmediatePropagationStopped = M, s.stopPropagation(); }, (e => e.isDefaultPrevented === M || e.isDefaultPrevented === L)(s) || (s.isDefaultPrevented = !0 === s.defaultPrevented ? M : L, s.isPropagationStopped = !0 === s.cancelBubble ? M : L, s.isImmediatePropagationStopped = L)), s; }, ja = /^(?:mouse|contextmenu)|click/, Ha = (e, t, n, o) => { e.addEventListener(t, n, o || !1); }, $a = (e, t, n, o) => { e.removeEventListener(t, n, o || !1); }, Va = (e, t) => { const n = za(e.type, e, document, t); if ((e => C(e) && ja.test(e.type))(e) && v(e.pageX) && !v(e.clientX)) {
        const t = n.target.ownerDocument || document, o = t.documentElement, r = t.body, s = n;
        s.pageX = e.clientX + (o && o.scrollLeft || r && r.scrollLeft || 0) - (o && o.clientLeft || r && r.clientLeft || 0), s.pageY = e.clientY + (o && o.scrollTop || r && r.scrollTop || 0) - (o && o.clientTop || r && r.clientTop || 0);
    } return n; }, qa = (e, t, n) => { const o = e.document, r = { type: "ready" }; if (n.domLoaded)
        return void t(r); const s = () => { $a(e, "DOMContentLoaded", s), $a(e, "load", s), n.domLoaded || (n.domLoaded = !0, t(r)), e = null; }; "complete" === o.readyState || "interactive" === o.readyState && o.body ? s() : Ha(e, "DOMContentLoaded", s), n.domLoaded || Ha(e, "load", s); };
    class Wa {
        constructor() { this.domLoaded = !1, this.events = {}, this.count = 1, this.expando = "mce-data-" + (+new Date).toString(32), this.hasFocusIn = "onfocusin" in document.documentElement, this.count = 1; }
        bind(e, t, n, o) { const r = this; let s; const a = window, i = e => { r.executeHandlers(Va(e || a.event), l); }; if (!e || es(e) || os(e))
            return n; let l; e[r.expando] ? l = e[r.expando] : (l = r.count++, e[r.expando] = l, r.events[l] = {}), o = o || e; const d = t.split(" "); let c = d.length; for (; c--;) {
            let t = d[c], u = i, m = !1, f = !1;
            "DOMContentLoaded" === t && (t = "ready"), r.domLoaded && "ready" === t && "complete" === e.readyState ? n.call(o, Va({ type: t })) : (r.hasFocusIn || "focusin" !== t && "focusout" !== t || (m = !0, f = "focusin" === t ? "focus" : "blur", u = e => { const t = Va(e || a.event); t.type = "focus" === t.type ? "focusin" : "focusout", r.executeHandlers(t, l); }), s = r.events[l][t], s ? "ready" === t && r.domLoaded ? n(Va({ type: t })) : s.push({ func: n, scope: o }) : (r.events[l][t] = s = [{ func: n, scope: o }], s.fakeName = f, s.capture = m, s.nativeHandler = u, "ready" === t ? qa(e, u, r) : Ha(e, f || t, u, m)));
        } return e = s = null, n; }
        unbind(e, t, n) { if (!e || es(e) || os(e))
            return this; const o = e[this.expando]; if (o) {
            let r = this.events[o];
            if (t) {
                const o = t.split(" ");
                let s = o.length;
                for (; s--;) {
                    const t = o[s], a = r[t];
                    if (a) {
                        if (n) {
                            let e = a.length;
                            for (; e--;)
                                if (a[e].func === n) {
                                    const n = a.nativeHandler, o = a.fakeName, s = a.capture, i = a.slice(0, e).concat(a.slice(e + 1));
                                    i.nativeHandler = n, i.fakeName = o, i.capture = s, r[t] = i;
                                }
                        }
                        n && 0 !== a.length || (delete r[t], $a(e, a.fakeName || t, a.nativeHandler, a.capture));
                    }
                }
            }
            else
                pe(r, ((t, n) => { $a(e, t.fakeName || n, t.nativeHandler, t.capture); })), r = {};
            for (const e in r)
                if (_e(r, e))
                    return this;
            delete this.events[o];
            try {
                delete e[this.expando];
            }
            catch (t) {
                e[this.expando] = null;
            }
        } return this; }
        fire(e, t, n) { return this.dispatch(e, t, n); }
        dispatch(e, t, n) { if (!e || es(e) || os(e))
            return this; const o = Va({ type: t, target: e }, n); do {
            const t = e[this.expando];
            t && this.executeHandlers(o, t), e = e.parentNode || e.ownerDocument || e.defaultView || e.parentWindow;
        } while (e && !o.isPropagationStopped()); return this; }
        clean(e) { if (!e || es(e) || os(e))
            return this; if (e[this.expando] && this.unbind(e), e.getElementsByTagName || (e = e.document), e && e.getElementsByTagName) {
            this.unbind(e);
            const t = e.getElementsByTagName("*");
            let n = t.length;
            for (; n--;)
                (e = t[n])[this.expando] && this.unbind(e);
        } return this; }
        destroy() { this.events = {}; }
        cancel(e) { return e && (e.preventDefault(), e.stopImmediatePropagation()), !1; }
        executeHandlers(e, t) { const n = this.events[t], o = n && n[e.type]; if (o)
            for (let t = 0, n = o.length; t < n; t++) {
                const n = o[t];
                if (n && !1 === n.func.call(n.scope, e) && e.preventDefault(), e.isImmediatePropagationStopped())
                    return;
            } }
    }
    Wa.Event = new Wa;
    const Ka = an.each, Ya = an.grep, Xa = "data-mce-style", Ga = an.makeMap("fill-opacity font-weight line-height opacity orphans widows z-index zoom", " "), Za = (e, t, n) => { y(n) || "" === n ? yo(e, t) : go(e, t, n); }, Qa = e => e.replace(/[A-Z]/g, (e => "-" + e.toLowerCase())), Ja = (e, t) => { let n = 0; if (e)
        for (let o = e.nodeType, r = e.previousSibling; r; r = r.previousSibling) {
            const e = r.nodeType;
            (!t || !es(r) || e !== o && r.data.length) && (n++, o = e);
        } return n; }, ei = (e, t) => { const n = ho(t, "style"), o = e.serialize(e.parse(n), xn(t)); Za(t, Xa, o); }, ti = (e, t, n) => { const o = Qa(t); y(n) || "" === n ? Uo(e, o) : Po(e, o, ((e, t) => E(e) ? _e(Ga, t) ? e + "" : e + "px" : e)(n, o)); }, ni = (e, t = {}) => { const n = {}, o = window, r = {}; let s = 0; const a = Ys.forElement(mn(e), { contentCssCors: t.contentCssCors, referrerPolicy: t.referrerPolicy }), i = [], l = t.schema ? t.schema : Ra({}), d = Ia({ url_converter: t.url_converter, url_converter_scope: t.url_converter_scope }, t.schema), c = t.ownEvents ? new Wa : Wa.Event, u = l.getBlockElements(), f = t => t && e && m(t) ? e.getElementById(t) : t, h = e => { const t = f(e); return C(t) ? mn(t) : null; }, b = (e, t, n = "") => { let o; const r = h(e); if (C(r) && Nn(r)) {
        const e = X[t];
        o = e && e.get ? e.get(r.dom, t) : ho(r, t);
    } return C(o) ? o : n; }, v = e => { const t = f(e); return y(t) ? [] : t.attributes; }, E = (e, n, o) => { B(e, (e => { if (qr(e)) {
        const r = mn(e), s = "" === o ? null : o, a = ho(r, n), i = X[n];
        i && i.set ? i.set(r.dom, s, n) : Za(r, n, s), a !== s && t.onSetAttrib && t.onSetAttrib({ attrElm: r.dom, attrName: n, attrValue: s });
    } })); }, x = () => t.root_element || e.body, S = (t, n) => ((e, t, n) => { let o = 0, r = 0; const s = e.ownerDocument; if (n = n || e, t) {
        if (n === e && t.getBoundingClientRect && "static" === Lo(mn(e), "position")) {
            const n = t.getBoundingClientRect();
            return o = n.left + (s.documentElement.scrollLeft || e.scrollLeft) - s.documentElement.clientLeft, r = n.top + (s.documentElement.scrollTop || e.scrollTop) - s.documentElement.clientTop, { x: o, y: r };
        }
        let a = t;
        for (; a && a !== n && a.nodeType && !Ws(a, n);) {
            const e = a;
            o += e.offsetLeft || 0, r += e.offsetTop || 0, a = e.offsetParent;
        }
        for (a = t.parentNode; a && a !== n && a.nodeType && !Ws(a, n);)
            o -= a.scrollLeft || 0, r -= a.scrollTop || 0, a = a.parentNode;
        r += (e => Vs.isFirefox() && "table" === xn(e) ? qs(Hn(e)).filter((e => "caption" === xn(e))).bind((e => qs(jn(e)).map((t => { const n = t.dom.offsetTop, o = e.dom.offsetTop, r = e.dom.offsetHeight; return n <= o ? -r : 0; })))).getOr(0) : 0)(mn(t));
    } return { x: o, y: r }; })(e.body, f(t), n), k = (e, t, n) => { const o = f(e); var r; if (!y(o) && (Wr(o) || qr(r = o) && "http://www.w3.org/2000/svg" === r.namespaceURI))
        return n ? Lo(mn(o), Qa(t)) : ("float" === (t = t.replace(/-(\D)/g, ((e, t) => t.toUpperCase()))) && (t = "cssFloat"), o.style ? o.style[t] : void 0); }, R = e => { const t = f(e); if (!t)
        return { w: 0, h: 0 }; let n = k(t, "width"), o = k(t, "height"); return n && -1 !== n.indexOf("px") || (n = "0"), o && -1 !== o.indexOf("px") || (o = "0"), { w: parseInt(n, 10) || t.offsetWidth || t.clientWidth, h: parseInt(o, 10) || t.offsetHeight || t.clientHeight }; }, A = (e, t) => { if (!e)
        return !1; const n = p(e) ? e : [e]; return $(n, (e => vn(mn(e), t))); }, T = (e, t, n, o) => { const r = []; let s = f(e); o = void 0 === o; const a = n || ("BODY" !== x().nodeName ? x().parentNode : null); if (m(t))
        if ("*" === t)
            t = qr;
        else {
            const e = t;
            t = t => A(t, e);
        } for (; s && !(s === a || y(s.nodeType) || rs(s) || ss(s));) {
        if (!t || t(s)) {
            if (!o)
                return [s];
            r.push(s);
        }
        s = s.parentNode;
    } return o ? r : null; }, O = (e, t, n) => { let o = t; if (e) {
        m(t) && (o = e => A(e, t));
        for (let t = e[n]; t; t = t[n])
            if (w(o) && o(t))
                return t;
    } return null; }, B = function (e, t, n) { const o = null != n ? n : this; if (p(e)) {
        const n = [];
        return Ka(e, ((e, r) => { const s = f(e); s && n.push(t.call(o, s, r)); })), n;
    } {
        const n = f(e);
        return !!n && t.call(o, n);
    } }, P = (e, t) => { B(e, (e => { pe(t, ((t, n) => { E(e, n, t); })); })); }, D = (e, t) => { B(e, (e => { const n = mn(e); Ao(n, t); })); }, L = (t, n, o, r, s) => B(t, (t => { const a = m(n) ? e.createElement(n) : n; return C(o) && P(a, o), r && (!m(r) && r.nodeType ? a.appendChild(r) : m(r) && D(a, r)), s ? a : t.appendChild(a); })), M = (t, n, o) => L(e.createElement(t), t, n, o, !0), I = ua.encodeAllRaw, F = (e, t) => B(e, (e => { const n = mn(e); return t && q(Hn(n), (e => { Rn(e) && 0 === e.dom.length ? Eo(e) : ao(n, e); })), Eo(n), n.dom; })), U = (e, t, n) => { B(e, (e => { if (qr(e)) {
        const o = mn(e), r = t.split(" ");
        q(r, (e => { C(n) ? (n ? cr : mr)(o, e) : ((e, t) => { const n = ar(e) ? e.dom.classList.toggle(t) : ((e, t) => H(ir(e), t) ? dr(e, t) : lr(e, t))(e, t); ur(e); })(o, e); }));
    } })); }, z = (e, t, n) => B(t, (o => { var r; const s = p(t) ? e.cloneNode(!0) : e; return n && Ka(Ya(o.childNodes), (e => { s.appendChild(e); })), null === (r = o.parentNode) || void 0 === r || r.replaceChild(s, o), o; })), j = () => e.createRange(), V = (n, r, s, a) => { if (p(n)) {
        let e = n.length;
        const t = [];
        for (; e--;)
            t[e] = V(n[e], r, s, a);
        return t;
    } return !t.collect || n !== e && n !== o || i.push([n, r, s, a]), c.bind(n, r, s, a || Y); }, W = (t, n, r) => { if (p(t)) {
        let e = t.length;
        const o = [];
        for (; e--;)
            o[e] = W(t[e], n, r);
        return o;
    } if (i.length > 0 && (t === e || t === o)) {
        let e = i.length;
        for (; e--;) {
            const [o, s, a] = i[e];
            t !== o || n && n !== s || r && r !== a || c.unbind(o, s, a);
        }
    } return c.unbind(t, n, r); }, K = e => { if (e && Wr(e)) {
        const t = e.getAttribute("data-mce-contenteditable");
        return t && "inherit" !== t ? t : "inherit" !== e.contentEditable ? e.contentEditable : null;
    } return null; }, Y = { doc: e, settings: t, win: o, files: r, stdMode: !0, boxModel: !0, styleSheetLoader: a, boundEvents: i, styles: d, schema: l, events: c, isBlock: e => m(e) ? _e(u, e) : qr(e) && (_e(u, e.nodeName) || js(l, e)), root: null, clone: (e, t) => e.cloneNode(t), getRoot: x, getViewPort: e => { const t = Ir(e); return { x: t.x, y: t.y, w: t.width, h: t.height }; }, getRect: e => { const t = f(e), n = S(t), o = R(t); return { x: n.x, y: n.y, w: o.w, h: o.h }; }, getSize: R, getParent: (e, t, n) => { const o = T(e, t, n, !1); return o && o.length > 0 ? o[0] : null; }, getParents: T, get: f, getNext: (e, t) => O(e, t, "nextSibling"), getPrev: (e, t) => O(e, t, "previousSibling"), select: (n, o) => { var r, s; const a = null !== (s = null !== (r = f(o)) && void 0 !== r ? r : t.root_element) && void 0 !== s ? s : e; return w(a.querySelectorAll) ? ce(a.querySelectorAll(n)) : []; }, is: A, add: L, create: M, createHTML: (e, t, n = "") => { let o = "<" + e; for (const e in t)
            Se(t, e) && (o += " " + e + '="' + I(t[e]) + '"'); return tt(n) && _e(l.getVoidElements(), e) ? o + " />" : o + ">" + n + "</" + e + ">"; }, createFragment: t => { const n = e.createElement("div"), o = e.createDocumentFragment(); let r; for (o.appendChild(n), t && (n.innerHTML = t); r = n.firstChild;)
            o.appendChild(r); return o.removeChild(n), o; }, remove: F, setStyle: (e, n, o) => { B(e, (e => { const r = mn(e); ti(r, n, o), t.update_styles && ei(d, r); })); }, getStyle: k, setStyles: (e, n) => { B(e, (e => { const o = mn(e); pe(n, ((e, t) => { ti(o, t, e); })), t.update_styles && ei(d, o); })); }, removeAllAttribs: e => B(e, (e => { const t = e.attributes; for (let n = t.length - 1; n >= 0; n--)
            e.removeAttributeNode(t.item(n)); })), setAttrib: E, setAttribs: P, getAttrib: b, getPos: S, parseStyle: e => d.parse(e), serializeStyle: (e, t) => d.serialize(e, t), addStyle: t => { if (Y !== ni.DOM && e === document) {
            if (n[t])
                return;
            n[t] = !0;
        } let o = e.getElementById("mceDefaultStyles"); if (!o) {
            o = e.createElement("style"), o.id = "mceDefaultStyles", o.type = "text/css";
            const t = e.head;
            t.firstChild ? t.insertBefore(o, t.firstChild) : t.appendChild(o);
        } o.styleSheet ? o.styleSheet.cssText += t : o.appendChild(e.createTextNode(t)); }, loadCSS: e => { e || (e = ""), q(e.split(","), (e => { r[e] = !0, a.load(e).catch(_); })); }, addClass: (e, t) => { U(e, t, !0); }, removeClass: (e, t) => { U(e, t, !1); }, hasClass: (e, t) => { const n = h(e), o = t.split(" "); return C(n) && ne(o, (e => fr(n, e))); }, toggleClass: U, show: e => { B(e, (e => Uo(mn(e), "display"))); }, hide: e => { B(e, (e => Po(mn(e), "display", "none"))); }, isHidden: e => { const t = h(e); return C(t) && Fe(Io(t, "display"), "none"); }, uniqueId: e => (e || "mce_") + s++, setHTML: D, getOuterHTML: e => { const t = h(e); return C(t) ? qr(t.dom) ? t.dom.outerHTML : (e => { const t = cn("div"), n = mn(e.dom.cloneNode(!0)); return co(t, n), Ro(t); })(t) : ""; }, setOuterHTML: (e, t) => { B(e, (e => { qr(e) && (e.outerHTML = t); })); }, decode: ua.decode, encode: I, insertAfter: (e, t) => { const n = f(t); return B(e, (e => { const t = null == n ? void 0 : n.parentNode, o = null == n ? void 0 : n.nextSibling; return t && (o ? t.insertBefore(e, o) : t.appendChild(e)), e; })); }, replace: z, rename: (e, t) => { if (e.nodeName !== t.toUpperCase()) {
            const n = M(t);
            return Ka(v(e), (t => { E(n, t.nodeName, b(e, t.nodeName)); })), z(n, e, !0), n;
        } return e; }, findCommonAncestor: (e, t) => { let n = e; for (; n;) {
            let e = t;
            for (; e && n !== e;)
                e = e.parentNode;
            if (n === e)
                break;
            n = n.parentNode;
        } return !n && e.ownerDocument ? e.ownerDocument.documentElement : n; }, run: B, getAttribs: v, isEmpty: (e, t, n) => { if (g(t)) {
            const o = e => { const n = e.nodeName.toLowerCase(); return Boolean(t[n]); };
            return Es(l, e, { ...n, isContent: o });
        } return Es(l, e, n); }, createRng: j, nodeIndex: Ja, split: (e, t, n) => { let o, r, s = j(); if (e && t && e.parentNode && t.parentNode) {
            const a = e.parentNode;
            return s.setStart(a, Ja(e)), s.setEnd(t.parentNode, Ja(t)), o = s.extractContents(), s = j(), s.setStart(t.parentNode, Ja(t) + 1), s.setEnd(a, Ja(e) + 1), r = s.extractContents(), a.insertBefore(Zs(Y, o, l), e), n ? a.insertBefore(n, e) : a.insertBefore(t, e), a.insertBefore(Zs(Y, r, l), e), F(e), n || t;
        } }, bind: V, unbind: W, fire: (e, t, n) => c.dispatch(e, t, n), dispatch: (e, t, n) => c.dispatch(e, t, n), getContentEditable: K, getContentEditableParent: e => { const t = x(); let n = null; for (let o = e; o && o !== t && (n = K(o), null === n); o = o.parentNode)
            ; return n; }, isEditable: e => { if (C(e)) {
            const t = qr(e) ? e : e.parentElement;
            return C(t) && Wr(t) && gr(mn(t));
        } return !1; }, destroy: () => { if (i.length > 0) {
            let e = i.length;
            for (; e--;) {
                const [t, n, o] = i[e];
                c.unbind(t, n, o);
            }
        } pe(r, ((e, t) => { a.unload(t), delete r[t]; })); }, isChildOf: (e, t) => e === t || t.contains(e), dumpRng: e => "startContainer: " + e.startContainer.nodeName + ", startOffset: " + e.startOffset + ", endContainer: " + e.endContainer.nodeName + ", endOffset: " + e.endOffset }, X = ((e, t, n) => { const o = t.keep_values, r = { set: (e, o, r) => { const s = mn(e); w(t.url_converter) && C(o) && (o = t.url_converter.call(t.url_converter_scope || n(), String(o), r, e)), Za(s, "data-mce-" + r, o), Za(s, r, o); }, get: (e, t) => { const n = mn(e); return ho(n, "data-mce-" + t) || ho(n, t); } }, s = { style: { set: (t, n) => { const r = mn(t); o && Za(r, Xa, n), yo(r, "style"), m(n) && Do(r, e.parse(n)); }, get: t => { const n = mn(t), o = ho(n, Xa) || ho(n, "style"); return e.serialize(e.parse(o), xn(n)); } } }; return o && (s.href = s.src = r), s; })(d, t, N(Y)); return Y; };
    ni.DOM = ni(document), ni.nodeIndex = Ja;
    const oi = ni.DOM;
    class ri {
        constructor(e = {}) { this.states = {}, this.queue = [], this.scriptLoadedCallbacks = {}, this.queueLoadedCallbacks = [], this.loading = !1, this.settings = e; }
        _setReferrerPolicy(e) { this.settings.referrerPolicy = e; }
        loadScript(e) { return new Promise(((t, n) => { const o = oi; let r; const s = () => { o.remove(a), r && (r.onerror = r.onload = r = null); }, a = o.uniqueId(); r = document.createElement("script"), r.id = a, r.type = "text/javascript", r.src = an._addCacheSuffix(e), this.settings.referrerPolicy && o.setAttrib(r, "referrerpolicy", this.settings.referrerPolicy), r.onload = () => { s(), t(); }, r.onerror = () => { s(), n("Failed to load script: " + e); }, (document.getElementsByTagName("head")[0] || document.body).appendChild(r); })); }
        isDone(e) { return 2 === this.states[e]; }
        markDone(e) { this.states[e] = 2; }
        add(e) { const t = this; return t.queue.push(e), void 0 === t.states[e] && (t.states[e] = 0), new Promise(((n, o) => { t.scriptLoadedCallbacks[e] || (t.scriptLoadedCallbacks[e] = []), t.scriptLoadedCallbacks[e].push({ resolve: n, reject: o }); })); }
        load(e) { return this.add(e); }
        remove(e) { delete this.states[e], delete this.scriptLoadedCallbacks[e]; }
        loadQueue() { const e = this.queue; return this.queue = [], this.loadScripts(e); }
        loadScripts(e) { const t = this, n = (e, n) => { xe(t.scriptLoadedCallbacks, n).each((t => { q(t, (t => t[e](n))); })), delete t.scriptLoadedCallbacks[n]; }, o = e => { const t = Y(e, (e => "rejected" === e.status)); return t.length > 0 ? Promise.reject(te(t, (({ reason: e }) => p(e) ? e : [e]))) : Promise.resolve(); }, r = e => Promise.allSettled(V(e, (e => 2 === t.states[e] ? (n("resolve", e), Promise.resolve()) : 3 === t.states[e] ? (n("reject", e), Promise.reject(e)) : (t.states[e] = 1, t.loadScript(e).then((() => { t.states[e] = 2, n("resolve", e); const s = t.queue; return s.length > 0 ? (t.queue = [], r(s).then(o)) : Promise.resolve(); }), (() => (t.states[e] = 3, n("reject", e), Promise.reject(e)))))))), s = e => (t.loading = !0, r(e).then((e => { t.loading = !1; const n = t.queueLoadedCallbacks.shift(); return I.from(n).each(D), o(e); }))), a = dt(e); return t.loading ? new Promise(((e, n) => { t.queueLoadedCallbacks.push((() => { s(a).then(e, n); })); })) : s(a); }
    }
    ri.ScriptLoader = new ri;
    const si = {}, ai = Ne("en"), ii = () => xe(si, ai.get()), li = { getData: () => he(si, (e => ({ ...e }))), setCode: e => { e && ai.set(e); }, getCode: () => ai.get(), add: (e, t) => { let n = si[e]; n || (si[e] = n = {}); const o = V(fe(t), (e => e.toLowerCase())); pe(t, ((e, r) => { const s = r.toLowerCase(); s !== r && ((e, t) => { const n = e.indexOf(t); return -1 !== n && e.indexOf(t, n + 1) > n; })(o, s) ? (_e(t, s) || (n[s] = e), n[r] = e) : n[s] = e; })); }, translate: e => { const t = ii().getOr({}), n = e => w(e) ? Object.prototype.toString.call(e) : o(e) ? "" : "" + e, o = e => "" === e || null == e, r = e => { const o = n(e); return _e(t, o) ? n(t[o]) : xe(t, o.toLowerCase()).map(n).getOr(o); }, s = e => e.replace(/{context:\w+}$/, ""); if (o(e))
            return ""; if (f(a = e) && _e(a, "raw"))
            return n(e.raw); var a; if ((e => p(e) && e.length > 1)(e)) {
            const t = e.slice(1);
            return s(r(e[0]).replace(/\{([0-9]+)\}/g, ((e, o) => _e(t, o) ? n(t[o]) : e)));
        } return s(r(e)); }, isRtl: () => ii().bind((e => xe(e, "_dir"))).exists((e => "rtl" === e)), hasCode: e => _e(si, e) }, di = () => { const e = [], t = {}, n = {}, o = [], r = (e, t) => { const n = Y(o, (n => n.name === e && n.state === t)); q(n, (e => e.resolve())); }, s = e => _e(t, e), a = (e, n) => { const o = li.getCode(); !o || n && -1 === ("," + (n || "") + ",").indexOf("," + o + ",") || ri.ScriptLoader.add(t[e] + "/langs/" + o + ".js"); }, i = (e, t = "added") => "added" === t && (e => _e(n, e))(e) || "loaded" === t && s(e) ? Promise.resolve() : new Promise((n => { o.push({ name: e, state: t, resolve: n }); })); return { items: e, urls: t, lookup: n, get: e => { if (n[e])
            return n[e].instance; }, requireLangPack: (e, t) => { !1 !== di.languageLoad && (s(e) ? a(e, t) : i(e, "loaded").then((() => a(e, t)))); }, add: (t, o) => (e.push(o), n[t] = { instance: o }, r(t, "added"), o), remove: e => { delete t[e], delete n[e]; }, createUrl: (e, t) => m(t) ? m(e) ? { prefix: "", resource: t, suffix: "" } : { prefix: e.prefix, resource: t, suffix: e.suffix } : t, load: (e, o) => { if (t[e])
            return Promise.resolve(); let s = m(o) ? o : o.prefix + o.resource + o.suffix; 0 !== s.indexOf("/") && -1 === s.indexOf("://") && (s = di.baseURL + "/" + s), t[e] = s.substring(0, s.lastIndexOf("/")); const a = () => (r(e, "loaded"), Promise.resolve()); return n[e] ? a() : ri.ScriptLoader.add(s).then(a); }, waitFor: i }; };
    di.languageLoad = !0, di.baseURL = "", di.PluginManager = di(), di.ThemeManager = di(), di.ModelManager = di();
    const ci = N("mce-annotation"), ui = N("data-mce-annotation"), mi = N("data-mce-annotation-uid"), fi = N("data-mce-annotation-active"), gi = N("data-mce-annotation-classes"), pi = N("data-mce-annotation-attrs"), hi = e => t => Cn(t, e), bi = (e, t) => { const n = e.selection.getRng(), o = mn(n.startContainer), r = mn(e.getBody()), s = t.fold((() => "." + ci()), (e => `[${ui()}="${e}"]`)), a = $n(o, n.startOffset).getOr(o); return sr(a, s, hi(r)).bind((t => bo(t, `${mi()}`).bind((n => bo(t, `${ui()}`).map((t => { const o = yi(e, n); return { uid: n, name: t, elements: o }; })))))); }, vi = (e, t) => vo(e, "data-mce-bogus") || ((e, t, n) => or(e, '[data-mce-bogus="all"]', n).isSome())(e, 0, hi(t)), yi = (e, t) => { const n = mn(e.getBody()), o = vr(n, `[${mi()}="${t}"]`); return Y(o, (e => !vi(e, n))); }, Ci = (e, t) => { const n = mn(e.getBody()), o = vr(n, `[${ui()}="${t}"]`), r = {}; return q(o, (e => { if (!vi(e, n)) {
        const t = ho(e, mi()), n = xe(r, t).getOr([]);
        r[t] = n.concat([e]);
    } })), r; }, wi = (e, t, n = L) => { const o = new Fr(e, t), r = e => { let t; do {
        t = o[e]();
    } while (t && !es(t) && !n(t)); return I.from(t).filter(es); }; return { current: () => I.from(o.current()).filter(es), next: () => r("next"), prev: () => r("prev"), prev2: () => r("prev2") }; }, Ei = (e, t) => { const n = t || (t => e.isBlock(t) || as(t) || cs(t)), o = (e, t, n, r) => { if (es(e)) {
        const n = r(e, t, e.data);
        if (-1 !== n)
            return I.some({ container: e, offset: n });
    } return n().bind((e => o(e.container, e.offset, n, r))); }; return { backwards: (t, r, s, a) => { const i = wi(t, null != a ? a : e.getRoot(), n); return o(t, r, (() => i.prev().map((e => ({ container: e, offset: e.length })))), s).getOrNull(); }, forwards: (t, r, s, a) => { const i = wi(t, null != a ? a : e.getRoot(), n); return o(t, r, (() => i.next().map((e => ({ container: e, offset: 0 })))), s).getOrNull(); } }; }, xi = e => { let t; return n => (t = t || se(e, M), _e(t, xn(n))); }, _i = e => Nn(e) && "br" === xn(e), Si = xi(["h1", "h2", "h3", "h4", "h5", "h6", "p", "div", "address", "pre", "form", "blockquote", "center", "dir", "fieldset", "header", "footer", "article", "section", "hgroup", "aside", "nav", "figure"]), ki = xi(["ul", "ol", "dl"]), Ni = xi(["li", "dd", "dt"]), Ri = xi(["thead", "tbody", "tfoot"]), Ai = xi(["td", "th"]), Ti = xi(["pre", "script", "textarea", "style"]), Oi = () => { const e = cn("br"); return go(e, "data-mce-bogus", "1"), e; }, Bi = e => { wo(e), co(e, Oi()); }, Pi = at, Di = lt, Li = e => e.replace(/\uFEFF/g, ""), Mi = qr, Ii = es, Fi = e => (Ii(e) && (e = e.parentNode), Mi(e) && e.hasAttribute("data-mce-caret")), Ui = e => Ii(e) && Di(e.data), zi = e => Fi(e) || Ui(e), ji = e => e.firstChild !== e.lastChild || !as(e.firstChild), Hi = e => { const t = e.container(); return !!es(t) && (t.data.charAt(e.offset()) === Pi || e.isAtStart() && Ui(t.previousSibling)); }, $i = e => { const t = e.container(); return !!es(t) && (t.data.charAt(e.offset() - 1) === Pi || e.isAtEnd() && Ui(t.nextSibling)); }, Vi = e => Ii(e) && e.data[0] === Pi, qi = e => Ii(e) && e.data[e.data.length - 1] === Pi, Wi = e => e && e.hasAttribute("data-mce-caret") ? ((e => { var t; const n = e.getElementsByTagName("br"), o = n[n.length - 1]; Gr(o) && (null === (t = o.parentNode) || void 0 === t || t.removeChild(o)); })(e), e.removeAttribute("data-mce-caret"), e.removeAttribute("data-mce-bogus"), e.removeAttribute("style"), e.removeAttribute("data-mce-style"), e.removeAttribute("_moz_abspos"), e) : null, Ki = e => Fi(e.startContainer), Yi = Math.round, Xi = e => e ? { left: Yi(e.left), top: Yi(e.top), bottom: Yi(e.bottom), right: Yi(e.right), width: Yi(e.width), height: Yi(e.height) } : { left: 0, top: 0, bottom: 0, right: 0, width: 0, height: 0 }, Gi = (e, t) => (e = Xi(e), t || (e.left = e.left + e.width), e.right = e.left, e.width = 0, e), Zi = (e, t, n) => e >= 0 && e <= Math.min(t.height, n.height) / 2, Qi = (e, t) => { const n = Math.min(t.height / 2, e.height / 2); return e.bottom - n < t.top || !(e.top > t.bottom) && Zi(t.top - e.bottom, e, t); }, Ji = (e, t) => e.top > t.bottom || !(e.bottom < t.top) && Zi(t.bottom - e.top, e, t), el = (e, t, n) => { const o = Math.max(Math.min(t, e.left + e.width), e.left), r = Math.max(Math.min(n, e.top + e.height), e.top); return Math.sqrt((t - o) * (t - o) + (n - r) * (n - r)); }, tl = e => { const t = e.startContainer, n = e.startOffset; return t === e.endContainer && t.hasChildNodes() && e.endOffset === n + 1 ? t.childNodes[n] : null; }, nl = (e, t) => { if (qr(e) && e.hasChildNodes()) {
        const n = e.childNodes, o = ((e, t, n) => Math.min(Math.max(e, 0), n))(t, 0, n.length - 1);
        return n[o];
    } return e; }, ol = new RegExp("[\u0300-\u036f\u0483-\u0487\u0488-\u0489\u0591-\u05bd\u05bf\u05c1-\u05c2\u05c4-\u05c5\u05c7\u0610-\u061a\u064b-\u065f\u0670\u06d6-\u06dc\u06df-\u06e4\u06e7-\u06e8\u06ea-\u06ed\u0711\u0730-\u074a\u07a6-\u07b0\u07eb-\u07f3\u0816-\u0819\u081b-\u0823\u0825-\u0827\u0829-\u082d\u0859-\u085b\u08e3-\u0902\u093a\u093c\u0941-\u0948\u094d\u0951-\u0957\u0962-\u0963\u0981\u09bc\u09be\u09c1-\u09c4\u09cd\u09d7\u09e2-\u09e3\u0a01-\u0a02\u0a3c\u0a41-\u0a42\u0a47-\u0a48\u0a4b-\u0a4d\u0a51\u0a70-\u0a71\u0a75\u0a81-\u0a82\u0abc\u0ac1-\u0ac5\u0ac7-\u0ac8\u0acd\u0ae2-\u0ae3\u0b01\u0b3c\u0b3e\u0b3f\u0b41-\u0b44\u0b4d\u0b56\u0b57\u0b62-\u0b63\u0b82\u0bbe\u0bc0\u0bcd\u0bd7\u0c00\u0c3e-\u0c40\u0c46-\u0c48\u0c4a-\u0c4d\u0c55-\u0c56\u0c62-\u0c63\u0c81\u0cbc\u0cbf\u0cc2\u0cc6\u0ccc-\u0ccd\u0cd5-\u0cd6\u0ce2-\u0ce3\u0d01\u0d3e\u0d41-\u0d44\u0d4d\u0d57\u0d62-\u0d63\u0dca\u0dcf\u0dd2-\u0dd4\u0dd6\u0ddf\u0e31\u0e34-\u0e3a\u0e47-\u0e4e\u0eb1\u0eb4-\u0eb9\u0ebb-\u0ebc\u0ec8-\u0ecd\u0f18-\u0f19\u0f35\u0f37\u0f39\u0f71-\u0f7e\u0f80-\u0f84\u0f86-\u0f87\u0f8d-\u0f97\u0f99-\u0fbc\u0fc6\u102d-\u1030\u1032-\u1037\u1039-\u103a\u103d-\u103e\u1058-\u1059\u105e-\u1060\u1071-\u1074\u1082\u1085-\u1086\u108d\u109d\u135d-\u135f\u1712-\u1714\u1732-\u1734\u1752-\u1753\u1772-\u1773\u17b4-\u17b5\u17b7-\u17bd\u17c6\u17c9-\u17d3\u17dd\u180b-\u180d\u18a9\u1920-\u1922\u1927-\u1928\u1932\u1939-\u193b\u1a17-\u1a18\u1a1b\u1a56\u1a58-\u1a5e\u1a60\u1a62\u1a65-\u1a6c\u1a73-\u1a7c\u1a7f\u1ab0-\u1abd\u1abe\u1b00-\u1b03\u1b34\u1b36-\u1b3a\u1b3c\u1b42\u1b6b-\u1b73\u1b80-\u1b81\u1ba2-\u1ba5\u1ba8-\u1ba9\u1bab-\u1bad\u1be6\u1be8-\u1be9\u1bed\u1bef-\u1bf1\u1c2c-\u1c33\u1c36-\u1c37\u1cd0-\u1cd2\u1cd4-\u1ce0\u1ce2-\u1ce8\u1ced\u1cf4\u1cf8-\u1cf9\u1dc0-\u1df5\u1dfc-\u1dff\u200c-\u200d\u20d0-\u20dc\u20dd-\u20e0\u20e1\u20e2-\u20e4\u20e5-\u20f0\u2cef-\u2cf1\u2d7f\u2de0-\u2dff\u302a-\u302d\u302e-\u302f\u3099-\u309a\ua66f\ua670-\ua672\ua674-\ua67d\ua69e-\ua69f\ua6f0-\ua6f1\ua802\ua806\ua80b\ua825-\ua826\ua8c4\ua8e0-\ua8f1\ua926-\ua92d\ua947-\ua951\ua980-\ua982\ua9b3\ua9b6-\ua9b9\ua9bc\ua9e5\uaa29-\uaa2e\uaa31-\uaa32\uaa35-\uaa36\uaa43\uaa4c\uaa7c\uaab0\uaab2-\uaab4\uaab7-\uaab8\uaabe-\uaabf\uaac1\uaaec-\uaaed\uaaf6\uabe5\uabe8\uabed\ufb1e\ufe00-\ufe0f\ufe20-\ufe2f\uff9e-\uff9f]"), rl = e => m(e) && e.charCodeAt(0) >= 768 && ol.test(e), sl = ds, al = cs, il = as, ll = es, dl = Yr(["script", "style", "textarea"]), cl = Yr(["img", "input", "textarea", "hr", "iframe", "video", "audio", "object", "embed"]), ul = Yr(["table"]), ml = zi, fl = e => !ml(e) && (ll(e) ? !dl(e.parentNode) : cl(e) || il(e) || ul(e) || gl(e)), gl = e => !(e => qr(e) && "true" === e.getAttribute("unselectable"))(e) && al(e), pl = (e, t) => fl(e) && ((e, t) => { for (let n = e.parentNode; n && n !== t; n = n.parentNode) {
        if (gl(n))
            return !1;
        if (sl(n))
            return !0;
    } return !0; })(e, t), hl = qr, bl = fl, vl = Xr("display", "block table"), yl = Xr("float", "left right"), Cl = ((...e) => t => { for (let n = 0; n < e.length; n++)
        if (!e[n](t))
            return !1; return !0; })(hl, bl, O(yl)), wl = O(Xr("white-space", "pre pre-line pre-wrap")), El = es, xl = as, _l = ni.nodeIndex, Sl = (e, t) => t < 0 && qr(e) && e.hasChildNodes() ? void 0 : nl(e, t), kl = e => e ? e.createRange() : ni.DOM.createRng(), Nl = e => m(e) && /[\r\n\t ]/.test(e), Rl = e => !!e.setStart && !!e.setEnd, Al = e => { const t = e.startContainer, n = e.startOffset; if (Nl(e.toString()) && wl(t.parentNode) && es(t)) {
        const e = t.data;
        if (Nl(e[n - 1]) || Nl(e[n + 1]))
            return !0;
    } return !1; }, Tl = e => 0 === e.left && 0 === e.right && 0 === e.top && 0 === e.bottom, Ol = e => { var t; let n; const o = e.getClientRects(); return n = o.length > 0 ? Xi(o[0]) : Xi(e.getBoundingClientRect()), !Rl(e) && xl(e) && Tl(n) ? (e => { const t = e.ownerDocument, n = kl(t), o = t.createTextNode(it), r = e.parentNode; r.insertBefore(o, e), n.setStart(o, 0), n.setEnd(o, 1); const s = Xi(n.getBoundingClientRect()); return r.removeChild(o), s; })(e) : Tl(n) && Rl(e) && null !== (t = (e => { const t = e.startContainer, n = e.endContainer, o = e.startOffset, r = e.endOffset; if (t === n && es(n) && 0 === o && 1 === r) {
        const t = e.cloneRange();
        return t.setEndAfter(n), Ol(t);
    } return null; })(e)) && void 0 !== t ? t : n; }, Bl = (e, t) => { const n = Gi(e, t); return n.width = 1, n.right = n.left + 1, n; }, Pl = (e, t, n) => { const o = () => (n || (n = (e => { const t = [], n = e => { var n, o; 0 !== e.height && (t.length > 0 && (n = e, o = t[t.length - 1], n.left === o.left && n.top === o.top && n.bottom === o.bottom && n.right === o.right) || t.push(e)); }, o = (e, t) => { const o = kl(e.ownerDocument); if (t < e.data.length) {
        if (rl(e.data[t]))
            return;
        if (rl(e.data[t - 1]) && (o.setStart(e, t), o.setEnd(e, t + 1), !Al(o)))
            return void n(Bl(Ol(o), !1));
    } t > 0 && (o.setStart(e, t - 1), o.setEnd(e, t), Al(o) || n(Bl(Ol(o), !1))), t < e.data.length && (o.setStart(e, t), o.setEnd(e, t + 1), Al(o) || n(Bl(Ol(o), !0))); }, r = e.container(), s = e.offset(); if (El(r))
        return o(r, s), t; if (hl(r))
        if (e.isAtEnd()) {
            const e = Sl(r, s);
            El(e) && o(e, e.data.length), Cl(e) && !xl(e) && n(Bl(Ol(e), !1));
        }
        else {
            const a = Sl(r, s);
            if (El(a) && o(a, 0), Cl(a) && e.isAtEnd())
                return n(Bl(Ol(a), !1)), t;
            const i = Sl(e.container(), e.offset() - 1);
            Cl(i) && !xl(i) && (vl(i) || vl(a) || !Cl(a)) && n(Bl(Ol(i), !1)), Cl(a) && n(Bl(Ol(a), !0));
        } return t; })(Pl(e, t))), n); return { container: N(e), offset: N(t), toRange: () => { const n = kl(e.ownerDocument); return n.setStart(e, t), n.setEnd(e, t), n; }, getClientRects: o, isVisible: () => o().length > 0, isAtStart: () => (El(e), 0 === t), isAtEnd: () => El(e) ? t >= e.data.length : t >= e.childNodes.length, isEqual: n => n && e === n.container() && t === n.offset(), getNode: n => Sl(e, n ? t - 1 : t) }; };
    Pl.fromRangeStart = e => Pl(e.startContainer, e.startOffset), Pl.fromRangeEnd = e => Pl(e.endContainer, e.endOffset), Pl.after = e => Pl(e.parentNode, _l(e) + 1), Pl.before = e => Pl(e.parentNode, _l(e)), Pl.isAbove = (e, t) => ze(le(t.getClientRects()), de(e.getClientRects()), Qi).getOr(!1), Pl.isBelow = (e, t) => ze(de(t.getClientRects()), le(e.getClientRects()), Ji).getOr(!1), Pl.isAtStart = e => !!e && e.isAtStart(), Pl.isAtEnd = e => !!e && e.isAtEnd(), Pl.isTextPosition = e => !!e && es(e.container()), Pl.isElementPosition = e => !Pl.isTextPosition(e);
    const Dl = (e, t) => { es(t) && 0 === t.data.length && e.remove(t); }, Ll = (e, t, n) => { ss(n) ? ((e, t, n) => { const o = I.from(n.firstChild), r = I.from(n.lastChild); t.insertNode(n), o.each((t => Dl(e, t.previousSibling))), r.each((t => Dl(e, t.nextSibling))); })(e, t, n) : ((e, t, n) => { t.insertNode(n), Dl(e, n.previousSibling), Dl(e, n.nextSibling); })(e, t, n); }, Ml = es, Il = Gr, Fl = ni.nodeIndex, Ul = e => { const t = e.parentNode; return Il(t) ? Ul(t) : t; }, zl = e => e ? pt(e.childNodes, ((e, t) => (Il(t) && "BR" !== t.nodeName ? e = e.concat(zl(t)) : e.push(t), e)), []) : [], jl = e => t => e === t, Hl = e => (Ml(e) ? "text()" : e.nodeName.toLowerCase()) + "[" + (e => { let t, n; t = zl(Ul(e)), n = ht(t, jl(e), e), t = t.slice(0, n + 1); const o = pt(t, ((e, n, o) => (Ml(n) && Ml(t[o - 1]) && e++, e)), 0); return t = gt(t, Yr([e.nodeName])), n = ht(t, jl(e), e), n - o; })(e) + "]", $l = (e, t) => { let n, o = [], r = t.container(), s = t.offset(); if (Ml(r))
        n = ((e, t) => { let n = e; for (; (n = n.previousSibling) && Ml(n);)
            t += n.data.length; return t; })(r, s);
    else {
        const e = r.childNodes;
        s >= e.length ? (n = "after", s = e.length - 1) : n = "before", r = e[s];
    } o.push(Hl(r)); let a = ((e, t) => { const n = []; for (let o = t.parentNode; o && o !== e; o = o.parentNode)
        n.push(o); return n; })(e, r); return a = gt(a, O(Gr)), o = o.concat(ft(a, (e => Hl(e)))), o.reverse().join("/") + "," + n; }, Vl = (e, t) => { if (!t)
        return null; const n = t.split(","), o = n[0].split("/"), r = n.length > 1 ? n[1] : "before", s = pt(o, ((e, t) => { const n = /([\w\-\(\)]+)\[([0-9]+)\]/.exec(t); return n ? ("text()" === n[1] && (n[1] = "#text"), ((e, t, n) => { let o = zl(e); return o = gt(o, ((e, t) => !Ml(e) || !Ml(o[t - 1]))), o = gt(o, Yr([t])), o[n]; })(e, n[1], parseInt(n[2], 10))) : null; }), e); if (!s)
        return null; if (!Ml(s) && s.parentNode) {
        let e;
        return e = "after" === r ? Fl(s) + 1 : Fl(s), Pl(s.parentNode, e);
    } return ((e, t) => { let n = e, o = 0; for (; Ml(n);) {
        const r = n.data.length;
        if (t >= o && t <= o + r) {
            e = n, t -= o;
            break;
        }
        if (!Ml(n.nextSibling)) {
            e = n, t = r;
            break;
        }
        o += r, n = n.nextSibling;
    } return Ml(e) && t > e.data.length && (t = e.data.length), Pl(e, t); })(s, parseInt(r, 10)); }, ql = cs, Wl = (e, t, n, o, r) => { const s = r ? o.startContainer : o.endContainer; let a = r ? o.startOffset : o.endOffset; const i = [], l = e.getRoot(); if (es(s))
        i.push(n ? ((e, t, n) => { let o = e(t.data.slice(0, n)).length; for (let n = t.previousSibling; n && es(n); n = n.previousSibling)
            o += e(n.data).length; return o; })(t, s, a) : a);
    else {
        let t = 0;
        const o = s.childNodes;
        a >= o.length && o.length && (t = 1, a = Math.max(0, o.length - 1)), i.push(e.nodeIndex(o[a], n) + t);
    } for (let t = s; t && t !== l; t = t.parentNode)
        i.push(e.nodeIndex(t, n)); return i; }, Kl = (e, t, n) => { let o = 0; return an.each(e.select(t), (e => "all" === e.getAttribute("data-mce-bogus") ? void 0 : e !== n && void o++)), o; }, Yl = (e, t) => { let n = t ? e.startContainer : e.endContainer, o = t ? e.startOffset : e.endOffset; if (qr(n) && "TR" === n.nodeName) {
        const r = n.childNodes;
        n = r[Math.min(t ? o : o - 1, r.length - 1)], n && (o = t ? 0 : n.childNodes.length, t ? e.setStart(n, o) : e.setEnd(n, o));
    } }, Xl = e => (Yl(e, !0), Yl(e, !1), e), Gl = (e, t) => { if (qr(e) && (e = nl(e, t), ql(e)))
        return e; if (zi(e)) {
        es(e) && Fi(e) && (e = e.parentNode);
        let t = e.previousSibling;
        if (ql(t))
            return t;
        if (t = e.nextSibling, ql(t))
            return t;
    } }, Zl = (e, t, n) => { const o = n.getNode(), r = n.getRng(); if ("IMG" === o.nodeName || ql(o)) {
        const e = o.nodeName;
        return { name: e, index: Kl(n.dom, e, o) };
    } const s = (e => Gl(e.startContainer, e.startOffset) || Gl(e.endContainer, e.endOffset))(r); if (s) {
        const e = s.tagName;
        return { name: e, index: Kl(n.dom, e, s) };
    } return ((e, t, n, o) => { const r = t.dom, s = Wl(r, e, n, o, !0), a = t.isForward(), i = Ki(o) ? { isFakeCaret: !0 } : {}; return t.isCollapsed() ? { start: s, forward: a, ...i } : { start: s, end: Wl(r, e, n, o, !1), forward: a, ...i }; })(e, n, t, r); }, Ql = (e, t, n) => { const o = { "data-mce-type": "bookmark", id: t, style: "overflow:hidden;line-height:0px" }; return n ? e.create("span", o, "&#xFEFF;") : e.create("span", o); }, Jl = (e, t) => { const n = e.dom; let o = e.getRng(); const r = n.uniqueId(), s = e.isCollapsed(), a = e.getNode(), i = a.nodeName, l = e.isForward(); if ("IMG" === i)
        return { name: i, index: Kl(n, i, a) }; const d = Xl(o.cloneRange()); if (!s) {
        d.collapse(!1);
        const e = Ql(n, r + "_end", t);
        Ll(n, d, e);
    } o = Xl(o), o.collapse(!0); const c = Ql(n, r + "_start", t); return Ll(n, o, c), e.moveToBookmark({ id: r, keep: !0, forward: l }), { id: r, forward: l }; }, ed = T(Zl, R, !0), td = e => "inline-command" === e.type || "inline-format" === e.type, nd = e => "block-command" === e.type || "block-format" === e.type, od = e => { var t; const n = t => Te.error({ message: t, pattern: e }), o = (t, o, r) => { if (void 0 !== e.format) {
        let r;
        if (p(e.format)) {
            if (!ne(e.format, m))
                return n(t + " pattern has non-string items in the `format` array");
            r = e.format;
        }
        else {
            if (!m(e.format))
                return n(t + " pattern has non-string `format` parameter");
            r = [e.format];
        }
        return Te.value(o(r));
    } return void 0 !== e.cmd ? m(e.cmd) ? Te.value(r(e.cmd, e.value)) : n(t + " pattern has non-string `cmd` parameter") : n(t + " pattern is missing both `format` and `cmd` parameters"); }; if (!f(e))
        return n("Raw pattern is not an object"); if (!m(e.start))
        return n("Raw pattern is missing `start` parameter"); if (void 0 !== e.end) {
        if (!m(e.end))
            return n("Inline pattern has non-string `end` parameter");
        if (0 === e.start.length && 0 === e.end.length)
            return n("Inline pattern has empty `start` and `end` parameters");
        let t = e.start, r = e.end;
        return 0 === r.length && (r = t, t = ""), o("Inline", (e => ({ type: "inline-format", start: t, end: r, format: e })), ((e, n) => ({ type: "inline-command", start: t, end: r, cmd: e, value: n })));
    } if (void 0 !== e.replacement)
        return m(e.replacement) ? 0 === e.start.length ? n("Replacement pattern has empty `start` parameter") : Te.value({ type: "inline-command", start: "", end: e.start, cmd: "mceInsertContent", value: e.replacement }) : n("Replacement pattern has non-string `replacement` parameter"); {
        const r = null !== (t = e.trigger) && void 0 !== t ? t : "space";
        return 0 === e.start.length ? n("Block pattern has empty `start` parameter") : o("Block", (t => ({ type: "block-format", start: e.start, format: t[0], trigger: r })), ((t, n) => ({ type: "block-command", start: e.start, cmd: t, value: n, trigger: r })));
    } }, rd = e => Y(e, nd), sd = e => Y(e, td), ad = (e, t) => ({ ...e, blockPatterns: Y(e.blockPatterns, (e => ((e, t) => ("block-command" === e.type || "block-format" === e.type) && e.trigger === t)(e, t))) }), id = e => { const t = (e => { const t = [], n = []; return q(e, (e => { e.fold((e => { t.push(e); }), (e => { n.push(e); })); })), { errors: t, values: n }; })(V(e, od)); return q(t.errors, (e => console.error(e.message, e.pattern))), t.values; }, ld = (e, t, n) => { e.dispatch(t, n); }, dd = (e, t, n, o) => { e.dispatch("FormatApply", { format: t, node: n, vars: o }); }, cd = (e, t, n, o) => { e.dispatch("FormatRemove", { format: t, node: n, vars: o }); }, ud = (e, t) => e.dispatch("SetContent", t), md = (e, t) => e.dispatch("GetContent", t), fd = (e, t) => { e.dispatch("AutocompleterUpdateActiveRange", t); }, gd = (e, t) => e.dispatch("PastePlainTextToggle", { state: t }), pd = Kt().deviceType, hd = pd.isTouch(), bd = ni.DOM, vd = e => u(e, RegExp), yd = e => t => t.options.get(e), Cd = e => m(e) || f(e), wd = (e, t = "") => n => { const o = m(n); if (o) {
        if (-1 !== n.indexOf("=")) {
            const r = (e => { const t = e.indexOf("=") > 0 ? e.split(/[;,](?![^=;,]*(?:[;,]|$))/) : e.split(","); return G(t, ((e, t) => { const n = t.split("="), o = n[0], r = n.length > 1 ? n[1] : o; return e[Ze(o)] = Ze(r), e; }), {}); })(n);
            return { value: xe(r, e.id).getOr(t), valid: o };
        }
        return { value: n, valid: o };
    } return { valid: !1, message: "Must be a string." }; }, Ed = yd("iframe_attrs"), xd = yd("doctype"), _d = yd("document_base_url"), Sd = yd("body_id"), kd = yd("body_class"), Nd = yd("content_security_policy"), Rd = yd("br_in_pre"), Ad = yd("forced_root_block"), Td = yd("forced_root_block_attrs"), Od = yd("newline_behavior"), Bd = yd("br_newline_selector"), Pd = yd("no_newline_selector"), Dd = yd("keep_styles"), Ld = yd("end_container_on_empty_block"), Md = yd("automatic_uploads"), Id = yd("images_reuse_filename"), Fd = yd("images_replace_blob_uris"), Ud = yd("icons"), zd = yd("icons_url"), jd = yd("images_upload_url"), Hd = yd("images_upload_base_path"), $d = yd("images_upload_credentials"), Vd = yd("images_upload_handler"), qd = yd("content_css_cors"), Wd = yd("referrer_policy"), Kd = yd("language"), Yd = yd("language_url"), Xd = yd("indent_use_margin"), Gd = yd("indentation"), Zd = yd("content_css"), Qd = yd("content_style"), Jd = yd("font_css"), ec = yd("directionality"), tc = yd("inline_boundaries_selector"), nc = yd("object_resizing"), oc = yd("resize_img_proportional"), rc = yd("placeholder"), sc = yd("event_root"), ac = yd("service_message"), ic = yd("theme"), lc = yd("theme_url"), dc = yd("model"), cc = yd("model_url"), uc = yd("inline_boundaries"), mc = yd("formats"), fc = yd("preview_styles"), gc = yd("format_empty_lines"), pc = yd("format_noneditable_selector"), hc = yd("custom_ui_selector"), bc = yd("inline"), vc = yd("hidden_input"), yc = yd("submit_patch"), Cc = yd("add_form_submit_trigger"), wc = yd("add_unload_trigger"), Ec = yd("custom_undo_redo_levels"), xc = yd("disable_nodechange"), _c = yd("readonly"), Sc = yd("editable_root"), kc = yd("content_css_cors"), Nc = yd("plugins"), Rc = yd("external_plugins"), Ac = yd("block_unsupported_drop"), Tc = yd("visual"), Oc = yd("visual_table_class"), Bc = yd("visual_anchor_class"), Pc = yd("iframe_aria_text"), Dc = yd("setup"), Lc = yd("init_instance_callback"), Mc = yd("urlconverter_callback"), Ic = yd("auto_focus"), Fc = yd("browser_spellcheck"), Uc = yd("protect"), zc = yd("paste_block_drop"), jc = yd("paste_data_images"), Hc = yd("paste_preprocess"), $c = yd("paste_postprocess"), Vc = yd("newdocument_content"), qc = yd("paste_webkit_styles"), Wc = yd("paste_remove_styles_if_webkit"), Kc = yd("paste_merge_formats"), Yc = yd("smart_paste"), Xc = yd("paste_as_text"), Gc = yd("paste_tab_spaces"), Zc = yd("allow_html_data_urls"), Qc = yd("text_patterns"), Jc = yd("text_patterns_lookup"), eu = yd("noneditable_class"), tu = yd("editable_class"), nu = yd("noneditable_regexp"), ou = yd("preserve_cdata"), ru = yd("highlight_on_focus"), su = yd("xss_sanitization"), au = yd("init_content_sync"), iu = e => an.explode(e.options.get("images_file_types")), lu = yd("table_tab_navigation"), du = yd("details_initial_state"), cu = yd("details_serialized_state"), uu = yd("sandbox_iframes"), mu = e => e.options.get("sandbox_iframes_exclusions"), fu = yd("convert_unsafe_embeds"), gu = yd("license_key"), pu = yd("api_key"), hu = yd("disabled"), bu = qr, vu = es, yu = e => { const t = e.parentNode; t && t.removeChild(e); }, Cu = e => { const t = Li(e); return { count: e.length - t.length, text: t }; }, wu = e => { let t; for (; -1 !== (t = e.data.lastIndexOf(Pi));)
        e.deleteData(t, 1); }, Eu = (e, t) => (_u(e), t), xu = (e, t) => Pl.isTextPosition(t) ? ((e, t) => vu(e) && t.container() === e ? ((e, t) => { const n = Cu(e.data.substr(0, t.offset())), o = Cu(e.data.substr(t.offset())); return (n.text + o.text).length > 0 ? (wu(e), Pl(e, t.offset() - n.count)) : t; })(e, t) : Eu(e, t))(e, t) : ((e, t) => t.container() === e.parentNode ? ((e, t) => { const n = t.container(), o = ((e, t) => { const n = j(e, t); return -1 === n ? I.none() : I.some(n); })(ce(n.childNodes), e).map((e => e < t.offset() ? Pl(n, t.offset() - 1) : t)).getOr(t); return _u(e), o; })(e, t) : Eu(e, t))(e, t), _u = e => { bu(e) && zi(e) && (ji(e) ? e.removeAttribute("data-mce-caret") : yu(e)), vu(e) && (wu(e), 0 === e.data.length && yu(e)); }, Su = cs, ku = gs, Nu = ms, Ru = (e, t, n) => { const o = Gi(t.getBoundingClientRect(), n); let r, s; if ("BODY" === e.tagName) {
        const t = e.ownerDocument.documentElement;
        r = e.scrollLeft || t.scrollLeft, s = e.scrollTop || t.scrollTop;
    }
    else {
        const t = e.getBoundingClientRect();
        r = e.scrollLeft - t.left, s = e.scrollTop - t.top;
    } o.left += r, o.right += r, o.top += s, o.bottom += s, o.width = 1; let a = t.offsetWidth - t.clientWidth; return a > 0 && (n && (a *= -1), o.left += a, o.right += a), o; }, Au = (e, t, n, o) => { const r = Ve(); let s, a; const i = Ad(e), l = e.dom, d = () => { (e => { var t, n; const o = vr(mn(e), "*[contentEditable=false],video,audio,embed,object"); for (let e = 0; e < o.length; e++) {
        const r = o[e].dom;
        let s = r.previousSibling;
        if (qi(s)) {
            const e = s.data;
            1 === e.length ? null === (t = s.parentNode) || void 0 === t || t.removeChild(s) : s.deleteData(e.length - 1, 1);
        }
        s = r.nextSibling, Vi(s) && (1 === s.data.length ? null === (n = s.parentNode) || void 0 === n || n.removeChild(s) : s.deleteData(0, 1));
    } })(t), a && (_u(a), a = null), r.on((e => { l.remove(e.caret), r.clear(); })), s && (clearInterval(s), s = void 0); }; return { isShowing: r.isSet, show: (e, c) => { let u; if (d(), Nu(c))
            return null; if (!n(c))
            return a = ((e, t) => { var n; const o = (null !== (n = e.ownerDocument) && void 0 !== n ? n : document).createTextNode(Pi), r = e.parentNode; if (t) {
                const t = e.previousSibling;
                if (Ii(t)) {
                    if (zi(t))
                        return t;
                    if (qi(t))
                        return t.splitText(t.data.length - 1);
                }
                null == r || r.insertBefore(o, e);
            }
            else {
                const t = e.nextSibling;
                if (Ii(t)) {
                    if (zi(t))
                        return t;
                    if (Vi(t))
                        return t.splitText(1), t;
                }
                e.nextSibling ? null == r || r.insertBefore(o, e.nextSibling) : null == r || r.appendChild(o);
            } return o; })(c, e), u = c.ownerDocument.createRange(), Ou(a.nextSibling) ? (u.setStart(a, 0), u.setEnd(a, 0)) : (u.setStart(a, 1), u.setEnd(a, 1)), u; {
            const n = ((e, t, n) => { var o; const r = (null !== (o = t.ownerDocument) && void 0 !== o ? o : document).createElement(e); r.setAttribute("data-mce-caret", n ? "before" : "after"), r.setAttribute("data-mce-bogus", "all"), r.appendChild(Oi().dom); const s = t.parentNode; return n ? null == s || s.insertBefore(r, t) : t.nextSibling ? null == s || s.insertBefore(r, t.nextSibling) : null == s || s.appendChild(r), r; })(i, c, e), d = Ru(t, c, e);
            l.setStyle(n, "top", d.top), l.setStyle(n, "caret-color", "transparent"), a = n;
            const m = l.create("div", { class: "mce-visual-caret", "data-mce-bogus": "all" });
            l.setStyles(m, { ...d }), l.add(t, m), r.set({ caret: m, element: c, before: e }), e && l.addClass(m, "mce-visual-caret-before"), s = window.setInterval((() => { r.on((e => { o() ? l.toggleClass(e.caret, "mce-visual-caret-hidden") : l.addClass(e.caret, "mce-visual-caret-hidden"); })); }), 500), u = c.ownerDocument.createRange(), u.setStart(n, 0), u.setEnd(n, 0);
        } return u; }, hide: d, getCss: () => ".mce-visual-caret {position: absolute;background-color: black;background-color: currentcolor;}.mce-visual-caret-hidden {display: none;}*[data-mce-caret] {position: absolute;left: -1000px;right: auto;top: 0;margin: 0;padding: 0;}", reposition: () => { r.on((e => { const n = Ru(t, e.element, e.before); l.setStyles(e.caret, { ...n }); })); }, destroy: () => clearInterval(s) }; }, Tu = () => nn.browser.isFirefox(), Ou = e => Su(e) || ku(e), Bu = e => (Ou(e) || Zr(e) && Tu()) && Mn(mn(e)).exists(gr), Pu = ds, Du = cs, Lu = gs, Mu = Xr("display", "block table table-cell table-row table-caption list-item"), Iu = zi, Fu = Fi, Uu = qr, zu = es, ju = fl, Hu = e => 1 === e, $u = e => -1 === e, Vu = (e, t) => { let n; for (; n = e(t);)
        if (!Fu(n))
            return n; return null; }, qu = (e, t, n, o, r) => { const s = new Fr(e, o), a = Du(e) || Fu(e); let i; if ($u(t)) {
        if (a && (i = Vu(s.prev.bind(s), !0), n(i)))
            return i;
        for (; i = Vu(s.prev.bind(s), r);)
            if (n(i))
                return i;
    } if (Hu(t)) {
        if (a && (i = Vu(s.next.bind(s), !0), n(i)))
            return i;
        for (; i = Vu(s.next.bind(s), r);)
            if (n(i))
                return i;
    } return null; }, Wu = (e, t) => { for (; e && e !== t;) {
        if (Mu(e))
            return e;
        e = e.parentNode;
    } return null; }, Ku = (e, t, n) => Wu(e.container(), n) === Wu(t.container(), n), Yu = (e, t) => { if (!t)
        return I.none(); const n = t.container(), o = t.offset(); return Uu(n) ? I.from(n.childNodes[o + e]) : I.none(); }, Xu = (e, t) => { var n; const o = (null !== (n = t.ownerDocument) && void 0 !== n ? n : document).createRange(); return e ? (o.setStartBefore(t), o.setEndBefore(t)) : (o.setStartAfter(t), o.setEndAfter(t)), o; }, Gu = (e, t, n) => Wu(t, e) === Wu(n, e), Zu = (e, t, n) => { const o = e ? "previousSibling" : "nextSibling"; let r = n; for (; r && r !== t;) {
        let e = r[o];
        if (e && Iu(e) && (e = e[o]), Du(e) || Lu(e)) {
            if (Gu(t, e, r))
                return e;
            break;
        }
        if (ju(e))
            break;
        r = r.parentNode;
    } return null; }, Qu = T(Xu, !0), Ju = T(Xu, !1), em = (e, t, n) => { let o; const r = T(Zu, !0, t), s = T(Zu, !1, t), a = n.startContainer, i = n.startOffset; if (Fi(a)) {
        const e = zu(a) ? a.parentNode : a, t = e.getAttribute("data-mce-caret");
        if ("before" === t && (o = e.nextSibling, Bu(o)))
            return Qu(o);
        if ("after" === t && (o = e.previousSibling, Bu(o)))
            return Ju(o);
    } if (!n.collapsed)
        return n; if (es(a)) {
        if (Iu(a)) {
            if (1 === e) {
                if (o = s(a), o)
                    return Qu(o);
                if (o = r(a), o)
                    return Ju(o);
            }
            if (-1 === e) {
                if (o = r(a), o)
                    return Ju(o);
                if (o = s(a), o)
                    return Qu(o);
            }
            return n;
        }
        if (qi(a) && i >= a.data.length - 1)
            return 1 === e && (o = s(a), o) ? Qu(o) : n;
        if (Vi(a) && i <= 1)
            return -1 === e && (o = r(a), o) ? Ju(o) : n;
        if (i === a.data.length)
            return o = s(a), o ? Qu(o) : n;
        if (0 === i)
            return o = r(a), o ? Ju(o) : n;
    } return n; }, tm = (e, t) => Yu(e ? 0 : -1, t).filter(Du), nm = (e, t, n) => { const o = em(e, t, n); return -1 === e ? Pl.fromRangeStart(o) : Pl.fromRangeEnd(o); }, om = e => I.from(e.getNode()).map(mn), rm = (e, t) => { let n = t; for (; n = e(n);)
        if (n.isVisible())
            return n; return n; }, sm = (e, t) => { const n = Ku(e, t); return !(n || !as(e.getNode())) || n; }, am = cs, im = es, lm = qr, dm = as, cm = fl, um = e => cl(e) || (e => !!gl(e) && !G(ce(e.getElementsByTagName("*")), ((e, t) => e || sl(t)), !1))(e), mm = pl, fm = (e, t) => e.hasChildNodes() && t < e.childNodes.length ? e.childNodes[t] : null, gm = (e, t) => { if (Hu(e)) {
        if (cm(t.previousSibling) && !im(t.previousSibling))
            return Pl.before(t);
        if (im(t))
            return Pl(t, 0);
    } if ($u(e)) {
        if (cm(t.nextSibling) && !im(t.nextSibling))
            return Pl.after(t);
        if (im(t))
            return Pl(t, t.data.length);
    } return $u(e) ? dm(t) ? Pl.before(t) : Pl.after(t) : Pl.before(t); }, pm = (e, t, n) => { let o, r, s, a; if (!lm(n) || !t)
        return null; if (t.isEqual(Pl.after(n)) && n.lastChild) {
        if (a = Pl.after(n.lastChild), $u(e) && cm(n.lastChild) && lm(n.lastChild))
            return dm(n.lastChild) ? Pl.before(n.lastChild) : a;
    }
    else
        a = t; const i = a.container(); let l = a.offset(); if (im(i)) {
        if ($u(e) && l > 0)
            return Pl(i, --l);
        if (Hu(e) && l < i.length)
            return Pl(i, ++l);
        o = i;
    }
    else {
        if ($u(e) && l > 0 && (r = fm(i, l - 1), cm(r)))
            return !um(r) && (s = qu(r, e, mm, r), s) ? im(s) ? Pl(s, s.data.length) : Pl.after(s) : im(r) ? Pl(r, r.data.length) : Pl.before(r);
        if (Hu(e) && l < i.childNodes.length && (r = fm(i, l), cm(r)))
            return dm(r) ? ((e, t) => { const n = t.nextSibling; return n && cm(n) ? im(n) ? Pl(n, 0) : Pl.before(n) : pm(1, Pl.after(t), e); })(n, r) : !um(r) && (s = qu(r, e, mm, r), s) ? im(s) ? Pl(s, 0) : Pl.before(s) : im(r) ? Pl(r, 0) : Pl.after(r);
        o = r || a.getNode();
    } if (o && (Hu(e) && a.isAtEnd() || $u(e) && a.isAtStart()) && (o = qu(o, e, M, n, !0), mm(o, n)))
        return gm(e, o); r = o ? qu(o, e, mm, n) : o; const d = bt(Y(((e, t) => { const n = []; let o = e; for (; o && o !== t;)
        n.push(o), o = o.parentNode; return n; })(i, n), am)); return !d || r && d.contains(r) ? r ? gm(e, r) : null : (a = Hu(e) ? Pl.after(d) : Pl.before(d), a); }, hm = e => ({ next: t => pm(1, t, e), prev: t => pm(-1, t, e) }), bm = e => Pl.isTextPosition(e) ? 0 === e.offset() : fl(e.getNode()), vm = e => { if (Pl.isTextPosition(e)) {
        const t = e.container();
        return e.offset() === t.data.length;
    } return fl(e.getNode(!0)); }, ym = (e, t) => !Pl.isTextPosition(e) && !Pl.isTextPosition(t) && e.getNode() === t.getNode(!0), Cm = (e, t, n) => { const o = hm(t); return I.from(e ? o.next(n) : o.prev(n)); }, wm = (e, t, n) => Cm(e, t, n).bind((o => Ku(n, o, t) && ((e, t, n) => { return e ? !ym(t, n) && (o = t, !(!Pl.isTextPosition(o) && as(o.getNode()))) && vm(t) && bm(n) : !ym(n, t) && bm(t) && vm(n); var o; })(e, n, o) ? Cm(e, t, o) : I.some(o))), Em = (e, t, n, o) => wm(e, t, n).bind((n => o(n) ? Em(e, t, n, o) : I.some(n))), xm = (e, t) => { const n = e ? t.firstChild : t.lastChild; return es(n) ? I.some(Pl(n, e ? 0 : n.data.length)) : n ? fl(n) ? I.some(e ? Pl.before(n) : as(o = n) ? Pl.before(o) : Pl.after(o)) : ((e, t, n) => { const o = e ? Pl.before(n) : Pl.after(n); return Cm(e, t, o); })(e, t, n) : I.none(); var o; }, _m = T(Cm, !0), Sm = T(Cm, !1), km = T(xm, !0), Nm = T(xm, !1), Rm = "_mce_caret", Am = e => qr(e) && e.id === Rm, Tm = (e, t) => { let n = t; for (; n && n !== e;) {
        if (Am(n))
            return n;
        n = n.parentNode;
    } return null; }, Om = e => _e(e, "name"), Bm = e => an.isArray(e.start), Pm = e => !(!Om(e) && b(e.forward)) || e.forward, Dm = (e, t) => (qr(t) && e.isBlock(t) && !t.innerHTML && (t.innerHTML = '<br data-mce-bogus="1" />'), t), Lm = (e, t) => Nm(e).fold(L, (e => (t.setStart(e.container(), e.offset()), t.setEnd(e.container(), e.offset()), !0))), Mm = (e, t, n) => !(!(e => !e.hasChildNodes())(t) || !Tm(e, t) || (((e, t) => { var n; const o = (null !== (n = e.ownerDocument) && void 0 !== n ? n : document).createTextNode(Pi); e.appendChild(o), t.setStart(o, 0), t.setEnd(o, 0); })(t, n), 0)), Im = (e, t, n, o) => { const r = n[t ? "start" : "end"], s = e.getRoot(); if (r) {
        let e = s, n = r[0];
        for (let t = r.length - 1; e && t >= 1; t--) {
            const n = e.childNodes;
            if (Mm(s, e, o))
                return !0;
            if (r[t] > n.length - 1)
                return !!Mm(s, e, o) || Lm(e, o);
            e = n[r[t]];
        }
        es(e) && (n = Math.min(r[0], e.data.length)), qr(e) && (n = Math.min(r[0], e.childNodes.length)), t ? o.setStart(e, n) : o.setEnd(e, n);
    } return !0; }, Fm = e => es(e) && e.data.length > 0, Um = (e, t, n) => { const o = e.get(n.id + "_" + t), r = null == o ? void 0 : o.parentNode, s = n.keep; if (o && r) {
        let a, i;
        if ("start" === t ? s ? o.hasChildNodes() ? (a = o.firstChild, i = 1) : Fm(o.nextSibling) ? (a = o.nextSibling, i = 0) : Fm(o.previousSibling) ? (a = o.previousSibling, i = o.previousSibling.data.length) : (a = r, i = e.nodeIndex(o) + 1) : (a = r, i = e.nodeIndex(o)) : s ? o.hasChildNodes() ? (a = o.firstChild, i = 1) : Fm(o.previousSibling) ? (a = o.previousSibling, i = o.previousSibling.data.length) : (a = r, i = e.nodeIndex(o)) : (a = r, i = e.nodeIndex(o)), !s) {
            const r = o.previousSibling, s = o.nextSibling;
            let l;
            for (an.each(an.grep(o.childNodes), (e => { es(e) && (e.data = e.data.replace(/\uFEFF/g, "")); })); l = e.get(n.id + "_" + t);)
                e.remove(l, !0);
            if (es(s) && es(r) && !nn.browser.isOpera()) {
                const t = r.data.length;
                r.appendData(s.data), e.remove(s), a = r, i = t;
            }
        }
        return I.some(Pl(a, i));
    } return I.none(); }, zm = (e, t, n) => ((e, t, n = !1) => 2 === t ? Zl(Li, n, e) : 3 === t ? (e => { const t = e.getRng(); return { start: $l(e.dom.getRoot(), Pl.fromRangeStart(t)), end: $l(e.dom.getRoot(), Pl.fromRangeEnd(t)), forward: e.isForward() }; })(e) : t ? (e => ({ rng: e.getRng(), forward: e.isForward() }))(e) : Jl(e, !1))(e, t, n), jm = (e, t) => { ((e, t) => { const n = e.dom; if (t) {
        if (Bm(t))
            return ((e, t) => { const n = e.createRng(); return Im(e, !0, t, n) && Im(e, !1, t, n) ? I.some({ range: n, forward: Pm(t) }) : I.none(); })(n, t);
        if ((e => m(e.start))(t))
            return ((e, t) => { const n = I.from(Vl(e.getRoot(), t.start)), o = I.from(Vl(e.getRoot(), t.end)); return ze(n, o, ((n, o) => { const r = e.createRng(); return r.setStart(n.container(), n.offset()), r.setEnd(o.container(), o.offset()), { range: r, forward: Pm(t) }; })); })(n, t);
        if ((e => _e(e, "id"))(t))
            return ((e, t) => { const n = Um(e, "start", t), o = Um(e, "end", t); return ze(n, o.or(n), ((n, o) => { const r = e.createRng(); return r.setStart(Dm(e, n.container()), n.offset()), r.setEnd(Dm(e, o.container()), o.offset()), { range: r, forward: Pm(t) }; })); })(n, t);
        if (Om(t))
            return ((e, t) => I.from(e.select(t.name)[t.index]).map((t => { const n = e.createRng(); return n.selectNode(t), { range: n, forward: !0 }; })))(n, t);
        if ((e => _e(e, "rng"))(t))
            return I.some({ range: t.rng, forward: Pm(t) });
    } return I.none(); })(e, t).each((({ range: t, forward: n }) => { e.setRng(t, n); })); }, Hm = e => qr(e) && "SPAN" === e.tagName && "bookmark" === e.getAttribute("data-mce-type"), $m = (Vm = it, e => Vm === e);
    var Vm;
    const qm = e => "" !== e && -1 !== " \f\n\r\t\v".indexOf(e), Wm = e => !qm(e) && !$m(e) && !lt(e), Km = e => { const t = []; if (e)
        for (let n = 0; n < e.rangeCount; n++)
            t.push(e.getRangeAt(n)); return t; }, Ym = (e, t) => { const n = vr(t, "td[data-mce-selected],th[data-mce-selected]"); return n.length > 0 ? n : (e => Y((e => te(e, (e => { const t = tl(e); return t ? [mn(t)] : []; })))(e), Ai))(e); }, Xm = e => Ym(Km(e.selection.getSel()), mn(e.getBody())), Gm = (e, t) => or(e, "table", t), Zm = e => Vn(e).fold(N([e]), (t => [e].concat(Zm(t)))), Qm = e => qn(e).fold(N([e]), (t => "br" === xn(t) ? Fn(t).map((t => [e].concat(Qm(t)))).getOr([]) : [e].concat(Qm(t)))), Jm = (e, t) => ze((e => { const t = e.startContainer, n = e.startOffset; return es(t) ? 0 === n ? I.some(mn(t)) : I.none() : I.from(t.childNodes[n]).map(mn); })(t), (e => { const t = e.endContainer, n = e.endOffset; return es(t) ? n === t.data.length ? I.some(mn(t)) : I.none() : I.from(t.childNodes[n - 1]).map(mn); })(t), ((t, n) => { const o = Q(Zm(e), T(Cn, t)), r = Q(Qm(e), T(Cn, n)); return o.isSome() && r.isSome(); })).getOr(!1), ef = (e, t, n, o) => { const r = n, s = new Fr(n, r), a = Ce(e.schema.getMoveCaretBeforeOnEnterElements(), ((e, t) => !H(["td", "th", "table"], t.toLowerCase()))); let i = n; do {
        if (es(i) && 0 !== an.trim(i.data).length)
            return void (o ? t.setStart(i, 0) : t.setEnd(i, i.data.length));
        if (a[i.nodeName])
            return void (o ? t.setStartBefore(i) : "BR" === i.nodeName ? t.setEndBefore(i) : t.setEndAfter(i));
    } while (i = o ? s.next() : s.prev()); "BODY" === r.nodeName && (o ? t.setStart(r, 0) : t.setEnd(r, r.childNodes.length)); }, tf = e => { const t = e.selection.getSel(); return C(t) && t.rangeCount > 0; }, nf = (e, t) => { const n = Xm(e); n.length > 0 ? q(n, (n => { const o = n.dom, r = e.dom.createRng(); r.setStartBefore(o), r.setEndAfter(o), t(r, !0); })) : t(e.selection.getRng(), !1); }, of = (e, t, n) => { const o = Jl(e, t); n(o), e.moveToBookmark(o); }, rf = e => E(null == e ? void 0 : e.nodeType), sf = e => qr(e) && !Hm(e) && !Am(e) && !Gr(e), af = (e, t, n) => { const { selection: o, dom: r } = e, s = o.getNode(), a = cs(s); of(o, !0, (() => { t(); })), a && cs(s) && r.isChildOf(s, e.getBody()) ? e.selection.select(s) : n(o.getStart()) && lf(r, o); }, lf = (e, t) => { var n, o; const r = t.getRng(), { startContainer: s, startOffset: a } = r; if (!((e, t) => { if (sf(t) && !/^(TD|TH)$/.test(t.nodeName)) {
        const n = e.getAttrib(t, "data-mce-selected"), o = parseInt(n, 10);
        return !isNaN(o) && o > 0;
    } return !1; })(e, t.getNode()) && qr(s)) {
        const i = s.childNodes, l = e.getRoot();
        let d;
        if (a < i.length) {
            const t = i[a];
            d = new Fr(t, null !== (n = e.getParent(t, e.isBlock)) && void 0 !== n ? n : l);
        }
        else {
            const t = i[i.length - 1];
            d = new Fr(t, null !== (o = e.getParent(t, e.isBlock)) && void 0 !== o ? o : l), d.next(!0);
        }
        for (let n = d.current(); n; n = d.next()) {
            if ("false" === e.getContentEditable(n))
                return;
            if (es(n) && !mf(n))
                return r.setStart(n, 0), void t.setRng(r);
        }
    } }, df = (e, t, n) => { if (e) {
        const o = t ? "nextSibling" : "previousSibling";
        for (e = n ? e : e[o]; e; e = e[o])
            if (qr(e) || !mf(e))
                return e;
    } }, cf = (e, t) => !!e.getTextBlockElements()[t.nodeName.toLowerCase()] || js(e, t), uf = (e, t, n) => e.schema.isValidChild(t, n), mf = (e, t = !1) => { if (C(e) && es(e)) {
        const n = t ? e.data.replace(/ /g, "\xa0") : e.data;
        return zr(n);
    } return !1; }, ff = (e, t) => { const n = e.dom; return sf(t) && "false" === n.getContentEditable(t) && ((e, t) => { const n = "[data-mce-cef-wrappable]", o = pc(e), r = tt(o) ? n : `${n},${o}`; return vn(mn(t), r); })(e, t) && 0 === n.select('[contenteditable="true"]', t).length; }, gf = (e, t) => w(e) ? e(t) : (C(t) && (e = e.replace(/%(\w+)/g, ((e, n) => t[n] || e))), e), pf = (e, t) => (t = t || "", e = "" + ((e = e || "").nodeName || e), t = "" + (t.nodeName || t), e.toLowerCase() === t.toLowerCase()), hf = (e, t) => { if (y(e))
        return null; {
        let n = String(e);
        return "color" !== t && "backgroundColor" !== t || (n = Ma(n)), "fontWeight" === t && 700 === e && (n = "bold"), "fontFamily" === t && (n = n.replace(/[\'\"]/g, "").replace(/,\s+/g, ",")), n;
    } }, bf = (e, t, n) => { const o = e.getStyle(t, n); return hf(o, n); }, vf = (e, t) => { let n; return e.getParent(t, (t => !!qr(t) && (n = e.getStyle(t, "text-decoration"), !!n && "none" !== n))), n; }, yf = (e, t, n) => e.getParents(t, n, e.getRoot()), Cf = (e, t, n) => { const o = e.formatter.get(t); return C(o) && $(o, n); }, wf = e => Se(e, "block"), Ef = e => Se(e, "selector"), xf = e => Se(e, "inline"), _f = e => Ef(e) && !1 !== e.expand && !xf(e), Sf = e => (e => { const t = []; let n = e; for (; n;) {
        if (es(n) && n.data !== Pi || n.childNodes.length > 1)
            return [];
        qr(n) && t.push(n), n = n.firstChild;
    } return t; })(e).length > 0, kf = e => Am(e.dom) && Sf(e.dom), Nf = Hm, Rf = yf, Af = mf, Tf = cf, Of = (e, t) => { let n = t; for (; n;) {
        if (qr(n) && e.getContentEditable(n))
            return "false" === e.getContentEditable(n) ? n : t;
        n = n.parentNode;
    } return t; }, Bf = (e, t, n, o) => { const r = t.data; if (e) {
        for (let e = n; e > 0; e--)
            if (o(r.charAt(e - 1)))
                return e;
    }
    else
        for (let e = n; e < r.length; e++)
            if (o(r.charAt(e)))
                return e; return -1; }, Pf = (e, t, n) => Bf(e, t, n, (e => $m(e) || qm(e))), Df = (e, t, n) => Bf(e, t, n, Wm), Lf = (e, t, n, o, r, s) => { let a; const i = e.getParent(n, (t => us(t) || e.isBlock(t))), l = C(i) ? i : t, d = (t, n, o) => { const s = Ei(e), i = r ? s.backwards : s.forwards; return I.from(i(t, n, ((e, t) => Nf(e.parentNode) ? -1 : (a = e, o(r, e, t))), l)); }; return d(n, o, Pf).bind((e => s ? d(e.container, e.offset + (r ? -1 : 0), Df) : I.some(e))).orThunk((() => a ? I.some({ container: a, offset: r ? 0 : a.length }) : I.none())); }, Mf = (e, t, n, o, r) => { const s = o[r]; es(o) && tt(o.data) && s && (o = s); const a = Rf(e, o); for (let o = 0; o < a.length; o++)
        for (let r = 0; r < t.length; r++) {
            const s = t[r];
            if ((!C(s.collapsed) || s.collapsed === n.collapsed) && Ef(s) && e.is(a[o], s.selector))
                return a[o];
        } return o; }, If = (e, t, n, o) => { var r; let s = n; const a = e.getRoot(), i = t[0]; if (wf(i) && (s = i.wrapper ? null : e.getParent(n, i.block, a)), !s) {
        const t = null !== (r = e.getParent(n, "LI,TD,TH,SUMMARY")) && void 0 !== r ? r : a;
        s = e.getParent(es(n) ? n.parentNode : n, (t => t !== a && Tf(e.schema, t)), t);
    } if (s && wf(i) && i.wrapper && (s = Rf(e, s, "ul,ol").reverse()[0] || s), !s)
        for (s = n; s && s[o] && !e.isBlock(s[o]) && (s = s[o], !pf(s, "br"));)
            ; return s || n; }, Ff = (e, t, n, o) => { const r = n.parentNode; return !C(n[o]) && (!(r !== t && !y(r) && !e.isBlock(r)) || Ff(e, t, r, o)); }, Uf = (e, t, n, o, r, s) => { let a = n; const i = r ? "previousSibling" : "nextSibling", l = e.getRoot(); if (es(n) && !Af(n) && (r ? o > 0 : o < n.data.length))
        return n; for (; a;) {
        if (us(a))
            return n;
        if (!t[0].block_expand && e.isBlock(a))
            return s ? a : n;
        for (let t = a[i]; t; t = t[i]) {
            const n = es(t) && !Ff(e, l, t, i);
            if (!Nf(t) && (!as(d = t) || !d.getAttribute("data-mce-bogus") || d.nextSibling) && !Af(t, n))
                return a;
        }
        if (a === l || a.parentNode === l) {
            n = a;
            break;
        }
        a = a.parentNode;
    } var d; return n; }, zf = e => Nf(e.parentNode) || Nf(e), jf = (e, t, n, o = {}) => { const { includeTrailingSpace: r = !1, expandToBlock: s = !0 } = o, a = e.getParent(t.commonAncestorContainer, (e => us(e))), i = C(a) ? a : e.getRoot(); let { startContainer: l, startOffset: d, endContainer: c, endOffset: u } = t; const m = n[0]; return qr(l) && l.hasChildNodes() && (l = nl(l, d), es(l) && (d = 0)), qr(c) && c.hasChildNodes() && (c = nl(c, t.collapsed ? u : u - 1), es(c) && (u = c.data.length)), l = Of(e, l), c = Of(e, c), zf(l) && (l = Nf(l) ? l : l.parentNode, l = t.collapsed ? l.previousSibling || l : l.nextSibling || l, es(l) && (d = t.collapsed ? l.length : 0)), zf(c) && (c = Nf(c) ? c : c.parentNode, c = t.collapsed ? c.nextSibling || c : c.previousSibling || c, es(c) && (u = t.collapsed ? 0 : c.length)), t.collapsed && (Lf(e, i, l, d, !0, r).each((({ container: e, offset: t }) => { l = e, d = t; })), Lf(e, i, c, u, !1, r).each((({ container: e, offset: t }) => { c = e, u = t; }))), (xf(m) || m.block_expand) && (xf(m) && es(l) && 0 !== d || (l = Uf(e, n, l, d, !0, s)), xf(m) && es(c) && u !== c.data.length || (c = Uf(e, n, c, u, !1, s))), _f(m) && (l = Mf(e, n, t, l, "previousSibling"), c = Mf(e, n, t, c, "nextSibling")), (wf(m) || Ef(m)) && (l = If(e, n, l, "previousSibling"), c = If(e, n, c, "nextSibling"), wf(m) && (e.isBlock(l) || (l = Uf(e, n, l, d, !0, s), es(l) && (d = 0)), e.isBlock(c) || (c = Uf(e, n, c, u, !1, s), es(c) && (u = c.data.length)))), qr(l) && l.parentNode && (d = e.nodeIndex(l), l = l.parentNode), qr(c) && c.parentNode && (u = e.nodeIndex(c) + 1, c = c.parentNode), { startContainer: l, startOffset: d, endContainer: c, endOffset: u }; }, Hf = (e, t, n) => { var o; const r = t.startOffset, s = nl(t.startContainer, r), a = t.endOffset, i = nl(t.endContainer, a - 1), l = e => { const t = e[0]; es(t) && t === s && r >= t.data.length && e.splice(0, 1); const n = e[e.length - 1]; return 0 === a && e.length > 0 && n === i && es(n) && e.splice(e.length - 1, 1), e; }, d = (e, t, n) => { const o = []; for (; e && e !== n; e = e[t])
        o.push(e); return o; }, c = (t, n) => e.getParent(t, (e => e.parentNode === n), n), u = (e, t, o) => { const r = o ? "nextSibling" : "previousSibling"; for (let s = e, a = s.parentNode; s && s !== t; s = a) {
        a = s.parentNode;
        const t = d(s === e ? s : s[r], r);
        t.length && (o || t.reverse(), n(l(t)));
    } }; if (s === i)
        return n(l([s])); const m = null !== (o = e.findCommonAncestor(s, i)) && void 0 !== o ? o : e.getRoot(); if (e.isChildOf(s, i))
        return u(s, m, !0); if (e.isChildOf(i, s))
        return u(i, m); const f = c(s, m) || s, g = c(i, m) || i; u(s, f, !0); const p = d(f === s ? f : f.nextSibling, "nextSibling", g === i ? g.nextSibling : g); p.length && n(l(p)), u(i, g); }, $f = ['pre[class*=language-][contenteditable="false"]', "figure.image", "div[data-ephox-embed-iri]", "div.tiny-pageembed", "div.mce-toc", "div[data-mce-toc]", "div.mce-footnotes"], Vf = (e, t, n, o, r, s) => { const { uid: a = t, ...i } = n; cr(e, ci()), go(e, `${mi()}`, a), go(e, `${ui()}`, o); const { attributes: l = {}, classes: d = [] } = r(a, i); if (po(e, l), ((e, t) => { q(t, (t => { cr(e, t); })); })(e, d), s) {
        d.length > 0 && go(e, `${gi()}`, d.join(","));
        const t = fe(l);
        t.length > 0 && go(e, `${pi()}`, t.join(","));
    } }, qf = (e, t, n, o, r) => { const s = cn("span", e); return Vf(s, t, n, o, r, !1), s; }, Wf = (e, t, n, o, r, s) => { const a = [], i = qf(e.getDoc(), n, s, o, r), l = Ve(), d = () => { l.clear(); }, c = e => { q(e, u); }, u = t => { switch (((e, t, n, o) => Mn(t).fold((() => "skipping"), (r => "br" === o || (e => Rn(e) && Go(e) === Pi)(t) ? "valid" : (e => Nn(e) && fr(e, ci()))(t) ? "existing" : Am(t.dom) ? "caret" : $($f, (e => vn(t, e))) ? "valid-block" : uf(e, n, o) && uf(e, xn(r), n) ? "valid" : "invalid-child")))(e, t, "span", xn(t))) {
        case "invalid-child": {
            d();
            const e = Hn(t);
            c(e), d();
            break;
        }
        case "valid-block":
            d(), Vf(t, n, s, o, r, !0);
            break;
        case "valid": {
            const e = l.get().getOrThunk((() => { const e = So(i); return a.push(e), l.set(e), e; }));
            uo(t, e);
            break;
        }
    } }; return Hf(e.dom, t, (e => { d(), (e => { const t = V(e, mn); c(t); })(e); })), a; }, Kf = e => { const t = (() => { const e = {}; return { register: (t, n) => { e[t] = { name: t, settings: n }; }, lookup: t => xe(e, t).map((e => e.settings)), getNames: () => fe(e) }; })(); ((e, t) => { const n = ui(), o = e => I.from(e.attr(n)).bind(t.lookup), r = e => { var t, n; e.attr(mi(), null), e.attr(ui(), null), e.attr(fi(), null); const o = I.from(e.attr(pi())).map((e => e.split(","))).getOr([]), r = I.from(e.attr(gi())).map((e => e.split(","))).getOr([]); q(o, (t => e.attr(t, null))); const s = null !== (n = null === (t = e.attr("class")) || void 0 === t ? void 0 : t.split(" ")) && void 0 !== n ? n : [], a = re(s, [ci()].concat(r)); e.attr("class", a.length > 0 ? a.join(" ") : null), e.attr(gi(), null), e.attr(pi(), null); }; e.serializer.addTempAttr(fi()), e.serializer.addAttributeFilter(n, (e => { for (const t of e)
        o(t).each((e => { !1 === e.persistent && ("span" === t.name ? t.unwrap() : r(t)); })); })); })(e, t); const n = ((e, t) => { const n = Ne({}), o = () => ({ listeners: [], previous: Ve() }), r = (e, t) => { s(e, (e => (t(e), e))); }, s = (e, t) => { const r = n.get(), s = t(xe(r, e).getOrThunk(o)); r[e] = s, n.set(r); }, a = (t, n) => { q(yi(e, t), (e => { n ? go(e, fi(), "true") : yo(e, fi()); })); }, i = rt((() => { const n = ae(t.getNames()); q(n, (t => { s(t, (n => { const o = n.previous.get(); return bi(e, I.some(t)).fold((() => { o.each((e => { (e => { r(e, (t => { q(t.listeners, (t => t(!1, e))); })); })(t), n.previous.clear(), a(e, !1); })); }), (({ uid: e, name: t, elements: s }) => { Fe(o, e) || (o.each((e => a(e, !1))), ((e, t, n) => { r(e, (o => { q(o.listeners, (o => o(!0, e, { uid: t, nodes: V(n, (e => e.dom)) }))); })); })(t, e, s), n.previous.set(e), a(e, !0)); })), { previous: n.previous, listeners: n.listeners }; })); })); }), 30); return e.on("remove", (() => { i.cancel(); })), e.on("NodeChange", (() => { i.throttle(); })), { addListener: (e, t) => { s(e, (e => ({ previous: e.previous, listeners: e.listeners.concat([t]) }))); } }; })(e, t), o = On("span"), r = e => { q(e, (e => { o(e) ? xo(e) : (e => { mr(e, ci()), yo(e, `${mi()}`), yo(e, `${ui()}`), yo(e, `${fi()}`); const t = bo(e, `${pi()}`).map((e => e.split(","))).getOr([]), n = bo(e, `${gi()}`).map((e => e.split(","))).getOr([]); var o; q(t, (t => yo(e, t))), o = e, q(n, (e => { mr(o, e); })), yo(e, `${gi()}`), yo(e, `${pi()}`); })(e); })); }; return { register: (e, n) => { t.register(e, n); }, annotate: (n, o) => { t.lookup(n).each((t => { ((e, t, n, o) => { e.undoManager.transact((() => { const r = e.selection, s = r.getRng(), a = Xm(e).length > 0, i = De("mce-annotation"); if (s.collapsed && !a && ((e, t) => { const n = jf(e.dom, t, [{ inline: "span" }]); t.setStart(n.startContainer, n.startOffset), t.setEnd(n.endContainer, n.endOffset), e.selection.setRng(t); })(e, s), r.getRng().collapsed && !a) {
            const s = qf(e.getDoc(), i, o, t, n.decorate);
            Ao(s, it), r.getRng().insertNode(s.dom), r.select(s.dom);
        }
        else
            of(r, !1, (() => { nf(e, (r => { Wf(e, r, i, t, n.decorate, o); })); })); })); })(e, n, t, o); })); }, annotationChanged: (e, t) => { n.addListener(e, t); }, remove: t => { bi(e, I.some(t)).each((({ elements: t }) => { const n = e.selection.getBookmark(); r(t), e.selection.moveToBookmark(n); })); }, removeAll: t => { const n = e.selection.getBookmark(); pe(Ci(e, t), ((e, t) => { r(e); })), e.selection.moveToBookmark(n); }, getAll: t => { const n = Ci(e, t); return he(n, (e => V(e, (e => e.dom)))); } }; }, Yf = e => ({ getBookmark: T(zm, e), moveToBookmark: T(jm, e) });
    Yf.isBookmarkNode = Hm;
    const Xf = (e, t, n) => !n.collapsed && $(n.getClientRects(), (n => ((e, t, n) => t >= e.left && t <= e.right && n >= e.top && n <= e.bottom)(n, e, t))), Gf = (e, t) => { const n = Rn(t) ? Go(t).length : Hn(t).length + 1; return e > n ? n : e < 0 ? 0 : e; }, Zf = e => Tr.range(e.start, Gf(e.soffset, e.start), e.finish, Gf(e.foffset, e.finish)), Qf = (e, t) => !Vr(t.dom) && (wn(e, t) || Cn(e, t)), Jf = e => t => Qf(e, t.start) && Qf(e, t.finish), eg = e => Tr.range(mn(e.startContainer), e.startOffset, mn(e.endContainer), e.endOffset), tg = e => { const t = document.createRange(); try {
        return t.setStart(e.start.dom, e.soffset), t.setEnd(e.finish.dom, e.foffset), I.some(t);
    }
    catch (e) {
        return I.none();
    } }, ng = e => { const t = (e => e.inline || nn.browser.isFirefox())(e) ? (n = mn(e.getBody()), (e => { const t = e.getSelection(); return (t && 0 !== t.rangeCount ? I.from(t.getRangeAt(0)) : I.none()).map(eg); })(Dn(n).dom).filter(Jf(n))) : I.none(); var n; e.bookmark = t.isSome() ? t : e.bookmark; }, og = e => (e.bookmark ? e.bookmark : I.none()).bind((t => { return n = mn(e.getBody()), o = t, I.from(o).filter(Jf(n)).map(Zf); var n, o; })).bind(tg), rg = { isEditorUIElement: e => { const t = e.className.toString(); return -1 !== t.indexOf("tox-") || -1 !== t.indexOf("mce-"); } }, sg = { setEditorTimeout: (e, t, n) => ((e, t) => (E(t) || (t = 0), window.setTimeout(e, t)))((() => { e.removed || t(); }), n), setEditorInterval: (e, t, n) => { const o = ((e, t) => (E(t) || (t = 0), window.setInterval(e, t)))((() => { e.removed ? window.clearInterval(o) : t(); }), n); return o; } }, ag = (e, t) => e.view(t).fold(N([]), (t => { const n = e.owner(t), o = ag(e, n); return [t].concat(o); }));
    var ig = Object.freeze({ __proto__: null, view: e => { var t; return (e.dom === document ? I.none() : I.from(null === (t = e.dom.defaultView) || void 0 === t ? void 0 : t.frameElement)).map(mn); }, owner: e => Pn(e) });
    const lg = e => { const t = no(), n = qo(t), o = ((e, t) => { const n = t.owner(e); return ag(t, n); })(e, ig), r = Vo(e), s = X(o, ((e, t) => { const n = Vo(t); return { left: e.left + n.left, top: e.top + n.top }; }), { left: 0, top: 0 }); return Ho(s.left + r.left + n.left, s.top + r.top + n.top); };
    let dg;
    const cg = ni.DOM, ug = e => { const t = e.classList; return void 0 !== t && (t.contains("tox-edit-area") || t.contains("tox-edit-area__iframe") || t.contains("mce-content-body")); }, mg = (e, t) => { const n = hc(e), o = cg.getParent(t, (t => (e => qr(e) && rg.isEditorUIElement(e))(t) || !!n && e.dom.is(t, n))); return null !== o; }, fg = e => { try {
        const t = Yn(mn(e.getElement()));
        return so(t).fold((() => document.body), (e => e.dom));
    }
    catch (e) {
        return document.body;
    } }, gg = (e, t) => { const n = t.editor; (e => { const t = ot((() => { ng(e); }), 0); e.on("init", (() => { e.inline && ((e, t) => { const n = () => { t.throttle(); }; ni.DOM.bind(document, "mouseup", n), e.on("remove", (() => { ni.DOM.unbind(document, "mouseup", n); })); })(e, t), ((e, t) => { ((e, t) => { e.on("mouseup touchend", (e => { t.throttle(); })); })(e, t), e.on("keyup NodeChange AfterSetSelectionRange", (t => { (e => "nodechange" === e.type && e.selectionChange)(t) || ng(e); })); })(e, t); })), e.on("remove", (() => { t.cancel(); })); })(n); const o = (e, t) => { ru(e) && !0 !== e.inline && t(mn(e.getContainer()), "tox-edit-focus"); }; n.on("focusin", (() => { const t = e.focusedEditor; if (ug(fg(n)) && o(n, cr), t !== n) {
        t && t.dispatch("blur", { focusedEditor: n }), e.setActive(n), e.focusedEditor = n, n.dispatch("focus", { blurredEditor: t }), n.focus(!0);
        const o = Kt().browser;
        !0 !== n.inline && (o.isSafari() || o.isChromium()) && (e => { if (!e.iframeElement)
            return; const t = mn(e.iframeElement), n = lg(t), o = Ir(window); (n.top < o.y || n.top > o.bottom - 25) && t.dom.scrollIntoView({ block: "center" }); })(n);
    } })), n.on("focusout", (() => { sg.setEditorTimeout(n, (() => { const t = e.focusedEditor; ug(fg(n)) && t === n || o(n, mr), mg(n, fg(n)) || t !== n || (n.dispatch("blur", { focusedEditor: null }), e.focusedEditor = null); })); })), dg || (dg = t => { const n = e.activeEditor; n && Zn(t).each((t => { const o = t; o.ownerDocument === document && (o === document.body || mg(n, o) || e.focusedEditor !== n || (n.dispatch("blur", { focusedEditor: null }), e.focusedEditor = null)); })); }, cg.bind(document, "focusin", dg)); }, pg = (e, t) => { e.focusedEditor === t.editor && (e.focusedEditor = null), !e.activeEditor && dg && (cg.unbind(document, "focusin", dg), dg = null); }, hg = (e, t) => { ((e, t) => (e => e.collapsed ? I.from(nl(e.startContainer, e.startOffset)).map(mn) : I.none())(t).bind((t => Ri(t) ? I.some(t) : wn(e, t) ? I.none() : I.some(e))))(mn(e.getBody()), t).bind((e => km(e.dom))).fold((() => { e.selection.normalize(); }), (t => e.selection.setRng(t.toRange()))); }, bg = e => { if (e.setActive)
        try {
            e.setActive();
        }
        catch (t) {
            e.focus();
        }
    else
        e.focus(); }, vg = e => e.inline ? (e => { const t = e.getBody(); return t && (n = mn(t), ro(n) || (o = n, so(Yn(o)).filter((e => o.dom.contains(e.dom)))).isSome()); var n, o; })(e) : (e => C(e.iframeElement) && ro(mn(e.iframeElement)))(e), yg = e => vg(e) || (e => { const t = Yn(mn(e.getElement())); return so(t).filter((t => !ug(t.dom) && mg(e, t.dom))).isSome(); })(e), Cg = e => e.editorManager.setActive(e), wg = { BACKSPACE: 8, DELETE: 46, DOWN: 40, ENTER: 13, ESC: 27, LEFT: 37, RIGHT: 39, SPACEBAR: 32, TAB: 9, UP: 38, PAGE_UP: 33, PAGE_DOWN: 34, END: 35, HOME: 36, modifierPressed: e => e.shiftKey || e.ctrlKey || e.altKey || wg.metaKeyPressed(e), metaKeyPressed: e => nn.os.isMacOS() || nn.os.isiOS() ? e.metaKey : e.ctrlKey && !e.altKey }, Eg = "data-mce-selected", xg = Math.abs, _g = Math.round, Sg = { nw: [0, 0, -1, -1], ne: [1, 0, 1, -1], se: [1, 1, 1, 1], sw: [0, 1, -1, 1] }, kg = (e, t) => { const n = t.dom, o = t.getDoc(), r = document, s = t.getBody(); let a, i, l, d, c, u, m, f, g, p, h, b, v, y, w; const E = e => C(e) && (is(e) || n.is(e, "figure.image")), x = e => gs(e) || n.hasClass(e, "mce-preview-object"), _ = e => { const n = e.target; ((e, t) => { if ((e => "longpress" === e.type || 0 === e.type.indexOf("touch"))(e)) {
        const n = e.touches[0];
        return E(e.target) && !Xf(n.clientX, n.clientY, t);
    } return E(e.target) && !Xf(e.clientX, e.clientY, t); })(e, t.selection.getRng()) && !e.isDefaultPrevented() && t.selection.select(n); }, S = e => n.hasClass(e, "mce-preview-object") && C(e.firstElementChild) ? [e, e.firstElementChild] : n.is(e, "figure.image") ? [e.querySelector("img")] : [e], k = e => { const o = nc(t); return !(!o || t.mode.isReadOnly()) && "false" !== e.getAttribute("data-mce-resize") && e !== t.getBody() && (n.hasClass(e, "mce-preview-object") && C(e.firstElementChild) ? vn(mn(e.firstElementChild), o) : vn(mn(e), o)); }, N = (e, o, r) => { if (C(r)) {
        const s = S(e);
        q(s, (e => { e.style[o] || !t.schema.isValid(e.nodeName.toLowerCase(), o) ? n.setStyle(e, o, r) : n.setAttrib(e, o, "" + r); }));
    } }, R = (e, t, n) => { N(e, "width", t), N(e, "height", n); }, A = e => { let o, r, c, C, _; o = e.screenX - u, r = e.screenY - m, b = o * d[2] + f, v = r * d[3] + g, b = b < 5 ? 5 : b, v = v < 5 ? 5 : v, c = (E(a) || x(a)) && !1 !== oc(t) ? !wg.modifierPressed(e) : wg.modifierPressed(e), c && (xg(o) > xg(r) ? (v = _g(b * p), b = _g(v / p)) : (b = _g(v / p), v = _g(b * p))), R(i, b, v), C = d.startPos.x + o, _ = d.startPos.y + r, C = C > 0 ? C : 0, _ = _ > 0 ? _ : 0, n.setStyles(l, { left: C, top: _, display: "block" }), l.innerHTML = b + " &times; " + v, o = s.scrollWidth - y, r = s.scrollHeight - w, o + r !== 0 && n.setStyles(l, { left: C - o, top: _ - r }), h || (((e, t, n, o, r) => { e.dispatch("ObjectResizeStart", { target: t, width: n, height: o, origin: r }); })(t, a, f, g, "corner-" + d.name), h = !0); }, T = () => { const e = h; h = !1, e && (N(a, "width", b), N(a, "height", v)), n.unbind(o, "mousemove", A), n.unbind(o, "mouseup", T), r !== o && (n.unbind(r, "mousemove", A), n.unbind(r, "mouseup", T)), n.remove(i), n.remove(l), n.remove(c), O(a), e && (((e, t, n, o, r) => { e.dispatch("ObjectResized", { target: t, width: n, height: o, origin: r }); })(t, a, b, v, "corner-" + d.name), n.setAttrib(a, "style", n.getAttrib(a, "style"))), t.nodeChanged(); }, O = e => { M(); const h = n.getPos(e, s), C = h.x, E = h.y, _ = e.getBoundingClientRect(), N = _.width || _.right - _.left, O = _.height || _.bottom - _.top; a !== e && (P(), a = e, b = v = 0); const B = t.dispatch("ObjectSelected", { target: e }); k(e) && !B.isDefaultPrevented() ? pe(Sg, ((e, t) => { let h = n.get("mceResizeHandle" + t); h && n.remove(h), h = n.add(s, "div", { id: "mceResizeHandle" + t, "data-mce-bogus": "all", class: "mce-resizehandle", unselectable: !0, style: "cursor:" + t + "-resize; margin:0; padding:0" }), n.bind(h, "mousedown", (h => { h.stopImmediatePropagation(), h.preventDefault(), (h => { const b = S(a)[0]; u = h.screenX, m = h.screenY, f = b.clientWidth, g = b.clientHeight, p = g / f, d = e, d.name = t, d.startPos = { x: N * e[0] + C, y: O * e[1] + E }, y = s.scrollWidth, w = s.scrollHeight, c = n.add(s, "div", { class: "mce-resize-backdrop", "data-mce-bogus": "all" }), n.setStyles(c, { position: "fixed", left: "0", top: "0", width: "100%", height: "100%" }), i = ((e, t) => { if (x(t))
        return e.create("img", { src: nn.transparentSrc }); if (Zr(t)) {
        const n = Ye(d.name, "n") ? le : de, o = t.cloneNode(!0);
        return n(e.select("tr", o)).each((t => { const n = e.select("td,th", t); e.setStyle(t, "height", null), q(n, (t => e.setStyle(t, "height", null))); })), o;
    } return t.cloneNode(!0); })(n, a), n.addClass(i, "mce-clonedresizable"), n.setAttrib(i, "data-mce-bogus", "all"), i.contentEditable = "false", n.setStyles(i, { left: C, top: E, margin: 0 }), R(i, N, O), i.removeAttribute(Eg), s.appendChild(i), n.bind(o, "mousemove", A), n.bind(o, "mouseup", T), r !== o && (n.bind(r, "mousemove", A), n.bind(r, "mouseup", T)), l = n.add(s, "div", { class: "mce-resize-helper", "data-mce-bogus": "all" }, f + " &times; " + g); })(h); })), e.elm = h, n.setStyles(h, { left: N * e[0] + C - h.offsetWidth / 2, top: O * e[1] + E - h.offsetHeight / 2 }); })) : P(!1); }, B = ot(O, 0), P = (e = !0) => { B.cancel(), M(), a && e && a.removeAttribute(Eg), pe(Sg, ((e, t) => { const o = n.get("mceResizeHandle" + t); o && (n.unbind(o), n.remove(o)); })); }, D = (e, t) => n.isChildOf(e, t), L = o => { if (h || t.removed || t.composing)
        return; const r = "mousedown" === o.type ? o.target : e.getNode(), a = sr(mn(r), "table,img,figure.image,hr,video,span.mce-preview-object,details").map((e => e.dom)).filter((e => n.isEditable(e.parentElement) || "IMG" === e.nodeName && n.isEditable(e))).getOrUndefined(), i = C(a) ? n.getAttrib(a, Eg, "1") : "1"; if (q(n.select(`img[${Eg}],hr[${Eg}]`), (e => { e.removeAttribute(Eg); })), C(a) && D(a, s) && yg(t)) {
        I();
        const t = e.getStart(!0);
        if (D(t, a) && D(e.getEnd(!0), a))
            return n.setAttrib(a, Eg, i), void B.throttle(a);
    } P(); }, M = () => { pe(Sg, (e => { e.elm && (n.unbind(e.elm), delete e.elm); })); }, I = () => { try {
        t.getDoc().execCommand("enableObjectResizing", !1, "false");
    }
    catch (e) { } }; return t.on("init", (() => { I(), t.on("NodeChange ResizeEditor ResizeWindow ResizeContent drop", L), t.on("keyup compositionend", (e => { a && "TABLE" === a.nodeName && L(e); })), t.on("hide blur", P), t.on("contextmenu longpress", _, !0); })), t.on("remove", M), { isResizable: k, showResizeRect: O, hideResizeRect: P, updateResizeRect: L, destroy: () => { B.cancel(), a = i = c = null; } }; }, Ng = (e, t, n) => { const o = Dn(mn(n)); return Lr(o.dom, e, t).map((e => { const t = n.createRange(); return t.setStart(e.start.dom, e.soffset), t.setEnd(e.finish.dom, e.foffset), t; })).getOrUndefined(); }, Rg = (e, t) => C(e) && C(t) && e.startContainer === t.startContainer && e.startOffset === t.startOffset && e.endContainer === t.endContainer && e.endOffset === t.endOffset, Ag = (e, t, n) => null !== ((e, t, n) => { let o = e; for (; o && o !== t;) {
        if (n(o))
            return o;
        o = o.parentNode;
    } return null; })(e, t, n), Tg = (e, t, n) => Ag(e, t, (e => e.nodeName === n)), Og = (e, t) => zi(e) && !Ag(e, t, Am), Bg = (e, t, n) => { const o = t.parentNode; if (o) {
        const r = new Fr(t, e.getParent(o, e.isBlock) || e.getRoot());
        let s;
        for (; s = r[n ? "prev" : "next"]();)
            if (as(s))
                return !0;
    } return !1; }, Pg = (e, t, n, o, r) => { const s = e.getRoot(), a = e.schema.getNonEmptyElements(), i = r.parentNode; let l, d; if (!i)
        return I.none(); const c = e.getParent(i, e.isBlock) || s; if (o && as(r) && t && e.isEmpty(c))
        return I.some(Pl(i, e.nodeIndex(r))); const u = new Fr(r, c); for (; d = u[o ? "prev" : "next"]();) {
        if ("false" === e.getContentEditableParent(d) || Og(d, s))
            return I.none();
        if (es(d) && d.data.length > 0)
            return Tg(d, s, "A") ? I.none() : I.some(Pl(d, o ? d.data.length : 0));
        if (e.isBlock(d) || a[d.nodeName.toLowerCase()])
            return I.none();
        l = d;
    } return os(l) ? I.none() : n && l ? I.some(Pl(l, 0)) : I.none(); }, Dg = (e, t, n, o) => { const r = e.getRoot(); let s, a = !1, i = n ? o.startContainer : o.endContainer, l = n ? o.startOffset : o.endOffset; const d = qr(i) && l === i.childNodes.length, c = e.schema.getNonEmptyElements(); let u = n; if (zi(i))
        return I.none(); if (qr(i) && l > i.childNodes.length - 1 && (u = !1), rs(i) && (i = r, l = 0), i === r) {
        if (u && (s = i.childNodes[l > 0 ? l - 1 : 0], s)) {
            if (zi(s))
                return I.none();
            if (c[s.nodeName] || Zr(s))
                return I.none();
        }
        if (i.hasChildNodes()) {
            if (l = Math.min(!u && l > 0 ? l - 1 : l, i.childNodes.length - 1), i = i.childNodes[l], l = es(i) && d ? i.data.length : 0, !t && i === r.lastChild && Zr(i))
                return I.none();
            if (((e, t) => { let n = t; for (; n && n !== e;) {
                if (cs(n))
                    return !0;
                n = n.parentNode;
            } return !1; })(r, i) || zi(i))
                return I.none();
            if (hs(i))
                return I.none();
            if (i.hasChildNodes() && !Zr(i)) {
                s = i;
                const t = new Fr(i, r);
                do {
                    if (cs(s) || zi(s)) {
                        a = !1;
                        break;
                    }
                    if (es(s) && s.data.length > 0) {
                        l = u ? 0 : s.data.length, i = s, a = !0;
                        break;
                    }
                    if (c[s.nodeName.toLowerCase()] && !fs(s)) {
                        l = e.nodeIndex(s), i = s.parentNode, u || l++, a = !0;
                        break;
                    }
                } while (s = u ? t.next() : t.prev());
            }
        }
    } return t && (es(i) && 0 === l && Pg(e, d, t, !0, i).each((e => { i = e.container(), l = e.offset(), a = !0; })), qr(i) && (s = i.childNodes[l], s || (s = i.childNodes[l - 1]), !s || !as(s) || (e => { var t; return "A" === (null === (t = e.previousSibling) || void 0 === t ? void 0 : t.nodeName); })(s) || Bg(e, s, !1) || Bg(e, s, !0) || Pg(e, d, t, !0, s).each((e => { i = e.container(), l = e.offset(), a = !0; })))), u && !t && es(i) && l === i.data.length && Pg(e, d, t, !1, i).each((e => { i = e.container(), l = e.offset(), a = !0; })), a && i ? I.some(Pl(i, l)) : I.none(); }, Lg = (e, t) => { const n = t.collapsed, o = t.cloneRange(), r = Pl.fromRangeStart(t); return Dg(e, n, !0, o).each((e => { n && Pl.isAbove(r, e) || o.setStart(e.container(), e.offset()); })), n || Dg(e, n, !1, o).each((e => { o.setEnd(e.container(), e.offset()); })), n && o.collapse(!0), Rg(t, o) ? I.none() : I.some(o); }, Mg = (e, t) => e.splitText(t), Ig = e => { let t = e.startContainer, n = e.startOffset, o = e.endContainer, r = e.endOffset; if (t === o && es(t)) {
        if (n > 0 && n < t.data.length)
            if (o = Mg(t, n), t = o.previousSibling, r > n) {
                r -= n;
                const e = Mg(o, r).previousSibling;
                t = o = e, r = e.data.length, n = 0;
            }
            else
                r = 0;
    }
    else if (es(t) && n > 0 && n < t.data.length && (t = Mg(t, n), n = 0), es(o) && r > 0 && r < o.data.length) {
        const e = Mg(o, r).previousSibling;
        o = e, r = e.data.length;
    } return { startContainer: t, startOffset: n, endContainer: o, endOffset: r }; }, Fg = e => ({ walk: (t, n) => Hf(e, t, n), split: Ig, expand: (t, n = { type: "word" }) => { if ("word" === n.type) {
            const n = jf(e, t, [{ inline: "span" }], { includeTrailingSpace: !1, expandToBlock: !1 }), o = e.createRng();
            return o.setStart(n.startContainer, n.startOffset), o.setEnd(n.endContainer, n.endOffset), o;
        } return t; }, normalize: t => Lg(e, t).fold(L, (e => (t.setStart(e.startContainer, e.startOffset), t.setEnd(e.endContainer, e.endOffset), !0))) });
    Fg.compareRanges = Rg, Fg.getCaretRangeFromPoint = Ng, Fg.getSelectedNode = tl, Fg.getNode = nl;
    const Ug = e => "textarea" === xn(e), zg = (e, t) => { const n = (e => { const t = e.dom.ownerDocument, n = t.body, o = t.defaultView, r = t.documentElement; if (n === e.dom)
        return Ho(n.offsetLeft, n.offsetTop); const s = $o(null == o ? void 0 : o.pageYOffset, r.scrollTop), a = $o(null == o ? void 0 : o.pageXOffset, r.scrollLeft), i = $o(r.clientTop, n.clientTop), l = $o(r.clientLeft, n.clientLeft); return Vo(e).translate(a - l, s - i); })(e), o = (e => zo.get(e))(e); return { element: e, bottom: n.top + o, height: o, pos: n, cleanup: t }; }, jg = (e, t, n, o) => { qg(e, ((r, s) => $g(e, t, n, o)), n); }, Hg = (e, t, n, o, r) => { const s = { elm: o.element.dom, alignToTop: r }; ((e, t) => e.dispatch("ScrollIntoView", t).isDefaultPrevented())(e, s) || (n(e, t, qo(t).top, o, r), ((e, t) => { e.dispatch("AfterScrollIntoView", t); })(e, s)); }, $g = (e, t, n, o) => { const r = mn(e.getBody()), s = mn(e.getDoc()); r.dom.offsetWidth; const a = ((e, t) => { const n = ((e, t) => { const n = Hn(e); if (0 === n.length || Ug(e))
        return { element: e, offset: t }; if (t < n.length && !Ug(n[t]))
        return { element: n[t], offset: 0 }; {
        const o = n[n.length - 1];
        return Ug(o) ? { element: e, offset: t } : "img" === xn(o) ? { element: o, offset: 1 } : Rn(o) ? { element: o, offset: Go(o).length } : { element: o, offset: Hn(o).length };
    } })(e, t), o = dn('<span data-mce-bogus="all" style="display: inline-block;">\ufeff</span>'); return ao(n.element, o), zg(o, (() => Eo(o))); })(mn(n.startContainer), n.startOffset); Hg(e, s, t, a, o), a.cleanup(); }, Vg = (e, t, n, o) => { const r = mn(e.getDoc()); Hg(e, r, n, (e => zg(mn(e), _))(t), o); }, qg = (e, t, n) => { const o = n.startContainer, r = n.startOffset, s = n.endContainer, a = n.endOffset; t(mn(o), mn(s)); const i = e.dom.createRng(); i.setStart(o, r), i.setEnd(s, a), e.selection.setRng(n); }, Wg = (e, t, n, o, r) => { const s = t.pos; if (o)
        Wo(s.left, Math.max(0, s.top - 30), r);
    else {
        const o = s.top - n + t.height + 30;
        Wo(-e.getBody().getBoundingClientRect().left, o, r);
    } }, Kg = (e, t, n, o, r, s) => { const a = o + n, i = r.pos.top, l = r.bottom, d = l - i >= o; i < n ? Wg(e, r, o, !1 !== s, t) : i > a ? Wg(e, r, o, d ? !1 !== s : !0 === s, t) : l > a && !d && Wg(e, r, o, !0 === s, t); }, Yg = (e, t, n, o, r) => { const s = Dn(t).dom.innerHeight; Kg(e, t, n, s, o, r); }, Xg = (e, t, n, o, r) => { const s = Dn(t).dom.innerHeight; Kg(e, t, n, s, o, r); const a = lg(o.element), i = Ir(window); a.top < i.y ? Ko(o.element, !1 !== r) : a.top > i.bottom && Ko(o.element, !0 === r); }, Gg = (e, t, n) => jg(e, Yg, t, n), Zg = (e, t, n) => Vg(e, t, Yg, n), Qg = (e, t, n) => jg(e, Xg, t, n), Jg = (e, t, n) => Vg(e, t, Xg, n), ep = (e, t, n) => { (e.inline ? Gg : Qg)(e, t, n); }, tp = (e, t) => t.collapsed ? e.isEditable(t.startContainer) : e.isEditable(t.startContainer) && e.isEditable(t.endContainer), np = (e, t, n, o, r) => { const s = n ? t.startContainer : t.endContainer, a = n ? t.startOffset : t.endOffset; return I.from(s).map(mn).map((e => o && t.collapsed ? e : $n(e, r(e, a)).getOr(e))).bind((e => Nn(e) ? I.some(e) : Ln(e).filter(Nn))).map((e => e.dom)).getOr(e); }, op = (e, t, n = !1) => np(e, t, !0, n, ((e, t) => Math.min(Wn(e), t))), rp = (e, t, n = !1) => np(e, t, !1, n, ((e, t) => t > 0 ? t - 1 : t)), sp = (e, t) => { const n = e; for (; e && es(e) && 0 === e.length;)
        e = t ? e.nextSibling : e.previousSibling; return e || n; }, ap = (e, t) => V(t, (t => { const n = e.dispatch("GetSelectionRange", { range: t }); return n.range !== t ? n.range : t; })), ip = { "#text": 3, "#comment": 8, "#cdata": 4, "#pi": 7, "#doctype": 10, "#document-fragment": 11 }, lp = (e, t, n) => { const o = n ? "lastChild" : "firstChild", r = n ? "prev" : "next"; if (e[o])
        return e[o]; if (e !== t) {
        let n = e[r];
        if (n)
            return n;
        for (let o = e.parent; o && o !== t; o = o.parent)
            if (n = o[r], n)
                return n;
    } }, dp = e => { var t; const n = null !== (t = e.value) && void 0 !== t ? t : ""; if (!zr(n))
        return !1; const o = e.parent; return !o || "span" === o.name && !o.attr("style") || !/^[ ]+$/.test(n); }, cp = e => { const t = "a" === e.name && !e.attr("href") && e.attr("id"); return e.attr("name") || e.attr("id") && !e.firstChild || e.attr("data-mce-bookmark") || t; };
    class up {
        static create(e, t) { const n = new up(e, ip[e] || 1); return t && pe(t, ((e, t) => { n.attr(t, e); })), n; }
        constructor(e, t) { this.name = e, this.type = t, 1 === t && (this.attributes = [], this.attributes.map = {}); }
        replace(e) { const t = this; return e.parent && e.remove(), t.insert(e, t), t.remove(), t; }
        attr(e, t) { const n = this; if (!m(e))
            return C(e) && pe(e, ((e, t) => { n.attr(t, e); })), n; const o = n.attributes; if (o) {
            if (void 0 !== t) {
                if (null === t) {
                    if (e in o.map) {
                        delete o.map[e];
                        let t = o.length;
                        for (; t--;)
                            if (o[t].name === e)
                                return o.splice(t, 1), n;
                    }
                    return n;
                }
                if (e in o.map) {
                    let n = o.length;
                    for (; n--;)
                        if (o[n].name === e) {
                            o[n].value = t;
                            break;
                        }
                }
                else
                    o.push({ name: e, value: t });
                return o.map[e] = t, n;
            }
            return o.map[e];
        } }
        clone() { const e = this, t = new up(e.name, e.type), n = e.attributes; if (n) {
            const e = [];
            e.map = {};
            for (let t = 0, o = n.length; t < o; t++) {
                const o = n[t];
                "id" !== o.name && (e[e.length] = { name: o.name, value: o.value }, e.map[o.name] = o.value);
            }
            t.attributes = e;
        } return t.value = e.value, t; }
        wrap(e) { const t = this; return t.parent && (t.parent.insert(e, t), e.append(t)), t; }
        unwrap() { const e = this; for (let t = e.firstChild; t;) {
            const n = t.next;
            e.insert(t, e, !0), t = n;
        } e.remove(); }
        remove() { const e = this, t = e.parent, n = e.next, o = e.prev; return t && (t.firstChild === e ? (t.firstChild = n, n && (n.prev = null)) : o && (o.next = n), t.lastChild === e ? (t.lastChild = o, o && (o.next = null)) : n && (n.prev = o), e.parent = e.next = e.prev = null), e; }
        append(e) { const t = this; e.parent && e.remove(); const n = t.lastChild; return n ? (n.next = e, e.prev = n, t.lastChild = e) : t.lastChild = t.firstChild = e, e.parent = t, e; }
        insert(e, t, n) { e.parent && e.remove(); const o = t.parent || this; return n ? (t === o.firstChild ? o.firstChild = e : t.prev && (t.prev.next = e), e.prev = t.prev, e.next = t, t.prev = e) : (t === o.lastChild ? o.lastChild = e : t.next && (t.next.prev = e), e.next = t.next, e.prev = t, t.next = e), e.parent = o, e; }
        getAll(e) { const t = this, n = []; for (let o = t.firstChild; o; o = lp(o, t))
            o.name === e && n.push(o); return n; }
        children() { const e = []; for (let t = this.firstChild; t; t = t.next)
            e.push(t); return e; }
        empty() { const e = this; if (e.firstChild) {
            const t = [];
            for (let n = e.firstChild; n; n = lp(n, e))
                t.push(n);
            let n = t.length;
            for (; n--;) {
                const e = t[n];
                e.parent = e.firstChild = e.lastChild = e.next = e.prev = null;
            }
        } return e.firstChild = e.lastChild = null, e; }
        isEmpty(e, t = {}, n) { var o; const r = this; let s = r.firstChild; if (cp(r))
            return !1; if (s)
            do {
                if (1 === s.type) {
                    if (s.attr("data-mce-bogus"))
                        continue;
                    if (e[s.name])
                        return !1;
                    if (cp(s))
                        return !1;
                }
                if (8 === s.type)
                    return !1;
                if (3 === s.type && !dp(s))
                    return !1;
                if (3 === s.type && s.parent && t[s.parent.name] && zr(null !== (o = s.value) && void 0 !== o ? o : ""))
                    return !1;
                if (n && n(s))
                    return !1;
            } while (s = lp(s, r)); return !0; }
        walk(e) { return lp(this, null, e); }
    }
    const mp = an.makeMap("NOSCRIPT STYLE SCRIPT XMP IFRAME NOEMBED NOFRAMES PLAINTEXT", " "), fp = e => m(e.nodeValue) && e.nodeValue.includes(Pi), gp = e => (0 === e.length ? "" : `${V(e, (e => `[${e}]`)).join(",")},`) + '[data-mce-bogus="all"]', pp = e => document.createTreeWalker(e, NodeFilter.SHOW_COMMENT, (e => fp(e) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP)), hp = e => document.createTreeWalker(e, NodeFilter.SHOW_TEXT, (e => { if (fp(e)) {
        const t = e.parentNode;
        return t && _e(mp, t.nodeName) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP;
    } return NodeFilter.FILTER_SKIP; })), bp = e => null !== pp(e).nextNode(), vp = e => null !== hp(e).nextNode(), yp = (e, t) => null !== t.querySelector(gp(e)), Cp = (e, t) => { q(((e, t) => t.querySelectorAll(gp(e)))(e, t), (t => { const n = mn(t); "all" === ho(n, "data-mce-bogus") ? Eo(n) : q(e, (e => { vo(n, e) && yo(n, e); })); })); }, wp = e => { let t = e.nextNode(); for (; null !== t;)
        t.nodeValue = null, t = e.nextNode(); }, Ep = S(wp, pp), xp = S(wp, hp), _p = (e, t) => { const n = [{ condition: T(yp, t), action: T(Cp, t) }, { condition: bp, action: Ep }, { condition: vp, action: xp }]; let o = e, r = !1; return q(n, (({ condition: t, action: n }) => { t(o) && (r || (o = e.cloneNode(!0), r = !0), n(o)); })), o; }, Sp = e => { const t = vr(e, "[data-mce-bogus]"); q(t, (e => { "all" === ho(e, "data-mce-bogus") ? Eo(e) : _i(e) ? (ao(e, un(at)), Eo(e)) : xo(e); })); }, kp = e => { const t = vr(e, "input"); q(t, (e => { yo(e, "name"); })); }, Np = (e, t, n) => { let o; return o = "raw" === t.format ? an.trim(Li(_p(n, e.serializer.getTempAttrs()).innerHTML)) : "text" === t.format ? ((e, t) => { const n = e.getDoc(), o = Yn(mn(e.getBody())), r = cn("div", n); go(r, "data-mce-bogus", "all"), Do(r, { position: "fixed", left: "-9999999px", top: "0" }), Ao(r, t.innerHTML), Sp(r), kp(r); const s = (e => Kn(e) ? e : mn(Pn(e).dom.body))(o); co(s, r); const a = Li(r.dom.innerText); return Eo(r), a; })(e, n) : "tree" === t.format ? e.serializer.serialize(n, t) : ((e, t) => { const n = Ad(e), o = new RegExp(`^(<${n}[^>]*>(&nbsp;|&#160;|\\s|\xa0|<br \\/>|)<\\/${n}>[\r\n]*|<br \\/>[\r\n]*)$`); return t.replace(o, ""); })(e, e.serializer.serialize(n, t)), "text" !== t.format && !Ti(mn(n)) && m(o) ? an.trim(o) : o; }, Rp = an.makeMap, Ap = e => { const t = [], n = (e = e || {}).indent, o = Rp(e.indent_before || ""), r = Rp(e.indent_after || ""), s = ua.getEncodeFunc(e.entity_encoding || "raw", e.entities), a = "xhtml" !== e.element_format; return { start: (e, i, l) => { if (n && o[e] && t.length > 0) {
            const e = t[t.length - 1];
            e.length > 0 && "\n" !== e && t.push("\n");
        } if (t.push("<", e), i)
            for (let e = 0, n = i.length; e < n; e++) {
                const n = i[e];
                t.push(" ", n.name, '="', s(n.value, !0), '"');
            } if (t[t.length] = !l || a ? ">" : " />", l && n && r[e] && t.length > 0) {
            const e = t[t.length - 1];
            e.length > 0 && "\n" !== e && t.push("\n");
        } }, end: e => { let o; t.push("</", e, ">"), n && r[e] && t.length > 0 && (o = t[t.length - 1], o.length > 0 && "\n" !== o && t.push("\n")); }, text: (e, n) => { e.length > 0 && (t[t.length] = n ? e : s(e)); }, cdata: e => { t.push("<![CDATA[", e, "]]>"); }, comment: e => { t.push("\x3c!--", e, "--\x3e"); }, pi: (e, o) => { o ? t.push("<?", e, " ", s(o), "?>") : t.push("<?", e, "?>"), n && t.push("\n"); }, doctype: e => { t.push("<!DOCTYPE", e, ">", n ? "\n" : ""); }, reset: () => { t.length = 0; }, getContent: () => t.join("").replace(/\n$/, "") }; }, Tp = (e = {}, t = Ra()) => { const n = Ap(e); return e.validate = !("validate" in e) || e.validate, { serialize: o => { const r = e.validate, s = { 3: e => { var t; n.text(null !== (t = e.value) && void 0 !== t ? t : "", e.raw); }, 8: e => { var t; n.comment(null !== (t = e.value) && void 0 !== t ? t : ""); }, 7: e => { n.pi(e.name, e.value); }, 10: e => { var t; n.doctype(null !== (t = e.value) && void 0 !== t ? t : ""); }, 4: e => { var t; n.cdata(null !== (t = e.value) && void 0 !== t ? t : ""); }, 11: e => { let t = e; if (t = t.firstChild)
                do {
                    a(t);
                } while (t = t.next); } }; n.reset(); const a = e => { var o; const i = s[e.type]; if (i)
            i(e);
        else {
            const s = e.name, i = s in t.getVoidElements();
            let l = e.attributes;
            if (r && l && l.length > 1) {
                const n = [];
                n.map = {};
                const o = t.getElementRule(e.name);
                if (o) {
                    for (let e = 0, t = o.attributesOrder.length; e < t; e++) {
                        const t = o.attributesOrder[e];
                        if (t in l.map) {
                            const e = l.map[t];
                            n.map[t] = e, n.push({ name: t, value: e });
                        }
                    }
                    for (let e = 0, t = l.length; e < t; e++) {
                        const t = l[e].name;
                        if (!(t in n.map)) {
                            const e = l.map[t];
                            n.map[t] = e, n.push({ name: t, value: e });
                        }
                    }
                    l = n;
                }
            }
            if (n.start(s, l, i), ks(s))
                m(e.value) && n.text(e.value, !0), n.end(s);
            else if (!i) {
                let t = e.firstChild;
                if (t) {
                    "pre" !== s && "textarea" !== s || 3 !== t.type || "\n" !== (null === (o = t.value) || void 0 === o ? void 0 : o[0]) || n.text("\n", !0);
                    do {
                        a(t);
                    } while (t = t.next);
                }
                n.end(s);
            }
        } }; return 1 !== o.type || e.inner ? 3 === o.type ? s[3](o) : s[11](o) : a(o), n.getContent(); } }; }, Op = new Set;
    q(["margin", "margin-left", "margin-right", "margin-top", "margin-bottom", "padding", "padding-left", "padding-right", "padding-top", "padding-bottom", "border", "border-width", "border-style", "border-color", "background", "background-attachment", "background-clip", "background-image", "background-origin", "background-position", "background-repeat", "background-size", "float", "position", "left", "right", "top", "bottom", "z-index", "display", "transform", "width", "max-width", "min-width", "height", "max-height", "min-height", "overflow", "overflow-x", "overflow-y", "text-overflow", "vertical-align", "transition", "transition-delay", "transition-duration", "transition-property", "transition-timing-function"], (e => { Op.add(e); }));
    const Bp = new Set;
    q(["background-color"], (e => { Bp.add(e); }));
    const Pp = ["font", "text-decoration", "text-emphasis"], Dp = (e, t) => fe(((e, t) => e.parseStyle(e.getAttrib(t, "style")))(e, t)), Lp = (e, t) => $(Dp(e, t), (e => (e => Op.has(e))(e))), Mp = (e, t, n) => I.from(n.container()).filter(es).exists((o => { const r = e ? 0 : -1; return t(o.data.charAt(n.offset() + r)); })), Ip = T(Mp, !0, qm), Fp = T(Mp, !1, qm), Up = e => { const t = e.container(); return es(t) && (0 === t.data.length || Di(t.data) && Yf.isBookmarkNode(t.parentNode)); }, zp = (e, t) => n => Yu(e ? 0 : -1, n).filter(t).isSome(), jp = e => is(e) && "block" === Lo(mn(e), "display"), Hp = e => cs(e) && !(e => qr(e) && "all" === e.getAttribute("data-mce-bogus"))(e), $p = zp(!0, jp), Vp = zp(!1, jp), qp = zp(!0, gs), Wp = zp(!1, gs), Kp = zp(!0, Zr), Yp = zp(!1, Zr), Xp = zp(!0, Hp), Gp = zp(!1, Hp), Zp = (e, t) => ((e, t, n) => wn(t, e) ? In(e, (e => n(e) || Cn(e, t))).slice(0, -1) : [])(e, t, L), Qp = (e, t) => [e].concat(Zp(e, t)), Jp = (e, t, n) => Em(e, t, n, Up), eh = (e, t, n) => Q(Qp(mn(t.container()), e), (e => t => e.isBlock(xn(t)))(n)), th = (e, t, n, o) => Jp(e, t.dom, n).forall((e => eh(t, n, o).fold((() => !Ku(e, n, t.dom)), (o => !Ku(e, n, t.dom) && wn(o, mn(e.container())))))), nh = (e, t, n, o) => eh(t, n, o).fold((() => Jp(e, t.dom, n).forall((e => !Ku(e, n, t.dom)))), (t => Jp(e, t.dom, n).isNone())), oh = T(nh, !1), rh = T(nh, !0), sh = T(th, !1), ah = T(th, !0), ih = e => om(e).exists(_i), lh = (e, t, n, o) => { const r = Y(Qp(mn(n.container()), t), (e => o.isBlock(xn(e)))), s = le(r).getOr(t); return Cm(e, s.dom, n).filter(ih); }, dh = (e, t, n) => om(t).exists(_i) || lh(!0, e, t, n).isSome(), ch = (e, t, n) => (e => I.from(e.getNode(!0)).map(mn))(t).exists(_i) || lh(!1, e, t, n).isSome(), uh = T(lh, !1), mh = T(lh, !0), fh = e => Pl.isTextPosition(e) && !e.isAtStart() && !e.isAtEnd(), gh = (e, t, n) => { const o = Y(Qp(mn(t.container()), e), (e => n.isBlock(xn(e)))); return le(o).getOr(e); }, ph = (e, t, n) => fh(t) ? Fp(t) : Fp(t) || Sm(gh(e, t, n).dom, t).exists(Fp), hh = (e, t, n) => fh(t) ? Ip(t) : Ip(t) || _m(gh(e, t, n).dom, t).exists(Ip), bh = e => om(e).bind((e => tr(e, Nn))).exists((e => (e => H(["pre", "pre-wrap"], e))(Lo(e, "white-space")))), vh = (e, t) => n => { return o = new Fr(n, e)[t](), C(o) && cs(o) && Mu(o); var o; }, yh = (e, t, n) => !bh(t) && (((e, t, n) => ((e, t) => Sm(e.dom, t).isNone())(e, t) || ((e, t) => _m(e.dom, t).isNone())(e, t) || oh(e, t, n) || rh(e, t, n) || ch(e, t, n) || dh(e, t, n))(e, t, n) || ph(e, t, n) || hh(e, t, n)), Ch = (e, t, n) => !bh(t) && (oh(e, t, n) || sh(e, t, n) || ch(e, t, n) || ph(e, t, n) || ((e, t) => { const n = Sm(e.dom, t).getOr(t), o = vh(e.dom, "prev"); return t.isAtStart() && (o(t.container()) || o(n.container())); })(e, t)), wh = (e, t, n) => !bh(t) && (rh(e, t, n) || ah(e, t, n) || dh(e, t, n) || hh(e, t, n) || ((e, t) => { const n = _m(e.dom, t).getOr(t), o = vh(e.dom, "next"); return t.isAtEnd() && (o(t.container()) || o(n.container())); })(e, t)), Eh = (e, t, n) => Ch(e, t, n) || wh(e, (e => { const t = e.container(), n = e.offset(); return es(t) && n < t.data.length ? Pl(t, n + 1) : e; })(t), n), xh = (e, t) => $m(e.charAt(t)), _h = (e, t) => qm(e.charAt(t)), Sh = (e, t, n, o) => { const r = t.data, s = Pl(t, 0); return n || !xh(r, 0) || Eh(e, s, o) ? !!(n && _h(r, 0) && Ch(e, s, o)) && (t.data = it + r.slice(1), !0) : (t.data = " " + r.slice(1), !0); }, kh = (e, t, n, o) => { const r = t.data, s = Pl(t, r.length - 1); return n || !xh(r, r.length - 1) || Eh(e, s, o) ? !!(n && _h(r, r.length - 1) && wh(e, s, o)) && (t.data = r.slice(0, -1) + it, !0) : (t.data = r.slice(0, -1) + " ", !0); }, Nh = (e, t, n) => { const o = t.container(); if (!es(o))
        return I.none(); if ((e => { const t = e.container(); return es(t) && Ke(t.data, it); })(t)) {
        const r = Sh(e, o, !1, n) || (e => { const t = e.data, n = (e => { const t = e.split(""); return V(t, ((e, n) => $m(e) && n > 0 && n < t.length - 1 && Wm(t[n - 1]) && Wm(t[n + 1]) ? " " : e)).join(""); })(t); return n !== t && (e.data = n, !0); })(o) || kh(e, o, !1, n);
        return je(r, t);
    } if (Eh(e, t, n)) {
        const r = Sh(e, o, !0, n) || kh(e, o, !0, n);
        return je(r, t);
    } return I.none(); }, Rh = (e, t, n, o) => { if (0 === n)
        return; const r = mn(e), s = er(r, (e => o.isBlock(xn(e)))).getOr(r), a = e.data.slice(t, t + n), i = t + n >= e.data.length && wh(s, Pl(e, e.data.length), o), l = 0 === t && Ch(s, Pl(e, 0), o); e.replaceData(t, n, Hr(a, 4, l, i)); }, Ah = (e, t, n) => { const o = e.data.slice(t), r = o.length - Qe(o).length; Rh(e, t, r, n); }, Th = (e, t, n) => { const o = e.data.slice(0, t), r = o.length - Je(o).length; Rh(e, t - r, r, n); }, Oh = (e, t, n, o, r = !0) => { const s = Je(e.data).length, a = r ? e : t, i = r ? t : e; return r ? a.appendData(i.data) : a.insertData(0, i.data), Eo(mn(i)), o && Ah(a, s, n), a; }, Bh = (e, t) => ((e, t) => { const n = e.container(), o = e.offset(); return !Pl.isTextPosition(e) && n === t.parentNode && o > Pl.before(t).offset(); })(t, e) ? Pl(t.container(), t.offset() - 1) : t, Ph = e => { return fl(e.previousSibling) ? I.some((t = e.previousSibling, es(t) ? Pl(t, t.data.length) : Pl.after(t))) : e.previousSibling ? Nm(e.previousSibling) : I.none(); var t; }, Dh = e => { return fl(e.nextSibling) ? I.some((t = e.nextSibling, es(t) ? Pl(t, 0) : Pl.before(t))) : e.nextSibling ? km(e.nextSibling) : I.none(); var t; }, Lh = (e, t, n) => ((e, t, n) => e ? ((e, t) => Dh(t).orThunk((() => Ph(t))).orThunk((() => ((e, t) => _m(e, Pl.after(t)).orThunk((() => Sm(e, Pl.before(t)))))(e, t))))(t, n) : ((e, t) => Ph(t).orThunk((() => Dh(t))).orThunk((() => ((e, t) => I.from(t.previousSibling ? t.previousSibling : t.parentNode).bind((t => Sm(e, Pl.before(t)))).orThunk((() => _m(e, Pl.after(t)))))(e, t))))(t, n))(e, t, n).map(T(Bh, n)), Mh = (e, t, n) => { n.fold((() => { e.focus(); }), (n => { e.selection.setRng(n.toRange(), t); })); }, Ih = (e, t) => t && _e(e.schema.getBlockElements(), xn(t)), Fh = (e, t, n, o = !0, r = !1) => { const s = Lh(t, e.getBody(), n.dom), a = er(n, T(Ih, e), (i = e.getBody(), e => e.dom === i)); var i; const l = ((e, t, n, o) => { const r = Fn(e).filter(Rn), s = Un(e).filter(Rn); return Eo(e), (a = r, i = s, l = t, d = (e, t, r) => { const s = e.dom, a = t.dom, i = s.data.length; return Oh(s, a, n, o), r.container() === a ? Pl(s, i) : r; }, a.isSome() && i.isSome() && l.isSome() ? I.some(d(a.getOrDie(), i.getOrDie(), l.getOrDie())) : I.none()).orThunk((() => (o && (r.each((e => Th(e.dom, e.dom.length, n))), s.each((e => Ah(e.dom, 0, n)))), t))); var a, i, l, d; })(n, s, e.schema, ((e, t) => _e(e.schema.getTextInlineElements(), xn(t)))(e, n)); e.dom.isEmpty(e.getBody()) ? (e.setContent(""), e.selection.setCursorLocation()) : a.bind((t => ((e, t, n) => { if (xs(e, t)) {
        const e = dn('<br data-mce-bogus="1">');
        return n ? q(Hn(t), (e => { kf(e) || Eo(e); })) : wo(t), co(t, e), I.some(Pl.before(e.dom));
    } return I.none(); })(e.schema, t, r))).fold((() => { o && Mh(e, t, l); }), (n => { o && Mh(e, t, I.some(n)); })); }, Uh = /[\u0591-\u07FF\uFB1D-\uFDFF\uFE70-\uFEFC]/, zh = (e, t) => vn(mn(t), tc(e)) && !js(e.schema, t) && e.dom.isEditable(t), jh = e => { var t; return "rtl" === ni.DOM.getStyle(e, "direction", !0) || (e => Uh.test(e))(null !== (t = e.textContent) && void 0 !== t ? t : ""); }, Hh = (e, t, n) => { const o = ((e, t, n) => Y(ni.DOM.getParents(n.container(), "*", t), e))(e, t, n); return I.from(o[o.length - 1]); }, $h = (e, t) => { const n = t.container(), o = t.offset(); return e ? Ui(n) ? es(n.nextSibling) ? Pl(n.nextSibling, 0) : Pl.after(n) : Hi(t) ? Pl(n, o + 1) : t : Ui(n) ? es(n.previousSibling) ? Pl(n.previousSibling, n.previousSibling.data.length) : Pl.before(n) : $i(t) ? Pl(n, o - 1) : t; }, Vh = T($h, !0), qh = T($h, !1), Wh = (e, t) => { const n = e => e.stopImmediatePropagation(); e.on("beforeinput input", n, !0), e.getDoc().execCommand(t), e.off("beforeinput input", n); }, Kh = e => Wh(e, "Delete"), Yh = e => Wh(e, "ForwardDelete"), Xh = e => Si(e) || Ni(e), Gh = (e, t) => wn(e, t) ? tr(t, Xh, (e => t => Fe(Ln(t), e, Cn))(e)) : I.none(), Zh = (e, t = !0) => { e.dom.isEmpty(e.getBody()) && e.setContent("", { no_selection: !t }); }, Qh = (e, t, n) => ze(km(n), Nm(n), ((o, r) => { const s = $h(!0, o), a = $h(!1, r), i = $h(!1, t); return e ? _m(n, i).exists((e => e.isEqual(a) && t.isEqual(s))) : Sm(n, i).exists((e => e.isEqual(s) && t.isEqual(a))); })).getOr(!0), Jh = e => { var t; return (8 === _n(t = e) || "#comment" === xn(t) ? Fn(e) : qn(e)).bind(Jh).orThunk((() => I.some(e))); }, eb = (e, t, n, o = !0) => { var r; t.deleteContents(); const s = Jh(n).getOr(n), a = mn(null !== (r = e.dom.getParent(s.dom, e.dom.isBlock)) && void 0 !== r ? r : n.dom); if (a.dom === e.getBody() ? Zh(e, o) : xs(e.schema, a, { checkRootAsContent: !1 }) && (Bi(a), o && e.selection.setCursorLocation(a.dom, 0)), !Cn(n, a)) {
        const t = Fe(Ln(a), n) ? [] : Ln(i = a).map(Hn).map((e => Y(e, (e => !Cn(i, e))))).getOr([]);
        q(t.concat(Hn(n)), (t => { Cn(t, a) || wn(t, a) || !xs(e.schema, t) || Eo(t); }));
    } var i; }, tb = e => vr(e, "td,th"), nb = (e, t) => Gm(mn(e), t), ob = (e, t) => ({ start: e, end: t }), rb = ke([{ singleCellTable: ["rng", "cell"] }, { fullTable: ["table"] }, { partialTable: ["cells", "outsideDetails"] }, { multiTable: ["startTableCells", "endTableCells", "betweenRng"] }]), sb = (e, t) => sr(mn(e), "td,th", t), ab = e => !Cn(e.start, e.end), ib = (e, t) => Gm(e.start, t).bind((n => Gm(e.end, t).bind((e => je(Cn(n, e), n))))), lb = e => t => ib(t, e).map((e => ((e, t, n) => ({ rng: e, table: t, cells: n }))(t, e, tb(e)))), db = (e, t, n, o) => { if (n.collapsed || !e.forall(ab))
        return I.none(); if (t.isSameTable) {
        const t = e.bind(lb(o));
        return I.some({ start: t, end: t });
    } {
        const e = sb(n.startContainer, o), t = sb(n.endContainer, o), r = e.bind((e => t => Gm(t, e).bind((e => de(tb(e)).map((e => ob(t, e))))))(o)).bind(lb(o)), s = t.bind((e => t => Gm(t, e).bind((e => le(tb(e)).map((e => ob(e, t))))))(o)).bind(lb(o));
        return I.some({ start: r, end: s });
    } }, cb = (e, t) => J(e, (e => Cn(e, t))), ub = e => ze(cb(e.cells, e.rng.start), cb(e.cells, e.rng.end), ((t, n) => e.cells.slice(t, n + 1))), mb = (e, t) => { const { startTable: n, endTable: o } = t, r = e.cloneRange(); return n.each((e => r.setStartAfter(e.dom))), o.each((e => r.setEndBefore(e.dom))), r; }, fb = (e, t) => { const n = (e => t => Cn(e, t))(e), o = ((e, t) => { const n = sb(e.startContainer, t), o = sb(e.endContainer, t); return ze(n, o, ob); })(t, n), r = ((e, t) => { const n = nb(e.startContainer, t), o = nb(e.endContainer, t), r = n.isSome(), s = o.isSome(), a = ze(n, o, Cn).getOr(!1); return (e => ze(e.startTable, e.endTable, ((t, n) => { const o = wr(t, (e => Cn(e, n))), r = wr(n, (e => Cn(e, t))); return o || r ? { ...e, startTable: o ? I.none() : e.startTable, endTable: r ? I.none() : e.endTable, isSameTable: !1, isMultiTable: !1 } : e; })).getOr(e))({ startTable: n, endTable: o, isStartInTable: r, isEndInTable: s, isSameTable: a, isMultiTable: !a && r && s }); })(t, n); return ((e, t, n) => e.exists((e => ((e, t) => !ab(e) && ib(e, t).exists((e => { const t = e.dom.rows; return 1 === t.length && 1 === t[0].cells.length; })))(e, n) && Jm(e.start, t))))(o, t, n) ? o.map((e => rb.singleCellTable(t, e.start))) : r.isMultiTable ? ((e, t, n, o) => db(e, t, n, o).bind((({ start: e, end: o }) => { const r = e.bind(ub).getOr([]), s = o.bind(ub).getOr([]); if (r.length > 0 && s.length > 0) {
        const e = mb(n, t);
        return I.some(rb.multiTable(r, s, e));
    } return I.none(); })))(o, r, t, n) : ((e, t, n, o) => db(e, t, n, o).bind((({ start: e, end: t }) => e.or(t))).bind((e => { const { isSameTable: o } = t, r = ub(e).getOr([]); if (o && e.cells.length === r.length)
        return I.some(rb.fullTable(e.table)); if (r.length > 0) {
        if (o)
            return I.some(rb.partialTable(r, I.none()));
        {
            const e = mb(n, t);
            return I.some(rb.partialTable(r, I.some({ ...t, rng: e })));
        }
    } return I.none(); })))(o, r, t, n); }, gb = e => q(e, (e => { yo(e, "contenteditable"), Bi(e); })), pb = (e, t, n, o) => { const r = n.cloneRange(); o ? (r.setStart(n.startContainer, n.startOffset), r.setEndAfter(t.dom.lastChild)) : (r.setStartBefore(t.dom.firstChild), r.setEnd(n.endContainer, n.endOffset)), yb(e, r, t, !1).each((e => e())); }, hb = e => { const t = Xm(e), n = mn(e.selection.getNode()); ms(n.dom) && xs(e.schema, n) ? e.selection.setCursorLocation(n.dom, 0) : e.selection.collapse(!0), t.length > 1 && $(t, (e => Cn(e, n))) && go(n, "data-mce-selected", "1"); }, bb = (e, t, n) => I.some((() => { const o = e.selection.getRng(), r = n.bind((({ rng: n, isStartInTable: r }) => { const s = ((e, t) => I.from(e.dom.getParent(t, e.dom.isBlock)).map(mn))(e, r ? n.endContainer : n.startContainer); n.deleteContents(), ((e, t, n) => { n.each((n => { t ? Eo(n) : (Bi(n), e.selection.setCursorLocation(n.dom, 0)); })); })(e, r, s.filter(T(xs, e.schema))); const a = r ? t[0] : t[t.length - 1]; return pb(e, a, o, r), xs(e.schema, a) ? I.none() : I.some(r ? t.slice(1) : t.slice(0, -1)); })).getOr(t); gb(r), hb(e); })), vb = (e, t, n, o) => I.some((() => { const r = e.selection.getRng(), s = t[0], a = n[n.length - 1]; pb(e, s, r, !0), pb(e, a, r, !1); const i = xs(e.schema, s) ? t : t.slice(1), l = xs(e.schema, a) ? n : n.slice(0, -1); gb(i.concat(l)), o.deleteContents(), hb(e); })), yb = (e, t, n, o = !0) => I.some((() => { eb(e, t, n, o); })), Cb = (e, t) => I.some((() => Fh(e, !1, t))), wb = (e, t) => Q(Qp(t, e), Ai), Eb = (e, t) => Q(Qp(t, e), On("caption")), xb = (e, t) => I.some((() => { Bi(t), e.selection.setCursorLocation(t.dom, 0); })), _b = (e, t) => e ? Kp(t) : Yp(t), Sb = (e, t, n) => { const o = mn(e.getBody()); return Eb(o, n).fold((() => ((e, t, n, o) => { const r = Pl.fromRangeStart(e.selection.getRng()); return wb(n, o).bind((o => xs(e.schema, o, { checkRootAsContent: !1 }) ? xb(e, o) : ((e, t, n, o, r) => wm(n, e.getBody(), r).bind((e => wb(t, mn(e.getNode())).bind((e => Cn(e, o) ? I.none() : I.some(_))))))(e, n, t, o, r))); })(e, t, o, n).orThunk((() => je(((e, t) => { const n = Pl.fromRangeStart(e.selection.getRng()); return _b(t, n) || Cm(t, e.getBody(), n).exists((e => _b(t, e))); })(e, t), _)))), (n => ((e, t, n, o) => { const r = Pl.fromRangeStart(e.selection.getRng()); return xs(e.schema, o) ? xb(e, o) : ((e, t, n, o, r) => wm(n, e.getBody(), r).fold((() => I.some(_)), (s => ((e, t, n, o) => km(e.dom).bind((r => Nm(e.dom).map((e => t ? n.isEqual(r) && o.isEqual(e) : n.isEqual(e) && o.isEqual(r))))).getOr(!0))(o, n, r, s) ? ((e, t) => xb(e, t))(e, o) : ((e, t, n) => Eb(e, mn(n.getNode())).fold((() => I.some(_)), (e => je(!Cn(e, t), _))))(t, o, s))))(e, n, t, o, r); })(e, t, o, n))); }, kb = (e, t) => { const n = mn(e.selection.getStart(!0)), o = Xm(e); return e.selection.isCollapsed() && 0 === o.length ? Sb(e, t, n) : ((e, t, n) => { const o = mn(e.getBody()), r = e.selection.getRng(); return 0 !== n.length ? bb(e, n, I.none()) : ((e, t, n, o) => Eb(t, o).fold((() => ((e, t, n) => fb(t, n).bind((t => t.fold(T(yb, e), T(Cb, e), T(bb, e), T(vb, e)))))(e, t, n)), (t => ((e, t) => xb(e, t))(e, t))))(e, o, r, t); })(e, n, o); }, Nb = (e, t) => { let n = t; for (; n && n !== e;) {
        if (ds(n) || cs(n))
            return n;
        n = n.parentNode;
    } return null; }, Rb = ["data-ephox-", "data-mce-", "data-alloy-", "data-snooker-", "_"], Ab = an.each, Tb = e => { const t = e.dom, n = new Set(e.serializer.getTempAttrs()), o = e => $(Rb, (t => Ye(e, t))) || n.has(e); return { compare: (e, n) => { if (e.nodeName !== n.nodeName || e.nodeType !== n.nodeType)
            return !1; const r = e => { const n = {}; return Ab(t.getAttribs(e), (r => { const s = r.nodeName.toLowerCase(); "style" === s || o(s) || (n[s] = t.getAttrib(e, s)); })), n; }, s = (e, t) => { for (const n in e)
            if (_e(e, n)) {
                const o = t[n];
                if (v(o))
                    return !1;
                if (e[n] !== o)
                    return !1;
                delete t[n];
            } for (const e in t)
            if (_e(t, e))
                return !1; return !0; }; if (qr(e) && qr(n)) {
            if (!s(r(e), r(n)))
                return !1;
            if (!s(t.parseStyle(t.getAttrib(e, "style")), t.parseStyle(t.getAttrib(n, "style"))))
                return !1;
        } return !Hm(e) && !Hm(n); }, isAttributeInternal: o }; }, Ob = e => ["h1", "h2", "h3", "h4", "h5", "h6"].includes(e.name), Bb = (e, t, n, o) => { const r = n.name; for (let t = 0, s = e.length; t < s; t++) {
        const s = e[t];
        if (s.name === r) {
            const e = o.nodes[r];
            e ? e.nodes.push(n) : o.nodes[r] = { filter: s, nodes: [n] };
        }
    } if (n.attributes)
        for (let e = 0, r = t.length; e < r; e++) {
            const r = t[e], s = r.name;
            if (s in n.attributes.map) {
                const e = o.attributes[s];
                e ? e.nodes.push(n) : o.attributes[s] = { filter: r, nodes: [n] };
            }
        } }, Pb = (e, t) => { const n = (e, n) => { pe(e, (e => { const o = ce(e.nodes); q(e.filter.callbacks, (r => { for (let t = o.length - 1; t >= 0; t--) {
        const r = o[t];
        (n ? void 0 !== r.attr(e.filter.name) : r.name === e.filter.name) && !y(r.parent) || o.splice(t, 1);
    } o.length > 0 && r(o, e.filter.name, t); })); })); }; n(e.nodes, !1), n(e.attributes, !0); }, Db = (e, t, n, o = {}) => { const r = ((e, t, n) => { const o = { nodes: {}, attributes: {} }; return n.firstChild && (n => { let r = n; for (; r = r.walk();)
        Bb(e, t, r, o); })(n), o; })(e, t, n); Pb(r, o); }, Lb = (e, t, n, o) => { if ((e.pad_empty_with_br || t.insert) && n(o)) {
        const e = new up("br", 1);
        t.insert && e.attr("data-mce-bogus", "1"), o.empty().append(e);
    }
    else
        o.empty().append(new up("#text", 3)).value = it; }, Mb = (e, t) => { const n = null == e ? void 0 : e.firstChild; return C(n) && n === e.lastChild && n.name === t; }, Ib = (e, t, n, o) => o.isEmpty(t, n, (t => ((e, t) => { const n = e.getElementRule(t.name); return !0 === (null == n ? void 0 : n.paddEmpty); })(e, t))), Fb = e => { let t; for (let n = e; n; n = n.parent) {
        const e = n.attr("contenteditable");
        if ("false" === e)
            break;
        "true" === e && (t = n);
    } return I.from(t); }, Ub = (e, t, n = e.parent) => { if (t.getSpecialElements()[e.name])
        e.empty().remove();
    else {
        const o = e.children();
        for (const e of o)
            n && !t.isValidChild(n.name, e.name) && Ub(e, t, n);
        e.unwrap();
    } }, zb = (e, t, n, o = _) => { const r = t.getTextBlockElements(), s = t.getNonEmptyElements(), a = t.getWhitespaceElements(), i = an.makeMap("tr,td,th,tbody,thead,tfoot,table,summary"), l = new Set, d = e => e !== n && !i[e.name]; for (let n = 0; n < e.length; n++) {
        const i = e[n];
        let c, u, m;
        if (!i.parent || l.has(i))
            continue;
        if (r[i.name] && "li" === i.parent.name) {
            let e = i.next;
            for (; e && r[e.name];)
                e.name = "li", l.add(e), i.parent.insert(e, i.parent), e = e.next;
            i.unwrap();
            continue;
        }
        const f = [i];
        for (c = i.parent; c && !t.isValidChild(c.name, i.name) && d(c); c = c.parent)
            f.push(c);
        if (c && f.length > 1)
            if (jb(t, i, c))
                Ub(i, t);
            else {
                f.reverse(), u = f[0].clone(), o(u);
                let e = u;
                for (let n = 0; n < f.length - 1; n++) {
                    t.isValidChild(e.name, f[n].name) && n > 0 ? (m = f[n].clone(), o(m), e.append(m)) : m = e;
                    for (let e = f[n].firstChild; e && e !== f[n + 1];) {
                        const t = e.next;
                        m.append(e), e = t;
                    }
                    e = m;
                }
                Ib(t, s, a, u) ? c.insert(i, f[0], !0) : (c.insert(u, f[0], !0), c.insert(i, u)), c = f[0], (Ib(t, s, a, c) || Mb(c, "br")) && c.empty().remove();
            }
        else if (i.parent) {
            if ("li" === i.name) {
                let e = i.prev;
                if (e && ("ul" === e.name || "ol" === e.name)) {
                    e.append(i);
                    continue;
                }
                if (e = i.next, e && ("ul" === e.name || "ol" === e.name) && e.firstChild) {
                    e.insert(i, e.firstChild, !0);
                    continue;
                }
                const t = new up("ul", 1);
                o(t), i.wrap(t);
                continue;
            }
            if (t.isValidChild(i.parent.name, "div") && t.isValidChild("div", i.name)) {
                const e = new up("div", 1);
                o(e), i.wrap(e);
            }
            else
                Ub(i, t);
        }
    } }, jb = (e, t, n = t.parent) => !(!n || (!e.children[t.name] || e.isValidChild(n.name, t.name)) && ("a" !== t.name || !(e => { let t = e; for (; t;) {
        if ("a" === t.name)
            return !0;
        t = t.parent;
    } return !1; })(n)) && (!(e => "summary" === e.name)(n) || !Ob(t) || (null == n ? void 0 : n.firstChild) === t && (null == n ? void 0 : n.lastChild) === t)), Hb = e => e.collapsed ? e : (e => { const t = Pl.fromRangeStart(e), n = Pl.fromRangeEnd(e), o = e.commonAncestorContainer; return Cm(!1, o, n).map((r => !Ku(t, n, o) && Ku(t, r, o) ? ((e, t, n, o) => { const r = document.createRange(); return r.setStart(e, t), r.setEnd(n, o), r; })(t.container(), t.offset(), r.container(), r.offset()) : e)).getOr(e); })(e), $b = (e, t) => { let n = t.firstChild, o = t.lastChild; return n && "meta" === n.name && (n = n.next), o && "mce_marker" === o.attr("id") && (o = o.prev), ((e, t) => { const n = e.getNonEmptyElements(); return C(t) && (t.isEmpty(n) || ((e, t) => e.getBlockElements()[t.name] && (e => C(e.firstChild) && e.firstChild === e.lastChild)(t) && (e => "br" === e.name || e.value === it)(t.firstChild))(e, t)); })(e, o) && (o = null == o ? void 0 : o.prev), !(!n || n !== o || "ul" !== n.name && "ol" !== n.name); }, Vb = e => { return e.length > 0 && (!(n = e[e.length - 1]).firstChild || C(null == (t = n) ? void 0 : t.firstChild) && t.firstChild === t.lastChild && (e => e.data === it || as(e))(t.firstChild)) ? e.slice(0, -1) : e; var t, n; }, qb = (e, t) => { const n = e.getParent(t, e.isBlock); return n && "LI" === n.nodeName ? n : null; }, Wb = (e, t) => { const n = Pl.after(e), o = hm(t).prev(n); return o ? o.toRange() : null; }, Kb = (e, t, n, o) => { const r = ((e, t, n) => { const o = t.serialize(n); return (e => { var t, n; const o = e.firstChild, r = e.lastChild; return o && "META" === o.nodeName && (null === (t = o.parentNode) || void 0 === t || t.removeChild(o)), r && "mce_marker" === r.id && (null === (n = r.parentNode) || void 0 === n || n.removeChild(r)), e; })(e.createFragment(o)); })(t, e, o), s = qb(t, n.startContainer), a = Vb((i = r.firstChild, Y(null !== (l = null == i ? void 0 : i.childNodes) && void 0 !== l ? l : [], (e => "LI" === e.nodeName)))); var i, l; const d = t.getRoot(), c = e => { const o = Pl.fromRangeStart(n), r = hm(t.getRoot()), a = 1 === e ? r.prev(o) : r.next(o), i = null == a ? void 0 : a.getNode(); return !i || qb(t, i) !== s; }; return s ? c(1) ? ((e, t, n) => { const o = e.parentNode; return o && an.each(t, (t => { o.insertBefore(t, e); })), ((e, t) => { const n = Pl.before(e), o = hm(t).next(n); return o ? o.toRange() : null; })(e, n); })(s, a, d) : c(2) ? ((e, t, n, o) => (o.insertAfter(t.reverse(), e), Wb(t[0], n)))(s, a, d, t) : ((e, t, n, o) => { const r = ((e, t) => { const n = t.cloneRange(), o = t.cloneRange(); return n.setStartBefore(e), o.setEndAfter(e), [n.cloneContents(), o.cloneContents()]; })(e, o), s = e.parentNode; return s && (s.insertBefore(r[0], e), an.each(t, (t => { s.insertBefore(t, e); })), s.insertBefore(r[1], e), s.removeChild(e)), Wb(t[t.length - 1], n); })(s, a, d, n) : null; }, Yb = ["pre"], Xb = ms, Gb = e => Qb(e, es), Zb = e => Qb(e, ls), Qb = (e, t) => e.startContainer === e.endContainer && e.endOffset - e.startOffset == 1 && t(e.startContainer.childNodes[e.startOffset]), Jb = (e, t, n) => { var o, r; const s = e.selection, a = e.dom, i = e.parser, l = n.merge, d = Tp({ validate: !0 }, e.schema), c = '<span id="mce_marker" data-mce-type="bookmark">&#xFEFF;</span>'; n.preserve_zwsp || (t = Li(t)), -1 === t.indexOf("{$caret}") && (t += "{$caret}"), t = t.replace(/\{\$caret\}/, c); let u = s.getRng(); const m = u.startContainer, f = e.getBody(); m === f && s.isCollapsed() && a.isBlock(f.firstChild) && ((e, t) => C(t) && !e.schema.getVoidElements()[t.nodeName])(e, f.firstChild) && a.isEmpty(f.firstChild) && (u = a.createRng(), u.setStart(f.firstChild, 0), u.setEnd(f.firstChild, 0), s.setRng(u)), s.isCollapsed() || (e => { const t = e.dom, n = Hb(e.selection.getRng()); e.selection.setRng(n); const o = t.getParent(n.startContainer, Xb); ((e, t, n) => !!C(n) && n === e.getParent(t.endContainer, Xb) && Jm(mn(n), t))(t, n, o) ? yb(e, n, mn(o)) : Zb(n) || Gb(n) ? n.deleteContents() : e.getDoc().execCommand("Delete", !1); })(e); const g = s.getNode(), p = { context: g.nodeName.toLowerCase(), data: n.data, insert: !0 }, h = i.parse(t, p); if (!0 === n.paste && $b(e.schema, h) && ((e, t) => !!qb(e, t))(a, g))
        return u = Kb(d, a, s.getRng(), h), u && s.setRng(u), t; !0 === n.paste && ((e, t, n, o) => { var r; const s = t.firstChild, a = t.lastChild, i = s === ("bookmark" === a.attr("data-mce-type") ? a.prev : a), l = H(Yb, s.name); if (i && l) {
        const t = "false" !== s.attr("contenteditable"), a = (null === (r = e.getParent(n, e.isBlock)) || void 0 === r ? void 0 : r.nodeName.toLowerCase()) === s.name, i = I.from(Nb(o, n)).forall(ds);
        return t && a && i;
    } return !1; })(a, h, g, e.getBody()) && (null === (o = h.firstChild) || void 0 === o || o.unwrap()), (e => { let t = e; for (; t = t.walk();)
        1 === t.type && t.attr("data-mce-fragment", "1"); })(h); let b = h.lastChild; if (b && "mce_marker" === b.attr("id")) {
        const t = b;
        for (b = b.prev; b && "table" !== b.name; b = b.walk(!0))
            if (3 === b.type || !a.isBlock(b.name)) {
                b.parent && e.schema.isValidChild(b.parent.name, "span") && b.parent.insert(t, b, "br" === b.name);
                break;
            }
    } if (e._selectionOverrides.showBlockCaretContainer(g), p.invalid || ((e, t, n) => { var o; return $(n.children(), Ob) && "SUMMARY" === (null === (o = e.getParent(t, e.isBlock)) || void 0 === o ? void 0 : o.nodeName); })(a, g, h)) {
        e.selection.setContent(c);
        let n, o = s.getNode();
        const l = e.getBody();
        for (rs(o) ? o = n = l : n = o; n && n !== l;)
            o = n, n = n.parentNode;
        t = o === l ? l.innerHTML : a.getOuterHTML(o);
        const u = i.parse(t), m = (e => { for (let t = e; t; t = t.walk())
            if ("mce_marker" === t.attr("id"))
                return I.some(t); return I.none(); })(u), f = m.bind(Fb).getOr(u);
        m.each((e => e.replace(h)));
        const g = h.children(), p = null !== (r = h.parent) && void 0 !== r ? r : u;
        h.unwrap();
        const b = Y(g, (t => jb(e.schema, t, p)));
        zb(b, e.schema, f), Db(i.getNodeFilters(), i.getAttributeFilters(), u), t = d.serialize(u), o === l ? a.setHTML(l, t) : a.setOuterHTML(o, t);
    }
    else
        t = d.serialize(h), ((e, t, n) => { var o; "all" === n.getAttribute("data-mce-bogus") ? null === (o = n.parentNode) || void 0 === o || o.insertBefore(e.dom.createFragment(t), n) : ((e, t) => { if (e.isBlock(t) && e.isEditable(t)) {
            const e = t.childNodes;
            return 1 === e.length && as(e[0]) || 0 === e.length;
        } return !1; })(e.dom, n) ? e.dom.setHTML(n, t) : e.selection.setContent(t, { no_events: !0 }); })(e, t, g); var v; return ((e, t) => { const n = e.schema.getTextInlineElements(), o = e.dom; if (t) {
        const t = e.getBody(), r = Tb(e), s = "*[data-mce-fragment]", a = o.select(s);
        an.each(a, (e => { const a = e => C(n[e.nodeName.toLowerCase()]), i = e => 1 === e.childNodes.length; if (!Lp(o, l = e) && !((e, t) => Lp(e, t) && $(Dp(e, t), (e => (e => Bp.has(e))(e))))(o, l) && a(e) && i(e)) {
            const n = Dp(o, e), l = (e, t) => ne(e, (e => H(t, e))), d = t => i(e) && o.is(t, s) && a(t) && (t.nodeName === e.nodeName && l(n, Dp(o, t)) || d(t.children[0])), c = n => C(n) && n !== t && (r.compare(e, n) || c(n.parentElement)), u = n => C(n) && n !== t && o.is(n, s) && (((e, t, n) => { const o = Dp(e, t), r = Dp(e, n), s = o => { var r, s; const a = null !== (r = e.getStyle(t, o)) && void 0 !== r ? r : "", i = null !== (s = e.getStyle(n, o)) && void 0 !== s ? s : ""; return et(a) && et(i) && a !== i; }; return $(o, (e => { const t = t => $(t, (t => t === e)); if (!t(r) && t(Pp)) {
                const e = Y(r, (e => $(Pp, (t => Ye(e, t)))));
                return $(e, s);
            } return s(e); })); })(o, e, n) || u(n.parentElement));
            (d(e.children[0]) || c(e.parentElement) && !u(e.parentElement)) && o.remove(e, !0);
        } var l; }));
    } })(e, l), ((e, t) => { var n, o, r; let s; const a = e.dom, i = e.selection; if (!t)
        return; i.scrollIntoView(t); const l = Nb(e.getBody(), t); if (l && "false" === a.getContentEditable(l))
        return a.remove(t), void i.select(l); let d = a.createRng(); const c = t.previousSibling; if (es(c)) {
        d.setStart(c, null !== (o = null === (n = c.nodeValue) || void 0 === n ? void 0 : n.length) && void 0 !== o ? o : 0);
        const e = t.nextSibling;
        es(e) && (c.appendData(e.data), null === (r = e.parentNode) || void 0 === r || r.removeChild(e));
    }
    else
        d.setStartBefore(t), d.setEndBefore(t); const u = a.getParent(t, a.isBlock); if (a.remove(t), u && a.isEmpty(u)) {
        const t = Xb(u);
        wo(mn(u)), d.setStart(u, 0), d.setEnd(u, 0), t || (e => !!e.getAttribute("data-mce-fragment"))(u) || !(s = (t => { let n = Pl.fromRangeStart(t); return n = hm(e.getBody()).next(n), null == n ? void 0 : n.toRange(); })(d)) ? a.add(u, a.create("br", t ? {} : { "data-mce-bogus": "1" })) : (d = s, a.remove(u));
    } i.setRng(d); })(e, a.get("mce_marker")), v = e.getBody(), an.each(v.getElementsByTagName("*"), (e => { e.removeAttribute("data-mce-fragment"); })), ((e, t, n) => { I.from(e.getParent(t, "td,th")).map(mn).each((e => ((e, t) => { qn(e).each((n => { Fn(n).each((o => { t.isBlock(xn(e)) && _i(n) && t.isBlock(xn(o)) && Eo(n); })); })); })(e, n))); })(a, s.getStart(), e.schema), ((e, t, n) => { const o = In(mn(n), (e => Cn(e, mn(t)))); ie(o, o.length - 2).filter(Nn).fold((() => Ms(e, t)), (t => Ms(e, t.dom))); })(e.schema, e.getBody(), s.getStart()), t; }, ev = e => e instanceof up, tv = (e, t, n) => { e.dom.setHTML(e.getBody(), t), !0 !== n && (e => { vg(e) && km(e.getBody()).each((t => { const n = t.getNode(), o = Zr(n) ? km(n).getOr(t) : t; e.selection.setRng(o.toRange()); })); })(e); }, nv = pf, ov = (e, t, n) => { const o = e.formatter.get(n); if (o)
        for (let n = 0; n < o.length; n++) {
            const r = o[n];
            if (Ef(r) && !1 === r.inherit && e.dom.is(t, r.selector))
                return !0;
        } return !1; }, rv = (e, t, n, o, r) => { const s = e.dom.getRoot(); if (t === s)
        return !1; const a = e.dom.getParent(t, (t => !!ov(e, t, n) || t.parentNode === s || !!iv(e, t, n, o, !0))); return !!iv(e, a, n, o, r); }, sv = (e, t, n) => !(!xf(n) || !nv(t, n.inline)) || !(!wf(n) || !nv(t, n.block)) || !!Ef(n) && qr(t) && e.is(t, n.selector), av = (e, t, n, o, r, s) => { const a = n[o], i = "attributes" === o; if (w(n.onmatch))
        return n.onmatch(t, n, o); if (a)
        if (ct(a)) {
            for (let n = 0; n < a.length; n++)
                if (i ? e.getAttrib(t, a[n]) : bf(e, t, a[n]))
                    return !0;
        }
        else
            for (const o in a)
                if (_e(a, o)) {
                    const l = i ? e.getAttrib(t, o) : bf(e, t, o), d = gf(a[o], s), c = y(l) || tt(l);
                    if (c && y(d))
                        continue;
                    if (r && c && !n.exact)
                        return !1;
                    if ((!r || n.exact) && !nv(l, hf(d, o)))
                        return !1;
                } return !0; }, iv = (e, t, n, o, r) => { const s = e.formatter.get(n), a = e.dom; if (s && qr(t))
        for (let n = 0; n < s.length; n++) {
            const i = s[n];
            if (sv(e.dom, t, i) && av(a, t, i, "attributes", r, o) && av(a, t, i, "styles", r, o)) {
                const n = i.classes;
                if (n)
                    for (let r = 0; r < n.length; r++)
                        if (!e.dom.hasClass(t, gf(n[r], o)))
                            return;
                return i;
            }
        } }, lv = (e, t, n, o, r) => { if (o)
        return rv(e, o, t, n, r); if (o = e.selection.getNode(), rv(e, o, t, n, r))
        return !0; const s = e.selection.getStart(); return !(s === o || !rv(e, s, t, n, r)); }, dv = Pi, cv = e => { if (e) {
        const t = new Fr(e, e);
        for (let e = t.current(); e; e = t.next())
            if (es(e))
                return e;
    } return null; }, uv = e => { const t = cn("span"); return po(t, { id: Rm, "data-mce-bogus": "1", "data-mce-type": "format-caret" }), e && co(t, un(dv)), t; }, mv = (e, t, n) => { const o = e.dom, r = e.selection; if (Sf(t))
        Fh(e, !1, mn(t), n, !0);
    else {
        const e = r.getRng(), n = o.getParent(t, o.isBlock), s = e.startContainer, a = e.startOffset, i = e.endContainer, l = e.endOffset, d = (e => { const t = cv(e); return t && t.data.charAt(0) === dv && t.deleteData(0, 1), t; })(t);
        o.remove(t, !0), s === d && a > 0 && e.setStart(d, a - 1), i === d && l > 0 && e.setEnd(d, l - 1), n && o.isEmpty(n) && Bi(mn(n)), r.setRng(e);
    } }, fv = (e, t, n) => { const o = e.dom, r = e.selection; if (t)
        mv(e, t, n);
    else if (!(t = Tm(e.getBody(), r.getStart())))
        for (; t = o.get(Rm);)
            mv(e, t, n); }, gv = (e, t) => (e.appendChild(t), t), pv = (e, t) => { var n; const o = X(e, ((e, t) => gv(e, t.cloneNode(!1))), t), r = null !== (n = o.ownerDocument) && void 0 !== n ? n : document; return gv(o, r.createTextNode(dv)); }, hv = e => Zo(e, Go(e).replace(new RegExp(`${it}$`), " ")), bv = (e, t) => { const n = () => { null === t || e.dom.isEmpty(t) || Fn(mn(t)).each((e => { Rn(e) ? hv(e) : nr(e, (e => Rn(e))).each((e => { Rn(e) && hv(e); })); })); }; e.once("input", (t => { t.data && !qm(t.data) && (t.isComposing ? e.once("compositionend", (() => { n(); })) : n()); })); }, vv = (e, t, n, o) => { const a = e.dom, i = e.selection; let l = !1; const d = e.formatter.get(t); if (!d)
        return; const c = i.getRng(), u = c.startContainer, m = c.startOffset; let f = u; es(u) && (m !== u.data.length && (l = !0), f = f.parentNode); const g = []; let h; for (; f;) {
        if (iv(e, f, t, n, o)) {
            h = f;
            break;
        }
        f.nextSibling && (l = !0), g.push(f), f = f.parentNode;
    } if (h)
        if (l) {
            const r = i.getBookmark();
            c.collapse(!0);
            let s = jf(a, c, d, { includeTrailingSpace: !0 });
            s = Ig(s), e.formatter.remove(t, n, s, o), i.moveToBookmark(r);
        }
        else {
            const l = Tm(e.getBody(), h), d = C(l) ? a.getParents(h.parentNode, M, l) : [], c = uv(!1).dom;
            ((e, t, n) => { var o, r; const s = e.dom, a = s.getParent(n, T(cf, e.schema)); a && s.isEmpty(a) ? null === (o = n.parentNode) || void 0 === o || o.replaceChild(t, n) : ((e => { const t = vr(e, "br"), n = Y((e => { const t = []; let n = e.dom; for (; n;)
                t.push(mn(n)), n = n.lastChild; return t; })(e).slice(-1), _i); t.length === n.length && q(n, Eo); })(mn(n)), s.isEmpty(n) ? null === (r = n.parentNode) || void 0 === r || r.replaceChild(t, n) : s.insertAfter(t, n)); })(e, c, null != l ? l : h);
            const u = ((e, t, n, o, a, i) => { const l = e.formatter, d = e.dom, c = Y(fe(l.get()), (e => e !== o && !Ke(e, "removeformat"))), u = ((e, t, n) => G(n, ((n, o) => { const r = ((e, t) => Cf(e, t, (e => { const t = e => w(e) || e.length > 1 && "%" === e.charAt(0); return $(["styles", "attributes"], (n => xe(e, n).exists((e => { const n = p(e) ? e : Ee(e); return $(n, t); })))); })))(e, o); return e.formatter.matchNode(t, o, {}, r) ? n.concat([o]) : n; }), []))(e, n, c); if (Y(u, (t => !((e, t, n) => { const o = ["inline", "block", "selector", "attributes", "styles", "classes"], a = e => Ce(e, ((e, t) => $(o, (e => e === t)))); return Cf(e, t, (t => { const o = a(t); return Cf(e, n, (e => { const t = a(e); return ((e, t, n = s) => r(n).eq(e, t))(o, t); })); })); })(e, t, o))).length > 0) {
                const e = n.cloneNode(!1);
                return d.add(t, e), l.remove(o, a, e, i), d.remove(e), I.some(e);
            } return I.none(); })(e, c, h, t, n, o), m = pv([...g, ...u.toArray(), ...d], c);
            l && mv(e, l, C(l)), i.setCursorLocation(m, 1), bv(e, c), a.isEmpty(h) && a.remove(h);
        } }, yv = e => { const t = uv(!1), n = pv(e, t.dom); return { caretContainer: t, caretPosition: Pl(n, 0) }; }, Cv = (e, t) => { const { caretContainer: n, caretPosition: o } = yv(t); return ao(mn(e), n), Eo(mn(e)), o; }, wv = (e, t) => { if (Am(t.dom))
        return !1; const n = e.schema.getTextInlineElements(); return _e(n, xn(t)) && !Am(t.dom) && !Gr(t.dom); }, Ev = {}, xv = Yr(["pre"]);
    (e => { Ev[e] || (Ev[e] = []), Ev[e].push((e => { if (!e.selection.getRng().collapsed) {
        const t = e.selection.getSelectedBlocks(), n = Y(Y(t, xv), (e => t => { const n = t.previousSibling; return xv(n) && H(e, n); })(t));
        q(n, (e => { ((e, t) => { const n = mn(t), o = Pn(n).dom; Eo(n), mo(mn(e), [cn("br", o), cn("br", o), ...Hn(n)]); })(e.previousSibling, e); }));
    } })); })("pre");
    const _v = ["fontWeight", "fontStyle", "color", "fontSize", "fontFamily"], Sv = (e, t) => { const n = e.get(t); return p(n) ? Q(n, (e => xf(e) && "span" === e.inline && (e => f(e.styles) && $(fe(e.styles), (e => H(_v, e))))(e))) : I.none(); }, kv = (e, t) => Sm(t, Pl.fromRangeStart(e)).isNone(), Nv = (e, t) => !1 === _m(t, Pl.fromRangeEnd(e)).exists((e => !as(e.getNode()) || _m(t, e).isSome())), Rv = e => t => ps(t) && e.isEditable(t), Av = e => Y(e.getSelectedBlocks(), Rv(e.dom)), Tv = an.each, Ov = e => qr(e) && !Hm(e) && !Am(e) && !Gr(e), Bv = (e, t) => { for (let n = e; n; n = n[t]) {
        if (es(n) && et(n.data))
            return e;
        if (qr(n) && !Hm(n))
            return n;
    } return e; }, Pv = (e, t, n) => { const o = Tb(e), r = Wr(t) && e.dom.isEditable(t), s = Wr(n) && e.dom.isEditable(n); if (r && s) {
        const r = Bv(t, "previousSibling"), s = Bv(n, "nextSibling");
        if (o.compare(r, s)) {
            for (let e = r.nextSibling; e && e !== s;) {
                const t = e;
                e = e.nextSibling, r.appendChild(t);
            }
            return e.dom.remove(s), an.each(an.grep(s.childNodes), (e => { r.appendChild(e); })), r;
        }
    } return n; }, Dv = (e, t, n, o) => { var r; if (o && !1 !== t.merge_siblings) {
        const t = null !== (r = Pv(e, df(o), o)) && void 0 !== r ? r : o;
        Pv(e, t, df(t, !0));
    } }, Lv = (e, t, n) => { Tv(e.childNodes, (e => { Ov(e) && (t(e) && n(e), e.hasChildNodes() && Lv(e, t, n)); })); }, Mv = (e, t) => n => !(!n || !bf(e, n, t)), Iv = (e, t, n) => o => { e.setStyle(o, t, n), "" === o.getAttribute("style") && o.removeAttribute("style"), ((e, t) => { "SPAN" === t.nodeName && 0 === e.getAttribs(t).length && e.remove(t, !0); })(e, o); }, Fv = ke([{ keep: [] }, { rename: ["name"] }, { removed: [] }]), Uv = /^(src|href|style)$/, zv = an.each, jv = pf, Hv = (e, t, n) => e.isChildOf(t, n) && t !== n && !e.isBlock(n), $v = (e, t, n) => { let o = t[n ? "startContainer" : "endContainer"], r = t[n ? "startOffset" : "endOffset"]; if (qr(o)) {
        const e = o.childNodes.length - 1;
        !n && r && r--, o = o.childNodes[r > e ? e : r];
    } return es(o) && n && r >= o.data.length && (o = new Fr(o, e.getBody()).next() || o), es(o) && !n && 0 === r && (o = new Fr(o, e.getBody()).prev() || o), o; }, Vv = (e, t) => { const n = t ? "firstChild" : "lastChild", o = e[n]; return (e => /^(TR|TH|TD)$/.test(e.nodeName))(e) && o ? "TR" === e.nodeName && o[n] || o : e; }, qv = (e, t, n, o) => { var r; const s = e.create(n, o); return null === (r = t.parentNode) || void 0 === r || r.insertBefore(s, t), s.appendChild(t), s; }, Wv = (e, t, n, o, r) => { const s = mn(t), a = mn(e.create(o, r)), i = n ? jn(s) : zn(s); return mo(a, i), n ? (ao(s, a), lo(a, s)) : (io(s, a), co(a, s)), a.dom; }, Kv = (e, t, n) => { const o = t.parentNode; let r; const s = e.dom, a = Ad(e); wf(n) && o === s.getRoot() && (n.list_block && jv(t, n.list_block) || q(ce(t.childNodes), (t => { uf(e, a, t.nodeName.toLowerCase()) ? r ? r.appendChild(t) : (r = qv(s, t, a), s.setAttribs(r, Td(e))) : r = null; }))), (e => Ef(e) && xf(e) && Fe(xe(e, "mixed"), !0))(n) && !jv(n.inline, t) || s.remove(t, !0); }, Yv = (e, t, n) => E(e) ? { name: t, value: null } : { name: e, value: gf(t, n) }, Xv = (e, t) => { "" === e.getAttrib(t, "style") && (t.removeAttribute("style"), t.removeAttribute("data-mce-style")); }, Gv = (e, t, n, o, r) => { let s = !1; zv(n.styles, ((a, i) => { const { name: l, value: d } = Yv(i, a, o), c = hf(d, l); (n.remove_similar || h(d) || !qr(r) || jv(bf(e, r, l), c)) && e.setStyle(t, l, ""), s = !0; })), s && Xv(e, t); }, Zv = (e, t, n, o, r) => { const s = e.dom, a = Tb(e), i = e.schema; if (xf(t) && Us(i, t.inline) && js(i, o) && o.parentElement === e.getBody())
        return Kv(e, o, t), Fv.removed(); if (!t.ceFalseOverride && o && "false" === s.getContentEditableParent(o))
        return Fv.keep(); if (o && !sv(s, o, t) && !((e, t) => t.links && "A" === e.nodeName)(o, t))
        return Fv.keep(); const l = o, d = t.preserve_attributes; if (xf(t) && "all" === t.remove && p(d)) {
        const e = Y(s.getAttribs(l), (e => H(d, e.name.toLowerCase())));
        if (s.removeAllAttribs(l), q(e, (e => s.setAttrib(l, e.name, e.value))), e.length > 0)
            return Fv.rename("span");
    } if ("all" !== t.remove) {
        Gv(s, l, t, n, r), zv(t.attributes, ((e, o) => { const { name: a, value: i } = Yv(o, e, n); if (t.remove_similar || h(i) || !qr(r) || jv(s.getAttrib(r, a), i)) {
            if ("class" === a) {
                const e = s.getAttrib(l, a);
                if (e) {
                    let t = "";
                    if (q(e.split(/\s+/), (e => { /mce\-\w+/.test(e) && (t += (t ? " " : "") + e); })), t)
                        return void s.setAttrib(l, a, t);
                }
            }
            if (Uv.test(a) && l.removeAttribute("data-mce-" + a), "style" === a && Yr(["li"])(l) && "none" === s.getStyle(l, "list-style-type"))
                return l.removeAttribute(a), void s.setStyle(l, "list-style-type", "none");
            "class" === a && l.removeAttribute("className"), l.removeAttribute(a);
        } })), zv(t.classes, (e => { e = gf(e, n), qr(r) && !s.hasClass(r, e) || s.removeClass(l, e); }));
        const e = s.getAttribs(l);
        for (let t = 0; t < e.length; t++) {
            const n = e[t].nodeName;
            if (!a.isAttributeInternal(n))
                return Fv.keep();
        }
    } return "none" !== t.remove ? (Kv(e, l, t), Fv.removed()) : Fv.keep(); }, Qv = (e, t, n, o) => Zv(e, t, n, o, o).fold(N(o), (t => (e.dom.createFragment().appendChild(o), e.dom.rename(o, t))), N(null)), Jv = (e, t, n, o, r) => { (o || e.selection.isEditable()) && ((e, t, n, o, r) => { const s = e.formatter.get(t), a = s[0], i = e.dom, l = e.selection, d = o => { const i = ((e, t, n, o, r) => { let s; return t.parentNode && q(yf(e.dom, t.parentNode).reverse(), (t => { if (!s && qr(t) && "_start" !== t.id && "_end" !== t.id) {
        const a = iv(e, t, n, o, r);
        a && !1 !== a.split && (s = t);
    } })), s; })(e, o, t, n, r); return ((e, t, n, o, r, s, a, i) => { var l, d; let c, u; const m = e.dom; if (n) {
        const s = n.parentNode;
        for (let n = o.parentNode; n && n !== s; n = n.parentNode) {
            let o = m.clone(n, !1);
            for (let n = 0; n < t.length && (o = Qv(e, t[n], i, o), null !== o); n++)
                ;
            o && (c && o.appendChild(c), u || (u = o), c = o);
        }
        a.mixed && m.isBlock(n) || (o = null !== (l = m.split(n, o)) && void 0 !== l ? l : o), c && u && (null === (d = r.parentNode) || void 0 === d || d.insertBefore(c, r), u.appendChild(r), xf(a) && Dv(e, a, 0, c));
    } return o; })(e, s, i, o, o, 0, a, n); }, c = t => $(s, (o => ey(e, o, n, t, t))), u = t => { const n = ce(t.childNodes), o = c(t) || $(s, (e => sv(i, t, e))), r = t.parentNode; if (!o && C(r) && _f(a) && c(r), a.deep && n.length)
        for (let e = 0; e < n.length; e++)
            u(n[e]); q(["underline", "line-through", "overline"], (n => { qr(t) && e.dom.getStyle(t, "text-decoration") === n && t.parentNode && vf(i, t.parentNode) === n && ey(e, { deep: !1, exact: !0, inline: "span", styles: { textDecoration: n } }, void 0, t); })); }, m = e => { const t = i.get(e ? "_start" : "_end"); if (t) {
        let n = t[e ? "firstChild" : "lastChild"];
        return (e => Hm(e) && qr(e) && ("_start" === e.id || "_end" === e.id))(n) && (n = n[e ? "firstChild" : "lastChild"]), es(n) && 0 === n.data.length && (n = e ? t.previousSibling || t.nextSibling : t.nextSibling || t.previousSibling), i.remove(t, !0), n;
    } return null; }, f = t => { let n, o, r = jf(i, t, s, { includeTrailingSpace: t.collapsed }); if (a.split) {
        if (r = Ig(r), n = $v(e, r, !0), o = $v(e, r), n !== o) {
            if (n = Vv(n, !0), o = Vv(o, !1), Hv(i, n, o)) {
                const e = I.from(n.firstChild).getOr(n);
                return d(Wv(i, e, !0, "span", { id: "_start", "data-mce-type": "bookmark" })), void m(!0);
            }
            if (Hv(i, o, n)) {
                const e = I.from(o.lastChild).getOr(o);
                return d(Wv(i, e, !1, "span", { id: "_end", "data-mce-type": "bookmark" })), void m(!1);
            }
            n = qv(i, n, "span", { id: "_start", "data-mce-type": "bookmark" }), o = qv(i, o, "span", { id: "_end", "data-mce-type": "bookmark" });
            const e = i.createRng();
            e.setStartAfter(n), e.setEndBefore(o), Hf(i, e, (e => { q(e, (e => { Hm(e) || Hm(e.parentNode) || d(e); })); })), d(n), d(o), n = m(!0), o = m();
        }
        else
            n = o = d(n);
        r.startContainer = n.parentNode ? n.parentNode : n, r.startOffset = i.nodeIndex(n), r.endContainer = o.parentNode ? o.parentNode : o, r.endOffset = i.nodeIndex(o) + 1;
    } Hf(i, r, (e => { q(e, u); })); }; if (o) {
        if (rf(o)) {
            const e = i.createRng();
            e.setStartBefore(o), e.setEndAfter(o), f(e);
        }
        else
            f(o);
        cd(e, t, o, n);
    }
    else
        l.isCollapsed() && xf(a) && !Xm(e).length ? vv(e, t, n, r) : (af(e, (() => nf(e, f)), (o => xf(a) && lv(e, t, n, o))), e.nodeChanged()), ((e, t, n) => { "removeformat" === t ? q(Av(e.selection), (t => { q(_v, (n => e.dom.setStyle(t, n, ""))), Xv(e.dom, t); })) : Sv(e.formatter, t).each((t => { q(Av(e.selection), (o => Gv(e.dom, o, t, n, null))); })); })(e, t, n), cd(e, t, o, n); })(e, t, n, o, r); }, ey = (e, t, n, o, r) => Zv(e, t, n, o, r).fold(L, (t => (e.dom.rename(o, t), !0)), M), ty = an.each, ny = an.each, oy = (e, t, n, o) => { if (ny(n.styles, ((n, r) => { e.setStyle(t, r, gf(n, o)); })), n.styles) {
        const n = e.getAttrib(t, "style");
        n && e.setAttrib(t, "data-mce-style", n);
    } }, ry = (e, t, n, o) => { const r = e.formatter.get(t), s = r[0], a = !o && e.selection.isCollapsed(), i = e.dom, l = e.selection, d = (e, t = s) => { w(t.onformat) && t.onformat(e, t, n, o), oy(i, e, t, n), ny(t.attributes, ((t, o) => { i.setAttrib(e, o, gf(t, n)); })), ny(t.classes, (t => { const o = gf(t, n); i.hasClass(e, o) || i.addClass(e, o); })); }, c = (e, t) => { let n = !1; return ny(e, (e => !(!Ef(e) || ("false" !== i.getContentEditable(t) || e.ceFalseOverride) && (!C(e.collapsed) || e.collapsed === a) && i.is(t, e.selector) && !Am(t) && (d(t, e), n = !0, 1)))), n; }, u = e => { if (m(e)) {
        const t = i.create(e);
        return d(t), t;
    } return null; }, f = (o, a, i) => { const l = []; let m = !0; const f = s.inline || s.block, g = u(f); Hf(o, a, (a => { let u; const p = a => { let h = !1, b = m, v = !1; const y = a.parentNode, w = y.nodeName.toLowerCase(), E = o.getContentEditable(a); C(E) && (b = m, m = "true" === E, h = !0, v = ff(e, a)); const x = m && !h; if (as(a) && !((e, t, n, o) => { if (gc(e) && xf(t) && n.parentNode) {
        const t = ka(e.schema), r = Cr(mn(n), (e => Am(e.dom)));
        return Se(t, o) && Es(e.schema, n.parentNode, { skipBogus: !1, includeZwsp: !0 }) && !r;
    } return !1; })(e, s, a, w))
        return u = null, void (wf(s) && o.remove(a)); if ((o => (e => wf(e) && !0 === e.wrapper)(s) && iv(e, o, t, n))(a))
        u = null;
    else {
        if (((t, n, o) => { const r = (e => wf(e) && !0 !== e.wrapper)(s) && cf(e.schema, t) && uf(e, n, f); return o && r; })(a, w, x)) {
            const e = o.rename(a, f);
            return d(e), l.push(e), void (u = null);
        }
        if (Ef(s)) {
            let e = c(r, a);
            if (!e && C(y) && _f(s) && (e = c(r, y)), !xf(s) || e)
                return void (u = null);
        }
        C(g) && ((t, n, r, a) => { const l = t.nodeName.toLowerCase(), d = uf(e, f, l) && uf(e, n, f), c = !i && es(t) && Di(t.data), u = Am(t), m = !xf(s) || !o.isBlock(t); return (r || a) && d && !c && !u && m; })(a, w, x, v) ? (u || (u = o.clone(g, !1), y.insertBefore(u, a), l.push(u)), v && h && (m = b), u.appendChild(a)) : (u = null, q(ce(a.childNodes), p), h && (m = b), u = null);
    } }; q(a, p); })), !0 === s.links && q(l, (e => { const t = e => { "A" === e.nodeName && d(e, s), q(ce(e.childNodes), t); }; t(e); })), q(l, (a => { const i = (e => { let t = 0; return q(e.childNodes, (e => { (e => C(e) && es(e) && 0 === e.length)(e) || Hm(e) || t++; })), t; })(a); !(l.length > 1) && o.isBlock(a) || 0 !== i ? (xf(s) || wf(s) && s.wrapper) && (s.exact || 1 !== i || (a = (e => { const t = Q(e.childNodes, sf).filter((e => "false" !== o.getContentEditable(e) && sv(o, e, s))); return t.map((t => { const n = o.clone(t, !1); return d(n), o.replace(n, e, !0), o.remove(t, !0), n; })).getOr(e); })(a)), ((e, t, n, o) => { ty(t, (t => { xf(t) && ty(e.dom.select(t.inline, o), (o => { Ov(o) && ey(e, t, n, o, t.exact ? o : null); })), ((e, t, n) => { if (t.clear_child_styles) {
        const o = t.links ? "*:not(a)" : "*";
        Tv(e.select(o, n), (n => { Ov(n) && e.isEditable(n) && Tv(t.styles, ((t, o) => { e.setStyle(n, o, ""); })); }));
    } })(e.dom, t, o); })); })(e, r, n, a), ((e, t, n, o, r) => { const s = r.parentNode; iv(e, s, n, o) && ey(e, t, o, r) || t.merge_with_parents && s && e.dom.getParent(s, (s => !!iv(e, s, n, o) && (ey(e, t, o, r), !0))); })(e, s, t, n, a), ((e, t, n, o) => { if (t.styles && t.styles.backgroundColor) {
        const r = Mv(e, "fontSize");
        Lv(o, (t => r(t) && e.isEditable(t)), Iv(e, "backgroundColor", gf(t.styles.backgroundColor, n)));
    } })(o, s, n, a), ((e, t, n, o) => { const r = t => { if (Wr(t) && qr(t.parentNode) && e.isEditable(t)) {
        const n = vf(e, t.parentNode);
        e.getStyle(t, "color") && n ? e.setStyle(t, "text-decoration", n) : e.getStyle(t, "text-decoration") === n && e.setStyle(t, "text-decoration", null);
    } }; t.styles && (t.styles.color || t.styles.textDecoration) && (an.walk(o, r, "childNodes"), r(o)); })(o, s, 0, a), ((e, t, n, o) => { if (xf(t) && ("sub" === t.inline || "sup" === t.inline)) {
        const n = Mv(e, "fontSize");
        Lv(o, (t => n(t) && e.isEditable(t)), Iv(e, "fontSize", ""));
        const r = Y(e.select("sup" === t.inline ? "sub" : "sup", o), e.isEditable);
        e.remove(r, !0);
    } })(o, s, 0, a), Dv(e, s, 0, a)) : o.remove(a, !0); })); }, g = rf(o) ? o : l.getNode(); if ("false" === i.getContentEditable(g) && !ff(e, g))
        return c(r, o = g), void dd(e, t, o, n); if (s) {
        if (o)
            if (rf(o)) {
                if (!c(r, o)) {
                    const e = i.createRng();
                    e.setStartBefore(o), e.setEndAfter(o), f(i, jf(i, e, r), !0);
                }
            }
            else
                f(i, o, !0);
        else
            a && xf(s) && !Xm(e).length ? ((e, t, n) => { let o; const r = e.selection, s = e.formatter.get(t); if (!s)
                return; const a = r.getRng(); let i = a.startOffset; const l = a.startContainer.nodeValue; o = Tm(e.getBody(), r.getStart()); const d = /[^\s\u00a0\u00ad\u200b\ufeff]/; if (l && i > 0 && i < l.length && d.test(l.charAt(i)) && d.test(l.charAt(i - 1))) {
                const o = r.getBookmark();
                a.collapse(!0);
                let i = jf(e.dom, a, s);
                i = Ig(i), e.formatter.apply(t, n, i), r.moveToBookmark(o);
            }
            else {
                let s = o ? cv(o) : null;
                o && (null == s ? void 0 : s.data) === dv || (c = e.getDoc(), u = uv(!0).dom, o = c.importNode(u, !0), s = o.firstChild, a.insertNode(o), i = 1, bv(e, o)), e.formatter.apply(t, n, o), r.setCursorLocation(s, i);
            } var c, u; })(e, t, n) : (l.setRng(Hb(l.getRng())), af(e, (() => { nf(e, ((e, t) => { const n = t ? e : jf(i, e, r); f(i, n, !1); })); }), M), e.nodeChanged()), Sv(e.formatter, t).each((t => { q((e => Y((e => { const t = e.getSelectedBlocks(), n = e.getRng(); if (e.isCollapsed())
                return []; if (1 === t.length)
                return kv(n, t[0]) && Nv(n, t[0]) ? t : []; {
                const e = le(t).filter((e => kv(n, e))).toArray(), o = de(t).filter((e => Nv(n, e))).toArray(), r = t.slice(1, -1);
                return e.concat(r).concat(o);
            } })(e), Rv(e.dom)))(e.selection), (e => oy(i, e, t, n))); }));
        ((e, t) => { _e(Ev, e) && q(Ev[e], (e => { e(t); })); })(t, e);
    } dd(e, t, o, n); }, sy = (e, t, n, o) => { (o || e.selection.isEditable()) && ry(e, t, n, o); }, ay = e => _e(e, "vars"), iy = e => e.selection.getStart(), ly = (e, t, n, o, r) => Z(t, (t => { const s = e.formatter.matchNode(t, n, null != r ? r : {}, o); return !v(s); }), (t => !!ov(e, t, n) || !o && C(e.formatter.matchNode(t, n, r, !0)))), dy = (e, t) => { const n = null != t ? t : iy(e); return Y(yf(e.dom, n), (e => qr(e) && !Gr(e))); }, cy = (e, t, n) => { const o = dy(e, t); pe(n, ((n, r) => { const s = n => { const s = ly(e, o, r, n.similar, ay(n) ? n.vars : void 0), a = s.isSome(); if (n.state.get() !== a) {
        n.state.set(a);
        const e = s.getOr(t);
        ay(n) ? n.callback(a, { node: e, format: r, parents: o }) : q(n.callbacks, (t => t(a, { node: e, format: r, parents: o })));
    } }; q([n.withSimilar, n.withoutSimilar], s), q(n.withVars, s); })); }, uy = an.explode, my = () => { const e = {}; return { addFilter: (t, n) => { q(uy(t), (t => { _e(e, t) || (e[t] = { name: t, callbacks: [] }), e[t].callbacks.push(n); })); }, getFilters: () => Ee(e), removeFilter: (t, n) => { q(uy(t), (t => { if (_e(e, t))
            if (C(n)) {
                const o = e[t], r = Y(o.callbacks, (e => e !== n));
                r.length > 0 ? o.callbacks = r : delete e[t];
            }
            else
                delete e[t]; })); } }; }, fy = (e, t, n) => { var o; const r = Ia(); t.convert_fonts_to_spans && ((e, t, n) => { e.addNodeFilter("font", (e => { q(e, (e => { const o = t.parse(e.attr("style")), r = e.attr("color"), s = e.attr("face"), a = e.attr("size"); r && (o.color = r), s && (o["font-family"] = s), a && nt(a).each((e => { o["font-size"] = n[e - 1]; })), e.name = "span", e.attr("style", t.serialize(o)), (e => { q(["color", "face", "size"], (t => { e.attr(t, null); })); })(e); })); })); })(e, r, an.explode(null !== (o = t.font_size_legacy_values) && void 0 !== o ? o : "")), ((e, t, n) => { e.addNodeFilter("strike", (e => { const o = "html4" !== t.type; q(e, (e => { if (o)
        e.name = "s";
    else {
        const t = n.parse(e.attr("style"));
        t["text-decoration"] = "line-through", e.name = "span", e.attr("style", n.serialize(t));
    } })); })); })(e, n, r); }, gy = e => { const [t, ...n] = e.split(","), o = n.join(","), r = /data:([^/]+\/[^;]+)(;.+)?/.exec(t); if (r) {
        const e = ";base64" === r[2], t = (e => { try {
            return decodeURIComponent(e);
        }
        catch (t) {
            return e;
        } })(o), n = e ? (e => { const t = /([a-z0-9+\/=\s]+)/i.exec(e); return t ? t[1] : ""; })(t) : t;
        return I.some({ type: r[1], data: n, base64Encoded: e });
    } return I.none(); }, py = (e, t, n = !0) => { let o = t; if (n)
        try {
            o = atob(t);
        }
        catch (e) {
            return I.none();
        } const r = new Uint8Array(o.length); for (let e = 0; e < r.length; e++)
        r[e] = o.charCodeAt(e); return I.some(new Blob([r], { type: e })); }, hy = e => new Promise(((t, n) => { const o = new FileReader; o.onloadend = () => { t(o.result); }, o.onerror = () => { var e; n(null === (e = o.error) || void 0 === e ? void 0 : e.message); }, o.readAsDataURL(e); }));
    let by = 0;
    const vy = (e, t, n) => gy(e).bind((({ data: e, type: o, base64Encoded: r }) => { if (t && !r)
        return I.none(); {
        const t = r ? e : btoa(e);
        return n(t, o);
    } })), yy = (e, t, n) => { const o = e.create("blobid" + by++, t, n); return e.add(o), o; }, Cy = (e, t, n = !1) => vy(t, n, ((t, n) => I.from(e.getByData(t, n)).orThunk((() => py(n, t).map((n => yy(e, n, t))))))), wy = /^(?:(?:(?:[A-Za-z][A-Za-z\d.+-]{0,14}:\/\/(?:[-.~*+=!&;:'%@?^${}(),\w]+@)?|www\.|[-;:&=+$,.\w]+@)([A-Za-z\d-]+(?:\.[A-Za-z\d-]+)*))(?::\d+)?(?:\/(?:[-.~*+=!;:'%@$(),\/\w]*[-~*+=%@$()\/\w])?)?(?:\?(?:[-.~*+=!&;:'%@?^${}(),\/\w]+)?)?(?:#(?:[-.~*+=!&;:'%@?^${}(),\/\w]+)?)?)$/, Ey = e => I.from(e.match(wy)).bind((e => ie(e, 1))).map((e => Ye(e, "www.") ? e.substring(4) : e)), xy = (e, t) => { I.from(e.attr("src")).bind(Ey).forall((e => !H(t, e))) && e.attr("sandbox", ""); }, _y = (e, t) => Ye(e, `${t}/`), { entries: Sy, setPrototypeOf: ky, isFrozen: Ny, getPrototypeOf: Ry, getOwnPropertyDescriptor: Ay } = Object;
    let { freeze: Ty, seal: Oy, create: By } = Object, { apply: Py, construct: Dy } = "undefined" != typeof Reflect && Reflect;
    Ty || (Ty = function (e) { return e; }), Oy || (Oy = function (e) { return e; }), Py || (Py = function (e, t, n) { return e.apply(t, n); }), Dy || (Dy = function (e, t) { return new e(...t); });
    const Ly = Gy(Array.prototype.forEach), My = Gy(Array.prototype.lastIndexOf), Iy = Gy(Array.prototype.pop), Fy = Gy(Array.prototype.push), Uy = Gy(Array.prototype.splice), zy = Gy(String.prototype.toLowerCase), jy = Gy(String.prototype.toString), Hy = Gy(String.prototype.match), $y = Gy(String.prototype.replace), Vy = Gy(String.prototype.indexOf), qy = Gy(String.prototype.trim), Wy = Gy(Object.prototype.hasOwnProperty), Ky = Gy(RegExp.prototype.test), Yy = (Xy = TypeError, function () { for (var e = arguments.length, t = new Array(e), n = 0; n < e; n++)
        t[n] = arguments[n]; return Dy(Xy, t); });
    var Xy;
    function Gy(e) { return function (t) { for (var n = arguments.length, o = new Array(n > 1 ? n - 1 : 0), r = 1; r < n; r++)
        o[r - 1] = arguments[r]; return Py(e, t, o); }; }
    function Zy(e, t) { let n = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : zy; ky && ky(e, null); let o = t.length; for (; o--;) {
        let r = t[o];
        if ("string" == typeof r) {
            const e = n(r);
            e !== r && (Ny(t) || (t[o] = e), r = e);
        }
        e[r] = !0;
    } return e; }
    function Qy(e) { for (let t = 0; t < e.length; t++)
        Wy(e, t) || (e[t] = null); return e; }
    function Jy(e) { const t = By(null); for (const [n, o] of Sy(e))
        Wy(e, n) && (Array.isArray(o) ? t[n] = Qy(o) : o && "object" == typeof o && o.constructor === Object ? t[n] = Jy(o) : t[n] = o); return t; }
    function eC(e, t) { for (; null !== e;) {
        const n = Ay(e, t);
        if (n) {
            if (n.get)
                return Gy(n.get);
            if ("function" == typeof n.value)
                return Gy(n.value);
        }
        e = Ry(e);
    } return function () { return null; }; }
    const tC = Ty(["a", "abbr", "acronym", "address", "area", "article", "aside", "audio", "b", "bdi", "bdo", "big", "blink", "blockquote", "body", "br", "button", "canvas", "caption", "center", "cite", "code", "col", "colgroup", "content", "data", "datalist", "dd", "decorator", "del", "details", "dfn", "dialog", "dir", "div", "dl", "dt", "element", "em", "fieldset", "figcaption", "figure", "font", "footer", "form", "h1", "h2", "h3", "h4", "h5", "h6", "head", "header", "hgroup", "hr", "html", "i", "img", "input", "ins", "kbd", "label", "legend", "li", "main", "map", "mark", "marquee", "menu", "menuitem", "meter", "nav", "nobr", "ol", "optgroup", "option", "output", "p", "picture", "pre", "progress", "q", "rp", "rt", "ruby", "s", "samp", "section", "select", "shadow", "small", "source", "spacer", "span", "strike", "strong", "style", "sub", "summary", "sup", "table", "tbody", "td", "template", "textarea", "tfoot", "th", "thead", "time", "tr", "track", "tt", "u", "ul", "var", "video", "wbr"]), nC = Ty(["svg", "a", "altglyph", "altglyphdef", "altglyphitem", "animatecolor", "animatemotion", "animatetransform", "circle", "clippath", "defs", "desc", "ellipse", "filter", "font", "g", "glyph", "glyphref", "hkern", "image", "line", "lineargradient", "marker", "mask", "metadata", "mpath", "path", "pattern", "polygon", "polyline", "radialgradient", "rect", "stop", "style", "switch", "symbol", "text", "textpath", "title", "tref", "tspan", "view", "vkern"]), oC = Ty(["feBlend", "feColorMatrix", "feComponentTransfer", "feComposite", "feConvolveMatrix", "feDiffuseLighting", "feDisplacementMap", "feDistantLight", "feDropShadow", "feFlood", "feFuncA", "feFuncB", "feFuncG", "feFuncR", "feGaussianBlur", "feImage", "feMerge", "feMergeNode", "feMorphology", "feOffset", "fePointLight", "feSpecularLighting", "feSpotLight", "feTile", "feTurbulence"]), rC = Ty(["animate", "color-profile", "cursor", "discard", "font-face", "font-face-format", "font-face-name", "font-face-src", "font-face-uri", "foreignobject", "hatch", "hatchpath", "mesh", "meshgradient", "meshpatch", "meshrow", "missing-glyph", "script", "set", "solidcolor", "unknown", "use"]), sC = Ty(["math", "menclose", "merror", "mfenced", "mfrac", "mglyph", "mi", "mlabeledtr", "mmultiscripts", "mn", "mo", "mover", "mpadded", "mphantom", "mroot", "mrow", "ms", "mspace", "msqrt", "mstyle", "msub", "msup", "msubsup", "mtable", "mtd", "mtext", "mtr", "munder", "munderover", "mprescripts"]), aC = Ty(["maction", "maligngroup", "malignmark", "mlongdiv", "mscarries", "mscarry", "msgroup", "mstack", "msline", "msrow", "semantics", "annotation", "annotation-xml", "mprescripts", "none"]), iC = Ty(["#text"]), lC = Ty(["accept", "action", "align", "alt", "autocapitalize", "autocomplete", "autopictureinpicture", "autoplay", "background", "bgcolor", "border", "capture", "cellpadding", "cellspacing", "checked", "cite", "class", "clear", "color", "cols", "colspan", "controls", "controlslist", "coords", "crossorigin", "datetime", "decoding", "default", "dir", "disabled", "disablepictureinpicture", "disableremoteplayback", "download", "draggable", "enctype", "enterkeyhint", "face", "for", "headers", "height", "hidden", "high", "href", "hreflang", "id", "inputmode", "integrity", "ismap", "kind", "label", "lang", "list", "loading", "loop", "low", "max", "maxlength", "media", "method", "min", "minlength", "multiple", "muted", "name", "nonce", "noshade", "novalidate", "nowrap", "open", "optimum", "pattern", "placeholder", "playsinline", "popover", "popovertarget", "popovertargetaction", "poster", "preload", "pubdate", "radiogroup", "readonly", "rel", "required", "rev", "reversed", "role", "rows", "rowspan", "spellcheck", "scope", "selected", "shape", "size", "sizes", "span", "srclang", "start", "src", "srcset", "step", "style", "summary", "tabindex", "title", "translate", "type", "usemap", "valign", "value", "width", "wrap", "xmlns", "slot"]), dC = Ty(["accent-height", "accumulate", "additive", "alignment-baseline", "amplitude", "ascent", "attributename", "attributetype", "azimuth", "basefrequency", "baseline-shift", "begin", "bias", "by", "class", "clip", "clippathunits", "clip-path", "clip-rule", "color", "color-interpolation", "color-interpolation-filters", "color-profile", "color-rendering", "cx", "cy", "d", "dx", "dy", "diffuseconstant", "direction", "display", "divisor", "dur", "edgemode", "elevation", "end", "exponent", "fill", "fill-opacity", "fill-rule", "filter", "filterunits", "flood-color", "flood-opacity", "font-family", "font-size", "font-size-adjust", "font-stretch", "font-style", "font-variant", "font-weight", "fx", "fy", "g1", "g2", "glyph-name", "glyphref", "gradientunits", "gradienttransform", "height", "href", "id", "image-rendering", "in", "in2", "intercept", "k", "k1", "k2", "k3", "k4", "kerning", "keypoints", "keysplines", "keytimes", "lang", "lengthadjust", "letter-spacing", "kernelmatrix", "kernelunitlength", "lighting-color", "local", "marker-end", "marker-mid", "marker-start", "markerheight", "markerunits", "markerwidth", "maskcontentunits", "maskunits", "max", "mask", "media", "method", "mode", "min", "name", "numoctaves", "offset", "operator", "opacity", "order", "orient", "orientation", "origin", "overflow", "paint-order", "path", "pathlength", "patterncontentunits", "patterntransform", "patternunits", "points", "preservealpha", "preserveaspectratio", "primitiveunits", "r", "rx", "ry", "radius", "refx", "refy", "repeatcount", "repeatdur", "restart", "result", "rotate", "scale", "seed", "shape-rendering", "slope", "specularconstant", "specularexponent", "spreadmethod", "startoffset", "stddeviation", "stitchtiles", "stop-color", "stop-opacity", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke", "stroke-width", "style", "surfacescale", "systemlanguage", "tabindex", "tablevalues", "targetx", "targety", "transform", "transform-origin", "text-anchor", "text-decoration", "text-rendering", "textlength", "type", "u1", "u2", "unicode", "values", "viewbox", "visibility", "version", "vert-adv-y", "vert-origin-x", "vert-origin-y", "width", "word-spacing", "wrap", "writing-mode", "xchannelselector", "ychannelselector", "x", "x1", "x2", "xmlns", "y", "y1", "y2", "z", "zoomandpan"]), cC = Ty(["accent", "accentunder", "align", "bevelled", "close", "columnsalign", "columnlines", "columnspan", "denomalign", "depth", "dir", "display", "displaystyle", "encoding", "fence", "frame", "height", "href", "id", "largeop", "length", "linethickness", "lspace", "lquote", "mathbackground", "mathcolor", "mathsize", "mathvariant", "maxsize", "minsize", "movablelimits", "notation", "numalign", "open", "rowalign", "rowlines", "rowspacing", "rowspan", "rspace", "rquote", "scriptlevel", "scriptminsize", "scriptsizemultiplier", "selection", "separator", "separators", "stretchy", "subscriptshift", "supscriptshift", "symmetric", "voffset", "width", "xmlns"]), uC = Ty(["xlink:href", "xml:id", "xlink:title", "xml:space", "xmlns:xlink"]), mC = Oy(/\{\{[\w\W]*|[\w\W]*\}\}/gm), fC = Oy(/<%[\w\W]*|[\w\W]*%>/gm), gC = Oy(/\$\{[\w\W]*/gm), pC = Oy(/^data-[\-\w.\u00B7-\uFFFF]+$/), hC = Oy(/^aria-[\-\w]+$/), bC = Oy(/^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i), vC = Oy(/^(?:\w+script|data):/i), yC = Oy(/[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205F\u3000]/g), CC = Oy(/^html$/i), wC = Oy(/^[a-z][.\w]*(-[.\w]+)+$/i);
    var EC = Object.freeze({ __proto__: null, ARIA_ATTR: hC, ATTR_WHITESPACE: yC, CUSTOM_ELEMENT: wC, DATA_ATTR: pC, DOCTYPE_NAME: CC, ERB_EXPR: fC, IS_ALLOWED_URI: bC, IS_SCRIPT_OR_DATA: vC, MUSTACHE_EXPR: mC, TMPLIT_EXPR: gC });
    const xC = function () { return "undefined" == typeof window ? null : window; };
    var _C = function e() { let t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : xC(); const n = t => e(t); if (n.version = "3.2.4", n.removed = [], !t || !t.document || 9 !== t.document.nodeType || !t.Element)
        return n.isSupported = !1, n; let { document: o } = t; const r = o, s = r.currentScript, { DocumentFragment: a, HTMLTemplateElement: i, Node: l, Element: d, NodeFilter: c, NamedNodeMap: u = t.NamedNodeMap || t.MozNamedAttrMap, HTMLFormElement: m, DOMParser: f, trustedTypes: g } = t, p = d.prototype, h = eC(p, "cloneNode"), b = eC(p, "remove"), v = eC(p, "nextSibling"), y = eC(p, "childNodes"), C = eC(p, "parentNode"); if ("function" == typeof i) {
        const e = o.createElement("template");
        e.content && e.content.ownerDocument && (o = e.content.ownerDocument);
    } let w, E = ""; const { implementation: x, createNodeIterator: _, createDocumentFragment: S, getElementsByTagName: k } = o, { importNode: N } = r; let R = { afterSanitizeAttributes: [], afterSanitizeElements: [], afterSanitizeShadowDOM: [], beforeSanitizeAttributes: [], beforeSanitizeElements: [], beforeSanitizeShadowDOM: [], uponSanitizeAttribute: [], uponSanitizeElement: [], uponSanitizeShadowNode: [] }; n.isSupported = "function" == typeof Sy && "function" == typeof C && x && void 0 !== x.createHTMLDocument; const { MUSTACHE_EXPR: A, ERB_EXPR: T, TMPLIT_EXPR: O, DATA_ATTR: B, ARIA_ATTR: P, IS_SCRIPT_OR_DATA: D, ATTR_WHITESPACE: L, CUSTOM_ELEMENT: M } = EC; let { IS_ALLOWED_URI: I } = EC, F = null; const U = Zy({}, [...tC, ...nC, ...oC, ...sC, ...iC]); let z = null; const j = Zy({}, [...lC, ...dC, ...cC, ...uC]); let H = Object.seal(By(null, { tagNameCheck: { writable: !0, configurable: !1, enumerable: !0, value: null }, attributeNameCheck: { writable: !0, configurable: !1, enumerable: !0, value: null }, allowCustomizedBuiltInElements: { writable: !0, configurable: !1, enumerable: !0, value: !1 } })), $ = null, V = null, q = !0, W = !0, K = !1, Y = !0, X = !1, G = !0, Z = !1, Q = !1, J = !1, ee = !1, te = !1, ne = !1, oe = !0, re = !1, se = !0, ae = !1, ie = {}, le = null; const de = Zy({}, ["annotation-xml", "audio", "colgroup", "desc", "foreignobject", "head", "iframe", "math", "mi", "mn", "mo", "ms", "mtext", "noembed", "noframes", "noscript", "plaintext", "script", "style", "svg", "template", "thead", "title", "video", "xmp"]); let ce = null; const ue = Zy({}, ["audio", "video", "img", "source", "image", "track"]); let me = null; const fe = Zy({}, ["alt", "class", "for", "id", "label", "name", "pattern", "placeholder", "role", "summary", "title", "value", "style", "xmlns"]), ge = "http://www.w3.org/1998/Math/MathML", pe = "http://www.w3.org/2000/svg", he = "http://www.w3.org/1999/xhtml"; let be = he, ve = !1, ye = null; const Ce = Zy({}, [ge, pe, he], jy); let we = Zy({}, ["mi", "mo", "mn", "ms", "mtext"]), Ee = Zy({}, ["annotation-xml"]); const xe = Zy({}, ["title", "style", "font", "a", "script"]); let _e = null; const Se = ["application/xhtml+xml", "text/html"]; let ke = null, Ne = null; const Re = o.createElement("form"), Ae = function (e) { return e instanceof RegExp || e instanceof Function; }, Te = function () { let e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {}; if (!Ne || Ne !== e) {
        if (e && "object" == typeof e || (e = {}), e = Jy(e), _e = -1 === Se.indexOf(e.PARSER_MEDIA_TYPE) ? "text/html" : e.PARSER_MEDIA_TYPE, ke = "application/xhtml+xml" === _e ? jy : zy, F = Wy(e, "ALLOWED_TAGS") ? Zy({}, e.ALLOWED_TAGS, ke) : U, z = Wy(e, "ALLOWED_ATTR") ? Zy({}, e.ALLOWED_ATTR, ke) : j, ye = Wy(e, "ALLOWED_NAMESPACES") ? Zy({}, e.ALLOWED_NAMESPACES, jy) : Ce, me = Wy(e, "ADD_URI_SAFE_ATTR") ? Zy(Jy(fe), e.ADD_URI_SAFE_ATTR, ke) : fe, ce = Wy(e, "ADD_DATA_URI_TAGS") ? Zy(Jy(ue), e.ADD_DATA_URI_TAGS, ke) : ue, le = Wy(e, "FORBID_CONTENTS") ? Zy({}, e.FORBID_CONTENTS, ke) : de, $ = Wy(e, "FORBID_TAGS") ? Zy({}, e.FORBID_TAGS, ke) : {}, V = Wy(e, "FORBID_ATTR") ? Zy({}, e.FORBID_ATTR, ke) : {}, ie = !!Wy(e, "USE_PROFILES") && e.USE_PROFILES, q = !1 !== e.ALLOW_ARIA_ATTR, W = !1 !== e.ALLOW_DATA_ATTR, K = e.ALLOW_UNKNOWN_PROTOCOLS || !1, Y = !1 !== e.ALLOW_SELF_CLOSE_IN_ATTR, X = e.SAFE_FOR_TEMPLATES || !1, G = !1 !== e.SAFE_FOR_XML, Z = e.WHOLE_DOCUMENT || !1, ee = e.RETURN_DOM || !1, te = e.RETURN_DOM_FRAGMENT || !1, ne = e.RETURN_TRUSTED_TYPE || !1, J = e.FORCE_BODY || !1, oe = !1 !== e.SANITIZE_DOM, re = e.SANITIZE_NAMED_PROPS || !1, se = !1 !== e.KEEP_CONTENT, ae = e.IN_PLACE || !1, I = e.ALLOWED_URI_REGEXP || bC, be = e.NAMESPACE || he, we = e.MATHML_TEXT_INTEGRATION_POINTS || we, Ee = e.HTML_INTEGRATION_POINTS || Ee, H = e.CUSTOM_ELEMENT_HANDLING || {}, e.CUSTOM_ELEMENT_HANDLING && Ae(e.CUSTOM_ELEMENT_HANDLING.tagNameCheck) && (H.tagNameCheck = e.CUSTOM_ELEMENT_HANDLING.tagNameCheck), e.CUSTOM_ELEMENT_HANDLING && Ae(e.CUSTOM_ELEMENT_HANDLING.attributeNameCheck) && (H.attributeNameCheck = e.CUSTOM_ELEMENT_HANDLING.attributeNameCheck), e.CUSTOM_ELEMENT_HANDLING && "boolean" == typeof e.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements && (H.allowCustomizedBuiltInElements = e.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements), X && (W = !1), te && (ee = !0), ie && (F = Zy({}, iC), z = [], !0 === ie.html && (Zy(F, tC), Zy(z, lC)), !0 === ie.svg && (Zy(F, nC), Zy(z, dC), Zy(z, uC)), !0 === ie.svgFilters && (Zy(F, oC), Zy(z, dC), Zy(z, uC)), !0 === ie.mathMl && (Zy(F, sC), Zy(z, cC), Zy(z, uC))), e.ADD_TAGS && (F === U && (F = Jy(F)), Zy(F, e.ADD_TAGS, ke)), e.ADD_ATTR && (z === j && (z = Jy(z)), Zy(z, e.ADD_ATTR, ke)), e.ADD_URI_SAFE_ATTR && Zy(me, e.ADD_URI_SAFE_ATTR, ke), e.FORBID_CONTENTS && (le === de && (le = Jy(le)), Zy(le, e.FORBID_CONTENTS, ke)), se && (F["#text"] = !0), Z && Zy(F, ["html", "head", "body"]), F.table && (Zy(F, ["tbody"]), delete $.tbody), e.TRUSTED_TYPES_POLICY) {
            if ("function" != typeof e.TRUSTED_TYPES_POLICY.createHTML)
                throw Yy('TRUSTED_TYPES_POLICY configuration option must provide a "createHTML" hook.');
            if ("function" != typeof e.TRUSTED_TYPES_POLICY.createScriptURL)
                throw Yy('TRUSTED_TYPES_POLICY configuration option must provide a "createScriptURL" hook.');
            w = e.TRUSTED_TYPES_POLICY, E = w.createHTML("");
        }
        else
            void 0 === w && (w = function (e, t) { if ("object" != typeof e || "function" != typeof e.createPolicy)
                return null; let n = null; const o = "data-tt-policy-suffix"; t && t.hasAttribute(o) && (n = t.getAttribute(o)); const r = "dompurify" + (n ? "#" + n : ""); try {
                return e.createPolicy(r, { createHTML: e => e, createScriptURL: e => e });
            }
            catch (e) {
                return console.warn("TrustedTypes policy " + r + " could not be created."), null;
            } }(g, s)), null !== w && "string" == typeof E && (E = w.createHTML(""));
        Ty && Ty(e), Ne = e;
    } }, Oe = Zy({}, [...nC, ...oC, ...rC]), Be = Zy({}, [...sC, ...aC]), Pe = function (e) { Fy(n.removed, { element: e }); try {
        C(e).removeChild(e);
    }
    catch (t) {
        b(e);
    } }, De = function (e, t) { try {
        Fy(n.removed, { attribute: t.getAttributeNode(e), from: t });
    }
    catch (e) {
        Fy(n.removed, { attribute: null, from: t });
    } if (t.removeAttribute(e), "is" === e)
        if (ee || te)
            try {
                Pe(t);
            }
            catch (e) { }
        else
            try {
                t.setAttribute(e, "");
            }
            catch (e) { } }, Le = function (e) { let t = null, n = null; if (J)
        e = "<remove></remove>" + e;
    else {
        const t = Hy(e, /^[\r\n\t ]+/);
        n = t && t[0];
    } "application/xhtml+xml" === _e && be === he && (e = '<html xmlns="http://www.w3.org/1999/xhtml"><head></head><body>' + e + "</body></html>"); const r = w ? w.createHTML(e) : e; if (be === he)
        try {
            t = (new f).parseFromString(r, _e);
        }
        catch (e) { } if (!t || !t.documentElement) {
        t = x.createDocument(be, "template", null);
        try {
            t.documentElement.innerHTML = ve ? E : r;
        }
        catch (e) { }
    } const s = t.body || t.documentElement; return e && n && s.insertBefore(o.createTextNode(n), s.childNodes[0] || null), be === he ? k.call(t, Z ? "html" : "body")[0] : Z ? t.documentElement : s; }, Me = function (e) { return _.call(e.ownerDocument || e, e, c.SHOW_ELEMENT | c.SHOW_COMMENT | c.SHOW_TEXT | c.SHOW_PROCESSING_INSTRUCTION | c.SHOW_CDATA_SECTION, null); }, Ie = function (e) { return e instanceof m && ("string" != typeof e.nodeName || "string" != typeof e.textContent || "function" != typeof e.removeChild || !(e.attributes instanceof u) || "function" != typeof e.removeAttribute || "function" != typeof e.setAttribute || "string" != typeof e.namespaceURI || "function" != typeof e.insertBefore || "function" != typeof e.hasChildNodes); }, Fe = function (e) { return "function" == typeof l && e instanceof l; }; function Ue(e, t, o) { Ly(e, (e => { e.call(n, t, o, Ne); })); } const ze = function (e) { let t = null; if (Ue(R.beforeSanitizeElements, e, null), Ie(e))
        return Pe(e), !0; const o = ke(e.nodeName); if (Ue(R.uponSanitizeElement, e, { tagName: o, allowedTags: F }), e.hasChildNodes() && !Fe(e.firstElementChild) && Ky(/<[/\w]/g, e.innerHTML) && Ky(/<[/\w]/g, e.textContent))
        return Pe(e), !0; if (7 === e.nodeType)
        return Pe(e), !0; if (G && 8 === e.nodeType && Ky(/<[/\w]/g, e.data))
        return Pe(e), !0; if (!F[o] || $[o]) {
        if (!$[o] && He(o)) {
            if (H.tagNameCheck instanceof RegExp && Ky(H.tagNameCheck, o))
                return !1;
            if (H.tagNameCheck instanceof Function && H.tagNameCheck(o))
                return !1;
        }
        if (se && !le[o]) {
            const t = C(e) || e.parentNode, n = y(e) || e.childNodes;
            if (n && t)
                for (let o = n.length - 1; o >= 0; --o) {
                    const r = h(n[o], !0);
                    r.__removalCount = (e.__removalCount || 0) + 1, t.insertBefore(r, v(e));
                }
        }
        return Pe(e), !0;
    } return e instanceof d && !function (e) { let t = C(e); t && t.tagName || (t = { namespaceURI: be, tagName: "template" }); const n = zy(e.tagName), o = zy(t.tagName); return !!ye[e.namespaceURI] && (e.namespaceURI === pe ? t.namespaceURI === he ? "svg" === n : t.namespaceURI === ge ? "svg" === n && ("annotation-xml" === o || we[o]) : Boolean(Oe[n]) : e.namespaceURI === ge ? t.namespaceURI === he ? "math" === n : t.namespaceURI === pe ? "math" === n && Ee[o] : Boolean(Be[n]) : e.namespaceURI === he ? !(t.namespaceURI === pe && !Ee[o]) && !(t.namespaceURI === ge && !we[o]) && !Be[n] && (xe[n] || !Oe[n]) : !("application/xhtml+xml" !== _e || !ye[e.namespaceURI])); }(e) ? (Pe(e), !0) : "noscript" !== o && "noembed" !== o && "noframes" !== o || !Ky(/<\/no(script|embed|frames)/i, e.innerHTML) ? (X && 3 === e.nodeType && (t = e.textContent, Ly([A, T, O], (e => { t = $y(t, e, " "); })), e.textContent !== t && (Fy(n.removed, { element: e.cloneNode() }), e.textContent = t)), Ue(R.afterSanitizeElements, e, null), !1) : (Pe(e), !0); }, je = function (e, t, n) { if (oe && ("id" === t || "name" === t) && (n in o || n in Re))
        return !1; if (W && !V[t] && Ky(B, t))
        ;
    else if (q && Ky(P, t))
        ;
    else if (!z[t] || V[t]) {
        if (!(He(e) && (H.tagNameCheck instanceof RegExp && Ky(H.tagNameCheck, e) || H.tagNameCheck instanceof Function && H.tagNameCheck(e)) && (H.attributeNameCheck instanceof RegExp && Ky(H.attributeNameCheck, t) || H.attributeNameCheck instanceof Function && H.attributeNameCheck(t)) || "is" === t && H.allowCustomizedBuiltInElements && (H.tagNameCheck instanceof RegExp && Ky(H.tagNameCheck, n) || H.tagNameCheck instanceof Function && H.tagNameCheck(n))))
            return !1;
    }
    else if (me[t])
        ;
    else if (Ky(I, $y(n, L, "")))
        ;
    else if ("src" !== t && "xlink:href" !== t && "href" !== t || "script" === e || 0 !== Vy(n, "data:") || !ce[e])
        if (K && !Ky(D, $y(n, L, "")))
            ;
        else if (n)
            return !1; return !0; }, He = function (e) { return "annotation-xml" !== e && Hy(e, M); }, $e = function (e) { Ue(R.beforeSanitizeAttributes, e, null); const { attributes: t } = e; if (!t || Ie(e))
        return; const o = { attrName: "", attrValue: "", keepAttr: !0, allowedAttributes: z, forceKeepAttr: void 0 }; let r = t.length; for (; r--;) {
        const s = t[r], { name: a, namespaceURI: i, value: l } = s, d = ke(a);
        let c = "value" === a ? l : qy(l);
        const u = c;
        if (o.attrName = d, o.attrValue = c, o.keepAttr = !0, o.forceKeepAttr = void 0, Ue(R.uponSanitizeAttribute, e, o), c = o.attrValue, !re || "id" !== d && "name" !== d || (De(a, e), c = "user-content-" + c), G && Ky(/((--!?|])>)|<\/(style|title)/i, c)) {
            De(a, e);
            continue;
        }
        if (o.forceKeepAttr)
            continue;
        if (!o.keepAttr) {
            De(a, e);
            continue;
        }
        if (!Y && Ky(/\/>/i, c)) {
            De(a, e);
            continue;
        }
        X && Ly([A, T, O], (e => { c = $y(c, e, " "); }));
        const m = ke(e.nodeName);
        if (je(m, d, c)) {
            if (w && "object" == typeof g && "function" == typeof g.getAttributeType)
                if (i)
                    ;
                else
                    switch (g.getAttributeType(m, d)) {
                        case "TrustedHTML":
                            c = w.createHTML(c);
                            break;
                        case "TrustedScriptURL": c = w.createScriptURL(c);
                    }
            if (c !== u)
                try {
                    i ? e.setAttributeNS(i, a, c) : e.setAttribute(a, c), Ie(e) ? Pe(e) : Iy(n.removed);
                }
                catch (e) { }
        }
        else
            De(a, e);
    } Ue(R.afterSanitizeAttributes, e, null); }, Ve = function e(t) { let n = null; const o = Me(t); for (Ue(R.beforeSanitizeShadowDOM, t, null); n = o.nextNode();)
        Ue(R.uponSanitizeShadowNode, n, null), ze(n), $e(n), n.content instanceof a && e(n.content); Ue(R.afterSanitizeShadowDOM, t, null); }; return n.sanitize = function (e) { let t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {}, o = null, s = null, i = null, d = null; if (ve = !e, ve && (e = "\x3c!--\x3e"), "string" != typeof e && !Fe(e)) {
        if ("function" != typeof e.toString)
            throw Yy("toString is not a function");
        if ("string" != typeof (e = e.toString()))
            throw Yy("dirty is not a string, aborting");
    } if (!n.isSupported)
        return e; if (Q || Te(t), n.removed = [], "string" == typeof e && (ae = !1), ae) {
        if (e.nodeName) {
            const t = ke(e.nodeName);
            if (!F[t] || $[t])
                throw Yy("root node is forbidden and cannot be sanitized in-place");
        }
    }
    else if (e instanceof l)
        o = Le("\x3c!----\x3e"), s = o.ownerDocument.importNode(e, !0), 1 === s.nodeType && "BODY" === s.nodeName || "HTML" === s.nodeName ? o = s : o.appendChild(s);
    else {
        if (!ee && !X && !Z && -1 === e.indexOf("<"))
            return w && ne ? w.createHTML(e) : e;
        if (o = Le(e), !o)
            return ee ? null : ne ? E : "";
    } o && J && Pe(o.firstChild); const c = Me(ae ? e : o); for (; i = c.nextNode();)
        ze(i), $e(i), i.content instanceof a && Ve(i.content); if (ae)
        return e; if (ee) {
        if (te)
            for (d = S.call(o.ownerDocument); o.firstChild;)
                d.appendChild(o.firstChild);
        else
            d = o;
        return (z.shadowroot || z.shadowrootmode) && (d = N.call(r, d, !0)), d;
    } let u = Z ? o.outerHTML : o.innerHTML; return Z && F["!doctype"] && o.ownerDocument && o.ownerDocument.doctype && o.ownerDocument.doctype.name && Ky(CC, o.ownerDocument.doctype.name) && (u = "<!DOCTYPE " + o.ownerDocument.doctype.name + ">\n" + u), X && Ly([A, T, O], (e => { u = $y(u, e, " "); })), w && ne ? w.createHTML(u) : u; }, n.setConfig = function () { Te(arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {}), Q = !0; }, n.clearConfig = function () { Ne = null, Q = !1; }, n.isValidAttribute = function (e, t, n) { Ne || Te({}); const o = ke(e), r = ke(t); return je(o, r, n); }, n.addHook = function (e, t) { "function" == typeof t && Fy(R[e], t); }, n.removeHook = function (e, t) { if (void 0 !== t) {
        const n = My(R[e], t);
        return -1 === n ? void 0 : Uy(R[e], n, 1)[0];
    } return Iy(R[e]); }, n.removeHooks = function (e) { R[e] = []; }, n.removeAllHooks = function () { R = { afterSanitizeAttributes: [], afterSanitizeElements: [], afterSanitizeShadowDOM: [], beforeSanitizeAttributes: [], beforeSanitizeElements: [], beforeSanitizeShadowDOM: [], uponSanitizeAttribute: [], uponSanitizeElement: [], uponSanitizeShadowNode: [] }; }, n; }();
    const SC = an.each, kC = an.trim, NC = ["source", "protocol", "authority", "userInfo", "user", "password", "host", "port", "relative", "path", "directory", "file", "query", "anchor"], RC = { ftp: 21, http: 80, https: 443, mailto: 25 }, AC = ["img", "video"], TC = (e, t, n) => { const o = (e => { try {
        return decodeURIComponent(e);
    }
    catch (t) {
        return unescape(e);
    } })(t).replace(/\s/g, ""); return !e.allow_script_urls && (!!/((java|vb)script|mhtml):/i.test(o) || !e.allow_html_data_urls && (/^data:image\//i.test(o) ? ((e, t) => C(e) ? !e : !C(t) || !H(AC, t))(e.allow_svg_data_urls, n) && /^data:image\/svg\+xml/i.test(o) : /^data:/i.test(o))); };
    class OC {
        static parseDataUri(e) { let t; const n = decodeURIComponent(e).split(","), o = /data:([^;]+)/.exec(n[0]); return o && (t = o[1]), { type: t, data: n[1] }; }
        static isDomSafe(e, t, n = {}) { if (n.allow_script_urls)
            return !0; {
            const o = ua.decode(e).replace(/[\s\u0000-\u001F]+/g, "");
            return !TC(n, o, t);
        } }
        static getDocumentBaseUrl(e) { var t; let n; return n = 0 !== e.protocol.indexOf("http") && "file:" !== e.protocol ? null !== (t = e.href) && void 0 !== t ? t : "" : e.protocol + "//" + e.host + e.pathname, /^[^:]+:\/\/\/?[^\/]+\//.test(n) && (n = n.replace(/[\?#].*$/, "").replace(/[\/\\][^\/]+$/, ""), /[\/\\]$/.test(n) || (n += "/")), n; }
        constructor(e, t = {}) { this.path = "", this.directory = "", e = kC(e), this.settings = t; const n = t.base_uri, o = this; if (/^([\w\-]+):([^\/]{2})/i.test(e) || /^\s*#/.test(e))
            return void (o.source = e); const r = 0 === e.indexOf("//"); if (0 !== e.indexOf("/") || r || (e = (n && n.protocol || "http") + "://mce_host" + e), !/^[\w\-]*:?\/\//.test(e)) {
            const t = n ? n.path : new OC(document.location.href).directory;
            if ("" === (null == n ? void 0 : n.protocol))
                e = "//mce_host" + o.toAbsPath(t, e);
            else {
                const r = /([^#?]*)([#?]?.*)/.exec(e);
                r && (e = (n && n.protocol || "http") + "://mce_host" + o.toAbsPath(t, r[1]) + r[2]);
            }
        } e = e.replace(/@@/g, "(mce_at)"); const s = /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@\/]*):?([^:@\/]*))?@)?(\[[a-zA-Z0-9:.%]+\]|[^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/.exec(e); s && SC(NC, ((e, t) => { let n = s[t]; n && (n = n.replace(/\(mce_at\)/g, "@@")), o[e] = n; })), n && (o.protocol || (o.protocol = n.protocol), o.userInfo || (o.userInfo = n.userInfo), o.port || "mce_host" !== o.host || (o.port = n.port), o.host && "mce_host" !== o.host || (o.host = n.host), o.source = ""), r && (o.protocol = ""); }
        setPath(e) { const t = /^(.*?)\/?(\w+)?$/.exec(e); t && (this.path = t[0], this.directory = t[1], this.file = t[2]), this.source = "", this.getURI(); }
        toRelative(e) { if ("./" === e)
            return e; const t = new OC(e, { base_uri: this }); if ("mce_host" !== t.host && this.host !== t.host && t.host || this.port !== t.port || this.protocol !== t.protocol && "" !== t.protocol)
            return t.getURI(); const n = this.getURI(), o = t.getURI(); if (n === o || "/" === n.charAt(n.length - 1) && n.substr(0, n.length - 1) === o)
            return n; let r = this.toRelPath(this.path, t.path); return t.query && (r += "?" + t.query), t.anchor && (r += "#" + t.anchor), r; }
        toAbsolute(e, t) { const n = new OC(e, { base_uri: this }); return n.getURI(t && this.isSameOrigin(n)); }
        isSameOrigin(e) { if (this.host == e.host && this.protocol == e.protocol) {
            if (this.port == e.port)
                return !0;
            const t = this.protocol ? RC[this.protocol] : null;
            if (t && (this.port || t) == (e.port || t))
                return !0;
        } return !1; }
        toRelPath(e, t) { let n, o, r = 0, s = ""; const a = e.substring(0, e.lastIndexOf("/")).split("/"), i = t.split("/"); if (a.length >= i.length)
            for (n = 0, o = a.length; n < o; n++)
                if (n >= i.length || a[n] !== i[n]) {
                    r = n + 1;
                    break;
                } if (a.length < i.length)
            for (n = 0, o = i.length; n < o; n++)
                if (n >= a.length || a[n] !== i[n]) {
                    r = n + 1;
                    break;
                } if (1 === r)
            return t; for (n = 0, o = a.length - (r - 1); n < o; n++)
            s += "../"; for (n = r - 1, o = i.length; n < o; n++)
            s += n !== r - 1 ? "/" + i[n] : i[n]; return s; }
        toAbsPath(e, t) { let n = 0; const o = /\/$/.test(t) ? "/" : "", r = e.split("/"), s = t.split("/"), a = []; SC(r, (e => { e && a.push(e); })); const i = []; for (let e = s.length - 1; e >= 0; e--)
            0 !== s[e].length && "." !== s[e] && (".." !== s[e] ? n > 0 ? n-- : i.push(s[e]) : n++); const l = a.length - n; let d; return d = l <= 0 ? oe(i).join("/") : a.slice(0, l).join("/") + "/" + oe(i).join("/"), 0 !== d.indexOf("/") && (d = "/" + d), o && d.lastIndexOf("/") !== d.length - 1 && (d += o), d; }
        getURI(e = !1) { let t; return this.source && !e || (t = "", e || (this.protocol ? t += this.protocol + "://" : t += "//", this.userInfo && (t += this.userInfo + "@"), this.host && (t += this.host), this.port && (t += ":" + this.port)), this.path && (t += this.path), this.query && (t += "?" + this.query), this.anchor && (t += "#" + this.anchor), this.source = t), this.source; }
    }
    const BC = an.makeMap("src,href,data,background,action,formaction,poster,xlink:href"), PC = "data-mce-type";
    let DC = 0;
    const LC = (e, t, n, o, r) => { var s, a, i, l; const d = t.validate, c = n.getSpecialElements(); 8 === e.nodeType && !t.allow_conditional_comments && /^\[if/i.test(null !== (s = e.nodeValue) && void 0 !== s ? s : "") && (e.nodeValue = " " + e.nodeValue); const u = null !== (a = null == r ? void 0 : r.tagName) && void 0 !== a ? a : e.nodeName.toLowerCase(); if ("html" !== o && n.isValid(o))
        return void (C(r) && (r.allowedTags[u] = !0)); if (1 !== e.nodeType || "body" === u)
        return; const f = mn(e), g = vo(f, PC), p = ho(f, "data-mce-bogus"); if (!g && m(p))
        return void ("all" === p ? Eo(f) : xo(f)); const h = n.getElementRule(u); if (!d || h) {
        if (C(r) && (r.allowedTags[u] = !0), d && h && !g) {
            if (q(null !== (i = h.attributesForced) && void 0 !== i ? i : [], (e => { go(f, e.name, "{$uid}" === e.value ? "mce_" + DC++ : e.value); })), q(null !== (l = h.attributesDefault) && void 0 !== l ? l : [], (e => { vo(f, e.name) || go(f, e.name, "{$uid}" === e.value ? "mce_" + DC++ : e.value); })), h.attributesRequired && !$(h.attributesRequired, (e => vo(f, e))))
                return void xo(f);
            if (h.removeEmptyAttrs && (e => { const t = e.dom.attributes; return null == t || 0 === t.length; })(f))
                return void xo(f);
            h.outputName && h.outputName !== u && ((e, t) => { const n = ((e, t) => { const n = cn(t), o = Co(e); return po(n, o), n; })(e, t); io(e, n); const o = Hn(e); mo(n, o), Eo(e); })(f, h.outputName);
        }
    }
    else
        _e(c, u) ? Eo(f) : xo(f); }, MC = (e, t, n, o, r, s) => "html" !== n && !ks(o) || !(r in BC && TC(e, s, o)) && (!e.validate || t.isValid(o, r) || Ye(r, "data-") || Ye(r, "aria-")), IC = (e, t) => e.hasAttribute(PC) && ("id" === t || "class" === t || "style" === t), FC = (e, t) => e in t.getBoolAttrs(), UC = (e, t, n, o) => { const { attributes: r } = e; for (let s = r.length - 1; s >= 0; s--) {
        const a = r[s], i = a.name, l = a.value;
        MC(t, n, o, e.tagName.toLowerCase(), i, l) || IC(e, i) ? FC(i, n) && e.setAttribute(i, i) : e.removeAttribute(i);
    } }, zC = (e, t, n) => { const o = _C(); return o.addHook("uponSanitizeElement", ((o, r) => { LC(o, e, t, n.track(o), r); })), o.addHook("uponSanitizeAttribute", ((o, r) => { ((e, t, n, o, r) => { const s = e.tagName.toLowerCase(), { attrName: a, attrValue: i } = r; r.keepAttr = MC(t, n, o, s, a, i), r.keepAttr ? (r.allowedAttributes[a] = !0, FC(a, n) && (r.attrValue = a), t.allow_svg_data_urls && Ye(i, "data:image/svg+xml") && (r.forceKeepAttr = !0)) : IC(e, a) && (r.forceKeepAttr = !0); })(o, e, t, n.current(), r); })), o; }, jC = (e, t) => { const n = _C(), o = t.allow_mathml_annotation_encodings, r = p(o) && o.length > 0; n.addHook("uponSanitizeElement", ((e, n) => { var s; const a = null !== (s = n.tagName) && void 0 !== s ? s : e.nodeName.toLowerCase(); ((e, n) => r && "semantics" === n ? I.some(!0) : "annotation" === n ? I.some(qr(e) && (e => { const t = e.getAttribute("encoding"); return r && m(t) && H(o, t); })(e)) : p(t.extended_mathml_elements) && t.extended_mathml_elements.includes(n) ? I.from(!0) : I.none())(e, a).each((o => { n.allowedTags[a] = o, !o && t.sanitize && qr(e) && e.remove(); })); })), n.addHook("uponSanitizeAttribute", ((e, n) => { p(t.extended_mathml_attributes) && t.extended_mathml_attributes.includes(n.attrName) && (n.forceKeepAttr = !0); })), n.sanitize(e, { IN_PLACE: !0, USE_PROFILES: { mathMl: !0 } }); }, HC = e => t => { const n = Rs(t); if ("svg" === n)
        (e => { const t = ["type", "href", "role", "arcrole", "title", "show", "actuate", "label", "from", "to"].map((e => `xlink:${e}`)), n = { IN_PLACE: !0, USE_PROFILES: { html: !0, svg: !0, svgFilters: !0 }, ALLOWED_ATTR: t }; _C().sanitize(e, n); })(t);
    else {
        if ("math" !== n)
            throw new Error("Not a namespace element");
        jC(t, e);
    } }, $C = an.makeMap, VC = an.extend, qC = (e, t, n, o) => { const r = e.name, s = r in n && "title" !== r && "textarea" !== r && "noscript" !== r, a = t.childNodes; for (let t = 0, r = a.length; t < r; t++) {
        const r = a[t], i = new up(r.nodeName.toLowerCase(), r.nodeType);
        if (qr(r)) {
            const e = r.attributes;
            for (let t = 0, n = e.length; t < n; t++) {
                const n = e[t];
                i.attr(n.name, n.value);
            }
            ks(i.name) && (o(r), i.value = r.innerHTML);
        }
        else
            es(r) ? (i.value = r.data, s && (i.raw = !0)) : (os(r) || ts(r) || ns(r)) && (i.value = r.data);
        ks(i.name) || qC(i, r, n, o), e.append(i);
    } }, WC = (e = {}, t = Ra()) => { const n = my(), o = my(), r = { validate: !0, root_name: "body", sanitize: !0, ...e }, s = new DOMParser, a = ((e, t) => { const n = (() => { const e = Ve(), t = () => e.get().map(Rs).getOr("html"); return { track: n => (Ns(n) ? e.set(n) : e.get().exists((e => !e.contains(n))) && e.clear(), t()), current: t, reset: () => { e.clear(); } }; })(); if (e.sanitize) {
        const o = zC(e, t, n), r = (t, r) => { o.sanitize(t, ((e, t) => { const n = { IN_PLACE: !0, ALLOW_UNKNOWN_PROTOCOLS: !0, ALLOWED_TAGS: ["#comment", "#cdata-section", "body"], ALLOWED_ATTR: [], SAFE_FOR_XML: !1 }; return n.PARSER_MEDIA_TYPE = t, e.allow_script_urls ? n.ALLOWED_URI_REGEXP = /.*/ : e.allow_html_data_urls && (n.ALLOWED_URI_REGEXP = /^(?!(\w+script|mhtml):)/i), n; })(e, r)), o.removed = [], n.reset(); };
        return { sanitizeHtmlElement: r, sanitizeNamespaceElement: HC(e) };
    } return { sanitizeHtmlElement: (o, r) => { const s = document.createNodeIterator(o, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_COMMENT | NodeFilter.SHOW_TEXT); let a; for (; a = s.nextNode();) {
            const o = n.track(a);
            LC(a, e, t, o), qr(a) && UC(a, e, t, o);
        } n.reset(); }, sanitizeNamespaceElement: _ }; })(r, t), i = n.addFilter, l = n.getFilters, d = n.removeFilter, c = o.addFilter, u = o.getFilters, f = o.removeFilter, g = (e, n) => { const o = m(n.attr(PC)), r = 1 === n.type && !_e(e, n.name) && !$s(t, n) && !ks(n.name); return 3 === n.type || r && !o; }, p = { schema: t, addAttributeFilter: c, getAttributeFilters: u, removeAttributeFilter: f, addNodeFilter: i, getNodeFilters: l, removeNodeFilter: d, parse: (e, n = {}) => { var o; const i = r.validate, d = null !== (o = n.context) && void 0 !== o ? o : r.root_name, c = ((e, n, o = "html") => { const r = "xhtml" === o ? "application/xhtml+xml" : "text/html", i = _e(t.getSpecialElements(), n.toLowerCase()), l = i ? `<${n}>${e}</${n}>` : e, d = s.parseFromString("xhtml" === o ? `<html xmlns="http://www.w3.org/1999/xhtml"><head></head><body>${l}</body></html>` : /^[\s]*<head/i.test(e) || /^[\s]*<html/i.test(e) || /^[\s]*<!DOCTYPE/i.test(e) ? `<html>${l}</html>` : `<body>${l}</body>`, r).body; return a.sanitizeHtmlElement(d, r), i ? d.firstChild : d; })(e, d, n.format); Ms(t, c); const m = new up(d, 11); qC(m, c, t.getSpecialElements(), a.sanitizeNamespaceElement), c.innerHTML = ""; const [f, p] = ((e, t, n, o) => { const r = n.validate, s = t.getNonEmptyElements(), a = t.getWhitespaceElements(), i = VC($C("script,style,head,html,body,title,meta,param"), t.getBlockElements()), l = ka(t), d = /[ \t\r\n]+/g, c = /^[ \t\r\n]+/, u = /[ \t\r\n]+$/, m = e => { let t = e.parent; for (; C(t);) {
            if (t.name in a)
                return !0;
            t = t.parent;
        } return !1; }, f = n => n.name in i || $s(t, n) || ks(n.name) && n.parent === e, g = (t, n) => { const r = n ? t.prev : t.next; return !C(r) && !y(t.parent) && f(t.parent) && (t.parent !== e || !0 === o.isRootContent); }; return [e => { var t; if (3 === e.type && !m(e)) {
                let n = null !== (t = e.value) && void 0 !== t ? t : "";
                n = n.replace(d, " "), (((e, t) => C(e) && (t(e) || "br" === e.name))(e.prev, f) || g(e, !0)) && (n = n.replace(c, "")), 0 === n.length || " " === n && e.prev && 8 === e.prev.type && e.next && 8 === e.next.type ? e.remove() : e.value = n;
            } }, e => { var i; if (1 === e.type) {
                const i = t.getElementRule(e.name);
                if (r && i) {
                    const r = Ib(t, s, a, e);
                    i.paddInEmptyBlock && r && (e => { let n = e; for (; C(n);) {
                        if (n.name in l)
                            return Ib(t, s, a, n);
                        n = n.parent;
                    } return !1; })(e) ? Lb(n, o, f, e) : i.removeEmpty && r ? f(e) ? e.remove() : e.unwrap() : i.paddEmpty && (r || (e => { var t; return Mb(e, "#text") && (null === (t = null == e ? void 0 : e.firstChild) || void 0 === t ? void 0 : t.value) === it; })(e)) && Lb(n, o, f, e);
                }
            }
            else if (3 === e.type && !m(e)) {
                let t = null !== (i = e.value) && void 0 !== i ? i : "";
                (e.next && f(e.next) || g(e, !1)) && (t = t.replace(u, "")), 0 === t.length ? e.remove() : e.value = t;
            } }]; })(m, t, r, n), h = [], b = i ? e => ((e, n) => { jb(t, e) && n.push(e); })(e, h) : _, v = { nodes: {}, attributes: {} }, w = e => Bb(l(), u(), e, v); if (((e, t, n) => { const o = []; for (let n = e, r = n; n; r = n, n = n.walk()) {
            const s = n;
            q(t, (e => e(s))), y(s.parent) && s !== e ? n = r : o.push(s);
        } for (let e = o.length - 1; e >= 0; e--) {
            const t = o[e];
            q(n, (e => e(t)));
        } })(m, [f, w], [p, b]), h.reverse(), i && h.length > 0)
            if (n.context) {
                const { pass: e, fail: o } = K(h, (e => e.parent === m));
                zb(o, t, m, w), n.invalid = e.length > 0;
            }
            else
                zb(h, t, m, w); const E = ((e, t) => { var n; const o = null !== (n = t.forced_root_block) && void 0 !== n ? n : e.forced_root_block; return !1 === o ? "" : !0 === o ? "p" : o; })(r, n); return E && ("body" === m.name || n.isRootContent) && ((e, n) => { const o = VC($C("script,style,head,html,body,title,meta,param"), t.getBlockElements()), s = /^[ \t\r\n]+/, a = /[ \t\r\n]+$/; let i = e.firstChild, l = null; const d = e => { var t, n; e && (i = e.firstChild, i && 3 === i.type && (i.value = null === (t = i.value) || void 0 === t ? void 0 : t.replace(s, "")), i = e.lastChild, i && 3 === i.type && (i.value = null === (n = i.value) || void 0 === n ? void 0 : n.replace(a, ""))); }; if (t.isValidChild(e.name, n.toLowerCase())) {
            for (; i;) {
                const t = i.next;
                g(o, i) ? (l || (l = new up(n, 1), l.attr(r.forced_root_block_attrs), e.insert(l, i)), l.append(i)) : (d(l), l = null), i = t;
            }
            d(l);
        } })(m, E), n.invalid || Pb(v, n), m; } }; return ((e, t) => { var n, o; const r = e.schema; e.addAttributeFilter("href", (e => { let n = e.length; const o = e => { const t = e ? an.trim(e) : ""; return /\b(noopener)\b/g.test(t) ? t : (e => e.split(" ").filter((e => e.length > 0)).concat(["noopener"]).sort().join(" "))(t); }; if (!t.allow_unsafe_link_target)
        for (; n--;) {
            const t = e[n];
            "a" === t.name && "_blank" === t.attr("target") && t.attr("rel", o(t.attr("rel")));
        } })), t.allow_html_in_named_anchor || e.addAttributeFilter("id,name", (e => { let t, n, o, r, s = e.length; for (; s--;)
        if (r = e[s], "a" === r.name && r.firstChild && !r.attr("href"))
            for (o = r.parent, t = r.lastChild; t && o;)
                n = t.prev, o.insert(t, r), t = n; })), t.fix_list_elements && e.addNodeFilter("ul,ol", (e => { let t, n, o = e.length; for (; o--;)
        if (t = e[o], n = t.parent, n && ("ul" === n.name || "ol" === n.name))
            if (t.prev && "li" === t.prev.name)
                t.prev.append(t);
            else {
                const e = new up("li", 1);
                e.attr("style", "list-style-type: none"), t.wrap(e);
            } })); const s = r.getValidClasses(); t.validate && s && e.addAttributeFilter("class", (e => { var t; let n = e.length; for (; n--;) {
        const o = e[n], r = null !== (t = o.attr("class")) && void 0 !== t ? t : "", a = an.explode(r, " ");
        let i = "";
        for (let e = 0; e < a.length; e++) {
            const t = a[e];
            let n = !1, r = s["*"];
            r && r[t] && (n = !0), r = s[o.name], !n && r && r[t] && (n = !0), n && (i && (i += " "), i += t);
        }
        i.length || (i = null), o.attr("class", i);
    } })), ((e, t) => { const { blob_cache: n } = t; if (n) {
        const t = e => { const t = e.attr("src"); (e => e.attr("src") === nn.transparentSrc || C(e.attr("data-mce-placeholder")))(e) || (e => C(e.attr("data-mce-bogus")))(e) || y(t) || Cy(n, t, !0).each((t => { e.attr("src", t.blobUri()); })); };
        e.addAttributeFilter("src", (e => q(e, t)));
    } })(e, t); const a = null !== (n = t.sandbox_iframes) && void 0 !== n && n, i = me(null !== (o = t.sandbox_iframes_exclusions) && void 0 !== o ? o : []); t.convert_unsafe_embeds && e.addNodeFilter("object,embed", (e => q(e, (e => { e.replace((({ type: e, src: t, width: n, height: o } = {}, r, s) => { const a = (e => v(e) ? "iframe" : _y(e, "image") ? "img" : _y(e, "video") ? "video" : _y(e, "audio") ? "audio" : "iframe")(e), i = new up(a, 1); return i.attr("audio" === a ? { src: t } : { src: t, width: n, height: o }), "audio" !== a && "video" !== a || i.attr("controls", ""), "iframe" === a && r && xy(i, s), i; })({ type: e.attr("type"), src: "object" === e.name ? e.attr("data") : e.attr("src"), width: e.attr("width"), height: e.attr("height") }, a, i)); })))), a && e.addNodeFilter("iframe", (e => q(e, (e => xy(e, i))))); })(p, r), ((e, t, n) => { t.inline_styles && fy(e, t, n); })(p, r, t), p; }, KC = (e, t, n) => { const o = (e => ev(e) ? Tp({ validate: !1 }).serialize(e) : e)(e), r = t(o); if (r.isDefaultPrevented())
        return r; if (ev(e)) {
        if (r.content !== o) {
            const t = WC({ validate: !1, forced_root_block: !1, ...n }).parse(r.content, { context: e.name });
            return { ...r, content: t };
        }
        return { ...r, content: e };
    } return r; }, YC = e => ({ sanitize: su(e), sandbox_iframes: uu(e), sandbox_iframes_exclusions: mu(e) }), XC = (e, t) => { if (t.no_events)
        return Te.value(t); {
        const n = ((e, t) => e.dispatch("BeforeGetContent", t))(e, t);
        return n.isDefaultPrevented() ? Te.error(md(e, { content: "", ...n }).content) : Te.value(n);
    } }, GC = (e, t, n) => { if (n.no_events)
        return t; {
        const o = KC(t, (t => md(e, { ...n, content: t })), YC(e));
        return o.content;
    } }, ZC = (e, t) => { if (t.no_events)
        return Te.value(t); {
        const n = KC(t.content, (n => ((e, t) => e.dispatch("BeforeSetContent", t))(e, { ...t, content: n })), YC(e));
        return n.isDefaultPrevented() ? (ud(e, n), Te.error(void 0)) : Te.value(n);
    } }, QC = (e, t, n) => { n.no_events || ud(e, { ...n, content: t }); }, JC = (e, t, n) => ({ element: e, width: t, rows: n }), ew = (e, t) => ({ element: e, cells: t }), tw = (e, t) => ({ x: e, y: t }), nw = (e, t) => bo(e, t).bind(nt).getOr(1), ow = (e, t, n) => { const o = e.rows; return !!(o[n] ? o[n].cells : [])[t]; }, rw = e => G(e, ((e, t) => t.cells.length > e ? t.cells.length : e), 0), sw = (e, t) => { const n = e.rows; for (let e = 0; e < n.length; e++) {
        const o = n[e].cells;
        for (let n = 0; n < o.length; n++)
            if (Cn(o[n], t))
                return I.some(tw(n, e));
    } return I.none(); }, aw = (e, t, n, o, r) => { const s = [], a = e.rows; for (let e = n; e <= r; e++) {
        const n = a[e].cells, r = t < o ? n.slice(t, o + 1) : n.slice(o, t + 1);
        s.push(ew(a[e].element, r));
    } return s; }, iw = e => ((e, t) => { const n = So(e.element), o = cn("tbody"); return mo(o, t), co(n, o), n; })(e, (e => V(e.rows, (e => { const t = V(e.cells, (e => { const t = ko(e); return yo(t, "colspan"), yo(t, "rowspan"), t; })), n = So(e.element); return mo(n, t), n; })))(e)), lw = (e, t, n) => { const o = mn(t.commonAncestorContainer), r = Qp(o, e), s = Y(r, (e => n.isWrapper(xn(e)))), a = ((e, t) => Q(e, (e => "li" === xn(e) && Jm(e, t))).fold(N([]), (t => (e => Q(e, (e => "ul" === xn(e) || "ol" === xn(e))))(e).map((e => { const t = cn(xn(e)), n = Ce(Fo(e), ((e, t) => Ye(t, "list-style"))); return Do(t, n), [cn("li"), t]; })).getOr([]))))(r, t), i = s.concat(a.length ? a : (e => Ni(e) ? Ln(e).filter(ki).fold(N([]), (t => [e, t])) : ki(e) ? [e] : [])(o)); return V(i, So); }, dw = () => Yo([]), cw = (e, t) => ((e, t) => or(t, "table", T(Cn, e)))(e, t[0]).bind((e => { const n = t[0], o = t[t.length - 1], r = (e => { const t = JC(So(e), 0, []); return q(vr(e, "tr"), ((e, n) => { q(vr(e, "td,th"), ((o, r) => { ((e, t, n, o, r) => { const s = nw(r, "rowspan"), a = nw(r, "colspan"), i = e.rows; for (let e = n; e < n + s; e++) {
        i[e] || (i[e] = ew(ko(o), []));
        for (let o = t; o < t + a; o++)
            i[e].cells[o] = e === n && o === t ? r : So(r);
    } })(t, ((e, t, n) => { for (; ow(e, t, n);)
        t++; return t; })(t, r, n), n, e, o); })); })), JC(t.element, rw(t.rows), t.rows); })(e); return ((e, t, n) => sw(e, t).bind((t => sw(e, n).map((n => ((e, t, n) => { const o = t.x, r = t.y, s = n.x, a = n.y, i = r < a ? aw(e, o, r, s, a) : aw(e, o, a, s, r); return JC(e.element, rw(i), i); })(e, t, n))))))(r, n, o).map((e => Yo([iw(e)]))); })).getOrThunk(dw), uw = (e, t, n) => { const o = Ym(t, e); return o.length > 0 ? cw(e, o) : ((e, t, n) => t.length > 0 && t[0].collapsed ? dw() : ((e, t, n) => ((e, t) => { const n = G(t, ((e, t) => (co(t, e), t)), e); return t.length > 0 ? Yo([n]) : n; })(mn(t.cloneContents()), lw(e, t, n)))(e, t[0], n))(e, t, n); }, mw = (e, t) => t >= 0 && t < e.length && qm(e.charAt(t)), fw = e => Li(e.innerText), gw = e => qr(e) ? e.outerHTML : es(e) ? ua.encodeRaw(e.data, !1) : os(e) ? "\x3c!--" + e.data + "--\x3e" : "", pw = (e, t) => (((e, t) => { let n = 0; q(e, (e => { 0 === e[0] ? n++ : 1 === e[0] ? (((e, t, n) => { const o = (e => { let t; const n = document.createElement("div"), o = document.createDocumentFragment(); for (e && (n.innerHTML = e); t = n.firstChild;)
        o.appendChild(t); return o; })(t); if (e.hasChildNodes() && n < e.childNodes.length) {
        const t = e.childNodes[n];
        e.insertBefore(o, t);
    }
    else
        e.appendChild(o); })(t, e[1], n), n++) : 2 === e[0] && ((e, t) => { if (e.hasChildNodes() && t < e.childNodes.length) {
        const n = e.childNodes[t];
        e.removeChild(n);
    } })(t, n); })); })(((e, t) => { const n = e.length + t.length + 2, o = new Array(n), r = new Array(n), s = (n, o, r, a, l) => { const d = i(n, o, r, a); if (null === d || d.start === o && d.diag === o - a || d.end === n && d.diag === n - r) {
        let s = n, i = r;
        for (; s < o || i < a;)
            s < o && i < a && e[s] === t[i] ? (l.push([0, e[s]]), ++s, ++i) : o - n > a - r ? (l.push([2, e[s]]), ++s) : (l.push([1, t[i]]), ++i);
    }
    else {
        s(n, d.start, r, d.start - d.diag, l);
        for (let t = d.start; t < d.end; ++t)
            l.push([0, e[t]]);
        s(d.end, o, d.end - d.diag, a, l);
    } }, a = (n, o, r, s) => { let a = n; for (; a - o < s && a < r && e[a] === t[a - o];)
        ++a; return ((e, t, n) => ({ start: e, end: t, diag: n }))(n, a, o); }, i = (n, s, i, l) => { const d = s - n, c = l - i; if (0 === d || 0 === c)
        return null; const u = d - c, m = c + d, f = (m % 2 == 0 ? m : m + 1) / 2; let g, p, h, b, v; for (o[1 + f] = n, r[1 + f] = s + 1, g = 0; g <= f; ++g) {
        for (p = -g; p <= g; p += 2) {
            for (h = p + f, p === -g || p !== g && o[h - 1] < o[h + 1] ? o[h] = o[h + 1] : o[h] = o[h - 1] + 1, b = o[h], v = b - n + i - p; b < s && v < l && e[b] === t[v];)
                o[h] = ++b, ++v;
            if (u % 2 != 0 && u - g <= p && p <= u + g && r[h - u] <= o[h])
                return a(r[h - u], p + n - i, s, l);
        }
        for (p = u - g; p <= u + g; p += 2) {
            for (h = p + f - u, p === u - g || p !== u + g && r[h + 1] <= r[h - 1] ? r[h] = r[h + 1] - 1 : r[h] = r[h - 1], b = r[h] - 1, v = b - n + i - p; b >= n && v >= i && e[b] === t[v];)
                r[h] = b--, v--;
            if (u % 2 == 0 && -g <= p && p <= g && r[h] <= o[h + u])
                return a(r[h], p + n - i, s, l);
        }
    } return null; }, l = []; return s(0, e.length, 0, t.length, l), l; })(V(ce(t.childNodes), gw), e), t), t), hw = st((() => document.implementation.createHTMLDocument("undo"))), bw = e => { const t = e.serializer.getTempAttrs(), n = _p(e.getBody(), t); return (e => null !== e.querySelector("iframe"))(n) ? { type: "fragmented", fragments: Y(V(ce(n.childNodes), S(Li, gw)), (e => e.length > 0)), content: "", bookmark: null, beforeBookmark: null } : { type: "complete", fragments: null, content: Li(n.innerHTML), bookmark: null, beforeBookmark: null }; }, vw = (e, t, n) => { const o = n ? t.beforeBookmark : t.bookmark; "fragmented" === t.type ? pw(t.fragments, e.getBody()) : e.setContent(t.content, { format: "raw", no_selection: !C(o) || !Bm(o) || !o.isFakeCaret }), o && (e.selection.moveToBookmark(o), e.selection.scrollIntoView()); }, yw = e => "fragmented" === e.type ? e.fragments.join("") : e.content, Cw = e => { const t = cn("body", hw()); return Ao(t, yw(e)), q(vr(t, "*[data-mce-bogus]"), xo), Ro(t); }, ww = (e, t) => !(!e || !t) && (!!((e, t) => yw(e) === yw(t))(e, t) || ((e, t) => Cw(e) === Cw(t))(e, t)), Ew = e => 0 === e.get(), xw = (e, t, n) => { Ew(n) && (e.typing = t); }, _w = (e, t) => { e.typing && (xw(e, !1, t), e.add()); }, Sw = e => ({ init: { bindEvents: _ }, undoManager: { beforeChange: (t, n) => ((e, t, n) => { Ew(t) && n.set(ed(e.selection)); })(e, t, n), add: (t, n, o, r, s, a) => ((e, t, n, o, r, s, a) => { const i = bw(e), l = an.extend(s || {}, i); if (!Ew(o) || e.removed)
                return null; const d = t.data[n.get()]; if (e.dispatch("BeforeAddUndo", { level: l, lastLevel: d, originalEvent: a }).isDefaultPrevented())
                return null; if (d && ww(d, l))
                return null; t.data[n.get()] && r.get().each((e => { t.data[n.get()].beforeBookmark = e; })); const c = Ec(e); if (c && t.data.length > c) {
                for (let e = 0; e < t.data.length - 1; e++)
                    t.data[e] = t.data[e + 1];
                t.data.length--, n.set(t.data.length);
            } l.bookmark = ed(e.selection), n.get() < t.data.length - 1 && (t.data.length = n.get() + 1), t.data.push(l), n.set(t.data.length - 1); const u = { level: l, lastLevel: d, originalEvent: a }; return n.get() > 0 ? (e.setDirty(!0), e.dispatch("AddUndo", u), e.dispatch("change", u)) : e.dispatch("AddUndo", u), l; })(e, t, n, o, r, s, a), undo: (t, n, o) => ((e, t, n, o) => { let r; return t.typing && (t.add(), t.typing = !1, xw(t, !1, n)), o.get() > 0 && (o.set(o.get() - 1), r = t.data[o.get()], vw(e, r, !0), e.setDirty(!0), e.dispatch("Undo", { level: r })), r; })(e, t, n, o), redo: (t, n) => ((e, t, n) => { let o; return t.get() < n.length - 1 && (t.set(t.get() + 1), o = n[t.get()], vw(e, o, !1), e.setDirty(!0), e.dispatch("Redo", { level: o })), o; })(e, t, n), clear: (t, n) => ((e, t, n) => { t.data = [], n.set(0), t.typing = !1, e.dispatch("ClearUndos"); })(e, t, n), reset: e => (e => { e.clear(), e.add(); })(e), hasUndo: (t, n) => ((e, t, n) => n.get() > 0 || t.typing && t.data[0] && !ww(bw(e), t.data[0]))(e, t, n), hasRedo: (e, t) => ((e, t) => t.get() < e.data.length - 1 && !e.typing)(e, t), transact: (e, t, n) => ((e, t, n) => (_w(e, t), e.beforeChange(), e.ignore(n), e.add()))(e, t, n), ignore: (e, t) => ((e, t) => { try {
                e.set(e.get() + 1), t();
            }
            finally {
                e.set(e.get() - 1);
            } })(e, t), extra: (t, n, o, r) => ((e, t, n, o, r) => { if (t.transact(o)) {
                const o = t.data[n.get()].bookmark, s = t.data[n.get() - 1];
                vw(e, s, !0), t.transact(r) && (t.data[n.get() - 1].beforeBookmark = o);
            } })(e, t, n, o, r) }, formatter: { match: (t, n, o, r) => lv(e, t, n, o, r), matchAll: (t, n) => ((e, t, n) => { const o = [], r = {}, s = e.selection.getStart(); return e.dom.getParent(s, (s => { for (let a = 0; a < t.length; a++) {
                const i = t[a];
                !r[i] && iv(e, s, i, n) && (r[i] = !0, o.push(i));
            } }), e.dom.getRoot()), o; })(e, t, n), matchNode: (t, n, o, r) => iv(e, t, n, o, r), canApply: t => ((e, t) => { const n = e.formatter.get(t), o = e.dom; if (n && e.selection.isEditable()) {
                const t = e.selection.getStart(), r = yf(o, t);
                for (let e = n.length - 1; e >= 0; e--) {
                    const t = n[e];
                    if (!Ef(t))
                        return !0;
                    for (let e = r.length - 1; e >= 0; e--)
                        if (o.is(r[e], t.selector))
                            return !0;
                }
            } return !1; })(e, t), closest: t => ((e, t) => { const n = t => Cn(t, mn(e.getBody())); return I.from(e.selection.getStart(!0)).bind((o => xr(mn(o), (n => ue(t, (t => ((t, n) => iv(e, t.dom, n) ? I.some(n) : I.none())(n, t)))), n))).getOrNull(); })(e, t), apply: (t, n, o) => sy(e, t, n, o), remove: (t, n, o, r) => Jv(e, t, n, o, r), toggle: (t, n, o) => ((e, t, n, o) => { const r = e.formatter.get(t); r && (!lv(e, t, n, o) || "toggle" in r[0] && !r[0].toggle ? sy(e, t, n, o) : Jv(e, t, n, o)); })(e, t, n, o), formatChanged: (t, n, o, r, s) => ((e, t, n, o, r, s) => (((e, t, n, o, r, s) => { const a = t.get(); q(n.split(","), (t => { const n = xe(a, t).getOrThunk((() => { const e = { withSimilar: { state: Ne(!1), similar: !0, callbacks: [] }, withoutSimilar: { state: Ne(!1), similar: !1, callbacks: [] }, withVars: [] }; return a[t] = e, e; })), i = () => { const n = dy(e); return ly(e, n, t, r, s).isSome(); }; if (v(s)) {
                const e = r ? n.withSimilar : n.withoutSimilar;
                e.callbacks.push(o), 1 === e.callbacks.length && e.state.set(i());
            }
            else
                n.withVars.push({ state: Ne(i()), similar: r, vars: s, callback: o }); })), t.set(a); })(e, t, n, o, r, s), { unbind: () => ((e, t, n) => { const o = e.get(); q(t.split(","), (e => xe(o, e).each((t => { o[e] = { withSimilar: { ...t.withSimilar, callbacks: Y(t.withSimilar.callbacks, (e => e !== n)) }, withoutSimilar: { ...t.withoutSimilar, callbacks: Y(t.withoutSimilar.callbacks, (e => e !== n)) }, withVars: Y(t.withVars, (e => e.callback !== n)) }; })))), e.set(o); })(t, n, o) }))(e, t, n, o, r, s) }, editor: { getContent: t => ((e, t) => I.from(e.getBody()).fold(N("tree" === t.format ? new up("body", 11) : ""), (n => Np(e, t, n))))(e, t), setContent: (t, n) => ((e, t, n) => I.from(e.getBody()).map((o => ev(t) ? ((e, t, n, o) => { Db(e.parser.getNodeFilters(), e.parser.getAttributeFilters(), n); const r = Tp({ validate: !1 }, e.schema).serialize(n), s = Li(Ti(mn(t)) ? r : an.trim(r)); return tv(e, s, o.no_selection), { content: n, html: s }; })(e, o, t, n) : ((e, t, n, o) => { if (0 === (n = Li(n)).length || /^\s+$/.test(n)) {
                const r = '<br data-mce-bogus="1">';
                "TABLE" === t.nodeName ? n = "<tr><td>" + r + "</td></tr>" : /^(UL|OL)$/.test(t.nodeName) && (n = "<li>" + r + "</li>");
                const s = Ad(e);
                return e.schema.isValidChild(t.nodeName.toLowerCase(), s.toLowerCase()) ? (n = r, n = e.dom.createHTML(s, Td(e), n)) : n || (n = r), tv(e, n, o.no_selection), { content: n, html: n };
            } {
                "raw" !== o.format && (n = Tp({ validate: !1 }, e.schema).serialize(e.parser.parse(n, { isRootContent: !0, insert: !0 })));
                const r = Ti(mn(t)) ? n : an.trim(n);
                return tv(e, r, o.no_selection), { content: r, html: r };
            } })(e, o, t, n))).getOr({ content: t, html: ev(n.content) ? "" : n.content }))(e, t, n), insertContent: (t, n) => Jb(e, t, n), addVisual: t => ((e, t) => { const n = e.dom, o = C(t) ? t : e.getBody(); q(n.select("table,a", o), (t => { switch (t.nodeName) {
                case "TABLE":
                    const o = Oc(e), r = n.getAttrib(t, "border");
                    r && "0" !== r || !e.hasVisual ? n.removeClass(t, o) : n.addClass(t, o);
                    break;
                case "A": if (!n.getAttrib(t, "href")) {
                    const o = n.getAttrib(t, "name") || t.id, r = Bc(e);
                    o && e.hasVisual ? n.addClass(t, r) : n.removeClass(t, r);
                }
            } })), e.dispatch("VisualAid", { element: t, hasVisual: e.hasVisual }); })(e, t) }, selection: { getContent: (t, n) => ((e, t, n = {}) => { const o = ((e, t) => ({ ...e, format: t, get: !0, selection: !0, getInner: !0 }))(n, t); return XC(e, o).fold(R, (t => { const n = ((e, t) => { if ("text" === t.format)
                return (e => I.from(e.selection.getRng()).map((t => { var n; const o = I.from(e.dom.getParent(t.commonAncestorContainer, e.dom.isBlock)), r = e.getBody(), s = (e => e.map((e => e.nodeName)).getOr("div").toLowerCase())(o), a = mn(t.cloneContents()); Sp(a), kp(a); const i = e.dom.add(r, s, { "data-mce-bogus": "all", style: "overflow: hidden; opacity: 0;" }, a.dom), l = fw(i), d = Li(null !== (n = i.textContent) && void 0 !== n ? n : ""); if (e.dom.remove(i), mw(d, 0) || mw(d, d.length - 1)) {
                    const e = o.getOr(r), t = fw(e), n = t.indexOf(l);
                    return -1 === n ? l : (mw(t, n - 1) ? " " : "") + l + (mw(t, n + l.length) ? " " : "");
                } return l; })).getOr(""))(e); {
                const n = ((e, t) => { const n = e.selection.getRng(), o = e.dom.create("body"), r = e.selection.getSel(), s = ap(e, Km(r)), a = t.contextual ? uw(mn(e.getBody()), s, e.schema).dom : n.cloneContents(); return a && o.appendChild(a), e.selection.serializer.serialize(o, t); })(e, t);
                return "tree" === t.format ? n : e.selection.isCollapsed() ? "" : n;
            } })(e, t); return GC(e, n, t); })); })(e, t, n) }, autocompleter: { addDecoration: _, removeDecoration: _ }, raw: { getModel: () => I.none() } }), kw = e => _e(e.plugins, "rtc"), Nw = e => e.rtcInstance ? e.rtcInstance : Sw(e), Rw = e => { const t = e.rtcInstance; if (t)
        return t; throw new Error("Failed to get RTC instance not yet initialized."); }, Aw = e => Rw(e).init.bindEvents(), Tw = e => 0 === e.dom.length ? (Eo(e), I.none()) : I.some(e), Ow = (e, t, n, o, r) => { e.bind((e => ((o ? Th : Ah)(e.dom, o ? e.dom.length : 0, r), t.filter(Rn).map((t => ((e, t, n, o, r) => { const s = e.dom, a = t.dom, i = o ? s.length : a.length; o ? (Oh(s, a, r, !1, !o), n.setStart(a, i)) : (Oh(a, s, r, !1, !o), n.setEnd(a, i)); })(e, t, n, o, r)))))).orThunk((() => { const e = ((e, t) => e.filter((e => Yf.isBookmarkNode(e.dom))).bind(t ? Un : Fn))(t, o).or(t).filter(Rn); return e.map((e => ((e, t, n) => { Ln(e).each((o => { const r = e.dom; t && Ch(o, Pl(r, 0), n) ? Ah(r, 0, n) : !t && wh(o, Pl(r, r.length), n) && Th(r, r.length, n); })); })(e, o, r))); })); }, Bw = (e, t, n) => { if (_e(e, t)) {
        const o = Y(e[t], (e => e !== n));
        0 === o.length ? delete e[t] : e[t] = o;
    } };
    const Pw = e => !(!e || !e.ownerDocument) && wn(mn(e.ownerDocument), mn(e)), Dw = (e, t, n, o) => { let r, s; const { selectorChangedWithUnbind: a } = ((e, t) => { let n, o; const r = (t, n) => Q(n, (n => e.is(n, t))), s = t => e.getParents(t, void 0, e.getRoot()); return { selectorChangedWithUnbind: (e, a) => (n || (n = {}, o = {}, t.on("NodeChange", (e => { const t = e.element, a = s(t), i = {}; pe(n, ((e, t) => { r(t, a).each((n => { o[t] || (q(e, (e => { e(!0, { node: n, selector: t, parents: a }); })), o[t] = e), i[t] = e; })); })), pe(o, ((e, n) => { i[n] || (delete o[n], q(e, (e => { e(!1, { node: t, selector: n, parents: a }); }))); })); }))), n[e] || (n[e] = []), n[e].push(a), r(e, s(t.selection.getStart())).each((() => { o[e] = n[e]; })), { unbind: () => { Bw(n, e, a), Bw(o, e, a); } }) }; })(e, o), i = (e, t) => ((e, t, n = {}) => { const o = ((e, t) => ({ format: "html", ...e, set: !0, selection: !0, content: t }))(n, t); ZC(e, o).each((t => { const n = ((e, t) => { if ("raw" !== t.format) {
        const n = e.selection.getRng(), o = e.dom.getParent(n.commonAncestorContainer, e.dom.isBlock), r = o ? { context: o.nodeName.toLowerCase() } : {}, s = e.parser.parse(t.content, { forced_root_block: !1, ...r, ...t });
        return Tp({ validate: !1 }, e.schema).serialize(s);
    } return t.content; })(e, t), o = e.selection.getRng(); ((e, t, n) => { const o = I.from(t.firstChild).map(mn), r = I.from(t.lastChild).map(mn); e.deleteContents(), e.insertNode(t); const s = o.bind(Fn).filter(Rn).bind(Tw), a = r.bind(Un).filter(Rn).bind(Tw); Ow(s, o, e, !0, n), Ow(a, r, e, !1, n), e.collapse(!1); })(o, o.createContextualFragment(n), e.schema), e.selection.setRng(o), ep(e, o), QC(e, n, t); })); })(o, e, t), l = e => { const t = c(); t.collapse(!!e), u(t); }, d = () => t.getSelection ? t.getSelection() : t.document.selection, c = () => { let n; const a = (e, t, n) => { try {
        return t.compareBoundaryPoints(e, n);
    }
    catch (e) {
        return -1;
    } }, i = t.document; if (C(o.bookmark) && !vg(o)) {
        const e = og(o);
        if (e.isSome())
            return e.map((e => ap(o, [e])[0])).getOr(i.createRange());
    } try {
        const e = d();
        e && !Vr(e.anchorNode) && (n = e.rangeCount > 0 ? e.getRangeAt(0) : i.createRange(), n = ap(o, [n])[0]);
    }
    catch (e) { } if (n || (n = i.createRange()), rs(n.startContainer) && n.collapsed) {
        const t = e.getRoot();
        n.setStart(t, 0), n.setEnd(t, 0);
    } return r && s && (0 === a(n.START_TO_START, n, r) && 0 === a(n.END_TO_END, n, r) ? n = s : (r = null, s = null)), n; }, u = (e, t) => { if (!(e => !!e && Pw(e.startContainer) && Pw(e.endContainer))(e))
        return; const n = d(); if (e = o.dispatch("SetSelectionRange", { range: e, forward: t }).range, n) {
        s = e;
        try {
            n.removeAllRanges(), n.addRange(e);
        }
        catch (e) { }
        !1 === t && n.extend && (n.collapse(e.endContainer, e.endOffset), n.extend(e.startContainer, e.startOffset)), r = n.rangeCount > 0 ? n.getRangeAt(0) : null;
    } if (!e.collapsed && e.startContainer === e.endContainer && (null == n ? void 0 : n.setBaseAndExtent) && e.endOffset - e.startOffset < 2 && e.startContainer.hasChildNodes()) {
        const t = e.startContainer.childNodes[e.startOffset];
        t && "IMG" === t.nodeName && (n.setBaseAndExtent(e.startContainer, e.startOffset, e.endContainer, e.endOffset), n.anchorNode === e.startContainer && n.focusNode === e.endContainer || n.setBaseAndExtent(t, 0, t, 1));
    } o.dispatch("AfterSetSelectionRange", { range: e, forward: t }); }, m = () => { const t = d(), n = null == t ? void 0 : t.anchorNode, o = null == t ? void 0 : t.focusNode; if (!t || !n || !o || Vr(n) || Vr(o))
        return !0; const r = e.createRng(), s = e.createRng(); try {
        r.setStart(n, t.anchorOffset), r.collapse(!0), s.setStart(o, t.focusOffset), s.collapse(!0);
    }
    catch (e) {
        return !0;
    } return r.compareBoundaryPoints(r.START_TO_START, s) <= 0; }, f = { dom: e, win: t, serializer: n, editor: o, expand: (t = { type: "word" }) => u(Fg(e).expand(c(), t)), collapse: l, setCursorLocation: (t, n) => { const r = e.createRng(); C(t) && C(n) ? (r.setStart(t, n), r.setEnd(t, n), u(r), l(!1)) : (ef(e, r, o.getBody(), !0), u(r)); }, getContent: e => ((e, t = {}) => ((e, t, n) => Rw(e).selection.getContent(t, n))(e, t.format ? t.format : "html", t))(o, e), setContent: i, getBookmark: (e, t) => g.getBookmark(e, t), moveToBookmark: e => g.moveToBookmark(e), select: (t, n) => (((e, t, n) => I.from(t).bind((t => I.from(t.parentNode).map((o => { const r = e.nodeIndex(t), s = e.createRng(); return s.setStart(o, r), s.setEnd(o, r + 1), n && (ef(e, s, t, !0), ef(e, s, t, !1)), s; })))))(e, t, n).each(u), t), isCollapsed: () => { const e = c(), t = d(); return !(!e || e.item) && (e.compareEndPoints ? 0 === e.compareEndPoints("StartToEnd", e) : !t || e.collapsed); }, isEditable: () => { if (o.mode.isReadOnly())
            return !1; const t = c(), n = o.getBody().querySelectorAll('[data-mce-selected="1"]'); return n.length > 0 ? ne(n, (t => e.isEditable(t.parentElement))) : tp(e, t); }, isForward: m, setNode: t => (i(e.getOuterHTML(t)), t), getNode: () => ((e, t) => { if (!t)
            return e; let n = t.startContainer, o = t.endContainer; const r = t.startOffset, s = t.endOffset; let a = t.commonAncestorContainer; t.collapsed || (n === o && s - r < 2 && n.hasChildNodes() && (a = n.childNodes[r]), es(n) && es(o) && (n = n.length === r ? sp(n.nextSibling, !0) : n.parentNode, o = 0 === s ? sp(o.previousSibling, !1) : o.parentNode, n && n === o && (a = n))); const i = es(a) ? a.parentNode : a; return Wr(i) ? i : e; })(o.getBody(), c()), getSel: d, setRng: u, getRng: c, getStart: e => op(o.getBody(), c(), e), getEnd: e => rp(o.getBody(), c(), e), getSelectedBlocks: (t, n) => ((e, t, n, o) => { const r = [], s = e.getRoot(), a = e.getParent(n || op(s, t, t.collapsed), e.isBlock), i = e.getParent(o || rp(s, t, t.collapsed), e.isBlock); if (a && a !== s && r.push(a), a && i && a !== i) {
            let t;
            const n = new Fr(a, s);
            for (; (t = n.next()) && t !== i;)
                e.isBlock(t) && r.push(t);
        } return i && a !== i && i !== s && r.push(i), r; })(e, c(), t, n), normalize: () => { const t = c(), n = d(); if (!(Km(n).length > 1) && tf(o)) {
            const n = Lg(e, t);
            return n.each((e => { u(e, m()); })), n.getOr(t);
        } return t; }, selectorChanged: (e, t) => (a(e, t), f), selectorChangedWithUnbind: a, getScrollContainer: () => { let t, n = e.getRoot(); for (; n && "BODY" !== n.nodeName;) {
            if (n.scrollHeight > n.clientHeight) {
                t = n;
                break;
            }
            n = n.parentNode;
        } return t; }, scrollIntoView: (e, t) => { C(e) ? ((e, t, n) => { (e.inline ? Zg : Jg)(e, t, n); })(o, e, t) : ep(o, c(), t); }, placeCaretAt: (e, t) => u(Ng(e, t, o.getDoc())), getBoundingClientRect: () => { const e = c(); return e.collapsed ? Pl.fromRangeStart(e).getClientRects()[0] : e.getBoundingClientRect(); }, destroy: () => { t = r = s = null, p.destroy(); } }, g = Yf(f), p = kg(f, o); return f.bookmarkManager = g, f.controlSelection = p, f; }, Lw = (e, t, n) => { -1 === an.inArray(t, n) && (e.addAttributeFilter(n, ((e, t) => { let n = e.length; for (; n--;)
        e[n].attr(t, null); })), t.push(n)); }, Mw = (e, t) => { const n = ["data-mce-selected"], o = { entity_encoding: "named", remove_trailing_brs: !0, pad_empty_with_br: !1, ...e }, r = t && t.dom ? t.dom : ni.DOM, s = t && t.schema ? t.schema : Ra(o), a = WC(o, s); return ((e, t, n) => { e.addAttributeFilter("data-mce-tabindex", ((e, t) => { let n = e.length; for (; n--;) {
        const o = e[n];
        o.attr("tabindex", o.attr("data-mce-tabindex")), o.attr(t, null);
    } })), e.addAttributeFilter("src,href,style", ((e, o) => { const r = "data-mce-" + o, s = t.url_converter, a = t.url_converter_scope; let i = e.length; for (; i--;) {
        const t = e[i];
        let l = t.attr(r);
        void 0 !== l ? (t.attr(o, l.length > 0 ? l : null), t.attr(r, null)) : (l = t.attr(o), "style" === o ? l = n.serializeStyle(n.parseStyle(l), t.name) : s && (l = s.call(a, l, o, t.name)), t.attr(o, l.length > 0 ? l : null));
    } })), e.addAttributeFilter("class", (e => { let t = e.length; for (; t--;) {
        const n = e[t];
        let o = n.attr("class");
        o && (o = o.replace(/(?:^|\s)mce-item-\w+(?!\S)/g, ""), n.attr("class", o.length > 0 ? o : null));
    } })), e.addAttributeFilter("data-mce-type", ((e, t, n) => { let o = e.length; for (; o--;) {
        const t = e[o];
        if ("bookmark" === t.attr("data-mce-type") && !n.cleanup) {
            const e = I.from(t.firstChild).exists((e => { var t; return !Di(null !== (t = e.value) && void 0 !== t ? t : ""); }));
            e ? t.unwrap() : t.remove();
        }
    } })), e.addNodeFilter("script,style", ((e, n) => { var o; const r = e => e.replace(/(<!--\[CDATA\[|\]\]-->)/g, "\n").replace(/^[\r\n]*|[\r\n]*$/g, "").replace(/^\s*((<!--)?(\s*\/\/)?\s*<!\[CDATA\[|(<!--\s*)?\/\*\s*<!\[CDATA\[\s*\*\/|(\/\/)?\s*<!--|\/\*\s*<!--\s*\*\/)\s*[\r\n]*/gi, "").replace(/\s*(\/\*\s*\]\]>\s*\*\/(-->)?|\s*\/\/\s*\]\]>(-->)?|\/\/\s*(-->)?|\]\]>|\/\*\s*-->\s*\*\/|\s*-->\s*)\s*$/g, ""); let s = e.length; for (; s--;) {
        const a = e[s], i = a.firstChild, l = null !== (o = null == i ? void 0 : i.value) && void 0 !== o ? o : "";
        if ("script" === n) {
            const e = a.attr("type");
            e && a.attr("type", "mce-no/type" === e ? null : e.replace(/^mce\-/, "")), "xhtml" === t.element_format && i && l.length > 0 && (i.value = "// <![CDATA[\n" + r(l) + "\n// ]]>");
        }
        else
            "xhtml" === t.element_format && i && l.length > 0 && (i.value = "\x3c!--\n" + r(l) + "\n--\x3e");
    } })), e.addNodeFilter("#comment", (e => { let o = e.length; for (; o--;) {
        const r = e[o], s = r.value;
        t.preserve_cdata && 0 === (null == s ? void 0 : s.indexOf("[CDATA[")) ? (r.name = "#cdata", r.type = 4, r.value = n.decode(s.replace(/^\[CDATA\[|\]\]$/g, ""))) : 0 === (null == s ? void 0 : s.indexOf("mce:protected ")) && (r.name = "#text", r.type = 3, r.raw = !0, r.value = unescape(s).substr(14));
    } })), e.addNodeFilter("xml:namespace,input", ((e, t) => { let n = e.length; for (; n--;) {
        const o = e[n];
        7 === o.type ? o.remove() : 1 === o.type && ("input" !== t || o.attr("type") || o.attr("type", "text"));
    } })), e.addAttributeFilter("data-mce-type", (t => { q(t, (t => { "format-caret" === t.attr("data-mce-type") && (t.isEmpty(e.schema.getNonEmptyElements()) ? t.remove() : t.unwrap()); })); })), e.addAttributeFilter("data-mce-src,data-mce-href,data-mce-style,data-mce-selected,data-mce-expando,data-mce-block,data-mce-type,data-mce-resize,data-mce-placeholder", ((e, t) => { let n = e.length; for (; n--;)
        e[n].attr(t, null); })), t.remove_trailing_brs && ((e, t, n) => { t.addNodeFilter("br", ((t, o, r) => { const s = an.extend({}, n.getBlockElements()), a = n.getNonEmptyElements(), i = n.getWhitespaceElements(); s.body = 1; const l = e => e.name in s || $s(n, e); for (let o = 0, d = t.length; o < d; o++) {
        let d = t[o], c = d.parent;
        if (c && l(c) && d === c.lastChild) {
            let t = d.prev;
            for (; t;) {
                const e = t.name;
                if ("span" !== e || "bookmark" !== t.attr("data-mce-type")) {
                    "br" === e && (d = null);
                    break;
                }
                t = t.prev;
            }
            if (d && (d.remove(), Ib(n, a, i, c))) {
                const t = n.getElementRule(c.name);
                t && (t.removeEmpty ? c.remove() : t.paddEmpty && Lb(e, r, l, c));
            }
        }
        else {
            let e = d;
            for (; c && c.firstChild === e && c.lastChild === e && (e = c, !s[c.name]);)
                c = c.parent;
            if (e === c) {
                const e = new up("#text", 3);
                e.value = it, d.replace(e);
            }
        }
    } })); })(t, e, e.schema); })(a, o, r), { schema: s, addNodeFilter: a.addNodeFilter, addAttributeFilter: a.addAttributeFilter, serialize: (e, n = {}) => { const i = { format: "html", ...n }, l = ((e, t, n) => ((e, t) => C(e) && e.hasEventListeners("PreProcess") && !t.no_events)(e, n) ? ((e, t, n) => { let o; const r = e.dom; let s = t.cloneNode(!0); const a = document.implementation; if (a.createHTMLDocument) {
            const e = a.createHTMLDocument("");
            an.each("BODY" === s.nodeName ? s.childNodes : [s], (t => { e.body.appendChild(e.importNode(t, !0)); })), s = "BODY" !== s.nodeName ? e.body.firstChild : e.body, o = r.doc, r.doc = e;
        } return ((e, t) => { e.dispatch("PreProcess", t); })(e, { ...n, node: s }), o && (r.doc = o), s; })(e, t, n) : t)(t, e, i), d = ((e, t, n) => { const o = Li(n.getInner ? t.innerHTML : e.getOuterHTML(t)); return n.selection || Ti(mn(t)) ? o : an.trim(o); })(r, l, i), c = ((e, t, n) => { const o = n.selection ? { forced_root_block: !1, ...n } : n, r = e.parse(t, o); return (e => { const t = e => "br" === (null == e ? void 0 : e.name), n = e.lastChild; if (t(n)) {
            const e = n.prev;
            t(e) && (n.remove(), e.remove());
        } })(r), r; })(a, d, i); return "tree" === i.format ? c : ((e, t, n, o, r) => { const s = ((e, t, n) => Tp(e, t).serialize(n))(t, n, o); return ((e, t, n) => { if (!t.no_events && e) {
            const o = ((e, t) => e.dispatch("PostProcess", t))(e, { ...t, content: n });
            return o.content;
        } return n; })(e, r, s); })(t, o, s, c, i); }, addRules: s.addValidElements, setRules: s.setValidElements, addTempAttr: T(Lw, a, n), getTempAttrs: N(n), getNodeFilters: a.getNodeFilters, getAttributeFilters: a.getAttributeFilters, removeNodeFilter: a.removeNodeFilter, removeAttributeFilter: a.removeAttributeFilter }; }, Iw = (e, t) => { const n = Mw(e, t); return { schema: n.schema, addNodeFilter: n.addNodeFilter, addAttributeFilter: n.addAttributeFilter, serialize: n.serialize, addRules: n.addRules, setRules: n.setRules, addTempAttr: n.addTempAttr, getTempAttrs: n.getTempAttrs, getNodeFilters: n.getNodeFilters, getAttributeFilters: n.getAttributeFilters, removeNodeFilter: n.removeNodeFilter, removeAttributeFilter: n.removeAttributeFilter }; }, Fw = (e, t, n = {}) => { const o = ((e, t) => ({ format: "html", ...e, set: !0, content: t }))(n, t); return ZC(e, o).map((t => { const n = ((e, t, n) => Nw(e).editor.setContent(t, n))(e, t.content, t); return QC(e, n.html, t), n.content; })).getOr(t); }, Uw = "autoresize_on_init,content_editable_state,padd_empty_with_br,block_elements,boolean_attributes,editor_deselector,editor_selector,elements,file_browser_callback_types,filepicker_validator_handler,force_hex_style_colors,force_p_newlines,gecko_spellcheck,images_dataimg_filter,media_scripts,mode,move_caret_before_on_enter_elements,non_empty_elements,self_closing_elements,short_ended_elements,special,spellchecker_select_languages,spellchecker_whitelist,tab_focus,tabfocus_elements,table_responsive_width,text_block_elements,text_inline_elements,toolbar_drawer,types,validate,whitespace_elements,paste_enable_default_filters,paste_filter_drop,paste_word_valid_elements,paste_retain_style_properties,paste_convert_word_fake_lists,template_cdate_classes,template_mdate_classes,template_selected_content_classes,template_preview_replace_values,template_replace_values,templates,template_cdate_format,template_mdate_format".split(","), zw = [], jw = "bbcode,colorpicker,contextmenu,fullpage,legacyoutput,spellchecker,template,textcolor,rtc".split(","), Hw = [{ name: "export", replacedWith: "Export to PDF" }], $w = (e, t) => { const n = Y(t, (t => _e(e, t))); return ae(n); }, Vw = e => { const t = $w(e, Uw), n = e.forced_root_block; return !1 !== n && "" !== n || t.push("forced_root_block (false only)"), ae(t); }, qw = e => $w(e, zw), Ww = (e, t) => { const n = an.makeMap(e.plugins, " "), o = Y(t, (e => _e(n, e))); return ae(o); }, Kw = e => Ww(e, jw), Yw = e => Ww(e, Hw.map((e => e.name))), Xw = e => Q(Hw, (t => t.name === e)).fold((() => e), (t => t.replacedWith ? `${e}, replaced by ${t.replacedWith}` : e)), Gw = ni.DOM, Zw = e => I.from(e).each((e => e.destroy())), Qw = (() => { const e = {}; return { add: (t, n) => { e[t] = n; }, get: t => e[t] ? e[t] : { icons: {} }, has: t => _e(e, t) }; })(), Jw = di.ModelManager, eE = (e, t) => t.dom[e], tE = (e, t) => parseInt(Lo(t, e), 10), nE = T(eE, "clientWidth"), oE = T(eE, "clientHeight"), rE = T(tE, "margin-top"), sE = T(tE, "margin-left"), aE = e => { const t = [], n = () => { const t = e.theme; return t && t.getNotificationManagerImpl ? t.getNotificationManagerImpl() : (() => { const e = () => { throw new Error("Theme did not provide a NotificationManager implementation."); }; return { open: e, close: e, getArgs: e }; })(); }, o = () => I.from(t[0]), r = () => { o().each((e => { e.reposition(); })); }, s = e => { J(t, (t => t === e)).each((e => { t.splice(e, 1); })); }, a = (o, a = !0) => e.removed || !(e => { return (t = e.inline ? e.getBody() : e.getContentAreaContainer(), I.from(t).map(mn)).map(Oo).getOr(!1); var t; })(e) ? {} : (a && e.dispatch("BeforeOpenNotification", { notification: o }), Q(t, (e => { return t = n().getArgs(e), r = o, !(t.type !== r.type || t.text !== r.text || t.progressBar || t.timeout || r.progressBar || r.timeout); var t, r; })).getOrThunk((() => { e.editorManager.setActive(e); const a = n().open(o, (() => { s(a); }), (() => yg(e))); return (e => { t.push(e); })(a), r(), e.dispatch("OpenNotification", { notification: { ...a } }), a; }))), i = N(t); return (e => { e.on("SkinLoaded", (() => { const t = ac(e); t && a({ text: t, type: "warning", timeout: 0 }, !1), r(); })), e.on("show ResizeEditor ResizeWindow NodeChange ToggleView FullscreenStateChanged", (() => { requestAnimationFrame(r); })), e.on("remove", (() => { q(t.slice(), (e => { n().close(e); })); })), e.on("keydown", (e => { var t; const n = "f12" === (null === (t = e.key) || void 0 === t ? void 0 : t.toLowerCase()) || 123 === e.keyCode; e.altKey && n && (e.preventDefault(), o().map((e => mn(e.getEl()))).each((e => oo(e)))); })); })(e), { open: a, close: () => { o().each((e => { n().close(e), s(e), r(); })); }, getNotifications: i }; }, iE = di.PluginManager, lE = di.ThemeManager, dE = e => { let t = []; const n = () => { const t = e.theme; return t && t.getWindowManagerImpl ? t.getWindowManagerImpl() : (() => { const e = () => { throw new Error("Theme did not provide a WindowManager implementation."); }; return { open: e, openUrl: e, alert: e, confirm: e, close: e }; })(); }, o = (e, t) => (...n) => t ? t.apply(e, n) : void 0, r = n => { (t => { e.dispatch("CloseWindow", { dialog: t }); })(n), t = Y(t, (e => e !== n)), 0 === t.length && e.focus(); }, s = n => { e.editorManager.setActive(e), ng(e), e.ui.show(); const o = n(); return (n => { t.push(n), (t => { e.dispatch("OpenWindow", { dialog: t }); })(n); })(o), o; }; return e.on("remove", (() => { q(t, (e => { n().close(e); })); })), { open: (e, t) => s((() => n().open(e, t, r))), openUrl: e => s((() => n().openUrl(e, r))), alert: (e, t, r) => { const s = n(); s.alert(e, o(r || s, t)); }, confirm: (e, t, r) => { const s = n(); s.confirm(e, o(r || s, t)); }, close: () => { I.from(t[t.length - 1]).each((e => { n().close(e), r(e); })); } }; }, cE = (e, t) => { e.notificationManager.open({ type: "error", text: t }); }, uE = (e, t) => { e._skinLoaded ? cE(e, t) : e.on("SkinLoaded", (() => { cE(e, t); })); }, mE = (e, t, n) => { ld(e, t, { message: n }), console.error(n); }, fE = (e, t, n) => n ? `Failed to load ${e}: ${n} from url ${t}` : `Failed to load ${e} url: ${t}`, gE = (e, ...t) => { const n = window.console; n && (n.error ? n.error(e, ...t) : n.log(e, ...t)); }, pE = (e, t, n) => { try {
        e.getDoc().execCommand(t, !1, String(n));
    }
    catch (e) { } }, hE = (e, t, n) => { fr(e, t) && !n ? mr(e, t) : n && cr(e, t); }, bE = e => { const t = mn(e.getBody()); hE(t, "mce-content-readonly", !0), e.selection.controlSelection.hideResizeRect(), e._selectionOverrides.hideFakeCaret(), (e => { I.from(e.selection.getNode()).each((e => { e.removeAttribute("data-mce-selected"); })); })(e); }, vE = e => { const t = mn(e.getBody()); hE(t, "mce-content-readonly", !1), e.hasEditableRoot() && hr(t, !0), ((e, t) => { pE(e, "StyleWithCSS", t), pE(e, "enableInlineTableEditing", t), pE(e, "enableObjectResizing", t); })(e, !1), yg(e) && e.focus(), (e => { e.selection.setRng(e.selection.getRng()); })(e), e.nodeChanged(); }, yE = e => hu(e), CE = "data-mce-contenteditable", wE = (e, t) => { const n = mn(e.getBody()); t ? (bE(e), hr(n, !1), q(vr(n, '*[contenteditable="true"]'), (e => { go(e, CE, "true"), hr(e, !1); }))) : (q(vr(n, `*[${CE}="true"]`), (e => { yo(e, CE), hr(e, !0); })), vE(e)); }, EE = e => { e.parser.addAttributeFilter("contenteditable", (t => { yE(e) && q(t, (e => { e.attr(CE, e.attr("contenteditable")), e.attr("contenteditable", "false"); })); })), e.serializer.addAttributeFilter(CE, (t => { yE(e) && q(t, (e => { e.attr("contenteditable", e.attr(CE)); })); })), e.serializer.addTempAttr(CE); }, xE = ["copy"], _E = e => "content/" + e + "/content.css", SE = (e, t) => { const n = e.editorManager.baseURL + "/skins/content", o = `content${e.editorManager.suffix}.css`; return V(t, (t => (e => tinymce.Resource.has(_E(e)))(t) ? t : (e => /^[a-z0-9\-]+$/i.test(e))(t) && !e.inline ? `${n}/${t}/${o}` : e.documentBaseURI.toAbsolute(t))); }, kE = (e, t) => { const n = {}; return { findAll: (o, r = M) => { const s = Y((e => e ? ce(e.getElementsByTagName("img")) : [])(o), (t => { const n = t.src; return !t.hasAttribute("data-mce-bogus") && !t.hasAttribute("data-mce-placeholder") && !(!n || n === nn.transparentSrc) && (Ye(n, "blob:") ? !e.isUploaded(n) && r(t) : !!Ye(n, "data:") && r(t)); })), a = V(s, (e => { const o = e.src; if (_e(n, o))
            return n[o].then((t => m(t) ? t : { image: e, blobInfo: t.blobInfo })); {
            const r = ((e, t) => { const n = () => Promise.reject("Invalid data URI"); if (Ye(t, "blob:")) {
                const s = e.getByUri(t);
                return C(s) ? Promise.resolve(s) : (o = t, Ye(o, "blob:") ? (e => fetch(e).then((e => e.ok ? e.blob() : Promise.reject())).catch((() => Promise.reject({ message: `Cannot convert ${e} to Blob. Resource might not exist or is inaccessible.`, uriType: "blob" }))))(o) : Ye(o, "data:") ? (r = o, new Promise(((e, t) => { gy(r).bind((({ type: e, data: t, base64Encoded: n }) => py(e, t, n))).fold((() => t("Invalid data URI")), e); }))) : Promise.reject("Unknown URI format")).then((t => hy(t).then((o => vy(o, !1, (n => I.some(yy(e, t, n)))).getOrThunk(n)))));
            } var o, r; return Ye(t, "data:") ? Cy(e, t).fold(n, (e => Promise.resolve(e))) : Promise.reject("Unknown image data format"); })(t, o).then((t => (delete n[o], { image: e, blobInfo: t }))).catch((e => (delete n[o], e)));
            return n[o] = r, r;
        } })); return Promise.all(a); } }; }, NE = () => { let e = {}; const t = (e, t) => ({ status: e, resultUri: t }), n = t => t in e; return { hasBlobUri: n, getResultUri: t => { const n = e[t]; return n ? n.resultUri : null; }, isPending: t => !!n(t) && 1 === e[t].status, isUploaded: t => !!n(t) && 2 === e[t].status, markPending: n => { e[n] = t(1, null); }, markUploaded: (n, o) => { e[n] = t(2, o); }, removeFailed: t => { delete e[t]; }, destroy: () => { e = {}; } }; };
    let RE = 0;
    const AE = (e, t) => { const n = {}, o = (e, n) => new Promise(((o, r) => { const s = new XMLHttpRequest; s.open("POST", t.url), s.withCredentials = t.credentials, s.upload.onprogress = e => { n(e.loaded / e.total * 100); }, s.onerror = () => { r("Image upload failed due to a XHR Transport error. Code: " + s.status); }, s.onload = () => { if (s.status < 200 || s.status >= 300)
        return void r("HTTP Error: " + s.status); const e = JSON.parse(s.responseText); var n, a; e && m(e.location) ? o((n = t.basePath, a = e.location, n ? n.replace(/\/$/, "") + "/" + a.replace(/^\//, "") : a)) : r("Invalid JSON: " + s.responseText); }; const a = new FormData; a.append("file", e.blob(), e.filename()), s.send(a); })), r = w(t.handler) ? t.handler : o, s = (e, t) => ({ url: t, blobInfo: e, status: !0 }), a = (e, t) => ({ url: "", blobInfo: e, status: !1, error: t }), i = (e, t) => { an.each(n[e], (e => { e(t); })), delete n[e]; }; return { upload: (l, d) => t.url || r !== o ? ((t, o) => (t = an.grep(t, (t => !e.isUploaded(t.blobUri()))), Promise.all(an.map(t, (t => e.isPending(t.blobUri()) ? (e => { const t = e.blobUri(); return new Promise((e => { n[t] = n[t] || [], n[t].push(e); })); })(t) : ((t, n, o) => (e.markPending(t.blobUri()), new Promise((r => { let l, d; try {
            const c = () => { l && (l.close(), d = _); }, u = n => { c(), e.markUploaded(t.blobUri(), n), i(t.blobUri(), s(t, n)), r(s(t, n)); }, f = n => { c(), e.removeFailed(t.blobUri()), i(t.blobUri(), a(t, n)), r(a(t, n)); };
            d = e => { e < 0 || e > 100 || I.from(l).orThunk((() => I.from(o).map(P))).each((t => { l = t, t.progressBar.value(e); })); }, n(t, d).then(u, (e => { f(m(e) ? { message: e } : e); }));
        }
        catch (e) {
            r(a(t, e));
        } }))))(t, r, o))))))(l, d) : new Promise((e => { e([]); })) }; }, TE = e => () => e.notificationManager.open({ text: e.translate("Image uploading..."), type: "info", timeout: -1, progressBar: !0 }), OE = (e, t) => AE(t, { url: jd(e), basePath: Hd(e), credentials: $d(e), handler: Vd(e) }), BE = e => { const t = (() => { let e = []; const t = e => { if (!e.blob || !e.base64)
        throw new Error("blob and base64 representations of the image are required for BlobInfo to be created"); const t = e.id || "blobid" + RE++ + (() => { const e = () => Math.round(4294967295 * Be()).toString(36); return "s" + (new Date).getTime().toString(36) + e() + e() + e(); })(), n = e.name || t, o = e.blob; var r; return { id: N(t), name: N(n), filename: N(e.filename || n + "." + (r = o.type, { "image/jpeg": "jpg", "image/jpg": "jpg", "image/gif": "gif", "image/png": "png", "image/apng": "apng", "image/avif": "avif", "image/svg+xml": "svg", "image/webp": "webp", "image/bmp": "bmp", "image/tiff": "tiff" }[r.toLowerCase()] || "dat")), blob: N(o), base64: N(e.base64), blobUri: N(e.blobUri || URL.createObjectURL(o)), uri: N(e.uri) }; }, n = t => Q(e, t).getOrUndefined(), o = e => n((t => t.id() === e)); return { create: (e, n, o, r, s) => { if (m(e))
            return t({ id: e, name: r, filename: s, blob: n, base64: o }); if (f(e))
            return t(e); throw new Error("Unknown input type"); }, add: t => { o(t.id()) || e.push(t); }, get: o, getByUri: e => n((t => t.blobUri() === e)), getByData: (e, t) => n((n => n.base64() === e && n.blob().type === t)), findFirst: n, removeByUri: t => { e = Y(e, (e => e.blobUri() !== t || (URL.revokeObjectURL(e.blobUri()), !1))); }, destroy: () => { q(e, (e => { URL.revokeObjectURL(e.blobUri()); })), e = []; } }; })(); let n, o; const r = NE(), s = [], a = t => n => e.selection ? t(n) : [], i = (e, t, n) => { let o = 0; do {
        o = e.indexOf(t, o), -1 !== o && (e = e.substring(0, o) + n + e.substr(o + t.length), o += n.length - t.length + 1);
    } while (-1 !== o); return e; }, l = (e, t, n) => { const o = `src="${n}"${n === nn.transparentSrc ? ' data-mce-placeholder="1"' : ""}`; return e = i(e, `src="${t}"`, o), i(e, 'data-mce-src="' + t + '"', 'data-mce-src="' + n + '"'); }, d = (t, n) => { q(e.undoManager.data, (e => { "fragmented" === e.type ? e.fragments = V(e.fragments, (e => l(e, t, n))) : e.content = l(e.content, t, n); })); }, c = () => (n || (n = OE(e, r)), p().then(a((o => { const r = V(o, (e => e.blobInfo)); return n.upload(r, TE(e)).then(a((n => { const r = []; let s = !1; const a = V(n, ((n, a) => { const { blobInfo: i, image: l } = o[a]; let c = !1; return n.status && Fd(e) ? (n.url && !Ke(l.src, n.url) && (s = !0), t.removeByUri(l.src), kw(e) || ((t, n) => { const o = e.convertURL(n, "src"); var r; d(t.src, n), po(mn(t), { src: Id(e) ? (r = n, r + (-1 === r.indexOf("?") ? "?" : "&") + (new Date).getTime()) : n, "data-mce-src": o }); })(l, n.url)) : n.error && (n.error.remove && (d(l.src, nn.transparentSrc), r.push(l), c = !0), ((e, t) => { uE(e, li.translate(["Failed to upload image: {0}", t])); })(e, n.error.message)), { element: l, status: n.status, uploadUri: n.url, blobInfo: i, removed: c }; })); return r.length > 0 && !kw(e) ? e.undoManager.transact((() => { q(No(r), (n => { const o = Ln(n); Eo(n), o.each((e => t => { ((e, t) => e.dom.isEmpty(t.dom) && C(e.schema.getTextBlockElements()[xn(t)]))(e, t) && co(t, dn('<br data-mce-bogus="1" />')); })(e)), t.removeByUri(n.dom.src); })); })) : s && e.undoManager.dispatchChange(), a; }))); })))), u = () => Md(e) ? c() : Promise.resolve([]), g = e => ne(s, (t => t(e))), p = () => (o || (o = kE(r, t)), o.findAll(e.getBody(), g).then(a((t => { const n = Y(t, (t => m(t) ? (uE(e, t), !1) : "blob" !== t.uriType)); return kw(e) || q(n, (e => { d(e.image.src, e.blobInfo.blobUri()), e.image.src = e.blobInfo.blobUri(), e.image.removeAttribute("data-mce-src"); })), n; })))), h = n => n.replace(/src="(blob:[^"]+)"/g, ((n, o) => { const s = r.getResultUri(o); if (s)
        return 'src="' + s + '"'; let a = t.getByUri(o); return a || (a = G(e.editorManager.get(), ((e, t) => e || t.editorUpload && t.editorUpload.blobCache.getByUri(o)), void 0)), a ? 'src="data:' + a.blob().type + ";base64," + a.base64() + '"' : n; })); return e.on("SetContent", (() => { Md(e) ? u() : p(); })), e.on("RawSaveContent", (e => { e.content = h(e.content); })), e.on("GetContent", (e => { e.source_view || "raw" === e.format || "tree" === e.format || (e.content = h(e.content)); })), e.on("PostRender", (() => { e.parser.addNodeFilter("img", (e => { q(e, (e => { const n = e.attr("src"); if (!n || t.getByUri(n))
        return; const o = r.getResultUri(n); o && e.attr("src", o); })); })); })), { blobCache: t, addFilter: e => { s.push(e); }, uploadImages: c, uploadImagesAuto: u, scanForImages: p, destroy: () => { t.destroy(), r.destroy(), o = n = null; } }; }, PE = { remove_similar: !0, inherit: !1 }, DE = { selector: "td,th", ...PE }, LE = { tablecellbackgroundcolor: { styles: { backgroundColor: "%value" }, ...DE }, tablecellverticalalign: { styles: { "vertical-align": "%value" }, ...DE }, tablecellbordercolor: { styles: { borderColor: "%value" }, ...DE }, tablecellclass: { classes: ["%value"], ...DE }, tableclass: { selector: "table", classes: ["%value"], ...PE }, tablecellborderstyle: { styles: { borderStyle: "%value" }, ...DE }, tablecellborderwidth: { styles: { borderWidth: "%value" }, ...DE } }, ME = N(LE), IE = an.each, FE = ni.DOM, UE = e => C(e) && f(e), zE = (e, t) => { const n = t && t.schema || Ra({}), o = e => { const t = m(e) ? { name: e, classes: [], attrs: {} } : e, n = FE.create(t.name); return ((e, t) => { t.classes.length > 0 && FE.addClass(e, t.classes.join(" ")), FE.setAttribs(e, t.attrs); })(n, t), n; }, r = (e, t, s) => { let a; const i = t[0], l = UE(i) ? i.name : void 0, d = ((e, t) => { const o = n.getElementRule(e.nodeName.toLowerCase()), r = null == o ? void 0 : o.parentsRequired; return !(!r || !r.length) && (t && H(r, t) ? t : r[0]); })(e, l); if (d)
        l === d ? (a = i, t = t.slice(1)) : a = d;
    else if (i)
        a = i, t = t.slice(1);
    else if (!s)
        return e; const c = a ? o(a) : FE.create("div"); c.appendChild(e), s && an.each(s, (t => { const n = o(t); c.insertBefore(n, e); })); const u = UE(a) ? a.siblings : void 0; return r(c, t, u); }, s = FE.create("div"); if (e.length > 0) {
        const t = e[0], n = o(t), a = UE(t) ? t.siblings : void 0;
        s.appendChild(r(n, e.slice(1), a));
    } return s; }, jE = e => { let t = "div"; const n = { name: t, classes: [], attrs: {}, selector: e = an.trim(e) }; return "*" !== e && (t = e.replace(/(?:([#\.]|::?)([\w\-]+)|(\[)([^\]]+)\]?)/g, ((e, t, o, r, s) => { switch (t) {
        case "#":
            n.attrs.id = o;
            break;
        case ".":
            n.classes.push(o);
            break;
        case ":": -1 !== an.inArray("checked disabled enabled read-only required".split(" "), o) && (n.attrs[o] = o);
    } if ("[" === r) {
        const e = s.match(/([\w\-]+)(?:\=\"([^\"]+))?/);
        e && (n.attrs[e[1]] = e[2]);
    } return ""; }))), n.name = t || "div", n; }, HE = (e, t) => { let n = "", o = fc(e); if ("" === o)
        return ""; const r = e => m(e) ? e.replace(/%(\w+)/g, "") : "", s = (t, n) => FE.getStyle(null != n ? n : e.getBody(), t, !0); if (m(t)) {
        const n = e.formatter.get(t);
        if (!n)
            return "";
        t = n[0];
    } if ("preview" in t) {
        const e = t.preview;
        if (!1 === e)
            return "";
        o = e || o;
    } let a, i = t.block || t.inline || "span"; const l = (d = t.selector, m(d) ? (d = (d = d.split(/\s*,\s*/)[0]).replace(/\s*(~\+|~|\+|>)\s*/g, "$1"), an.map(d.split(/(?:>|\s+(?![^\[\]]+\]))/), (e => { const t = an.map(e.split(/(?:~\+|~|\+)/), jE), n = t.pop(); return t.length && (n.siblings = t), n; })).reverse()) : []); var d; l.length > 0 ? (l[0].name || (l[0].name = i), i = t.selector, a = zE(l, e)) : a = zE([i], e); const c = FE.select(i, a)[0] || a.firstChild; IE(t.styles, ((e, t) => { const n = r(e); n && FE.setStyle(c, t, n); })), IE(t.attributes, ((e, t) => { const n = r(e); n && FE.setAttrib(c, t, n); })), IE(t.classes, (e => { const t = r(e); FE.hasClass(c, t) || FE.addClass(c, t); })), e.dispatch("PreviewFormats"), FE.setStyles(a, { position: "absolute", left: -65535 }), e.getBody().appendChild(a); const u = s("fontSize"), f = /px$/.test(u) ? parseInt(u, 10) : 0; return IE(o.split(" "), (e => { let t = s(e, c); if (!("background-color" === e && /transparent|rgba\s*\([^)]+,\s*0\)/.test(t) && (t = s(e), "#ffffff" === Ma(t).toLowerCase()) || "color" === e && "#000000" === Ma(t).toLowerCase())) {
        if ("font-size" === e && /em|%$/.test(t)) {
            if (0 === f)
                return;
            t = parseFloat(t) / (/%$/.test(t) ? 100 : 1) * f + "px";
        }
        "border" === e && t && (n += "padding:0 2px;"), n += e + ":" + t + ";";
    } })), e.dispatch("AfterPreviewFormats"), FE.remove(a), n; }, $E = e => { const t = (e => { const t = {}, n = (e, o) => { e && (m(e) ? (p(o) || (o = [o]), q(o, (e => { v(e.deep) && (e.deep = !Ef(e)), v(e.split) && (e.split = !Ef(e) || xf(e)), v(e.remove) && Ef(e) && !xf(e) && (e.remove = "none"), Ef(e) && xf(e) && (e.mixed = !0, e.block_expand = !0), m(e.classes) && (e.classes = e.classes.split(/\s+/)); })), t[e] = o) : pe(e, ((e, t) => { n(t, e); }))); }; return n((e => { const t = e.dom, n = e.schema.type, o = { valigntop: [{ selector: "td,th", styles: { verticalAlign: "top" } }], valignmiddle: [{ selector: "td,th", styles: { verticalAlign: "middle" } }], valignbottom: [{ selector: "td,th", styles: { verticalAlign: "bottom" } }], alignleft: [{ selector: "figure.image", collapsed: !1, classes: "align-left", ceFalseOverride: !0, preview: "font-family font-size" }, { selector: "figure,p,h1,h2,h3,h4,h5,h6,td,th,tr,div,ul,ol,li,pre", styles: { textAlign: "left" }, inherit: !1, preview: !1 }, { selector: "img,audio,video", collapsed: !1, styles: { float: "left" }, preview: "font-family font-size" }, { selector: ".mce-placeholder", styles: { float: "left" }, ceFalseOverride: !0 }, { selector: "table", collapsed: !1, styles: { marginLeft: "0px", marginRight: "auto" }, onformat: e => { t.setStyle(e, "float", null); }, preview: "font-family font-size" }, { selector: ".mce-preview-object,[data-ephox-embed-iri]", ceFalseOverride: !0, styles: { float: "left" } }], aligncenter: [{ selector: "figure,p,h1,h2,h3,h4,h5,h6,td,th,tr,div,ul,ol,li,pre", styles: { textAlign: "center" }, inherit: !1, preview: "font-family font-size" }, { selector: "figure.image", collapsed: !1, classes: "align-center", ceFalseOverride: !0, preview: "font-family font-size" }, { selector: "img,audio,video", collapsed: !1, styles: { display: "block", marginLeft: "auto", marginRight: "auto" }, preview: !1 }, { selector: ".mce-placeholder", styles: { display: "block", marginLeft: "auto", marginRight: "auto" }, ceFalseOverride: !0 }, { selector: "table", collapsed: !1, styles: { marginLeft: "auto", marginRight: "auto" }, preview: "font-family font-size" }, { selector: ".mce-preview-object", ceFalseOverride: !0, styles: { display: "table", marginLeft: "auto", marginRight: "auto" }, preview: !1 }, { selector: "[data-ephox-embed-iri]", ceFalseOverride: !0, styles: { marginLeft: "auto", marginRight: "auto" }, preview: !1 }], alignright: [{ selector: "figure.image", collapsed: !1, classes: "align-right", ceFalseOverride: !0, preview: "font-family font-size" }, { selector: "figure,p,h1,h2,h3,h4,h5,h6,td,th,tr,div,ul,ol,li,pre", styles: { textAlign: "right" }, inherit: !1, preview: "font-family font-size" }, { selector: "img,audio,video", collapsed: !1, styles: { float: "right" }, preview: "font-family font-size" }, { selector: ".mce-placeholder", styles: { float: "right" }, ceFalseOverride: !0 }, { selector: "table", collapsed: !1, styles: { marginRight: "0px", marginLeft: "auto" }, onformat: e => { t.setStyle(e, "float", null); }, preview: "font-family font-size" }, { selector: ".mce-preview-object,[data-ephox-embed-iri]", ceFalseOverride: !0, styles: { float: "right" }, preview: !1 }], alignjustify: [{ selector: "figure,p,h1,h2,h3,h4,h5,h6,td,th,tr,div,ul,ol,li,pre", styles: { textAlign: "justify" }, inherit: !1, preview: "font-family font-size" }], bold: [{ inline: "strong", remove: "all", preserve_attributes: ["class", "style"] }, { inline: "span", styles: { fontWeight: "bold" } }, { inline: "b", remove: "all", preserve_attributes: ["class", "style"] }], italic: [{ inline: "em", remove: "all", preserve_attributes: ["class", "style"] }, { inline: "span", styles: { fontStyle: "italic" } }, { inline: "i", remove: "all", preserve_attributes: ["class", "style"] }], underline: [{ inline: "span", styles: { textDecoration: "underline" }, exact: !0 }, { inline: "u", remove: "all", preserve_attributes: ["class", "style"] }], strikethrough: (() => { const e = { inline: "span", styles: { textDecoration: "line-through" }, exact: !0 }, t = { inline: "strike", remove: "all", preserve_attributes: ["class", "style"] }, o = { inline: "s", remove: "all", preserve_attributes: ["class", "style"] }; return "html4" !== n ? [o, e, t] : [e, o, t]; })(), forecolor: { inline: "span", styles: { color: "%value" }, links: !0, remove_similar: !0, clear_child_styles: !0 }, hilitecolor: { inline: "span", styles: { backgroundColor: "%value" }, links: !0, remove_similar: !0, clear_child_styles: !0 }, fontname: { inline: "span", toggle: !1, styles: { fontFamily: "%value" }, clear_child_styles: !0 }, fontsize: { inline: "span", toggle: !1, styles: { fontSize: "%value" }, clear_child_styles: !0 }, lineheight: { selector: "h1,h2,h3,h4,h5,h6,p,li,td,th,div", styles: { lineHeight: "%value" } }, fontsize_class: { inline: "span", attributes: { class: "%value" } }, blockquote: { block: "blockquote", wrapper: !0, remove: "all" }, subscript: { inline: "sub" }, superscript: { inline: "sup" }, code: { inline: "code" }, samp: { inline: "samp" }, link: { inline: "a", selector: "a", remove: "all", split: !0, deep: !0, onmatch: (e, t, n) => qr(e) && e.hasAttribute("href"), onformat: (e, n, o) => { an.each(o, ((n, o) => { t.setAttrib(e, o, n); })); } }, lang: { inline: "span", clear_child_styles: !0, remove_similar: !0, attributes: { lang: "%value", "data-mce-lang": e => { var t; return null !== (t = null == e ? void 0 : e.customValue) && void 0 !== t ? t : null; } } }, removeformat: [{ selector: "b,strong,em,i,font,u,strike,s,sub,sup,dfn,code,samp,kbd,var,cite,mark,q,del,ins,small", remove: "all", split: !0, expand: !1, block_expand: !0, deep: !0 }, { selector: "span", attributes: ["style", "class"], remove: "empty", split: !0, expand: !1, deep: !0 }, { selector: "*", attributes: ["style", "class"], split: !1, expand: !1, deep: !0 }] }; return an.each("p h1 h2 h3 h4 h5 h6 div address pre dt dd".split(/\s/), (e => { o[e] = { block: e, remove: "all" }; })), o; })(e)), n(ME()), n(mc(e)), { get: e => C(e) ? t[e] : t, has: e => _e(t, e), register: n, unregister: e => (e && t[e] && delete t[e], t) }; })(e), n = Ne({}); return (e => { e.addShortcut("meta+b", "", "Bold"), e.addShortcut("meta+i", "", "Italic"), e.addShortcut("meta+u", "", "Underline"); for (let t = 1; t <= 6; t++)
        e.addShortcut("access+" + t, "", ["FormatBlock", !1, "h" + t]); e.addShortcut("access+7", "", ["FormatBlock", !1, "p"]), e.addShortcut("access+8", "", ["FormatBlock", !1, "div"]), e.addShortcut("access+9", "", ["FormatBlock", !1, "address"]); })(e), (e => { e.on("mouseup keydown", (t => { var n; ((e, t, n) => { const o = e.selection, r = e.getBody(); fv(e, null, n), 8 !== t && 46 !== t || !o.isCollapsed() || o.getStart().innerHTML !== dv || fv(e, Tm(r, o.getStart()), !0), 37 !== t && 39 !== t || fv(e, Tm(r, o.getStart()), !0); })(e, t.keyCode, (n = e.selection.getRng().endContainer, es(n) && Xe(n.data, it))); })); })(e), kw(e) || ((e, t) => { e.set({}), t.on("NodeChange", (n => { cy(t, n.element, e.get()); })), t.on("FormatApply FormatRemove", (n => { const o = I.from(n.node).map((e => rf(e) ? e : e.startContainer)).bind((e => qr(e) ? I.some(e) : I.from(e.parentElement))).getOrThunk((() => iy(t))); cy(t, o, e.get()); })); })(n, e), { get: t.get, has: t.has, register: t.register, unregister: t.unregister, apply: (t, n, o) => { ((e, t, n, o) => { Rw(e).formatter.apply(t, n, o); })(e, t, n, o); }, remove: (t, n, o, r) => { ((e, t, n, o, r) => { Rw(e).formatter.remove(t, n, o, r); })(e, t, n, o, r); }, toggle: (t, n, o) => { ((e, t, n, o) => { Rw(e).formatter.toggle(t, n, o); })(e, t, n, o); }, match: (t, n, o, r) => ((e, t, n, o, r) => Rw(e).formatter.match(t, n, o, r))(e, t, n, o, r), closest: t => ((e, t) => Rw(e).formatter.closest(t))(e, t), matchAll: (t, n) => ((e, t, n) => Rw(e).formatter.matchAll(t, n))(e, t, n), matchNode: (t, n, o, r) => ((e, t, n, o, r) => Rw(e).formatter.matchNode(t, n, o, r))(e, t, n, o, r), canApply: t => ((e, t) => Rw(e).formatter.canApply(t))(e, t), formatChanged: (t, o, r, s) => ((e, t, n, o, r, s) => Rw(e).formatter.formatChanged(t, n, o, r, s))(e, n, t, o, r, s), getCssText: T(HE, e) }; }, VE = e => { switch (e.toLowerCase()) {
        case "undo":
        case "redo":
        case "mcefocus": return !0;
        default: return !1;
    } }, qE = e => { const t = Ve(), n = Ne(0), o = Ne(0), r = { data: [], typing: !1, beforeChange: () => { ((e, t, n) => { Rw(e).undoManager.beforeChange(t, n); })(e, n, t); }, add: (s, a) => ((e, t, n, o, r, s, a) => Rw(e).undoManager.add(t, n, o, r, s, a))(e, r, o, n, t, s, a), dispatchChange: () => { e.setDirty(!0); const t = bw(e); t.bookmark = ed(e.selection), e.dispatch("change", { level: t, lastLevel: ie(r.data, o.get()).getOrUndefined() }); }, undo: () => ((e, t, n, o) => Rw(e).undoManager.undo(t, n, o))(e, r, n, o), redo: () => ((e, t, n) => Rw(e).undoManager.redo(t, n))(e, o, r.data), clear: () => { ((e, t, n) => { Rw(e).undoManager.clear(t, n); })(e, r, o); }, reset: () => { ((e, t) => { Rw(e).undoManager.reset(t); })(e, r); }, hasUndo: () => ((e, t, n) => Rw(e).undoManager.hasUndo(t, n))(e, r, o), hasRedo: () => ((e, t, n) => Rw(e).undoManager.hasRedo(t, n))(e, r, o), transact: t => ((e, t, n, o) => Rw(e).undoManager.transact(t, n, o))(e, r, n, t), ignore: t => { ((e, t, n) => { Rw(e).undoManager.ignore(t, n); })(e, n, t); }, extra: (t, n) => { ((e, t, n, o, r) => { Rw(e).undoManager.extra(t, n, o, r); })(e, r, o, t, n); } }; return kw(e) || ((e, t, n) => { const o = Ne(!1), r = e => { xw(t, !1, n), t.add({}, e); }; e.on("init", (() => { t.add(); })), e.on("BeforeExecCommand", (e => { const o = e.command; VE(o) || (_w(t, n), t.beforeChange()); })), e.on("ExecCommand", (e => { const t = e.command; VE(t) || r(e); })), e.on("ObjectResizeStart cut", (() => { t.beforeChange(); })), e.on("SaveContent ObjectResized blur", r), e.on("dragend", r), e.on("keyup", (n => { const s = n.keyCode; if (n.isDefaultPrevented())
        return; const a = nn.os.isMacOS() && "Meta" === n.key; (s >= 33 && s <= 36 || s >= 37 && s <= 40 || 45 === s || n.ctrlKey || a) && (r(), e.nodeChanged()), 46 !== s && 8 !== s || e.nodeChanged(), o.get() && t.typing && !ww(bw(e), t.data[0]) && (e.isDirty() || e.setDirty(!0), e.dispatch("TypingUndo"), o.set(!1), e.nodeChanged()); })), e.on("keydown", (e => { const s = e.keyCode; if (e.isDefaultPrevented())
        return; if (s >= 33 && s <= 36 || s >= 37 && s <= 40 || 45 === s)
        return void (t.typing && r(e)); const a = e.ctrlKey && !e.altKey || e.metaKey; if ((s < 16 || s > 20) && 224 !== s && 91 !== s && !t.typing && !a)
        return t.beforeChange(), xw(t, !0, n), t.add({}, e), void o.set(!0); (nn.os.isMacOS() ? e.metaKey : e.ctrlKey && !e.altKey) && t.beforeChange(); })), e.on("mousedown", (e => { t.typing && r(e); })), e.on("input", (e => { var t; e.inputType && ("insertReplacementText" === e.inputType || "insertText" === (t = e).inputType && null === t.data || (e => "insertFromPaste" === e.inputType || "insertFromDrop" === e.inputType)(e)) && r(e); })), e.on("AddUndo Undo Redo ClearUndos", (t => { t.isDefaultPrevented() || e.nodeChanged(); })); })(e, r, n), (e => { e.addShortcut("meta+z", "", "Undo"), e.addShortcut("meta+y,meta+shift+z", "", "Redo"); })(e), r; }, WE = [9, 27, wg.HOME, wg.END, 19, 20, 44, 144, 145, 33, 34, 45, 16, 17, 18, 91, 92, 93, wg.DOWN, wg.UP, wg.LEFT, wg.RIGHT].concat(nn.browser.isFirefox() ? [224] : []), KE = "data-mce-placeholder", YE = e => "keydown" === e.type || "keyup" === e.type, XE = e => { const t = e.keyCode; return t === wg.BACKSPACE || t === wg.DELETE; }, GE = (e, t) => ({ from: e, to: t }), ZE = (e, t) => { const n = mn(e), o = mn(t.container()); return Gh(n, o).map((e => ((e, t) => ({ block: e, position: t }))(e, t))); }, QE = (e, t) => tr(t, (e => Ai(e) || ds(e.dom)), (t => Cn(t, e))).filter(Nn).getOr(e), JE = (e, t) => { const n = ((e, t) => { const n = Hn(e); return J(n, (e => t.isBlock(xn(e)))).fold(N(n), (e => n.slice(0, e))); })(e, t); return q(n, Eo), n; }, ex = (e, t, n) => { const o = Qp(n, t); return Q(o.reverse(), (t => xs(e, t))).each(Eo); }, tx = (e, t, n, o, r) => { if (xs(o, n))
        return Bi(n), km(n.dom); ((e, t) => 0 === Y(zn(t), (t => !xs(e, t))).length)(o, r) && xs(o, t) && ao(r, cn("br")); const s = Sm(n.dom, Pl.before(r.dom)); return q(JE(t, o), (e => { ao(r, e); })), ex(o, e, t), s; }, nx = (e, t, n, o) => { if (xs(o, n)) {
        if (xs(o, t)) {
            const e = e => { const t = (e, n) => Vn(e).fold((() => n), (e => ((e, t) => e.isInline(xn(t)))(o, e) ? t(e, n.concat(So(e))) : n)); return t(e, []); }, r = X(e(n), ((e, t) => (uo(e, t), t)), Oi());
            wo(t), co(t, r);
        }
        return Eo(n), km(t.dom);
    } const r = Nm(n.dom); return q(JE(t, o), (e => { co(n, e); })), ex(o, e, t), r; }, ox = (e, t) => { xm(e, t.dom).bind((e => I.from(e.getNode()))).map(mn).filter(_i).each(Eo); }, rx = (e, t, n, o) => (ox(!0, t), ox(!1, n), ((e, t) => wn(t, e) ? ((e, t) => { const n = Qp(t, e); return I.from(n[n.length - 1]); })(t, e) : I.none())(t, n).fold(T(nx, e, t, n, o), T(tx, e, t, n, o))), sx = (e, t, n, o, r) => t ? rx(e, o, n, r) : rx(e, n, o, r), ax = (e, t) => { const n = mn(e.getBody()), o = ((e, t, n, o) => o.collapsed ? ((e, t, n, o) => { const r = ZE(t, Pl.fromRangeStart(o)), s = r.bind((o => Cm(n, t, o.position).bind((o => ZE(t, o).map((o => ((e, t, n, o) => as(o.position.getNode()) && !xs(e, o.block) ? xm(!1, o.block.dom).bind((e => e.isEqual(o.position) ? Cm(n, t, e).bind((e => ZE(t, e))) : I.some(o))).getOr(o) : o)(e, t, n, o))))))); return ze(r, s, GE).filter((e => (e => !Cn(e.from.block, e.to.block))(e) && ((e, t) => { const n = mn(e); return Cn(QE(n, t.from.block), QE(n, t.to.block)); })(t, e) && (e => !1 === cs(e.from.block.dom) && !1 === cs(e.to.block.dom))(e) && (e => { const t = e => Si(e) || Fs(e.dom) || Ni(e); return t(e.from.block) && t(e.to.block); })(e) && (e => !(wn(e.to.block, e.from.block) || wn(e.from.block, e.to.block)))(e))); })(e, t, n, o) : I.none())(e.schema, n.dom, t, e.selection.getRng()).map((o => () => { sx(n, t, o.from.block, o.to.block, e.schema).each((t => { e.selection.setRng(t.toRange()); })); })); return o; }, ix = (e, t) => { const n = mn(t), o = T(Cn, e); return er(n, Ai, o).isSome(); }, lx = e => { const t = mn(e.getBody()); return ((e, t) => { const n = Sm(e.dom, Pl.fromRangeStart(t)).isNone(), o = _m(e.dom, Pl.fromRangeEnd(t)).isNone(); return !((e, t) => ix(e, t.startContainer) || ix(e, t.endContainer))(e, t) && n && o; })(t, e.selection.getRng()) ? (e => I.some((() => { e.setContent(""), e.selection.setCursorLocation(); })))(e) : ((e, t, n) => { const o = t.getRng(); return ze(Gh(e, mn(o.startContainer)), Gh(e, mn(o.endContainer)), ((r, s) => Cn(r, s) ? I.none() : I.some((() => { o.deleteContents(), sx(e, !0, r, s, n).each((e => { t.setRng(e.toRange()); })); })))).getOr(I.none()); })(t, e.selection, e.schema); }, dx = (e, t) => e.selection.isCollapsed() ? I.none() : lx(e), cx = (e, t, n, o, r) => I.from(t._selectionOverrides.showCaret(e, n, o, r)), ux = (e, t) => e.dispatch("BeforeObjectSelected", { target: t }).isDefaultPrevented() ? I.none() : I.some((e => { const t = e.ownerDocument.createRange(); return t.selectNode(e), t; })(t)), mx = (e, t, n) => t.collapsed ? ((e, t, n) => { const o = em(1, e.getBody(), t), r = Pl.fromRangeStart(o), s = r.getNode(); if (Ou(s))
        return cx(1, e, s, !r.isAtEnd(), !1); const a = r.getNode(!0); if (Ou(a))
        return cx(1, e, a, !1, !1); const i = Nb(e.dom.getRoot(), r.getNode()); return Ou(i) ? cx(1, e, i, !1, n) : I.none(); })(e, t, n).getOr(t) : t, fx = e => Xp(e) || qp(e), gx = e => Gp(e) || Wp(e), px = (e, t, n, o, r, s) => { cx(o, e, s.getNode(!r), r, !0).each((n => { if (t.collapsed) {
        const e = t.cloneRange();
        r ? e.setEnd(n.startContainer, n.startOffset) : e.setStart(n.endContainer, n.endOffset), e.deleteContents();
    }
    else
        t.deleteContents(); e.selection.setRng(n); })), ((e, t) => { es(t) && 0 === t.data.length && e.remove(t); })(e.dom, n); }, hx = (e, t) => ((e, t) => { const n = e.selection.getRng(); if (!es(n.commonAncestorContainer))
        return I.none(); const o = t ? 1 : -1, r = hm(e.getBody()), s = T(rm, t ? r.next : r.prev), a = t ? fx : gx, i = nm(o, e.getBody(), n), l = s(i), d = l ? $h(t, l) : l; if (!d || !sm(i, d))
        return I.none(); if (a(d))
        return I.some((() => px(e, n, i.getNode(), o, t, d))); const c = s(d); return c && a(c) && sm(d, c) ? I.some((() => px(e, n, i.getNode(), o, t, c))) : I.none(); })(e, t), bx = (e, t) => { const n = e.getBody(); return t ? km(n).filter(Xp) : Nm(n).filter(Gp); }, vx = e => { const t = e.selection.getRng(); return !t.collapsed && (bx(e, !0).exists((e => e.isEqual(Pl.fromRangeStart(t)))) || bx(e, !1).exists((e => e.isEqual(Pl.fromRangeEnd(t))))); }, yx = ke([{ remove: ["element"] }, { moveToElement: ["element"] }, { moveToPosition: ["position"] }]), Cx = (e, t, n, o) => Cm(t, e, n).bind((r => { return s = r.getNode(), C(s) && (Ai(mn(s)) || Ni(mn(s))) || ((e, t, n, o, r) => { const s = t => r.isInline(t.nodeName.toLowerCase()) && !Ku(n, o, e); return tm(!t, n).fold((() => tm(t, o).fold(L, s)), s); })(e, t, n, r, o) ? I.none() : t && cs(r.getNode()) || !t && cs(r.getNode(!0)) ? ((e, t, n, o, r) => { const s = r.getNode(!n); return Gh(mn(t), mn(o.getNode())).map((t => xs(e, t) ? yx.remove(t.dom) : yx.moveToElement(s))).orThunk((() => I.some(yx.moveToElement(s)))); })(o, e, t, n, r) : t && Gp(n) || !t && Xp(n) ? I.some(yx.moveToPosition(r)) : I.none(); var s; })), wx = (e, t) => I.from(Nb(e.getBody(), t)), Ex = (e, t) => { const n = e.selection.getNode(); return wx(e, n).filter(cs).fold((() => ((e, t, n, o) => { const r = em(t ? 1 : -1, e, n), s = Pl.fromRangeStart(r), a = mn(e); return !t && Gp(s) ? I.some(yx.remove(s.getNode(!0))) : t && Xp(s) ? I.some(yx.remove(s.getNode())) : !t && Xp(s) && ch(a, s, o) ? uh(a, s, o).map((e => yx.remove(e.getNode()))) : t && Gp(s) && dh(a, s, o) ? mh(a, s, o).map((e => yx.remove(e.getNode()))) : ((e, t, n, o) => ((e, t) => { const n = t.getNode(!e), o = e ? "after" : "before"; return qr(n) && n.getAttribute("data-mce-caret") === o; })(t, n) ? ((e, t) => y(t) ? I.none() : e && cs(t.nextSibling) ? I.some(yx.moveToElement(t.nextSibling)) : !e && cs(t.previousSibling) ? I.some(yx.moveToElement(t.previousSibling)) : I.none())(t, n.getNode(!t)).orThunk((() => Cx(e, t, n, o))) : Cx(e, t, n, o).bind((t => ((e, t, n) => n.fold((e => I.some(yx.remove(e))), (e => I.some(yx.moveToElement(e))), (n => Ku(t, n, e) ? I.none() : I.some(yx.moveToPosition(n)))))(e, n, t))))(e, t, s, o); })(e.getBody(), t, e.selection.getRng(), e.schema).map((n => () => n.fold(((e, t) => n => (e._selectionOverrides.hideFakeCaret(), Fh(e, t, mn(n)), !0))(e, t), ((e, t) => n => { const o = t ? Pl.before(n) : Pl.after(n); return e.selection.setRng(o.toRange()), !0; })(e, t), (e => t => (e.selection.setRng(t.toRange()), !0))(e))))), (() => I.some(_))); }, xx = e => { const t = e.dom, n = e.selection, o = Nb(e.getBody(), n.getNode()); if (ds(o) && t.isBlock(o) && t.isEmpty(o)) {
        const e = t.create("br", { "data-mce-bogus": "1" });
        t.setHTML(o, ""), o.appendChild(e), n.setRng(Pl.before(e).toRange());
    } return !0; }, _x = (e, t) => e.selection.isCollapsed() ? Ex(e, t) : ((e, t) => { const n = e.selection.getNode(); return cs(n) && !ms(n) ? wx(e, n.parentNode).filter(cs).fold((() => I.some((() => { var n; n = mn(e.getBody()), q(vr(n, ".mce-offscreen-selection"), Eo), Fh(e, t, mn(e.selection.getNode())), Zh(e); }))), (() => I.some(_))) : vx(e) ? I.some((() => { eb(e, e.selection.getRng(), mn(e.getBody())); })) : I.none(); })(e, t), Sx = e => e.hasOwnProperty("text"), kx = e => e.hasOwnProperty("marker"), Nx = (e, t) => { const n = (e, n) => { if (es(e))
        return { text: e, offset: n }; {
        const o = t(), r = e.childNodes;
        return n < r.length ? (e.insertBefore(o, r[n]), { marker: o, before: !0 }) : (e.appendChild(o), { marker: o, before: !1 });
    } }, o = n(e.endContainer, e.endOffset); return { start: n(e.startContainer, e.startOffset), end: o }; }, Rx = e => { var t, n; const { start: o, end: r } = e, s = new window.Range; return Sx(o) ? s.setStart(o.text, o.offset) : kx(o) && (o.before ? s.setStartBefore(o.marker) : s.setStartAfter(o.marker), null === (t = o.marker.parentNode) || void 0 === t || t.removeChild(o.marker)), Sx(r) ? s.setEnd(r.text, r.offset) : kx(r) && (r.before ? s.setEndBefore(r.marker) : s.setEndAfter(r.marker), null === (n = r.marker.parentNode) || void 0 === n || n.removeChild(r.marker)), s; }, Ax = (e, t) => { var n; const o = e.dom, r = o.getParent(e.selection.getStart(), o.isBlock), s = o.getParent(e.selection.getEnd(), o.isBlock), a = e.getBody(); if ("div" === (null === (n = null == r ? void 0 : r.nodeName) || void 0 === n ? void 0 : n.toLowerCase()) && r && s && r === a.firstChild && s === a.lastChild && !o.isEmpty(a)) {
        const n = r.cloneNode(!1), o = () => { if (t ? Yh(e) : Kh(e), a.firstChild !== r) {
            const t = Nx(e.selection.getRng(), (() => document.createElement("span")));
            Array.from(a.childNodes).forEach((e => n.appendChild(e))), a.appendChild(n), e.selection.setRng(Rx(t));
        } };
        return I.some(o);
    } return I.none(); }, Tx = (e, t) => e.selection.isCollapsed() ? ((e, t) => { const n = Pl.fromRangeStart(e.selection.getRng()); return Cm(t, e.getBody(), n).filter((e => t ? $p(e) : Vp(e))).bind((e => Yu(t ? 0 : -1, e))).map((t => () => e.selection.select(t))); })(e, t) : I.none(), Ox = es, Bx = e => Ox(e) && e.data[0] === Pi, Px = e => Ox(e) && e.data[e.data.length - 1] === Pi, Dx = e => { var t; return (null !== (t = e.ownerDocument) && void 0 !== t ? t : document).createTextNode(Pi); }, Lx = (e, t) => e ? (e => { var t; if (Ox(e.previousSibling))
        return Px(e.previousSibling) || e.previousSibling.appendData(Pi), e.previousSibling; if (Ox(e))
        return Bx(e) || e.insertData(0, Pi), e; {
        const n = Dx(e);
        return null === (t = e.parentNode) || void 0 === t || t.insertBefore(n, e), n;
    } })(t) : (e => { var t, n; if (Ox(e.nextSibling))
        return Bx(e.nextSibling) || e.nextSibling.insertData(0, Pi), e.nextSibling; if (Ox(e))
        return Px(e) || e.appendData(Pi), e; {
        const o = Dx(e);
        return e.nextSibling ? null === (t = e.parentNode) || void 0 === t || t.insertBefore(o, e.nextSibling) : null === (n = e.parentNode) || void 0 === n || n.appendChild(o), o;
    } })(t), Mx = T(Lx, !0), Ix = T(Lx, !1), Fx = (e, t) => es(e.container()) ? Lx(t, e.container()) : Lx(t, e.getNode()), Ux = (e, t) => { const n = t.get(); return n && e.container() === n && Ui(n); }, zx = (e, t) => t.fold((t => { _u(e.get()); const n = Mx(t); return e.set(n), I.some(Pl(n, n.length - 1)); }), (t => km(t).map((t => { if (Ux(t, e)) {
        const t = e.get();
        return Pl(t, 1);
    } {
        _u(e.get());
        const n = Fx(t, !0);
        return e.set(n), Pl(n, 1);
    } }))), (t => Nm(t).map((t => { if (Ux(t, e)) {
        const t = e.get();
        return Pl(t, t.length - 1);
    } {
        _u(e.get());
        const n = Fx(t, !1);
        return e.set(n), Pl(n, n.length - 1);
    } }))), (t => { _u(e.get()); const n = Ix(t); return e.set(n), I.some(Pl(n, 1)); })), jx = (e, t) => { for (let n = 0; n < e.length; n++) {
        const o = e[n].apply(null, t);
        if (o.isSome())
            return o;
    } return I.none(); }, Hx = ke([{ before: ["element"] }, { start: ["element"] }, { end: ["element"] }, { after: ["element"] }]), $x = (e, t) => Wu(t, e) || e, Vx = (e, t, n) => { const o = Vh(n), r = $x(t, o.container()); return Hh(e, r, o).fold((() => _m(r, o).bind(T(Hh, e, r)).map((e => Hx.before(e)))), I.none); }, qx = (e, t) => null === Tm(e, t), Wx = (e, t, n) => Hh(e, t, n).filter(T(qx, t)), Kx = (e, t, n) => { const o = qh(n); return Wx(e, t, o).bind((e => Sm(e, o).isNone() ? I.some(Hx.start(e)) : I.none())); }, Yx = (e, t, n) => { const o = Vh(n); return Wx(e, t, o).bind((e => _m(e, o).isNone() ? I.some(Hx.end(e)) : I.none())); }, Xx = (e, t, n) => { const o = qh(n), r = $x(t, o.container()); return Hh(e, r, o).fold((() => Sm(r, o).bind(T(Hh, e, r)).map((e => Hx.after(e)))), I.none); }, Gx = e => !jh(Qx(e)), Zx = (e, t, n) => jx([Vx, Kx, Yx, Xx], [e, t, n]).filter(Gx), Qx = e => e.fold(R, R, R, R), Jx = e => e.fold(N("before"), N("start"), N("end"), N("after")), e_ = e => e.fold(Hx.before, Hx.before, Hx.after, Hx.after), t_ = e => e.fold(Hx.start, Hx.start, Hx.end, Hx.end), n_ = (e, t, n, o, r, s) => ze(Hh(t, n, o), Hh(t, n, r), ((t, o) => t !== o && ((e, t, n) => { const o = Wu(t, e), r = Wu(n, e); return C(o) && o === r; })(n, t, o) ? Hx.after(e ? t : o) : s)).getOr(s), o_ = (e, t) => e.fold(M, (e => { return o = t, !(Jx(n = e) === Jx(o) && Qx(n) === Qx(o)); var n, o; })), r_ = (e, t) => e ? t.fold(S(I.some, Hx.start), I.none, S(I.some, Hx.after), I.none) : t.fold(I.none, S(I.some, Hx.before), I.none, S(I.some, Hx.end)), s_ = (e, t, n) => { const o = e ? 1 : -1; return t.setRng(Pl(n.container(), n.offset() + o).toRange()), t.getSel().modify("move", e ? "forward" : "backward", "word"), !0; };
    var a_;
    !function (e) { e[e.Br = 0] = "Br", e[e.Block = 1] = "Block", e[e.Wrap = 2] = "Wrap", e[e.Eol = 3] = "Eol"; }(a_ || (a_ = {}));
    const i_ = (e, t) => -1 === e ? oe(t) : t, l_ = (e, t, n) => 1 === e ? t.next(n) : t.prev(n), d_ = (e, t, n, o) => as(o.getNode(1 === t)) ? a_.Br : !1 === Ku(n, o) ? a_.Block : a_.Wrap, c_ = (e, t, n, o) => { const r = hm(n); let s = o; const a = []; for (; s;) {
        const n = l_(t, r, s);
        if (!n)
            break;
        if (as(n.getNode(!1)))
            return 1 === t ? { positions: i_(t, a).concat([n]), breakType: a_.Br, breakAt: I.some(n) } : { positions: i_(t, a), breakType: a_.Br, breakAt: I.some(n) };
        if (n.isVisible()) {
            if (e(s, n)) {
                const e = d_(0, t, s, n);
                return { positions: i_(t, a), breakType: e, breakAt: I.some(n) };
            }
            a.push(n), s = n;
        }
        else
            s = n;
    } return { positions: i_(t, a), breakType: a_.Eol, breakAt: I.none() }; }, u_ = (e, t, n, o) => t(n, o).breakAt.map((o => { const r = t(n, o).positions; return -1 === e ? r.concat(o) : [o].concat(r); })).getOr([]), m_ = (e, t) => G(e, ((e, n) => e.fold((() => I.some(n)), (o => ze(le(o.getClientRects()), le(n.getClientRects()), ((e, r) => { const s = Math.abs(t - e.left); return Math.abs(t - r.left) <= s ? n : o; })).or(e)))), I.none()), f_ = (e, t) => le(t.getClientRects()).bind((t => m_(e, t.left))), g_ = T(c_, Pl.isAbove, -1), p_ = T(c_, Pl.isBelow, 1), h_ = T(u_, -1, g_), b_ = T(u_, 1, p_), v_ = (e, t) => g_(e, t).breakAt.isNone(), y_ = (e, t) => p_(e, t).breakAt.isNone(), C_ = (e, t) => f_(h_(e, t), t), w_ = (e, t) => f_(b_(e, t), t), E_ = cs, x_ = (e, t) => Math.abs(e.left - t), __ = (e, t) => Math.abs(e.right - t), S_ = (e, t) => pt(e, ((e, n) => { const o = Math.min(x_(e, t), __(e, t)), r = Math.min(x_(n, t), __(n, t)); return r === o && Se(n, "node") && E_(n.node) || r < o ? n : e; })), k_ = e => { const t = t => V(t, (t => { const n = Xi(t); return n.node = e, n; })); if (qr(e))
        return t(e.getClientRects()); if (es(e)) {
        const n = e.ownerDocument.createRange();
        return n.setStart(e, 0), n.setEnd(e, e.data.length), t(n.getClientRects());
    } return []; }, N_ = e => te(e, k_);
    var R_;
    !function (e) { e[e.Up = -1] = "Up", e[e.Down = 1] = "Down"; }(R_ || (R_ = {}));
    const A_ = (e, t, n, o, r, s) => { let a = 0; const i = [], l = o => { let s = N_([o]); e === R_.Up && (s = s.reverse()); for (let e = 0; e < s.length; e++) {
        const o = s[e];
        if (!n(o, d)) {
            if (i.length > 0 && t(o, bt(i)) && a++, o.line = a, r(o))
                return !0;
            i.push(o);
        }
    } return !1; }, d = bt(s.getClientRects()); if (!d)
        return i; const c = s.getNode(); return c && (l(c), ((e, t, n, o) => { let r = o; for (; r = qu(r, e, pl, t);)
        if (n(r))
            return; })(e, o, l, c)), i; }, T_ = T(A_, R_.Up, Qi, Ji), O_ = T(A_, R_.Down, Ji, Qi), B_ = e => bt(e.getClientRects()), P_ = e => t => ((e, t) => t.line > e)(e, t), D_ = e => t => ((e, t) => t.line === e)(e, t), L_ = (e, t) => { e.selection.setRng(t), ep(e, e.selection.getRng()); }, M_ = (e, t, n) => I.some(mx(e, t, n)), I_ = (e, t, n, o, r, s) => { const a = 1 === t, i = hm(e.getBody()), l = T(rm, a ? i.next : i.prev), d = a ? o : r; if (!n.collapsed) {
        const o = tl(n);
        if (s(o))
            return cx(t, e, o, -1 === t, !1);
        if (vx(e)) {
            const e = n.cloneRange();
            return e.collapse(-1 === t), I.from(e);
        }
    } const c = nm(t, e.getBody(), n); if (d(c))
        return ux(e, c.getNode(!a)); let u = l(c); const m = Ki(n); if (!u)
        return m ? I.some(n) : I.none(); if (u = $h(a, u), d(u))
        return cx(t, e, u.getNode(!a), a, !1); const f = l(u); return f && d(f) && sm(u, f) ? cx(t, e, f.getNode(!a), a, !1) : m ? M_(e, u.toRange(), !1) : I.none(); }, F_ = (e, t, n, o, r, s) => { const a = nm(t, e.getBody(), n), i = bt(a.getClientRects()), l = t === R_.Down, d = e.getBody(); if (!i)
        return I.none(); if (vx(e)) {
        const e = l ? Pl.fromRangeEnd(n) : Pl.fromRangeStart(n);
        return (l ? w_ : C_)(d, e).orThunk((() => I.from(e))).map((e => e.toRange()));
    } const c = (l ? O_ : T_)(d, P_(1), a), u = Y(c, D_(1)), m = i.left, f = S_(u, m); if (f && s(f.node)) {
        const n = Math.abs(m - f.left), o = Math.abs(m - f.right);
        return cx(t, e, f.node, n < o, !1);
    } let g; if (g = o(a) ? a.getNode() : r(a) ? a.getNode(!0) : tl(n), g) {
        const n = ((e, t, n, o) => { const r = hm(t); let s, a, i, l; const d = []; let c = 0; e === R_.Down ? (s = r.next, a = Ji, i = Qi, l = Pl.after(o)) : (s = r.prev, a = Qi, i = Ji, l = Pl.before(o)); const u = B_(l); do {
            if (!l.isVisible())
                continue;
            const e = B_(l);
            if (i(e, u))
                continue;
            d.length > 0 && a(e, bt(d)) && c++;
            const t = Xi(e);
            if (t.position = l, t.line = c, n(t))
                return d;
            d.push(t);
        } while (l = s(l)); return d; })(t, d, P_(1), g);
        let o = S_(Y(n, D_(1)), m);
        if (o)
            return M_(e, o.position.toRange(), !1);
        if (o = bt(Y(n, D_(0))), o)
            return M_(e, o.position.toRange(), !1);
    } return 0 === u.length ? U_(e, l).filter(l ? r : o).map((t => mx(e, t.toRange(), !1))) : I.none(); }, U_ = (e, t) => { const n = e.selection.getRng(), o = t ? Pl.fromRangeEnd(n) : Pl.fromRangeStart(n), r = (s = o.container(), a = e.getBody(), er(mn(s), (e => Pu(e.dom)), (e => e.dom === a)).map((e => e.dom)).getOr(a)); var s, a; if (t) {
        const e = p_(r, o);
        return de(e.positions);
    } {
        const e = g_(r, o);
        return le(e.positions);
    } }, z_ = (e, t, n) => U_(e, t).filter(n).exists((t => (e.selection.setRng(t.toRange()), !0))), j_ = (e, t) => { const n = e.dom.createRng(); n.setStart(t.container(), t.offset()), n.setEnd(t.container(), t.offset()), e.selection.setRng(n); }, H_ = (e, t) => { e ? t.setAttribute("data-mce-selected", "inline-boundary") : t.removeAttribute("data-mce-selected"); }, $_ = (e, t, n) => zx(t, n).map((t => (j_(e, t), n))), V_ = (e, t, n) => { const o = e.getBody(), r = ((e, t, n) => { const o = Pl.fromRangeStart(e); if (e.collapsed)
        return o; {
        const r = Pl.fromRangeEnd(e);
        return n ? Sm(t, r).getOr(r) : _m(t, o).getOr(o);
    } })(e.selection.getRng(), o, n); return ((e, t, n, o) => { const r = $h(e, o), s = Zx(t, n, r); return Zx(t, n, r).bind(T(r_, e)).orThunk((() => ((e, t, n, o, r) => { const s = $h(e, r); return Cm(e, n, s).map(T($h, e)).fold((() => o.map(e_)), (r => Zx(t, n, r).map(T(n_, e, t, n, s, r)).filter(T(o_, o)))).filter(Gx); })(e, t, n, s, o))); })(n, T(zh, e), o, r).bind((n => $_(e, t, n))); }, q_ = (e, t, n) => !!uc(e) && V_(e, t, n).isSome(), W_ = (e, t, n) => !!uc(t) && ((e, t) => { const n = t.selection.getRng(), o = e ? Pl.fromRangeEnd(n) : Pl.fromRangeStart(n); return !!(e => w(e.selection.getSel().modify))(t) && (e && Hi(o) ? s_(!0, t.selection, o) : !(e || !$i(o)) && s_(!1, t.selection, o)); })(e, t), K_ = e => { const t = Ne(null), n = T(zh, e); return e.on("NodeChange", (o => { uc(e) && (((e, t, n) => { const o = V(vr(mn(t.getRoot()), '*[data-mce-selected="inline-boundary"]'), (e => e.dom)), r = Y(o, e), s = Y(n, e); q(re(r, s), T(H_, !1)), q(re(s, r), T(H_, !0)); })(n, e.dom, o.parents), ((e, t) => { const n = t.get(); if (e.selection.isCollapsed() && !e.composing && n) {
        const o = Pl.fromRangeStart(e.selection.getRng());
        Pl.isTextPosition(o) && !(e => Hi(e) || $i(e))(o) && (j_(e, xu(n, o)), t.set(null));
    } })(e, t), ((e, t, n, o) => { if (t.selection.isCollapsed()) {
        const r = Y(o, e);
        q(r, (o => { const r = Pl.fromRangeStart(t.selection.getRng()); Zx(e, t.getBody(), r).bind((e => $_(t, n, e))); }));
    } })(n, e, t, o.parents)); })), t; }, Y_ = T(W_, !0), X_ = T(W_, !1), G_ = (e, t, n) => { if (uc(e)) {
        const o = U_(e, t).getOrThunk((() => { const n = e.selection.getRng(); return t ? Pl.fromRangeEnd(n) : Pl.fromRangeStart(n); }));
        return Zx(T(zh, e), e.getBody(), o).exists((t => { const o = e_(t); return zx(n, o).exists((t => (j_(e, t), !0))); }));
    } return !1; }, Z_ = (e, t) => n => zx(t, n).map((t => () => j_(e, t))), Q_ = (e, t, n, o) => { const r = e.getBody(), s = T(zh, e); e.undoManager.ignore((() => { e.selection.setRng(((e, t) => { const n = document.createRange(); return n.setStart(e.container(), e.offset()), n.setEnd(t.container(), t.offset()), n; })(n, o)), Kh(e), Zx(s, r, Pl.fromRangeStart(e.selection.getRng())).map(t_).bind(Z_(e, t)).each(D); })), e.nodeChanged(); }, J_ = (e, t, n) => { if (e.selection.isCollapsed() && uc(e)) {
        const o = Pl.fromRangeStart(e.selection.getRng());
        return ((e, t, n, o) => { const r = ((e, t) => Wu(t, e) || e)(e.getBody(), o.container()), s = T(zh, e), a = Zx(s, r, o); return a.bind((e => n ? e.fold(N(I.some(t_(e))), I.none, N(I.some(e_(e))), I.none) : e.fold(I.none, N(I.some(e_(e))), I.none, N(I.some(t_(e)))))).map(Z_(e, t)).getOrThunk((() => { const i = wm(n, r, o), l = i.bind((e => Zx(s, r, e))); return ze(a, l, (() => Hh(s, r, o).bind((t => (e => ze(km(e), Nm(e), ((t, n) => { const o = $h(!0, t), r = $h(!1, n); return _m(e, o).forall((e => e.isEqual(r))); })).getOr(!0))(t) ? I.some((() => { Fh(e, n, mn(t)); })) : I.none())))).getOrThunk((() => l.bind((() => i.map((r => () => { n ? Q_(e, t, o, r) : Q_(e, t, r, o); })))))); })); })(e, t, n, o);
    } return I.none(); }, eS = (e, t) => { const n = mn(e.getBody()), o = mn(e.selection.getStart()), r = Qp(o, n); return J(r, t).fold(N(r), (e => r.slice(0, e))); }, tS = e => 1 === Wn(e), nS = (e, t) => { const n = T(wv, e); return te(t, (e => n(e) ? [e.dom] : [])); }, oS = e => { const t = (e => eS(e, (t => e.schema.isBlock(xn(t)))))(e); return nS(e, t); }, rS = (e, t) => { const n = Y((e => eS(e, (t => e.schema.isBlock(xn(t)) || (e => Wn(e) > 1)(t))))(e), tS); return de(n).bind((o => { const r = Pl.fromRangeStart(e.selection.getRng()); return Qh(t, r, o.dom) && !kf(o) ? I.some((() => ((e, t, n, o) => { const r = nS(t, o); if (0 === r.length)
        Fh(t, e, n);
    else {
        const e = Cv(n.dom, r);
        t.selection.setRng(e.toRange());
    } })(t, e, o, n))) : I.none(); })); }, sS = (e, t) => { const n = e.selection.getStart(), o = ((e, t) => { const n = t.parentElement; return as(t) && !h(n) && e.dom.isEmpty(n); })(e, n) || kf(mn(n)) ? Cv(n, t) : ((e, t) => { const { caretContainer: n, caretPosition: o } = yv(t); return e.insertNode(n.dom), o; })(e.selection.getRng(), t); e.selection.setRng(o.toRange()); }, aS = e => es(e.startContainer), iS = e => { const t = e.selection.getRng(); return (e => 0 === e.startOffset && aS(e))(t) && ((e, t) => { const n = t.startContainer.parentElement; return !h(n) && wv(e, mn(n)); })(e, t) && (e => (e => (e => { const t = e.startContainer.parentNode, n = e.endContainer.parentNode; return !h(t) && !h(n) && t.isEqualNode(n); })(e) && (e => { const t = e.endContainer; return e.endOffset === (es(t) ? t.length : t.childNodes.length); })(e))(e) || (e => !e.endContainer.isEqualNode(e.commonAncestorContainer))(e))(t); }, lS = (e, t) => e.selection.isCollapsed() ? rS(e, t) : (e => { if (iS(e)) {
        const t = oS(e);
        return I.some((() => { Kh(e), ((e, t) => { const n = re(t, oS(e)); n.length > 0 && sS(e, n); })(e, t); }));
    } return I.none(); })(e), dS = e => ((e => { const t = e.selection.getRng(); return t.collapsed && (aS(t) || e.dom.isEmpty(t.startContainer)) && !(e => { return t = mn(e.selection.getStart()), n = e.schema, yr(t, (e => Am(e.dom)), (e => n.isBlock(xn(e)))); var t, n; })(e); })(e) && sS(e, []), !0), cS = (e, t, n) => C(n) ? I.some((() => { e._selectionOverrides.hideFakeCaret(), Fh(e, t, mn(n)); })) : I.none(), uS = (e, t) => e.selection.isCollapsed() ? ((e, t) => { const n = t ? qp : Wp, o = nm(t ? 1 : -1, e.getBody(), e.selection.getRng()); return n(o) ? cS(e, t, o.getNode(!t)) : I.from($h(t, o)).filter((e => n(e) && sm(o, e))).bind((n => cS(e, t, n.getNode(!t)))); })(e, t) : ((e, t) => { const n = e.selection.getNode(); return gs(n) ? cS(e, t, n) : I.none(); })(e, t), mS = e => nt(null != e ? e : "").getOr(0), fS = (e, t) => (e || "table" === xn(t) ? "margin" : "padding") + ("rtl" === Lo(t, "direction") ? "-right" : "-left"), gS = e => { const t = hS(e); return !e.mode.isReadOnly() && (t.length > 1 || ((e, t) => ne(t, (t => { const n = fS(Xd(e), t), o = Io(t, n).map(mS).getOr(0); return "false" !== e.dom.getContentEditable(t.dom) && o > 0; })))(e, t)); }, pS = e => ki(e) || Ni(e), hS = e => Y(No(e.selection.getSelectedBlocks()), (e => !pS(e) && !(e => Ln(e).exists(pS))(e) && tr(e, (e => ds(e.dom) || cs(e.dom))).exists((e => ds(e.dom))))), bS = (e, t) => { var n, o; if (e.mode.isReadOnly())
        return; const { dom: r } = e, s = Gd(e), a = null !== (o = null === (n = /[a-z%]+$/i.exec(s)) || void 0 === n ? void 0 : n[0]) && void 0 !== o ? o : "px", i = mS(s), l = Xd(e); q(hS(e), (e => { ((e, t, n, o, r, s) => { const a = fS(n, mn(s)), i = mS(e.getStyle(s, a)); if ("outdent" === t) {
        const t = Math.max(0, i - o);
        e.setStyle(s, a, t ? t + r : "");
    }
    else {
        const t = i + o + r;
        e.setStyle(s, a, t);
    } })(r, t, l, i, a, e.dom); })); }, vS = e => bS(e, "outdent"), yS = e => { if (e.selection.isCollapsed() && gS(e)) {
        const t = e.dom, n = e.selection.getRng(), o = Pl.fromRangeStart(n), r = t.getParent(n.startContainer, t.isBlock);
        if (null !== r && oh(mn(r), o, e.schema))
            return I.some((() => vS(e)));
    } return I.none(); }, CS = (e, t, n) => ue([yS, _x, hx, (e, n) => J_(e, t, n), ax, kb, Tx, uS, dx, lS, Ax], (t => t(e, n))).filter((t => e.selection.isEditable())), wS = e => void 0 === e.touches || 1 !== e.touches.length ? I.none() : I.some(e.touches[0]), ES = (e, t) => _e(e, t.nodeName), xS = (e, t) => !!es(t) || !!qr(t) && !(ES(e.getBlockElements(), t) || Hm(t) || js(e, t) || Ns(t)), _S = (e, t) => { if (es(t)) {
        if (0 === t.data.length)
            return !0;
        if (/^\s+$/.test(t.data))
            return !t.nextSibling || ES(e, t.nextSibling) || Ns(t.nextSibling);
    } return !1; }, SS = e => e.dom.create(Ad(e), Td(e)), kS = (e, t, n) => { const o = mn(SS(e)), r = Oi(); co(o, r), n(t, o); const s = document.createRange(); return s.setStartBefore(r.dom), s.setEndBefore(r.dom), s; }, NS = e => t => -1 !== (" " + t.attr("class") + " ").indexOf(e), RS = (e, t, n) => function (o) { const r = arguments, s = r[r.length - 2], a = s > 0 ? t.charAt(s - 1) : ""; if ('"' === a)
        return o; if (">" === a) {
        const e = t.lastIndexOf("<", s);
        if (-1 !== e && -1 !== t.substring(e, s).indexOf('contenteditable="false"'))
            return o;
    } return '<span class="' + n + '" data-mce-content="' + e.dom.encode(r[0]) + '">' + e.dom.encode("string" == typeof r[1] ? r[1] : r[0]) + "</span>"; }, AS = (e, t) => ne(e, (e => { const n = t.match(e); return null !== n && n[0].length === t.length; })), TS = (e, t) => { t.hasAttribute("data-mce-caret") && (Wi(t), e.selection.setRng(e.selection.getRng()), e.selection.scrollIntoView(t)); }, OS = (e, t) => { const n = (e => rr(mn(e.getBody()), "*[data-mce-caret]").map((e => e.dom)).getOrNull())(e); if (n)
        return "compositionstart" === t.type ? (t.preventDefault(), t.stopPropagation(), void TS(e, n)) : void (ji(n) && (TS(e, n), e.undoManager.add())); }, BS = cs, PS = (e, t, n) => { const o = hm(e.getBody()), r = T(rm, 1 === t ? o.next : o.prev); if (n.collapsed) {
        const o = e.dom.getParent(n.startContainer, "PRE");
        if (!o)
            return;
        if (!r(Pl.fromRangeStart(n))) {
            const n = mn((e => { const t = e.dom.create(Ad(e)); return t.innerHTML = '<br data-mce-bogus="1">', t; })(e));
            1 === t ? io(mn(o), n) : ao(mn(o), n), e.selection.select(n.dom, !0), e.selection.collapse();
        }
    } }, DS = (e, t) => ((e, t) => { const n = t ? 1 : -1, o = e.selection.getRng(); return ((e, t, n) => I_(t, e, n, Xp, Gp, BS))(n, e, o).orThunk((() => (PS(e, n, o), I.none()))); })(e, ((e, t) => { const n = t ? e.getEnd(!0) : e.getStart(!0); return jh(n) ? !t : t; })(e.selection, t)).exists((t => (L_(e, t), !0))), LS = (e, t) => ((e, t) => { const n = t ? 1 : -1, o = e.selection.getRng(); return ((e, t, n) => F_(t, e, n, (e => Xp(e) || Kp(e)), (e => Gp(e) || Yp(e)), BS))(n, e, o).orThunk((() => (PS(e, n, o), I.none()))); })(e, t).exists((t => (L_(e, t), !0))), MS = (e, t) => z_(e, t, t ? Gp : Xp), IS = (e, t) => bx(e, !t).map((n => { const o = n.toRange(), r = e.selection.getRng(); return t ? o.setStart(r.startContainer, r.startOffset) : o.setEnd(r.endContainer, r.endOffset), o; })).exists((t => (L_(e, t), !0))), FS = e => H(["figcaption"], xn(e)), US = (e, t) => !!e.selection.isCollapsed() && ((e, t) => { const n = mn(e.getBody()), o = Pl.fromRangeStart(e.selection.getRng()); return ((e, t, n) => { const o = T(Cn, t); return tr(mn(e.container()), (e => n.isBlock(xn(e))), o).filter(FS); })(o, n, e.schema).exists((() => { if (((e, t, n) => t ? y_(e.dom, n) : v_(e.dom, n))(n, t, o)) {
        const o = kS(e, n, t ? co : lo);
        return e.selection.setRng(o), !0;
    } return !1; })); })(e, t), zS = (e, t) => ((e, t) => t ? I.from(e.dom.getParent(e.selection.getNode(), "details")).map((t => ((e, t) => { const n = e.selection.getRng(), o = Pl.fromRangeStart(n); return !(e.getBody().lastChild !== t || !y_(t, o) || (e.execCommand("InsertNewBlockAfter"), 0)); })(e, t))).getOr(!1) : I.from(e.dom.getParent(e.selection.getNode(), "summary")).bind((t => I.from(e.dom.getParent(t, "details")).map((n => ((e, t, n) => { const o = e.selection.getRng(), r = Pl.fromRangeStart(o); return !(e.getBody().firstChild !== t || !v_(n, r) || (e.execCommand("InsertNewBlockBefore"), 0)); })(e, n, t))))).getOr(!1))(e, t), jS = { shiftKey: !1, altKey: !1, ctrlKey: !1, metaKey: !1, keyCode: 0 }, HS = (e, t) => t.keyCode === e.keyCode && t.shiftKey === e.shiftKey && t.altKey === e.altKey && t.ctrlKey === e.ctrlKey && t.metaKey === e.metaKey, $S = (e, ...t) => () => e.apply(null, t), VS = (e, t) => Q(((e, t) => te((e => V(e, (e => ({ ...jS, ...e }))))(e), (e => HS(e, t) ? [e] : [])))(e, t), (e => e.action())), qS = (e, t) => ue(((e, t) => te((e => V(e, (e => ({ ...jS, ...e }))))(e), (e => HS(e, t) ? [e] : [])))(e, t), (e => e.action())), WS = (e, t) => { const n = t ? 1 : -1, o = e.selection.getRng(); return I_(e, n, o, qp, Wp, gs).exists((t => (L_(e, t), !0))); }, KS = (e, t) => { const n = t ? 1 : -1, o = e.selection.getRng(); return F_(e, n, o, qp, Wp, gs).exists((t => (L_(e, t), !0))); }, YS = (e, t) => z_(e, t, t ? Wp : qp), XS = (e, t, n) => te(Hn(e), (e => vn(e, t) ? n(e) ? [e] : [] : XS(e, t, n))), GS = (e, t) => sr(e, "table", t), ZS = ke([{ none: ["current"] }, { first: ["current"] }, { middle: ["current", "target"] }, { last: ["current"] }]), QS = { ...ZS, none: e => ZS.none(e) }, JS = (e, t, n, o, r = M) => { const s = 1 === o; if (!s && n <= 0)
        return QS.first(e[0]); if (s && n >= e.length - 1)
        return QS.last(e[e.length - 1]); {
        const s = n + o, a = e[s];
        return r(a) ? QS.middle(t, a) : JS(e, t, s, o, r);
    } }, ek = (e, t) => GS(e, t).bind((t => { const n = XS(t, "th,td", M); return J(n, (t => Cn(e, t))).map((e => ({ index: e, all: n }))); })), tk = De("image"), nk = De("event"), ok = e => t => { t[nk] = e; }, rk = ok(0), sk = ok(2), ak = ok(1), ik = e => { const t = e; return I.from(t[nk]).exists((e => 0 === e)); };
    const lk = De("mode"), dk = e => t => { t[lk] = e; }, ck = (e, t) => dk(t)(e), uk = dk(0), mk = dk(2), fk = dk(1), gk = e => t => { const n = t; return I.from(n[lk]).exists((t => t === e)); }, pk = gk(0), hk = gk(1), bk = ["none", "copy", "link", "move"], vk = ["none", "copy", "copyLink", "copyMove", "link", "linkMove", "move", "all", "uninitialized"], yk = () => { const e = new window.DataTransfer; let t = "move", n = "all"; const o = { get dropEffect() { return t; }, set dropEffect(e) { H(bk, e) && (t = e); }, get effectAllowed() { return n; }, set effectAllowed(e) { ik(o) && H(vk, e) && (n = e); }, get items() { return ((e, t) => ({ ...t, get length() { return t.length; }, add: (n, o) => { if (pk(e)) {
                if (!m(n))
                    return t.add(n);
                if (!v(o))
                    return t.add(n, o);
            } return null; }, remove: n => { pk(e) && t.remove(n); }, clear: () => { pk(e) && t.clear(); } }))(o, e.items); }, get files() { return hk(o) ? Object.freeze({ length: 0, item: e => null }) : e.files; }, get types() { return e.types; }, setDragImage: (t, n, r) => { var s; pk(o) && (s = { image: t, x: n, y: r }, o[tk] = s, e.setDragImage(t, n, r)); }, getData: t => hk(o) ? "" : e.getData(t), setData: (t, n) => { pk(o) && e.setData(t, n); }, clearData: t => { pk(o) && e.clearData(t); } }; return uk(o), o; }, Ck = (e, t) => e.setData("text/html", t), wk = (e, t, n, o, r) => { const s = vr(mn(n), "td,th,caption").map((e => e.dom)), a = Y(((e, t) => te(t, (t => { const n = ((e, t) => ({ left: e.left - t, top: e.top - t, right: e.right + -2, bottom: e.bottom + -2, width: e.width + t, height: e.height + t }))(Xi(t.getBoundingClientRect()), -1); return [{ x: n.left, y: e(n), cell: t }, { x: n.right, y: e(n), cell: t }]; })))(e, s), (e => t(e, r))); return ((e, t, n) => G(e, ((e, o) => e.fold((() => I.some(o)), (e => { const r = Math.sqrt(Math.abs(e.x - t) + Math.abs(e.y - n)), s = Math.sqrt(Math.abs(o.x - t) + Math.abs(o.y - n)); return I.some(s < r ? o : e); }))), I.none()))(a, o, r).map((e => e.cell)); }, Ek = T(wk, (e => e.bottom), ((e, t) => e.y < t)), xk = T(wk, (e => e.top), ((e, t) => e.y > t)), _k = (e, t, n) => { const o = e(t, n); return (e => e.breakType === a_.Wrap && 0 === e.positions.length)(o) || !as(n.getNode()) && (e => e.breakType === a_.Br && 1 === e.positions.length)(o) ? !((e, t, n) => n.breakAt.exists((n => e(t, n).breakAt.isSome())))(e, t, o) : o.breakAt.isNone(); }, Sk = T(_k, g_), kk = T(_k, p_), Nk = (e, t, n, o) => { const r = e.selection.getRng(), s = t ? 1 : -1; return !(!Tu() || !((e, t, n) => { const o = Pl.fromRangeStart(t); return xm(!e, n).exists((e => e.isEqual(o))); })(t, r, n) || (cx(s, e, n, !t, !1).each((t => { L_(e, t); })), 0)); }, Rk = (e, t, n) => { const o = ((e, t) => { const n = t.getNode(e); return Zr(n) ? I.some(n) : I.none(); })(!!t, n), r = !1 === t; o.fold((() => L_(e, n.toRange())), (o => xm(r, e.getBody()).filter((e => e.isEqual(n))).fold((() => L_(e, n.toRange())), (n => ((e, t, n) => { t.undoManager.transact((() => { const o = e ? io : ao, r = kS(t, mn(n), o); L_(t, r); })); })(t, e, o))))); }, Ak = (e, t, n, o) => { const r = e.selection.getRng(), s = Pl.fromRangeStart(r), a = e.getBody(); if (!t && Sk(o, s)) {
        const o = ((e, t, n) => ((e, t) => le(t.getClientRects()).bind((t => Ek(e, t.left, t.top))).bind((e => { return f_(Nm(n = e).map((e => g_(n, e).positions.concat(e))).getOr([]), t); var n; })))(t, n).orThunk((() => le(n.getClientRects()).bind((n => m_(h_(e, Pl.before(t)), n.left))))).getOr(Pl.before(t)))(a, n, s);
        return Rk(e, t, o), !0;
    } if (t && kk(o, s)) {
        const o = ((e, t, n) => ((e, t) => de(t.getClientRects()).bind((t => xk(e, t.left, t.top))).bind((e => { return f_(km(n = e).map((e => [e].concat(p_(n, e).positions))).getOr([]), t); var n; })))(t, n).orThunk((() => le(n.getClientRects()).bind((n => m_(b_(e, Pl.after(t)), n.left))))).getOr(Pl.after(t)))(a, n, s);
        return Rk(e, t, o), !0;
    } return !1; }, Tk = (e, t, n) => I.from(e.dom.getParent(e.selection.getNode(), "td,th")).bind((o => I.from(e.dom.getParent(o, "table")).map((r => n(e, t, r, o))))).getOr(!1), Ok = (e, t) => Tk(e, t, Nk), Bk = (e, t) => Tk(e, t, Ak), Pk = (e, t, n) => n.fold(I.none, I.none, ((e, t) => { return (n = t, nr(n, Sr)).map((e => (e => { const t = Tr.exact(e, 0, e, 0); return Dr(t); })(e))); var n; }), (n => e.mode.isReadOnly() || !Dk(n) ? I.none() : (e.execCommand("mceTableInsertRowAfter"), Lk(e, t, n)))), Dk = e => tr(e, On("table")).exists(gr), Lk = (e, t, n) => { return Pk(e, t, (r = Ik, ek(o = n, void 0).fold((() => QS.none(o)), (e => JS(e.all, o, e.index, 1, r))))); var o, r; }, Mk = (e, t, n) => { return Pk(e, t, (r = Ik, ek(o = n, void 0).fold((() => QS.none()), (e => JS(e.all, o, e.index, -1, r))))); var o, r; }, Ik = e => gr(e) || wr(e, Fk), Fk = e => kn(e) && gr(e), Uk = (e, t) => { const n = ["table", "li", "dl"], o = mn(e.getBody()), r = e => { const t = xn(e); return Cn(e, o) || H(n, t); }, s = e.selection.getRng(); return ((e, t) => ((e, t, n = L) => n(t) ? I.none() : H(e, xn(t)) ? I.some(t) : or(t, e.join(","), (e => vn(e, "table") || n(e))))(["td", "th"], e, t))(mn(t ? s.endContainer : s.startContainer), r).map((n => (GS(n, r).each((t => { e.model.table.clearSelectedCells(t.dom); })), e.selection.collapse(!t), (t ? Lk : Mk)(e, r, n).each((t => { e.selection.setRng(t); })), !0))).getOr(!1); }, zk = (e, t) => ({ container: e, offset: t }), jk = ni.DOM, Hk = e => t => e === t ? -1 : 0, $k = (e, t, n) => { if (es(e) && t >= 0)
        return I.some(zk(e, t)); {
        const o = Ei(jk);
        return I.from(o.backwards(e, t, Hk(e), n)).map((e => zk(e.container, e.container.data.length)));
    } }, Vk = (e, t, n) => { if (!es(e))
        return I.none(); const o = e.data; if (t >= 0 && t <= o.length)
        return I.some(zk(e, t)); {
        const o = Ei(jk);
        return I.from(o.backwards(e, t, Hk(e), n)).bind((e => { const o = e.container.data; return Vk(e.container, t + o.length, n); }));
    } }, qk = (e, t, n) => { if (!es(e))
        return I.none(); const o = e.data; if (t <= o.length)
        return I.some(zk(e, t)); {
        const r = Ei(jk);
        return I.from(r.forwards(e, t, Hk(e), n)).bind((e => qk(e.container, t - o.length, n)));
    } }, Wk = (e, t, n, o, r) => { const s = Ei(e, (e => t => e.isBlock(t) || H(["BR", "IMG", "HR", "INPUT"], t.nodeName) || "false" === e.getContentEditable(t))(e)); return I.from(s.backwards(t, n, o, r)); }, Kk = e => "" !== e && -1 !== " \xa0\ufeff\f\n\r\t\v".indexOf(e), Yk = (e, t) => e.substring(t.length), Xk = (e, t, n, o = !1) => { if (!(r = t).collapsed || !es(r.startContainer))
        return I.none(); var r; const s = { text: "", offset: 0 }, a = e.getParent(t.startContainer, e.isBlock) || e.getRoot(); return Wk(e, t.startContainer, t.startOffset, ((e, t, r) => (s.text = r + s.text, s.offset += t, ((e, t, n, o = !1) => { let r; const s = n.charAt(0); for (r = t - 1; r >= 0; r--) {
        const a = e.charAt(r);
        if (!o && Kk(a))
            return I.none();
        if (s === a && Ke(e, n, r, t))
            break;
    } return I.some(r); })(s.text, s.offset, n, o).getOr(t))), a).bind((e => { const o = t.cloneRange(); if (o.setStart(e.container, e.offset), o.setEnd(t.endContainer, t.endOffset), o.collapsed)
        return I.none(); const r = (e => Li(e.toString().replace(/\u00A0/g, " ")))(o); return 0 !== r.lastIndexOf(n) ? I.none() : I.some({ text: Yk(r, n), range: o, trigger: n }); })); }, Gk = e => { if ((e => 3 === e.nodeType)(e))
        return zk(e, e.data.length); {
        const t = e.childNodes;
        return t.length > 0 ? Gk(t[t.length - 1]) : zk(e, t.length);
    } }, Zk = (e, t) => { const n = e.childNodes; return n.length > 0 && t < n.length ? Zk(n[t], 0) : n.length > 0 && (e => 1 === e.nodeType)(e) && n.length === t ? Gk(n[n.length - 1]) : zk(e, t); }, Qk = (e, t, n, o = {}) => { var r; const s = t(), a = null !== (r = e.selection.getRng().startContainer.nodeValue) && void 0 !== r ? r : "", i = Y(s.lookupByTrigger(n.trigger), (t => n.text.length >= t.minChars && t.matches.getOrThunk((() => (e => t => { const n = Zk(t.startContainer, t.startOffset); return !((e, t) => { var n; const o = null !== (n = e.getParent(t.container, e.isBlock)) && void 0 !== n ? n : e.getRoot(); return Wk(e, t.container, t.offset, ((e, t) => 0 === t ? -1 : t), o).filter((e => { const t = e.container.data.charAt(e.offset - 1); return !Kk(t); })).isSome(); })(e, n); })(e.dom)))(n.range, a, n.text))); if (0 === i.length)
        return I.none(); const l = Promise.all(V(i, (e => e.fetch(n.text, e.maxResults, o).then((t => ({ matchText: n.text, items: t, columns: e.columns, onAction: e.onAction, highlightOn: e.highlightOn })))))); return I.some({ lookupData: l, context: n }); };
    var Jk;
    !function (e) { e[e.Error = 0] = "Error", e[e.Value = 1] = "Value"; }(Jk || (Jk = {}));
    const eN = (e, t, n) => e.stype === Jk.Error ? t(e.serror) : n(e.svalue), tN = e => ({ stype: Jk.Value, svalue: e }), nN = e => ({ stype: Jk.Error, serror: e }), oN = eN, rN = e => f(e) && fe(e).length > 100 ? " removed due to size" : JSON.stringify(e, null, 2), sN = (e, t) => nN([{ path: e, getErrorInfo: t }]), aN = e => ({ extract: (t, n) => { return o = e(n), r = e => ((e, t) => sN(e, N(t)))(t, e), o.stype === Jk.Error ? r(o.serror) : o; var o, r; }, toString: N("val") }), iN = aN(tN), lN = N(iN), dN = (e, t) => aN((n => { const o = typeof n; return e(n) ? tN(n) : nN(`Expected type: ${t} but got: ${o}`); })), cN = dN(E, "number"), uN = dN(m, "string"), mN = dN(b, "boolean"), fN = dN(w, "function"), gN = e => { if (Object(e) !== e)
        return !0; switch ({}.toString.call(e).slice(8, -1)) {
        case "Boolean":
        case "Number":
        case "String":
        case "Date":
        case "RegExp":
        case "Blob":
        case "FileList":
        case "ImageData":
        case "ImageBitmap":
        case "ArrayBuffer": return !0;
        case "Array":
        case "Object": return Object.keys(e).every((t => gN(e[t])));
        default: return !1;
    } };
    aN((e => gN(e) ? tN(e) : nN("Expected value to be acceptable for sending via postMessage")));
    const pN = e => ({ tag: "defaultedThunk", process: N(e) }), hN = (e, t, n) => { switch (e.tag) {
        case "field": return t(e.key, e.newKey, e.presence, e.prop);
        case "custom": return n(e.newKey, e.instantiator);
    } }, bN = e => { const t = (e => { const t = [], n = []; return q(e, (e => { eN(e, (e => n.push(e)), (e => t.push(e))); })), { values: t, errors: n }; })(e); return t.errors.length > 0 ? (n = t.errors, S(nN, ee)(n)) : tN(t.values); var n; }, vN = (e, t, n, o) => o(xe(e, t).getOrThunk((() => n(e)))), yN = (e, t, n, o, r) => { const s = e => r.extract(t.concat([o]), e), a = e => e.fold((() => tN(I.none())), (e => { const n = r.extract(t.concat([o]), e); return s = n, a = I.some, s.stype === Jk.Value ? { stype: Jk.Value, svalue: a(s.svalue) } : s; var s, a; })); switch (e.tag) {
        case "required": return ((e, t, n, o) => xe(t, n).fold((() => ((e, t, n) => sN(e, (() => 'Could not find valid *required* value for "' + t + '" in ' + rN(n))))(e, n, t)), o))(t, n, o, s);
        case "defaultedThunk": return vN(n, o, e.process, s);
        case "option": return ((e, t, n) => n(xe(e, t)))(n, o, a);
        case "defaultedOptionThunk": return ((e, t, n, o) => o(xe(e, t).map((t => !0 === t ? n(e) : t))))(n, o, e.process, a);
        case "mergeWithThunk": return vN(n, o, N({}), (t => { const o = Me(e.process(n), t); return s(o); }));
    } }, CN = e => ({ extract: (t, n) => ((e, t, n) => { const o = {}, r = []; for (const s of n)
            hN(s, ((n, s, a, i) => { const l = yN(a, e, t, n, i); oN(l, (e => { r.push(...e); }), (e => { o[s] = e; })); }), ((e, n) => { o[e] = n(t); })); return r.length > 0 ? nN(r) : tN(o); })(t, n, e), toString: () => { const t = V(e, (e => hN(e, ((e, t, n, o) => e + " -> " + o.toString()), ((e, t) => "state(" + e + ")")))); return "obj{\n" + t.join("\n") + "}"; } }), wN = (e, t, n) => { return o = ((e, t, n) => ((e, t) => e.stype === Jk.Error ? { stype: Jk.Error, serror: t(e.serror) } : e)(t.extract([e], n), (e => ({ input: n, errors: e }))))(e, t, n), eN(o, Te.error, Te.value); var o; }, EN = (e, t, n, o) => ({ tag: "field", key: e, newKey: t, presence: n, prop: o }), xN = (e, t) => EN(e, e, { tag: "required", process: {} }, t), _N = e => xN(e, uN), SN = e => xN(e, fN), kN = (e, t) => EN(e, e, { tag: "option", process: {} }, t), NN = e => kN(e, uN), RN = e => kN(e, fN), AN = (e, t) => EN(e, e, pN(t), lN()), TN = (e, t, n) => EN(e, e, pN(t), n), ON = (e, t) => TN(e, t, cN), BN = (e, t) => TN(e, t, mN), PN = (e, t) => TN(e, t, fN), DN = _N("type");
    _N("name"), _N("label"), _N("text"), _N("title"), _N("icon"), _N("url");
    const LN = _N("value"), MN = SN("fetch");
    SN("getSubmenuItems");
    const IN = SN("onAction");
    SN("onItemAction"), PN("onSetup", (() => _)), NN("name");
    const FN = NN("text");
    NN("role");
    const UN = NN("icon");
    NN("url"), NN("tooltip"), NN("label"), NN("shortcut"), RN("select");
    const zN = BN("active", !1);
    BN("borderless", !1);
    const jN = BN("enabled", !0);
    BN("primary", !1);
    const HN = AN("meta", {});
    PN("onAction", _), CN([(() => TN("type", "autocompleteitem", uN))(0, "autocompleteitem"), zN, jN, HN, LN, FN, UN]);
    const $N = CN([DN, _N("trigger"), ON("minChars", 1), AN("columns", 1), ON("maxResults", 10), RN("matches"), MN, IN, (VN = uN, TN("highlightOn", [], (qN = VN, { extract: (e, t) => { const n = V(t, ((t, n) => qN.extract(e.concat(["[" + n + "]"]), t))); return bN(n); }, toString: () => "array(" + qN.toString() + ")" })))]);
    var VN, qN;
    const WN = e => { const t = e.ui.registry.getAll().popups, n = he(t, (e => { return (t = e, wN("Autocompleter", $N, t)).fold((e => { throw new Error("Errors: \n" + (e => { const t = e.length > 10 ? e.slice(0, 10).concat([{ path: [], getErrorInfo: N("... (only showing first ten failures)") }]) : e; return V(t, (e => "Failed path: (" + e.path.join(" > ") + ")\n" + e.getErrorInfo())); })((t = e).errors).join("\n") + "\n\nInput object: " + rN(t.input)); var t; }), R); var t; })), o = dt(we(n, (e => e.trigger))), r = Ee(n); return { dataset: n, triggers: o, lookupByTrigger: e => Y(r, (t => t.trigger === e)) }; }, KN = e => { const t = Ve(), n = Ne(!1), o = t.isSet, r = () => { o() && ((e => { e.dispatch("AutocompleterEnd"); })(e), n.set(!1), t.clear()); }, s = st((() => WN(e))), a = a => { (n => t.get().map((t => Xk(e.dom, e.selection.getRng(), t.trigger, !0).bind((t => Qk(e, s, t, n))))).getOrThunk((() => ((e, t) => { const n = t(), o = e.selection.getRng(); return ((e, t, n) => ue(n.triggers, (n => Xk(e, t, n))))(e.dom, o, n).bind((n => Qk(e, t, n))); })(e, s))))(a).fold(r, (r => { (e => { o() || t.set({ trigger: e.trigger, matchLength: e.text.length }); })(r.context), r.lookupData.then((o => { t.get().map((s => { const a = r.context; s.trigger === a.trigger && (t.set({ ...s, matchLength: a.text.length }), n.get() ? (fd(e, { range: a.range }), ((e, t) => { e.dispatch("AutocompleterUpdate", t); })(e, { lookupData: o })) : (n.set(!0), fd(e, { range: a.range }), ((e, t) => { e.dispatch("AutocompleterStart", t); })(e, { lookupData: o }))); })); })); })); }, i = () => t.get().bind((({ trigger: t }) => { const o = e.selection.getRng(); return Xk(e.dom, o, t, n.get()).filter((({ range: e }) => ((e, t) => { const n = e.compareBoundaryPoints(window.Range.START_TO_START, t), o = e.compareBoundaryPoints(window.Range.END_TO_END, t); return n >= 0 && o <= 0; })(o, e))).map((({ range: e }) => e)); })); e.addCommand("mceAutocompleterReload", ((e, t) => { const n = f(t) ? t.fetchOptions : {}; a(n); })), e.addCommand("mceAutocompleterClose", r), e.addCommand("mceAutocompleterRefreshActiveRange", (() => { i().each((t => { fd(e, { range: t }); })); })), e.editorCommands.addQueryStateHandler("mceAutoCompleterInRange", (() => i().isSome())), ((e, t) => { const n = rt(t.load, 50); e.on("input", (t => { ("insertCompositionText" !== t.inputType || e.composing) && n.throttle(); })), e.on("keydown", (e => { const o = e.which; 8 === o ? n.throttle() : 27 === o ? (n.cancel(), t.cancelIfNecessary()) : 38 !== o && 40 !== o || n.cancel(); }), !0), e.on("remove", n.cancel); })(e, { cancelIfNecessary: r, load: a }); }, YN = Kt().browser.isSafari(), XN = e => Bi(mn(e)), GN = (e, t) => { var n; return 0 === e.startOffset && e.endOffset === (null === (n = t.textContent) || void 0 === n ? void 0 : n.length); }, ZN = (e, t) => I.from(e.getParent(t.container(), "details")), QN = (e, t) => ZN(e, t).isSome(), JN = (e, t) => { const n = t.getNode(); v(n) || e.selection.setCursorLocation(n, t.offset()); }, eR = (e, t, n) => { const o = e.dom.getParent(t.container(), "details"); if (o && !o.open) {
        const t = e.dom.select("summary", o)[0];
        t && (n ? km(t) : Nm(t)).each((t => JN(e, t)));
    }
    else
        JN(e, t); }, tR = (e, t, n) => { const { dom: o, selection: r } = e, s = e.getBody(); if ("character" === n) {
        const n = Pl.fromRangeStart(r.getRng()), a = o.getParent(n.container(), o.isBlock), i = ZN(o, n), l = a && o.isEmpty(a), d = h(null == a ? void 0 : a.previousSibling), c = h(null == a ? void 0 : a.nextSibling);
        return !!(l && (t ? c : d) && wm(!t, s, n).exists((e => QN(o, e) && !Ue(i, ZN(o, e))))) || wm(t, s, n).fold(L, (n => { const r = ZN(o, n); if (QN(o, n) && !Ue(i, r)) {
            if (t || eR(e, n, !1), a && l) {
                if (t && d)
                    return !0;
                if (!t && c)
                    return !0;
                eR(e, n, t), e.dom.remove(a);
            }
            return !0;
        } return !1; }));
    } return !1; }, nR = (e, t, n, o) => { const r = e.selection.getRng(), s = Pl.fromRangeStart(r), a = e.getBody(); return "selection" === o ? ((e, t) => { const n = t.startSummary.exists((t => t.contains(e.startContainer))), o = t.startSummary.exists((t => t.contains(e.endContainer))), r = t.startDetails.forall((e => t.endDetails.forall((t => e !== t)))); return (n || o) && !(n && o) || r; })(r, t) : n ? ((e, t) => t.startSummary.exists((t => ((e, t) => Nm(t).exists((n => as(n.getNode()) && Sm(t, n).exists((t => t.isEqual(e))) || n.isEqual(e))))(e, t))))(s, t) || ((e, t, n) => n.startDetails.exists((n => _m(e, t).forall((e => !n.contains(e.container()))))))(a, s, t) : ((e, t) => t.startSummary.exists((t => ((e, t) => km(t).exists((t => t.isEqual(e))))(e, t))))(s, t) || ((e, t) => t.startDetails.exists((n => Sm(n, e).forall((n => t.startSummary.exists((t => !t.contains(e.container()) && t.contains(n.container()))))))))(s, t); }, oR = (e, t, n) => ((e, t, n) => ((e, t) => { const n = I.from(e.getParent(t.startContainer, "details")), o = I.from(e.getParent(t.endContainer, "details")); if (n.isSome() || o.isSome()) {
        const t = n.bind((t => I.from(e.select("summary", t)[0])));
        return I.some({ startSummary: t, startDetails: n, endDetails: o });
    } return I.none(); })(e.dom, e.selection.getRng()).fold((() => tR(e, t, n)), (o => nR(e, o, t, n) || tR(e, t, n))))(e, t, n) || YN && ((e, t, n) => { const o = e.selection, r = o.getNode(), s = o.getRng(), a = Pl.fromRangeStart(s); return !!bs(r) && ("selection" === n && GN(s, r) || Qh(t, a, r) ? XN(r) : e.undoManager.transact((() => { const s = o.getSel(); let { anchorNode: a, anchorOffset: i, focusNode: l, focusOffset: d } = null != s ? s : {}; const c = () => { C(a) && C(i) && C(l) && C(d) && (null == s || s.setBaseAndExtent(a, i, l, d)); }, u = (e, t) => { q(e.childNodes, (e => { rf(e) && t.appendChild(e); })); }, m = e.dom.create("span", { "data-mce-bogus": "1" }); u(r, m), r.appendChild(m), c(), "word" !== n && "line" !== n || null == s || s.modify("extend", t ? "right" : "left", n), !o.isCollapsed() && GN(o.getRng(), m) ? XN(r) : (e.execCommand(t ? "ForwardDelete" : "Delete"), a = null == s ? void 0 : s.anchorNode, i = null == s ? void 0 : s.anchorOffset, l = null == s ? void 0 : s.focusNode, d = null == s ? void 0 : s.focusOffset, u(m, r), c()), e.dom.remove(m); })), !0); })(e, t, n) ? I.some(_) : I.none(), rR = e => (t, n, o = {}) => { const r = t.getBody(), s = { bubbles: !0, composed: !0, data: null, isComposing: !1, detail: 0, view: null, target: r, currentTarget: r, eventPhase: Event.AT_TARGET, originalTarget: r, explicitOriginalTarget: r, isTrusted: !1, srcElement: r, cancelable: !1, preventDefault: _, inputType: n }, a = Ua(new InputEvent(e)); return t.dispatch(e, { ...a, ...s, ...o }); }, sR = rR("input"), aR = rR("beforeinput"), iR = Kt(), lR = iR.os, dR = lR.isMacOS() || lR.isiOS(), cR = iR.browser.isFirefox(), uR = (e, t) => { const n = e.dom, o = e.schema.getMoveCaretBeforeOnEnterElements(); if (!t)
        return; if (/^(LI|DT|DD)$/.test(t.nodeName)) {
        const e = (e => { for (; e;) {
            if (qr(e) || es(e) && e.data && /[\r\n\s]/.test(e.data))
                return e;
            e = e.nextSibling;
        } return null; })(t.firstChild);
        e && /^(UL|OL|DL)$/.test(e.nodeName) && t.insertBefore(n.doc.createTextNode(it), t.firstChild);
    } const r = n.createRng(); if (t.normalize(), t.hasChildNodes()) {
        const e = new Fr(t, t);
        let n, s = t;
        for (; n = e.current();) {
            if (es(n)) {
                r.setStart(n, 0), r.setEnd(n, 0);
                break;
            }
            if (o[n.nodeName.toLowerCase()]) {
                r.setStartBefore(n), r.setEndBefore(n);
                break;
            }
            s = n, n = e.next();
        }
        n || (r.setStart(s, 0), r.setEnd(s, 0));
    }
    else
        as(t) ? t.nextSibling && n.isBlock(t.nextSibling) ? (r.setStartBefore(t), r.setEndBefore(t)) : (r.setStartAfter(t), r.setEndAfter(t)) : (r.setStart(t, 0), r.setEnd(t, 0)); e.selection.setRng(r), ep(e, r); }, mR = (e, t) => { const n = e.getRoot(); let o, r = t; for (; r !== n && r && "false" !== e.getContentEditable(r);) {
        if ("true" === e.getContentEditable(r)) {
            o = r;
            break;
        }
        r = r.parentNode;
    } return r !== n ? o : n; }, fR = e => I.from(e.dom.getParent(e.selection.getStart(!0), e.dom.isBlock)), gR = e => { e.innerHTML = '<br data-mce-bogus="1">'; }, pR = (e, t) => { Ad(e).toLowerCase() === t.tagName.toLowerCase() && ((e, t, n) => { const o = e.dom; I.from(n.style).map(o.parseStyle).each((e => { const n = { ...Fo(mn(t)), ...e }; o.setStyles(t, n); })); const r = I.from(n.class).map((e => e.split(/\s+/))), s = I.from(t.className).map((e => Y(e.split(/\s+/), (e => "" !== e)))); ze(r, s, ((e, n) => { const r = Y(n, (t => !H(e, t))), s = [...e, ...r]; o.setAttrib(t, "class", s.join(" ")); })); const a = ["style", "class"], i = Ce(n, ((e, t) => !H(a, t))); o.setAttribs(t, i); })(e, t, Td(e)); }, hR = (e, t, n, o, r = !0, s, a) => { const i = e.dom, l = e.schema, d = Ad(e), c = n ? n.nodeName.toUpperCase() : ""; let u = t; const m = l.getTextInlineElements(); let f; f = s || "TABLE" === c || "HR" === c ? i.create(s || d, a || {}) : n.cloneNode(!1); let g = f; if (r) {
        do {
            if (m[u.nodeName]) {
                if (Am(u) || Hm(u))
                    continue;
                const e = u.cloneNode(!1);
                i.setAttrib(e, "id", ""), f.hasChildNodes() ? (e.appendChild(f.firstChild), f.appendChild(e)) : (g = e, f.appendChild(e));
            }
        } while ((u = u.parentNode) && u !== o);
    }
    else
        i.setAttrib(f, "style", null), i.setAttrib(f, "class", null); return pR(e, f), gR(g), f; }, bR = (e, t) => { const n = null == e ? void 0 : e.parentNode; return C(n) && n.nodeName === t; }, vR = e => C(e) && /^(OL|UL|LI)$/.test(e.nodeName), yR = e => C(e) && /^(LI|DT|DD)$/.test(e.nodeName), CR = e => { const t = e.parentNode; return yR(t) ? t : e; }, wR = (e, t, n) => { let o = e[n ? "firstChild" : "lastChild"]; for (; o && !qr(o);)
        o = o[n ? "nextSibling" : "previousSibling"]; return o === t; }, ER = e => G(we(Fo(mn(e)), ((e, t) => `${t}: ${e};`)), ((e, t) => e + t), ""), xR = (e, t) => t && "A" === t.nodeName && e.isEmpty(t), _R = (e, t) => e.nodeName === t || e.previousSibling && e.previousSibling.nodeName === t, SR = (e, t) => C(t) && e.isBlock(t) && !/^(TD|TH|CAPTION|FORM)$/.test(t.nodeName) && !/^(fixed|absolute)/i.test(t.style.position) && e.isEditable(t.parentNode) && "false" !== e.getContentEditable(t), kR = (e, t, n) => es(t) ? e ? 1 === n && t.data.charAt(n - 1) === Pi ? 0 : n : n === t.data.length - 1 && t.data.charAt(n) === Pi ? t.data.length : n : n, NR = { insert: (e, t) => { let n, o, r, s, a = !1; const i = e.dom, l = e.schema.getNonEmptyElements(), d = e.selection.getRng(), c = Ad(e), u = mn(d.startContainer), f = $n(u, d.startOffset), g = f.exists((e => kn(e) && !gr(e))), p = d.collapsed && g, b = (t, o) => hR(e, n, S, _, Dd(e), t, o), v = e => { const t = kR(e, n, o); if (es(n) && (e ? t > 0 : t < n.data.length))
            return !1; if ((n.parentNode === S || n === S) && a && !e)
            return !0; if (e && qr(n) && n === S.firstChild)
            return !0; if (_R(n, "TABLE") || _R(n, "HR"))
            return (e => "BR" === e.nodeName || e.nextSibling && "BR" === e.nextSibling.nodeName)(n) ? !e : a && !e || !a && e; const r = new Fr(n, S); let s; for (es(n) && (e && 0 === t ? r.prev() : e || t !== n.data.length || r.next()); s = r.current();) {
            if (qr(s)) {
                if (!s.getAttribute("data-mce-bogus")) {
                    const e = s.nodeName.toLowerCase();
                    if (l[e] && "br" !== e)
                        return !1;
                }
            }
            else if (es(s) && !zr(s.data))
                return !1;
            e ? r.prev() : r.next();
        } return !0; }, w = () => { let t; return t = /^(H[1-6]|PRE|FIGURE)$/.test(r) && "HGROUP" !== k ? b(c) : b(), ((e, t) => { const n = Ld(e); return !y(t) && (m(n) ? H(an.explode(n), t.nodeName.toLowerCase()) : n); })(e, s) && SR(i, s) && i.isEmpty(S, void 0, { includeZwsp: !0 }) ? t = i.split(s, S) : i.insertAfter(t, S), uR(e, t), t; }; Lg(i, d).each((e => { d.setStart(e.startContainer, e.startOffset), d.setEnd(e.endContainer, e.endOffset); })), n = d.startContainer, o = d.startOffset; const E = !(!t || !t.shiftKey), x = !(!t || !t.ctrlKey); qr(n) && n.hasChildNodes() && !p && (a = o > n.childNodes.length - 1, n = n.childNodes[Math.min(o, n.childNodes.length - 1)] || n, o = a && es(n) ? n.data.length : 0); const _ = mR(i, n); if (!_ || ((e, t) => { const n = e.dom.getParent(t, "ol,ul,dl"); return null !== n && "false" === e.dom.getContentEditableParent(n); })(e, n))
            return; E || (n = ((e, t, n, o, r) => { var s, a; const i = e.dom, l = null !== (s = mR(i, o)) && void 0 !== s ? s : i.getRoot(); let d = i.getParent(o, i.isBlock); if (!d || !SR(i, d)) {
            if (d = d || l, !d.hasChildNodes()) {
                const o = i.create(t);
                return pR(e, o), d.appendChild(o), n.setStart(o, 0), n.setEnd(o, 0), o;
            }
            let s, c = o;
            for (; c && c.parentNode !== d;)
                c = c.parentNode;
            for (; c && !i.isBlock(c);)
                s = c, c = c.previousSibling;
            const u = null === (a = null == s ? void 0 : s.parentElement) || void 0 === a ? void 0 : a.nodeName;
            if (s && u && e.schema.isValidChild(u, t.toLowerCase())) {
                const a = s.parentNode, l = i.create(t);
                for (pR(e, l), a.insertBefore(l, s), c = s; c && !i.isBlock(c);) {
                    const e = c.nextSibling;
                    l.appendChild(c), c = e;
                }
                n.setStart(o, r), n.setEnd(o, r);
            }
        } return o; })(e, c, d, n, o)); let S = i.getParent(n, i.isBlock) || i.getRoot(); s = C(null == S ? void 0 : S.parentNode) ? i.getParent(S.parentNode, i.isBlock) : null, r = S ? S.nodeName.toUpperCase() : ""; const k = s ? s.nodeName.toUpperCase() : ""; if ("LI" !== k || x || (S = s, s = s.parentNode, r = k), qr(s) && ((e, t, n) => !t && n.nodeName.toLowerCase() === Ad(e) && e.dom.isEmpty(n) && ((t, n) => { let o = n; for (; o && o !== t && h(o.nextSibling);) {
            const t = o.parentElement;
            if (!t || (r = t, !_e(e.schema.getTextBlockElements(), r.nodeName.toLowerCase())))
                return hs(t);
            o = t;
        } var r; return !1; })(e.getBody(), n))(e, E, S))
            return ((e, t, n) => { var o, r, s; const a = t(Ad(e)), i = ((e, t) => e.dom.getParent(t, hs))(e, n); i && (e.dom.insertAfter(a, i), uR(e, a), (null !== (s = null === (r = null === (o = n.parentElement) || void 0 === o ? void 0 : o.childNodes) || void 0 === r ? void 0 : r.length) && void 0 !== s ? s : 0) > 1 && e.dom.remove(n)); })(e, b, S); if (/^(LI|DT|DD)$/.test(r) && qr(s) && i.isEmpty(S))
            return void ((e, t, n, o, r) => { const s = e.dom, a = e.selection.getRng(), i = n.parentNode; if (n === e.getBody() || !i)
                return; var l; vR(l = n) && vR(l.parentNode) && (r = "LI"); const d = yR(o) ? ER(o) : void 0; let c = yR(o) && d ? t(r, { style: ER(o) }) : t(r); if (wR(n, o, !0) && wR(n, o, !1))
                if (bR(n, "LI")) {
                    const e = CR(n);
                    s.insertAfter(c, e), (e => { var t; return (null === (t = e.parentNode) || void 0 === t ? void 0 : t.firstChild) === e; })(n) ? s.remove(e) : s.remove(n);
                }
                else
                    s.replace(c, n);
            else if (wR(n, o, !0))
                bR(n, "LI") ? (s.insertAfter(c, CR(n)), c.appendChild(s.doc.createTextNode(" ")), c.appendChild(n)) : i.insertBefore(c, n), s.remove(o);
            else if (wR(n, o, !1))
                s.insertAfter(c, CR(n)), s.remove(o);
            else {
                n = CR(n);
                const e = a.cloneRange();
                e.setStartAfter(o), e.setEndAfter(n);
                const t = e.extractContents();
                if ("LI" === r && (e => e.firstChild && "LI" === e.firstChild.nodeName)(t)) {
                    const e = Y(V(c.children, mn), O(On("br")));
                    c = t.firstChild, s.insertAfter(t, n), q(e, (e => lo(mn(c), e))), d && c.setAttribute("style", d);
                }
                else
                    s.insertAfter(t, n), s.insertAfter(c, n);
                s.remove(o);
            } uR(e, c); })(e, b, s, S, c); if (!(p || S !== e.getBody() && SR(i, S)))
            return; const N = S.parentNode; let R; if (p)
            R = b(c), f.fold((() => { co(u, mn(R)); }), (e => { ao(e, mn(R)); })), e.selection.setCursorLocation(R, 0);
        else if (Fi(S))
            R = Wi(S), i.isEmpty(S) && gR(S), pR(e, R), uR(e, R);
        else if (v(!1))
            R = w();
        else if (v(!0) && N) {
            const t = Pl.fromRangeStart(d), n = Yp(t), o = mn(S), r = ch(o, t, e.schema) ? uh(o, t, e.schema).bind((e => I.from(e.getNode()))) : I.none();
            R = N.insertBefore(b(), S);
            const s = _R(S, "HR") || n ? R : r.getOr(S);
            uR(e, s);
        }
        else {
            const t = (e => { const t = e.cloneRange(); return t.setStart(e.startContainer, kR(!0, e.startContainer, e.startOffset)), t.setEnd(e.endContainer, kR(!1, e.endContainer, e.endOffset)), t; })(d).cloneRange();
            t.setEndAfter(S);
            const n = t.extractContents();
            (e => { q(br(mn(e), Rn), (e => { const t = e.dom; t.nodeValue = Li(t.data); })); })(n), (e => { let t = e; do {
                es(t) && (t.data = t.data.replace(/^[\r\n]+/, "")), t = t.firstChild;
            } while (t); })(n), R = n.firstChild, i.insertAfter(n, S), ((e, t, n) => { var o; const r = []; if (!n)
                return; let s = n; for (; s = s.firstChild;) {
                if (e.isBlock(s))
                    return;
                qr(s) && !t[s.nodeName.toLowerCase()] && r.push(s);
            } let a = r.length; for (; a--;)
                s = r[a], (!s.hasChildNodes() || s.firstChild === s.lastChild && "" === (null === (o = s.firstChild) || void 0 === o ? void 0 : o.nodeValue) || xR(e, s)) && e.remove(s); })(i, l, R), ((e, t) => { t.normalize(); const n = t.lastChild; (!n || qr(n) && /^(left|right)$/gi.test(e.getStyle(n, "float", !0))) && e.add(t, "br"); })(i, S), i.isEmpty(S) && gR(S), R.normalize(), i.isEmpty(R) ? (i.remove(R), w()) : (pR(e, R), uR(e, R));
        } i.setAttrib(R, "id", ""), e.dispatch("NewBlock", { newBlock: R }); }, fakeEventName: "insertParagraph" }, RR = (e, t, n) => { const o = e.dom.createRng(); n ? (o.setStartBefore(t), o.setEndBefore(t)) : (o.setStartAfter(t), o.setEndAfter(t)), e.selection.setRng(o), ep(e, o); }, AR = (e, t) => { const n = cn("br"); ao(mn(t), n), e.undoManager.add(); }, TR = (e, t) => { OR(e.getBody(), t) || io(mn(t), cn("br")); const n = cn("br"); io(mn(t), n), RR(e, n.dom, !1), e.undoManager.add(); }, OR = (e, t) => { return n = Pl.after(t), !!as(n.getNode()) || _m(e, Pl.after(t)).map((e => as(e.getNode()))).getOr(!1); var n; }, BR = e => e && "A" === e.nodeName && "href" in e, PR = e => e.fold(L, BR, BR, L), DR = (e, t) => { t.fold(_, T(AR, e), T(TR, e), _); }, LR = { insert: (e, t) => { const n = (e => { const t = T(zh, e), n = Pl.fromRangeStart(e.selection.getRng()); return Zx(t, e.getBody(), n).filter(PR); })(e); n.isSome() ? n.each(T(DR, e)) : ((e, t) => { const n = e.selection, o = e.dom, r = n.getRng(); let s, a = !1; Lg(o, r).each((e => { r.setStart(e.startContainer, e.startOffset), r.setEnd(e.endContainer, e.endOffset); })); let i = r.startOffset, l = r.startContainer; if (qr(l) && l.hasChildNodes()) {
            const e = i > l.childNodes.length - 1;
            l = l.childNodes[Math.min(i, l.childNodes.length - 1)] || l, i = e && es(l) ? l.data.length : 0;
        } let d = o.getParent(l, o.isBlock); const c = d && d.parentNode ? o.getParent(d.parentNode, o.isBlock) : null, u = c ? c.nodeName.toUpperCase() : "", m = !(!t || !t.ctrlKey); "LI" !== u || m || (d = c), es(l) && i >= l.data.length && (((e, t, n) => { const o = new Fr(t, n); let r; const s = e.getNonEmptyElements(); for (; r = o.next();)
            if (s[r.nodeName.toLowerCase()] || es(r) && r.length > 0)
                return !0; return !1; })(e.schema, l, d || o.getRoot()) || (s = o.create("br"), r.insertNode(s), r.setStartAfter(s), r.setEndAfter(s), a = !0)), s = o.create("br"), Ll(o, r, s), RR(e, s, a), e.undoManager.add(); })(e, t); }, fakeEventName: "insertLineBreak" }, MR = (e, t) => fR(e).filter((e => t.length > 0 && vn(mn(e), t))).isSome(), IR = ke([{ br: [] }, { block: [] }, { none: [] }]), FR = (e, t) => (e => MR(e, Pd(e)))(e), UR = e => (t, n) => (e => fR(e).filter((e => Ni(mn(e)))).isSome())(t) === e, zR = (e, t) => (n, o) => { const r = (e => fR(e).fold(N(""), (e => e.nodeName.toUpperCase())))(n) === e.toUpperCase(); return r === t; }, jR = e => { const t = mR(e.dom, e.selection.getStart()); return y(t); }, HR = e => zR("pre", e), $R = e => (t, n) => Rd(t) === e, VR = (e, t) => (e => MR(e, Bd(e)))(e), qR = (e, t) => t, WR = e => { const t = Ad(e), n = mR(e.dom, e.selection.getStart()); return C(n) && e.schema.isValidChild(n.nodeName, t); }, KR = e => { const t = e.selection.getRng(), n = mn(t.startContainer), o = $n(n, t.startOffset).map((e => kn(e) && !gr(e))); return t.collapsed && o.getOr(!0); }, YR = (e, t) => (n, o) => G(e, ((e, t) => e && t(n, o)), !0) ? I.some(t) : I.none(), XR = (e, t, n) => { if (!t.mode.isReadOnly()) {
        if (t.selection.isCollapsed() || (e => { e.execCommand("delete"); })(t), C(n) && aR(t, e.fakeEventName).isDefaultPrevented())
            return;
        e.insert(t, n), C(n) && sR(t, e.fakeEventName);
    } }, GR = (e, t) => { if (e.mode.isReadOnly())
        return; const n = () => XR(LR, e, t), o = () => XR(NR, e, t), r = ((e, t) => jx([YR([FR], IR.none()), YR([HR(!0), jR], IR.none()), YR([zR("summary", !0)], IR.br()), YR([HR(!0), $R(!1), qR], IR.br()), YR([HR(!0), $R(!1)], IR.block()), YR([HR(!0), $R(!0), qR], IR.block()), YR([HR(!0), $R(!0)], IR.br()), YR([UR(!0), qR], IR.br()), YR([UR(!0)], IR.block()), YR([VR], IR.br()), YR([qR], IR.br()), YR([WR], IR.block()), YR([KR], IR.block())], [e, !(!t || !t.shiftKey)]).getOr(IR.none()))(e, t); switch (Od(e)) {
        case "linebreak":
            r.fold(n, n, _);
            break;
        case "block":
            r.fold(o, o, _);
            break;
        case "invert":
            r.fold(o, n, _);
            break;
        default: r.fold(n, o, _);
    } }, ZR = Kt(), QR = ZR.os.isiOS() && ZR.browser.isSafari(), JR = (e, t) => { var n; t.isDefaultPrevented() || (t.preventDefault(), (n = e.undoManager).typing && (n.typing = !1, n.add()), e.undoManager.transact((() => { GR(e, t); }))); }, eA = Kt(), tA = e => e.stopImmediatePropagation(), nA = e => e.keyCode === wg.PAGE_UP || e.keyCode === wg.PAGE_DOWN, oA = (e, t, n) => { n && !e.get() ? t.on("NodeChange", tA, !0) : !n && e.get() && t.off("NodeChange", tA), e.set(n); }, rA = (e, t) => e === t || e.contains(t), sA = (e, t) => { const n = t.container(), o = t.offset(); return es(n) ? (n.insertData(o, e), I.some(Pl(n, o + e.length))) : om(t).map((n => { const o = un(e); return t.isAtEnd() ? io(n, o) : ao(n, o), Pl(o.dom, e.length); })); }, aA = T(sA, it), iA = T(sA, " "), lA = e => t => { e.selection.setRng(t.toRange()), e.nodeChanged(); }, dA = e => { const t = Pl.fromRangeStart(e.selection.getRng()), n = mn(e.getBody()); if (e.selection.isCollapsed()) {
        const o = T(zh, e), r = Pl.fromRangeStart(e.selection.getRng());
        return Zx(o, e.getBody(), r).bind((e => t => t.fold((t => Sm(e.dom, Pl.before(t))), (e => km(e)), (e => Nm(e)), (t => _m(e.dom, Pl.after(t)))))(n)).map((o => () => ((e, t, n) => o => yh(e, o, n) ? aA(t) : iA(t))(n, t, e.schema)(o).each(lA(e))));
    } return I.none(); }, cA = e => { return je(nn.browser.isFirefox() && e.selection.isEditable() && (t = e.dom, n = e.selection.getRng().startContainer, t.isEditable(t.getParent(n, "summary"))), (() => { const t = mn(e.getBody()); e.selection.isCollapsed() || e.getDoc().execCommand("Delete"), ((e, t, n) => yh(e, t, n) ? aA(t) : iA(t))(t, Pl.fromRangeStart(e.selection.getRng()), e.schema).each(lA(e)); })); var t, n; }, uA = e => lu(e) ? [{ keyCode: wg.TAB, action: $S(Uk, e, !0) }, { keyCode: wg.TAB, shiftKey: !0, action: $S(Uk, e, !1) }] : [], mA = e => { if (e.addShortcut("Meta+P", "", "mcePrint"), KN(e), kw(e))
        return Ne(null); {
        const t = K_(e);
        return (e => { e.on("beforeinput", (t => { e.selection.isEditable() && !$(t.getTargetRanges(), (t => !((e, t) => !rA(e.getBody(), t.startContainer) || !rA(e.getBody(), t.endContainer) || tp(e.dom, t))(e, t))) || t.preventDefault(); })); })(e), (e => { e.on("keyup compositionstart", T(OS, e)); })(e), ((e, t) => { e.on("keydown", (n => { n.isDefaultPrevented() || ((e, t, n) => { const o = nn.os.isMacOS() || nn.os.isiOS(); VS([{ keyCode: wg.RIGHT, action: $S(DS, e, !0) }, { keyCode: wg.LEFT, action: $S(DS, e, !1) }, { keyCode: wg.UP, action: $S(LS, e, !1) }, { keyCode: wg.DOWN, action: $S(LS, e, !0) }, ...o ? [{ keyCode: wg.UP, action: $S(IS, e, !1), metaKey: !0, shiftKey: !0 }, { keyCode: wg.DOWN, action: $S(IS, e, !0), metaKey: !0, shiftKey: !0 }] : [], { keyCode: wg.RIGHT, action: $S(Ok, e, !0) }, { keyCode: wg.LEFT, action: $S(Ok, e, !1) }, { keyCode: wg.UP, action: $S(Bk, e, !1) }, { keyCode: wg.DOWN, action: $S(Bk, e, !0) }, { keyCode: wg.UP, action: $S(Bk, e, !1) }, { keyCode: wg.UP, action: $S(zS, e, !1) }, { keyCode: wg.DOWN, action: $S(zS, e, !0) }, { keyCode: wg.RIGHT, action: $S(WS, e, !0) }, { keyCode: wg.LEFT, action: $S(WS, e, !1) }, { keyCode: wg.UP, action: $S(KS, e, !1) }, { keyCode: wg.DOWN, action: $S(KS, e, !0) }, { keyCode: wg.RIGHT, action: $S(q_, e, t, !0) }, { keyCode: wg.LEFT, action: $S(q_, e, t, !1) }, { keyCode: wg.RIGHT, ctrlKey: !o, altKey: o, action: $S(Y_, e, t) }, { keyCode: wg.LEFT, ctrlKey: !o, altKey: o, action: $S(X_, e, t) }, { keyCode: wg.UP, action: $S(US, e, !1) }, { keyCode: wg.DOWN, action: $S(US, e, !0) }], n).each((e => { n.preventDefault(); })); })(e, t, n); })); })(e, t), ((e, t) => { let n = !1; e.on("keydown", (o => { n = o.keyCode === wg.BACKSPACE, o.isDefaultPrevented() || ((e, t, n) => { const o = n.keyCode === wg.BACKSPACE ? "deleteContentBackward" : "deleteContentForward", r = e.selection.isCollapsed(), s = r ? "character" : "selection", a = e => r ? e ? "word" : "line" : "selection"; qS([{ keyCode: wg.BACKSPACE, action: $S(yS, e) }, { keyCode: wg.BACKSPACE, action: $S(_x, e, !1) }, { keyCode: wg.DELETE, action: $S(_x, e, !0) }, { keyCode: wg.BACKSPACE, action: $S(hx, e, !1) }, { keyCode: wg.DELETE, action: $S(hx, e, !0) }, { keyCode: wg.BACKSPACE, action: $S(J_, e, t, !1) }, { keyCode: wg.DELETE, action: $S(J_, e, t, !0) }, { keyCode: wg.BACKSPACE, action: $S(kb, e, !1) }, { keyCode: wg.DELETE, action: $S(kb, e, !0) }, { keyCode: wg.BACKSPACE, action: $S(oR, e, !1, s) }, { keyCode: wg.DELETE, action: $S(oR, e, !0, s) }, ...dR ? [{ keyCode: wg.BACKSPACE, altKey: !0, action: $S(oR, e, !1, a(!0)) }, { keyCode: wg.DELETE, altKey: !0, action: $S(oR, e, !0, a(!0)) }, { keyCode: wg.BACKSPACE, metaKey: !0, action: $S(oR, e, !1, a(!1)) }] : [{ keyCode: wg.BACKSPACE, ctrlKey: !0, action: $S(oR, e, !1, a(!0)) }, { keyCode: wg.DELETE, ctrlKey: !0, action: $S(oR, e, !0, a(!0)) }], { keyCode: wg.BACKSPACE, action: $S(Tx, e, !1) }, { keyCode: wg.DELETE, action: $S(Tx, e, !0) }, { keyCode: wg.BACKSPACE, action: $S(uS, e, !1) }, { keyCode: wg.DELETE, action: $S(uS, e, !0) }, { keyCode: wg.BACKSPACE, action: $S(dx, e, !1) }, { keyCode: wg.DELETE, action: $S(dx, e, !0) }, { keyCode: wg.BACKSPACE, action: $S(ax, e, !1) }, { keyCode: wg.DELETE, action: $S(ax, e, !0) }, { keyCode: wg.BACKSPACE, action: $S(lS, e, !1) }, { keyCode: wg.DELETE, action: $S(lS, e, !0) }, { keyCode: wg.BACKSPACE, action: $S(Ax, e, !1) }, { keyCode: wg.DELETE, action: $S(Ax, e, !0) }], n).filter((t => e.selection.isEditable())).each((t => { n.preventDefault(), aR(e, o).isDefaultPrevented() || (t(), sR(e, o)); })); })(e, t, o); })), e.on("keyup", (t => { t.isDefaultPrevented() || ((e, t, n) => { VS([{ keyCode: wg.BACKSPACE, action: $S(xx, e) }, { keyCode: wg.DELETE, action: $S(xx, e) }, ...dR ? [{ keyCode: wg.BACKSPACE, altKey: !0, action: $S(dS, e) }, { keyCode: wg.DELETE, altKey: !0, action: $S(dS, e) }, ...n ? [{ keyCode: cR ? 224 : 91, action: $S(dS, e) }] : []] : [{ keyCode: wg.BACKSPACE, ctrlKey: !0, action: $S(dS, e) }, { keyCode: wg.DELETE, ctrlKey: !0, action: $S(dS, e) }]], t); })(e, t, n), n = !1; })); })(e, t), (e => { let t = I.none(); e.on("keydown", (n => { n.keyCode === wg.ENTER && (QR && (e => { if (!e.collapsed)
            return !1; const t = e.startContainer; if (es(t)) {
            const n = /^[\uAC00-\uD7AF\u1100-\u11FF\u3130-\u318F\uA960-\uA97F\uD7B0-\uD7FF]$/, o = t.data.charAt(e.startOffset - 1);
            return n.test(o);
        } return !1; })(e.selection.getRng()) ? (e => { t = I.some(e.selection.getBookmark()), e.undoManager.add(); })(e) : JR(e, n)); })), e.on("keyup", (n => { n.keyCode === wg.ENTER && t.each((() => ((e, n) => { e.undoManager.undo(), t.fold(_, (t => e.selection.moveToBookmark(t))), JR(e, n), t = I.none(); })(e, n))); })); })(e), (e => { e.on("keydown", (t => { t.isDefaultPrevented() || ((e, t) => { qS([{ keyCode: wg.SPACEBAR, action: $S(dA, e) }, { keyCode: wg.SPACEBAR, action: $S(cA, e) }], t).each((n => { t.preventDefault(), aR(e, "insertText", { data: " " }).isDefaultPrevented() || (n(), sR(e, "insertText", { data: " " })); })); })(e, t); })); })(e), (e => { e.on("input", (t => { t.isComposing || (e => { const t = mn(e.getBody()); e.selection.isCollapsed() && Nh(t, Pl.fromRangeStart(e.selection.getRng()), e.schema).each((t => { e.selection.setRng(t.toRange()); })); })(e); })); })(e), (e => { e.on("keydown", (t => { t.isDefaultPrevented() || ((e, t) => { VS([...uA(e)], t).each((e => { t.preventDefault(); })); })(e, t); })); })(e), ((e, t) => { e.on("keydown", (n => { n.isDefaultPrevented() || ((e, t, n) => { const o = nn.os.isMacOS() || nn.os.isiOS(); VS([{ keyCode: wg.END, action: $S(MS, e, !0) }, { keyCode: wg.HOME, action: $S(MS, e, !1) }, ...o ? [] : [{ keyCode: wg.HOME, action: $S(IS, e, !1), ctrlKey: !0, shiftKey: !0 }, { keyCode: wg.END, action: $S(IS, e, !0), ctrlKey: !0, shiftKey: !0 }], { keyCode: wg.END, action: $S(YS, e, !0) }, { keyCode: wg.HOME, action: $S(YS, e, !1) }, { keyCode: wg.END, action: $S(G_, e, !0, t) }, { keyCode: wg.HOME, action: $S(G_, e, !1, t) }], n).each((e => { n.preventDefault(); })); })(e, t, n); })); })(e, t), ((e, t) => { if (eA.os.isMacOS())
            return; const n = Ne(!1); e.on("keydown", (t => { nA(t) && oA(n, e, !0); })), e.on("keyup", (o => { o.isDefaultPrevented() || ((e, t, n) => { VS([{ keyCode: wg.PAGE_UP, action: $S(G_, e, !1, t) }, { keyCode: wg.PAGE_DOWN, action: $S(G_, e, !0, t) }], n); })(e, t, o), nA(o) && n.get() && (oA(n, e, !1), e.nodeChanged()); })); })(e, t), t;
    } };
    class fA {
        constructor(e) { let t; this.lastPath = [], this.editor = e; const n = this; "onselectionchange" in e.getDoc() || e.on("NodeChange click mouseup keyup focus", (n => { const o = e.selection.getRng(), r = { startContainer: o.startContainer, startOffset: o.startOffset, endContainer: o.endContainer, endOffset: o.endOffset }; "nodechange" !== n.type && Rg(r, t) || e.dispatch("SelectionChange"), t = r; })), e.on("contextmenu", (() => { ng(e), e.dispatch("SelectionChange"); })), e.on("SelectionChange", (() => { const t = e.selection.getStart(!0); t && tf(e) && !n.isSameElementPath(t) && e.dom.isChildOf(t, e.getBody()) && e.nodeChanged({ selectionChange: !0 }); })), e.on("mouseup", (t => { !t.isDefaultPrevented() && tf(e) && ("IMG" === e.selection.getNode().nodeName ? sg.setEditorTimeout(e, (() => { e.nodeChanged(); })) : e.nodeChanged()); })); }
        nodeChanged(e = {}) { const t = this.editor, n = t.selection; let o; if (t.initialized && n && !xc(t) && !hu(t)) {
            const r = t.getBody();
            o = n.getStart(!0) || r, o.ownerDocument === t.getDoc() && t.dom.isChildOf(o, r) || (o = r);
            const s = [];
            t.dom.getParent(o, (e => e === r || (s.push(e), !1))), t.dispatch("NodeChange", { ...e, element: o, parents: s });
        } }
        isSameElementPath(e) { let t; const n = this.editor, o = oe(n.dom.getParents(e, M, n.getBody())); if (o.length === this.lastPath.length) {
            for (t = o.length; t >= 0 && o[t] === this.lastPath[t]; t--)
                ;
            if (-1 === t)
                return this.lastPath = o, !0;
        } return this.lastPath = o, !1; }
    }
    const gA = "x-tinymce/html", pA = N(gA), hA = "\x3c!-- " + gA + " --\x3e", bA = e => hA + e, vA = e => -1 !== e.indexOf(hA), yA = "%MCEPASTEBIN%", CA = e => e.dom.get("mcepastebin"), wA = e => C(e) && "mcepastebin" === e.id, EA = e => e === yA, xA = (e, t) => (an.each(t, (t => { e = u(t, RegExp) ? e.replace(t, "") : e.replace(t[0], t[1]); })), e), _A = e => xA(e, [/^[\s\S]*<body[^>]*>\s*|\s*<\/body[^>]*>[\s\S]*$/gi, /<!--StartFragment-->|<!--EndFragment-->/g, [/( ?)<span class="Apple-converted-space">\u00a0<\/span>( ?)/g, (e, t, n) => t || n ? it : " "], /<br class="Apple-interchange-newline">/g, /<br>$/i]), SA = (e, t) => ({ content: e, cancelled: t }), kA = (e, t) => (e.insertContent(t, { merge: Kc(e), paste: !0 }), !0), NA = e => /^https?:\/\/[\w\-\/+=.,!;:&%@^~(){}?#]+$/i.test(e), RA = (e, t, n) => !(e.selection.isCollapsed() || !NA(t)) && ((e, t, n) => (e.undoManager.extra((() => { n(e, t); }), (() => { e.execCommand("mceInsertLink", !1, t); })), !0))(e, t, n), AA = (e, t, n) => !!((e, t) => NA(t) && $(iu(e), (e => Xe(t.toLowerCase(), `.${e.toLowerCase()}`))))(e, t) && ((e, t, n) => (e.undoManager.extra((() => { n(e, t); }), (() => { e.insertContent('<img src="' + t + '">'); })), !0))(e, t, n), TA = (() => { let e = 0; return () => "mceclip" + e++; })(), OA = e => { const t = yk(); return Ck(t, e), mk(t), t; }, BA = (e, t, n, o, r) => { const s = ((e, t, n) => ((e, t, n) => { const o = ((e, t, n) => e.dispatch("PastePreProcess", { content: t, internal: n }))(e, t, n), r = ((e, t) => { const n = WC({ sanitize: su(e), sandbox_iframes: uu(e), sandbox_iframes_exclusions: mu(e), convert_unsafe_embeds: fu(e) }, e.schema); n.addNodeFilter("meta", (e => { an.each(e, (e => { e.remove(); })); })); const o = n.parse(t, { forced_root_block: !1, isRootContent: !0 }); return Tp({ validate: !0 }, e.schema).serialize(o); })(e, o.content); return e.hasEventListeners("PastePostProcess") && !o.isDefaultPrevented() ? ((e, t, n) => { const o = e.dom.create("div", { style: "display:none" }, t), r = ((e, t, n) => e.dispatch("PastePostProcess", { node: t, internal: n }))(e, o, n); return SA(r.node.innerHTML, r.isDefaultPrevented()); })(e, r, n) : SA(r, o.isDefaultPrevented()); })(e, t, n))(e, t, n); if (!s.cancelled) {
        const t = s.content, n = () => ((e, t, n) => { n || !Yc(e) ? kA(e, t) : ((e, t) => { an.each([RA, AA, kA], (n => !n(e, t, kA))); })(e, t); })(e, t, o);
        r ? aR(e, "insertFromPaste", { dataTransfer: OA(t) }).isDefaultPrevented() || (n(), sR(e, "insertFromPaste")) : n();
    } }, PA = (e, t, n, o) => { const r = n || vA(t); BA(e, (e => e.replace(hA, ""))(t), r, !1, o); }, DA = (e, t, n) => { const o = e.dom.encode(t).replace(/\r\n/g, "\n"), r = ((e, t, n) => { const o = e.split(/\n\n/), r = ((e, t) => { let n = "<" + e; const o = we(t, ((e, t) => t + '="' + ua.encodeAllRaw(e) + '"')); return o.length && (n += " " + o.join(" ")), n + ">"; })(t, n), s = "</" + t + ">", a = V(o, (e => e.split(/\n/).join("<br />"))); return 1 === a.length ? a[0] : V(a, (e => r + e + s)).join(""); })(Hr(o, Gc(e)), Ad(e), Td(e)); BA(e, r, !1, !0, n); }, LA = e => { const t = {}; if (e && e.types)
        for (let n = 0; n < e.types.length; n++) {
            const o = e.types[n];
            try {
                t[o] = e.getData(o);
            }
            catch (e) {
                t[o] = "";
            }
        } return t; }, MA = (e, t) => t in e && e[t].length > 0, IA = e => MA(e, "text/html") || MA(e, "text/plain"), FA = (e, t, n) => { const o = "paste" === t.type ? t.clipboardData : t.dataTransfer; var r; if (jc(e) && o) {
        const s = ((e, t) => { const n = t.items ? te(ce(t.items), (e => "file" === e.kind ? [e.getAsFile()] : [])) : [], o = t.files ? ce(t.files) : []; return Y(n.length > 0 ? n : o, (e => { const t = iu(e); return e => Ye(e.type, "image/") && $(t, (t => (e => { const t = e.toLowerCase(), n = { jpg: "jpeg", jpe: "jpeg", jfi: "jpeg", jif: "jpeg", jfif: "jpeg", pjpeg: "jpeg", pjp: "jpeg", svg: "svg+xml" }; return an.hasOwn(n, t) ? "image/" + n[t] : "image/" + t; })(t) === e.type)); })(e)); })(e, o);
        if (s.length > 0)
            return t.preventDefault(), (r = s, Promise.all(V(r, (e => hy(e).then((t => ({ file: e, uri: t }))))))).then((t => { n && e.selection.setRng(n), q(t, (t => { ((e, t) => { gy(t.uri).each((({ data: n, type: o, base64Encoded: r }) => { const s = r ? n : btoa(n), a = t.file, i = e.editorUpload.blobCache, l = i.getByData(s, o), d = null != l ? l : ((e, t, n, o) => { const r = TA(), s = Id(e) && C(n.name), a = s ? ((e, t) => { const n = t.match(/([\s\S]+?)(?:\.[a-z0-9.]+)$/i); return C(n) ? e.dom.encode(n[1]) : void 0; })(e, n.name) : r, i = s ? n.name : void 0, l = t.create(r, n, o, a, i); return t.add(l), l; })(e, i, a, s); PA(e, `<img src="${d.blobUri()}">`, !1, !0); })); })(e, t); })); })), !0;
    } return !1; }, UA = (e, t, n, o, r) => { let s = _A(n); const a = MA(t, pA()) || vA(n), i = !a && (e => !/<(?:\/?(?!(?:div|p|br|span)>)\w+|(?:(?!(?:span style="white-space:\s?pre;?">)|br\s?\/>))\w+\s[^>]+)>/i.test(e))(s), l = NA(s); (EA(s) || !s.length || i && !l) && (o = !0), (o || l) && (s = MA(t, "text/plain") && i ? t["text/plain"] : (e => { const t = Ra(), n = WC({}, t); let o = ""; const r = t.getVoidElements(), s = an.makeMap("script noscript style textarea video audio iframe object", " "), a = t.getBlockElements(), i = e => { const n = e.name, l = e; if ("br" !== n) {
        if ("wbr" !== n)
            if (r[n] && (o += " "), s[n])
                o += " ";
            else {
                if (3 === e.type && (o += e.value), !(e.name in t.getVoidElements())) {
                    let t = e.firstChild;
                    if (t)
                        do {
                            i(t);
                        } while (t = t.next);
                }
                a[n] && l.next && (o += "\n", "p" === n && (o += "\n"));
            }
    }
    else
        o += "\n"; }; return e = xA(e, [/<!\[[^\]]+\]>/g]), i(n.parse(e)), o; })(s)), EA(s) || (o ? DA(e, s, r) : PA(e, s, a, r)); }, zA = (e, t, n) => { ((e, t, n) => { let o; e.on("keydown", (e => { (e => wg.metaKeyPressed(e) && 86 === e.keyCode || e.shiftKey && 45 === e.keyCode)(e) && !e.isDefaultPrevented() && (o = e.shiftKey && 86 === e.keyCode); })), e.on("paste", (r => { if (r.isDefaultPrevented() || (e => { var t, n; return nn.os.isAndroid() && 0 === (null === (n = null === (t = e.clipboardData) || void 0 === t ? void 0 : t.items) || void 0 === n ? void 0 : n.length); })(r))
        return; const s = "text" === n.get() || o; o = !1; const a = LA(r.clipboardData); !IA(a) && FA(e, r, t.getLastRng() || e.selection.getRng()) || (MA(a, "text/html") ? (r.preventDefault(), UA(e, a, a["text/html"], s, !0)) : MA(a, "text/plain") && MA(a, "text/uri-list") ? (r.preventDefault(), UA(e, a, a["text/plain"], s, !0)) : (t.create(), sg.setEditorTimeout(e, (() => { const n = t.getHtml(); t.remove(), UA(e, a, n, s, !1); }), 0))); })); })(e, t, n), (e => { const t = e => Ye(e, "webkit-fake-url"), n = e => Ye(e, "data:"); e.parser.addNodeFilter("img", ((o, r, s) => { if (!jc(e) && (e => { var t; return !0 === (null === (t = e.data) || void 0 === t ? void 0 : t.paste); })(s))
        for (const r of o) {
            const o = r.attr("src");
            m(o) && !r.attr("data-mce-object") && o !== nn.transparentSrc && (t(o) || !Zc(e) && n(o)) && r.remove();
        } })); })(e); }, jA = (e, t, n, o) => { ((e, t, n) => { if (!e)
        return !1; try {
        return e.clearData(), e.setData("text/html", t), e.setData("text/plain", n), e.setData(pA(), t), !0;
    }
    catch (e) {
        return !1;
    } })(e.clipboardData, t.html, t.text) ? (e.preventDefault(), o()) : n(t.html, o); }, HA = e => (t, n) => { const { dom: o, selection: r } = e, s = o.create("div", { contenteditable: "false", "data-mce-bogus": "all" }), a = o.create("div", { contenteditable: "true" }, t); o.setStyles(s, { position: "fixed", top: "0", left: "-3000px", width: "1000px", overflow: "hidden" }), s.appendChild(a), o.add(e.getBody(), s); const i = r.getRng(); a.focus(); const l = o.createRng(); l.selectNodeContents(a), r.setRng(l), sg.setEditorTimeout(e, (() => { r.setRng(i), o.remove(s), n(); }), 0); }, $A = e => ({ html: bA(e.selection.getContent({ contextual: !0 })), text: e.selection.getContent({ format: "text" }) }), VA = e => !e.selection.isCollapsed() || (e => !!e.dom.getParent(e.selection.getStart(), "td[data-mce-selected],th[data-mce-selected]", e.getBody()))(e), qA = (e, t) => { var n, o; return Fg.getCaretRangeFromPoint(null !== (n = t.clientX) && void 0 !== n ? n : 0, null !== (o = t.clientY) && void 0 !== o ? o : 0, e.getDoc()); }, WA = (e, t) => { e.focus(), t && e.selection.setRng(t); }, KA = /rgb\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*\)/gi, YA = e => an.trim(e).replace(KA, Ma).toLowerCase(), XA = (e, t, n) => { const o = qc(e); if (n || "all" === o || !Wc(e))
        return t; const r = o ? o.split(/[, ]/) : []; if (r && "none" !== o) {
        const n = e.dom, o = e.selection.getNode();
        t = t.replace(/(<[^>]+) style="([^"]*)"([^>]*>)/gi, ((e, t, s, a) => { const i = n.parseStyle(n.decode(s)), l = {}; for (let e = 0; e < r.length; e++) {
            const t = i[r[e]];
            let s = t, a = n.getStyle(o, r[e], !0);
            /color/.test(r[e]) && (s = YA(s), a = YA(a)), a !== s && (l[r[e]] = t);
        } const d = n.serializeStyle(l, "span"); return d ? t + ' style="' + d + '"' + a : t + a; }));
    }
    else
        t = t.replace(/(<[^>]+) style="([^"]*)"([^>]*>)/gi, "$1$3"); return t = t.replace(/(<[^>]+) data-mce-style="([^"]+)"([^>]*>)/gi, ((e, t, n, o) => t + ' style="' + n + '"' + o)), t; }, GA = e => { const t = Ne(!1), n = Ne(Xc(e) ? "text" : "html"), o = (e => { const t = Ne(null); return { create: () => ((e, t) => { const { dom: n, selection: o } = e, r = e.getBody(); t.set(o.getRng()); const s = n.add(e.getBody(), "div", { id: "mcepastebin", class: "mce-pastebin", contentEditable: !0, "data-mce-bogus": "all", style: "position: fixed; top: 50%; width: 10px; height: 10px; overflow: hidden; opacity: 0" }, yA); nn.browser.isFirefox() && n.setStyle(s, "left", "rtl" === n.getStyle(r, "direction", !0) ? 65535 : -65535), n.bind(s, "beforedeactivate focusin focusout", (e => { e.stopPropagation(); })), s.focus(), o.select(s, !0); })(e, t), remove: () => ((e, t) => { const n = e.dom; if (CA(e)) {
            let o;
            const r = t.get();
            for (; o = CA(e);)
                n.remove(o), n.unbind(o);
            r && e.selection.setRng(r);
        } t.set(null); })(e, t), getEl: () => CA(e), getHtml: () => (e => { const t = e.dom, n = (e, n) => { e.appendChild(n), t.remove(n, !0); }, [o, ...r] = Y(e.getBody().childNodes, wA); q(r, (e => { n(o, e); })); const s = t.select("div[id=mcepastebin]", o); for (let e = s.length - 1; e >= 0; e--) {
            const r = t.create("div");
            o.insertBefore(r, s[e]), n(r, s[e]);
        } return o ? o.innerHTML : ""; })(e), getLastRng: t.get }; })(e); (e => { (nn.browser.isChromium() || nn.browser.isSafari()) && ((e, t) => { e.on("PastePreProcess", (n => { n.content = t(e, n.content, n.internal); })); })(e, XA); })(e), ((e, t) => { e.addCommand("mceTogglePlainTextPaste", (() => { ((e, t) => { "text" === t.get() ? (t.set("html"), gd(e, !1)) : (t.set("text"), gd(e, !0)), e.focus(); })(e, t); })), e.addCommand("mceInsertClipboardContent", ((t, n) => { n.html && PA(e, n.html, n.internal, !1), n.text && DA(e, n.text, !1); })); })(e, n), (e => { const t = t => n => { t(e, n); }, n = Hc(e); w(n) && e.on("PastePreProcess", t(n)); const o = $c(e); w(o) && e.on("PastePostProcess", t(o)); })(e), e.addQueryStateHandler("mceTogglePlainTextPaste", (() => "text" === n.get())), e.on("PreInit", (() => { (e => { e.on("cut", (e => t => { !t.isDefaultPrevented() && VA(e) && e.selection.isEditable() && jA(t, $A(e), HA(e), (() => { if (nn.browser.isChromium() || nn.browser.isFirefox()) {
        const t = e.selection.getRng();
        sg.setEditorTimeout(e, (() => { e.selection.setRng(t), e.execCommand("Delete"); }), 0);
    }
    else
        e.execCommand("Delete"); })); })(e)), e.on("copy", (e => t => { !t.isDefaultPrevented() && VA(e) && jA(t, $A(e), HA(e), _); })(e)); })(e), ((e, t) => { zc(e) && e.on("dragend dragover draggesture dragdrop drop drag", (e => { e.preventDefault(), e.stopPropagation(); })), jc(e) || e.on("drop", (e => { const t = e.dataTransfer; t && (e => $(e.files, (e => /^image\//.test(e.type))))(t) && e.preventDefault(); })), e.on("drop", (n => { if (n.isDefaultPrevented())
        return; const o = qA(e, n); if (y(o))
        return; const r = LA(n.dataTransfer), s = MA(r, pA()); if ((!IA(r) || (e => { const t = e["text/plain"]; return !!t && 0 === t.indexOf("file://"); })(r)) && FA(e, n, o))
        return; const a = r[pA()], i = a || r["text/html"] || r["text/plain"], l = ((e, t, n, o) => { const r = e.getParent(n, (e => js(t, e))); if (!h(e.getParent(n, "summary")))
        return !0; if (r && _e(o, "text/html")) {
        const e = (new DOMParser).parseFromString(o["text/html"], "text/html").body;
        return !h(e.querySelector(r.nodeName.toLowerCase()));
    } return !1; })(e.dom, e.schema, o.startContainer, r), d = t.get(); d && !l || i && (n.preventDefault(), sg.setEditorTimeout(e, (() => { e.undoManager.transact((() => { (a || d && l) && e.execCommand("Delete"), WA(e, o); const t = _A(i); r["text/html"] ? PA(e, t, s, !0) : DA(e, t, !0); })); }))); })), e.on("dragstart", (e => { t.set(!0); })), e.on("dragover dragend", (n => { jc(e) && !t.get() && (n.preventDefault(), WA(e, qA(e, n))), "dragend" === n.type && t.set(!1); })), (e => { e.on("input", (t => { const n = e => h(e.querySelector("summary")); if ("deleteByDrag" === t.inputType) {
        const t = Y(e.dom.select("details"), n);
        q(t, (t => { as(t.firstChild) && t.firstChild.remove(); const n = e.dom.create("summary"); n.appendChild(Oi().dom), t.prepend(n); }));
    } })); })(e); })(e, t), zA(e, o, n); })); }, ZA = as, QA = es, JA = e => cs(e.dom), eT = e => t => Cn(mn(e), t), tT = (e, t) => tr(mn(e), JA, eT(t)), nT = (e, t, n) => { const o = new Fr(e, t), r = n ? o.next.bind(o) : o.prev.bind(o); let s = e; for (let t = n ? e : r(); t && !ZA(t); t = r())
        fl(t) && (s = t); return s; }, oT = e => { const t = ((e, t, n) => { const o = Pl.fromRangeStart(e).getNode(), r = ((e, t, n) => tr(mn(e), (e => (e => ds(e.dom))(e) || n.isBlock(xn(e))), eT(t)).getOr(mn(t)).dom)(o, t, n), s = nT(o, r, !1), a = nT(o, r, !0), i = document.createRange(); return tT(s, r).fold((() => { QA(s) ? i.setStart(s, 0) : i.setStartBefore(s); }), (e => i.setStartBefore(e.dom))), tT(a, r).fold((() => { QA(a) ? i.setEnd(a, a.data.length) : i.setEndAfter(a); }), (e => i.setEndAfter(e.dom))), i; })(e.selection.getRng(), e.getBody(), e.schema); e.selection.setRng(Hb(t)); };
    var rT;
    !function (e) { e.Before = "before", e.After = "after"; }(rT || (rT = {}));
    const sT = (e, t) => Math.abs(e.left - t), aT = (e, t) => Math.abs(e.right - t), iT = (e, t) => (e => G(e, ((e, t) => e.fold((() => I.some(t)), (e => { const n = Math.min(t.left, e.left), o = Math.min(t.top, e.top), r = Math.max(t.right, e.right), s = Math.max(t.bottom, e.bottom); return I.some({ top: o, right: r, bottom: s, left: n, width: r - n, height: s - o }); }))), I.none()))(Y(e, (e => { return (n = t) >= (o = e).top && n <= o.bottom; var n, o; }))).fold((() => [[], e]), (t => { const { pass: n, fail: o } = K(e, (e => ((e, t) => { const n = ((e, t) => Math.max(0, Math.min(e.bottom, t.bottom) - Math.max(e.top, t.top)))(e, t) / Math.min(e.height, t.height); return ((e, t) => e.top < t.bottom && e.bottom > t.top)(e, t) && n > .5; })(e, t))); return [n, o]; })), lT = (e, t, n) => t > e.left && t < e.right ? 0 : Math.min(Math.abs(e.left - t), Math.abs(e.right - t)), dT = (e, t, n, o) => { const r = e => fl(e.node) ? I.some(e) : qr(e.node) ? dT(ce(e.node.childNodes), t, n, !1) : I.none(), s = (e, s) => { const a = ae(e, ((e, o) => s(e, t, n) - s(o, t, n))); return ue(a, r).map((e => o && !es(e.node) && a.length > 1 ? ((e, o, s) => r(o).filter((o => Math.abs(s(e, t, n) - s(o, t, n)) < 2 && es(o.node))))(e, a[1], s).getOr(e) : e)); }, [a, i] = iT(N_(e), n), { pass: l, fail: d } = K(i, (e => e.top < n)); return s(a, lT).orThunk((() => s(d, el))).orThunk((() => s(l, el))); }, cT = (e, t, n) => ((e, t, n) => { const o = mn(e), r = Pn(o), s = fn(r, t, n).filter((e => wn(o, e))).getOr(o); return ((e, t, n, o) => { const r = (t, s) => { const a = Y(t.dom.childNodes, O((e => qr(e) && e.classList.contains("mce-drag-container")))); return s.fold((() => dT(a, n, o, !0)), (e => { const t = Y(a, (t => t !== e.dom)); return dT(t, n, o, !0); })).orThunk((() => (Cn(t, e) ? I.none() : Mn(t)).bind((e => r(e, I.some(t)))))); }; return r(t, I.none()); })(o, s, t, n); })(e, t, n).filter((e => Bu(e.node))).map((e => ((e, t) => ({ node: e.node, position: sT(e, t) < aT(e, t) ? rT.Before : rT.After }))(e, t))), uT = e => { var t, n; const o = e.getBoundingClientRect(), r = e.ownerDocument, s = r.documentElement, a = r.defaultView; return { top: o.top + (null !== (t = null == a ? void 0 : a.scrollY) && void 0 !== t ? t : 0) - s.clientTop, left: o.left + (null !== (n = null == a ? void 0 : a.scrollX) && void 0 !== n ? n : 0) - s.clientLeft }; }, mT = e => ({ target: e, srcElement: e }), fT = (e, t, n, o) => { const r = ((e, t) => { const n = (e => { const t = yk(), n = (e => { const t = e; return I.from(t[lk]); })(e); return mk(e), rk(t), t.dropEffect = e.dropEffect, t.effectAllowed = e.effectAllowed, (e => { const t = e; return I.from(t[tk]); })(e).each((e => t.setDragImage(e.image, e.x, e.y))), q(e.types, (n => { "Files" !== n && t.setData(n, e.getData(n)); })), q(e.files, (e => t.items.add(e))), (e => { const t = e; return I.from(t[nk]); })(e).each((e => { ((e, t) => { ok(t)(e); })(t, e); })), n.each((n => { ck(e, n), ck(t, n); })), t; })(e); return "dragstart" === t ? (rk(n), uk(n)) : "drop" === t ? (sk(n), mk(n)) : (ak(n), fk(n)), n; })(n, e); return v(o) ? ((e, t, n) => { const o = B("Function not supported on simulated event."); return { bubbles: !0, cancelBubble: !1, cancelable: !0, composed: !1, currentTarget: null, defaultPrevented: !1, eventPhase: 0, isTrusted: !0, returnValue: !1, timeStamp: 0, type: e, composedPath: o, initEvent: o, preventDefault: _, stopImmediatePropagation: _, stopPropagation: _, AT_TARGET: window.Event.AT_TARGET, BUBBLING_PHASE: window.Event.BUBBLING_PHASE, CAPTURING_PHASE: window.Event.CAPTURING_PHASE, NONE: window.Event.NONE, altKey: !1, button: 0, buttons: 0, clientX: 0, clientY: 0, ctrlKey: !1, layerX: 0, layerY: 0, metaKey: !1, movementX: 0, movementY: 0, offsetX: 0, offsetY: 0, pageX: 0, pageY: 0, relatedTarget: null, screenX: 0, screenY: 0, shiftKey: !1, x: 0, y: 0, detail: 0, view: null, which: 0, initUIEvent: o, initMouseEvent: o, getModifierState: o, dataTransfer: n, ...mT(t) }; })(e, t, r) : ((e, t, n, o) => ({ ...t, dataTransfer: o, type: e, ...mT(n) }))(e, o, t, r); }, gT = cs, pT = ((...e) => t => { for (let n = 0; n < e.length; n++)
        if (e[n](t))
            return !0; return !1; })(gT, ds), hT = (e, t, n, o) => { const r = e.dom, s = t.cloneNode(!0); r.setStyles(s, { width: n, height: o }), r.setAttrib(s, "data-mce-selected", null); const a = r.create("div", { class: "mce-drag-container", "data-mce-bogus": "all", unselectable: "on", contenteditable: "false" }); return r.setStyles(a, { position: "absolute", opacity: .5, overflow: "hidden", border: 0, padding: 0, margin: 0, width: n, height: o }), r.setStyles(s, { margin: 0, boxSizing: "border-box" }), a.appendChild(s), a; }, bT = (e, t) => n => () => { const o = "left" === e ? n.scrollX : n.scrollY; n.scroll({ [e]: o + t, behavior: "smooth" }); }, vT = bT("left", -32), yT = bT("left", 32), CT = bT("top", -32), wT = bT("top", 32), ET = e => { e && e.parentNode && e.parentNode.removeChild(e); }, xT = (e, t, n, o, r) => { "dragstart" === t && Ck(o, e.dom.getOuterHTML(n)); const s = fT(t, n, o, r); return e.dispatch(t, s); }, _T = (e, t) => { const n = ot(((e, n) => ((e, t, n) => { e._selectionOverrides.hideFakeCaret(), cT(e.getBody(), t, n).fold((() => e.selection.placeCaretAt(t, n)), (o => { const r = e._selectionOverrides.showCaret(1, o.node, o.position === rT.Before, !1); r ? e.selection.setRng(r) : e.selection.placeCaretAt(t, n); })); })(t, e, n)), 0); t.on("remove", n.cancel); const o = e; return r => e.on((e => { const s = Math.max(Math.abs(r.screenX - e.screenX), Math.abs(r.screenY - e.screenY)); if (!e.dragging && s > 10) {
        const n = xT(t, "dragstart", e.element, e.dataTransfer, r);
        if (C(n.dataTransfer) && (e.dataTransfer = n.dataTransfer), n.isDefaultPrevented())
            return;
        e.dragging = !0, t.focus();
    } if (e.dragging) {
        const s = r.currentTarget === t.getDoc().documentElement, l = ((e, t) => ({ pageX: t.pageX - e.relX, pageY: t.pageY + 5 }))(e, ((e, t) => { return n = (e => e.inline ? uT(e.getBody()) : { left: 0, top: 0 })(e), o = (e => { const t = e.getBody(); return e.inline ? { left: t.scrollLeft, top: t.scrollTop } : { left: 0, top: 0 }; })(e), r = ((e, t) => { if (t.target.ownerDocument !== e.getDoc()) {
            const n = uT(e.getContentAreaContainer()), o = (e => { const t = e.getBody(), n = e.getDoc().documentElement, o = { left: t.scrollLeft, top: t.scrollTop }, r = { left: t.scrollLeft || n.scrollLeft, top: t.scrollTop || n.scrollTop }; return e.inline ? o : r; })(e);
            return { left: t.pageX - n.left + o.left, top: t.pageY - n.top + o.top };
        } return { left: t.pageX, top: t.pageY }; })(e, t), { pageX: r.left - n.left + o.left, pageY: r.top - n.top + o.top }; var n, o, r; })(t, r));
        a = e.ghost, i = t.getBody(), a.parentNode !== i && i.appendChild(a), ((e, t, n, o, r, s, a, i, l, d, c, u) => { let m = 0, f = 0; e.style.left = t.pageX + "px", e.style.top = t.pageY + "px", t.pageX + n > r && (m = t.pageX + n - r), t.pageY + o > s && (f = t.pageY + o - s), e.style.width = n - m + "px", e.style.height = o - f + "px"; const g = l.clientHeight, p = l.clientWidth, h = a + l.getBoundingClientRect().top, b = i + l.getBoundingClientRect().left; c.on((e => { e.intervalId.clear(), e.dragging && u && (a + 8 >= g ? e.intervalId.set(wT(d)) : a - 8 <= 0 ? e.intervalId.set(CT(d)) : i + 8 >= p ? e.intervalId.set(yT(d)) : i - 8 <= 0 ? e.intervalId.set(vT(d)) : h + 16 >= window.innerHeight ? e.intervalId.set(wT(window)) : h - 16 <= 0 ? e.intervalId.set(CT(window)) : b + 16 >= window.innerWidth ? e.intervalId.set(yT(window)) : b - 16 <= 0 && e.intervalId.set(vT(window))); })); })(e.ghost, l, e.width, e.height, e.maxX, e.maxY, r.clientY, r.clientX, t.getContentAreaContainer(), t.getWin(), o, s), n.throttle(r.clientX, r.clientY);
    } var a, i; })); }, ST = (e, t, n) => { e.on((e => { e.intervalId.clear(), e.dragging && n.fold((() => xT(t, "dragend", e.element, e.dataTransfer)), (n => xT(t, "dragend", e.element, e.dataTransfer, n))); })), kT(e); }, kT = e => { e.on((e => { e.intervalId.clear(), ET(e.ghost); })), e.clear(); }, NT = e => { const t = Ve(), n = ni.DOM, o = document, r = ((e, t) => n => { if ((e => 0 === e.button)(n)) {
        const o = Q(t.dom.getParents(n.target), pT).getOr(null);
        if (C(o) && ((e, t, n) => gT(n) && n !== t && e.isEditable(n.parentElement))(t.dom, t.getBody(), o)) {
            const r = t.dom.getPos(o), s = t.getBody(), a = t.getDoc().documentElement;
            e.set({ element: o, dataTransfer: yk(), dragging: !1, screenX: n.screenX, screenY: n.screenY, maxX: (t.inline ? s.scrollWidth : a.offsetWidth) - 2, maxY: (t.inline ? s.scrollHeight : a.offsetHeight) - 2, relX: n.pageX - r.x, relY: n.pageY - r.y, width: o.offsetWidth, height: o.offsetHeight, ghost: hT(t, o, o.offsetWidth, o.offsetHeight), intervalId: $e(100) });
        }
    } })(t, e), s = _T(t, e), a = ((e, t) => n => { e.on((e => { var o; if (e.intervalId.clear(), e.dragging) {
        if (((e, t, n) => !y(t) && t !== n && !e.dom.isChildOf(t, n) && e.dom.isEditable(t))(t, (e => { const t = e.getSel(); if (C(t)) {
            const e = t.getRangeAt(0).startContainer;
            return es(e) ? e.parentNode : e;
        } return null; })(t.selection), e.element)) {
            const r = null !== (o = t.getDoc().elementFromPoint(n.clientX, n.clientY)) && void 0 !== o ? o : t.getBody();
            xT(t, "drop", r, e.dataTransfer, n).isDefaultPrevented() || t.undoManager.transact((() => { ((e, t) => { const n = e.getParent(t.parentNode, e.isBlock); ET(t), n && n !== e.getRoot() && e.isEmpty(n) && Bi(mn(n)); })(t.dom, e.element), (e => { const t = e.getData("text/html"); return "" === t ? I.none() : I.some(t); })(e.dataTransfer).each((e => t.insertContent(e))), t._selectionOverrides.hideFakeCaret(); }));
        }
        xT(t, "dragend", t.getBody(), e.dataTransfer, n);
    } })), kT(e); })(t, e), i = ((e, t) => n => ST(e, t, I.some(n)))(t, e); e.on("mousedown", r), e.on("mousemove", s), e.on("mouseup", a), n.bind(o, "mousemove", s), n.bind(o, "mouseup", i), e.on("remove", (() => { n.unbind(o, "mousemove", s), n.unbind(o, "mouseup", i); })), e.on("keydown", (n => { n.keyCode === wg.ESC && ST(t, e, I.none()); })); }, RT = cs, AT = (e, t) => Nb(e.getBody(), t), TT = e => { const t = e.selection, n = e.dom, o = e.getBody(), r = Au(e, o, n.isBlock, (() => vg(e))), s = "sel-" + n.uniqueId(), a = "data-mce-selected"; let i; const l = e => e !== o && (RT(e) || gs(e)) && n.isChildOf(e, o) && n.isEditable(e.parentNode), d = (n, o, s, a = !0) => e.dispatch("ShowCaret", { target: o, direction: n, before: s }).isDefaultPrevented() ? null : (a && t.scrollIntoView(o, -1 === n), r.show(s, o)), c = e => zi(e) || Vi(e) || qi(e), u = e => c(e.startContainer) || c(e.endContainer), m = t => { const o = e.schema.getVoidElements(), r = n.createRng(), s = t.startContainer, a = t.startOffset, i = t.endContainer, l = t.endOffset; return _e(o, s.nodeName.toLowerCase()) ? 0 === a ? r.setStartBefore(s) : r.setStartAfter(s) : r.setStart(s, a), _e(o, i.nodeName.toLowerCase()) ? 0 === l ? r.setEndBefore(i) : r.setEndAfter(i) : r.setEnd(i, l), r; }, f = (r, c) => { if (!r)
        return null; if (r.collapsed) {
        if (!u(r)) {
            const e = c ? 1 : -1, t = nm(e, o, r), s = t.getNode(!c);
            if (C(s)) {
                if (Bu(s))
                    return d(e, s, !!c && !t.isAtEnd(), !1);
                if (Ui(s) && cs(s.nextSibling)) {
                    const e = n.createRng();
                    return e.setStart(s, 0), e.setEnd(s, 0), e;
                }
            }
            const a = t.getNode(c);
            if (C(a)) {
                if (Bu(a))
                    return d(e, a, !c && !t.isAtEnd(), !1);
                if (Ui(a) && cs(a.previousSibling)) {
                    const e = n.createRng();
                    return e.setStart(a, 1), e.setEnd(a, 1), e;
                }
            }
        }
        return null;
    } let m = r.startContainer, f = r.startOffset; const g = r.endOffset; if (es(m) && 0 === f && RT(m.parentNode) && (m = m.parentNode, f = n.nodeIndex(m), m = m.parentNode), !qr(m))
        return null; if (g === f + 1 && m === r.endContainer) {
        const o = m.childNodes[f];
        if (l(o))
            return (o => { const r = o.cloneNode(!0), l = e.dispatch("ObjectSelected", { target: o, targetClone: r }); if (l.isDefaultPrevented())
                return null; const d = ((o, r) => { const a = mn(e.getBody()), i = e.getDoc(), l = rr(a, "#" + s).getOrThunk((() => { const e = dn('<div data-mce-bogus="all" class="mce-offscreen-selection"></div>', i); return go(e, "id", s), co(a, e), e; })), d = n.createRng(); wo(l), mo(l, [un(it, i), mn(r), un(it, i)]), d.setStart(l.dom.firstChild, 1), d.setEnd(l.dom.lastChild, 0), Do(l, { top: n.getPos(o, e.getBody()).y + "px" }), oo(l); const c = t.getSel(); return c && (c.removeAllRanges(), c.addRange(d)), d; })(o, l.targetClone), c = mn(o); return q(vr(mn(e.getBody()), `*[${a}]`), (e => { Cn(c, e) || yo(e, a); })), n.getAttrib(o, a) || o.setAttribute(a, "1"), i = o, p(), d; })(o);
    } return null; }, g = () => { i && i.removeAttribute(a), rr(mn(e.getBody()), "#" + s).each(Eo), i = null; }, p = () => { r.hide(); }; return kw(e) || (e.on("click", (t => { n.isEditable(t.target) || (t.preventDefault(), e.focus()); })), e.on("blur NewBlock", g), e.on("ResizeWindow FullscreenStateChanged", r.reposition), e.on("tap", (t => { const n = t.target, o = AT(e, n); RT(o) ? (t.preventDefault(), ux(e, o).each(f)) : l(n) && ux(e, n).each(f); }), !0), e.on("mousedown", (r => { const s = r.target; if (s !== o && "HTML" !== s.nodeName && !n.isChildOf(s, o))
        return; if (!((e, t, n) => { const o = mn(e.getBody()), r = e.inline ? o : mn(Pn(o).dom.documentElement), s = ((e, t, n, o) => { const r = (e => e.dom.getBoundingClientRect())(t); return { x: n - (e ? r.left + t.dom.clientLeft + sE(t) : 0), y: o - (e ? r.top + t.dom.clientTop + rE(t) : 0) }; })(e.inline, r, t, n); return ((e, t, n) => { const o = nE(e), r = oE(e); return t >= 0 && n >= 0 && t <= o && n <= r; })(r, s.x, s.y); })(e, r.clientX, r.clientY))
        return; g(), p(); const a = AT(e, s); RT(a) ? (r.preventDefault(), ux(e, a).each(f)) : cT(o, r.clientX, r.clientY).each((n => { var o; r.preventDefault(), (o = d(1, n.node, n.position === rT.Before, !1)) && t.setRng(o), Wr(a) ? a.focus() : e.getBody().focus(); })); })), e.on("keypress", (e => { wg.modifierPressed(e) || RT(t.getNode()) && e.preventDefault(); })), e.on("GetSelectionRange", (e => { let t = e.range; if (i) {
        if (!i.parentNode)
            return void (i = null);
        t = t.cloneRange(), t.selectNode(i), e.range = t;
    } })), e.on("focusin", (t => { if (r.isShowing() && e.getBody().contains(t.target) && t.target !== e.getBody() && !e.dom.isEditable(t.target.parentNode)) {
        r.hide(), t.target.contains(e.selection.getNode()) || (e.selection.select(t.target, !0), e.selection.collapse(!0));
        const n = f(e.selection.getRng(), !0);
        n && e.selection.setRng(n);
    } })), e.on("SetSelectionRange", (e => { e.range = m(e.range); const t = f(e.range, e.forward); t && (e.range = t); })), e.on("AfterSetSelectionRange", (e => { const t = e.range, o = t.startContainer.parentElement; var r; u(t) || qr(r = o) && "mcepastebin" === r.id || p(), (e => C(e) && n.hasClass(e, "mce-offscreen-selection"))(o) || g(); })), (e => { NT(e), Ac(e) && (e => { const t = t => { if (!t.isDefaultPrevented()) {
        const n = t.dataTransfer;
        n && (H(n.types, "Files") || n.files.length > 0) && (t.preventDefault(), "drop" === t.type && uE(e, "Dropped file type is not supported"));
    } }, n = n => { mg(e, n.target) && t(n); }, o = () => { const o = ni.DOM, r = e.dom, s = document, a = e.inline ? e.getBody() : e.getDoc(), i = ["drop", "dragover"]; q(i, (e => { o.bind(s, e, n), r.bind(a, e, t); })), e.on("remove", (() => { q(i, (e => { o.unbind(s, e, n), r.unbind(a, e, t); })); })); }; e.on("init", (() => { sg.setEditorTimeout(e, o, 0); })); })(e); })(e), (e => { const t = ot((() => { if (!e.removed && e.getBody().contains(document.activeElement)) {
        const t = e.selection.getRng();
        if (t.collapsed) {
            const n = mx(e, t, !1);
            e.selection.setRng(n);
        }
    } }), 0); e.on("focus", (() => { t.throttle(); })), e.on("blur", (() => { t.cancel(); })); })(e), (e => { e.on("init", (() => { e.on("focusin", (t => { const n = t.target; if (gs(n)) {
        const t = Nb(e.getBody(), n), o = cs(t) ? t : n;
        e.selection.getNode() !== o && ux(e, o).each((t => e.selection.setRng(t)));
    } })); })); })(e)), { showCaret: d, showBlockCaretContainer: e => { e.hasAttribute("data-mce-caret") && (Wi(e), t.scrollIntoView(e)); }, hideFakeCaret: p, destroy: () => { r.destroy(), i = null; } }; }, OT = (e, t) => { let n = t; for (let t = e.previousSibling; es(t); t = t.previousSibling)
        n += t.data.length; return n; }, BT = (e, t, n, o, r) => { if (es(n) && (o < 0 || o > n.data.length))
        return []; const s = r && es(n) ? [OT(n, o)] : [o]; let a = n; for (; a !== t && a.parentNode;)
        s.push(e.nodeIndex(a, r)), a = a.parentNode; return a === t ? s.reverse() : []; }, PT = (e, t, n, o, r, s, a = !1) => ({ start: BT(e, t, n, o, a), end: BT(e, t, r, s, a) }), DT = (e, t) => { const n = t.slice(), o = n.pop(); return E(o) ? G(n, ((e, t) => e.bind((e => I.from(e.childNodes[t])))), I.some(e)).bind((e => es(e) && (o < 0 || o > e.data.length) ? I.none() : I.some({ node: e, offset: o }))) : I.none(); }, LT = (e, t) => DT(e, t.start).bind((({ node: n, offset: o }) => DT(e, t.end).map((({ node: e, offset: t }) => { const r = document.createRange(); return r.setStart(n, o), r.setEnd(e, t), r; })))), MT = (e, t, n) => { if (t && e.isEmpty(t) && !n(t)) {
        const o = t.parentNode;
        e.remove(t, es(t.firstChild) && zr(t.firstChild.data)), MT(e, o, n);
    } }, IT = (e, t, n, o = !0) => { const r = t.startContainer.parentNode, s = t.endContainer.parentNode; t.deleteContents(), o && !n(t.startContainer) && (es(t.startContainer) && 0 === t.startContainer.data.length && e.remove(t.startContainer), es(t.endContainer) && 0 === t.endContainer.data.length && e.remove(t.endContainer), MT(e, r, n), r !== s && MT(e, s, n)); }, FT = (e, t) => I.from(e.dom.getParent(t.startContainer, e.dom.isBlock)), UT = (e, t, n) => { const o = e.dynamicPatternsLookup({ text: n, block: t }); return { ...e, blockPatterns: rd(o).concat(e.blockPatterns), inlinePatterns: sd(o).concat(e.inlinePatterns) }; }, zT = (e, t, n, o) => { const r = e.createRng(); return r.setStart(t, 0), r.setEnd(n, o), r.toString(); }, jT = (e, t) => e.create("span", { "data-mce-type": "bookmark", id: t }), HT = (e, t) => { const n = e.createRng(); return n.setStartAfter(t.start), n.setEndBefore(t.end), n; }, $T = (e, t, n) => { const o = LT(e.getRoot(), n).getOrDie("Unable to resolve path range"), r = o.startContainer, s = o.endContainer, a = 0 === o.endOffset ? s : s.splitText(o.endOffset), i = 0 === o.startOffset ? r : r.splitText(o.startOffset), l = i.parentNode; return { prefix: t, end: a.parentNode.insertBefore(jT(e, t + "-end"), a), start: l.insertBefore(jT(e, t + "-start"), i) }; }, VT = (e, t, n) => { MT(e, e.get(t.prefix + "-end"), n), MT(e, e.get(t.prefix + "-start"), n); }, qT = e => 0 === e.start.length, WT = (e, t, n, o) => { const r = t.start; var s; return Wk(e, o.container, o.offset, (s = r, (e, t) => { const n = e.data.substring(0, t), o = n.lastIndexOf(s.charAt(s.length - 1)), r = n.lastIndexOf(s); return -1 !== r ? r + s.length : -1 !== o ? o + 1 : -1; }), n).bind((o => { var s, a; const i = null !== (a = null === (s = n.textContent) || void 0 === s ? void 0 : s.indexOf(r)) && void 0 !== a ? a : -1; if (-1 !== i && o.offset >= i + r.length) {
        const t = e.createRng();
        return t.setStart(o.container, o.offset - r.length), t.setEnd(o.container, o.offset), I.some(t);
    } {
        const s = o.offset - r.length;
        return Vk(o.container, s, n).map((t => { const n = e.createRng(); return n.setStart(t.container, t.offset), n.setEnd(o.container, o.offset), n; })).filter((e => e.toString() === r)).orThunk((() => WT(e, t, n, zk(o.container, 0))));
    } })); }, KT = (e, t, n, o) => { const r = e.dom, s = r.getRoot(), a = n.pattern, i = n.position.container, l = n.position.offset; return Vk(i, l - n.pattern.end.length, t).bind((d => { const c = PT(r, s, d.container, d.offset, i, l, o); if (qT(a))
        return I.some({ matches: [{ pattern: a, startRng: c, endRng: c }], position: d }); {
        const i = YT(e, n.remainingPatterns, d.container, d.offset, t, o), l = i.getOr({ matches: [], position: d }), u = l.position, m = ((e, t, n, o, r, s = !1) => { if (0 === t.start.length && !s) {
            const t = e.createRng();
            return t.setStart(n, o), t.setEnd(n, o), I.some(t);
        } return $k(n, o, r).bind((n => WT(e, t, r, n).bind((e => { var t; if (s) {
            if (e.endContainer === n.container && e.endOffset === n.offset)
                return I.none();
            if (0 === n.offset && (null === (t = e.endContainer.textContent) || void 0 === t ? void 0 : t.length) === e.endOffset)
                return I.none();
        } return I.some(e); })))); })(r, a, u.container, u.offset, t, i.isNone());
        return m.map((e => { const t = ((e, t, n, o = !1) => PT(e, t, n.startContainer, n.startOffset, n.endContainer, n.endOffset, o))(r, s, e, o); return { matches: l.matches.concat([{ pattern: a, startRng: t, endRng: c }]), position: zk(e.startContainer, e.startOffset) }; }));
    } })); }, YT = (e, t, n, o, r, s) => { const a = e.dom; return $k(n, o, a.getRoot()).bind((i => { const l = zT(a, r, n, o); for (let a = 0; a < t.length; a++) {
        const d = t[a];
        if (!Xe(l, d.end))
            continue;
        const c = t.slice();
        c.splice(a, 1);
        const u = KT(e, r, { pattern: d, remainingPatterns: c, position: i }, s);
        if (u.isNone() && o > 0)
            return YT(e, t, n, o - 1, r, s);
        if (u.isSome())
            return u;
    } return I.none(); })); }, XT = (e, t, n) => { e.selection.setRng(n), "inline-format" === t.type ? q(t.format, (t => { e.formatter.apply(t); })) : e.execCommand(t.cmd, !1, t.value); }, GT = (e, t, n, o, r, s) => { var a; return ((e, t) => { const n = ne(e, (e => $(t, (t => e.pattern.start === t.pattern.start && e.pattern.end === t.pattern.end)))); return e.length === t.length ? n ? e : t : e.length > t.length ? e : t; })(YT(e, r.inlinePatterns, n, o, t, s).fold((() => []), (e => e.matches)), YT(e, (a = r.inlinePatterns, ae(a, ((e, t) => t.end.length - e.end.length))), n, o, t, s).fold((() => []), (e => e.matches))); }, ZT = (e, t) => { if (0 === t.length)
        return; const n = e.dom, o = e.selection.getBookmark(), r = ((e, t) => { const n = De("mce_textpattern"), o = X(t, ((t, o) => { const r = $T(e, n + `_end${t.length}`, o.endRng); return t.concat([{ ...o, endMarker: r }]); }), []); return X(o, ((t, r) => { const s = o.length - t.length - 1, a = qT(r.pattern) ? r.endMarker : $T(e, n + `_start${s}`, r.startRng); return t.concat([{ ...r, startMarker: a }]); }), []); })(n, t); q(r, (t => { const o = n.getParent(t.startMarker.start, n.isBlock), r = e => e === o; qT(t.pattern) ? ((e, t, n, o) => { const r = HT(e.dom, n); IT(e.dom, r, o), XT(e, t, r); })(e, t.pattern, t.endMarker, r) : ((e, t, n, o, r) => { const s = e.dom, a = HT(s, o), i = HT(s, n); IT(s, i, r), IT(s, a, r); const l = { prefix: n.prefix, start: n.end, end: o.start }, d = HT(s, l); XT(e, t, d); })(e, t.pattern, t.startMarker, t.endMarker, r), VT(n, t.endMarker, r), VT(n, t.startMarker, r); })), e.selection.moveToBookmark(o); }, QT = (e, t, n) => ((e, t, n) => { if (es(e) && 0 >= e.length)
        return I.some(zk(e, 0)); {
        const t = Ei(jk);
        return I.from(t.forwards(e, 0, Hk(e), n)).map((e => zk(e.container, 0)));
    } })(t, 0, t).map((o => { const r = o.container; return qk(r, n.start.length, t).each((n => { const o = e.createRng(); o.setStart(r, 0), o.setEnd(n.container, n.offset), IT(e, o, (e => e === t)); })), r; })), JT = e => (t, n) => { const o = t.dom, r = n.pattern, s = LT(o.getRoot(), n.range).getOrDie("Unable to resolve path range"); return FT(t, s).each((n => { "block-format" === r.type ? ((e, t) => { const n = t.get(e); return p(n) && le(n).exists((e => _e(e, "block"))); })(r.format, t.formatter) && t.undoManager.transact((() => { e(t.dom, n, r), t.formatter.apply(r.format); })) : "block-command" === r.type && t.undoManager.transact((() => { e(t.dom, n, r), t.execCommand(r.cmd, !1, r.value); })); })), !0; }, eO = e => (t, n) => { const o = (e => ae(e, ((e, t) => t.start.length - e.start.length)))(t), r = n.replace(it, " "); return Q(o, (t => e(t, n, r))); }, tO = (e, t) => (n, o, r, s, a) => { var i; void 0 === a && (a = null !== (i = o.textContent) && void 0 !== i ? i : ""); const l = n.dom, d = Ad(n); return l.is(o, d) ? e(r.blockPatterns, a).map((e => t && an.trim(a).length === e.start.length ? [] : [{ pattern: e, range: PT(l, l.getRoot(), o, 0, o, 0, s) }])).getOr([]) : []; }, nO = JT(((e, t, n) => { QT(e, t, n).each((e => { const t = mn(e), n = Go(t); /^\s[^\s]/.test(n) && Zo(t, n.slice(1)); })); })), oO = eO(((e, t, n) => 0 === t.indexOf(e.start) || 0 === n.indexOf(e.start))), rO = tO(oO, !0), sO = JT(QT), aO = eO(((e, t, n) => t === e.start || n === e.start)), iO = tO(aO, !1), lO = (e, t, n) => { for (let o = 0; o < e.length; o++)
        if (n(e[o], t))
            return !0; return !1; }, dO = e => { const t = [",", ".", ";", ":", "!", "?"], n = [32], o = () => { return t = Qc(e).filter((t => "inline-command" !== t.type && "block-command" !== t.type || e.queryCommandSupported(t.cmd))), n = Jc(e), { inlinePatterns: sd(t), blockPatterns: rd(t), dynamicPatternsLookup: n }; var t, n; }, r = () => (e => e.options.isSet("text_patterns_lookup"))(e); e.on("keydown", (t => { if (13 === t.keyCode && !wg.modifierPressed(t) && e.selection.isCollapsed() && e.selection.isEditable()) {
        const n = ad(o(), "enter");
        (n.inlinePatterns.length > 0 || n.blockPatterns.length > 0 || r()) && ((e, t) => ((e, t) => { const n = e.selection.getRng(); return FT(e, n).map((o => { var r; const s = Math.max(0, n.startOffset), a = UT(t, o, null !== (r = o.textContent) && void 0 !== r ? r : ""); return { inlineMatches: GT(e, o, n.startContainer, s, a, !0), blockMatches: rO(e, o, a, !0) }; })).filter((({ inlineMatches: e, blockMatches: t }) => t.length > 0 || e.length > 0)); })(e, t).fold(L, (({ inlineMatches: t, blockMatches: n }) => (e.undoManager.add(), e.undoManager.extra((() => { e.execCommand("mceInsertNewLine"); }), (() => { (e => { e.insertContent(Pi, { preserve_zwsp: !0 }); })(e), ZT(e, t), ((e, t) => { if (0 === t.length)
            return; const n = e.selection.getBookmark(); q(t, (t => nO(e, t))), e.selection.moveToBookmark(n); })(e, n); const o = e.selection.getRng(), r = $k(o.startContainer, o.startOffset, e.dom.getRoot()); e.execCommand("mceInsertNewLine"), r.each((t => { const n = t.container; n.data.charAt(t.offset - 1) === at && (n.deleteData(t.offset - 1, 1), MT(e.dom, n.parentNode, (t => t === e.dom.getRoot()))); })); })), !0))))(e, n) && t.preventDefault();
    } }), !0), e.on("keydown", (t => { if (32 === t.keyCode && e.selection.isCollapsed() && e.selection.isEditable()) {
        const n = ad(o(), "space");
        (n.blockPatterns.length > 0 || r()) && ((e, t) => ((e, t) => { const n = e.selection.getRng(); return FT(e, n).map((o => { const r = Math.max(0, n.startOffset), s = zT(e.dom, o, n.startContainer, r), a = UT(t, o, s); return iO(e, o, a, !1, s); })).filter((e => e.length > 0)); })(e, t).fold(L, (t => (e.undoManager.transact((() => { ((e, t) => { q(t, (t => sO(e, t))); })(e, t); })), !0))))(e, n) && t.preventDefault();
    } }), !0); const s = () => { if (e.selection.isCollapsed() && e.selection.isEditable()) {
        const t = ad(o(), "space");
        (t.inlinePatterns.length > 0 || r()) && ((e, t) => { const n = e.selection.getRng(); FT(e, n).map((o => { const r = Math.max(0, n.startOffset - 1), s = zT(e.dom, o, n.startContainer, r), a = UT(t, o, s), i = GT(e, o, n.startContainer, r, a, !1); i.length > 0 && e.undoManager.transact((() => { ZT(e, i); })); })); })(e, t);
    } }; e.on("keyup", (e => { lO(n, e, ((e, t) => e === t.keyCode && !wg.modifierPressed(t))) && s(); })), e.on("keypress", (n => { lO(t, n, ((e, t) => e.charCodeAt(0) === t.charCode)) && sg.setEditorTimeout(e, s); })); }, cO = e => { const t = an.each, n = wg.BACKSPACE, o = wg.DELETE, r = e.dom, s = e.selection, a = e.parser, i = nn.browser, l = i.isFirefox(), d = i.isChromium() || i.isSafari(), c = nn.deviceType.isiPhone() || nn.deviceType.isiPad(), u = nn.os.isMacOS() || nn.os.isiOS(), m = (t, n) => { try {
        e.getDoc().execCommand(t, !1, String(n));
    }
    catch (e) { } }, f = e => e.isDefaultPrevented(), g = () => { e.shortcuts.add("meta+a", null, "SelectAll"); }, p = () => { e.inline || r.bind(e.getDoc(), "mousedown mouseup", (t => { let n; if (t.target === e.getDoc().documentElement)
        if (n = s.getRng(), e.getBody().focus(), "mousedown" === t.type) {
            if (zi(n.startContainer))
                return;
            s.placeCaretAt(t.clientX, t.clientY);
        }
        else
            s.setRng(n); })); }, h = () => { Range.prototype.getClientRects || e.on("mousedown", (t => { if (!f(t) && "HTML" === t.target.nodeName) {
        const t = e.getBody();
        t.blur(), sg.setEditorTimeout(e, (() => { t.focus(); }));
    } })); }, b = () => { const t = Bc(e); e.on("click", (n => { const o = n.target; /^(IMG|HR)$/.test(o.nodeName) && r.isEditable(o) && (n.preventDefault(), e.selection.select(o), e.nodeChanged()), "A" === o.nodeName && r.hasClass(o, t) && 0 === o.childNodes.length && r.isEditable(o.parentNode) && (n.preventDefault(), s.select(o)); })); }, v = () => { e.on("keydown", (e => { if (!f(e) && e.keyCode === n && s.isCollapsed() && 0 === s.getRng().startOffset) {
        const t = s.getNode().previousSibling;
        if (t && t.nodeName && "table" === t.nodeName.toLowerCase())
            return e.preventDefault(), !1;
    } return !0; })); }, y = () => { _c(e) || e.on("BeforeExecCommand mousedown", (() => { m("StyleWithCSS", !1), m("enableInlineTableEditing", !1), nc(e) || m("enableObjectResizing", !1); })); }, C = () => { e.contentStyles.push("img:-moz-broken {-moz-force-broken-image-icon:1;min-width:24px;min-height:24px}"); }, w = () => { e.inline || e.on("keydown", (() => { document.activeElement === document.body && e.getWin().focus(); })); }, E = () => { e.inline || (e.contentStyles.push("body {min-height: 150px}"), e.on("click", (t => { let n; "HTML" === t.target.nodeName && (n = e.selection.getRng(), e.getBody().focus(), e.selection.setRng(n), e.selection.normalize(), e.nodeChanged()); }))); }, x = () => { u && e.on("keydown", (t => { !wg.metaKeyPressed(t) || t.shiftKey || 37 !== t.keyCode && 39 !== t.keyCode || (t.preventDefault(), e.selection.getSel().modify("move", 37 === t.keyCode ? "backward" : "forward", "lineboundary")); })); }, S = () => { e.on("click", (e => { let t = e.target; do {
        if ("A" === t.tagName)
            return void e.preventDefault();
    } while (t = t.parentNode); })), e.contentStyles.push(".mce-content-body {-webkit-touch-callout: none}"); }, k = () => { e.on("init", (() => { e.dom.bind(e.getBody(), "submit", (e => { e.preventDefault(); })); })); }, N = _; return kw(e) ? (d && (p(), b(), k(), g(), c && (w(), E(), S())), l && (h(), y(), C(), x())) : (e.on("keydown", (t => { if (f(t) || t.keyCode !== wg.BACKSPACE)
        return; let n = s.getRng(); const o = n.startContainer, a = n.startOffset, i = r.getRoot(); let l = o; if (n.collapsed && 0 === a) {
        for (; l.parentNode && l.parentNode.firstChild === l && l.parentNode !== i;)
            l = l.parentNode;
        "BLOCKQUOTE" === l.nodeName && (e.formatter.toggle("blockquote", void 0, l), n = r.createRng(), n.setStart(o, 0), n.setEnd(o, 0), s.setRng(n));
    } })), (() => { const t = e => { const t = r.create("body"), n = e.cloneContents(); return t.appendChild(n), s.serializer.serialize(t, { format: "html" }); }; e.on("keydown", (s => { const a = s.keyCode; if (!f(s) && (a === o || a === n) && e.selection.isEditable()) {
        const n = e.selection.isCollapsed(), o = e.getBody();
        if (n && !Es(e.schema, o))
            return;
        if (!n && !(n => { const o = t(n), s = r.createRng(); return s.selectNode(e.getBody()), o === t(s); })(e.selection.getRng()))
            return;
        s.preventDefault(), e.setContent(""), o.firstChild && r.isBlock(o.firstChild) ? e.selection.setCursorLocation(o.firstChild, 0) : e.selection.setCursorLocation(o, 0), e.nodeChanged();
    } })); })(), nn.windowsPhone || e.on("keyup focusin mouseup", (t => { wg.modifierPressed(t) || (e => { const t = e.getBody(), n = e.selection.getRng(); return n.startContainer === n.endContainer && n.startContainer === t && 0 === n.startOffset && n.endOffset === t.childNodes.length; })(e) || s.normalize(); }), !0), d && (p(), b(), e.on("init", (() => { m("DefaultParagraphSeparator", Ad(e)); })), k(), v(), a.addNodeFilter("br", (e => { let t = e.length; for (; t--;)
        "Apple-interchange-newline" === e[t].attr("class") && e[t].remove(); })), c ? (w(), E(), S()) : g()), l && (e.on("mousedown", (t => { ze(I.from(t.clientX), I.from(t.clientY), ((n, o) => { const r = e.getDoc().caretPositionFromPoint(n, o); if (r && "IMG" === (s = r.offsetNode).nodeName && e.dom.isEditable(s)) {
        const n = r.offsetNode.getBoundingClientRect();
        t.preventDefault(), e.hasFocus() || e.focus(), e.selection.select(r.offsetNode), t.clientX < n.left || t.clientY < n.top ? e.selection.collapse(!0) : (t.clientX > n.right || t.clientY > n.bottom) && e.selection.collapse(!1);
    } var s; })); })), e.on("keydown", (t => { if (!f(t) && t.keyCode === n) {
        if (!e.getBody().getElementsByTagName("hr").length)
            return;
        if (s.isCollapsed() && 0 === s.getRng().startOffset) {
            const e = s.getNode(), n = e.previousSibling;
            if ("HR" === e.nodeName)
                return r.remove(e), void t.preventDefault();
            n && n.nodeName && "hr" === n.nodeName.toLowerCase() && (r.remove(n), t.preventDefault());
        }
    } })), h(), (() => { const n = () => { const n = r.getAttribs(s.getStart().cloneNode(!1)); return () => { const o = s.getStart(); o !== e.getBody() && (r.setAttrib(o, "style", null), t(n, (e => { o.setAttributeNode(e.cloneNode(!0)); }))); }; }, o = () => !s.isCollapsed() && r.getParent(s.getStart(), r.isBlock) !== r.getParent(s.getEnd(), r.isBlock); e.on("keypress", (t => { let r; return !(!(f(t) || 8 !== t.keyCode && 46 !== t.keyCode) && o() && (r = n(), e.getDoc().execCommand("delete", !1), r(), t.preventDefault(), 1)); })), r.bind(e.getDoc(), "cut", (t => { if (!f(t) && o()) {
        const t = n();
        sg.setEditorTimeout(e, (() => { t(); }));
    } })); })(), y(), e.on("SetContent ExecCommand", (e => { "setcontent" !== e.type && "mceInsertLink" !== e.command || t(r.select("a:not([data-mce-block])"), (e => { var t; let n = e.parentNode; const o = r.getRoot(); if ((null == n ? void 0 : n.lastChild) === e) {
        for (; n && !r.isBlock(n);) {
            if ((null === (t = n.parentNode) || void 0 === t ? void 0 : t.lastChild) !== n || n === o)
                return;
            n = n.parentNode;
        }
        r.add(n, "br", { "data-mce-bogus": 1 });
    } })); })), C(), x(), v())), { refreshContentEditable: N, isHidden: () => { if (!l || e.removed)
            return !1; const t = e.selection.getSel(); return !t || !t.rangeCount || 0 === t.rangeCount; } }; }, uO = ni.DOM, mO = e => e.inline ? e.getElement().nodeName.toLowerCase() : void 0, fO = e => Ce(e, (e => !1 === v(e))), gO = e => { const t = e.options.get, n = e.editorUpload.blobCache; return fO({ allow_conditional_comments: t("allow_conditional_comments"), allow_html_data_urls: t("allow_html_data_urls"), allow_svg_data_urls: t("allow_svg_data_urls"), allow_html_in_named_anchor: t("allow_html_in_named_anchor"), allow_script_urls: t("allow_script_urls"), allow_mathml_annotation_encodings: t("allow_mathml_annotation_encodings"), allow_unsafe_link_target: t("allow_unsafe_link_target"), convert_unsafe_embeds: t("convert_unsafe_embeds"), convert_fonts_to_spans: t("convert_fonts_to_spans"), extended_mathml_attributes: t("extended_mathml_attributes"), extended_mathml_elements: t("extended_mathml_elements"), fix_list_elements: t("fix_list_elements"), font_size_legacy_values: t("font_size_legacy_values"), forced_root_block: t("forced_root_block"), forced_root_block_attrs: t("forced_root_block_attrs"), preserve_cdata: t("preserve_cdata"), inline_styles: t("inline_styles"), root_name: mO(e), sandbox_iframes: t("sandbox_iframes"), sandbox_iframes_exclusions: mu(e), sanitize: t("xss_sanitization"), validate: !0, blob_cache: n, document: e.getDoc() }); }, pO = e => { const t = e.options.get; return fO({ custom_elements: t("custom_elements"), extended_valid_elements: t("extended_valid_elements"), invalid_elements: t("invalid_elements"), invalid_styles: t("invalid_styles"), schema: t("schema"), valid_children: t("valid_children"), valid_classes: t("valid_classes"), valid_elements: t("valid_elements"), valid_styles: t("valid_styles"), verify_html: t("verify_html"), padd_empty_block_inline_children: t("format_empty_lines") }); }, hO = e => e.inline ? e.ui.styleSheetLoader : e.dom.styleSheetLoader, bO = e => { const t = hO(e), n = Jd(e), o = e.contentCSS, r = () => { t.unloadAll(o), e.inline || e.ui.styleSheetLoader.unloadAll(n); }, s = () => { e.removed ? r() : e.on("remove", r); }; if (e.contentStyles.length > 0) {
        let t = "";
        an.each(e.contentStyles, (e => { t += e + "\r\n"; })), e.dom.addStyle(t);
    } const a = Promise.all(((e, t, n) => { const { pass: o, fail: r } = K(t, (e => tinymce.Resource.has(_E(e)))), s = o.map((t => { const n = tinymce.Resource.get(_E(t)); return m(n) ? Promise.resolve(hO(e).loadRawCss(t, n)) : Promise.resolve(); })), a = [...s, hO(e).loadAll(r)]; return e.inline ? a : a.concat([e.ui.styleSheetLoader.loadAll(n)]); })(e, o, n)).then(s).catch(s), i = Qd(e); return i && ((e, t) => { const n = mn(e.getBody()), o = Xn(Yn(n)), r = cn("style"); go(r, "type", "text/css"), co(r, un(t)), co(o, r), e.on("remove", (() => { Eo(r); })); })(e, i), a; }, vO = e => { !0 !== e.removed && ((e => { kw(e) || e.load({ initial: !0, format: "html" }), e.startContent = e.getContent({ format: "raw" }); })(e), (e => { e.bindPendingEventDelegates(), e.initialized = !0, (e => { e.dispatch("Init"); })(e), e.focus(!0), (e => { const t = e.dom.getRoot(); e.inline || tf(e) && e.selection.getStart(!0) !== t || km(t).each((t => { const n = t.getNode(), o = Zr(n) ? km(n).getOr(t) : t; e.selection.setRng(o.toRange()); })); })(e), e.nodeChanged({ initial: !0 }); const t = Lc(e); w(t) && t.call(e, e), (e => { const t = Ic(e); t && sg.setEditorTimeout(e, (() => { let n; n = !0 === t ? e : e.editorManager.get(t), n && !n.destroyed && (n.focus(), n.selection.scrollIntoView()); }), 100); })(e), yE(e) && wE(e, !0); })(e)); }, yO = e => { const t = e.getElement(); let n = e.getDoc(); e.inline && (uO.addClass(t, "mce-content-body"), e.contentDocument = n = document, e.contentWindow = window, e.bodyElement = t, e.contentAreaContainer = t); const o = e.getBody(); o.disabled = !0, e.readonly = _c(e), e._editableRoot = Sc(e), !hu(e) && e.hasEditableRoot() && (e.inline && "static" === uO.getStyle(o, "position", !0) && (o.style.position = "relative"), o.contentEditable = "true"), o.disabled = !1, e.editorUpload = BE(e), e.schema = Ra(pO(e)), e.dom = ni(n, { keep_values: !0, url_converter: e.convertURL, url_converter_scope: e, update_styles: !0, root_element: e.inline ? e.getBody() : null, collect: e.inline, schema: e.schema, contentCssCors: qd(e), referrerPolicy: Wd(e), onSetAttrib: t => { e.dispatch("SetAttrib", t); } }), e.parser = (e => { const t = WC(gO(e), e.schema); return t.addAttributeFilter("src,href,style,tabindex", ((t, n) => { const o = e.dom, r = "data-mce-" + n; let s = t.length; for (; s--;) {
        const a = t[s];
        let i = a.attr(n);
        if (i && !a.attr(r)) {
            if (0 === i.indexOf("data:") || 0 === i.indexOf("blob:"))
                continue;
            "style" === n ? (i = o.serializeStyle(o.parseStyle(i), a.name), i.length || (i = null), a.attr(r, i), a.attr(n, i)) : "tabindex" === n ? (a.attr(r, i), a.attr(n, null)) : a.attr(r, e.convertURL(i, n, a.name));
        }
    } })), t.addNodeFilter("script", (e => { let t = e.length; for (; t--;) {
        const n = e[t], o = n.attr("type") || "no/type";
        0 !== o.indexOf("mce-") && n.attr("type", "mce-" + o);
    } })), ou(e) && t.addNodeFilter("#cdata", (t => { var n; let o = t.length; for (; o--;) {
        const r = t[o];
        r.type = 8, r.name = "#comment", r.value = "[CDATA[" + e.dom.encode(null !== (n = r.value) && void 0 !== n ? n : "") + "]]";
    } })), t.addNodeFilter("p,h1,h2,h3,h4,h5,h6,div", (t => { let n = t.length; const o = e.schema.getNonEmptyElements(); for (; n--;) {
        const e = t[n];
        e.isEmpty(o) && 0 === e.getAll("br").length && e.append(new up("br", 1));
    } })), t; })(e), e.serializer = Iw((e => { const t = e.options.get; return { ...gO(e), ...pO(e), ...fO({ remove_trailing_brs: t("remove_trailing_brs"), pad_empty_with_br: t("pad_empty_with_br"), url_converter: t("url_converter"), url_converter_scope: t("url_converter_scope"), element_format: t("element_format"), entities: t("entities"), entity_encoding: t("entity_encoding"), indent: t("indent"), indent_after: t("indent_after"), indent_before: t("indent_before") }) }; })(e), e), e.selection = Dw(e.dom, e.getWin(), e.serializer, e), e.annotator = Kf(e), e.formatter = $E(e), e.undoManager = qE(e), e._nodeChangeDispatcher = new fA(e), e._selectionOverrides = TT(e), (e => { const t = Ve(), n = Ne(!1), o = rt((t => { e.dispatch("longpress", { ...t, type: "longpress" }), n.set(!0); }), 400); e.on("touchstart", (e => { wS(e).each((r => { o.cancel(); const s = { x: r.clientX, y: r.clientY, target: e.target }; o.throttle(e), n.set(!1), t.set(s); })); }), !0), e.on("touchmove", (r => { o.cancel(), wS(r).each((o => { t.on((r => { ((e, t) => { const n = Math.abs(e.clientX - t.x), o = Math.abs(e.clientY - t.y); return n > 5 || o > 5; })(o, r) && (t.clear(), n.set(!1), e.dispatch("longpresscancel")); })); })); }), !0), e.on("touchend touchcancel", (r => { o.cancel(), "touchcancel" !== r.type && t.get().filter((e => e.target.isEqualNode(r.target))).each((() => { n.get() ? r.preventDefault() : e.dispatch("tap", { ...r, type: "tap" }); })); }), !0); })(e), (e => { (e => { e.on("click", (t => { e.dom.getParent(t.target, "details") && t.preventDefault(); })); })(e), (e => { e.parser.addNodeFilter("details", (t => { const n = du(e); q(t, (e => { "expanded" === n ? e.attr("open", "open") : "collapsed" === n && e.attr("open", null); })); })), e.serializer.addNodeFilter("details", (t => { const n = cu(e); q(t, (e => { "expanded" === n ? e.attr("open", "open") : "collapsed" === n && e.attr("open", null); })); })); })(e); })(e), (e => { const t = "contenteditable", n = " " + an.trim(tu(e)) + " ", o = " " + an.trim(eu(e)) + " ", r = NS(n), s = NS(o), a = nu(e); a.length > 0 && e.on("BeforeSetContent", (t => { ((e, t, n) => { let o = t.length, r = n.content; if ("raw" !== n.format) {
        for (; o--;)
            r = r.replace(t[o], RS(e, r, eu(e)));
        n.content = r;
    } })(e, a, t); })), e.parser.addAttributeFilter("class", (e => { let n = e.length; for (; n--;) {
        const o = e[n];
        r(o) ? o.attr(t, "true") : s(o) && o.attr(t, "false");
    } })), e.serializer.addAttributeFilter(t, (e => { let n = e.length; for (; n--;) {
        const o = e[n];
        if (!r(o) && !s(o))
            continue;
        const i = o.attr("data-mce-content");
        a.length > 0 && i ? AS(a, i) ? (o.name = "#text", o.type = 3, o.raw = !0, o.value = i) : o.remove() : o.attr(t, null);
    } })); })(e), kw(e) || ((e => { e.on("mousedown", (t => { t.detail >= 3 && (t.preventDefault(), oT(e)); })); })(e), (e => { dO(e); })(e)); const r = mA(e); ((e, t) => { e.addCommand("delete", (() => { ((e, t) => { CS(e, t, !1).fold((() => { e.selection.isEditable() && (Kh(e), Zh(e)); }), D); })(e, t); })), e.addCommand("forwardDelete", (() => { ((e, t) => { CS(e, t, !0).fold((() => { e.selection.isEditable() && Yh(e); }), D); })(e, t); })); })(e, r), (e => { e.on("NodeChange", (() => (e => { const t = e.dom, n = e.selection, o = e.schema, r = o.getBlockElements(), s = n.getStart(), a = e.getBody(); let i, l, d = null; const c = Ad(e); if (!s || !qr(s))
        return; const u = a.nodeName.toLowerCase(); if (!o.isValidChild(u, c.toLowerCase()) || ((e, t, n) => $(Zp(mn(n), mn(t)), (t => ES(e, t.dom))))(r, a, s))
        return; if (a.firstChild === a.lastChild && as(a.firstChild))
        return i = SS(e), i.appendChild(Oi().dom), a.replaceChild(i, a.firstChild), e.selection.setCursorLocation(i, 0), void e.nodeChanged(); let m = a.firstChild; for (; m;)
        if (qr(m) && Is(o, m), xS(o, m)) {
            if (_S(r, m)) {
                l = m, m = m.nextSibling, t.remove(l);
                continue;
            }
            if (!i) {
                if (!d && e.hasFocus() && (d = Nx(e.selection.getRng(), (() => document.createElement("span")))), !m.parentNode) {
                    m = null;
                    break;
                }
                i = SS(e), a.insertBefore(i, m);
            }
            l = m, m = m.nextSibling, i.appendChild(l);
        }
        else
            i = null, m = m.nextSibling; d && (e.selection.setRng(Rx(d)), e.nodeChanged()); })(e))); })(e), (e => { var t; const n = e.dom, o = Ad(e), r = null !== (t = rc(e)) && void 0 !== t ? t : "", s = (t, a) => { if ((e => { if (YE(e)) {
        const t = e.keyCode;
        return !XE(e) && (wg.metaKeyPressed(e) || e.altKey || t >= 112 && t <= 123 || H(WE, t));
    } return !1; })(t))
        return; const i = e.getBody(), l = !(e => YE(e) && !(XE(e) || "keyup" === e.type && 229 === e.keyCode))(t) && ((e, t, n) => { if (e.isEmpty(t, void 0, { skipBogus: !1, includeZwsp: !0 })) {
        const o = t.firstElementChild;
        return !o || !e.getStyle(t.firstElementChild, "padding-left") && !e.getStyle(t.firstElementChild, "padding-right") && n === o.nodeName.toLowerCase();
    } return !1; })(n, i, o); ("" !== n.getAttrib(i, KE) !== l || a) && (n.setAttrib(i, KE, l ? r : null), ((e, t) => { e.dispatch("PlaceholderToggle", { state: t }); })(e, l), e.on(l ? "keydown" : "keyup", s), e.off(l ? "keyup" : "keydown", s)); }; et(r) && e.on("init", (t => { s(t, !0), e.on("change SetContent ExecCommand", s), e.on("paste", (t => sg.setEditorTimeout(e, (() => s(t))))); })); })(e), GA(e); const s = (e => { const t = e; return (e => xe(e.plugins, "rtc").bind((e => I.from(e.setup))))(e).fold((() => (t.rtcInstance = Sw(e), I.none())), (e => (t.rtcInstance = (() => { const e = N(null), t = N(""); return { init: { bindEvents: _ }, undoManager: { beforeChange: _, add: e, undo: e, redo: e, clear: _, reset: _, hasUndo: L, hasRedo: L, transact: e, ignore: _, extra: _ }, formatter: { match: L, matchAll: N([]), matchNode: N(void 0), canApply: L, closest: t, apply: _, remove: _, toggle: _, formatChanged: N({ unbind: _ }) }, editor: { getContent: t, setContent: N({ content: "", html: "" }), insertContent: N(""), addVisual: _ }, selection: { getContent: t }, autocompleter: { addDecoration: _, removeDecoration: _ }, raw: { getModel: N(I.none()) } }; })(), I.some((() => e().then((e => (t.rtcInstance = (e => { const t = e => f(e) ? e : {}, { init: n, undoManager: o, formatter: r, editor: s, selection: a, autocompleter: i, raw: l } = e; return { init: { bindEvents: n.bindEvents }, undoManager: { beforeChange: o.beforeChange, add: o.add, undo: o.undo, redo: o.redo, clear: o.clear, reset: o.reset, hasUndo: o.hasUndo, hasRedo: o.hasRedo, transact: (e, t, n) => o.transact(n), ignore: (e, t) => o.ignore(t), extra: (e, t, n, r) => o.extra(n, r) }, formatter: { match: (e, n, o, s) => r.match(e, t(n), s), matchAll: r.matchAll, matchNode: r.matchNode, canApply: e => r.canApply(e), closest: e => r.closest(e), apply: (e, n, o) => r.apply(e, t(n)), remove: (e, n, o, s) => r.remove(e, t(n)), toggle: (e, n, o) => r.toggle(e, t(n)), formatChanged: (e, t, n, o, s) => r.formatChanged(t, n, o, s) }, editor: { getContent: e => s.getContent(e), setContent: (e, t) => ({ content: s.setContent(e, t), html: "" }), insertContent: (e, t) => (s.insertContent(e), ""), addVisual: s.addVisual }, selection: { getContent: (e, t) => a.getContent(t) }, autocompleter: { addDecoration: i.addDecoration, removeDecoration: i.removeDecoration }, raw: { getModel: () => I.some(l.getRawModel()) } }; })(e), e.rtc.isRemote)))))))); })(e); (e => { const t = e.getDoc(), n = e.getBody(); (e => { e.dispatch("PreInit"); })(e), Fc(e) || (t.body.spellcheck = !1, uO.setAttrib(n, "spellcheck", "false")), e.quirks = cO(e), (e => { e.dispatch("PostRender"); })(e); const o = ec(e); void 0 !== o && (n.dir = o); const r = Uc(e); r && e.on("BeforeSetContent", (e => { an.each(r, (t => { e.content = e.content.replace(t, (e => "\x3c!--mce:protected " + escape(e) + "--\x3e")); })); })), e.on("SetContent", (() => { e.addVisual(e.getBody()); })), e.on("compositionstart compositionend", (t => { e.composing = "compositionstart" === t.type; })); })(e), (e => { const t = gu(e); var n; m(pu(e)) || !v(t) && "INVALID" != ((e => "gpl" === e.toLowerCase())(n = t) || (e => e.length >= 64 && e.length <= 255)(n) ? "VALID" : "INVALID") || console.warn("TinyMCE is running in evaluation mode. Provide a valid license key or add license_key: 'gpl' to the init config to agree to the open source license terms. Read more at https://www.tiny.cloud/license-key/"); })(e), s.fold((() => { const t = (e => { let t = !1; const n = setTimeout((() => { t || e.setProgressState(!0); }), 500); return () => { clearTimeout(n), t = !0, e.setProgressState(!1); }; })(e); bO(e).then((() => { vO(e), t(); })); }), (t => { e.setProgressState(!0), bO(e).then((() => { t().then((t => { e.setProgressState(!1), vO(e), Aw(e); }), (t => { e.notificationManager.open({ type: "error", text: String(t) }), vO(e), Aw(e); })); })); })); }, CO = ni.DOM, wO = ni.DOM, EO = (e, t) => ({ editorContainer: e, iframeContainer: t, api: {} }), xO = e => { const t = e.getElement(); return e.inline ? EO(null) : (e => { const t = wO.create("div"); return wO.insertAfter(t, e), EO(t, t); })(t); }, _O = async (e) => { e.dispatch("ScriptsLoaded"), (e => { const t = an.trim(Ud(e)), n = e.ui.registry.getAll().icons, o = { ...Qw.get("default").icons, ...Qw.get(t).icons }; pe(o, ((t, o) => { _e(n, o) || e.ui.registry.addIcon(o, t); })); })(e), (e => { const t = ic(e); if (m(t)) {
        const n = lE.get(t);
        e.theme = n(e, lE.urls[t]) || {}, w(e.theme.init) && e.theme.init(e, lE.urls[t] || e.documentBaseUrl.replace(/\/$/, ""));
    }
    else
        e.theme = {}; })(e), (e => { const t = dc(e), n = Jw.get(t); e.model = n(e, Jw.urls[t]); })(e), (e => { const t = []; q(Nc(e), (n => { ((e, t, n) => { const o = iE.get(n), r = iE.urls[n] || e.documentBaseUrl.replace(/\/$/, ""); if (n = an.trim(n), o && -1 === an.inArray(t, n)) {
        if (e.plugins[n])
            return;
        try {
            const s = o(e, r) || {};
            e.plugins[n] = s, w(s.init) && (s.init(e, r), t.push(n));
        }
        catch (t) {
            ((e, t, n) => { const o = li.translate(["Failed to initialize plugin: {0}", t]); ld(e, "PluginLoadError", { message: o }), gE(o, n), uE(e, o); })(e, n, t);
        }
    } })(e, t, (e => e.replace(/^\-/, ""))(n)); })); })(e); const t = await (e => { const t = e.getElement(); return e.orgDisplay = t.style.display, m(ic(e)) ? (e => { const t = e.theme.renderUI; return t ? t() : xO(e); })(e) : w(ic(e)) ? (e => { const t = e.getElement(), n = ic(e)(e, t); return n.editorContainer.nodeType && (n.editorContainer.id = n.editorContainer.id || e.id + "_parent"), n.iframeContainer && n.iframeContainer.nodeType && (n.iframeContainer.id = n.iframeContainer.id || e.id + "_iframecontainer"), n.height = n.iframeHeight ? n.iframeHeight : t.offsetHeight, n; })(e) : xO(e); })(e); ((e, t) => { const n = { show: I.from(t.show).getOr(_), hide: I.from(t.hide).getOr(_), isEnabled: I.from(t.isEnabled).getOr(M), setEnabled: n => { n && ("readonly" === e.mode.get() || yE(e)) || I.from(t.setEnabled).each((e => e(n))); } }; e.ui = { ...e.ui, ...n }; })(e, I.from(t.api).getOr({})), e.editorContainer = t.editorContainer, (e => { e.contentCSS = e.contentCSS.concat((e => SE(e, Zd(e)))(e), (e => SE(e, Jd(e)))(e)); })(e), e.inline ? yO(e) : ((e, t) => { ((e, t) => { const n = nn.browser.isFirefox() ? Pc(e) : "Rich Text Area", o = e.translate(n), r = bo(mn(e.getElement()), "tabindex").bind(nt), s = ((e, t, n, o) => { const r = cn("iframe"); return o.each((e => go(r, "tabindex", e))), po(r, n), po(r, { id: e + "_ifr", frameBorder: "0", allowTransparency: "true", title: t }), cr(r, "tox-edit-area__iframe"), r; })(e.id, o, Ed(e), r).dom; s.onload = () => { s.onload = null, e.dispatch("load"); }, e.contentAreaContainer = t.iframeContainer, e.iframeElement = s, e.iframeHTML = (e => { let t = xd(e) + "<html><head>"; _d(e) !== e.documentBaseUrl && (t += '<base href="' + e.documentBaseURI.getURI() + '" />'), t += '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'; const n = Sd(e), o = kd(e), r = e.translate(Pc(e)); return Nd(e) && (t += '<meta http-equiv="Content-Security-Policy" content="' + Nd(e) + '" />'), t += `</head><body id="${n}" class="mce-content-body ${o}" data-id="${e.id}" aria-label="${r}"><br></body></html>`, t; })(e), CO.add(t.iframeContainer, s); })(e, t), t.editorContainer && (t.editorContainer.style.display = e.orgDisplay, e.hidden = CO.isHidden(t.editorContainer)), e.getElement().style.display = "none", CO.setAttrib(e.id, "aria-hidden", "true"), e.getElement().style.visibility = e.orgVisibility, (e => { const t = e.iframeElement, n = () => { e.contentDocument = t.contentDocument, yO(e); }; if (au(e) || nn.browser.isFirefox()) {
        const t = e.getDoc();
        t.open(), t.write(e.iframeHTML), t.close(), n();
    }
    else {
        const r = (o = mn(t), Jn(o, "load", to, (() => { r.unbind(), n(); })));
        t.srcdoc = e.iframeHTML;
    } var o; })(e); })(e, { editorContainer: t.editorContainer, iframeContainer: t.iframeContainer }); }, SO = ni.DOM, kO = e => "-" === e.charAt(0), NO = (e, t, n) => I.from(t).filter((e => et(e) && !Qw.has(e))).map((t => ({ url: `${e.editorManager.baseURL}/icons/${t}/icons${n}.js`, name: I.some(t) }))), RO = (e, t) => { const n = ri.ScriptLoader, o = () => { !e.removed && (e => { const t = ic(e); return !m(t) || C(lE.get(t)); })(e) && (e => { const t = dc(e); return C(Jw.get(t)); })(e) && _O(e); }; ((e, t) => { const n = ic(e); if (m(n) && !kO(n) && !_e(lE.urls, n)) {
        const o = lc(e), r = o ? e.documentBaseURI.toAbsolute(o) : `themes/${n}/theme${t}.js`;
        lE.load(n, r).catch((() => { ((e, t, n) => { mE(e, "ThemeLoadError", fE("theme", t, n)); })(e, r, n); }));
    } })(e, t), ((e, t) => { const n = dc(e); if ("plugin" !== n && !_e(Jw.urls, n)) {
        const o = cc(e), r = m(o) ? e.documentBaseURI.toAbsolute(o) : `models/${n}/model${t}.js`;
        Jw.load(n, r).catch((() => { ((e, t, n) => { mE(e, "ModelLoadError", fE("model", t, n)); })(e, r, n); }));
    } })(e, t), ((e, t) => { const n = Kd(t), o = Yd(t); if (!li.hasCode(n) && "en" !== n) {
        const r = et(o) ? o : `${t.editorManager.baseURL}/langs/${n}.js`;
        e.add(r).catch((() => { ((e, t, n) => { mE(e, "LanguageLoadError", fE("language", t, n)); })(t, r, n); }));
    } })(n, e), ((e, t, n) => { const o = NO(t, "default", n), r = (e => I.from(zd(e)).filter(et).map((e => ({ url: e, name: I.none() }))))(t).orThunk((() => NO(t, Ud(t), ""))); q((e => { const t = [], n = e => { t.push(e); }; for (let t = 0; t < e.length; t++)
        e[t].each(n); return t; })([o, r]), (n => { e.add(n.url).catch((() => { ((e, t, n) => { mE(e, "IconsLoadError", fE("icons", t, n)); })(t, n.url, n.name.getOrUndefined()); })); })); })(n, e, t), ((e, t) => { const n = (t, n) => { iE.load(t, n).catch((() => { ((e, t, n) => { mE(e, "PluginLoadError", fE("plugin", t, n)); })(e, n, t); })); }; pe(Rc(e), ((t, o) => { n(o, t), e.options.set("plugins", Nc(e).concat(o)); })), q(Nc(e), (e => { !(e = an.trim(e)) || iE.urls[e] || kO(e) || n(e, `plugins/${e}/plugin${t}.js`); })); })(e, t), n.loadQueue().then(o, o); }, AO = Kt().deviceType, TO = AO.isPhone(), OO = AO.isTablet(), BO = e => { if (y(e))
        return []; {
        const t = p(e) ? e : e.split(/[ ,]/), n = V(t, Ze);
        return Y(n, et);
    } }, PO = (e, t) => { const n = (t => { const n = {}, o = {}; return ye(t, ((t, n) => H(e, n)), ve(n), ve(o)), { t: n, f: o }; })(t); return o = n.t, r = n.f, { sections: N(o), options: N(r) }; var o, r; }, DO = (e, t) => _e(e.sections(), t), LO = (e, t) => ({ table_grid: !1, object_resizing: !1, resize: !1, toolbar_mode: xe(e, "toolbar_mode").getOr("scrolling"), toolbar_sticky: !1, ...t ? { menubar: !1 } : {} }), MO = (e, t) => { var n; const o = null !== (n = t.external_plugins) && void 0 !== n ? n : {}; return e && e.external_plugins ? an.extend({}, e.external_plugins, o) : o; }, IO = (e, t, n, o, r) => { var s; const a = e ? { mobile: LO(null !== (s = r.mobile) && void 0 !== s ? s : {}, t) } : {}, i = PO(["mobile"], Me(a, r)), l = an.extend(n, o, i.options(), ((e, t) => e && DO(t, "mobile"))(e, i) ? ((e, t, n = {}) => { const o = e.sections(), r = xe(o, t).getOr({}); return an.extend({}, n, r); })(i, "mobile") : {}, { external_plugins: MO(o, i.options()) }); return ((e, t, n, o) => { const r = BO(n.forced_plugins), s = BO(o.plugins), a = ((e, t) => DO(e, t) ? e.sections()[t] : {})(t, "mobile"), i = ((e, t, n, o) => e && DO(t, "mobile") ? o : n)(e, t, s, a.plugins ? BO(a.plugins) : s), l = ((e, t) => [...BO(e), ...BO(t)])(r, i); return an.extend(o, { forced_plugins: r, plugins: l }); })(e, i, o, l); }, FO = e => { (e => { const t = t => () => { q("left,center,right,justify".split(","), (n => { t !== n && e.formatter.remove("align" + n); })), "none" !== t && (t => { e.formatter.toggle(t, void 0), e.nodeChanged(); })("align" + t); }; e.editorCommands.addCommands({ JustifyLeft: t("left"), JustifyCenter: t("center"), JustifyRight: t("right"), JustifyFull: t("justify"), JustifyNone: t("none") }); })(e), (e => { const t = t => () => { const n = e.selection, o = n.isCollapsed() ? [e.dom.getParent(n.getNode(), e.dom.isBlock)] : n.getSelectedBlocks(); return $(o, (n => C(e.formatter.matchNode(n, t)))); }; e.editorCommands.addCommands({ JustifyLeft: t("alignleft"), JustifyCenter: t("aligncenter"), JustifyRight: t("alignright"), JustifyFull: t("alignjustify") }, "state"); })(e); }, UO = (e, t) => { const n = e.selection, o = e.dom; return /^ | $/.test(t) ? ((e, t, n, o) => { const r = mn(e.getRoot()); return n = Ch(r, Pl.fromRangeStart(t), o) ? n.replace(/^ /, "&nbsp;") : n.replace(/^&nbsp;/, " "), wh(r, Pl.fromRangeEnd(t), o) ? n.replace(/(&nbsp;| )(<br( \/)>)?$/, "&nbsp;") : n.replace(/&nbsp;(<br( \/)?>)?$/, " "); })(o, n.getRng(), t, e.schema) : t; }, zO = (e, t) => { if (e.selection.isEditable()) {
        const { content: n, details: o } = (e => { if ("string" != typeof e) {
            const t = an.extend({ paste: e.paste, data: { paste: e.paste } }, e);
            return { content: e.content, details: t };
        } return { content: e, details: {} }; })(t);
        ZC(e, { ...o, content: UO(e, n), format: "html", set: !1, selection: !0 }).each((t => { const n = ((e, t, n) => Nw(e).editor.insertContent(t, n))(e, t.content, o); QC(e, n, t), e.addVisual(); }));
    } }, jO = { "font-size": "size", "font-family": "face" }, HO = On("font"), $O = e => (t, n) => I.from(n).map(mn).filter(Nn).bind((n => ((e, t, n) => xr(mn(n), (t => (t => Io(t, e).orThunk((() => HO(t) ? xe(jO, e).bind((e => bo(t, e))) : I.none())))(t)), (e => Cn(mn(t), e))))(e, t, n.dom).or(((e, t) => I.from(ni.DOM.getStyle(t, e, !0)))(e, n.dom)))).getOr(""), VO = $O("font-size"), qO = S((e => e.replace(/[\'\"\\]/g, "").replace(/,\s+/g, ",")), $O("font-family")), WO = e => km(e.getBody()).bind((e => { const t = e.container(); return I.from(es(t) ? t.parentNode : t); })), KO = (e, t) => ((e, t) => (e => I.from(e.selection.getRng()).bind((t => { const n = e.getBody(); return t.startContainer === n && 0 === t.startOffset ? I.none() : I.from(e.selection.getStart(!0)); })))(e).orThunk(T(WO, e)).map(mn).filter(Nn).bind(t))(e, k(I.some, t)), YO = (e, t) => { if (/^[0-9.]+$/.test(t)) {
        const n = parseInt(t, 10);
        if (n >= 1 && n <= 7) {
            const o = (e => an.explode(e.options.get("font_size_style_values")))(e), r = (e => an.explode(e.options.get("font_size_classes")))(e);
            return r.length > 0 ? r[n - 1] || t : o[n - 1] || t;
        }
        return t;
    } return t; }, XO = e => { const t = e.split(/\s*,\s*/); return V(t, (e => -1 === e.indexOf(" ") || Ye(e, '"') || Ye(e, "'") ? e : `'${e}'`)).join(","); }, GO = (e, t) => { if (e.mode.isReadOnly())
        return; const n = e.dom, o = e.selection.getRng(), r = t ? e.selection.getStart() : e.selection.getEnd(), s = t ? o.startContainer : o.endContainer, a = mR(n, s); if (!a || !a.isContentEditable)
        return; const i = t ? ao : io, l = Ad(e); ((e, t, n, o) => { const r = e.dom, s = e => r.isBlock(e) && e.parentElement === n, a = s(t) ? t : r.getParent(o, s, n); return I.from(a).map(mn); })(e, r, a, s).each((t => { const n = hR(e, s, t.dom, a, !1, l); i(t, mn(n)), e.selection.setCursorLocation(n, 0), e.dispatch("NewBlock", { newBlock: n }), sR(e, "insertParagraph"); })); }, ZO = e => { FO(e), (e => { e.editorCommands.addCommands({ "Cut,Copy,Paste": t => { const n = e.getDoc(); let o; try {
            n.execCommand(t);
        }
        catch (e) {
            o = !0;
        } if ("paste" !== t || n.queryCommandEnabled(t) || (o = !0), o || !n.queryCommandSupported(t)) {
            let t = e.translate("Your browser doesn't support direct access to the clipboard. Please use the Ctrl+X/C/V keyboard shortcuts instead.");
            (nn.os.isMacOS() || nn.os.isiOS()) && (t = t.replace(/Ctrl\+/g, "\u2318+")), e.notificationManager.open({ text: t, type: "error" });
        } } }); })(e), (e => { e.editorCommands.addCommands({ mceAddUndoLevel: () => { e.undoManager.add(); }, mceEndUndoLevel: () => { e.undoManager.add(); }, Undo: () => { e.undoManager.undo(); }, Redo: () => { e.undoManager.redo(); } }); })(e), (e => { e.editorCommands.addCommands({ mceSelectNodeDepth: (t, n, o) => { let r = 0; e.dom.getParent(e.selection.getNode(), (t => !qr(t) || r++ !== o || (e.selection.select(t), !1)), e.getBody()); }, mceSelectNode: (t, n, o) => { e.selection.select(o); }, selectAll: () => { const t = e.dom.getParent(e.selection.getStart(), ds); if (t) {
            const n = e.dom.createRng();
            n.selectNodeContents(t), e.selection.setRng(n);
        } } }); })(e), (e => { e.editorCommands.addCommands({ mceCleanup: () => { const t = e.selection.getBookmark(); e.setContent(e.getContent()), e.selection.moveToBookmark(t); }, insertImage: (t, n, o) => { zO(e, e.dom.createHTML("img", { src: o })); }, insertHorizontalRule: () => { e.execCommand("mceInsertContent", !1, "<hr>"); }, insertText: (t, n, o) => { zO(e, e.dom.encode(o)); }, insertHTML: (t, n, o) => { zO(e, o); }, mceInsertContent: (t, n, o) => { zO(e, o); }, mceSetContent: (t, n, o) => { e.setContent(o); }, mceReplaceContent: (t, n, o) => { e.execCommand("mceInsertContent", !1, o.replace(/\{\$selection\}/g, e.selection.getContent({ format: "text" }))); }, mceNewDocument: () => { e.setContent(Vc(e)); } }); })(e), (e => { const t = (t, n, o) => { if (e.mode.isReadOnly())
        return; const r = m(o) ? { href: o } : o, s = e.dom.getParent(e.selection.getNode(), "a"); f(r) && m(r.href) && (r.href = r.href.replace(/ /g, "%20"), s && r.href || e.formatter.remove("link"), r.href && e.formatter.apply("link", r, s)); }; e.editorCommands.addCommands({ unlink: () => { if (e.selection.isEditable()) {
            if (e.selection.isCollapsed()) {
                const t = e.dom.getParent(e.selection.getStart(), "a");
                return void (t && e.dom.remove(t, !0));
            }
            e.formatter.remove("link");
        } }, mceInsertLink: t, createLink: t }); })(e), (e => { e.editorCommands.addCommands({ Indent: () => { (e => { bS(e, "indent"); })(e); }, Outdent: () => { vS(e); } }), e.editorCommands.addCommands({ Outdent: () => gS(e) }, "state"); })(e), (e => { e.editorCommands.addCommands({ InsertNewBlockBefore: () => { (e => { GO(e, !0); })(e); }, InsertNewBlockAfter: () => { (e => { GO(e, !1); })(e); } }); })(e), (e => { e.editorCommands.addCommands({ insertParagraph: () => { XR(NR, e); }, mceInsertNewLine: (t, n, o) => { GR(e, o); }, InsertLineBreak: (t, n, o) => { XR(LR, e); } }); })(e), (e => { (e => { const t = (t, n) => { e.formatter.toggle(t, n), e.nodeChanged(); }; e.editorCommands.addCommands({ "Bold,Italic,Underline,Strikethrough,Superscript,Subscript": e => { t(e); }, "ForeColor,HiliteColor": (e, n, o) => { t(e, { value: o }); }, BackColor: (e, n, o) => { t("hilitecolor", { value: o }); }, FontName: (t, n, o) => { ((e, t) => { const n = YO(e, t); e.formatter.toggle("fontname", { value: XO(n) }), e.nodeChanged(); })(e, o); }, FontSize: (t, n, o) => { ((e, t) => { e.formatter.toggle("fontsize", { value: YO(e, t) }), e.nodeChanged(); })(e, o); }, LineHeight: (t, n, o) => { ((e, t) => { e.formatter.toggle("lineheight", { value: String(t) }), e.nodeChanged(); })(e, o); }, Lang: (e, n, o) => { var r; t(e, { value: o.code, customValue: null !== (r = o.customCode) && void 0 !== r ? r : null }); }, RemoveFormat: t => { e.formatter.remove(t); }, mceBlockQuote: () => { t("blockquote"); }, FormatBlock: (e, n, o) => { t(m(o) ? o : "p"); }, mceToggleFormat: (e, n, o) => { t(o); } }); })(e), (e => { const t = t => e.formatter.match(t); e.editorCommands.addCommands({ "Bold,Italic,Underline,Strikethrough,Superscript,Subscript": e => t(e), mceBlockQuote: () => t("blockquote") }, "state"), e.editorCommands.addQueryValueHandler("FontName", (() => (e => KO(e, (t => qO(e.getBody(), t.dom))).getOr(""))(e))), e.editorCommands.addQueryValueHandler("FontSize", (() => (e => KO(e, (t => VO(e.getBody(), t.dom))).getOr(""))(e))), e.editorCommands.addQueryValueHandler("LineHeight", (() => (e => KO(e, (t => { const n = mn(e.getBody()), o = xr(t, (e => Io(e, "line-height")), T(Cn, n)); return o.getOrThunk((() => { const e = parseFloat(Lo(t, "line-height")), n = parseFloat(Lo(t, "font-size")); return String(e / n); })); })).getOr(""))(e))); })(e); })(e), (e => { e.editorCommands.addCommands({ mceRemoveNode: (t, n, o) => { const r = null != o ? o : e.selection.getNode(); if (r !== e.getBody()) {
            const t = e.selection.getBookmark();
            e.dom.remove(r, !0), e.selection.moveToBookmark(t);
        } }, mcePrint: () => { e.getWin().print(); }, mceFocus: (t, n, o) => { ((e, t) => { e.removed || (t ? Cg(e) : (e => { const t = e.selection, n = e.getBody(); let o = t.getRng(); e.quirks.refreshContentEditable(); const r = e => { og(e).each((t => { e.selection.setRng(t), o = t; })); }; !vg(e) && e.hasEditableRoot() && r(e); const s = ((e, t) => e.dom.getParent(t, (t => "true" === e.dom.getContentEditable(t))))(e, t.getNode()); if (s && e.dom.isChildOf(s, n))
            return ((e, t) => null !== e.dom.getParent(t, (t => "false" === e.dom.getContentEditable(t))))(e, s) || bg(n), bg(s), e.hasEditableRoot() || r(e), hg(e, o), void Cg(e); e.inline || (nn.browser.isOpera() || bg(n), e.getWin().focus()), (nn.browser.isFirefox() || e.inline) && (bg(n), hg(e, o)), Cg(e); })(e)); })(e, !0 === o); }, mceToggleVisualAid: () => { e.hasVisual = !e.hasVisual, e.addVisual(); } }); })(e); }, QO = ["toggleview"], JO = e => H(QO, e.toLowerCase());
    class eB {
        constructor(e) { this.commands = { state: {}, exec: {}, value: {} }, this.editor = e; }
        execCommand(e, t = !1, n, o) { const r = this.editor, s = e.toLowerCase(), a = null == o ? void 0 : o.skip_focus; if (r.removed)
            return !1; if ("mcefocus" !== s && (/^(mceAddUndoLevel|mceEndUndoLevel)$/i.test(s) || a ? (e => { og(e).each((t => e.selection.setRng(t))); })(r) : r.focus()), r.dispatch("BeforeExecCommand", { command: e, ui: t, value: n }).isDefaultPrevented())
            return !1; const i = this.commands.exec[s]; return !!w(i) && (i(s, t, n), r.dispatch("ExecCommand", { command: e, ui: t, value: n }), !0); }
        queryCommandState(e) { if (!JO(e) && this.editor.quirks.isHidden() || this.editor.removed)
            return !1; const t = e.toLowerCase(), n = this.commands.state[t]; return !!w(n) && n(t); }
        queryCommandValue(e) { if (!JO(e) && this.editor.quirks.isHidden() || this.editor.removed)
            return ""; const t = e.toLowerCase(), n = this.commands.value[t]; return w(n) ? n(t) : ""; }
        addCommands(e, t = "exec") { const n = this.commands; pe(e, ((e, o) => { q(o.toLowerCase().split(","), (o => { n[t][o] = e; })); })); }
        addCommand(e, t, n) { const o = e.toLowerCase(); this.commands.exec[o] = (e, o, r) => t.call(null != n ? n : this.editor, o, r); }
        queryCommandSupported(e) { const t = e.toLowerCase(); return !!this.commands.exec[t]; }
        addQueryStateHandler(e, t, n) { this.commands.state[e.toLowerCase()] = () => t.call(null != n ? n : this.editor); }
        addQueryValueHandler(e, t, n) { this.commands.value[e.toLowerCase()] = () => t.call(null != n ? n : this.editor); }
    }
    const tB = an.makeMap("focus blur focusin focusout click dblclick mousedown mouseup mousemove mouseover beforepaste paste cut copy selectionchange mouseout mouseenter mouseleave wheel keydown keypress keyup input beforeinput contextmenu dragstart dragend dragover draggesture dragdrop drop drag submit compositionstart compositionend compositionupdate touchstart touchmove touchend touchcancel", " ");
    class nB {
        static isNative(e) { return !!tB[e.toLowerCase()]; }
        constructor(e) { this.bindings = {}, this.settings = e || {}, this.scope = this.settings.scope || this, this.toggleEvent = this.settings.toggleEvent || L; }
        fire(e, t) { return this.dispatch(e, t); }
        dispatch(e, t) { const n = e.toLowerCase(), o = za(n, null != t ? t : {}, this.scope); this.settings.beforeFire && this.settings.beforeFire(o); const r = this.bindings[n]; if (r)
            for (let e = 0, t = r.length; e < t; e++) {
                const t = r[e];
                if (!t.removed) {
                    if (t.once && this.off(n, t.func), o.isImmediatePropagationStopped())
                        return o;
                    if (!1 === t.func.call(this.scope, o))
                        return o.preventDefault(), o;
                }
            } return o; }
        on(e, t, n, o) { if (!1 === t && (t = L), t) {
            const r = { func: t, removed: !1 };
            o && an.extend(r, o);
            const s = e.toLowerCase().split(" ");
            let a = s.length;
            for (; a--;) {
                const e = s[a];
                let t = this.bindings[e];
                t || (t = [], this.toggleEvent(e, !0)), t = n ? [r, ...t] : [...t, r], this.bindings[e] = t;
            }
        } return this; }
        off(e, t) { if (e) {
            const n = e.toLowerCase().split(" ");
            let o = n.length;
            for (; o--;) {
                const r = n[o];
                let s = this.bindings[r];
                if (!r)
                    return pe(this.bindings, ((e, t) => { this.toggleEvent(t, !1), delete this.bindings[t]; })), this;
                if (s) {
                    if (t) {
                        const e = K(s, (e => e.func === t));
                        s = e.fail, this.bindings[r] = s, q(e.pass, (e => { e.removed = !0; }));
                    }
                    else
                        s.length = 0;
                    s.length || (this.toggleEvent(e, !1), delete this.bindings[r]);
                }
            }
        }
        else
            pe(this.bindings, ((e, t) => { this.toggleEvent(t, !1); })), this.bindings = {}; return this; }
        once(e, t, n) { return this.on(e, t, n, { once: !0 }); }
        has(e) { e = e.toLowerCase(); const t = this.bindings[e]; return !(!t || 0 === t.length); }
    }
    const oB = e => (e._eventDispatcher || (e._eventDispatcher = new nB({ scope: e, toggleEvent: (t, n) => { nB.isNative(t) && e.toggleNativeEvent && e.toggleNativeEvent(t, n); } })), e._eventDispatcher), rB = { fire(e, t, n) { return console.warn('The "fire" event api has been deprecated and will be removed in TinyMCE 9. Use "dispatch" instead.', (new Error).stack), this.dispatch(e, t, n); }, dispatch(e, t, n) { const o = this; if (o.removed && "remove" !== e && "detach" !== e)
            return za(e.toLowerCase(), null != t ? t : {}, o); const r = oB(o).dispatch(e, t); if (!1 !== n && o.parent) {
            let t = o.parent();
            for (; t && !r.isPropagationStopped();)
                t.dispatch(e, r, !1), t = t.parent ? t.parent() : void 0;
        } return r; }, on(e, t, n) { return oB(this).on(e, t, n); }, off(e, t) { return oB(this).off(e, t); }, once(e, t) { return oB(this).once(e, t); }, hasEventListeners(e) { return oB(this).has(e); } }, sB = ni.DOM;
    let aB;
    const iB = (e, t) => { if ("selectionchange" === t)
        return e.getDoc(); if (!e.inline && /^(?:mouse|touch|click|contextmenu|drop|dragover|dragend)/.test(t))
        return e.getDoc().documentElement; const n = sc(e); return n ? (e.eventRoot || (e.eventRoot = sB.select(n)[0]), e.eventRoot) : e.getBody(); }, lB = (e, t, n) => { (e => !e.hidden && !yE(e))(e) ? e.dispatch(t, n) : yE(e) && ((e, t) => { if ((e => "click" === e.type)(t) && !wg.metaKeyPressed(t)) {
        const n = mn(t.target);
        ((e, t) => sr(t, "a", (t => Cn(t, mn(e.getBody())))).bind((e => bo(e, "href"))))(e, n).each((n => { if (t.preventDefault(), /^#/.test(n)) {
            const t = e.dom.select(`${n},[name="${We(n, "#")}"]`);
            t.length && e.selection.scrollIntoView(t[0], !0);
        }
        else
            window.open(n, "_blank", "rel=noopener noreferrer,menubar=yes,toolbar=yes,location=yes,status=yes,resizable=yes,scrollbars=yes"); }));
    }
    else
        (e => H(xE, e.type))(t) && e.dispatch(t.type, t); })(e, n); }, dB = (e, t) => { if (e.delegates || (e.delegates = {}), e.delegates[t] || e.removed)
        return; const n = iB(e, t); if (sc(e)) {
        if (aB || (aB = {}, e.editorManager.on("removeEditor", (() => { e.editorManager.activeEditor || aB && (pe(aB, ((t, n) => { e.dom.unbind(iB(e, n)); })), aB = null); }))), aB[t])
            return;
        const o = n => { const o = n.target, r = e.editorManager.get(); let s = r.length; for (; s--;) {
            const e = r[s].getBody();
            (e === o || sB.isChildOf(o, e)) && lB(r[s], t, n);
        } };
        aB[t] = o, sB.bind(n, t, o);
    }
    else {
        const o = n => { lB(e, t, n); };
        sB.bind(n, t, o), e.delegates[t] = o;
    } }, cB = { ...rB, bindPendingEventDelegates() { const e = this; an.each(e._pendingNativeEvents, (t => { dB(e, t); })); }, toggleNativeEvent(e, t) { const n = this; "focus" !== e && "blur" !== e && (n.removed || (t ? n.initialized ? dB(n, e) : n._pendingNativeEvents ? n._pendingNativeEvents.push(e) : n._pendingNativeEvents = [e] : n.initialized && n.delegates && (n.dom.unbind(iB(n, e), e, n.delegates[e]), delete n.delegates[e]))); }, unbindAllNativeEvents() { const e = this, t = e.getBody(), n = e.dom; e.delegates && (pe(e.delegates, ((t, n) => { e.dom.unbind(iB(e, n), n, t); })), delete e.delegates), !e.inline && t && n && (t.onload = null, n.unbind(e.getWin()), n.unbind(e.getDoc())), n && (n.unbind(t), n.unbind(e.getContainer())); } }, uB = e => m(e) ? { value: e.split(/[ ,]/), valid: !0 } : x(e, m) ? { value: e, valid: !0 } : { valid: !1, message: "The value must be a string[] or a comma/space separated string." }, mB = (e, t) => e + (tt(t.message) ? "" : `. ${t.message}`), fB = e => e.valid, gB = (e, t, n = "") => { const o = t(e); return b(o) ? o ? { value: e, valid: !0 } : { valid: !1, message: n } : o; }, pB = e => e.readonly, hB = ["design", "readonly"], bB = (e, t, n, o) => { const r = n[t.get()], s = n[o]; try {
        s.activate();
    }
    catch (e) {
        return void console.error(`problem while activating editor mode ${o}:`, e);
    } r.deactivate(), r.editorReadOnly !== s.editorReadOnly && ((e, t) => { const n = mn(e.getBody()); t ? (e.readonly = !0, e.hasEditableRoot() && (n.dom.contentEditable = "true"), bE(e)) : (e.readonly = !1, vE(e)); })(e, s.editorReadOnly), t.set(o), ((e, t) => { e.dispatch("SwitchMode", { mode: t }); })(e, o); }, vB = e => { const t = Ne("design"), n = Ne({ design: { activate: _, deactivate: _, editorReadOnly: !1 }, readonly: { activate: _, deactivate: _, editorReadOnly: !0 } }); return (e => { const t = t => { pB(e) && (e => $(e, (e => "characterData" === e.type || "childList" === e.type)))(t) && (e => { const t = e.undoManager.add(); C(t) && (e.undoManager.undo(), e.undoManager.reset()); })(e); }, n = new MutationObserver(t); e.on("beforeinput paste cut dragend dragover draggesture dragdrop drop drag", (t => { pB(e) && t.preventDefault(); })), e.on("BeforeExecCommand", (t => { "Undo" !== t.command && "Redo" !== t.command || !pB(e) || t.preventDefault(); })), e.on("compositionstart", (() => { pB(e) && n.observe(e.getBody(), { characterData: !0, childList: !0, subtree: !0 }); })), e.on("compositionend", (() => { if (pB(e)) {
        const e = n.takeRecords();
        t(e);
    } n.disconnect(); })); })(e), (e => { (e => { e.serializer ? EE(e) : e.on("PreInit", (() => { EE(e); })); })(e), (e => { e.on("ShowCaret ObjectSelected", (t => { yE(e) && t.preventDefault(); })), e.on("DisabledStateChange", (t => { t.isDefaultPrevented() || wE(e, t.state); })); })(e); })(e), { isReadOnly: () => pB(e), set: o => ((e, t, n, o) => { if (!(o === n.get() || e.initialized && yE(e))) {
            if (!_e(t, o))
                throw new Error(`Editor mode '${o}' is invalid`);
            e.initialized ? bB(e, n, t, o) : e.on("init", (() => bB(e, n, t, o)));
        } })(e, n.get(), t, o), get: () => t.get(), register: (e, t) => { n.set(((e, t, n) => { if (H(hB, t))
            throw new Error(`Cannot override default mode ${t}`); return { ...e, [t]: { ...n, deactivate: () => { try {
                    n.deactivate();
                }
                catch (e) {
                    console.error(`problem while deactivating editor mode ${t}:`, e);
                } } } }; })(n.get(), e, t)); } }; }, yB = an.each, CB = an.explode, wB = { f1: 112, f2: 113, f3: 114, f4: 115, f5: 116, f6: 117, f7: 118, f8: 119, f9: 120, f10: 121, f11: 122, f12: 123 }, EB = an.makeMap("alt,ctrl,shift,meta,access"), xB = e => { const t = {}, n = nn.os.isMacOS() || nn.os.isiOS(); yB(CB(e.toLowerCase(), "+"), (e => { (e => e in EB)(e) ? t[e] = !0 : /^[0-9]{2,}$/.test(e) ? t.keyCode = parseInt(e, 10) : (t.charCode = e.charCodeAt(0), t.keyCode = wB[e] || e.toUpperCase().charCodeAt(0)); })); const o = [t.keyCode]; let r; for (r in EB)
        t[r] ? o.push(r) : t[r] = !1; return t.id = o.join(","), t.access && (t.alt = !0, n ? t.ctrl = !0 : t.shift = !0), t.meta && (n ? t.meta = !0 : (t.ctrl = !0, t.meta = !1)), t; };
    class _B {
        constructor(e) { this.shortcuts = {}, this.pendingPatterns = [], this.editor = e; const t = this; e.on("keyup keypress keydown", (e => { !t.hasModifier(e) && !t.isFunctionKey(e) || e.isDefaultPrevented() || (yB(t.shortcuts, (n => { t.matchShortcut(e, n) && (t.pendingPatterns = n.subpatterns.slice(0), "keydown" === e.type && t.executeShortcutAction(n)); })), t.matchShortcut(e, t.pendingPatterns[0]) && (1 === t.pendingPatterns.length && "keydown" === e.type && t.executeShortcutAction(t.pendingPatterns[0]), t.pendingPatterns.shift())); })); }
        add(e, t, n, o) { const r = this, s = r.normalizeCommandFunc(n); return yB(CB(an.trim(e)), (e => { const n = r.createShortcut(e, t, s, o); r.shortcuts[n.id] = n; })), !0; }
        remove(e) { const t = this.createShortcut(e); return !!this.shortcuts[t.id] && (delete this.shortcuts[t.id], !0); }
        normalizeCommandFunc(e) { const t = this, n = e; return "string" == typeof n ? () => { t.editor.execCommand(n, !1, null); } : an.isArray(n) ? () => { t.editor.execCommand(n[0], n[1], n[2]); } : n; }
        createShortcut(e, t, n, o) { const r = an.map(CB(e, ">"), xB); return r[r.length - 1] = an.extend(r[r.length - 1], { func: n, scope: o || this.editor }), an.extend(r[0], { desc: this.editor.translate(t), subpatterns: r.slice(1) }); }
        hasModifier(e) { return e.altKey || e.ctrlKey || e.metaKey; }
        isFunctionKey(e) { return "keydown" === e.type && e.keyCode >= 112 && e.keyCode <= 123; }
        matchShortcut(e, t) { return !!t && t.ctrl === e.ctrlKey && t.meta === e.metaKey && t.alt === e.altKey && t.shift === e.shiftKey && !!(e.keyCode === t.keyCode || e.charCode && e.charCode === t.charCode) && (e.preventDefault(), !0); }
        executeShortcutAction(e) { return e.func ? e.func.call(e.scope) : null; }
    }
    const SB = () => { const e = (() => { const e = {}, t = {}, n = {}, o = {}, r = {}, s = {}, a = {}, i = {}, l = {}, d = (e, t) => (n, o) => { e[n.toLowerCase()] = { ...o, type: t }; }; return { addButton: d(e, "button"), addGroupToolbarButton: d(e, "grouptoolbarbutton"), addToggleButton: d(e, "togglebutton"), addMenuButton: d(e, "menubutton"), addSplitButton: d(e, "splitbutton"), addMenuItem: d(t, "menuitem"), addNestedMenuItem: d(t, "nestedmenuitem"), addToggleMenuItem: d(t, "togglemenuitem"), addAutocompleter: d(n, "autocompleter"), addContextMenu: d(r, "contextmenu"), addContextToolbar: d(s, "contexttoolbar"), addContextForm: (c = s, (e, t) => { c[e.toLowerCase()] = { type: "contextform", ...t }; }), addSidebar: d(i, "sidebar"), addView: d(l, "views"), addIcon: (e, t) => o[e.toLowerCase()] = t, addContext: (e, t) => a[e.toLowerCase()] = t, getAll: () => ({ buttons: e, menuItems: t, icons: o, popups: n, contextMenus: r, contextToolbars: s, sidebars: i, views: l, contexts: a }) }; var c; })(); return { addAutocompleter: e.addAutocompleter, addButton: e.addButton, addContextForm: e.addContextForm, addContextMenu: e.addContextMenu, addContextToolbar: e.addContextToolbar, addIcon: e.addIcon, addMenuButton: e.addMenuButton, addMenuItem: e.addMenuItem, addNestedMenuItem: e.addNestedMenuItem, addSidebar: e.addSidebar, addSplitButton: e.addSplitButton, addToggleButton: e.addToggleButton, addGroupToolbarButton: e.addGroupToolbarButton, addToggleMenuItem: e.addToggleMenuItem, addView: e.addView, addContext: e.addContext, getAll: e.getAll }; }, kB = ni.DOM, NB = an.extend, RB = an.each;
    class AB {
        constructor(e, t, n) { this.plugins = {}, this.contentCSS = [], this.contentStyles = [], this.loadedCSS = {}, this.isNotDirty = !1, this.composing = !1, this.destroyed = !1, this.hasHiddenInput = !1, this.iframeElement = null, this.initialized = !1, this.readonly = !1, this.removed = !1, this.startContent = "", this._pendingNativeEvents = [], this._skinLoaded = !1, this._editableRoot = !0, this.editorManager = n, this.documentBaseUrl = n.documentBaseURL, NB(this, cB); const o = this; this.id = e, this.hidden = !1; const r = ((e, t) => { const n = Ie(t); return IO(TO || OO, TO, n, e, n); })(n.defaultOptions, t); this.options = ((e, t, n = t) => { const o = {}, r = {}, s = (e, t, n) => { const o = gB(t, n); return fB(o) ? (r[e] = o.value, !0) : (console.warn(mB(`Invalid value passed for the ${e} option`, o)), !1); }, a = e => _e(o, e); return { register: (e, n) => { const a = (e => m(e.processor))(n) ? (e => { const t = (() => { switch (e) {
                case "array": return p;
                case "boolean": return b;
                case "function": return w;
                case "number": return E;
                case "object": return f;
                case "string": return m;
                case "string[]": return uB;
                case "object[]": return e => x(e, f);
                case "regexp": return e => u(e, RegExp);
                default: return M;
            } })(); return n => gB(n, t, `The value must be a ${e}.`); })(n.processor) : n.processor, i = ((e, t, n) => { if (!v(t)) {
                const o = gB(t, n);
                if (fB(o))
                    return o.value;
                console.error(mB(`Invalid default value passed for the "${e}" option`, o));
            } })(e, n.default, a); o[e] = { ...n, default: i, processor: a }, xe(r, e).orThunk((() => xe(t, e))).each((t => s(e, t, a))); }, isRegistered: a, get: e => xe(r, e).orThunk((() => xe(o, e).map((e => e.default)))).getOrUndefined(), set: (e, t) => { if (a(e)) {
                const n = o[e];
                return n.immutable ? (console.error(`"${e}" is an immutable option and cannot be updated`), !1) : s(e, t, n.processor);
            } return console.warn(`"${e}" is not a registered option. Ensure the option has been registered before setting a value.`), !1; }, unset: e => { const t = a(e); return t && delete r[e], t; }, isSet: e => _e(r, e), debug: () => { try {
                console.log(JSON.parse(JSON.stringify(n, ((e, t) => b(t) || E(t) || m(t) || h(t) || p(t) || g(t) ? t : Object.prototype.toString.call(t)))));
            }
            catch (e) {
                console.error(e);
            } } }; })(0, r, t), (e => { const t = e.options.register; t("id", { processor: "string", default: e.id }), t("selector", { processor: "string" }), t("target", { processor: "object" }), t("suffix", { processor: "string" }), t("cache_suffix", { processor: "string" }), t("base_url", { processor: "string" }), t("referrer_policy", { processor: "string", default: "" }), t("language_load", { processor: "boolean", default: !0 }), t("inline", { processor: "boolean", default: !1 }), t("iframe_attrs", { processor: "object", default: {} }), t("doctype", { processor: "string", default: "<!DOCTYPE html>" }), t("document_base_url", { processor: "string", default: e.documentBaseUrl }), t("body_id", { processor: wd(e, "tinymce"), default: "tinymce" }), t("body_class", { processor: wd(e), default: "" }), t("content_security_policy", { processor: "string", default: "" }), t("br_in_pre", { processor: "boolean", default: !0 }), t("forced_root_block", { processor: e => { const t = m(e) && et(e); return t ? { value: e, valid: t } : { valid: !1, message: "Must be a non-empty string." }; }, default: "p" }), t("forced_root_block_attrs", { processor: "object", default: {} }), t("newline_behavior", { processor: e => { const t = H(["block", "linebreak", "invert", "default"], e); return t ? { value: e, valid: t } : { valid: !1, message: "Must be one of: block, linebreak, invert or default." }; }, default: "default" }), t("br_newline_selector", { processor: "string", default: ".mce-toc h2,figcaption,caption" }), t("no_newline_selector", { processor: "string", default: "" }), t("keep_styles", { processor: "boolean", default: !0 }), t("end_container_on_empty_block", { processor: e => b(e) || m(e) ? { valid: !0, value: e } : { valid: !1, message: "Must be boolean or a string" }, default: "blockquote" }), t("font_size_style_values", { processor: "string", default: "xx-small,x-small,small,medium,large,x-large,xx-large" }), t("font_size_legacy_values", { processor: "string", default: "xx-small,small,medium,large,x-large,xx-large,300%" }), t("font_size_classes", { processor: "string", default: "" }), t("automatic_uploads", { processor: "boolean", default: !0 }), t("images_reuse_filename", { processor: "boolean", default: !1 }), t("images_replace_blob_uris", { processor: "boolean", default: !0 }), t("icons", { processor: "string", default: "" }), t("icons_url", { processor: "string", default: "" }), t("images_upload_url", { processor: "string", default: "" }), t("images_upload_base_path", { processor: "string", default: "" }), t("images_upload_credentials", { processor: "boolean", default: !1 }), t("images_upload_handler", { processor: "function" }), t("language", { processor: "string", default: "en" }), t("language_url", { processor: "string", default: "" }), t("entity_encoding", { processor: "string", default: "named" }), t("indent", { processor: "boolean", default: !0 }), t("indent_before", { processor: "string", default: "p,h1,h2,h3,h4,h5,h6,blockquote,div,title,style,pre,script,td,th,ul,ol,li,dl,dt,dd,area,table,thead,tfoot,tbody,tr,section,details,summary,article,hgroup,aside,figure,figcaption,option,optgroup,datalist" }), t("indent_after", { processor: "string", default: "p,h1,h2,h3,h4,h5,h6,blockquote,div,title,style,pre,script,td,th,ul,ol,li,dl,dt,dd,area,table,thead,tfoot,tbody,tr,section,details,summary,article,hgroup,aside,figure,figcaption,option,optgroup,datalist" }), t("indent_use_margin", { processor: "boolean", default: !1 }), t("indentation", { processor: "string", default: "40px" }), t("content_css", { processor: e => { const t = !1 === e || m(e) || x(e, m); return t ? m(e) ? { value: V(e.split(","), Ze), valid: t } : p(e) ? { value: e, valid: t } : !1 === e ? { value: [], valid: t } : { value: e, valid: t } : { valid: !1, message: "Must be false, a string or an array of strings." }; }, default: bc(e) ? [] : ["default"] }), t("content_style", { processor: "string" }), t("content_css_cors", { processor: "boolean", default: !1 }), t("font_css", { processor: e => { const t = m(e) || x(e, m); return t ? { value: p(e) ? e : V(e.split(","), Ze), valid: t } : { valid: !1, message: "Must be a string or an array of strings." }; }, default: [] }), t("extended_mathml_attributes", { processor: "string[]" }), t("extended_mathml_elements", { processor: "string[]" }), t("inline_boundaries", { processor: "boolean", default: !0 }), t("inline_boundaries_selector", { processor: "string", default: "a[href],code,span.mce-annotation" }), t("object_resizing", { processor: e => { const t = b(e) || m(e); return t ? !1 === e || pd.isiPhone() || pd.isiPad() ? { value: "", valid: t } : { value: !0 === e ? "table,img,figure.image,div,video,iframe" : e, valid: t } : { valid: !1, message: "Must be boolean or a string" }; }, default: !hd }), t("resize_img_proportional", { processor: "boolean", default: !0 }), t("event_root", { processor: "string" }), t("service_message", { processor: "string" }), t("onboarding", { processor: "boolean", default: !0 }), t("tiny_cloud_entry_url", { processor: "string" }), t("theme", { processor: e => !1 === e || m(e) || w(e), default: "silver" }), t("theme_url", { processor: "string" }), t("formats", { processor: "object" }), t("format_empty_lines", { processor: "boolean", default: !1 }), t("format_noneditable_selector", { processor: "string", default: "" }), t("preview_styles", { processor: e => { const t = !1 === e || m(e); return t ? { value: !1 === e ? "" : e, valid: t } : { valid: !1, message: "Must be false or a string" }; }, default: "font-family font-size font-weight font-style text-decoration text-transform color background-color border border-radius outline text-shadow" }), t("custom_ui_selector", { processor: "string", default: "" }), t("hidden_input", { processor: "boolean", default: !0 }), t("submit_patch", { processor: "boolean", default: !0 }), t("encoding", { processor: "string" }), t("add_form_submit_trigger", { processor: "boolean", default: !0 }), t("add_unload_trigger", { processor: "boolean", default: !0 }), t("custom_undo_redo_levels", { processor: "number", default: 0 }), t("disable_nodechange", { processor: "boolean", default: !1 }), t("disabled", { processor: t => b(t) ? (e.initialized && hu(e) !== t && Promise.resolve().then((() => { ((e, t) => { e.dispatch("DisabledStateChange", { state: t }); })(e, t); })), { valid: !0, value: t }) : { valid: !1, message: "The value must be a boolean." }, default: !1 }), t("readonly", { processor: "boolean", default: !1 }), t("editable_root", { processor: "boolean", default: !0 }), t("plugins", { processor: "string[]", default: [] }), t("external_plugins", { processor: "object" }), t("forced_plugins", { processor: "string[]" }), t("model", { processor: "string", default: e.hasPlugin("rtc") ? "plugin" : "dom" }), t("model_url", { processor: "string" }), t("block_unsupported_drop", { processor: "boolean", default: !0 }), t("visual", { processor: "boolean", default: !0 }), t("visual_table_class", { processor: "string", default: "mce-item-table" }), t("visual_anchor_class", { processor: "string", default: "mce-item-anchor" }), t("iframe_aria_text", { processor: "string", default: "Rich Text Area".concat(e.hasPlugin("help") ? ". Press ALT-0 for help." : "") }), t("setup", { processor: "function" }), t("init_instance_callback", { processor: "function" }), t("url_converter", { processor: "function", default: e.convertURL }), t("url_converter_scope", { processor: "object", default: e }), t("urlconverter_callback", { processor: "function" }), t("allow_conditional_comments", { processor: "boolean", default: !1 }), t("allow_html_data_urls", { processor: "boolean", default: !1 }), t("allow_svg_data_urls", { processor: "boolean" }), t("allow_html_in_named_anchor", { processor: "boolean", default: !1 }), t("allow_script_urls", { processor: "boolean", default: !1 }), t("allow_unsafe_link_target", { processor: "boolean", default: !1 }), t("allow_mathml_annotation_encodings", { processor: e => { const t = x(e, m); return t ? { value: e, valid: t } : { valid: !1, message: "Must be an array of strings." }; }, default: [] }), t("convert_fonts_to_spans", { processor: "boolean", default: !0, deprecated: !0 }), t("fix_list_elements", { processor: "boolean", default: !1 }), t("preserve_cdata", { processor: "boolean", default: !1 }), t("remove_trailing_brs", { processor: "boolean", default: !0 }), t("pad_empty_with_br", { processor: "boolean", default: !1 }), t("inline_styles", { processor: "boolean", default: !0, deprecated: !0 }), t("element_format", { processor: "string", default: "html" }), t("entities", { processor: "string" }), t("schema", { processor: "string", default: "html5" }), t("convert_urls", { processor: "boolean", default: !0 }), t("relative_urls", { processor: "boolean", default: !0 }), t("remove_script_host", { processor: "boolean", default: !0 }), t("custom_elements", { processor: Cd }), t("extended_valid_elements", { processor: "string" }), t("invalid_elements", { processor: "string" }), t("invalid_styles", { processor: Cd }), t("valid_children", { processor: "string" }), t("valid_classes", { processor: Cd }), t("valid_elements", { processor: "string" }), t("valid_styles", { processor: Cd }), t("verify_html", { processor: "boolean", default: !0 }), t("auto_focus", { processor: e => m(e) || !0 === e }), t("browser_spellcheck", { processor: "boolean", default: !1 }), t("protect", { processor: "array" }), t("images_file_types", { processor: "string", default: "jpeg,jpg,jpe,jfi,jif,jfif,png,gif,bmp,webp" }), t("deprecation_warnings", { processor: "boolean", default: !0 }), t("a11y_advanced_options", { processor: "boolean", default: !1 }), t("api_key", { processor: "string" }), t("license_key", { processor: "string" }), t("paste_block_drop", { processor: "boolean", default: !1 }), t("paste_data_images", { processor: "boolean", default: !0 }), t("paste_preprocess", { processor: "function" }), t("paste_postprocess", { processor: "function" }), t("paste_webkit_styles", { processor: "string", default: "none" }), t("paste_remove_styles_if_webkit", { processor: "boolean", default: !0 }), t("paste_merge_formats", { processor: "boolean", default: !0 }), t("smart_paste", { processor: "boolean", default: !0 }), t("paste_as_text", { processor: "boolean", default: !1 }), t("paste_tab_spaces", { processor: "number", default: 4 }), t("text_patterns", { processor: e => x(e, f) || !1 === e ? { value: id(!1 === e ? [] : e), valid: !0 } : { valid: !1, message: "Must be an array of objects or false." }, default: [{ start: "*", end: "*", format: "italic" }, { start: "**", end: "**", format: "bold" }, { start: "#", format: "h1", trigger: "space" }, { start: "##", format: "h2", trigger: "space" }, { start: "###", format: "h3", trigger: "space" }, { start: "####", format: "h4", trigger: "space" }, { start: "#####", format: "h5", trigger: "space" }, { start: "######", format: "h6", trigger: "space" }, { start: "1.", cmd: "InsertOrderedList", trigger: "space" }, { start: "*", cmd: "InsertUnorderedList", trigger: "space" }, { start: "-", cmd: "InsertUnorderedList", trigger: "space" }, { start: ">", cmd: "mceBlockQuote", trigger: "space" }, { start: "---", cmd: "InsertHorizontalRule", trigger: "space" }] }), t("text_patterns_lookup", { processor: e => { return w(e) ? { value: (t = e, e => { const n = t(e); return id(n); }), valid: !0 } : { valid: !1, message: "Must be a single function" }; var t; }, default: e => [] }), t("noneditable_class", { processor: "string", default: "mceNonEditable" }), t("editable_class", { processor: "string", default: "mceEditable" }), t("noneditable_regexp", { processor: e => x(e, vd) ? { value: e, valid: !0 } : vd(e) ? { value: [e], valid: !0 } : { valid: !1, message: "Must be a RegExp or an array of RegExp." }, default: [] }), t("table_tab_navigation", { processor: "boolean", default: !0 }), t("highlight_on_focus", { processor: "boolean", default: !0 }), t("xss_sanitization", { processor: "boolean", default: !0 }), t("details_initial_state", { processor: e => { const t = H(["inherited", "collapsed", "expanded"], e); return t ? { value: e, valid: t } : { valid: !1, message: "Must be one of: inherited, collapsed, or expanded." }; }, default: "inherited" }), t("details_serialized_state", { processor: e => { const t = H(["inherited", "collapsed", "expanded"], e); return t ? { value: e, valid: t } : { valid: !1, message: "Must be one of: inherited, collapsed, or expanded." }; }, default: "inherited" }), t("init_content_sync", { processor: "boolean", default: !1 }), t("newdocument_content", { processor: "string", default: "" }), t("sandbox_iframes", { processor: "boolean", default: !0 }), t("sandbox_iframes_exclusions", { processor: "string[]", default: ["youtube.com", "youtu.be", "vimeo.com", "player.vimeo.com", "dailymotion.com", "embed.music.apple.com", "open.spotify.com", "giphy.com", "dai.ly", "codepen.io"] }), t("convert_unsafe_embeds", { processor: "boolean", default: !0 }), e.on("ScriptsLoaded", (() => { t("directionality", { processor: "string", default: li.isRtl() ? "rtl" : void 0 }), t("placeholder", { processor: "string", default: bd.getAttrib(e.getElement(), "placeholder") }); })); })(o); const s = this.options.get; s("deprecation_warnings") && ((e, t) => { ((e, t) => { const n = Vw(e), o = Kw(t), r = o.length > 0, s = n.length > 0, a = "mobile" === t.theme; if (r || s || a) {
            const e = "\n- ", t = a ? `\n\nThemes:${e}mobile` : "", i = r ? `\n\nPlugins:${e}${o.join(e)}` : "", l = s ? `\n\nOptions:${e}${n.join(e)}` : "";
            console.warn("The following deprecated features are currently enabled and have been removed in TinyMCE 7.0. These features will no longer work and should be removed from the TinyMCE configuration. See https://www.tiny.cloud/docs/tinymce/7/migration-from-6x/ for more information." + t + i + l);
        } })(e, t), ((e, t) => { const n = qw(e), o = Yw(t), r = o.length > 0, s = n.length > 0; if (r || s) {
            const e = "\n- ", t = r ? `\n\nPlugins:${e}${o.map(Xw).join(e)}` : "", a = s ? `\n\nOptions:${e}${n.join(e)}` : "";
            console.warn("The following deprecated features are currently enabled but will be removed soon." + t + a);
        } })(e, t); })(t, r); const a = s("suffix"); a && (n.suffix = a), this.suffix = n.suffix; const i = s("base_url"); i && n._setBaseUrl(i), this.baseUri = n.baseURI; const l = Wd(o); l && (ri.ScriptLoader._setReferrerPolicy(l), ni.DOM.styleSheetLoader._setReferrerPolicy(l)); const d = kc(o); C(d) && ni.DOM.styleSheetLoader._setContentCssCors(d), di.languageLoad = s("language_load"), di.baseURL = n.baseURL, this.setDirty(!1), this.documentBaseURI = new OC(_d(o), { base_uri: this.baseUri }), this.baseURI = this.baseUri, this.inline = bc(o), this.hasVisual = Tc(o), this.shortcuts = new _B(this), this.editorCommands = new eB(this), ZO(this); const c = s("cache_suffix"); c && (nn.cacheSuffix = c.replace(/^[\?\&]+/, "")), this.ui = { registry: SB(), styleSheetLoader: void 0, show: _, hide: _, setEnabled: _, isEnabled: M }, this.mode = vB(o), n.dispatch("SetupEditor", { editor: this }); const y = Dc(o); w(y) && y.call(o, o); }
        render() { (e => { const t = e.id; li.setCode(Kd(e)); const n = () => { SO.unbind(window, "ready", n), e.render(); }; if (!Wa.Event.domLoaded)
            return void SO.bind(window, "ready", n); if (!e.getElement())
            return; const o = mn(e.getElement()), r = Co(o); e.on("remove", (() => { W(o.dom.attributes, (e => yo(o, e.name))), po(o, r); })), e.ui.styleSheetLoader = ((e, t) => Ys.forElement(e, { contentCssCors: kc(t), referrerPolicy: Wd(t) }))(o, e), bc(e) ? e.inline = !0 : (e.orgVisibility = e.getElement().style.visibility, e.getElement().style.visibility = "hidden"); const s = e.getElement().form || SO.getParent(t, "form"); s && (e.formElement = s, vc(e) && !Jr(e.getElement()) && (SO.insertAfter(SO.create("input", { type: "hidden", name: t }), t), e.hasHiddenInput = !0), e.formEventDelegate = t => { e.dispatch(t.type, t); }, SO.bind(s, "submit reset", e.formEventDelegate), e.on("reset", (() => { e.resetContent(); })), !yc(e) || s.submit.nodeType || s.submit.length || s._mceOldSubmit || (s._mceOldSubmit = s.submit, s.submit = () => (e.editorManager.triggerSave(), e.setDirty(!1), s._mceOldSubmit(s)))), e.windowManager = dE(e), e.notificationManager = aE(e), (e => "xml" === e.options.get("encoding"))(e) && e.on("GetContent", (e => { e.save && (e.content = SO.encode(e.content)); })), Cc(e) && e.on("submit", (() => { e.initialized && e.save(); })), wc(e) && (e._beforeUnload = () => { !e.initialized || e.destroyed || e.isHidden() || e.save({ format: "raw", no_events: !0, set_dirty: !1 }); }, e.editorManager.on("BeforeUnload", e._beforeUnload)), e.editorManager.add(e), RO(e, e.suffix); })(this); }
        focus(e) { this.execCommand("mceFocus", !1, e); }
        hasFocus() { return vg(this); }
        translate(e) { return li.translate(e); }
        getParam(e, t, n) { const o = this.options; return o.isRegistered(e) || (C(n) ? o.register(e, { processor: n, default: t }) : o.register(e, { processor: M, default: t })), o.isSet(e) || v(t) ? o.get(e) : t; }
        hasPlugin(e, t) { return !(!H(Nc(this), e) || t && void 0 === iE.get(e)); }
        nodeChanged(e) { this._nodeChangeDispatcher.nodeChanged(e); }
        addCommand(e, t, n) { this.editorCommands.addCommand(e, t, n); }
        addQueryStateHandler(e, t, n) { this.editorCommands.addQueryStateHandler(e, t, n); }
        addQueryValueHandler(e, t, n) { this.editorCommands.addQueryValueHandler(e, t, n); }
        addShortcut(e, t, n, o) { this.shortcuts.add(e, t, n, o); }
        execCommand(e, t, n, o) { return this.editorCommands.execCommand(e, t, n, o); }
        queryCommandState(e) { return this.editorCommands.queryCommandState(e); }
        queryCommandValue(e) { return this.editorCommands.queryCommandValue(e); }
        queryCommandSupported(e) { return this.editorCommands.queryCommandSupported(e); }
        show() { const e = this; e.hidden && (e.hidden = !1, e.inline ? e.getBody().contentEditable = "true" : (kB.show(e.getContainer()), kB.hide(e.id)), e.load(), e.dispatch("show")); }
        hide() { const e = this; e.hidden || (e.save(), e.inline ? (e.getBody().contentEditable = "false", e === e.editorManager.focusedEditor && (e.editorManager.focusedEditor = null)) : (kB.hide(e.getContainer()), kB.setStyle(e.id, "display", e.orgDisplay)), e.hidden = !0, e.dispatch("hide")); }
        isHidden() { return this.hidden; }
        setProgressState(e, t) { this.dispatch("ProgressState", { state: e, time: t }); }
        load(e = {}) { const t = this, n = t.getElement(); if (t.removed)
            return ""; if (n) {
            const o = { ...e, load: !0 }, r = Jr(n) ? n.value : n.innerHTML, s = t.setContent(r, o);
            return o.no_events || t.dispatch("LoadContent", { ...o, element: n }), s;
        } return ""; }
        save(e = {}) { const t = this; let n = t.getElement(); if (!n || !t.initialized || t.removed)
            return ""; const o = { ...e, save: !0, element: n }; let r = t.getContent(o); const s = { ...o, content: r }; if (s.no_events || t.dispatch("SaveContent", s), "raw" === s.format && t.dispatch("RawSaveContent", s), r = s.content, Jr(n))
            n.value = r;
        else {
            !e.is_removing && t.inline || (n.innerHTML = r);
            const o = kB.getParent(t.id, "form");
            o && RB(o.elements, (e => e.name !== t.id || (e.value = r, !1)));
        } return s.element = o.element = n = null, !1 !== s.set_dirty && t.setDirty(!1), r; }
        setContent(e, t) { return Fw(this, e, t); }
        getContent(e) { return ((e, t = {}) => { const n = ((e, t) => ({ ...e, format: t, get: !0, getInner: !0 }))(t, t.format ? t.format : "html"); return XC(e, n).fold(R, (t => { const n = ((e, t) => Nw(e).editor.getContent(t))(e, t); return GC(e, n, t); })); })(this, e); }
        insertContent(e, t) { t && (e = NB({ content: e }, t)), this.execCommand("mceInsertContent", !1, e); }
        resetContent(e) { void 0 === e ? Fw(this, this.startContent, { format: "raw" }) : Fw(this, e), this.undoManager.reset(), this.setDirty(!1), this.nodeChanged(); }
        isDirty() { return !this.isNotDirty; }
        setDirty(e) { const t = !this.isNotDirty; this.isNotDirty = !e, e && e !== t && this.dispatch("dirty"); }
        getContainer() { const e = this; return e.container || (e.container = e.editorContainer || kB.get(e.id + "_parent")), e.container; }
        getContentAreaContainer() { return this.contentAreaContainer; }
        getElement() { return this.targetElm || (this.targetElm = kB.get(this.id)), this.targetElm; }
        getWin() { const e = this; if (!e.contentWindow) {
            const t = e.iframeElement;
            t && (e.contentWindow = t.contentWindow);
        } return e.contentWindow; }
        getDoc() { const e = this; if (!e.contentDocument) {
            const t = e.getWin();
            t && (e.contentDocument = t.document);
        } return e.contentDocument; }
        getBody() { var e, t; const n = this.getDoc(); return null !== (t = null !== (e = this.bodyElement) && void 0 !== e ? e : null == n ? void 0 : n.body) && void 0 !== t ? t : null; }
        convertURL(e, t, n) { const o = this, r = o.options.get, s = Mc(o); if (w(s))
            return s.call(o, e, n, !0, t); if (!r("convert_urls") || "link" === n || f(n) && "LINK" === n.nodeName || 0 === e.indexOf("file:") || 0 === e.length)
            return e; const a = new OC(e); return "http" !== a.protocol && "https" !== a.protocol && "" !== a.protocol ? e : r("relative_urls") ? o.documentBaseURI.toRelative(e) : e = o.documentBaseURI.toAbsolute(e, r("remove_script_host")); }
        addVisual(e) { ((e, t) => { ((e, t) => { Rw(e).editor.addVisual(t); })(e, t); })(this, e); }
        setEditableRoot(e) { ((e, t) => { e._editableRoot !== t && (e._editableRoot = t, yE(e) || (e.getBody().contentEditable = String(e.hasEditableRoot()), e.nodeChanged()), ((e, t) => { e.dispatch("EditableRootStateChange", { state: t }); })(e, t)); })(this, e); }
        hasEditableRoot() { return this._editableRoot; }
        remove() { (e => { if (!e.removed) {
            const { _selectionOverrides: t, editorUpload: n } = e, o = e.getBody(), r = e.getElement();
            o && e.save({ is_removing: !0 }), e.removed = !0, e.unbindAllNativeEvents(), e.hasHiddenInput && C(null == r ? void 0 : r.nextSibling) && Gw.remove(r.nextSibling), (e => { e.dispatch("remove"); })(e), e.editorManager.remove(e), !e.inline && o && (e => { Gw.setStyle(e.id, "display", e.orgDisplay); })(e), (e => { e.dispatch("detach"); })(e), Gw.remove(e.getContainer()), Zw(t), Zw(n), e.destroy();
        } })(this); }
        destroy(e) { ((e, t) => { const { selection: n, dom: o } = e; e.destroyed || (t || e.removed ? (t || (e.editorManager.off("beforeunload", e._beforeUnload), e.theme && e.theme.destroy && e.theme.destroy(), Zw(n), Zw(o)), (e => { const t = e.formElement; t && (t._mceOldSubmit && (t.submit = t._mceOldSubmit, delete t._mceOldSubmit), Gw.unbind(t, "submit reset", e.formEventDelegate)); })(e), (e => { const t = e; t.contentAreaContainer = t.formElement = t.container = t.editorContainer = null, t.bodyElement = t.contentDocument = t.contentWindow = null, t.iframeElement = t.targetElm = null; const n = e.selection; if (n) {
            const e = n.dom;
            t.selection = n.win = n.dom = e.doc = null;
        } })(e), e.destroyed = !0) : e.remove()); })(this, e); }
        uploadImages() { return this.editorUpload.uploadImages(); }
        _scanForImages() { return this.editorUpload.scanForImages(); }
    }
    const TB = ni.DOM, OB = an.each;
    let BB, PB = !1, DB = [];
    const LB = e => { const t = e.type; OB(UB.get(), (n => { switch (t) {
        case "scroll":
            n.dispatch("ScrollWindow", e);
            break;
        case "resize": n.dispatch("ResizeWindow", e);
    } })); }, MB = e => { if (e !== PB) {
        const t = ni.DOM;
        e ? (t.bind(window, "resize", LB), t.bind(window, "scroll", LB)) : (t.unbind(window, "resize", LB), t.unbind(window, "scroll", LB)), PB = e;
    } }, IB = e => { const t = DB; return DB = Y(DB, (t => e !== t)), UB.activeEditor === e && (UB.activeEditor = DB.length > 0 ? DB[0] : null), UB.focusedEditor === e && (UB.focusedEditor = null), t.length !== DB.length; }, FB = "CSS1Compat" !== document.compatMode, UB = { ...rB, baseURI: null, baseURL: null, defaultOptions: {}, documentBaseURL: null, suffix: null, majorVersion: "7", minorVersion: "9.1", releaseDate: "2025-05-29", i18n: li, activeEditor: null, focusedEditor: null, setup() { const e = this; let t = "", n = "", o = OC.getDocumentBaseUrl(document.location); /^[^:]+:\/\/\/?[^\/]+\//.test(o) && (o = o.replace(/[\?#].*$/, "").replace(/[\/\\][^\/]+$/, ""), /[\/\\]$/.test(o) || (o += "/")); const r = window.tinymce || window.tinyMCEPreInit; if (r)
            t = r.base || r.baseURL, n = r.suffix;
        else {
            const e = document.getElementsByTagName("script");
            for (let o = 0; o < e.length; o++) {
                const r = e[o].src || "";
                if ("" === r)
                    continue;
                const s = r.substring(r.lastIndexOf("/"));
                if (/tinymce(\.full|\.jquery|)(\.min|\.dev|)\.js/.test(r)) {
                    -1 !== s.indexOf(".min") && (n = ".min"), t = r.substring(0, r.lastIndexOf("/"));
                    break;
                }
            }
            if (!t && document.currentScript) {
                const e = document.currentScript.src;
                -1 !== e.indexOf(".min") && (n = ".min"), t = e.substring(0, e.lastIndexOf("/"));
            }
        } var s; e.baseURL = new OC(o).toAbsolute(t), e.documentBaseURL = o, e.baseURI = new OC(e.baseURL), e.suffix = n, (s = e).on("AddEditor", T(gg, s)), s.on("RemoveEditor", T(pg, s)); }, overrideDefaults(e) { const t = e.base_url; t && this._setBaseUrl(t); const n = e.suffix; n && (this.suffix = n), this.defaultOptions = e; const o = e.plugin_base_urls; void 0 !== o && pe(o, ((e, t) => { di.PluginManager.urls[t] = e; })); }, init(e) { const t = this; let n; const o = an.makeMap("area base basefont br col frame hr img input isindex link meta param embed source wbr track colgroup option table tbody tfoot thead tr th td script noscript style textarea video audio iframe object menu", " "); let r = e => { n = e; }; const s = () => { let n = 0; const a = []; let i; TB.unbind(window, "ready", s), (() => { const n = e.onpageload; n && n.apply(t, []); })(), i = me((e => nn.browser.isIE() || nn.browser.isEdge() ? (gE("TinyMCE does not support the browser you are using. For a list of supported browsers please see: https://www.tiny.cloud/docs/tinymce/7/support/#supportedwebbrowsers"), []) : FB ? (gE("Failed to initialize the editor as the document is not in standards mode. TinyMCE requires standards mode."), []) : m(e.selector) ? TB.select(e.selector) : C(e.target) ? [e.target] : [])(e)), an.each(i, (e => { var n; (n = t.get(e.id)) && n.initialized && !(n.getContainer() || n.getBody()).parentNode && (IB(n), n.unbindAllNativeEvents(), n.destroy(!0), n.removed = !0); })), i = an.grep(i, (e => !t.get(e.id))), 0 === i.length ? r([]) : OB(i, (s => { ((e, t) => e.inline && t.tagName.toLowerCase() in o)(e, s) ? gE("Could not initialize inline editor on invalid inline target element", s) : ((e, o, s) => { const l = new AB(e, o, t); a.push(l), l.on("init", (() => { ++n === i.length && r(a); })), l.targetElm = l.targetElm || s, l.render(); })((e => { let t = e.id; return t || (t = xe(e, "name").filter((e => !TB.get(e))).getOrThunk(TB.uniqueId), e.setAttribute("id", t)), t; })(s), e, s); })); }; return TB.bind(window, "ready", s), new Promise((e => { n ? e(n) : r = t => { e(t); }; })); }, get(e) { return 0 === arguments.length ? DB.slice(0) : m(e) ? Q(DB, (t => t.id === e)).getOr(null) : E(e) && DB[e] ? DB[e] : null; }, add(e) { const t = this, n = t.get(e.id); return n === e || (null === n && DB.push(e), MB(!0), t.activeEditor = e, t.dispatch("AddEditor", { editor: e }), BB || (BB = e => { const n = t.dispatch("BeforeUnload"); if (n.returnValue)
            return e.preventDefault(), e.returnValue = n.returnValue, n.returnValue; }, window.addEventListener("beforeunload", BB))), e; }, createEditor(e, t) { return this.add(new AB(e, t, this)); }, remove(e) { const t = this; let n; if (e) {
            if (!m(e))
                return n = e, h(t.get(n.id)) ? null : (IB(n) && t.dispatch("RemoveEditor", { editor: n }), 0 === DB.length && window.removeEventListener("beforeunload", BB), n.remove(), MB(DB.length > 0), n);
            OB(TB.select(e), (e => { n = t.get(e.id), n && t.remove(n); }));
        }
        else
            for (let e = DB.length - 1; e >= 0; e--)
                t.remove(DB[e]); }, execCommand(e, t, n) { var o; const r = this, s = f(n) ? null !== (o = n.id) && void 0 !== o ? o : n.index : n; switch (e) {
            case "mceAddEditor":
                if (!r.get(s)) {
                    const e = n.options;
                    new AB(s, e, r).render();
                }
                return !0;
            case "mceRemoveEditor": {
                const e = r.get(s);
                return e && e.remove(), !0;
            }
            case "mceToggleEditor": {
                const e = r.get(s);
                return e ? (e.isHidden() ? e.show() : e.hide(), !0) : (r.execCommand("mceAddEditor", !1, n), !0);
            }
        } return !!r.activeEditor && r.activeEditor.execCommand(e, t, n); }, triggerSave: () => { OB(DB, (e => { e.save(); })); }, addI18n: (e, t) => { li.add(e, t); }, translate: e => li.translate(e), setActive(e) { const t = this.activeEditor; this.activeEditor !== e && (t && t.dispatch("deactivate", { relatedTarget: e }), e.dispatch("activate", { relatedTarget: t })), this.activeEditor = e; }, _setBaseUrl(e) { this.baseURL = new OC(this.documentBaseURL).toAbsolute(e.replace(/\/+$/, "")), this.baseURI = new OC(this.baseURL); } };
    UB.setup();
    const zB = (() => { const e = Ve(); return { FakeClipboardItem: e => ({ items: e, types: fe(e), getType: t => xe(e, t).getOrUndefined() }), write: t => { e.set(t); }, read: () => e.get().getOrUndefined(), clear: e.clear }; })(), jB = Math.min, HB = Math.max, $B = Math.round, VB = (e, t, n) => { let o = t.x, r = t.y; const s = e.w, a = e.h, i = t.w, l = t.h, d = (n || "").split(""); return "b" === d[0] && (r += l), "r" === d[1] && (o += i), "c" === d[0] && (r += $B(l / 2)), "c" === d[1] && (o += $B(i / 2)), "b" === d[3] && (r -= a), "r" === d[4] && (o -= s), "c" === d[3] && (r -= $B(a / 2)), "c" === d[4] && (o -= $B(s / 2)), qB(o, r, s, a); }, qB = (e, t, n, o) => ({ x: e, y: t, w: n, h: o }), WB = { inflate: (e, t, n) => qB(e.x - t, e.y - n, e.w + 2 * t, e.h + 2 * n), relativePosition: VB, findBestRelativePosition: (e, t, n, o) => { for (let r = 0; r < o.length; r++) {
            const s = VB(e, t, o[r]);
            if (s.x >= n.x && s.x + s.w <= n.w + n.x && s.y >= n.y && s.y + s.h <= n.h + n.y)
                return o[r];
        } return null; }, intersect: (e, t) => { const n = HB(e.x, t.x), o = HB(e.y, t.y), r = jB(e.x + e.w, t.x + t.w), s = jB(e.y + e.h, t.y + t.h); return r - n < 0 || s - o < 0 ? null : qB(n, o, r - n, s - o); }, clamp: (e, t, n) => { let o = e.x, r = e.y, s = e.x + e.w, a = e.y + e.h; const i = t.x + t.w, l = t.y + t.h, d = HB(0, t.x - o), c = HB(0, t.y - r), u = HB(0, s - i), m = HB(0, a - l); return o += d, r += c, n && (s += d, a += c, o -= u, r -= m), s -= u, a -= m, qB(o, r, s - o, a - r); }, create: qB, fromClientRect: e => qB(e.left, e.top, e.width, e.height) }, KB = (() => { const e = {}, t = {}, n = {}; return { load: (n, o) => { const r = `Script at URL "${o}" failed to load`, s = `Script at URL "${o}" did not call \`tinymce.Resource.add('${n}', data)\` within 1 second`; if (void 0 !== e[n])
            return e[n]; {
            const a = new Promise(((e, a) => { const i = ((e, t, n = 1e3) => { let o = !1, r = null; const s = e => (...t) => { o || (o = !0, null !== r && (window.clearTimeout(r), r = null), e.apply(null, t)); }, a = s(e), i = s(t); return { start: (...e) => { o || null !== r || (r = window.setTimeout((() => i.apply(null, e)), n)); }, resolve: a, reject: i }; })(e, a); t[n] = i.resolve, ri.ScriptLoader.loadScript(o).then((() => i.start(s)), (() => i.reject(r))); }));
            return e[n] = a, a;
        } }, add: (o, r) => { void 0 !== t[o] && (t[o](r), delete t[o]), e[o] = Promise.resolve(r), n[o] = r; }, has: e => e in n, get: e => n[e], unload: t => { delete e[t], delete n[t]; } }; })();
    let YB;
    try {
        const e = "__storage_test__";
        YB = window.localStorage, YB.setItem(e, e), YB.removeItem(e);
    }
    catch (e) {
        YB = (() => { let e = {}, t = []; const n = { getItem: t => e[t] || null, setItem: (n, o) => { t.push(n), e[n] = String(o); }, key: e => t[e], removeItem: n => { t = t.filter((e => e === n)), delete e[n]; }, clear: () => { t = [], e = {}; }, length: 0 }; return Object.defineProperty(n, "length", { get: () => t.length, configurable: !1, enumerable: !1 }), n; })();
    }
    const XB = { geom: { Rect: WB }, util: { Delay: sg, Tools: an, VK: wg, URI: OC, EventDispatcher: nB, Observable: rB, I18n: li, LocalStorage: YB, ImageUploader: e => { const t = NE(), n = OE(e, t); return { upload: (t, o = !0) => n.upload(t, o ? TE(e) : void 0) }; } }, dom: { EventUtils: Wa, TreeWalker: Fr, TextSeeker: Ei, DOMUtils: ni, ScriptLoader: ri, RangeUtils: Fg, Serializer: Iw, StyleSheetLoader: Ks, ControlSelection: kg, BookmarkManager: Yf, Selection: Dw, Event: Wa.Event }, html: { Styles: Ia, Entities: ua, Node: up, Schema: Ra, DomParser: WC, Writer: Ap, Serializer: Tp }, Env: nn, AddOnManager: di, Annotator: Kf, Formatter: $E, UndoManager: qE, EditorCommands: eB, WindowManager: dE, NotificationManager: aE, EditorObservable: cB, Shortcuts: _B, Editor: AB, FocusManager: rg, EditorManager: UB, DOM: ni.DOM, ScriptLoader: ri.ScriptLoader, PluginManager: iE, ThemeManager: lE, ModelManager: Jw, IconManager: Qw, Resource: KB, FakeClipboard: zB, trim: an.trim, isArray: an.isArray, is: an.is, toArray: an.toArray, makeMap: an.makeMap, each: an.each, map: an.map, grep: an.grep, inArray: an.inArray, extend: an.extend, walk: an.walk, resolve: an.resolve, explode: an.explode, _addCacheSuffix: an._addCacheSuffix }, GB = an.extend(UB, XB);
    (e => { window.tinymce = e, window.tinyMCE = e; })(GB), (e => { if ("object" == typeof module)
        try {
            module.exports = e;
        }
        catch (e) { } })(GB);
}();
async function ajax(url, formData = null, type = 'json', method = 'GET', timeout = AJAX_TIMEOUT, skipError = false) {
    let result;
    const controller = new AbortController();
    window.setTimeout(() => {
        controller.abort();
    }, timeout);
    try {
        const response = await fetch(url, {
            'body': ['POST', 'PUT', 'DELETE', 'PATCH'].includes(method) ? formData : null,
            'cache': 'no-cache',
            'credentials': 'same-origin',
            'headers': {
                'X-CSRF-Token': getMeta('X-CSRF-Token') ?? '',
            },
            'keepalive': false,
            method,
            'mode': 'same-origin',
            'redirect': 'error',
            'referrer': window.location.href,
            'referrerPolicy': 'same-origin',
            'signal': controller.signal,
        });
        if (!response.ok && !skipError) {
            addSnackbar(`Request to "${url}" returned code ${response.status}`, 'failure', SNACKBAR_FAIL_LIFE);
            return false;
        }
        switch (type) {
            case 'json':
                result = await response.json();
                break;
            case 'blob':
                result = await response.blob();
                break;
            case 'array':
                result = await response.arrayBuffer();
                break;
            case 'form':
                result = await response.formData();
                break;
            default:
                result = await response.text();
                break;
        }
        return result;
    }
    catch (err) {
        if (err instanceof DOMException && err.name === 'AbortError') {
            addSnackbar(`Request to "${url}" timed out after ${timeout} milliseconds`, 'failure', SNACKBAR_FAIL_LIFE);
        }
        else {
            addSnackbar(`Request to "${url}" failed on fetch operation`, 'failure', SNACKBAR_FAIL_LIFE);
        }
        return false;
    }
}
function inputInit(input) {
    ['focus', 'change', 'input',].forEach((eventType) => {
        input.addEventListener(eventType, () => {
            ariaNation(input);
        });
    });
    ariaNation(input);
    if (input.getAttribute('type') === 'url' && !input.form) {
        input.addEventListener('paste', (event) => {
            void urlClean(event);
        });
    }
    if (input.classList.contains('toggle_details')) {
        input.addEventListener('click', () => {
            toggleDetailsButton(input);
        });
    }
}
function textareaInit(textarea) {
    if (!textarea.hasAttribute('placeholder')) {
        textarea.setAttribute('placeholder', textarea.value || textarea.type || 'placeholder');
    }
    if (textarea.maxLength > 0) {
        ['change', 'keydown', 'keyup', 'input'].forEach((eventType) => {
            textarea.addEventListener(eventType, (event) => {
                countInTextarea(event.target);
            });
        });
        countInTextarea(textarea);
    }
    if (textarea.classList.contains('tinymce') && textarea.id) {
        loadTinyMCE(textarea.id);
    }
}
function headingInit(heading) {
    if (!heading.hasAttribute('id')) {
        let id = String(heading.textContent)
            .replaceAll(/\s/gmu, '_')
            .replaceAll(/[^a-zA-Z0-9_-]/gmu, '')
            .replaceAll(/^\d+/gmu, '')
            .replaceAll(/_{2,}/gmu, '_')
            .replaceAll(/(?<beginning>^.{1,64})(?<theRest>.*$)/gmu, `$<beginning>`)
            .replaceAll(/^_+$/gmu, '');
        if (empty(id)) {
            id = 'heading';
        }
        let index = 1;
        let altId = id;
        while (document.querySelector(`#${altId}`)) {
            index += 1;
            altId = `${id}_${index}`;
        }
        heading.setAttribute('id', altId);
    }
    heading.addEventListener('click', (event) => {
        const elementUnderMouse = document.elementFromPoint(event.clientX, event.clientY);
        if (elementUnderMouse && elementUnderMouse.tagName === "A") {
            return;
        }
        const selection = window.getSelection();
        if (selection && selection.type !== 'Range') {
            const link = `${window.location.href.replaceAll(/(?<beforeSharp>^[^#]*)(?<afterSharp>#.*)?$/gmu, `$<beforeSharp>`)}#${event.target.getAttribute('id') ?? ''}`;
            navigator.clipboard.writeText(link)
                .then(() => {
                addSnackbar(`Anchor link for "${event.target.textContent ?? ''}" copied to clipboard`, 'success');
            }, () => {
                addSnackbar(`Failed to copy anchor link for "${event.target.textContent ?? ''}"`, 'failure');
            });
        }
    });
}
function formInit(form) {
    form.addEventListener('keypress', (event) => {
        formEnter(event);
    });
    form.querySelectorAll('button, datalist, fieldset, input, meter, progress, select, textarea')
        .forEach((item) => {
        if (!item.hasAttribute('data-noname') && (!item.hasAttribute('name') || empty(item.getAttribute('name'))) && !empty(item.id)) {
            item.setAttribute('name', item.id);
        }
    });
    form.querySelectorAll('input[type="email"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"]')
        .forEach((item) => {
        item.addEventListener('keydown', inputBackSpace);
        if (!empty(item.getAttribute('maxlength'))) {
            ['input', 'change',].forEach((eventType) => {
                item.addEventListener(eventType, autoNext);
            });
            item.addEventListener('paste', (event) => {
                void pasteSplit(event);
            });
        }
    });
}
function sampInit(samp) {
    samp.innerHTML = `<img loading="lazy" decoding="async"  src="/assets/images/copy.svg" alt="Click to copy block" class="copy_quote">${samp.innerHTML}`;
    const description = samp.getAttribute('data-description') ?? '';
    if (!empty(description)) {
        samp.innerHTML = `<span class="code_desc">${description}</span>${samp.innerHTML}`;
    }
    const source = samp.getAttribute('data-source') ?? '';
    if (!empty(source)) {
        samp.innerHTML = `${samp.innerHTML}<span class="quote_source">${source}</span>`;
    }
    samp.querySelector('.copy_quote')
        ?.addEventListener('click', (event) => {
        copyQuote(event.target);
    });
}
function codeInit(code) {
    code.innerHTML = `<img loading="lazy" decoding="async"  src="/assets/images/copy.svg" alt="Click to copy block" class="copy_quote">${code.innerHTML}`;
    const description = code.getAttribute('data-description') ?? '';
    if (!empty(description)) {
        code.innerHTML = `<span class="code_desc">${description}</span>${code.innerHTML}`;
    }
    const source = code.getAttribute('data-source') ?? '';
    if (!empty(source)) {
        code.innerHTML = `${code.innerHTML}<span class="quote_source">${source}</span>`;
    }
    code.querySelector('.copy_quote')
        ?.addEventListener('click', (event) => {
        copyQuote(event.target);
    });
}
function blockquoteInit(quote) {
    quote.innerHTML = `<img loading="lazy" decoding="async"  src="/assets/images/copy.svg" alt="Click to copy block" class="copy_quote">${quote.innerHTML}`;
    const author = quote.getAttribute('data-author') ?? '';
    if (!empty(author)) {
        quote.innerHTML = `<span class="quote_author">${author}</span>${quote.innerHTML}`;
    }
    const source = quote.getAttribute('data-source') ?? '';
    if (!empty(source)) {
        quote.innerHTML = `${quote.innerHTML}<span class="quote_source">${source}</span>`;
    }
    quote.querySelector('.copy_quote')
        ?.addEventListener('click', (event) => {
        copyQuote(event.target);
    });
}
function qInit(quote) {
    quote.setAttribute('data-tooltip', 'Click to copy quote');
    quote.addEventListener('click', (event) => {
        copyQuote(event.target);
    });
}
function detailsInit(details) {
    if (!details.classList.contains('persistent') && !details.classList.contains('spoiler') && !details.classList.contains('adult')) {
        const summary = details.querySelector('summary');
        if (summary) {
            summary.addEventListener('click', (event) => {
                closeAllDetailsTags(event.target);
                resetDetailsTags(event.target);
            });
        }
    }
}
function imgInit(img) {
    if (empty(img.alt)) {
        img.alt = basename(String(img.src));
    }
    if (img.classList.contains('gallery_zoom')) {
        const parent = img.parentElement;
        if (parent && parent.nodeName.toLowerCase() !== 'a') {
            const link = document.createElement('a');
            link.href = img.src;
            link.target = '_blank';
            link.setAttribute('data-tooltip', (img.hasAttribute('data-tooltip') ? String(img.getAttribute('data-tooltip')) : String(img.alt)));
            link.classList.add('gallery_zoom');
            const clone = img.cloneNode(true);
            clone.classList.remove('gallery_zoom');
            link.appendChild(clone);
            img.replaceWith(link);
        }
        else if (parent && parent.nodeName.toLowerCase() === 'a') {
            parent.href = img.src;
            parent.target = '_blank';
            parent.setAttribute('data-tooltip', (img.hasAttribute('data-tooltip') ? String(img.getAttribute('data-tooltip')) : String(img.alt)));
            parent.classList.add('gallery_zoom');
            img.classList.contains('gallery_zoom');
        }
    }
}
function dialogInit(dialog) {
    if (dialog.classList.contains('modal')) {
        dialog.addEventListener('click', (event) => {
            const target = event.target;
            if (target) {
                if (target === dialog) {
                    dialog.close();
                }
            }
        });
    }
}
function anchorInit(anchor) {
    if (empty(anchor.href)) {
        return;
    }
    const currentURL = new URL(anchor.href);
    if (currentURL.host !== window.location.host) {
        anchor.target = '_blank';
    }
    if (anchor.target === '_blank' && !anchor.innerHTML.includes('assets/images/newtab.svg') && !anchor.classList.contains('no_new_tab_icon')) {
        anchor.innerHTML += '<img class="new_tab_icon" src="/assets/images/newtab.svg" alt="Opens in new tab">';
    }
    else if (!empty(anchor.href) && !empty(currentURL.hash) && currentURL.origin + currentURL.host + currentURL.pathname === window.location.origin + window.location.host + window.location.pathname) {
        anchor.addEventListener('click', () => {
            if (!window.location.hash.toLowerCase()
                .startsWith('#gallery=')) {
                history.replaceState(document.title, document.title, `${currentURL.hash}`);
            }
        });
    }
}
function customizeNewElements(newNode) {
    if (newNode.nodeType === 1) {
        const nodeName = newNode.nodeName.toLowerCase();
        switch (nodeName) {
            case 'a':
                anchorInit(newNode);
                break;
            case 'blockquote':
                blockquoteInit(newNode);
                break;
            case 'code':
                codeInit(newNode);
                break;
            case 'details':
                detailsInit(newNode);
                break;
            case 'form':
                formInit(newNode);
                break;
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
                headingInit(newNode);
                break;
            case 'img':
                imgInit(newNode);
                break;
            case 'input':
                inputInit(newNode);
                break;
            case 'q':
                qInit(newNode);
                break;
            case 'samp':
                sampInit(newNode);
                break;
            case 'textarea':
                textareaInit(newNode);
                break;
            default:
                break;
        }
    }
}
function getAllDetailsTags() {
    return document.querySelectorAll('details:not(.persistent):not(.spoiler):not(.adult)');
}
function closeAllDetailsTags(target) {
    const details = target.parentElement;
    if (details) {
        if (details.open) {
            getAllDetailsTags().forEach((tag) => {
                if (tag !== details) {
                    tag.open = false;
                }
            });
        }
    }
}
function clickOutsideDetailsTags(initialEvent, details) {
    if (details !== initialEvent.target && !details.contains(initialEvent.target)) {
        details.open = false;
        document.removeEventListener('click', (event) => {
            clickOutsideDetailsTags(event, details);
        });
    }
}
function resetDetailsTags(target) {
    const clickedDetails = target.parentElement;
    getAllDetailsTags().forEach((details) => {
        if (details.open && details !== clickedDetails && !details.contains(clickedDetails)) {
            details.open = false;
        }
        else if (details.classList.contains('popup')) {
            document.addEventListener('click', (event) => {
                clickOutsideDetailsTags(event, details);
            });
        }
    });
}
function toggleDetailsButton(input) {
    const detailsId = input.getAttribute('data-details-id');
    if (detailsId) {
        let details = document.getElementById(detailsId);
        if (details) {
            if (details.open) {
                details.open = false;
            }
            else {
                details.open = true;
            }
        }
    }
    input.blur();
}
function addSnackbar(text, color = '', milliseconds = 3000) {
    const snacks = document.querySelector('snack-bar');
    const template = document.querySelector('#snackbar_template');
    if (snacks && template) {
        const newSnack = template.content.cloneNode(true);
        const snack = newSnack.querySelector('dialog');
        if (snack !== null) {
            const textBlock = snack.querySelector('.snack_text');
            if (textBlock !== null) {
                textBlock.innerHTML = text;
            }
            snack.querySelector('snack-close')?.setAttribute('data-close-in', String(milliseconds));
            if (color) {
                snack.classList.add(color);
            }
            snacks.appendChild(snack);
            snack.show();
        }
    }
}
function getMeta(metaName) {
    const metas = Array.from(document.querySelectorAll('meta'));
    const tag = metas.find((obj) => {
        return obj.name === metaName;
    });
    if (tag) {
        return tag.getAttribute('content');
    }
    return null;
}
function updateHistory(newUrl, title) {
    if (document.title !== title) {
        document.title = title;
    }
    if (document.location.href !== newUrl) {
        window.history.pushState(title, title, newUrl);
    }
}
function submitIntercept(form, callable) {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        event.stopPropagation();
        callable();
        return false;
    });
    form.addEventListener('keydown', (event) => {
        if (event.code === 'Enter') {
            event.preventDefault();
            event.stopPropagation();
            callable();
            return false;
        }
        return true;
    });
}
function deleteRow(element) {
    const table = element.closest('table');
    const tr = element.closest('tr');
    if (table && tr) {
        table.deleteRow(tr.rowIndex);
        return true;
    }
    return false;
}
function basename(text) {
    return text.replace(/^.*\/|\.[^.]*$/gu, '');
}
function rawurlencode(str) {
    const definitelyString = String(str);
    return encodeURIComponent(definitelyString).
        replace(/!/ug, '%21').
        replace(/'/ug, '%27').
        replace(/\(/ug, '%28').
        replace(/\)/ug, '%29').
        replace(/\*/ug, '%2A');
}
function empty(variable) {
    if (typeof variable === 'undefined' || variable === null || variable === false || variable === 0 || variable === 'NaN') {
        return true;
    }
    if (typeof variable === 'string') {
        return (/^[\s\p{C}]*$/ui).test(variable);
    }
    if (Array.isArray(variable)) {
        return variable.length === 0;
    }
    if (variable instanceof NodeList) {
        return variable.length === 0;
    }
    if (variable instanceof HTMLCollection) {
        return variable.length === 0;
    }
    if (typeof variable === 'object') {
        return JSON.stringify(variable) === '{}';
    }
    return false;
}
function pageRefresh() {
    const url = new URL(document.location.href);
    url.searchParams.set('force_reload', String(Date.now()));
    window.location.replace(url.toString());
}
function copyQuote(target) {
    let node;
    if (target.tagName.toLowerCase() === 'q') {
        node = target;
    }
    else {
        node = target.parentElement;
    }
    if (!node) {
        return '';
    }
    const tagName = node.tagName.toLowerCase();
    let tag;
    switch (tagName) {
        case 'samp':
            tag = 'Sample';
            break;
        case 'code':
            tag = 'Code';
            break;
        case 'blockquote':
        case 'q':
            tag = 'Quote';
            break;
        default:
            return '';
    }
    let quoteText = String(node.textContent);
    if (tagName === 'blockquote' && node.hasAttribute('data-author')) {
        const authorMatch = new RegExp(`^(${String(node.getAttribute('data-author'))})`, 'ui');
        quoteText = quoteText.replace(authorMatch, '');
    }
    if ((tagName === 'samp' || tagName === 'code') && node.hasAttribute('data-description')) {
        const descMatch = new RegExp(`^(${String(node.getAttribute('data-description'))})`, 'ui');
        quoteText = quoteText.replace(descMatch, '');
    }
    if ((tagName === 'blockquote' || tagName === 'samp' || tagName === 'code') && node.hasAttribute('data-source')) {
        const sourceMatch = new RegExp(`(${String(node.getAttribute('data-source'))})$`, 'ui');
        quoteText = quoteText.replace(sourceMatch, '');
    }
    navigator.clipboard.writeText(quoteText).then(() => {
        addSnackbar(`${tag} copied to clipboard`, 'success');
    }, () => {
        addSnackbar(`Failed to copy ${tag.toLowerCase()}`, 'failure');
    });
    return String(node.textContent);
}
async function is_file(url) {
    return new Promise((resolve, reject) => {
        fetch(url, { 'method': 'HEAD' }).
            then((response) => {
            if (response.ok) {
                resolve(true);
            }
            else {
                resolve(false);
            }
        }).
            catch((error) => {
            reject(error);
        });
    });
}
const TIMEZONE = Intl.DateTimeFormat()
    .resolvedOptions().timeZone;
const AJAX_TIMEOUT = 60000;
const SNACKBAR_FAIL_LIFE = 10000;
function init() {
    const inputs = document.querySelectorAll('input');
    if (!empty(inputs)) {
        inputs.forEach((input) => {
            inputInit(input);
        });
    }
    const textAreas = document.querySelectorAll('textarea');
    if (!empty(textAreas)) {
        textAreas.forEach((textarea) => {
            textareaInit(textarea);
        });
    }
    const anchors = document.querySelectorAll('a');
    if (!empty(anchors)) {
        anchors.forEach((anchor) => {
            anchorInit(anchor);
        });
    }
    const headings = document.querySelectorAll('h1:not(#h1_title), h2, h3, h4, h5, h6');
    if (!empty(headings)) {
        headings.forEach((heading) => {
            headingInit(heading);
        });
    }
    const forms = document.querySelectorAll('form');
    if (!empty(forms)) {
        forms.forEach((form) => {
            formInit(form);
        });
    }
    const detailsTags = document.querySelectorAll('details');
    if (!empty(detailsTags)) {
        detailsTags.forEach((details) => {
            detailsInit(details);
        });
    }
    const sampTags = document.querySelectorAll('samp');
    if (!empty(sampTags)) {
        sampTags.forEach((samp) => {
            sampInit(samp);
        });
    }
    const codeTags = document.querySelectorAll('code');
    if (!empty(codeTags)) {
        codeTags.forEach((code) => {
            codeInit(code);
        });
    }
    const blockquotes = document.querySelectorAll('blockquote');
    if (!empty(blockquotes)) {
        blockquotes.forEach((blockquote) => {
            blockquoteInit(blockquote);
        });
    }
    const quotes = document.querySelectorAll('q');
    if (!empty(quotes)) {
        quotes.forEach((quote) => {
            qInit(quote);
        });
    }
    const dialogs = document.querySelectorAll('dialog');
    if (!empty(dialogs)) {
        dialogs.forEach((dialog) => {
            dialogInit(dialog);
        });
    }
    const images = document.querySelectorAll('img');
    if (!empty(images)) {
        images.forEach((image) => {
            imgInit(image);
        });
    }
    customElements.define('nav-show', NavShow);
    customElements.define('nav-hide', NavHide);
    customElements.define('side-show', SideShow);
    customElements.define('side-hide', SideHide);
    customElements.define('login-form', LoginForm);
    customElements.define('back-to-top', BackToTop);
    customElements.define('time-r', Timer);
    customElements.define('web-share', WebShare);
    customElements.define('tool-tip', Tooltip);
    customElements.define('snack-close', SnackbarClose);
    customElements.define('gallery-overlay', Gallery);
    customElements.define('gallery-close', GalleryClose);
    customElements.define('gallery-prev', GalleryPrev);
    customElements.define('gallery-next', GalleryNext);
    customElements.define('gallery-image', GalleryImage);
    customElements.define('image-carousel', CarouselList);
    customElements.define('og-image', OGImage);
    customElements.define('password-show', PasswordShow);
    customElements.define('password-requirements', PasswordRequirements);
    customElements.define('password-strength', PasswordStrength);
    customElements.define('like-dis', Likedis);
    customElements.define('tab-menu', TabMenu);
    customElements.define('image-upload', ImageUpload);
    customElements.define('select-custom', SelectCustom);
    customElements.define('post-form', PostForm);
    const newNodesObserver = new MutationObserver((mutations_list) => {
        mutations_list.forEach((mutation) => {
            mutation.addedNodes.forEach((added_node) => {
                customizeNewElements(added_node);
            });
        });
    });
    newNodesObserver.observe(document, {
        'attributes': false,
        'characterData': false,
        'childList': true,
        'subtree': true
    });
    cleanGET();
    hashCheck();
    router();
}
const configUrlElement = document.querySelector('head > link[rel="preload"][as="fetch"]');
let sharedWithPHP = {};
if (configUrlElement && configUrlElement.getAttribute('href')) {
    const configUrl = configUrlElement.getAttribute('href');
    fetch(configUrl)
        .then(response => response.json())
        .then(config => {
        sharedWithPHP = config;
    })
        .catch(() => {
        sharedWithPHP = {};
    })
        .finally(() => {
        sharedWithPHP = Object.freeze(sharedWithPHP);
    });
}
document.addEventListener('DOMContentLoaded', init);
window.addEventListener('hashchange', () => {
    hashCheck();
});
function ariaNation(inputElement) {
    inputElement.setAttribute('aria-invalid', String(!inputElement.validity.valid));
    if (!inputElement.hasAttribute('placeholder')) {
        inputElement.setAttribute('placeholder', inputElement.value || inputElement.type || 'placeholder');
    }
    if (empty(inputElement.getAttribute('type'))) {
        inputElement.setAttribute('type', 'text');
    }
    let type;
    if (empty(inputElement.type)) {
        type = 'text';
    }
    else {
        type = inputElement.type;
    }
    if (['text', 'search', 'url', 'tel', 'email', 'password', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'checkbox', 'radio', 'file',].includes(String(type))) {
        if (inputElement.required) {
            inputElement.setAttribute('aria-required', String(true));
        }
        else {
            inputElement.setAttribute('aria-required', String(false));
        }
    }
    if (type === 'checkbox') {
        inputElement.setAttribute('role', 'checkbox');
        inputElement.setAttribute('aria-checked', String(inputElement.checked));
        if (inputElement.indeterminate) {
            inputElement.setAttribute('aria-checked', 'mixed');
        }
    }
    if (type === 'checkbox') {
        inputElement.setAttribute('value', inputElement.value);
    }
}
function buttonToggle(button, enable = true) {
    let spinner;
    if (button.form) {
        spinner = button.form.querySelector('.spinner');
    }
    if (!spinner) {
        const buttonParent = button.parentElement;
        if (buttonParent) {
            spinner = buttonParent.querySelector('.spinner');
        }
    }
    if (button.disabled) {
        if (enable) {
            button.disabled = false;
        }
        if (spinner) {
            spinner.classList.add('hidden');
        }
    }
    else {
        button.disabled = true;
        if (spinner) {
            spinner.classList.remove('hidden');
        }
    }
}
function countInTextarea(textarea) {
    if (textarea.labels[0] && textarea.maxLength) {
        const label = textarea.labels[0];
        label.setAttribute('data-curlength', `(${textarea.value.length}/${textarea.maxLength}ch)`);
        label.classList.remove('at_the_limit', 'close_to_limit');
        if (textarea.value.length >= textarea.maxLength) {
            label.classList.add('at_the_limit');
        }
        else if (((100 * textarea.value.length) / textarea.maxLength) >= 75) {
            label.classList.add('close_to_limit');
        }
    }
}
function nextInput(initial, reverse = false) {
    const form = initial.form;
    if (form) {
        let previous;
        for (const moveTo of form.querySelectorAll('input[type="email"], input[type="password"], input[type="search"], input[type="tel"], input[type="text"], input[type="url"]')) {
            if (reverse) {
                if (moveTo === initial) {
                    if (previous) {
                        return previous;
                    }
                    return null;
                }
            }
            else if (previous && previous === initial) {
                return moveTo;
            }
            previous = moveTo;
        }
    }
    return null;
}
async function pasteSplit(event) {
    const originalString = event.clipboardData?.getData('text/plain');
    event.preventDefault();
    event.stopImmediatePropagation();
    let buffer = originalString;
    let current = event.target;
    if (current === null) {
        return;
    }
    if (current.getAttribute('type') === 'url') {
        buffer = urlCleanString(buffer);
    }
    if (current.value && !(current.selectionStart === 0 && current.selectionEnd === current.value.length)) {
        pasteAndMove(current, buffer);
        return;
    }
    let maxLength = parseInt(current.getAttribute('maxlength') ?? '0', 10);
    while (current !== null && maxLength && buffer.length > maxLength) {
        pasteAndMove(current, buffer.substring(0, maxLength));
        current.dispatchEvent(new Event('input', {
            'bubbles': true,
            'cancelable': true,
        }));
        if (!current.validity.valid) {
            return;
        }
        buffer = buffer.substring(maxLength);
        if (current.getAttribute('data-no-spill')) {
            return;
        }
        current = nextInput(current, false);
        if (current) {
            if (current.value) {
                return;
            }
            current.focus();
            maxLength = parseInt(current.getAttribute('maxlength') ?? '0', 10);
        }
    }
    if (current) {
        pasteAndMove(current, buffer);
        current.dispatchEvent(new Event('input', {
            'bubbles': true,
            'cancelable': true,
        }));
    }
}
function pasteAndMove(input, text) {
    const start = input.selectionStart;
    const end = input.selectionEnd;
    const selectedLength = end - start;
    const lengthAfterPaste = input.value.length + text.length - selectedLength;
    let newText;
    if (input.maxLength && lengthAfterPaste > input.maxLength) {
        newText = text.substring(0, input.maxLength - input.value.length + selectedLength);
    }
    else {
        newText = text;
    }
    const newCursorPos = start + newText.length;
    input.value = input.value.substring(0, start) + newText + input.value.substring(end);
    input.setSelectionRange(newCursorPos, newCursorPos);
    input.scrollLeft = (input.scrollWidth / input.value.length) * newCursorPos;
}
function formEnter(event) {
    if (event.target) {
        const form = event.target.form;
        if (form && (event.code === 'Enter' || event.code === 'NumpadEnter') && !form.action) {
            event.stopPropagation();
            event.preventDefault();
            return false;
        }
    }
    return true;
}
function inputBackSpace(event) {
    const current = event.target;
    if (event.code === 'Backspace' && !current.value) {
        const moveTo = nextInput(current, true);
        if (moveTo) {
            moveTo.focus();
            moveTo.selectionEnd = moveTo.value.length;
            moveTo.selectionStart = moveTo.value.length;
        }
    }
}
function autoNext(event) {
    const current = event.target;
    const maxLength = parseInt(current.getAttribute('maxlength') ?? '0', 10);
    if (maxLength && current.value.length === maxLength && current.validity.valid) {
        const moveTo = nextInput(current, false);
        if (moveTo) {
            moveTo.focus();
        }
    }
}
const CUSTOM_COLOR_MAP = {
    '#17141F': 'body',
    '#19424D': 'dark_border',
    '#231F2E': 'block',
    '#266373': 'light_border',
    '#2E293D': 'article',
    '#808080': 'disabled',
    '#8AE59C': 'success',
    '#9AD4EA': 'interactive',
    '#E6B63D': 'warning',
    '#F3A0B6': 'failure',
    '#F5F0F0': 'text',
};
const TINY_SETTINGS = {
    'automatic_uploads': true,
    'autosave_ask_before_unload': true,
    'autosave_interval': '5s',
    'autosave_restore_when_empty': true,
    'base_url': '/tinymce/',
    'block_formats': 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;',
    'block_unsupported_drop': true,
    'branding': true,
    'browser_spellcheck': true,
    'color_map': Object.keys(CUSTOM_COLOR_MAP)
        .map((key) => {
        return [key, CUSTOM_COLOR_MAP[key]];
    })
        .flat(),
    'content_css': '/assets/styles/tinymce.css',
    'content_security_policy': "default-src 'self'",
    'contextmenu': 'emoticons link image',
    'custom_colors': false,
    'default_link_target': '_blank',
    'document_base_url': `${window.location.protocol}//${window.location.hostname}/`,
    'emoticons_database': 'emojis',
    'entity_encoding': 'named',
    'file_picker_types': 'image',
    'font_formats': '',
    'fontsize_formats': '',
    'formats': {
        'aligncenter': {
            'classes': 'tiny_align_center',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
        },
        'alignjustify': {
            'classes': 'tiny_align_justify',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
        },
        'alignleft': {
            'classes': 'tiny_align_left',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
        },
        'alignright': {
            'classes': 'tiny_align_right',
            'remove': 'none',
            'selector': 'strong,em,sub,sup,s,a,time,p,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img,audio,video,blockquote',
        },
        'forecolor': {
            'attributes': {
                'class': (value) => {
                    return `tiny_color_${String(CUSTOM_COLOR_MAP[value.value])}`;
                },
            },
            'inline': 'span',
            'remove': 'none',
        },
        'hilitecolor': {
            'attributes': {
                'class': (value) => {
                    return `tiny_bg_color_${String(CUSTOM_COLOR_MAP[value.value])}`;
                },
            },
            'inline': 'span',
            'remove': 'none',
        },
        'list_circle': {
            'classes': 'tiny_list_circle',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list_decimal': {
            'classes': 'tiny_list_decimal',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list_decimal_leading_zero': {
            'classes': 'tiny_list_decimal_leading_zero',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list_disc': {
            'classes': 'tiny_list_disc',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list_disclosure_closed': {
            'classes': 'tiny_list_disclosure_closed',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list_disclosure_open': {
            'classes': 'iny_list_disclosure_open',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list_lower_alpha': {
            'classes': 'tiny_list_lower_alpha',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list_lower_greek': {
            'classes': 'tiny_list_lower_greek',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list_lower_roman': {
            'classes': 'tiny_list_lower_roman',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list_square': {
            'classes': 'tiny_list_square',
            'remove': 'none',
            'selector': 'ul,ul>li'
        },
        'list_upper_alpha': {
            'classes': 'tiny_list_upper_alpha',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'list_upper_roman': {
            'classes': 'tiny_list_upper_roman',
            'remove': 'none',
            'selector': 'ol,ol>li'
        },
        'underline': {
            'classes': 'tiny_underline',
            'inline': 'span',
            'remove': 'none',
        },
        'valignbottom': {
            'classes': 'tiny_valign_bottom',
            'remove': 'none',
            'selector': 'td,th,table',
        },
        'valignmiddle': {
            'classes': 'tiny_valign_middle',
            'remove': 'none',
            'selector': 'td,th,table',
        },
        'valigntop': {
            'classes': 'tiny_valign_top',
            'remove': 'none',
            'selector': 'td,th,table',
        },
    },
    'hidden_input': false,
    'image_advtab': false,
    'image_caption': false,
    'image_class_list': [
        {
            'title': 'Default',
            'value': 'w50pc middle block gallery_zoom'
        },
        {
            'menu': [
                {
                    'title': 'Quarter width',
                    'value': 'w25pc middle block gallery_zoom'
                },
                {
                    'title': 'Half width',
                    'value': 'w50pc middle block gallery_zoom'
                },
                {
                    'title': '3 quarters width',
                    'value': 'w75pc middle block gallery_zoom'
                },
                {
                    'title': 'Full width',
                    'value': 'w100pc middle block gallery_zoom'
                }
            ],
            'title': 'Block'
        },
        {
            'menu': [
                {
                    'title': 'Quarter width',
                    'value': 'w25pc middle gallery_zoom'
                },
                {
                    'title': 'Half width',
                    'value': 'w50pc middle gallery_zoom'
                },
                {
                    'title': '3 quarters width',
                    'value': 'w75pc middle gallery_zoom'
                },
                {
                    'title': 'Full width',
                    'value': 'w100pc middle gallery_zoom'
                }
            ],
            'title': 'Inline'
        },
        {
            'title': 'Icon',
            'value': 'link_icon'
        }
    ],
    'image_description': true,
    'image_dimensions': false,
    'image_title': false,
    'image_uploadtab': true,
    'images_file_types': 'jpeg,jpg,png,gif,bmp,webp,svg',
    'images_reuse_filename': true,
    'images_upload_credentials': true,
    'images_upload_url': '/api/upload/',
    'insertdatetime_element': true,
    'invalid_elements': 'acronym,applet,area,aside,base,basefont,bgsound,big,blink,body,button,canvas,center,content,datalist,dialog,dir,embed,fieldset,figure,figcaption,font,footer,form,frame,frameset,head,header,hgroup,html,iframe,input,image,keygen,legend,link,main,map,marquee,menuitem,meter,nav,nobr,noembed,noframes,noscript,object,optgroup,option,param,picture,plaintext,portal,pre,progress,rb,rp,rt,rtc,ruby,script,select,selectmenu,shadow,slot,strike,style,spacer,template,textarea,title,tt,xmp',
    'invalid_styles': 'font-size line-height',
    'license_key': 'gpl',
    'lineheight_formats': '',
    'link_assume_external_targets': 'https',
    'link_context_toolbar': true,
    'link_default_protocol': 'https',
    'link_target_list': [
        {
            'title': 'New window',
            'value': '_blank'
        },
        {
            'title': 'Current window',
            'value': '_self'
        }
    ],
    'link_title': false,
    'lists_indent_on_tab': true,
    'menu': {
        'edit': {
            'items': 'undo redo | cut copy paste pastetext | selectall | searchreplace',
            'title': 'Edit',
        },
        'file': {
            'items': 'newdocument restoredraft',
            'title': 'File',
        },
        'format': {
            'items': 'underline strikethrough superscript subscript | align | styles',
            'title': 'Format',
        },
        'help': {
            'items': 'help wordcount',
            'title': 'Help',
        },
        'insert': {
            'items': 'link image codeformat | emoticons charmap hr | insertdatetime',
            'title': 'Insert',
        },
        'table': {
            'items': 'inserttable | cell row column | deletetable',
            'title': 'Table',
        },
        'view': {
            'items': 'code preview | visualaid visualchars visualblocks | fullscreen',
            'title': 'View',
        },
    },
    'menubar': 'file edit view format insert table help',
    'object_resizing': false,
    'paste_block_drop': true,
    'paste_data_images': false,
    'paste_remove_styles_if_webkit': true,
    'paste_webkit_styles': 'none',
    'plugins': 'autolink autosave charmap code emoticons fullscreen help image insertdatetime link lists preview quickbars searchreplace table visualblocks visualchars wordcount',
    'promotion': false,
    'quickbars_insert_toolbar': false,
    'readonly': false,
    'referrer_policy': 'no-referrer',
    'relative_urls': false,
    'remove_script_host': true,
    'remove_trailing_brs': true,
    'resize_img_proportional': true,
    'schema': 'html5-strict',
    'selector': 'textarea.tinymce',
    'skin': 'oxide-dark',
    'style_formats': [
        {
            'items': [
                {
                    'format': 'list_decimal',
                    'title': 'Decimal (default)'
                },
                {
                    'format': 'list_decimal_leading_zero',
                    'title': 'Decimal, leading zero'
                },
                {
                    'format': 'list_lower_alpha',
                    'title': 'Lower Latin'
                },
                {
                    'format': 'list_lower_greek',
                    'title': 'Lower Greek'
                },
                {
                    'format': 'list_lower_roman',
                    'title': 'Lower Roman'
                },
                {
                    'format': 'list_upper_alpha',
                    'title': 'Upper Latin'
                },
                {
                    'format': 'list_upper_roman',
                    'title': 'Upper Roman'
                },
            ],
            'title': 'Ordered lists'
        },
        {
            'items': [
                {
                    'format': 'list_circle',
                    'title': 'Circle'
                },
                {
                    'format': 'list_disc',
                    'title': 'Disc (default)'
                },
                {
                    'format': 'list_disclosure_closed',
                    'title': 'Disclosure closed'
                },
                {
                    'format': 'list_disclosure_open',
                    'title': 'Disclosure open'
                },
                {
                    'format': 'list_square',
                    'title': 'Square'
                },
            ],
            'title': 'Unordered lists'
        },
    ],
    'style_formats_autohide': true,
    'table_advtab': false,
    'table_appearance_options': false,
    'table_border_styles': [
        {
            'title': 'Solid',
            'value': 'solid'
        },
    ],
    'table_border_widths': [
        {
            'title': 'default',
            'value': '0.125rem'
        },
    ],
    'table_cell_advtab': false,
    'table_default_attributes': {},
    'table_header_type': 'sectionCells',
    'table_resize_bars': false,
    'table_row_advtab': false,
    'table_sizing_mode': 'relative',
    'table_style_by_css': false,
    'table_toolbar': 'tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | tabledelete',
    'theme_advanced_default_foreground_color': "#F5F0F0",
    'toolbar': 'undo redo | blocks | bold italic | forecolor backcolor | blockquote bullist numlist | removeformat',
    'toolbar_mode': 'wrap',
    'valid_styles': {},
    'visual': true,
    'visualblocks_default_state': false,
};
function tinyMCEtoTextarea(textarea, tinyInstance) {
    textarea.value = String(tinyInstance.getContent());
    textarea.dispatchEvent(new Event('input'));
}
function tinyMCEHideInputs() {
    const dialog = document.querySelector('div[role=dialog].tox-dialog');
    if (dialog) {
        const title = dialog.querySelector('div.tox-dialog__title');
        if (title) {
            const labels = dialog.querySelectorAll('label');
            const titleText = String(title.textContent)
                .toLowerCase();
            if (titleText === 'insert/edit image') {
                labels.forEach((item) => {
                    if (String(item.textContent)
                        .toLowerCase() === 'source' && item.parentElement) {
                        item.parentElement.classList.add('hidden');
                    }
                });
            }
            else if (titleText === 'cell properties' || titleText === 'row properties') {
                labels.forEach((item) => {
                    const itemText = String(item.textContent)
                        .toLowerCase();
                    if ((itemText === 'height' || itemText === 'width' || itemText === 'scope') && item.parentElement) {
                        item.parentElement.classList.add('hidden');
                    }
                });
            }
        }
    }
}
function loadTinyMCE(id, noMedia = true, noRestoreOnEmpty = false) {
    if ((/^\s*$/ui).exec(id)) {
        return;
    }
    const textarea = document.querySelector(`#${id}`);
    if (textarea) {
        const settings = TINY_SETTINGS;
        settings.selector = `#${id}`;
        if (noMedia) {
            settings.plugins = String(settings.plugins)
                .replace('image ', '');
            settings.images_upload_url = '';
            settings.menu.insert.items = settings.menu.insert.items.replace('image ', '');
        }
        if (noRestoreOnEmpty) {
            settings.autosave_restore_when_empty = false;
        }
        void import('/tinymce/tinymce.min.js').then(() => {
            void tinymce.init(settings)
                .then(() => {
                const tinyInstance = tinymce.get(id);
                if (tinyInstance !== null) {
                    tinyInstance.on('OpenWindow', () => {
                        tinyMCEHideInputs();
                    });
                    [
                        'CloseWindow',
                        'FormatApply',
                        'FormatRemove',
                        'ObjectResized',
                        'NewBlock',
                        'Undo',
                        'Redo',
                        'SetAttrib',
                        'NewRow',
                        'NewCell',
                        'TableModified',
                        'Change',
                        'RestoreDraft',
                        'CommentChange',
                        'ListMutation',
                        'input',
                        'paste',
                        'cut',
                        'reset'
                    ].forEach((eventType) => {
                        tinyInstance.on(eventType, () => {
                            tinyMCEtoTextarea(textarea, tinyInstance);
                        });
                    });
                }
            });
        });
    }
}
function saveTinyMCE(id, textareaOnly = false) {
    if ((/^\s*$/ui).exec(id)) {
        return;
    }
    const textarea = document.querySelector(`#${id}`);
    if (textarea !== null) {
        void import('/tinymce/tinymce.min.js').then(() => {
            const tinyInstance = tinymce.get(id);
            if (tinyInstance !== null) {
                if (textareaOnly) {
                    tinyMCEtoTextarea(textarea, tinyInstance);
                }
                else {
                    tinyInstance.save();
                }
            }
        });
    }
}
function cleanGET() {
    const url = new URL(document.location.href);
    url.searchParams.delete('cache_reset');
    url.searchParams.delete('force_reload');
    window.history.replaceState(document.title, document.title, url.toString());
}
async function urlClean(event) {
    const originalString = event.clipboardData?.getData('text/plain');
    event.preventDefault();
    event.stopImmediatePropagation();
    const current = event.target;
    if (current === null) {
        return;
    }
    pasteAndMove(current, urlCleanString(originalString));
    current.dispatchEvent(new Event('input', {
        'bubbles': true,
        'cancelable': true,
    }));
}
function urlCleanString(url) {
    const paramsToDelete = sharedWithPHP?.tracking_query_parameters || [];
    const urlNew = new URL(url);
    for (const param of paramsToDelete) {
        urlNew.searchParams.delete(param);
    }
    return decodeURI(urlNew.toString());
}
function hashCheck() {
    const url = new URL(document.location.href);
    const hash = url.hash;
    const Gallery = document.querySelector('gallery-overlay');
    const galleryLink = /#gallery=\d+/ui;
    if (Gallery) {
        if (galleryLink.test(hash)) {
            const imageID = Number(hash.replace(/(?<hash>#gallery=)(?<number>\d+)/ui, '$<number>'));
            if (imageID) {
                if (Gallery.images[imageID - 1]) {
                    Gallery.current = imageID - 1;
                }
                else {
                    addSnackbar(`Image number ${imageID} not found on page`, 'failure');
                    window.history.replaceState(document.title, document.title, document.location.href.replace(hash, ''));
                }
            }
        }
        else {
            Gallery.close();
        }
    }
}
function router() {
    const url = new URL(document.location.href);
    const path = url.pathname.replace(/(?<startingSlash>\/)(?<url>.*)(?<endingSlash>\/)?/ui, '$<url>').
        toLowerCase().
        split('/');
    if (!empty(path[0])) {
        if (path[0] === 'bictracker') {
            if (!empty(path[1])) {
                if (path[1] === 'keying') {
                    void import('/assets/controllers/bictracker/keying.js').then((module) => { new module.bicKeying(); });
                }
                else if (path[1] === 'search') {
                    void import('/assets/controllers/bictracker/search.js').then((module) => { new module.bicRefresh(); });
                }
            }
        }
        else if (path[0] === 'fftracker') {
            if (!empty(path[1])) {
                if (path[1] === 'track') {
                    void import('/assets/controllers/fftracker/track.js').then((module) => {
                        new module.ffTrack();
                    });
                }
                else if (path[1] === 'crests') {
                    void import('/assets/controllers/fftracker/crests.js').then((module) => {
                        new module.ffCrests();
                    });
                }
                else if (['characters', 'freecompanies', 'linkshells', 'crossworldlinkshells', 'crossworld_linkshells', 'pvpteams',].includes(String(path[1]))) {
                    void import('/assets/controllers/fftracker/entity.js').then((module) => { new module.ffEntity(); });
                }
            }
        }
        else if (path[0] === 'uc') {
            if (!empty(path[1])) {
                if (path[1] === 'emails') {
                    void import('/assets/controllers/uc/emails.js').then((module) => { new module.Emails(); });
                }
                else if (path[1] === 'password') {
                    void import('/assets/controllers/uc/password.js').then((module) => { new module.PasswordChange(); });
                }
                else if (path[1] === 'profile') {
                    void import('/assets/controllers/uc/profile.js').then((module) => { new module.EditProfile(); });
                }
                else if (path[1] === 'avatars') {
                    void import('/assets/controllers/uc/avatars.js').then((module) => { new module.EditAvatars(); });
                }
                else if (path[1] === 'sessions') {
                    void import('/assets/controllers/uc/sessions.js').then((module) => { new module.EditSessions(); });
                }
                else if (path[1] === 'fftracker') {
                    void import('/assets/controllers/uc/fftracker.js').then((module) => { new module.EditFFLinks(); });
                }
                else if (path[1] === 'removal') {
                    void import('/assets/controllers/uc/removal.js').then((module) => { new module.RemoveProfile(); });
                }
            }
        }
        else if (path[0] === 'talks') {
            if (path[1] === 'edit') {
                if (path[2] === 'sections') {
                    void import('/assets/controllers/talks/sections.js').then((module) => { new module.Sections(); });
                }
                else if (path[2] === 'posts') {
                    void import('/assets/controllers/talks/posts.js').then((module) => { new module.Posts(); });
                }
            }
            else if (path[1] === 'sections') {
                void import('/assets/controllers/talks/sections.js').then((module) => { new module.Sections(); });
            }
            else if (path[1] === 'threads') {
                void import('/assets/controllers/talks/threads.js').then((module) => { new module.Threads(); });
            }
        }
        else if (path[0] === 'games') {
            void import('/assets/controllers/games/games.js').then((module) => { new module.Games(); });
        }
    }
}
class BackToTop extends HTMLElement {
    content;
    BTTs;
    constructor() {
        super();
        this.content = document.querySelector('#content');
        this.BTTs = document.querySelectorAll('back-to-top');
        if (this.content) {
            window.addEventListener('scroll', this.toggleButtons.bind(this), false);
            this.addEventListener('click', () => {
                window.scrollTo({
                    'behavior': 'smooth',
                    'left': 0,
                    'top': 0,
                });
            });
        }
    }
    toggleButtons() {
        if (this.content && !empty(this.BTTs)) {
            if (window.scrollY <= window.innerHeight / 100) {
                this.BTTs.forEach((item) => {
                    item.classList.add('hidden');
                });
            }
            else {
                this.BTTs.forEach((item) => {
                    item.classList.remove('hidden');
                });
            }
        }
        if (!window.location.hash.toLowerCase()
            .startsWith('#gallery=')) {
            const headings = document.querySelectorAll('h1:not(#h1_title), h2, h3, h4, h5, h6');
            for (let i = 0; i <= headings.length - 1; i++) {
                const heading = headings[i];
                const bottom = heading.getBoundingClientRect().bottom;
                const top = heading.getBoundingClientRect().top;
                const height = heading.getBoundingClientRect().height;
                if (top >= -height * 2 && bottom <= height * 2 && heading.checkVisibility()) {
                    history.replaceState(document.title, document.title, `#${heading.id}`);
                    return;
                }
            }
        }
    }
}
class Gallery extends HTMLElement {
    _current = 0;
    images = [];
    isOpened = false;
    gallery_name = null;
    gallery_name_link = null;
    gallery_loaded_image = null;
    gallery_total = null;
    gallery_current = null;
    get current() {
        return this._current;
    }
    set current(value) {
        if (value < 0) {
            this._current = this.images.length - 1;
        }
        else if (value > this.images.length - 1) {
            this._current = 0;
        }
        else {
            this._current = value;
        }
        if (this.images.length > 1 || !this.parentElement.open) {
            this.open();
        }
    }
    constructor() {
        super();
        this.images = Array.from(document.querySelectorAll('.gallery_zoom'));
        this.gallery_name = document.querySelector('#gallery_name');
        this.gallery_name_link = document.querySelector('#gallery_name_link');
        this.gallery_loaded_image = document.querySelector('#gallery_loaded_image');
        this.gallery_total = document.querySelector('#gallery_total');
        this.gallery_current = document.querySelector('#gallery_current');
        if (this.images.length > 0) {
            this.images.forEach((item, index) => {
                item.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.current = index;
                    return false;
                });
            });
            this.addEventListener('keydown', this.keyNav.bind(this));
        }
    }
    open() {
        this.tabIndex = 99;
        const link = this.images[this.current];
        if (link instanceof HTMLAnchorElement) {
            const image = link.querySelector('img');
            if (image instanceof HTMLImageElement) {
                image.classList.remove('zoomed_in');
                const caption = link.parentElement?.querySelector('figcaption');
                const name = link.getAttribute('data-tooltip') ?? link.getAttribute('title') ?? image.getAttribute('alt') ?? link.href.replace(/^.*[\\/]/u, '');
                if (this.gallery_name) {
                    this.gallery_name.innerHTML = caption ? caption.innerHTML : name;
                }
                if (this.gallery_name_link) {
                    this.gallery_name_link.href = link.href;
                }
                if (this.gallery_loaded_image) {
                    this.gallery_loaded_image.src = link.href;
                }
                if (this.gallery_total) {
                    this.gallery_total.innerText = this.images.length.toString();
                }
                if (this.gallery_current) {
                    this.gallery_current.innerText = (this.current + 1).toString();
                }
                if (!this.parentElement.open) {
                    this.parentElement.showModal();
                }
                this.history();
                this.focus();
                this.isOpened = true;
            }
        }
    }
    close() {
        if (!this.isOpened) {
            return;
        }
        this.tabIndex = -1;
        this.parentElement.close();
        this.history();
        document.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')[0].focus();
        this.isOpened = false;
    }
    previous() {
        this.current -= 1;
    }
    next() {
        this.current += 1;
    }
    keyNav(event) {
        event.stopPropagation();
        if (['ArrowDown', 'ArrowRight', 'PageDown'].includes(event.code)) {
            this.next();
            return false;
        }
        else if (['ArrowUp', 'ArrowLeft', 'PageUp'].includes(event.code)) {
            this.previous();
            return false;
        }
        else if (event.code === 'End') {
            this.current = this.images.length - 1;
            return false;
        }
        else if (event.code === 'Home') {
            this.current = 0;
            return false;
        }
        else if (['Escape', 'Backspace'].includes(event.code)) {
            this.close();
            return false;
        }
        return true;
    }
    history() {
        const url = new URL(document.location.href, window.location.origin);
        const new_index = (this.current + 1).toString();
        const new_url = new URL(document.location.href, window.location.origin);
        let new_title;
        if (this.parentElement.open) {
            new_title = `${document.title.replace(/(?<pageTitle>.*)(?<imagePrefix>, Image )(?<imageNumber>\d+)/ui, '$<pageTitle>')}, Image ${new_index}`;
            new_url.hash = `gallery=${new_index}`;
        }
        else {
            new_title = document.title.replace(/(?<pageTitle>.*)(?<imagePrefix>, Image )(?<imageNumber>\d+)/ui, '$<pageTitle>');
            new_url.hash = '';
        }
        if (url !== new URL(new_url, window.location.origin)) {
            updateHistory(new_url.href, new_title);
        }
    }
}
class GalleryImage extends HTMLElement {
    image = null;
    zoom_listener;
    constructor() {
        super();
        this.image = document.querySelector('#gallery_loaded_image');
        this.zoom_listener = this.zoom.bind(this);
        if (this.image) {
            this.image.addEventListener('load', this.checkZoom.bind(this));
        }
    }
    checkZoom() {
        if (this.image) {
            this.image.classList.remove('zoomed_in');
            if (this.image.naturalHeight <= this.image.height) {
                this.image.removeEventListener('click', this.zoom_listener);
                this.image.classList.add('no_zoom');
            }
            else {
                this.image.classList.remove('no_zoom');
                this.image.addEventListener('click', this.zoom_listener);
            }
        }
    }
    zoom() {
        if (this.image) {
            if (this.image.classList.contains('zoomed_in')) {
                this.image.classList.remove('zoomed_in');
            }
            else {
                this.image.classList.add('zoomed_in');
            }
        }
    }
}
class GalleryPrev extends HTMLElement {
    overlay;
    constructor() {
        super();
        this.overlay = document.querySelector('gallery-overlay');
        if (this.overlay !== null && this.overlay.images.length > 1) {
            this.addEventListener('click', () => {
                if (this.overlay !== null) {
                    this.overlay.previous();
                }
            });
        }
        else {
            this.classList.add('disabled');
        }
    }
}
class GalleryNext extends HTMLElement {
    overlay;
    constructor() {
        super();
        this.overlay = document.querySelector('gallery-overlay');
        if (this.overlay !== null && this.overlay.images.length > 1) {
            this.addEventListener('click', () => {
                if (this.overlay !== null) {
                    this.overlay.next();
                }
            });
        }
        else {
            this.classList.add('disabled');
        }
    }
}
class GalleryClose extends HTMLElement {
    constructor() {
        super();
        this.addEventListener('click', () => {
            const overlay = document.querySelector('gallery-overlay');
            if (overlay !== null) {
                overlay.close();
            }
        });
    }
}
class CarouselList extends HTMLElement {
    list;
    next;
    previous;
    maxScroll = 0;
    constructor() {
        super();
        this.list = this.querySelector('.image_carousel_list');
        this.next = this.querySelector('image-carousel-next');
        this.previous = this.querySelector('image-carousel-prev');
        if (this.list && this.next && this.previous) {
            this.maxScroll = this.list.scrollWidth - this.list.offsetWidth;
            this.list.addEventListener('scroll', () => {
                this.disableScroll();
            });
            [this.next, this.previous].forEach((item) => {
                item.addEventListener('click', (event) => {
                    this.toScroll(event);
                });
            });
            this.disableScroll();
        }
    }
    toScroll(event) {
        if (this.list) {
            const scrollButton = event.target;
            const img = this.list.querySelector('img');
            if (img) {
                if (scrollButton.nodeName === 'IMAGE-CAROUSEL-PREV') {
                    this.list.scrollLeft -= img.width;
                }
                else {
                    this.list.scrollLeft += img.width;
                }
                this.disableScroll();
            }
        }
    }
    disableScroll() {
        if (this.list) {
            if (this.previous) {
                if (this.list.scrollLeft === 0) {
                    this.previous.classList.add('disabled');
                }
                else {
                    this.previous.classList.remove('disabled');
                }
            }
            if (this.next) {
                if (this.list.scrollLeft >= this.maxScroll) {
                    this.next.classList.add('disabled');
                }
                else {
                    this.next.classList.remove('disabled');
                }
            }
        }
    }
}
class ImageUpload extends HTMLElement {
    preview = null;
    file = null;
    label = null;
    constructor() {
        super();
        this.file = this.querySelector('input[type=file]');
        this.label = this.querySelector('label');
        this.preview = this.querySelector('img');
        if (this.file) {
            if (empty(this.file.accept)) {
                this.file.accept = 'image/avif,image/bmp,image/gif,image/jpeg,image/png,image/webp,image/svg+xml';
            }
            this.file.placeholder = 'Image file';
            this.file.addEventListener('change', () => {
                this.update();
            });
        }
        if (this.preview && this.label) {
            this.preview.alt = `Preview of ${this.label.innerText.charAt(0).toLowerCase()}${this.label.innerText.slice(1)}`;
            this.preview.setAttribute('data-tooltip', this.preview.alt);
            const current = this.preview.getAttribute('data-current') ?? '';
            if (!(/^\s*$/ui).test(current)) {
                this.preview.src = current;
                this.preview.classList.remove('hidden');
            }
        }
    }
    update() {
        if (this.preview && this.file) {
            if (this.file.files?.[0]) {
                this.preview.src = URL.createObjectURL(this.file.files[0]);
                this.preview.classList.remove('hidden');
            }
            else {
                this.preview.classList.add('hidden');
            }
        }
    }
}
class Likedis extends HTMLElement {
    post_id = 0;
    like_value = 0;
    likesCount;
    dislikesCount;
    likeButton;
    dislikeButton;
    constructor() {
        super();
        this.like_value = Number(this.getAttribute('data-liked') ?? 0);
        this.post_id = Number(this.getAttribute('data-post_id') ?? 0);
        this.likesCount = this.querySelector('.likes_count');
        this.dislikesCount = this.querySelector('.dislikes_count');
        this.likeButton = this.querySelector('.like_button');
        this.dislikeButton = this.querySelector('.dislike_button');
        if (this.likeButton) {
            this.likeButton.addEventListener('click', this.like.bind(this));
        }
        if (this.dislikeButton) {
            this.dislikeButton.addEventListener('click', this.like.bind(this));
        }
    }
    like(event) {
        const button = event.target;
        let action;
        if (button.classList.contains('like_button')) {
            action = 'like';
        }
        else {
            action = 'dislike';
        }
        if (this.post_id === 0) {
            addSnackbar('No post ID', 'failure', SNACKBAR_FAIL_LIFE);
            return;
        }
        buttonToggle(button);
        ajax(`${location.protocol}//${location.host}/api/talks/posts/${this.post_id}/${action}`, null, 'json', 'PATCH', AJAX_TIMEOUT, true)
            .then((response) => {
            const data = response;
            if (data.data === 0 || data.data === 1 || data.data === -1) {
                this.updateCounts(data.data);
            }
            else {
                addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
            }
            buttonToggle(button);
        });
    }
    updateCounts(newValue) {
        if (this.likesCount && this.dislikesCount && this.likeButton && this.dislikeButton) {
            this.likesCount.classList.remove('success');
            this.dislikesCount.classList.remove('failure');
            if (newValue === 0) {
                if (this.like_value === 1 || this.like_value === -1) {
                    this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) + this.like_value);
                }
                this.likeButton.setAttribute('data-tooltip', 'Like');
                this.dislikeButton.setAttribute('data-tooltip', 'Dislike');
            }
            else if (newValue === 1) {
                if (this.like_value === -1) {
                    this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) - 1);
                }
                this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) + 1);
                this.likesCount.classList.add('success');
                this.likeButton.setAttribute('data-tooltip', 'Remove like');
                this.dislikeButton.setAttribute('data-tooltip', 'Dislike');
            }
            else if (newValue === -1) {
                if (this.like_value === 1) {
                    this.likesCount.innerHTML = String(Number(this.likesCount.innerHTML) - 1);
                }
                this.dislikesCount.innerHTML = String(Number(this.dislikesCount.innerHTML) + 1);
                this.dislikesCount.classList.add('failure');
                this.likeButton.setAttribute('data-tooltip', 'Like');
                this.dislikeButton.setAttribute('data-tooltip', 'Remove dislike');
            }
            if (Number(this.likesCount.innerHTML) < 0) {
                this.likesCount.innerHTML = '0';
            }
            if (Number(this.dislikesCount.innerHTML) < 0) {
                this.dislikesCount.innerHTML = '0';
            }
            this.setAttribute('data-liked', String(newValue));
            this.like_value = newValue;
        }
    }
}
class LoginForm extends HTMLElement {
    userRegex = '^[\\p{L}\\d.!$%&\'*+\\\\/=?^_`\\{\\|\\}~\\- ]{1,64}$';
    emailRegex = '[\\p{L}\\d.!#$%&\'*+\\/=?^_`\\{\\|\\}~\\-]+@[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?(?:\\.[a-zA-Z\\d](?:[a-zA-Z\\d\\-]{0,61}[a-zA-Z\\d])?)*';
    login_form = null;
    existUser = null;
    newUser = null;
    forget = null;
    login = null;
    password = null;
    button = null;
    rememberme = null;
    username = null;
    constructor() {
        super();
        this.login_form = document.querySelector('#signinup');
        if (this.login_form) {
            this.existUser = document.querySelector('#radio_existuser');
            this.newUser = document.querySelector('#radio_newuser');
            this.forget = document.querySelector('#radio_forget');
            this.login = document.querySelector('#signinup_email');
            this.password = document.querySelector('#signinup_password');
            this.button = document.querySelector('#signinup_submit');
            this.rememberme = document.querySelector('#rememberme');
            this.username = document.querySelector('#signinup_username');
            this.login_form.querySelectorAll('#radio_signinup input[type=radio]').forEach((item) => {
                item.addEventListener('change', this.loginRadioCheck.bind(this));
            });
            this.loginRadioCheck();
            submitIntercept(this.login_form, this.singInUpSubmit.bind(this));
        }
    }
    singInUpSubmit() {
        if (this.login_form) {
            const formData = new FormData(this.login_form);
            if (empty(formData.get('signinup[type]'))) {
                formData.set('signinup[type]', 'logout');
            }
            formData.set('signinup[timezone]', TIMEZONE);
            const button = this.login_form.querySelector('#signinup_submit');
            buttonToggle(button);
            void ajax(`${location.protocol}//${location.host}/api/uc/${String(formData.get('signinup[type]'))}`, formData, 'json', 'POST', AJAX_TIMEOUT, true).then((response) => {
                const data = response;
                if (data.data === true) {
                    if (formData.get('signinup[type]') === 'remind') {
                        addSnackbar('If respective account is registered an email has been sent with password reset link.', 'success');
                    }
                    else {
                        pageRefresh();
                    }
                }
                else {
                    addSnackbar(data.reason, 'failure', SNACKBAR_FAIL_LIFE);
                }
                buttonToggle(button);
            });
        }
    }
    loginRadioCheck() {
        if (this.login && this.password && this.button && this.rememberme && this.username) {
            let loginLabel;
            if (this.login.labels) {
                loginLabel = this.login.labels[0];
            }
            this.login.placeholder = 'Email or name';
            if (loginLabel) {
                loginLabel.innerHTML = 'Email or name';
            }
            this.login.setAttribute('pattern', `^(${this.userRegex})|(${this.emailRegex})$`);
            this.rememberme.checked = false;
            this.password.setAttribute('minlength', '8');
            this.login.setAttribute('type', 'text');
            this.login.setAttribute('autocomplete', 'username');
            if ((this.existUser?.checked) === true) {
                this.password.required = true;
                this.password.setAttribute('autocomplete', 'current-password');
                this.button.value = 'Sign in';
                this.password.parentElement.classList.remove('hidden');
                this.rememberme.parentElement.classList.remove('hidden');
                this.username.parentElement.classList.add('hidden');
                this.username.required = false;
            }
            if ((this.newUser?.checked) === true) {
                this.password.required = true;
                this.password.setAttribute('autocomplete', 'new-password');
                this.login.setAttribute('type', 'email');
                this.login.setAttribute('autocomplete', 'email');
                this.login.setAttribute('pattern', `^${this.emailRegex}$`);
                this.button.value = 'Join';
                this.password.parentElement.classList.remove('hidden');
                this.rememberme.parentElement.classList.remove('hidden');
                this.login.placeholder = 'Email';
                if (loginLabel) {
                    loginLabel.innerHTML = 'Email';
                }
                this.username.parentElement.classList.remove('hidden');
                this.username.required = true;
            }
            if ((this.forget?.checked) === true) {
                this.password.required = false;
                this.password.removeAttribute('autocomplete');
                this.password.removeAttribute('minlength');
                this.button.value = 'Remind';
                this.password.parentElement.classList.add('hidden');
                this.rememberme.parentElement.classList.add('hidden');
                this.username.parentElement.classList.add('hidden');
                this.username.required = false;
            }
            ariaNation(this.password);
        }
    }
}
class NavShow extends HTMLElement {
    navDiv = null;
    constructor() {
        super();
        this.navDiv = document.querySelector('#navigation');
        this.addEventListener('click', () => {
            this.navDiv?.classList.add('flex');
        });
    }
}
class NavHide extends HTMLElement {
    navDiv = null;
    constructor() {
        super();
        this.navDiv = document.querySelector('#navigation');
        this.addEventListener('click', () => {
            this.navDiv?.classList.remove('flex');
        });
    }
}
class SideShow extends HTMLElement {
    sideHide = null;
    sidebar = null;
    button = null;
    constructor() {
        super();
        this.button = this.querySelector('input');
        this.sideHide = document.querySelector('side-hide');
        if (this.id === 'prod_link') {
            if (this.button) {
                this.button.addEventListener('click', () => {
                    window.open(encodeURI(document.location.href.replace('local.simbiat.dev', 'www.simbiat.dev')), '_blank');
                });
            }
        }
        else if (this.button && this.sideHide && this.hasAttribute('data-sidebar')) {
            this.sidebar = document.querySelector(`#${String(this.getAttribute('data-sidebar'))}`);
            this.button.addEventListener('click', () => {
                this.sidebar?.showModal();
            });
        }
    }
}
class SideHide extends HTMLElement {
    sidebar = null;
    constructor() {
        super();
        if (this.parentElement) {
            this.sidebar = this.parentElement.parentElement;
            this.addEventListener('click', () => {
                this.sidebar?.close();
            });
        }
    }
}
class OGImage extends HTMLElement {
    og_image = null;
    hideBanner = null;
    constructor() {
        super();
        this.og_image = document.querySelector('#og_image');
        this.hideBanner = document.querySelector('hide-banner');
        if (this.hideBanner) {
            this.hideBanner.addEventListener('click', () => {
                this.toggleBanner();
            });
        }
    }
    toggleBanner() {
        if (this.og_image && this.hideBanner) {
            if (this.og_image.classList.contains('hidden')) {
                this.og_image.classList.remove('hidden');
                this.hideBanner.textContent = 'Hide banner';
            }
            else {
                this.og_image.classList.add('hidden');
                this.hideBanner.textContent = 'Show banner';
            }
        }
    }
}
class PasswordShow extends HTMLElement {
    passwordInput = null;
    constructor() {
        super();
        if (this.parentElement) {
            this.passwordInput = this.parentElement.querySelector('input');
            if (this.passwordInput) {
                this.addEventListener('click', this.toggle.bind(this));
            }
        }
    }
    toggle(event) {
        event.preventDefault();
        if (this.passwordInput) {
            if (this.passwordInput.type === 'password') {
                this.passwordInput.type = 'text';
                this.title = 'Hide password';
            }
            else {
                this.passwordInput.type = 'password';
                this.title = 'Show password';
            }
        }
    }
}
class PasswordRequirements extends HTMLElement {
    passwordInput = null;
    constructor() {
        super();
        this.classList.add('hidden');
        this.innerHTML = 'Only password requirement: at least 8 symbols';
        if (this.parentElement) {
            this.passwordInput = this.parentElement.querySelector('input');
            if (this.passwordInput) {
                this.passwordInput.addEventListener('focus', this.show.bind(this));
                this.passwordInput.addEventListener('focusout', this.hide.bind(this));
                ['focus', 'change', 'input',].forEach((eventType) => {
                    if (this.passwordInput) {
                        this.passwordInput.addEventListener(eventType, this.validate.bind(this));
                    }
                });
            }
        }
    }
    validate() {
        if (this.passwordInput) {
            if (this.passwordInput.validity.valid) {
                this.classList.remove('error');
                this.classList.add('success');
            }
            else {
                this.classList.add('error');
                this.classList.remove('success');
            }
        }
    }
    show() {
        if (this.passwordInput) {
            const autocomplete = this.passwordInput.getAttribute('autocomplete') ?? null;
            if (autocomplete === 'new-password') {
                this.classList.remove('hidden');
            }
            else {
                this.classList.add('hidden');
            }
        }
    }
    hide() {
        this.classList.add('hidden');
    }
}
class PasswordStrength extends HTMLElement {
    passwordInput = null;
    strengthSpan = null;
    constructor() {
        super();
        this.classList.add('hidden');
        this.innerHTML = 'New password strength: <span class="password_strength">weak</span>';
        if (this.parentElement) {
            this.passwordInput = this.parentElement.querySelector('input');
            this.strengthSpan = this.querySelector('span');
            if (this.passwordInput && this.strengthSpan) {
                this.passwordInput.addEventListener('focus', this.show.bind(this));
                this.passwordInput.addEventListener('focusout', this.hide.bind(this));
                ['focus', 'change', 'input',].forEach((eventType) => {
                    if (this.passwordInput) {
                        this.passwordInput.addEventListener(eventType, this.calculate.bind(this));
                    }
                });
            }
        }
    }
    calculate() {
        if (this.passwordInput && this.strengthSpan) {
            const password = this.passwordInput.value;
            let points = 0;
            if ((/.{8,}/u).test(password)) {
                points += 1;
            }
            if ((/.{16,}/u).test(password)) {
                points += 1;
            }
            if ((/.{32,}/u).test(password)) {
                points += 1;
            }
            if ((/.{64,}/u).test(password)) {
                points += 1;
            }
            if ((/\p{Ll}/u).test(password)) {
                points += 1;
            }
            if ((/\p{Lu}/u).test(password)) {
                points += 1;
            }
            if ((/\p{Lo}/u).test(password)) {
                points += 1;
            }
            if ((/\p{N}/u).test(password)) {
                points += 1;
            }
            if ((/[\p{P}\p{S}]/u).test(password)) {
                points += 1;
            }
            if ((/(?<character>.)\1{2,}/u).test(password)) {
                points -= 1;
            }
            let strength;
            if (points <= 2) {
                strength = 'weak';
            }
            else if (points > 2 && points < 5) {
                strength = 'medium';
            }
            else if (points === 5) {
                strength = 'strong';
            }
            else {
                strength = 'very strong';
            }
            this.strengthSpan.innerHTML = strength;
            this.strengthSpan.classList.remove('password_weak', 'password_medium', 'password_strong', 'password_very_strong');
            if (strength === 'very strong') {
                this.strengthSpan.classList.add('password_very_strong');
            }
            else {
                this.strengthSpan.classList.add(`password_${strength}`);
            }
            return strength;
        }
        return '';
    }
    show() {
        if (this.passwordInput) {
            const autocomplete = this.passwordInput.getAttribute('autocomplete') ?? null;
            if (autocomplete === 'new-password') {
                this.classList.remove('hidden');
            }
            else {
                this.classList.add('hidden');
            }
        }
    }
    hide() {
        this.classList.add('hidden');
    }
}
class PostForm extends HTMLElement {
    textarea = null;
    reply_to_input = null;
    label = null;
    constructor() {
        super();
        this.textarea = this.querySelector('textarea');
        this.reply_to_input = this.querySelector('#replying_to');
        this.label = this.querySelector('.label_for_tinymce');
        if (this.textarea && !empty(this.textarea.id)) {
            loadTinyMCE(this.textarea.id, false, true);
        }
    }
    replyTo(post_id) {
        if (this.reply_to_input && !((/^\s*$/ui).exec(post_id))) {
            this.reply_to_input.value = post_id;
            if (this.label) {
                this.label.innerHTML = `Replying to post #${post_id}`;
            }
        }
        window.location.assign(encodeURI('#post_form'));
    }
}
class SelectCustom extends HTMLElement {
    icon = null;
    select = null;
    label = null;
    description = null;
    constructor() {
        super();
        this.select = this.querySelector('select');
        this.label = this.querySelector('label');
        this.icon = this.querySelector('.select_icon');
        this.description = this.querySelector('.select_description');
        if (this.icon && this.label) {
            this.icon.alt = `Icon for ${this.label.innerText.charAt(0).toLowerCase()}${this.label.innerText.slice(1)}`;
            this.icon.setAttribute('data-tooltip', this.icon.alt);
        }
        if (this.select) {
            this.select.addEventListener('change', () => {
                this.update();
            });
        }
        this.update();
    }
    update() {
        if (this.select) {
            const option = this.select[this.select.selectedIndex];
            const description = option.getAttribute('data-description') ?? '';
            const icon = option.getAttribute('data-icon') ?? '';
            if (this.description) {
                if ((/^\s*$/ui).test(description)) {
                    this.description.classList.add('hidden');
                }
                else {
                    this.description.innerHTML = description;
                    this.description.classList.remove('hidden');
                }
            }
            if (this.icon) {
                if ((/^\s*$/ui).test(icon)) {
                    this.icon.classList.add('hidden');
                }
                else {
                    this.icon.src = icon;
                    this.icon.classList.remove('hidden');
                }
            }
        }
    }
}
class SnackbarClose extends HTMLElement {
    snackbar = null;
    snack;
    constructor() {
        super();
        this.snack = this.parentElement;
        const snackbar = document.querySelector('snack-bar');
        if (snackbar !== null) {
            this.snackbar = snackbar;
        }
        this.addEventListener('click', this.close.bind(this));
        const closeIn = parseInt(this.getAttribute('data-close-in') ?? '0', 10);
        if (closeIn > 0) {
            window.setTimeout(() => {
                this.close();
            }, closeIn);
        }
    }
    close() {
        this.snack.classList.remove('fade_in');
        this.snack.classList.add('fade_out');
        this.snack.addEventListener('animationend', () => {
            this.snack.close();
            if ((this.snackbar?.contains(this.snack)) === true) {
                this.snackbar.removeChild(this.snack);
            }
        });
    }
}
class TabMenu extends HTMLElement {
    tabs;
    contents;
    wrapper = null;
    current_tab = null;
    constructor() {
        super();
        this.wrapper = this.querySelector('tab-contents');
        this.tabs = Array.from(this.querySelectorAll('a.tab_name'));
        this.contents = Array.from(this.querySelectorAll('tab-content'));
        for (const tab of this.tabs) {
            tab.addEventListener('click', (event) => {
                this.tabSwitch(event);
            });
        }
        this.updateCurrentTab();
        if (this.wrapper?.querySelector('.active')) {
            this.wrapper.classList.remove('hidden');
        }
    }
    tabSwitch(event) {
        event.preventDefault();
        event.stopImmediatePropagation();
        const target = event.target;
        let tab_index = 0;
        for (const [index, tab] of this.tabs.entries()) {
            if (tab === target) {
                tab_index = index;
            }
            tab.classList.remove('active');
            if (this.contents[index]) {
                this.contents[index].classList.remove('active');
            }
        }
        this.wrapper?.classList.add('hidden');
        if (this.current_tab !== tab_index) {
            target.classList.add('active');
            if (target.href !== '' && target.href !== window.location.href) {
                window.location.assign(encodeURI(target.href));
                return;
            }
            if (this.contents[tab_index]) {
                this.contents[tab_index].classList.add('active');
            }
        }
        if (this.wrapper) {
            this.updateCurrentTab();
            if (this.wrapper.querySelector('.active')) {
                this.wrapper.classList.remove('hidden');
            }
        }
    }
    updateCurrentTab() {
        this.current_tab = null;
        for (const [index, tab] of this.tabs.entries()) {
            if (tab.classList.contains('active')) {
                this.current_tab = index;
            }
        }
    }
}
class Timer extends HTMLElement {
    interval = null;
    constructor() {
        super();
        this.interval = window.setInterval(() => {
            const dataIncrease = Boolean(this.getAttribute('data-increase') ?? false);
            if (parseInt(this.innerHTML, 10) > 0 || Boolean(this.getAttribute('data-negative') ?? false)) {
                if (dataIncrease) {
                    this.innerHTML = String(parseInt(this.innerHTML, 10) + 1);
                }
                else {
                    this.innerHTML = String(parseInt(this.innerHTML, 10) - 1);
                }
            }
            else {
                clearInterval(Number(this.interval));
                if (this.id === 'refresh_timer') {
                    pageRefresh();
                }
            }
        }, 1000);
    }
}
class Tooltip extends HTMLElement {
    x = 0;
    y = 0;
    width = 0;
    height = 0;
    constructor() {
        super();
        document.querySelectorAll('[alt]:not([alt=""]):not([data-tooltip]), [title]:not([title=""]):not([data-tooltip]):not(link)').forEach((item) => {
            if (item.parentElement?.hasAttribute('data-tooltip') === false) {
                item.setAttribute('data-tooltip', item.getAttribute('alt') ?? item.getAttribute('title') ?? '');
            }
        });
        document.querySelectorAll('[data-tooltip]:not([tabindex])').forEach((item) => {
            item.setAttribute('tabindex', '0');
        });
        document.addEventListener('pointermove', this.onPointerMove.bind(this));
        document.querySelectorAll('[data-tooltip]:not([data-tooltip=""])').forEach((item) => {
            item.addEventListener('focus', this.onFocus.bind(this));
        });
        document.querySelectorAll(':not([data-tooltip])').forEach((item) => {
            item.addEventListener('focus', () => { this.removeAttribute('data-tooltip'); });
        });
    }
    onPointerMove(event) {
        this.update(event.target);
        this.width = Math.max(event.width, 10);
        this.height = Math.max(event.height, 10);
        this.x = event.clientX + this.width;
        this.y = event.clientY - this.height;
        this.tooltipCursor();
    }
    onFocus(event) {
        this.update(event.target);
        const coordinates = event.target.getBoundingClientRect();
        this.x = coordinates.x + this.width;
        this.y = coordinates.y - (this.offsetHeight * 1.5);
        this.tooltipCursor();
    }
    tooltipCursor() {
        if (this.y + this.offsetHeight > window.innerHeight) {
            this.y = window.innerHeight - (this.offsetHeight * 2);
        }
        if (this.x + this.offsetWidth > window.innerWidth) {
            this.x = window.innerWidth - (this.offsetWidth * 1.5);
        }
        if (this.x - this.width < 0) {
            this.x = this.width;
        }
        if (this.y - this.height < 0) {
            this.y = this.height;
        }
        document.documentElement.style.setProperty('--cursor_x', `${this.x}px`);
        document.documentElement.style.setProperty('--cursor_y', `${this.y}px`);
    }
    update(element) {
        const parent = element.parentElement;
        const tooltip = element.getAttribute('data-tooltip') ?? parent?.getAttribute('data-tooltip') ?? '';
        if (!empty(tooltip) && element !== this && matchMedia('(pointer:fine)').matches) {
            this.setAttribute('data-tooltip', 'true');
            this.innerHTML = tooltip;
        }
        else {
            this.removeAttribute('data-tooltip');
            this.innerHTML = '';
        }
    }
}
class WebShare extends HTMLElement {
    shareData;
    constructor() {
        super();
        this.shareData = {
            'text': getMeta('og:description') ?? getMeta('description') ?? '',
            'title': document.title,
            'url': document.location.href,
        };
        this.addEventListener('click', this.share.bind(this));
    }
    share() {
        if (navigator.share) {
            navigator.share(this.shareData)
                .catch(() => {
                this.toClipboard();
            });
        }
        else {
            this.toClipboard();
        }
        this.blur();
    }
    toClipboard() {
        navigator.clipboard.writeText(window.location.href)
            .then(() => {
            addSnackbar(`Page link copied to clipboard`, 'success');
        }, () => {
            addSnackbar(`Failed to copy page link to clipboard`, 'failure');
        });
    }
}
//# sourceMappingURL=app.js.map
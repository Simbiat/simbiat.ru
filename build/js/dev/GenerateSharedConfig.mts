/* eslint-disable no-console */
/**
 * @file Generate settings, that are shared with PHP.
 */
import browserslist from 'browserslist';
// eslint-disable-next-line import/no-nodejs-modules
import fs from 'fs';
import semver from 'semver';

const browsers: string[] = browserslist();
const minimums: Record<string, string> = {};
const browser_map: Record<string, string> = {
  and_chr: 'Chrome Mobile',
  and_ff: 'Firefox Mobile',
  and_qq: 'QQ Browser',
  and_uc: 'UC Browser',
  android: 'Chrome',
  bb: 'BlackBerry Browser',
  chrome: 'Chrome',
  edge: 'Microsoft Edge',
  firefox: 'Firefox',
  ie: 'Internet Explorer',
  ie_mob: 'Internet Explorer',
  ios_saf: 'Safari',
  op_mini: 'Opera Mini',
  op_mob: 'Opera Mobile',
  opera: 'Opera',
  safari: 'Safari',
  samsung: 'Samsung Browser',
};

const tracking_query_parameters = [
  '__hsfp',
  '__hssc',
  '__hstc',
  '__s',
  '_hsenc',
  '_openstat',
  '_reqid',
  '_trkparms',
  'ad_bucket',
  'ad_size',
  'ad_slot',
  'ad_type',
  'adid',
  'adserverid',
  'adserveroptimizerid',
  'adtype',
  'adurl',
  'aff_id',
  'affiliate',
  'AffiliateGuid',
  'aid',
  'bid',
  'bdref',
  'bstk',
  'campaign_id',
  'campaignid',
  'cid',
  'clickid',
  'client_id',
  'clkurlenc',
  'data',
  'dclid',
  'documentref',
  'exitPop',
  'fb',
  'fb_source',
  'fb_ref',
  'fbclid',
  'first_visit',
  'flash',
  'ga_campaign',
  'ga_content',
  'ga_fc',
  'ga_hid',
  'ga_medium',
  'ga_place',
  'ga_sid',
  'ga_source',
  'ga_term',
  'ga_vid',
  'gclid',
  'hsCtaTracking',
  'ImpressionGuid',
  'matchid',
  'mc_eid',
  'mediadataid',
  'minbid',
  'mkt_tok',
  'ml_subscriber',
  'ml_subscriber_hash',
  'msclkid',
  'num_ads',
  'oly_anon_id',
  'oly_enc_id',
  'origin',
  'page_referrer',
  'payload',
  'pid',
  'piggiebackcookie',
  'pk_campaign',
  'providerid',
  'pubclick',
  'pubid',
  'rb_clickid',
  'rcm',
  'ref',
  'ref_',
  'referrer',
  'reftype',
  'rev',
  'revmod',
  'rid',
  'rurl',
  's_cid',
  'sid',
  'site',
  'siteid',
  'sourceid',
  'src',
  'tldid',
  'trackid',
  'tracking',
  'uid',
  'usegapi',
  'utm_campaign',
  'utm_cid',
  'utm_content',
  'utm_medium',
  'utm_name',
  'utm_reader',
  'utm_source',
  'utm_term',
  'vero_conv',
  'vero_id',
  'wickedid',
  'yclid',
  'zoneid',
];

type BrowserMap = Record<string, string[]>;
const collected: BrowserMap = {};

for (const entry of browsers) {
  const [raw_browser, raw_version] = entry.split(' ');
  if (typeof raw_browser === 'undefined' || typeof raw_version === 'undefined') {
    continue;
  }
  // eslint-disable-next-line security/detect-object-injection
  const browser = browser_map[raw_browser] ?? raw_browser;
  const version = semver.coerce(raw_version)
                        ?.toString();
  if (typeof version === 'undefined' || version === '') {
    continue;
  }
  // eslint-disable-next-line security/detect-object-injection
  collected[browser] ??= [];
  // eslint-disable-next-line security/detect-object-injection
  collected[browser].push(version);
}

for (const [browser, versions] of Object.entries(collected)) {
  let min = versions[0];
  if (typeof min !== 'undefined') {
    for (const v of versions) {
      if (semver.lt(v, min)) {
        min = v;
      }
    }
    // eslint-disable-next-line security/detect-object-injection
    minimums[browser] = min;
  }
}

// caniuse does not store history for Chrome Mobile, but due to Chrome's release cycle, it's safe to use the same value as from desktop Chrome
if (typeof minimums['Chrome'] !== 'undefined' && minimums['Chrome'] !== null) {
  minimums['Chrome Mobile'] = minimums['Chrome'];
}

fs.writeFileSync(
  './build/js/shared_with_php.json',
  JSON.stringify({
    tracking_query_parameters,
    teapot_browsers: minimums,
  }, null, 2),
  'utf-8',
);
console.log('✅ Config generated');

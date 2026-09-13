/* eslint-disable no-console */
/**
 * @file Generate settings that are shared with PHP in one way or another.
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

type BrowserMap = Record<string, string[]>;
const collected: BrowserMap = {};

for (const entry of browsers) {
    const [raw_browser, raw_version] = entry.split(' ');
    if (typeof raw_browser === 'undefined' || typeof raw_version === 'undefined') {
        continue;
    }
    // eslint-disable-next-line security/detect-object-injection
    const browser = browser_map[raw_browser] ?? raw_browser.replace(/[\n"\\]/gv, '');
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

const yaml_lines: string[] = ['parameters:', '    app.teapot_browsers:'];
for (const [browser, version] of Object.entries(minimums)) {
    yaml_lines.push(`        "${browser}": "${version}"`);
}

fs.writeFileSync(
    './config/packages/teapot_browsers.yaml',
    `${yaml_lines.join('\n')}\n`,
    'utf-8',
);
console.log('✅ Teapot browsers list generated');

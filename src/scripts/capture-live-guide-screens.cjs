const puppeteer = require('/tmp/nev-screenshots/node_modules/puppeteer');
const fs = require('fs');
const path = require('path');

const BASE = 'https://nevistanbul.sonerdurmus.com';
const OUT = path.join(__dirname, '../storage/app/docs/screens');
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
fs.mkdirSync(OUT, { recursive: true });

async function hideOverlays(page) {
  await page.evaluate(() => {
    try { localStorage.setItem('nev_istanbul_production_info_v1', '1'); } catch (e) {}
    ['production-info-modal', 'login-modal', 'dealer-success-modal'].forEach((id) => {
      const el = document.getElementById(id);
      if (el) el.classList.add('hidden');
    });
  });
}

async function shot(page, name, url) {
  await page.goto(url, { waitUntil: 'networkidle2', timeout: 90000 });
  await hideOverlays(page);
  await sleep(900);
  await page.screenshot({ path: path.join(OUT, `${name}.png`) });
  console.log('saved', name, '->', page.url());
}

async function typeLogin(page, emailSel, passSel, email, password) {
  await page.waitForSelector(emailSel, { timeout: 20000 });
  await hideOverlays(page);
  const emailEl = await page.$(emailSel);
  const passEl = await page.$(passSel);
  await emailEl.click({ clickCount: 3 });
  await page.keyboard.type(email, { delay: 25 });
  await passEl.click({ clickCount: 3 });
  await page.keyboard.type(password, { delay: 25 });
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 45000 }).catch(() => null),
    page.keyboard.press('Enter'),
  ]);
  await sleep(1200);
  console.log('login ->', page.url());
}

(async () => {
  const browser = await puppeteer.launch({
    headless: true,
    args: ['--no-sandbox', '--window-size=1440,900', '--ignore-certificate-errors'],
    defaultViewport: { width: 1440, height: 900 },
  });
  const page = await browser.newPage();
  await page.setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36');

  await page.goto(`${BASE}/locale/tr`, { waitUntil: 'networkidle2', timeout: 90000 });
  await hideOverlays(page);

  for (const [name, url] of [
    ['01-ana-sayfa', `${BASE}/`],
    ['02-giris', `${BASE}/giris`],
    ['03-bayi-ol', `${BASE}/bayi-ol`],
    ['04-urun', `${BASE}/urun/6`],
    ['05-iletisim', `${BASE}/iletisim`],
  ]) {
    await shot(page, name, url);
  }

  // Dealer login attempt
  await page.goto(`${BASE}/giris`, { waitUntil: 'networkidle2', timeout: 90000 });
  await hideOverlays(page);
  try {
    await typeLogin(page, '#page-login-email', '#page-login-password', 'dealer@demo.com', 'password');
  } catch (e) {
    console.log('dealer login error', e.message);
  }
  for (const [name, url] of [
    ['06-hesabim', `${BASE}/hesabim`],
    ['07-sepet', `${BASE}/sepet`],
    ['08-odeme', `${BASE}/odeme`],
  ]) {
    await shot(page, name, url);
  }

  // Admin login
  const client = await page.createCDPSession();
  await client.send('Network.clearBrowserCookies');
  await page.goto(`${BASE}/locale/tr`, { waitUntil: 'networkidle2', timeout: 90000 });
  await page.goto(`${BASE}/admin/login`, { waitUntil: 'networkidle2', timeout: 90000 });
  try {
    await typeLogin(page, 'input[type="email"]', 'input[type="password"]', 'admin@admin.com', 'password');
  } catch (e) {
    console.log('admin login error', e.message);
  }
  for (const [name, url] of [
    ['09-admin-panel', `${BASE}/admin`],
    ['10-admin-siparisler', `${BASE}/admin/orders`],
    ['11-admin-bayi-talepleri', `${BASE}/admin/dealer-requests`],
    ['12-admin-urunler', `${BASE}/admin/products`],
    ['13-admin-banka', `${BASE}/admin/bank-accounts`],
  ]) {
    await shot(page, name, url);
  }

  await browser.close();
  console.log('DONE');
})().catch((e) => {
  console.error(e);
  process.exit(1);
});

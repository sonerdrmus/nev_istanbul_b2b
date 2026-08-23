const puppeteer = require('/tmp/nev-screenshots/node_modules/puppeteer');
const path = require('path');
const BASE = 'http://localhost:8010';
const OUT = path.join(__dirname, '../storage/app/docs/screens');
const fs = require('fs');
fs.mkdirSync(OUT, { recursive: true });
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function hideStoreOverlays(page) {
  await page.evaluate(() => {
    try { localStorage.setItem('nev_istanbul_production_info_v1', '1'); } catch (e) {}
    ['production-info-modal', 'login-modal', 'dealer-success-modal'].forEach((id) => {
      const el = document.getElementById(id);
      if (el) el.classList.add('hidden');
    });
  });
}

async function shot(page, name, url) {
  await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });
  await hideStoreOverlays(page);
  await sleep(700);
  await page.screenshot({ path: path.join(OUT, `${name}.png`) });
  console.log('saved', name, '->', page.url());
}

(async () => {
  const browser = await puppeteer.launch({
    headless: true,
    args: ['--no-sandbox', '--window-size=1440,900'],
    defaultViewport: { width: 1440, height: 900 },
  });
  const page = await browser.newPage();

  await page.goto(`${BASE}/locale/tr`, { waitUntil: 'networkidle2' });
  await hideStoreOverlays(page);
  for (const [name, url] of [
    ['01-ana-sayfa', `${BASE}/`],
    ['02-giris', `${BASE}/giris`],
    ['03-bayi-ol', `${BASE}/bayi-ol`],
    ['04-urun', `${BASE}/urun/6`],
    ['05-iletisim', `${BASE}/iletisim`],
  ]) {
    await shot(page, name, url);
  }

  // dealer via local docs helper
  await page.goto(`${BASE}/__docs-login/dealer`, { waitUntil: 'networkidle2', timeout: 60000 });
  console.log('dealer', page.url());
  for (const [name, url] of [
    ['06-hesabim', `${BASE}/hesabim`],
    ['07-sepet', `${BASE}/sepet`],
    ['08-odeme', `${BASE}/odeme`],
  ]) {
    await shot(page, name, url);
  }

  // admin via local docs helper
  await page.goto(`${BASE}/__docs-login/admin`, { waitUntil: 'networkidle2', timeout: 60000 });
  console.log('admin', page.url());
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

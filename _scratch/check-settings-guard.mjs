import { chromium } from '@playwright/test';

const browser = await chromium.launch();
const context = await browser.newContext({ baseURL: 'http://localhost:8000' }); // no storageState = unauthenticated
const page = await context.newPage();

const response = await page.goto('/settings');
console.log('final url:', page.url());
console.log('status:', response.status());
const body = await response.text();
console.log('bodyLen:', body.length);
console.log('snippet:', JSON.stringify(body.slice(0, 200)));

await browser.close();

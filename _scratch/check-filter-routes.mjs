import { request } from '@playwright/test';
import fs from 'node:fs';

const BASE = 'http://localhost:8000';
const storageState = JSON.parse(fs.readFileSync('tests/E2E/.auth/admin.json', 'utf8'));
const ctx = await request.newContext({ baseURL: BASE, storageState });
const XHR = { 'X-Requested-With': 'XMLHttpRequest' };

const routes = [
  ['filter_custom_values', 'anything'],
  ['filter_custom_values_field', 'anything'],
  ['filter_invoices_recuring', 'anything'],
  ['filter_online_logs', 'anything'],
  ['filter_archives', 'anything'],
  ['filter_invoices', undefined],
  ['filter_invoices', "' OR '1'='1"],
];

for (const [route, query] of routes) {
  const form = query === undefined ? {} : { filter_query: query };
  const res = await ctx.post(`/filter/ajax/${route}`, { headers: XHR, form });
  const body = await res.text();
  console.log(`${route} (q=${JSON.stringify(query)}): status=${res.status()} bodyLen=${body.length} snippet=${JSON.stringify(body.slice(0, 150))}`);
}

await ctx.dispose();

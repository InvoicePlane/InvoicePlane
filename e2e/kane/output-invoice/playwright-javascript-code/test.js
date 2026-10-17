import testmu, { resolveVar, setVar } from '@testmuai/playwright-bindings';
import { expect } from '@playwright/test';

testmu.configure({
  "build": "fa334d37-677b-4f82-baa8-9091b0c15748",
  "name": "Create and Save Invoice for New Client",
  "tcId": "TC-215",
  "network": (process.env.NETWORK ?? "false").toLowerCase() === "true",
  "autoHealVersion": "AH2",
  "defaultActionTimeoutMs": 10000,
  "defaultNavigationTimeoutMs": 60000,
  "kaneRunV4": true
});

testmu.test("Create and Save Invoice for New Client", async (page) => {
  let element_0, element_1, element_7, element_8, element_2, element_10, element_11, element_3, element_14, element_15, element_4, element_5, element_6, element_19;
  await testmu.step("Navigate to http://localhost:4895/", { instructionId: "0d0c84af-194f-4378-aef0-28eb8dfe3f17" }, async () => {
    await page.goto("http://localhost:4895/");
  });
  
  await testmu.step("Waiting for email field to be ready", { instructionId: "ea624736-87f4-4e0b-8a90-8a61e95434b7" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"admin@example.com\" into \"Email\"", { instructionId: "dafd7dab-7095-43ca-a491-397cdbd8879b" }, async () => {
    element_0 = page.locator("internal:role=textbox[name=\"Email\"i]");
    await element_0.click();
    await element_0.fill("admin@example.com");
  });
  
  await testmu.step("Waiting for the next step to be ready", { instructionId: "48e33c08-5e27-44ee-9ac9-6580ea922abf" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"Invoice-Test-2026\" into \"Password\"", { instructionId: "b8f12e56-6124-4252-88cc-4c65fca6c800" }, async () => {
    element_1 = page.locator("internal:role=textbox[name=\"Password\"i]");
    await element_1.click();
    await element_1.fill("Invoice-Test-2026");
  });
  
  await testmu.step("Clicking Login submit button", { instructionId: "7ee16abc-a4d9-4284-942d-28dfeb3f5f06" }, async () => {
    element_7 = page.locator("internal:role=button[name=\"\uf09c Login\"i]");
    await element_7.click();
  });
  
  await testmu.step("Waiting for the dashboard to finish loading", { instructionId: "e5c7f721-fad2-41bd-8bd5-10972ef0d0b6" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Checking whether the dashboard is shown", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("dashboard_shown_check", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {30: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(30).querySelector(':scope > *') !== null);\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:text=\"Quick Actions Add Client Create Quote Create Invoice Enter Payment Quote\"i"], query: "whether the dashboard content panel is present", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The dashboard is shown." }));
  });
  
  await testmu.step("Checking whether Quick Actions is visible on the dashboard", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("quick_actions_visible", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {31: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(31).querySelector('b')?.textContent.trim() === 'Quick Actions');\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:text=\"Quick Actions Add Client\"i"], query: "whether the Quick Actions section heading is present on the dashboard", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The dashboard includes \"Quick Actions\"." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "e9c37a3d-6cf1-4b09-a440-dd2f944dda22" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The dashboard is shown.", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{dashboard_shown_check}}", "store_key": "dashboard_shown_check", "variable_refs": {"{{dashboard_shown_check}}": "true"}}, {"description": "The dashboard includes \"Quick Actions\".", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{quick_actions_visible}}", "store_key": "quick_actions_visible", "variable_refs": {"{{quick_actions_visible}}": "true"}}], "sub_checks": [{"description": "The dashboard is shown.", "store_key": "dashboard_shown_check", "expected_value": "true", "extracted_value": "{{dashboard_shown_check}}", "operator": "equals", "transforms": []}, {"description": "The dashboard includes \"Quick Actions\".", "store_key": "quick_actions_visible", "expected_value": "true", "extracted_value": "{{quick_actions_visible}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the dashboard is shown with \"Quick Actions\""});
  });
  
  await testmu.step("Clicking Add Client quick action", { instructionId: "b5a46aea-793b-4394-8c8c-6796eb092b18" }, async () => {
    element_8 = page.locator("internal:role=link[name=\"\uf007 Add Client\"i]");
    await element_8.click();
  });
  
  await testmu.step("Waiting for plan to finish processing before typing Client Name", { instructionId: "1a8f1065-728f-4173-8bbb-1606689e9e34" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"Globex\" into \"Client Name\"", { instructionId: "7e78a4fb-074e-4897-97a4-69e67279b7c0" }, async () => {
    element_2 = page.locator("internal:role=textbox[name=\"Client Name\"i]");
    await element_2.click();
    await element_2.fill("Globex");
  });
  
  await testmu.step("Click \"\uf00c Save\"", { instructionId: "09f74b9e-5524-4be4-9a49-93e73f23d6f4" }, async () => {
    element_10 = page.locator("internal:role=button[name=\"\uf00c Save\"i]");
    await element_10.click();
  });
  
  await testmu.step("Reading the client page heading", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("globex_client_page_shown", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {32: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(32).textContent);\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["h1"], query: "the client name shown in the client page heading", expectedValue: "Globex", needsUnitConversion: false, operator: "equals", transforms: ["strip"], condition: "The client page for \"Globex\" is shown." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "4c406fda-8c1a-4e23-8b82-4743c1861418" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The client page for \"Globex\" is shown.", "passed": true, "operator": "equals", "transforms": ["strip"], "json_path": null, "expected": "Globex", "extracted_value": "{{globex_client_page_shown}}", "store_key": "globex_client_page_shown", "variable_refs": {"{{globex_client_page_shown}}": "Globex"}}], "sub_checks": [{"description": "The client page for \"Globex\" is shown.", "store_key": "globex_client_page_shown", "expected_value": "Globex", "extracted_value": "{{globex_client_page_shown}}", "operator": "equals", "transforms": ["strip"]}], "composite_operator": "and", "claim": "the client page for \"Globex\" is shown"});
  });
  
  await testmu.step("Navigate to http://localhost:4895/dashboard", { instructionId: "77b3e19c-506f-4d62-afa1-f30bedac2aa2" }, async () => {
    await page.goto("http://localhost:4895/dashboard");
  });
  
  await testmu.step("Clicking Create Invoice in Quick Actions panel", { instructionId: "1ca02b3d-1fdc-4d2b-b371-6df57ea1c5be" }, async () => {
    element_11 = page.locator("internal:role=link[name=\"\uf15c Create Invoice\"i]");
    await element_11.click();
  });
  
  await testmu.step("Pressing Enter key to activate Create Invoice link", { instructionId: "9d3a75e0-de89-4fb0-8873-467b440f9646" }, async () => {
    await page.keyboard.press("Enter");
  });
  
  await testmu.step("PRIMARY: Client selection dropdown in Create Invoice dialog; role=combobox; text=\"Client\" | HINTS: position=upper dialog below Client label, currently showing placeholder Client", { instructionId: "7acf99d3-f22f-4aef-bbe4-e8c6eb2fa065" }, async () => {
    const _c_12 = await testmu.getVisionCoordinates(page, "PRIMARY: Client selection dropdown in Create Invoice dialog; role=combobox; text=\"Client\" | HINTS: position=upper dialog below Client label, currently showing placeholder Client", "click", 956, 136);
    await page.mouse.click(_c_12.x, _c_12.y);
  });
  
  await testmu.step("Typing Glob in client search box", { instructionId: "1800ff00-6207-45fa-a352-4ce65f51afbf" }, async () => {
    element_3 = page.locator("internal:role=searchbox");
    await element_3.click();
    await element_3.fill("Glob");
  });
  
  await testmu.step("Selecting Globex in the client dropdown", { instructionId: "de90057b-ce6e-48bd-b575-df422feb7668" }, async () => {
    element_14 = page.locator("internal:role=option[name=\"Globex\"i]");
    await element_14.click();
  });
  
  await testmu.step("Clicking Submit invoice button", { instructionId: "8df3617a-e211-4133-88db-cccdc2df4440" }, async () => {
    element_15 = page.locator("internal:text=\"Submit\"i");
    await element_15.click();
  });
  
  await testmu.step("Waiting for the invoice edit page to finish loading", { instructionId: "c60e894c-b41f-4c1d-90f0-81fe94b8b7c1" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Checking whether the invoice edit page is shown", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("invoice_edit_page_shown", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {35: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(35).innerText.includes('Save'));\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:role=link[name=\"\uf00c Save\"i]"], query: "whether the invoice edit interface is shown", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The invoice edit page is shown." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "32c5d3b3-1165-43fb-8f9a-42a872be00aa" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The invoice edit page is shown.", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{invoice_edit_page_shown}}", "store_key": "invoice_edit_page_shown", "variable_refs": {"{{invoice_edit_page_shown}}": "true"}}], "sub_checks": [{"description": "The invoice edit page is shown.", "store_key": "invoice_edit_page_shown", "expected_value": "true", "extracted_value": "{{invoice_edit_page_shown}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the invoice edit page is shown, waiting for it to load if needed."});
  });
  
  await testmu.step("Waiting for the plan steps to complete", { instructionId: "269a78a7-b586-4416-b356-d7089c3afa9a" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"Website design\" into \"textbox\"", { instructionId: "0763cb2f-63a1-43ee-a79d-9ed00d9fa7dd" }, async () => {
    element_4 = page.locator("internal:role=row[name=\"\uf047 Item Quantity Price $\"i] >> input[name=\"item_name\"]");
    await element_4.click();
    await element_4.fill("Website design");
  });
  
  await testmu.step("Type \"2\" into \"textbox\"", { instructionId: "14643b49-a0b6-4f3c-8d4f-947d600cc3b7" }, async () => {
    element_5 = page.locator("internal:role=cell[name=\"Quantity\"i] >> input[name=\"item_quantity\"]");
    await element_5.click();
    await element_5.fill("2");
  });
  
  await testmu.step("Type \"250\" into \"textbox\"", { instructionId: "17a021f7-f64a-4a29-93b1-4795be6d6444" }, async () => {
    element_6 = page.locator("internal:role=cell[name=\"Price $\"i] >> input[name=\"item_price\"]");
    await element_6.click();
    await element_6.fill("250");
  });
  
  await testmu.step("Click \"\uf00c Save\"", { instructionId: "3f0eab7f-98ca-4682-aeb1-cd46e2e29b03" }, async () => {
    element_19 = page.locator("internal:role=link[name=\"\uf00c Save\"i]");
    await element_19.click();
  });
  
  await testmu.step("Waiting 500 ms after entering invoice item", { instructionId: "e3a99e99-c2dc-4b3a-8e42-d7a9ff5f09d5" }, async () => {
    await page.waitForTimeout(500);
  });
  
  await testmu.step("Waiting 300 milliseconds after saving the invoice", { instructionId: "30ac7c35-9bb3-41e7-8a62-ac9688325a5b" }, async () => {
    await page.waitForTimeout(300);
  });
  
  await testmu.step("Reading the invoice line item name", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("invoice_item_website_design", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {73: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(73).value);\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:role=row[name=\"\uf047 Item Website design\"i] >> input[name=\"item_name\"]"], query: "the item name in the invoice line item", expectedValue: "Website design", needsUnitConversion: false, operator: "equals", transforms: ["strip"], condition: "The invoice shows the item \"Website design\"." }));
  });
  
  await testmu.step("Reading the invoice total from the summary", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("invoice_total_500_00", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {133: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(133).textContent);\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:role=cell[name=\"$500.00\"i] >> nth=3"], query: "the total amount in the invoice summary", expectedValue: "$500.00", needsUnitConversion: false, operator: "equals", transforms: ["strip"], condition: "The invoice shows a total of $500.00." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "59524e18-3689-46af-98fe-bc9fec57f82a" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The invoice shows the item \"Website design\".", "passed": true, "operator": "equals", "transforms": ["strip"], "json_path": null, "expected": "Website design", "extracted_value": "{{invoice_item_website_design}}", "store_key": "invoice_item_website_design", "variable_refs": {"{{invoice_item_website_design}}": "Website design"}}, {"description": "The invoice shows a total of $500.00.", "passed": true, "operator": "equals", "transforms": ["strip"], "json_path": null, "expected": "$500.00", "extracted_value": "{{invoice_total_500_00}}", "store_key": "invoice_total_500_00", "variable_refs": {"{{invoice_total_500_00}}": "$500.00"}}], "sub_checks": [{"description": "The invoice shows the item \"Website design\".", "store_key": "invoice_item_website_design", "expected_value": "Website design", "extracted_value": "{{invoice_item_website_design}}", "operator": "equals", "transforms": ["strip"]}, {"description": "The invoice shows a total of $500.00.", "store_key": "invoice_total_500_00", "expected_value": "$500.00", "extracted_value": "{{invoice_total_500_00}}", "operator": "equals", "transforms": ["strip"]}], "composite_operator": "and", "claim": "the invoice shows the item \"Website design\" and a total of $500.00"});
  });
});

testmu.run();
import testmu, { resolveVar, setVar } from '@testmuai/playwright-bindings';
import { expect } from '@playwright/test';

testmu.configure({
  "build": "e47afce6-1d31-40c0-aaae-1d423606ad1c",
  "name": "Add and Verify New Client",
  "tcId": "TC-214",
  "network": (process.env.NETWORK ?? "false").toLowerCase() === "true",
  "autoHealVersion": "AH2",
  "defaultActionTimeoutMs": 10000,
  "defaultNavigationTimeoutMs": 60000,
  "kaneRunV4": true
});

testmu.test("Add and Verify New Client", async (page) => {
  let element_0, element_1, element_6, element_7, element_2, element_3, element_4, element_5, element_12;
  await testmu.step("Navigate to http://localhost:4895/", { instructionId: "3f850490-b990-450b-9134-fd32f3bc8e15" }, async () => {
    await page.goto("http://localhost:4895/");
  });
  
  await testmu.step("Waiting for the login form to be queued", { instructionId: "01b88add-376a-48ba-8f69-4d8ce343cec7" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"admin@example.com\" into \"Email\"", { instructionId: "5347229c-b9dd-4f73-9887-38e5e4f67304" }, async () => {
    element_0 = page.locator("internal:role=textbox[name=\"Email\"i]");
    await element_0.click();
    await element_0.fill("admin@example.com");
  });
  
  await testmu.step("Waiting for the queued plan steps to complete", { instructionId: "04a327ed-7f89-408c-8465-c25c25a5a844" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"Invoice-Test-2026\" into \"Password\"", { instructionId: "ba7107dd-ef68-45e8-a1c8-962f2872c140" }, async () => {
    element_1 = page.locator("internal:role=textbox[name=\"Password\"i]");
    await element_1.click();
    await element_1.fill("Invoice-Test-2026");
  });
  
  await testmu.step("Clicking the Login submit button", { instructionId: "227dffbf-f649-4f11-968e-ed15058640d2" }, async () => {
    element_6 = page.locator("internal:role=button[name=\"\uf09c Login\"i]");
    await element_6.click();
  });
  
  for (let _untilRetries = 0; _untilRetries < 10; _untilRetries++) {
    console.log(`[until-loop] iteration ${_untilRetries + 1}/10`);
    await testmu.step("Waiting for the Quick Actions text to appear on the dashboard", { instructionId: "bcac261e-4b0e-4b71-93fa-807fa28fb41c" }, async () => {
      await page.waitForTimeout(1000);
    });
    await page.waitForTimeout(500);
    if (await testmu.checkUntilCondition(page, "the text \"Quick Actions\" is visible")) break;
  }
  
  await testmu.step("Checking whether the dashboard is shown", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("dashboard_shown_check", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {31: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(31).querySelector('b')?.textContent.trim() === 'Quick Actions');\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:text=\"Quick Actions Add Client\"i"], query: "whether the dashboard Quick Actions section is present", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The dashboard is shown." }));
  });
  
  await testmu.step("Checking whether Quick Actions is visible", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("quick_actions_visible", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {31: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(31).querySelector('b')?.textContent.trim() === 'Quick Actions');\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:text=\"Quick Actions Add Client\"i"], query: "whether the Quick Actions section is present on the dashboard", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The dashboard includes \"Quick Actions\"." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "ed225fb4-919c-40c4-aaea-484b1dd8bcf0" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The dashboard is shown.", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{dashboard_shown_check}}", "store_key": "dashboard_shown_check", "variable_refs": {"{{dashboard_shown_check}}": "true"}}, {"description": "The dashboard includes \"Quick Actions\".", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{quick_actions_visible}}", "store_key": "quick_actions_visible", "variable_refs": {"{{quick_actions_visible}}": "true"}}], "sub_checks": [{"description": "The dashboard is shown.", "store_key": "dashboard_shown_check", "expected_value": "true", "extracted_value": "{{dashboard_shown_check}}", "operator": "equals", "transforms": []}, {"description": "The dashboard includes \"Quick Actions\".", "store_key": "quick_actions_visible", "expected_value": "true", "extracted_value": "{{quick_actions_visible}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the dashboard is shown with \"Quick Actions\""});
  });
  
  await testmu.step("Clicking Add Client in Quick Actions panel", { instructionId: "25e3c074-42b8-49e9-91c9-9fce97e908d8" }, async () => {
    element_7 = page.locator("internal:role=link[name=\"\uf007 Add Client\"i]");
    await element_7.click();
  });
  
  await testmu.step("Waiting for the plan steps to finish processing", { instructionId: "cecfe6b8-e881-4a1d-ac8e-afb65c46fc8d" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Type \"Acme\" into \"Client Name\"", { instructionId: "d3fc425f-8d75-497a-a839-ba490239a4a3" }, async () => {
    element_2 = page.locator("internal:role=textbox[name=\"Client Name\"i]");
    await element_2.click();
    await element_2.fill("Acme");
  });
  
  await testmu.step("Type \"Corporation\" into \"Client Surname (Optional)\"", { instructionId: "9d1dd562-cca2-487b-bd26-caef32cd1697" }, async () => {
    element_3 = page.locator("internal:role=textbox[name=\"Client Surname (Optional)\"i]");
    await element_3.click();
    await element_3.fill("Corporation");
  });
  
  await testmu.step("Type \"Springfield\" into \"City\"", { instructionId: "a3494427-602b-4376-a74e-ed3932fbd9e4" }, async () => {
    element_4 = page.locator("internal:role=textbox[name=\"City\"i]");
    await element_4.click();
    await element_4.fill("Springfield");
  });
  
  await testmu.step("Type \"billing@acme.example.com\" into \"Email Address\"", { instructionId: "90ff8595-3a00-4436-97cf-ffcc1d379468" }, async () => {
    element_5 = page.locator("internal:role=textbox[name=\"Email Address\"i]");
    await element_5.click();
    await element_5.fill("billing@acme.example.com");
  });
  
  await testmu.step("Click \"\uf00c Save\"", { instructionId: "a7928506-760f-41ed-9858-1add59025083" }, async () => {
    element_12 = page.locator("internal:role=button[name=\"\uf00c Save\"i]");
    await element_12.click();
  });
  
  await testmu.step("Waiting 1000 ms for the saved client page to settle", { instructionId: "ddde0b18-6a70-4035-b740-71c1125d95e9" }, async () => {
    await page.waitForTimeout(1000);
  });
  
  await testmu.step("Checking whether the Acme Corporation client page is shown", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("acme_corporation_client_page_shown", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {32: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (el(32).textContent.trim() === 'Acme Corporation');\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["h1"], query: "whether the Acme Corporation client page heading is present", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The client page for \"Acme Corporation\" is shown." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "60ea4274-c02b-43e2-9529-7311b99a212e" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The client page for \"Acme Corporation\" is shown.", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{acme_corporation_client_page_shown}}", "store_key": "acme_corporation_client_page_shown", "variable_refs": {"{{acme_corporation_client_page_shown}}": "true"}}], "sub_checks": [{"description": "The client page for \"Acme Corporation\" is shown.", "store_key": "acme_corporation_client_page_shown", "expected_value": "true", "extracted_value": "{{acme_corporation_client_page_shown}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the client page for \"Acme Corporation\" is shown"});
  });
  
  await testmu.step("Navigate to http://localhost:4895/clients/status/active", { instructionId: "706ac0b7-8c93-42a2-86e8-1a6f48b97e2a" }, async () => {
    await page.goto("http://localhost:4895/clients/status/active");
  });
  
  await testmu.step("Checking the email shown for Acme Corporation", async () => {
    await page.waitForLoadState("domcontentloaded", { timeout: 5000 });
    setVar("acme_corporation_email_check", await testmu.textualAnalyzer(page, { wrappedJs: "(els) => {\n  const __m = {54: 0};\n  const el = (i) => els[__m[i]];\n  const __v = (Array.from(el(54).querySelectorAll('td')).some(cell => cell.textContent.trim() === 'Acme Corporation') && Array.from(el(54).querySelectorAll('td')).some(cell => cell.textContent.trim() === 'billing@acme.example.com'));\n  return (typeof __v === 'boolean' ? String(__v) : __v);\n}", locators: ["internal:role=row[name=\"Yes Acme Corporation billing@\"i]"], query: "whether the Acme Corporation client row contains the billing email address", expectedValue: "true", needsUnitConversion: false, operator: "equals", transforms: [], condition: "The clients list shows \"Acme Corporation\" with the email \"billing@acme.example.com\"." }));
  });
  
  await testmu.step("Assertion check", { instructionId: "60b020af-2d40-4b80-bd14-a62828715bfc" }, async () => {
    await testmu.verifyAssertion(page, "Assertion check", {"operator": ["equals"], "assertion_operands": [], "left_operand": null, "right_operand": null, "operands": [], "sub_results": [{"description": "The clients list shows \"Acme Corporation\" with the email \"billing@acme.example.com\".", "passed": true, "operator": "equals", "transforms": [], "json_path": null, "expected": "true", "extracted_value": "{{acme_corporation_email_check}}", "store_key": "acme_corporation_email_check", "variable_refs": {"{{acme_corporation_email_check}}": "true"}}], "sub_checks": [{"description": "The clients list shows \"Acme Corporation\" with the email \"billing@acme.example.com\".", "store_key": "acme_corporation_email_check", "expected_value": "true", "extracted_value": "{{acme_corporation_email_check}}", "operator": "equals", "transforms": []}], "composite_operator": "and", "claim": "the clients list shows \"Acme Corporation\" with the email \"billing@acme.example.com\""});
  });
});

testmu.run();
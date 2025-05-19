# Translating InvoicePlane

InvoicePlane is a multilingual application, and we rely on community contributions to keep translations up to date. If you want to help translate InvoicePlane into your language, follow this guide.

---

## 🌍 Where Are Translations Managed?

All translations are hosted on **[Crowdin](https://crowdin.com/)** under the project name:  
**[FusionInvoice on Crowdin](https://translations.invoiceplane.com)**

---

## 🔹 How to Contribute

1. Create an account at [crowdin.com](https://crowdin.com/).
2. Join the **FusionInvoice** project via [translations.invoiceplane.com](https://translations.invoiceplane.com).
3. Choose your preferred language (e.g., `de`, `fr`, `es`, `pt-BR`).
4. Translate missing strings or improve existing ones.
5. Save and submit your translations for review.

---

## 📜 Translation Guidelines

- Use consistent terminology (reference existing translations).
- **Do not translate** variables like `{invoice_number}` or `{client_name}`.
- Preserve all formatting (e.g., Markdown, HTML, newline breaks).
- Ask in the [community forums](https://community.invoiceplane.com/) if you're unsure.

---

## 🛠️ Technical Details (for Developers)

- Translations are stored in `lang/{locale}/` using Laravel conventions.
- File format is PHP: `lang/en/invoices.php`, `lang/fr/clients.php`, etc.
- Language folders use **short codes**: `en`, `de`, `fr`, `es`, etc.
- Do not edit translation files manually. All changes should go through Crowdin.

---

## 💬 Need Help?

- Ask questions in our [Community Forums](https://community.invoiceplane.com).
- Reach out via [Discord](https://discord.gg/PPzD2hTrXt).
- Check ongoing discussions directly in the Crowdin platform.

---

_Thank you for helping make InvoicePlane V2 accessible to a global audience!_

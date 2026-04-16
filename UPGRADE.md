# Upgrade Guide

This guide provides instructions for upgrading InvoicePlane to newer versions.

## Table of Contents

- [Instructions to upgrade to 1.6.3 from 1.6.2](#instructions-to-upgrade-to-163-from-162)
- [Instructions to upgrade to 1.6.0 from 1.5.11](#instructions-to-upgrade-to-160-from-1511)

---

## Instructions to upgrade to 1.6.3 from 1.6.2

### 1. Preliminary operations

Follow the procedure outlined in [Upgrade 1.6.0 from 1.5.11](#1-preliminary-operations).

### 2. Replace files & test

1. Copy all files to the root directory of your InvoicePlane installation but **do not** overwrite the following files:
   - The `ipconfig.php` file
   - Customized templates in the `application/views/` folder
   - The files for custom styles: `assets/core/css/custom.css` and `assets/core/css/custom-pdf.css`
   - Uploaded images in the `uploads/` folder (e.g. your company logo)
   - Custom language keys at `application/language/COUNTRY/custom_lang.php`

   > **Hint:** An *easy* way of performing this operation is to upload the whole new InvoicePlane version in a different folder, outside of your current installation root folder, and copy the above mentioned files in the new folder you just uploaded. Afterwards just rename your current folder to something like `my_current_folder_old` and rename your new-version-folder with the name of `my_current_folder`.

2. Open `http://yourdomain.com/index.php/setup` and follow the instructions. The app will run all updates on its own.
   - If you encounter any errors when upgrading the table, press "Try Again" to resolve those errors and continue with the setup.

3. Now that the update is installed, moved and protected, it's time to log in and see if everything is working: login again and check if everything is working.

---

## Instructions to upgrade to 1.6.0 from 1.5.11

### 1. Preliminary operations

1. Make a backup of your database and all files. (This is **very important** to prevent any data loss)

2. Download the latest version from [InvoicePlane.com](https://invoiceplane.com/downloads).

### 2. Replace files & test

1. Copy all files to the root directory of your InvoicePlane installation but **do not** overwrite the following files:
   - The `ipconfig.php` file
   - Customized templates in the `application/views/` folder
   - The files for custom styles: `assets/core/css/custom.css` and `assets/core/css/custom-pdf.css`
   - Uploaded images in the `uploads/` folder (e.g. your company logo)
   - Custom language keys at `application/language/COUNTRY/custom_lang.php`

   > **Hint:** An *easy* way of performing this operation is to upload the whole new InvoicePlane version in a different folder, outside of your current installation root folder, and copy the above mentioned files in the new folder you just uploaded. Afterwards just rename your current folder to something like `my_current_folder_old` and rename your new-version-folder with the name of `my_current_folder`.

2. Now that the files are placed, it's time to fix the `ipconfig.php` file.
   - Open `ipconfig.php` and comment out the top line in the file by adding a `#` at the beginning of the first line. The result should be like this:
     ```
     # <?php exit('No direct script access allowed'); ?>
     ```
   - Close the `ipconfig.php` file

3. Open `http://yourdomain.com/index.php/setup` and follow the instructions. The app will run all updates on its own.
   - If you encounter any errors when upgrading the table, press "Try Again" to resolve those errors and continue with the setup.

4. Now that the `ipconfig.php` file is fixed, moved and protected, it's time to log in and see if everything is working
   - Login again and check if everything is working.
   - If you were using the online payments module please navigate to `//your-domain.com/settings` and to the tab `online payment` and disable all payment methods that are not *stripe*. InvoicePlane 1.6 at the moment supports only Stripe as a payment gateway.

---

> **Note:** For additional help and support, visit the [official wiki](https://wiki.invoiceplane.com) or the [community forum](https://community.invoiceplane.com/).

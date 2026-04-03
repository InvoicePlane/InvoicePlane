<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Check if content is plain text (no HTML tags).
 * 
 * This helper function checks if the given content contains HTML tags.
 * Used to determine whether to add nl2br() conversion for plain text emails.
 *
 * @param string $content The content to check
 * @return bool True if plain text (no HTML), false if contains HTML
 */
function is_plain_text(string $content): bool
{
    return mb_strlen($content) === mb_strlen(strip_tags($content));
}

/**
 * Sanitize HTML content for email templates using HTML Purifier.
 * 
 * This function provides defense-in-depth protection against XSS attacks in email templates.
 * It allows safe HTML tags (p, br, strong, em, h1-h4, etc.) while removing all JavaScript,
 * event handlers, and dangerous attributes.
 *
 * @param string $html The HTML content to sanitize
 * @return string The sanitized HTML content
 */
function sanitize_email_template_html(string $html): string
{
    // Load HTML Purifier library
    require_once FCPATH . 'vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';
    
    // Create HTML Purifier configuration
    $config = HTMLPurifier_Config::createDefault();
    
    // Set cache directory (use CodeIgniter's cache directory)
    $cache_dir = APPPATH . 'cache/htmlpurifier';
    if ( ! is_dir($cache_dir)) {
        mkdir($cache_dir, 0755, true);
    }
    $config->set('Cache.SerializerPath', $cache_dir);
    
    // Allow safe HTML tags for email templates
    // This is a whitelist approach - only these tags are allowed
    $config->set('HTML.Allowed', 
        'p,br,strong,b,em,i,u,h1,h2,h3,h4,h5,h6,' .
        'ul,ol,li,a[href|title|target],' .
        'span[style],div[style],' .
        'table,thead,tbody,tr,th,td,' .
        'img[src|alt|width|height],' .
        'hr,code,pre,blockquote'
    );
    
    // Allow safe CSS properties in style attributes
    $config->set('CSS.AllowedProperties', 
        'color,background-color,font-size,font-weight,font-family,' .
        'text-align,text-decoration,margin,padding,' .
        'border,border-color,border-width,border-style'
    );
    
    // Ensure UTF-8 encoding
    $config->set('Core.Encoding', 'UTF-8');
    
    // Disable external resources (prevent SSRF attacks)
    $config->set('URI.DisableExternalResources', true);
    
    // Create purifier instance
    $purifier = new HTMLPurifier($config);
    
    // Sanitize and return
    return $purifier->purify($html);
}

/**
 * Prepare email template body for display in forms.
 * 
 * This function escapes the email template body for safe display in HTML form fields.
 * It prevents stored XSS by ensuring any malicious scripts stored in the database
 * cannot execute when the template is edited.
 *
 * @param string $html The HTML content to escape
 * @return string The escaped HTML content safe for display in form fields
 */
function escape_email_template_for_form(string $html): string
{
    // Use htmlspecialchars with ENT_QUOTES to escape both single and double quotes
    // This prevents breaking out of the textarea context
    return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
}

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
 * Check if content should have line breaks converted to HTML <br> tags.
 * 
 * This is a convenience function for email processing that determines whether
 * nl2br() should be applied. Returns true only for non-empty plain text content
 * (no HTML tags). Returns false for empty strings (optimization) or HTML content.
 * 
 * Note: Semantically, an empty string IS plain text, but we return false to
 * avoid unnecessary nl2br() processing in email workflows.
 *
 * @param string $content The content to check
 * @return bool True if nl2br() should be applied, false otherwise
 */
function is_plain_text(string $content): bool
{
    // Return false for empty strings to avoid unnecessary nl2br() processing
    if (trim($content) === '') {
        return false;
    }
    
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
        if ( ! mkdir($cache_dir, 0755, true)) {
            log_message('error', 'Failed to create HTMLPurifier cache directory: ' . $cache_dir);
            // Disable cache if directory creation fails
            $config->set('Cache.SerializerPath', null);
        } else {
            $config->set('Cache.SerializerPath', $cache_dir);
        }
    } else {
        $config->set('Cache.SerializerPath', $cache_dir);
    }
    
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

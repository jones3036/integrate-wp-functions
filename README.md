# Integrate WP Theme Custom Functions

A site-specific WordPress plugin that provides essential WordPress tweaks and customizations that survive theme updates.

**Version:** 1.0.0

## Features

### Security & Privacy
- **Disable XML-RPC** - Prevents XML-RPC attacks and improves site security
- **Clean WordPress Head Output** - Removes unnecessary meta tags and links that expose WordPress version and unnecessary functionality
- **Hide Admin Bar** - Hides the admin bar on the front end for all users

### Content Management
- **Disable Gutenberg for Posts** - Disables the block editor for post creation/editing
- **Disable Gutenberg Widgets Screen** - Falls back to the classic widgets interface
- **Remove Gutenberg Front-end Styles** - Eliminates unnecessary Gutenberg CSS from the front end

### Comments
- **Disable Comments Sitewide** - Completely disables comments functionality across the entire site:
  - Removes comments menu from admin
  - Hides comment support from all post types
  - Disables comment feeds
  - Removes comment-related dashboard widgets

### Media
- **Enable SVG Uploads** - Allows SVG and SVGZ file uploads in the media library with security sanitization:
  - Strips script tags from SVG files
  - Removes inline event handlers (onclick, onload, etc.)
  - Prevents XSS attacks while maintaining SVG functionality

### Admin Notifications
- **Plugin Recommendations** - Displays a notice to administrators about recommended plugins not currently installed:
  - Elementor / Elementor Pro
  - Yoast SEO / The SEO Framework
  - Redirection
  - UpdraftPlus
  - WP Mail SMTP
  - Code Snippets
  - CookieYes
  - LiteSpeed Cache
  - WordPress Importer

## Installation

1. Download or clone this repository into your WordPress `wp-content/plugins/` directory
2. Rename the folder to `integrate-wp-functions`
3. Go to **Plugins** in your WordPress admin panel
4. Find "Integrate WP theme custom functions" and click **Activate**

## Usage

This plugin works automatically once activated. No configuration is required—all tweaks are applied immediately.

### Customization

To disable specific features, comment out the relevant sections in `custom-functions.php`:

```php
// Example: Comment out to keep Gutenberg enabled
// add_filter( 'use_block_editor_for_post', '__return_false', 10 );
```

## File Structure

```
integrate-wp-functions/
├── custom-functions.php     (Main plugin file)
├── README.md               (This file)
└── .git/                   (Version control)
```

## Requirements

- WordPress 5.0+
- PHP 5.6+

## Notes

- This plugin is designed for site-specific customizations
- All changes persist across theme updates
- SVG uploads are sanitized to prevent security vulnerabilities
- The plugin only affects admin users for visibility-related features

## License

Please see your project documentation for licensing information.

## Support

For issues or questions regarding this plugin, contact your site administrator.

# Integrate WP Theme Custom Functions

A site-specific WordPress plugin that provides essential WordPress tweaks and customizations that survive theme updates.

**Version:** 1.3.1

**Repository:** [https://github.com/jones3036/integrate-wp-functions](https://github.com/jones3036/integrate-wp-functions)

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

### Performance & Hardening
- **Limit Post Revisions** - Keeps only the last 3 revisions per post
- **Remove Asset Query Strings** - Removes `?ver=` from scripts and styles for cache-friendly URLs
- **REST API Lockdown** - Optionally disables REST API access for unauthenticated visitors

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
- **Plugin Install/Activate Links** - Provides direct install or activate links for recommended plugins when possible
- **Update Checker** - Automatically checks GitHub for new releases and notifies admins when updates are available, allowing one-click updates directly from the plugins page

## Installation

1. Download or clone this repository into your WordPress `wp-content/plugins/` directory
2. Rename the folder to `integrate-wp-functions`
3. Go to **Plugins** in your WordPress admin panel
4. Find "Integrate WP theme custom functions" and click **Activate**

## Usage

This plugin works automatically once activated. You can also customize behavior from the new settings page at **Settings > Integrate WP Functions**.

### Customization

To disable specific features, use the settings page or comment out the relevant sections in `custom-functions.php` if needed:

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
- The plugin only affects admin users for visibility-related features

## License

Please see your project documentation for licensing information.

## Support

For issues or questions regarding this plugin, contact your site administrator.

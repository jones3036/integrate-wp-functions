# Integrate WP Theme Custom Functions

A site-specific WordPress plugin that provides essential WordPress tweaks and customizations that survive theme updates.

**Version:** 1.4.1

**Repository:** [https://github.com/jones3036/integrate-wp-functions](https://github.com/jones3036/integrate-wp-functions)

## Features

### Security & Privacy
- **Disable XML-RPC** - Prevents XML-RPC attacks and improves site security
- **Clean WordPress Head Output** - Removes unnecessary meta tags and links that expose WordPress version and unnecessary functionality
- **Hide Admin Bar** - Hides the admin bar on the front end for all users
- **Hide WordPress Version** - Removes WordPress version from frontend (hides from generators and meta tags)
- **Disable User Enumeration** - Blocks access to `?author=` archives to prevent user discovery
- **Disable Login Error Hints** - Doesn't reveal whether a username exists on login form
- **Force SSL on Admin Login** - Enforces secure HTTPS connection for admin login
- **Remove "Howdy" Greeting** - Simplifies the admin bar greeting message

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

### Admin & Integration
- **Force Strong Password Requirements** - Enforces 12+ characters, uppercase, lowercase, numbers, and special characters
- **Disable Jetpack Integration** - Prevents Jetpack from loading and showing promotional notices

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
- **Plugin Update Checker** - Automatically checks GitHub for new releases and notifies admins when updates are available, allowing one-click updates directly from the plugins page. Supports both public and private repositories.

## Installation

1. Download or clone this repository into your WordPress `wp-content/plugins/` directory
2. Rename the folder to `integrate-wp-functions`
3. Go to **Plugins** in your WordPress admin panel
4. Find "Integrate WP theme custom functions" and click **Activate**

## Setup for Private Repositories

If your GitHub repository is **private**, you must provide a GitHub Personal Access Token (PAT):

### Option 1: Via Settings (Easier)
1. Go to **Settings > Integrate WP Functions**
2. Scroll to **GitHub Private Repository** section
3. Paste your PAT in the "GitHub Personal Access Token" field
4. Click **Save Changes**

### Option 2: Via wp-config.php (More Secure)
Add this to your `wp-config.php` file:
```php
define( 'IWF_GITHUB_TOKEN', 'your_github_pat_here' );
```

### Creating a GitHub Personal Access Token (PAT)

1. Go to [GitHub Settings > Developer settings > Personal access tokens](https://github.com/settings/tokens)
2. Click **Generate new token (classic)**
3. Give it a name (e.g., "WordPress Plugin Updates")
4. Select the **`repo`** scope (full control of private repositories)
5. Click **Generate token**
6. Copy the token (you won't be able to see it again)
7. Use the token in the plugin settings or wp-config.php

**⚠️ Security Notes:**
- Keep your token secret. If using Option 1, it's stored in your database.
- Using wp-config.php (Option 2) is more secure.
- Only the `repo` scope is needed for private repo access.
- Tokens don't expire by default but can be regenerated anytime.

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

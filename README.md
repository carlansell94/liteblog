![Composer workflow](https://github.com/carlansell94/liteblog-php/actions/workflows/composer.yml/badge.svg?event=push)
![PHPStan workflow](https://github.com/carlansell94/liteblog-php/actions/workflows/phpstan.yml/badge.svg?event=push)

A lightweight blogging platform written in PHP, using an MVC style structure.

Originally written a few years ago, it has been updated with PHP 8.1 features.

It is currently under development. While it it currently working, several features are missing and parts of the codebase are planned to be rewritten in the future.

## Features
* Posts
* Categories
* Tags
* Admin Area

## Future Improvements
* Setup
* Images/Videos
* Pages
* Comments
* Settings UI
* Additional Themes
* Probably a lot more

# Installation
This project is designed to be installed using composer.

To do this, clone the repository and run
```
composer update
composer install -o
```
inside the cloned folder.

You'll need to have MariaDB/MySQL running on your server, with the mysqli PHP extension installed. Import the schema.sql file to set up the database.

# Configuration
Before running, configure the app using the config.ini file. Current options are:
* DB_HOST (string): Database hostname
* DB_NAME (string): Database name
* DB_USER (string): Database username
* DB_PASS (string): Database password
* SITE_ROOT (string): Root of the app's domain, relative to the domain root. E.g. for example.com/blog, this value should be set to "/blog".
* THEME_DIR (string): The directory, should be set to "/default", unless you add your own theme directory under /themes.
* ADMIN_URL (string): Path to use for the admin dashboard.
* EXCERPT_LENGTH (int): Length of the excerpt to show on the list of posts.
* POSTS_PER_PAGE (int): Number of entires to display per page. Applies to both the main site, and admin pages.
* SITE_NAME (string): Name of the site, displayed in the top-left.
* SITE_TAGLINE (string): Tagline of the site, displayed under the site name.
* DEBUG_MODE (bool): If enabled, PHP errors are output to the user.

Once complete, move this file to /src/Config.

Your web server should be configured to direct all requests to index.php. For example, for nginx:
```
try_files $uri $uri/ /index.php?$args;
```

Admin users currently have to be added to the database manually. The 'pass' field should be generated using password_hash().

# Third-Party Libraries
This application uses the following third-party libraries:
* [PHP Markdown](https://github.com/michelf/php-markdown) - installed by composer
* [EasyMDE](https://github.com/Ionaru/easy-markdown-editor) - external JS dependency
* [Tagify](https://github.com/yairEO/tagify) - external JS dependency
* [PHPStan](https://github.com/phpstan/phpstan) - installed by composer, dev version only
* [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) - installed by composer, dev version only

### Icons
Inspired by the equivalent Google icons, the icons included in this app have been created from scratch using Inkscape. Feel free to use them in your own projects.
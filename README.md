
# WP Code Reference

WP Code Reference is a theme used for displaying code reference of WordPress project (plugin or any WordPress library or theme that follows inline [WordPress PHP documentation standards](https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/php/)).

## Requirements

Yu will need Composer and WP-CLI, and PHP 5.4+.

## Setup

There are two ways for setting everything up.

### 1.

If you manage your WordPress install and plugins with Composer (for example, with help of [Bedrock](https://roots.io/bedrock/)), you just need to require [`dimadin/wp-plugin-code-reference` package](https://packagist.org/packages/dimadin/wp-plugin-code-reference) and it will install all necessary dependencies.

### 2.

In other case, you will need to manually setup all required dependencies.

#### Install parent theme

Checkout the [developer.wordpress.org theme](https://meta.svn.wordpress.org/sites/trunk/wordpress.org/public_html/wp-content/themes/pub/wporg-developer/) from the meta svn repository inside themes directory (usually `/wp-content/themes`).

```bash
svn checkout https://meta.svn.wordpress.org/sites/trunk/wordpress.org/public_html/wp-content/themes/pub/wporg-developer/
```

#### Install child theme

This repository contain child theme, WP Code Reference. Clone this repository inside themes directory.

#### Install WP-Parser

Inside plugins directory (usually `/wp-content/plugins`), clone [WP-Parser](https://github.com/WordPress/phpdoc-parser) and inside of it install its dependencies with `composer install`.

#### Install SyntaxHighlighter Evolved

This is not required, but it's recommended to install [SyntaxHighlighter Evolved](https://wordpress.org/plugins/syntaxhighlighter/) plugin for nicer code display.

## Usage

When you have installed everything, activate WP-Parser and SyntaxHighlighter Evolved plugins, and WP Code Reference theme.

Then, you will need to install or clone repository of WordPress project you want to have code reference for. You can do it in its standard location (for example, if it's plugin inside plugins directory).

Finally, you need to run indexing command from WP-Parser. Go to project's directory and run

```bash
wp parser create . --user=1
```

where `.` stands for the current directory and `--user=1` is for ID of a user that posts will be attributed to.

If you want link "View on GitHub" to work, you will also have to run

```bash
wp option update wp_parser_gh_repository 'user/repo'
```

where `'user/repo'` is a path to your GitHub repository. If link is still not shown, run

```bash
wp option update wp_parser_imported_wp_version 'x.y.z'
```

where `'x.y.z'` is a tag that you based on your code reference.

## Notes

 - In one WordPress site, you can have code reference for just one project. If you want to have references for multiple projects, you either need to install separate WordPress instances and follow procedure from above, or have a WordPress multisite installation where each site in a network is for one project.
 - For every new version of your project, you will need to run parser as explained in section *[Usage](#usage)*. However, note that previously parsed resources that are no longer getting parsed (for example removed from the new version), are not removed from the database because of limitations of [WP-Parser](https://github.com/WordPress/phpdoc-parser/issues/147). You will need to reset database or do something else to remove them.
 - Directories `/tests` and `/vendor` are skipped and it will not be shown in code reference.

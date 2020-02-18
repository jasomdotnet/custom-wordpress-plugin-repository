# custom-wordpress-plugin-repository
Self-hosted WordPress plugin (and theme) repository so your custom plugins can be updated automatically. I have released this repository so you can start build your solution upon.

## What are minimum requirements
It runs for me on PHP 7.0. It requires no database, access to shell command and unzip class (both are not a big deal even on shared web-hosting).

## How to configure repository server
1. Fill `Config` class with your data, namely domain, path to the repository folder, latest tested WordPress version for all your plugins, minimum required WordPress version for all your plugins and array of slugs for plugins hosted within your private repository server
2. Set web accessible cronjob `https://www.example.com/repositoryfolder/get-info.php?action=cron` to every 6 hours

## How do looks like plugin header
Repo script supports plugin with structure : `mypluginslug1/mypluginslug1.php` while plugin header looks as follows:

```
/**
 * Plugin Name: Mypluginslug One
 * Description: Plugin description.
 * Version: 1.3
 * Author: Jasom Dotnet
 * Author URI: https://www.jasom.net
 * Plugin URI: https://www.jasom.net
 * License: MIT License
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: mypluginslug1
 * Domain Path: /languages
 */
 ```
 
 Version tags `1.1`, `1.2`, `1.3`...


## How to add new plugin to repository server
1. Create plugin with following structure: `mypluginslug1/mypluginslug1.php`
2. Zip plugin and upload to the repository folder
3. Add new plugin slug to the array represented by constant PLUGINS in Config class
4. Add 2 banners to the repository folder with name (and dimensions) like `mypluginslug1-banner-1544x500.jpg` and `mypluginslug1-banner-772x250.jpg`
5. Run cronjob above
6. See final jSon with under `https://www.example.com/repositoryfolder/get-info.php?slug=mypluginslug1`

## How to add new plugin release
1. Remove old `mypluginslug1.zip`
2. Upload new `mypluginslug1.zip`
3. Cronjob automatically does the rest

## What code needs to be added to plugin for synchronization

I have attached simple plugin in mypluginslug1.zip so read the code. It is also explained on repository homepage.

## Where I can find more info
Script home page is here https://www.jasom.net/
This code was inspired by @rudrastyh post [Self-Hosted Plugin Update](https://rudrastyh.com/wordpress/self-hosted-plugin-update.html) who took inspiration (I quess) from @YahnisElsts [wp-update-server](https://github.com/YahnisElsts/wp-update-server)

## Credits

Yahnis Elsts https://w-shadow.com
Misha Rudrastyh https://rudrastyh.com
Jasom Dotnet https://www.jasom.net
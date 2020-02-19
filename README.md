# Custom Wordpress Plugin Repository
Private self-hosted Wordpress plugin (and theme) repository so your custom plugins (and themes) can be updated automatically. I have released this code so you can start building your solution upon.

This GitHub package contains also a demo plugin called `mypluginslug1`. Read the [instructions](https://github.com/jasomdotnet/custom-wordpress-plugin-repository/tree/master/mypluginslug1) before you deploy it on test server.

## What are minimum requirements?
I don't know min. requirements, but it runs for me on PHP 7.3. It needs no database and requires access to shell + unzip class (both are not a big deal even on shared web-hosts).

## How to configure the repository server?
1. Fill `Config` class with your data, namely: domain, the path to the repository folder, latest tested WordPress version for all hosted plugins, minimum required WordPress version for all hosted plugins and an array of slugs belonging to plugins hosted within your private repository server
2. Set web accessible cronjob `https://www.example.com/repositoryfolder/get-info.php?action=cron` to every 6 hours

## How does look like a plugin header?
Repo script supports out of the box plugins with structure: `mypluginslug1/mypluginslug1.php` while plugin header looks as follows:

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
Tag versions like `1.1`, `1.2`, `1.3`...

## How to add a new plugin to repository server?
1. Create plugin with following structure: `mypluginslug1/mypluginslug1.php`
2. Zip plugin and upload it to repository folder
3. In Config class, add a new plugin slug to the array represented by constant `PLUGINS`
4. Add 2 banners to the repository folder with the name (and dimensions) like `mypluginslug1-banner-1544x500.jpg` and `mypluginslug1-banner-772x250.jpg`
5. Run cronjob above
6. See final jSon under `https://www.example.com/repositoryfolder/get-info.php?slug=mypluginslug1`

## How to add a new plugin release?
1. Remove old `mypluginslug1.zip` from repositore server
2. Upload new `mypluginslug1.zip` (with changed version in plugin header) to repository server 
3. Cronjob automatically does the rest

## How does jSon example look like?
This is sample jSon produces by the script:
```
{
   "status":"ok",
   "name":"Myplugin One",
   "slug":"mypluginslug1",
   "download_link":"https:\/\/www.dobka.tk\/repositoryfolder\/mypluginslug1.zip",
   "version":"1.0",
   "requires":"5.0",
   "tested":"5.3.2",
   "last_updated":"2020-02-19 00:02:29",
   "upgrade_notice":"Plugin update is available.",
   "author":"Jasom Dotnet",
   "author_homepage":"https:\/\/www.jasom.net",
   "sections":{
      "description":"Demonstration plugin.",
      "installation":"Upload the plugin to your blog, activate it and that is it!"
   
},
   "banners":{
      "low":"https:\/\/www.dobka.tk\/repositoryfolder\/mypluginslug1-banner-772x250.jpg",
      "high":"https:\/\/www.dobka.tk\/repositoryfolder\/mypluginslug1-banner-1544x500.jpg"
   
   }
}
```
## What code needs to be added to plugin for synchronization?

I have attached a simple plugin in `mypluginslug1.zip` so read the code. It is also explained on script homepage.

## Where I can find more info?
Script homepage is [here](https://www.jasom.net/). This code was inspired by [@rudrastyh](https://github.com/rudrastyh)'s post [Self-Hosted Plugin Update](https://rudrastyh.com/wordpress/self-hosted-plugin-update.html) who took inspiration (I guess) from [@YahnisElsts](https://github.com/YahnisElsts)'s [wp-update-server](https://github.com/YahnisElsts/wp-update-server).

### Credit

- [Yahnis Elsts](https://w-shadow.com)
- [Misha Rudrastyh](https://rudrastyh.com)
- [Jasom Dotnet](https://www.jasom.net)
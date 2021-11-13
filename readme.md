# Kirby LogBook plugin

A Kirby 3 panel plugin to visualise the `site/logs` folder in the panel.
Opinionated defaults to work best with the [kirby-log](https://github.com/bvdputte/kirby-log/) plugin.

![Kirby LogBook screenshot](https://user-images.githubusercontent.com/490505/141613064-4afaa26e-5cf9-4d52-8fc4-e8ea0606fb8a.png)

## Installation

- unzip [master.zip](https://github.com/bvdputte/kirby-logbook/archive/master.zip) as folder `site/plugins/kirby-logbook` or
- `git submodule add https://github.com/bvdputte/kirby-logbook.git site/plugins/kirby-logbook` or
- `composer require bvdputte/kirby-logbook`

## Formatting & access

For now only users with the admin role will be able to open/use the area in the plugin.

Logs from the [kirby-log](https://github.com/bvdputte/kirby-log/) plugin will be output as a table. All other logs will be print as a list of lines.

### Configurable options

All of them are optional.

```php
// in site/config/config.php
'bvdputte.logbook.default' => 'mylog.log', // String. Name of logfile to show by default. Defaults to first.
'bvdputte.logbook.hide' => ['my-restricted-log.log','some-other-log.log'], // Array with log filenames with no access in panel. Defaults to []
'bvdputte.logbook.maxLogLines' => 2000, // Integer. For performance reasons, only the x last lines of the log are being fetched and shown. Defaults to 2500
'bvdputte.logbook.paginationSize' => 50 // Integer. The amount of lines per paginated set in the panel. Defaults to 25
```

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bvdputte/kirby-logbook/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

# Kirby LogBook plugin

A Kirby 3 panel plugin to visualise the `site/logs` folder in the panel.\
Opinionated defaults to work best with the [kirby-log](https://github.com/bvdputte/kirby-log/) plugin out-of-the-box.

![Kirby LogBook screenshot](https://user-images.githubusercontent.com/490505/141863354-64864db9-41f8-4fdc-9816-238e2eb352b8.png)

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
// in site/config/config.php:
// String. Name of logfile to show by default. Defaults to first.
'bvdputte.logbook.default' => 'mylog.log',
// Array with log filenames with no access in panel. Defaults to []
'bvdputte.logbook.hide' => ['my-restricted-log.log','some-other-log.log'],
// Integer. For performance reasons, only the x last lines of the log are being fetched and shown. Defaults to 2500
'bvdputte.logbook.maxLogLines' => 2000,
// Integer. The amount of lines per paginated set in the panel. Defaults to 25
'bvdputte.logbook.paginationSize' => 50
```

## Development info

For development to the vue part, run `npm run dev`\
To build vue sfc, before commit, run `npm run build`


## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/bvdputte/kirby-logbook/issues/new).

## License

[MIT](https://opensource.org/licenses/MIT)

It is discouraged to use this plugin in any project that promotes racism, sexism, homophobia, animal abuse, violence or any other form of hate speech.

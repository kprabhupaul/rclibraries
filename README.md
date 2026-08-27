# RC Libraries

Manage and enqueue RC Libraries (CSS & JavaScript) on the WordPress frontend and backend.

## About

**RC Libraries** is a lightweight WordPress plugin for managing a collection of CSS and JavaScript libraries from a single admin page.

The plugin keeps library management simple: you can enable or disable each included library independently for the WordPress frontend and backend.

## Features

- Simple WordPress admin interface
- Enable or disable libraries on the frontend
- Enable or disable libraries in the WordPress backend
- Local, bundled library assets
- Versioning of bundled assets using the file modification time to help prevent stale browser caches
- Dependency support for libraries
- Lightweight and focused on library management

## Included Libraries

RC Libraries currently includes these author-owned libraries:

- **Fexios** — JavaScript library
- **Simal CSS** — CSS library
- **Simal JS** — JavaScript library

These libraries are developed and owned by the plugin author.

## How It Works

After activating the plugin:

1. Open **RC Libraries** from the WordPress admin menu.
2. Enable the libraries you want to use on the **Frontend**.
3. Enable the libraries you want to use in the **Backend**.
4. Save the settings.

Enabled libraries are automatically enqueued in the appropriate WordPress context.

## Requirements

- WordPress 6.0 or later
- PHP 8.1 or later

## Plugin Structure

```text
rc-libraries/
├── rc-libraries.php
├── functions.php
├── pages.php
├── ajax-callbacks.php
├── uninstall.php
└── default-libraries/
    ├── fexios.js
    ├── simal.css
    └── simal.js
```

## Development

This project is developed as a WordPress plugin and is intended to keep library management simple, predictable, and easy to maintain.

The repository contains the development source of the plugin. Releases can be packaged and distributed as a standard WordPress plugin ZIP.

## License

RC Libraries is licensed under the [MIT License](LICENSE).

## Author

**Pratap Kumar Kotti**

- GitHub: https://github.com/kprabhupaul

## Related Libraries

- Fexios: https://github.com/kprabhupaul/fexios
- Simal: https://github.com/kprabhupaul/simal

## Status

RC Libraries is currently under development.

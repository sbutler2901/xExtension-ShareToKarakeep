# ShareToKarakeep Extension for FreshRSS

This FreshRSS extension allows you to share articles directly to your self-hosted Karakeep instance.

## How to Install

1.  Download the extension and upload this directory to the `./extensions` directory of your FreshRSS installation.
2.  Enable the extension in the FreshRSS extension management panel.

## How to Configure

1.  After installation, navigate to the "Configuration" section of your FreshRSS settings.
2.  Under the "Extensions" tab, you will find the settings for "Share to Karakeep".
3.  Enter the URL of your Karakeep instance (e.g., `https://karakeep.example.com`).
4.  Enter your Karakeep API token. You can generate a token in your Karakeep account settings.
5.  Save the configuration.
6.  After saving, go to the "Sharing" tab in the configuration and enable the "Karakeep" sharing service.

## How to Use

1.  Once configured, a "Karakeep" option will be available in the sharing menu for each article.
2.  Clicking this option will open a confirmation dialog.
3.  Click "Send" to share the article to your Karakeep instance.

## Changelog

- 0.1: Initial version

## Dev Inspiration
- https://github.com/FreshRSS/Extensions/tree/main
- https://freshrss.github.io/FreshRSS/en/developers/Minz/

## Acknowledgements

This extension was retrofitted from https://github.com/daften/xExtension-ShareToLinkwarden/ to support Karakeep.

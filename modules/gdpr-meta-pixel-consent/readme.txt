=== GDPR Meta Pixel Consent for PixelYourSite ===
Contributors: chatgpt
Tags: gdpr, consent, meta pixel, facebook pixel, pixelyoursite, cookies
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later

Lightweight GDPR-style consent banner for PixelYourSite Meta/Facebook browser pixel.

== Description ==
This plugin does not store or duplicate your Meta Pixel ID. Keep all Meta/Facebook Pixel settings inside PixelYourSite. This plugin provides a responsive consent banner and feeds PixelYourSite's GDPR filters so the Meta/Facebook browser pixel and noscript output are blocked until marketing consent is accepted.

== Installation ==
1. Upload and activate the plugin.
2. Keep your Meta Pixel ID and events configured inside PixelYourSite.
3. Go to Settings > GDPR Meta Pixel and enable the consent gate.
4. Recommended: enable PixelYourSite's GDPR "AJAX filter values update" option when using page cache.

== Verification ==
In an incognito window, before consent, connect.facebook.net/fbevents.js should not load and _fbp/_fbc should not be created. After accepting marketing consent, the page reloads and PixelYourSite can load the browser pixel.

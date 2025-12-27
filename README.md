# Mautic Friendly Captcha Plugin

This plugin integrates Friendly Captcha with Mautic 5.

Friendly Captcha is a protected trademark. All Friendly Captcha trademarks, logos and brand names are the sole property of Friendly Captcha GmbH, Germany. Use of them does not imply any affiliation with or endorsement by them.

## Modes
This plugin supports three different embedding modes, depending on your use case:
- Legacy (default)
- Automatic
- Manual

### Legacy
Legacy is the default mode after upgrading the plugin, unless you select another mode. In Legacy mode, all necessary scripts are embedded automatically, and the captcha is added after a specified delay. This mode is required when embedding forms using an *iframe*.

**Delay**
- 2-second timeout via window.setTimeout (default for legacy reasons)
- [HTMLElement: load event on script tag](https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/load_event) (RECOMMENDED whenever possible)

If the Friendly Captcha SDK is not yet available in the window, the API script checks for its presence every 500ms. 

**Friendly Captcha versions**
- v1: 0.9.18
- v2: 0.1.36

The Friendly Captcha challenge scripts are integrated into this plugin to help with GDPR compliance, provided Mautic is hosted and configured appropriately. This plugin does not guarantee automatic compliance. 
Because all scripts are loaded from your Mautic instance, you depend on the plugin maintainer and your Mautic instance provider to update the Friendly Captcha SDK to the latest version.

### Automatic
Like **Legacy** mode, the captcha is added automatically to your form. The difference is that you must embed the Friendly Captcha SDK yourself. This makes the website owner independent from the plugin maintainer and the Mautic instance provider. Please note that the library expects the following global variables: `friendlyChallenge` for [Friendly Captcha v1](https://developer.friendlycaptcha.com/docs/v1/sdk/#if-you-are-using-the-widget-script-tag) and `frcaptcha` for v2. These variables should be available when using the widget script tag. Another advantage of embedding the SDK yourself in the header before the Mautic forms load is faster captcha loading times. We recommend this mode whenever possible for security reasons. See [Links to Friendly Captcha's Getting Started](#links-to-friendly-captchas-getting-started) for instructions on embedding the scripts.

Automatic embedding is the recommended method and should work with both traditional server-side rendered applications and client-side JavaScript frameworks.

### Manual Mode
This mode is optimized for embedding scripts in Javascript frameworks like Next.js, Vue, or Angular. By choosing manual embed mode, you control the lifecycle of all scripts in the most efficient way. We recommend this mode if you encounter script loading issues caused by your framework or if you want to [develop your own API](#rolling-your-own-api).

You MUST embed all scripts mentioned in the [Friendly Captcha documentation](#links-to-friendly-captchas-getting-started) yourself. You will also need to embed the API used for this plugin (or roll your own), which is located at Assets/js/add-captcha.js. This means you are responsible for manually adding the API and updating the file if there are breaking changes.

When using this mode, the form pushes a captcha settings object into a globally available queue which is located in the window. You CAN call the function `displayCaptchasInForms`, passing this queue as a parameter.

```
window.FriendlyCaptchaQueue = window.FriendlyCaptchaQueue || [];
window.FriendlyCaptchaQueue.push({
    wrapperId: '{{ captchaWrapperId }}',
    inputName: '{{ inputName }}',
    siteKey: '{{ siteKey }}',
    version: '{{ version }}',
    mode: '{{ field.properties.mode }}'
});

displayCaptchasInForms(window.FriendlyCaptchaQueue);
```

NOTE: `displayCaptchasInForms` is a convenience function but you are free to develop your own api and add the catcha in the way you need. See [Rolling your own API](#rolling-your-own-api) for limitations.

#### Rolling your own API
Wether you import the widget as npm module and/or write your own API, Friendly Captcha expects the following HTML Elements:
- A wrapper div
- A input field as captcha solution

**wrapper id**
The wrapper is created by this plugin and holds an id `'mauticform_' ~ formName ~ '_' ~ id ~ '_captcha'` e.g. `mauticform_testform_mycaptchaid_captcha`; testform would be the name of your mautic form whereas mycaptchaid the html id that mautic or the form creator gave to the form field.

**captcha solution field name**
This plugin expects the input field name to be set to `'mauticform[' ~ field.alias ~ ']'` whereas field.alias is the field alias defined in the mautic form field e.g. mauticform[captcha].

## Links to Friendly Captcha Getting Started
- [Friendly Captcha v1](https://developer.friendlycaptcha.com/docs/v2/getting-started/install)
- [Friendly Captcha v2](https://developer.friendlycaptcha.com/docs/v1/getting-started/install)
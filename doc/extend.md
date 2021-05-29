[HTML5 Boilerplate homepage](https://html5boilerplate.com) | [Documentation
table of contents](TOC.md)

# Extend and customise HTML5 Boilerplate

Here is some useful advice for how you can make your project with HTML5
Boilerplate even better. We don't want to include it all by default, as not
everything fits with everyone's needs.

* [News Feeds](#news-feeds)
* [Search](#search)
* [Social Networks](#social-networks)
* [URLs](#urls)
* [Web Apps](#web-apps)
* [security.txt](#security.txt)

## News Feeds

### Pingbacks

Your server may be notified when another site links to yours. The href attribute
should contain the location of your pingback service.

```html
<link rel="pingback" href="">
```

* High-level explanation:
  https://codex.wordpress.org/Introduction_to_Blogging#Pingbacks
* Step-by-step example case:
  https://www.hixie.ch/specs/pingback/pingback-1.0#TOC5
* PHP pingback service:
  https://web.archive.org/web/20131211032834/http://blog.perplexedlabs.com/2009/07/15/xmlrpc-pingbacks-using-php/



## Social Networks

### Schema.org

Google also provides a snippet specification that serves a similar purpose to
Facebook's Open Graph or Twitter Cards. This metadata is a subset of
[schema.org's microdata vocabulary](https://schema.org/), which covers many
other schemas that can describe the content of your pages to search engines. For
this reason, this metadata is more generic for SEO, notably for Google's
search-engine, although this vocabulary is also used by Microsoft, Pinterest and
Yandex.

You can validate your markup with the [Structured Data Testing
Tool](https://search.google.com/structured-data/testing-tool). Also, please
note that this markup requires to add attributes to your top `html` tag.

```html
<html class="no-js" lang="" itemscope itemtype="https://schema.org/Article">
  <head>

    <link rel="author" href="">
    <link rel="publisher" href="">
    <meta itemprop="name" content="">
    <meta itemprop="description" content="">
    <meta itemprop="image" content="">
```

## URLs

## Web Apps

There are a couple of meta tags that provide information about a web app when
added to the Home Screen on iOS:

* Adding `apple-mobile-web-app-capable` will make your web app chrome-less and
  provide the default iOS app view. You can control the color scheme of the
  default view by adding `apple-mobile-web-app-status-bar-style`.

```html
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
```

* You can use `apple-mobile-web-app-title` to add a specific sites name for the
  Home Screen icon.

```html
<meta name="apple-mobile-web-app-title" content="">
```

For further information please read the [official
documentation](https://developer.apple.com/library/archive/documentation/AppleApplications/Reference/SafariHTMLRef/Articles/MetaTags.html)
on Apple's site.


### Apple Touch Icons

Apple touch icons are used as icons when a user adds your webapp to the home
screen of an iOS devices.

Though the dimensions of the icon can vary between iOS devices and versions one
`180×180px` touch icon named `icon.png` and including the following in the
`<head>` of the page is enough:

```html
<link rel="apple-touch-icon" href="icon.png">
```

For a more comprehensive overview, please refer to Mathias' [article on Touch
Icons](https://mathiasbynens.be/notes/touch-icons).


### Apple Touch Startup Image

Apart from that it is possible to add start-up screens for web apps on iOS. This
basically works by defining `apple-touch-startup-image` with an according link
to the image. Since iOS devices have different screen resolutions it maybe
necessary to add media queries to detect which image to load. Here is an example
for an iPhone:

```html
<link rel="apple-touch-startup-image" media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" href="img/startup.png">
```


### Chrome Mobile web apps

Chrome Mobile has a specific meta tag for making apps [installable to the
homescreen](https://developer.chrome.com/multidevice/android/installtohomescreen)
which tries to be a more generic replacement to Apple's proprietary meta tag:

```html
<meta name="mobile-web-app-capable" content="yes">
```

Same applies to the touch icons:

```html
<link rel="icon" sizes="192x192" href="highres-icon.png">
```

### Theme Color

You can add the [`theme-color` meta
extension](https://html.spec.whatwg.org/multipage/semantics.html#meta-theme-color)
in the `<head>` of your pages to suggest the color that browsers and OSes should
use if they customize the display of individual pages in their UIs with varying
colors.

```html
<meta name="theme-color" content="#ff69b4">
```

The `content` attribute extension can take any valid CSS color.

Currently, the `theme-color` meta extension is supported by [Chrome 39+ for
Android
Lollipop](https://developers.google.com/web/updates/2014/11/Support-for-theme-color-in-Chrome-39-for-Android).


## security.txt

When security risks in web services are discovered by users they often lack the
channels to disclose them properly. As a result, security issues may be left
unreported.

Security.txt defines a standard to help organizations define the process for
users to disclose security vulnerabilities securely. Include a text file on your
server at `.well-known/security.txt` with the relevant contact details.

Check [https://securitytxt.org/](https://securitytxt.org/) for more details.

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


## security.txt

When security risks in web services are discovered by users they often lack the
channels to disclose them properly. As a result, security issues may be left
unreported.

Security.txt defines a standard to help organizations define the process for
users to disclose security vulnerabilities securely. Include a text file on your
server at `.well-known/security.txt` with the relevant contact details.

Check [https://securitytxt.org/](https://securitytxt.org/) for more details.

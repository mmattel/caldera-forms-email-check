# Wordpress: eMail Domain Check for Caldera Forms #
eMail Domain Check Processor for [Caldera Forms](https://calderaforms.com)

Contributors: mmattel

Tags: caldera forms, email, domain, dns

Requires at least: 4.5

Tested up to: 5.4

Stable tag: 1.0

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description ##

### Adds an eMail domain check processor to Caldera Forms ###

Check if the domain of the eMail given is most likely capable recieving eMails.
Useful to aviod misusage or mistyped eMails without additional confirmations.

## Installation ##

1. Upload the plugin files to the '/wp-content/plugins' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

## Usage ##

* Install and activate the plugin.
* Define a Caldera Form layout including at least one eMail address field
* Go to the Caldera Form Processors tab and add this processor.
* In Processors Settings add/select the magic eMail tag of your eMail field. Use only one tag!
* In Processors Conditions, enable the processor.

## Frequently Asked Questions ##

### What is the benefit of this processor ###

In a nutshell, minimizing misuse of bogus eMail domains like "asasd.asd" or mistypted eMail domains.
Situation, a customer wants to get in touch via a contact form with an eMail adresss field.
This processor checks locally for DNS records that point to a valid eMail server for the eMail domain given.
With this check, you can minimize bogus domains that eg. have been mistyped or misused and no eMail can be sent/replied to.
A requestor is enforced to check his eMail address without complex measures. Helps minimizing the misuse of the contact form.
It is most likely but not set, that missing DNS records will prevent recieving eMails at the domain given.

### Can this plugin guarantee that the eMail domain is bogus ###

This plugin can only check if the eMail domain is likely to recieve eMails.
By nature how eMail was defined, there is no easy and secure way to find this out.
But the majority of servers do have a proper MX or A or AAAA record that highlights the
possibility that this server can process eMails.

### Can this plugin guarantee eMail delivery ###

If the server can recieve eMails, you still can have the possibility that the recipients name is invalid.

### How is the check made ###

The check is made with php `checkdnsrr` to look if the eMail domain returns either a valid DNS MX or A or AAAA record,
in this sort order.


## Screenshots ##

![Setup](https://github.com/mmattel/caldera-forms-email-check/blob/master/caldera-forms-email-check-setup.png)

![Error](https://github.com/mmattel/caldera-forms-email-check/blob/master/caldera-forms-email-check-error.png)

## Changelog ##

= 1.0 =

*Initial Release
Tested with Caldera Forms 1.8.11

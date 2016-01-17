# Threema MSGAPI WebUI
[![Code Climate](https://codeclimate.com/github/rugk/threema-msgapi-webui/badges/gpa.svg)](https://codeclimate.com/github/rugk/threema-msgapi-webui)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rugk/threema-msgapi-webui/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rugk/threema-msgapi-webui/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c2555cec-c08f-4a64-afbc-0e80eec1407b/mini.png)](https://insight.sensiolabs.com/projects/c2555cec-c08f-4a64-afbc-0e80eec1407b)
[![Codacy Badge](https://api.codacy.com/project/badge/grade/30887d81bbdf419cba8684ca14c63b01)](https://www.codacy.com/app/rugk/threema-msgapi-webui)

This is a sample webui, which uses the [Threema Gateway PHP-SDK](https://github.com/rugk/threema-msgapi-sdk-php).

**Important:** This is only an example and it is mostly only intended as a development tool. Do not use it on productive systems unless you know what you are doing!

## Screenshot
![ThreemaDevUIScreenshot](assets/screenshots/ThreemaDevUIScreenshot.png)

## Requirements

You must use at least PHP 5.4 and it is strongly recommend to install libsodium. For more information, please have a look at the [installation instructions](https://github.com/rugk/threema-msgapi-sdk-php#installation) of the PHP SDK.

Additionally you need to set up a webserver and [composer](https://getcomposer.org/).

## Set up

1. Use composer to fetch and install the dependencies:

   ```Shell
   composer install
   ```

2. Go to this directory (or the `index.php`) and follow the instructions there.  
   In case you do not want to do this just look at the `.example` files and
   replace them with your own credentials.

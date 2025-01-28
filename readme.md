Simple script for web that logs hardware information about user's devices (Browser, OS, probable GPU.)

How to use:

The script is standalone and does not need to be deeply integrated into your code. Wherever your HTML runs (like `index.html`), simply add this line to the <head> of your HTML file:
`<script src="/devicelogger/device-logger.js"></script>`

`git clone` this repository to the root of your web server. The `devicelogger` folder contains:

    device-logger.js: The script that collects and sends device information.
    device-log.php: The script that processes and writes logs.
    device.log: The file where logs are saved.

The `index.html` file and the devicelogger folder should be at the same level in the webserver.
Such as:

```
/index.html
/devicelogger/
    device-logger.js
    device-log.php
    device.log
```

Some examples from where to call the script:

Wordpress:

Within the active theme header file, add this before `</head>` tag.
`<script src="/devicelogger/device-logger.js"></script>`

Moodle:

In "Additional html" setting in site settings of Moodle, to the head add:
`<script src="/devicelogger/device-logger.js"></script>`

Or just add it to `index.html` of the site.


Logs should write to yourdomain.com/devicelogger/device-log.php

You might need to adjust permissions of `device.log` with:
`chmod 666 device.log` if the webserver is user 666.


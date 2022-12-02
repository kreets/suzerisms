# Suzori SMS Sender Laravel Extension

## INSTALLATION

Run the command: `composer require kreets/suzorisms` to download the package into the Laravel platform.

## Config

Change data in /config/suzorisms.php
```php
<?php
return [
    'key' => 'suzoriapikey',
    'project' => 'projectname',
    'sender' => 'sender_id',
    'log' => "logs/sms.txt"
]
```

## USAGE

Send SMS through Suzori SMS Service Provider

```php
SuzoriSms::send("314555666", "message");
```
This will send an SMS to +254 314555666


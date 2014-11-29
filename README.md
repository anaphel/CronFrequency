CronFrequency
=============

Try to make the cron frequency easy to read.  
Example : 30 19 1 * * -> every 1st of the month at 19:30

Tool
-------------------------

You can test it on [this dedicated page](http://www.hatier.me/~louis/cron-frequency/).

How to use it
-------------------------

```php
$cron = new CronFrequency('0 23 * * 0-4');
echo $cron->toHuman();
```
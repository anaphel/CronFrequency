CronFrequency
=============

Try to make the cron frequency easy to read.  
Example : 30 19 1 * * -> every 1st of the month at 19:30

Tool
-------------------------

You can test it on [this dedicated page](http://louis.hatier.me/cron-frequency/).

How to use it
-------------------------

Here is a sample if you receive the data from POST.

```php
$cron = new CronFrequency('0 23 * * 0-4');
echo $cron->toHuman();
```
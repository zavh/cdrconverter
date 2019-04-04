# cdrconverter
A very specific tool to convert CSV based switch CDR [VOS3000] to [Huawei NGN SoftX CDR format of 907 Bytes] Binary
---
This is a combination of 3 modules
---
Module #1. Data Collection Process: Physical files are pushed from remote server,  the first module manages the receiving process.
Since almost the entire software is written in PHP, infinite loop is avoided.
Rather, the periodic collection is kept as CRON based. This particular module is named moirai.php. It is driven by the crontab,
and depending on the existance of remote files in a specific directory, moirai starts the second module [as specified in #2].
The most important part is, at no circumstance a second instance of module #2 can be allowed to run.
This will lead to server resource exhaustion and only a hard boot can save the day. moirai checks if any instance of #2 is running, if it is, execution of #1 is stopped
till the next round of cron and prevents incedents like this. This is tested over time and been in production for years without any unwanted event.

This are the steps taken care of by #1:

--> Search for specific format of file in specific directory. Since data transfer can be expected from out of network or even
over the internet through sftp, the difference between a matured and an immature file is a .tmp extension. Only the matured files 
are detected.

--> If file exists, check if any instance of writebyte.php is running or not. This can be a time consuming process depending on
volume of data passed down by the remote server. Since the timing is done by CRON, running writebyte is controlled by this module,
a second instance will never be spaned if one is running.

---> Logs of this module is written in controller.log [The Moirai are characters from Greek Mythology, were the three goddesses
of fate who personified the inescapable destiny of man., hence the log name]

Module #2. Data Process and Convert module is the core part controlled by module #1. This module knows a few things:

--> Data mapping between two different types of files [VOS CSV and Huawei Binary] The VOS CSV is known by getvoscdr.php 
and Huawei binary format is known by writebyte.php

--> When this module is invoked by moirai, getvoscdr.php reads each csv file that has been received, by VOS3000 CDR definition [kept as cdr.column]
data are kept in mysql, file is deleted when writing to the database is done.

--> getvoscdr.php then invokes writebyte.php. writebyte and the Huawei cdr definition are kept under cdrconverter directory.
The Huawei CDR definition is kept in XML format in huawei.cdr.xml. A specific amount of unconverted data read from mysql are passed to xml.php which converts each row to
binary data by bytes specified by Huawei [byte definitions are kept in constants.php]

This requies that xml modules are installed with php
---

The csv records converted into database rows are read in chunk and files are generated in binary format. Those rows read and converted
are marked as red by raising a database flag field [`processed` in webcdr table].

3. The last module is a bit straight forward and written in perl. I wrote the perl ftp transfer module back in 2012,
since it was working perfectly ok, didn't have to change and convert into php. It just transfers the converted binary files to 
a specific remote location that supports ftp [the perl file does not come with sftp support] It has a logfile of it's own - cdr-to-iof-auto-xfer.log
kept under the directory cdrconverter.


MOST IMPORTANT PLANNING
===
Is the CRON job planning. My cron looks like the following:

*/12 * * * * php /home/billing/voscdr/moirai.php

*/30 * * * * php /home/billing/voscdr/cdrconverter/writebyte.php

10 * * * * perl /home/billing/voscdr/cdrconverter/ftp.pl

Moirai is run every 12 minutes. It has the shortest timespan becuse this handles processing of Call Detail Record [CDR] of real life mobile/fixed telecommunicaiton lines.
A lot of data can be rushed in at any given moment. Moirai invokes getvoscdr within itself to start module #2 processes.

writebyte is called every 30 minutes to do the main conversion. It at most gets the data put into the DB by getvoscdr of two 
cycles which is enough to handle in 30 minutes. The volume of production system we have, writebyte takes nearly a minute to convert
during busiest hours.

Finally, the converted files are send to remote server after 10 minutes of each hour. 

The timings are planned to avoid process overlapping. Though programmatically it has been ensured so that no half cooked or partially
processed files are transferred or generated, this planning is important to best suite the volume of data that have been dealt with.


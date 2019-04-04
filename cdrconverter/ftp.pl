#!/usr/bin/perl
use warnings;
use strict;
use Net::FTP;
use Cwd;
use DateTime;

$|++;    # Autoflush

my @months = qw( 01 02 03 04 05 06 07 08 09 10 11 12 );
my @days = qw(Sun Mon Tue Wed Thu Fri Sat Sun);
my $sec;
my $min;
my $hour;
my $mday;
my $mon;
my $year;
my $wday;
my $yday;
my $isdst;

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
$year = $year+1900;
my $server    = "172.22.21.11";
my $username  = "sigma";
my $pass      = "usr_sigma3!2";
my $ftp;
my $filename;
my $timestamp = DateTime->now(time_zone=>'local');

open (LOGFILE, '>>/home/billing/voscdr/cdrconverter/cdr-to-iof-auto-xfer.log');
print LOGFILE "Reading CDR File Name..\n";

open (DATEFILE, '/home/billing/voscdr/cdrconverter/cdrdate.txt');

while (<DATEFILE>) {
	chomp;
	$filename="$_";
}
close (DATEFILE);

print LOGFILE "Current file name: $filename\n";
print "Connecting to $server..\n";

# Set up connection
$ftp = Net::FTP->new( $server, Passive => 1, Debug => 0 ) or die $@;
print "..authenticating..\n";

# Log in...
$ftp->login( $username, $pass ) or die $ftp->message;
print "..done!\n";

my @transferFileList = glob "voscdr/cdrconverter/convertedcdr/$filename";

print $ftp->pwd (), "\n";

$ftp->binary();

for (@transferFileList){
	$timestamp = DateTime->now(time_zone=>'local');;
	print LOGFILE "$timestamp: ";
	print LOGFILE "Transfering file $_ to BTRC ...\n";
	$ftp->put(cwd . "\/$_") or die $ftp->message;
	print LOGFILE "Done ... Cleaning up $_ from UBUNTU\n";
	unlink $_ or print LOGFILE "Could not unlink $_: $!\n";
}

print LOGFILE "Logging out..\n";
$ftp->quit or die $ftp->message;
print LOGFILE "..done!\n";
close (LOGFILE);

#Writing current date
open (CDRDATEFILE, '> /home/billing/voscdr/cdrconverter/cdrdate.txt');
if($mday<10)
{
	print CDRDATEFILE "b$year$months[$mon]0$mday\*\n";
}
else
{
	print CDRDATEFILE "b$year$months[$mon]$mday\*\n";
}
close (CDRDATEFILE);


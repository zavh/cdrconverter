<?php

  $addr = my_ip();
  echo "my ip address is $addr\n";

  function my_ip($dest='64.0.0.0', $port=80)
  {
    $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    socket_connect($socket, $dest, $port);
    socket_getsockname($socket, $addr, $port);
    socket_close($socket);
    return $addr;
  }
?>

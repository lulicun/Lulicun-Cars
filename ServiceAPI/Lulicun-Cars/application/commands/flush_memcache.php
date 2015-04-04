<?php
    global $memcache;
    $memcache = new Memcached();
    $memcache->addServer('localhost', 11211);
    $memcache->flush();
    echo "\n\n MEMCACHE FLUSHED\n\n";

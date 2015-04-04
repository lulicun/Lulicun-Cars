Exec { path => [ '/bin/', '/sbin/', '/usr/bin/', '/usr/sbin/' ] }

package { 'curl':
    ensure => installed,
    require => Package['php5']
}

package { 'memcached':
    ensure => installed
}

package { 'redis-server':
    ensure => installed
}

class { 'apache':
    mpm_module => 'prefork',
    default_vhost => false
}

include apache::mod::php
apache::mod { 'rewrite': }
apache::vhost { 'local-car.lulicun.com':
    servername => 'local-car.lulicun.com',
    port => '80',
    docroot => '/var/www/Lulicun-Cars/public',
    docroot_owner => 'vagrant',
    docroot_group => 'vagrant',
    directories => [{
        path => '/var/www/Lulicun-Cars/public',
        allow => 'from all',
        allow_override => ['All']
    }]
}

class {'::mongodb::globals':
    manage_package_repo => true
}->
class {'::mongodb::server':
    bind_ip => ['0.0.0.0']
}->
class {'::mongodb::client': }

include php
class php {
    
    package { 'php5':
        ensure => installed
    }

    package { 'php5-dev':
        require => Package['php5'],
        ensure => installed
    }

    package { 'php-pear':
        require => Package['php5-dev'],
        ensure => installed,
    }

    package {'libpcre3-dev':
        require => Package['php5-dev'],
        ensure => installed
    }

    package { 'php5-xdebug':
        require => Package['php5'],
        ensure => installed
    }

    file { '/etc/php5/mods-available/xdebug.ini':
        ensure => present,
        mode => 0644,
        owner => 'root',
        group => 'root',
        source => '/vagrant/puppet/files/xdebug.ini',
        require => Package['php5-xdebug']
    }

    package { 'php5-memcached':
        require => Package['php5'],
        ensure => installed
    }

    package { 'php5-cli':
        require => Package['php5'],
        ensure => installed
    }

    package { 'php5-curl':
        require => Package['php5'],
        ensure => installed
    }

    package { 'php-apc':
        require => Package['php5'],
        ensure => installed
    }

    package { 'php5-gd':
        require => Package['php5'],
        ensure => installed
    }

    package { 'php5-mcrypt':
        require => Package['php5'],
        ensure => installed
    }

    exec { 'pear-update-channels':
        command => 'pear update channels',
        require => Package['php-pear'],
        unless => 'pecl info mongo',
    }

    exec { 'pecl-install-mongo':
        command => 'pecl install mongo',
        require => Exec['pear-update-channels'],
        unless => 'pecl info mongo',
    }

    file { '/etc/php5/mods-available/mongo.ini':
        ensure => present,
        mode => 0644,
        content => 'extension=mongo.so',
        require => Exec['pecl-install-mongo']
    }

    exec { 'enable mongo apache':
        command => 'ln -s /etc/php5/mods-available/mongo.ini /etc/php5/apache2/conf.d/mongo.ini',
        require => File['/etc/php5/mods-available/mongo.ini'],
        unless => 'ls /etc/php5/apache2/conf.d/mongo.ini'
    }

    exec { 'enable mongo cli':
        command => 'ln -s /etc/php5/mods-available/mongo.ini /etc/php5/cli/conf.d/mongo.ini',
        require => File['/etc/php5/mods-available/mongo.ini'],
        unless => 'ls /etc/php5/apache2/conf.d/mongo.ini'
    }

    exec { 'pecl-install-redis':
        command => 'pecl install redis',
        require => Exec['pear-update-channels'],
        unless => 'pecl info redis',
    }

    exec { 'pecl-install-oauth':
        command => 'pecl install oauth',
        require => Package['libpcre3-dev'],
        unless => 'pecl info oauth',
    }

    file { '/etc/php5/apache2/conf.d/oauth.ini':
        ensure => present,
        mode => 0644,
        content => 'extension=/usr/lib/php5/20121212/oauth.so',
        require => Exec['pecl-install-oauth']
    }

    file { '/etc/php5/cli/conf.d/oauth.ini':
        ensure => present,
        mode => 0644,
        content => 'extension=/usr/lib/php5/20121212/oauth.so',
        require => Exec['pecl-install-oauth']
    }

    exec { 'install composer':
        command => 'curl -sS https://getcomposer.org/installer | php -d xdebug.remote_enable=0',
        require => Package['curl'],
        unless => 'ls composer.phar'
    }

    exec { 'install php libraries':
        command => 'php -d xdebug.remote_enable=0 composer.phar install --optimize-autoloader -d /var/www/Lulicun-Cars',
        require => Exec['install composer'],
        environment => [ "COMPOSER_HOME=/home/vagrant" ],
    }
}

file { '/home/vagrant/.bash_profile':
    ensure => present,
    mode => 0644,
    owner => 'vagrant',
    group => 'vagrant',
    source => '/vagrant/puppet/files/dot_bash_profile'
}

exec { 'readable logs':
    command => 'chmod -R 755 /var/log/apache2'
}

exec { 'update locate db':
    command => 'updatedb',
}

file { '/var/log/queue.log':
    ensure => present,
    mode => 0777,
    require => Exec['install php libraries']
}

file { '/etc/init.d/queue':
    ensure => present,
    mode => 0755,
    source => '/vagrant/puppet/files/init.d/queue_local',
    require => File['/var/log/queue.log']
}

exec { 'update-rc.d -f queue defaults':
    command => 'update-rc.d -f queue defaults',
    require => File['/etc/init.d/queue']
}

exec {'/etc/init.d/queue start':
    command => '/etc/init.d/queue start',
    require => Exec['update-rc.d -f queue defaults']
}
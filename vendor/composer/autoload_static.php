<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc72f81bfc9660059a53e27d6ebff8d4d
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Box\\Spout\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Box\\Spout\\' => 
        array (
            0 => __DIR__ . '/..' . '/box/spout/src/Spout',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc72f81bfc9660059a53e27d6ebff8d4d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc72f81bfc9660059a53e27d6ebff8d4d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc72f81bfc9660059a53e27d6ebff8d4d::$classMap;

        }, null, ClassLoader::class);
    }
}

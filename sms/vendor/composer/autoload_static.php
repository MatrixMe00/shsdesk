<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit671cc71a1e48117e815ee754ffd04cd2
{
    public static $prefixLengthsPsr4 = array (
        'U' => 
        array (
            'Urhitech\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Urhitech\\' => 
        array (
            0 => __DIR__ . '/..' . '/urhitech/urhitech-sms-php/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit671cc71a1e48117e815ee754ffd04cd2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit671cc71a1e48117e815ee754ffd04cd2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit671cc71a1e48117e815ee754ffd04cd2::$classMap;

        }, null, ClassLoader::class);
    }
}
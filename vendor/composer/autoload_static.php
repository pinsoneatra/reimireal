<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfc0971344025c17ec28503440be1ef23
{
    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'models\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/models',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfc0971344025c17ec28503440be1ef23::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfc0971344025c17ec28503440be1ef23::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
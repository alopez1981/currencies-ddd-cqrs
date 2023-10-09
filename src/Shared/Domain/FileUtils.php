<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain;

use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\map;

final class FileUtils
{
    public static function classesThatImplements(string $interface, string ...$dirs): array
    {
        if (empty($dirs)) {
            /** @var string[] $dirs */
            $dirs = config('shared.bus.scan_dirs');
        }

        return ArrUtils::flatten(map(
            function (Finder $finder) {
                return map(fn(SplFileInfo $file) => Utils::fullNamespace($file->getPathname()), $finder->files());
            },
            ArrUtils::flatten(map(function (string $dir) use ($interface) {
                try {
                    $finder = (new Finder())->in($dir);
                } catch (DirectoryNotFoundException) {
                    return [];
                }

                return $finder->files()->name('*.php')->filter(function (SplFileInfo $file) use ($interface) {
                    $classNamespace = Utils::fullNamespace($file->getPathname());

                    if (null === $classNamespace) {
                        return false;
                    }

                    $class = new ReflectionClass($classNamespace);

                    return $class->implementsInterface($interface) && $classNamespace !== $interface;
                });
            }, $dirs)),
        ));
    }

    public static function classesOfInstance(string $instance, string ...$dirs): array
    {
        if (empty($dirs)) {
            /** @var string[] $dirs */
            $dirs = config('shared.bus.scan_dirs');
        }

        return ArrUtils::flatten(map(
            function (Finder $finder) {
                return map(fn(SplFileInfo $file) => Utils::fullNamespace($file->getPathname()), $finder->files());
            },
            ArrUtils::flatten(map(function (string $dir) use ($instance) {
                try {
                    $finder = (new Finder())->in($dir);
                } catch (DirectoryNotFoundException) {
                    return [];
                }

                return $finder->files()->name('*.php')->filter(function (SplFileInfo $file) use ($instance) {
                    $classNamespace = Utils::fullNamespace($file->getPathname());

                    if (null === $classNamespace) {
                        return false;
                    }

                    $class = new ReflectionClass($classNamespace);

                    return $class->isSubclassOf($instance) && $classNamespace !== $instance;
                });
            }, $dirs)),
        ));
    }

    public static function filesIn(string $path, string $fileType): array
    {
        $paths = scandir($path);

        if (!is_array($paths)) {
            return [];
        }

        return filter(
            static fn(string $possibleModule) => strstr($possibleModule, $fileType),
            $paths
        );
    }
}

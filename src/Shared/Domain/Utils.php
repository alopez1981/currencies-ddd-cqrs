<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain;

use Closure;
use DeepCopy\DeepCopy;
use ReflectionClass;
use RuntimeException;

final class Utils
{
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function dataGet(mixed $target, string|int|array|null $key, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $target;
        }

        if (!is_array($key)) {
            $key = is_int($key) ? [$key] : explode('.', $key);
        }

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $target;
            }

            if ($segment === '*') {
                if (!is_iterable($target)) {
                    return self::value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = self::dataGet($item, $key);
                }

                return in_array('*', $key) ? ArrUtils::collapse($result) : $result;
            }

            if (ArrUtils::accessible($target) && ArrUtils::exists($target, $segment)) {
                $target = $target[$segment];
                continue;
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
                continue;
            } elseif (is_object($target) && method_exists($target, $segment)) {
                $target = $target->{$segment}();
                continue;
            }

            return value($default);
        }

        return $target;
    }

    /**
     * Return the default value of the given value.
     */
    public static function value(mixed $value, mixed ...$args): mixed
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }

    public static function jsonEncode(array $values): string
    {
        $json = json_encode($values);

        if (false === $json) {
            throw new RuntimeException('Unable to encode array of values.');
        }

        return $json;
    }

    public static function jsonDecode(string $json): array
    {
        $data = json_decode($json, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException('Unable to parse response body into JSON: ' . json_last_error());
        }

        /** @var array $data */

        return $data;
    }

    public static function extractClassName(object $object): string
    {
        $reflect = new ReflectionClass($object);

        return $reflect->getShortName();
    }

    /**
     * @return class-string|null
     */
    public static function fullNamespace(string $filename): ?string
    {
        $namespace = self::classNamespace($filename);

        if ($namespace === null) {
            return null;
        }

        /** @var class-string $fullNamespace */
        $fullNamespace = $namespace . '\\' . self::className($filename);

        return $fullNamespace;
    }

    public static function classNamespace(string $filename): ?string
    {
        $lines = file($filename);
        if (false === $lines) {
            return null;
        }

        $namespaceLine = preg_grep('/^namespace /', $lines);
        if (false === $namespaceLine) {
            return null;
        }

        $namespaceLine = array_shift($namespaceLine);
        if (null === $namespaceLine) {
            return null;
        }

        $match = [];
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);

        return array_pop($match);
    }

    public static function className(string $filename): string
    {
        $directoriesAndFilename = explode('/', $filename);
        $filename               = array_pop($directoriesAndFilename);
        $nameAndExtension       = explode('.', $filename);

        return array_shift($nameAndExtension);
    }

    /** @SuppressWarnings(PHPMD.BooleanArgumentFlag) */
    public static function deepCopy(mixed $object, bool $useCloneMethod = false): mixed
    {
        $copier = new DeepCopy($useCloneMethod);

        return $copier->copy($object);
    }

    public static function castToString(mixed $value): string
    {
        /** @phpstan-ignore-next-line */
        return strval($value);
    }

    public static function castToFloat(mixed $value): float
    {
        /** @phpstan-ignore-next-line */
        return floatval($value);
    }

    public static function castToInt(mixed $value): int
    {
        /** @phpstan-ignore-next-line */
        return intval($value);
    }
}

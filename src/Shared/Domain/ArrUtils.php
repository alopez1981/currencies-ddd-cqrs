<?php

declare(strict_types=1);

namespace Hoyvoy\Shared\Domain;

use ArrayAccess;
use InvalidArgumentException;
use RuntimeException;
use stdClass;
use Stringable;
use Traversable;
use function Lambdish\Phunctional\all;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
final class ArrUtils
{
    /**
     * Map into new array the callback return.
     */
    public static function map(callable $callback, array $array): array
    {
        $keys = array_keys($array);

        $items = array_map($callback, $array, $keys);

        return array_combine($keys, $items);
    }

    /**
     * Returns an array mapping keys and values.
     */
    public static function mapWithKeys(callable $callback, array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }

    /**
     * Run a dictionary map over the items.
     *
     * The callback should return an associative array with a single key/value pair.
     */
    public static function mapToDictionary(callable $callback, array $array): array
    {
        $dictionary = [];

        foreach ($array as $key => $item) {
            $pair = $callback($item, $key);

            $key = key($pair);

            $value = reset($pair);

            if (!isset($dictionary[$key])) {
                $dictionary[$key] = [];
            }

            $dictionary[$key][] = $value;
        }

        return $dictionary;
    }

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     */
    public static function add(array $array, string $key, mixed $value): array
    {
        if (is_null(self::get($array, $key))) {
            self::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Get an item from an array using "dot" notation.
     */
    public static function get(array $array, int|string $key, mixed $default = null): mixed
    {
        if (self::exists($array, $key)) {
            return $array[$key];
        }

        if (is_int($key) || !str_contains($key, '.')) {
            return $array[$key] ?? Utils::value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (self::exists($array, $segment)) {
                $array = $array[$segment];
                continue;
            }

            return Utils::value($default);
        }

        return $array;
    }

    /**
     * Determine whether the given value is array accessible.
     */
    public static function accessible(mixed $value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     */
    public static function exists(array $array, int|string $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     */
    public static function set(array &$array, string $key, mixed $value): array
    {
        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Collapse an array of arrays into a single array.
     */
    public static function collapse(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            if (!is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    /**
     * Determine if an item exists in the array.
     */
    public static function contains(
        array $array,
        string|callable $key,
        string $operator = null,
        mixed $value = null
    ): bool {
        if (func_num_args() === 2) {
            if (self::useAsCallable($key)) {
                /** @var callable $key */
                $placeholder = new stdClass();

                return self::first($array, $key, $placeholder) !== $placeholder;
            }

            /** @var string $key */
            return in_array($key, $array);
        }

        /** @var string $key */
        return self::contains($array, self::operatorForWhere($key, $operator, $value));
    }

    /**
     * Cross join the given arrays, returning all possible permutations.
     */
    public static function crossJoin(array ...$arrays): array
    {
        $results = [[]];

        foreach ($arrays as $index => $array) {
            $append = [];

            foreach ($results as $product) {
                foreach ($array as $item) {
                    $product[$index] = $item;

                    $append[] = $product;
                }
            }

            $results = $append;
        }

        return $results;
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     */
    public static function divide(array $array): array
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     */
    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, self::dot($value, $prepend . $key . '.'));
                continue;
            }

            $results[$prepend . $key] = $value;
        }

        return $results;
    }

    /**
     * Get all of the given array except for a specified array of keys.
     */
    public static function except(array $array, string ...$keys): array
    {
        self::forget($array, ...$keys);

        return $array;
    }

    /**
     * Get all of the given array except for a specified array of values.
     */
    public static function exceptValues(array $array, string ...$values): array
    {
        return self::where(function ($value) use ($values) {
            return !in_array($value, $values);
        }, $array);
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     */
    public static function forget(array &$array, int|string ...$keys): void
    {
        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            if ($key === '*') {
                $array = [];
                break;
            }

            // if the exact key exists in the top-level, remove it
            if (is_int($key) || self::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts     = explode('.', $key);
            $firstPart = array_shift($parts);
            $key       = implode('.', $parts);

            if (count($parts) === 0) {
                unset($array[$firstPart]);
                continue;
            }

            if ($firstPart !== '*') {
                self::forget($array[$firstPart], $key);
                continue;
            }

            foreach ($array as &$value) {
                self::forget($value, $key);
            }
        }
    }

    /**
     * Remove one or many array items from a given array of values.
     */
    public static function forgetValue(array &$array, mixed ...$values): void
    {
        foreach ($values as $value) {
            $key = array_search($value, $array);

            if ($key !== false) {
                self::forget($array, $key);
            }
        }
    }

    /**
     * Return the last element in an array passing a given truth test.
     */
    public static function last(array $array, callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            return empty($array) ? Utils::value($default) : end($array);
        }

        return self::first(array_reverse($array, true), $callback, $default);
    }

    /**
     * Return the first element in an array passing a given truth test.
     */
    public static function first(array $array, callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return Utils::value($default);
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return Utils::value($default);
    }

    /**
     * Return the first key element in an array passing a given truth test.
     */
    public static function firstKey(array $array, callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return Utils::value($default);
            }

            return array_key_first($array);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }

        return Utils::value($default);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     */
    public static function flatten(array $array, mixed $depth = INF): array
    {
        $result = [];

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
                continue;
            }

            $values = $depth === 1
                ? array_values($item)
                : self::flatten($item, $depth - 1);

            foreach ($values as $value) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Determine if any of the keys exist in an array using "dot" notation.
     */
    public static function hasAny(array $array, string ...$keys): bool
    {
        if (!$array) {
            return false;
        }

        if ($keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            if (self::has($array, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     */
    public static function has(array $array, int|string ...$keys): bool
    {
        if (empty($keys)) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (self::exists($array, $key)) {
                continue;
            }

            $segments = is_int($key) ? [$key] : explode('.', $key);

            do {
                /** @var string|int $segment */
                $segment       = array_shift($segments);
                $countSegments = count($segments);
                $subKeyArray   = Utils::dataGet($subKeyArray, $segment);

                if (null === $subKeyArray) {
                    return false;
                } elseif ($segment !== '*') {
                    continue;
                }

                /** @var array $subKeyArray */
                foreach ($subKeyArray as $subKeyArrayItem) {
                    if (false === self::has($subKeyArrayItem, implode('.', $segments))) {
                        return false;
                    }
                }

                return true;
            } while ($countSegments > 0);
        }

        return true;
    }

    /**
     * Return if all values are present in the given array.
     */
    public static function hasValue(array $array, mixed ...$values): bool
    {
        $return = true;
        foreach ($values as $value) {
            $key = array_search($value, $array);

            if ($key === false) {
                $return = false;
                break;
            }
        }

        return $return;
    }

    /**
     * Return if one of the values are present in the given array.
     */
    public static function hasAnyValue(array $array, mixed ...$values): bool
    {
        foreach ($values as $value) {
            $key = array_search($value, $array);

            if ($key !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a subset of the items from the given array.
     */
    public static function only(array $array, string ...$keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Get a subset of the items from the given array that match value.
     */
    public static function onlyValues(array $array, string ...$values): array
    {
        return array_intersect($array, $values);
    }

    /**
     * Pluck an array of values from an array.
     */
    public static function pluck(array $array, callable|string $value, string $key = null): array
    {
        $results = [];

        if (!is_callable($value)) {
            [$value, $key] = self::explodePluckParameters($value, $key);
        }

        foreach ($array as $arrayKey => $arrayValue) {
            if (!is_string($value) && is_callable($value)) {
                $results[] = $value($arrayValue, $arrayKey);
                continue;
            }

            $itemValue = Utils::dataGet($arrayValue, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
                continue;
            }

            $itemKey = Utils::dataGet($arrayValue, $key);

            if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                $itemKey = (string) $itemKey;
            }

            $results[$itemKey] = $itemValue;
        }

        return $results;
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     */
    protected static function explodePluckParameters(string $value, ?string $key): array
    {
        $value = explode('.', $value);

        $key = null === $key ? $key : explode('.', $key);

        return [$value, $key];
    }

    /**
     * Push an item onto the beginning of an array.
     */
    public static function prepend(array $array, mixed $value, string $key = null): array
    {
        if (func_num_args() == 2) {
            array_unshift($array, $value);

            return $array;
        }

        $array = [$key => $value] + $array;

        return $array;
    }

    /**
     * Get a value from the array, and remove it.
     */
    public static function pull(array &$array, string $key, mixed $default = null): mixed
    {
        $value = self::get($array, $key, $default);

        self::forget($array, $key);

        return $value;
    }

    /**
     * Get one or a specified number of random values from an array.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function random(array $array, int $number = null, bool $preserveKeys = false): mixed
    {
        $requested = $number ?? 1;

        $count = count($array);

        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }

        if (is_null($number)) {
            return $array[array_rand($array)];
        }

        if ((int) $number === 0) {
            return [];
        }

        $keys = array_rand($array, $number);

        $results = [];

        if ($preserveKeys) {
            foreach ((array) $keys as $key) {
                $results[$key] = $array[$key];
            }

            return $results;
        }

        foreach ((array) $keys as $key) {
            $results[] = $array[$key];
        }

        return $results;
    }

    /**
     * Shuffle the given array and return the result.
     */
    public static function shuffle(array $array, int $seed = null): array
    {
        if (null === $seed) {
            shuffle($array);

            return $array;
        }

        mt_srand($seed);
        shuffle($array);
        mt_srand();

        return $array;
    }

    /**
     * Sort through each item with a callback.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function sort(array $array, callable|int $callback = null, bool $preserveKeys = false): array
    {
        if ($preserveKeys) {
            $callback && is_callable($callback)
                ? uasort($array, $callback)
                : asort($array, $callback ?? SORT_REGULAR);

            return $array;
        }

        $callback && is_callable($callback)
            ? usort($array, $callback)
            : sort($array, $callback ?? SORT_REGULAR);

        return $array;
    }

    /**
     * Sort through each item with a callback in descending order.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function sortDesc(array $array, callable|int $callback = null, bool $preserveKeys = false): array
    {
        $array = self::sort($array, $callback, $preserveKeys);

        return array_reverse($array, $preserveKeys);
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function sortRecursive(array $array, int $options = SORT_REGULAR, bool $descending = false): array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = self::sortRecursive($value, $options, $descending);
            }
        }

        if (self::isAssoc($array)) {
            $descending
                ? krsort($array, $options)
                : ksort($array, $options);

            return $array;
        }

        $descending
            ? rsort($array, $options)
            : sort($array, $options);

        return $array;
    }

    /**
     * Sort the collection using multiple comparisons.
     */
    protected static function sortByMany(array $array, array $comparisons = []): array
    {
        usort($array, function ($valueA, $valueB) use ($comparisons) {
            foreach ($comparisons as $comparison) {
                $comparison = self::wrap($comparison);

                $prop = $comparison[0];

                $ascending = self::get($comparison, '1', true) === true ||
                    self::get($comparison, '1', true) === 'asc';

                if (!is_string($prop) && is_callable($prop)) {
                    return $prop($valueA, $valueB);
                }

                $values = [Utils::dataGet($valueA, $prop), Utils::dataGet($valueB, $prop)];

                if (!$ascending) {
                    $values = array_reverse($values);
                }

                $result = $values[0] <=> $values[1];

                if ($result === 0) {
                    continue;
                }

                return $result;
            }
        });

        return $array;
    }

    /**
     * Sort the collection using the given callback.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function sortBy(
        array $array,
        array|string|callable $callback,
        int $options = SORT_REGULAR,
        bool $descending = false
    ): array {
        if (is_array($callback) && !is_callable($callback)) {
            return self::sortByMany($array, $callback);
        }

        $results = [];

        $callback = self::valueRetriever($callback);

        // First we will loop through the items and get the comparator from a callback
        // function which we were given. Then, we will sort the returned values and
        // grab all the corresponding values for the sorted keys from this array.
        foreach ($array as $key => $value) {
            $results[$key] = $callback($value, $key);
        }

        $descending ? arsort($results, $options)
            : asort($results, $options);

        // Once we have sorted all of the keys in the array, we will loop through them
        // and grab the corresponding model so we can set the underlying items list
        // to the sorted version. Then we'll just return the collection instance.
        foreach (array_keys($results) as $key) {
            $results[$key] = $array[$key];
        }

        return $results;
    }

    /**
     * Sort the collection in descending order using the given callback.
     */
    public static function sortByDesc(array $array, array|string|callable $callback, int $options = SORT_REGULAR): array
    {
        return self::sortBy($array, $callback, $options, true);
    }

    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Convert the array into a query string.
     */
    public static function query(array $array): string
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Filter the array using the given callback.
     */
    public static function where(callable $callback, array $array): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * If the given value is not an array and not null, wrap it in one.
     */
    public static function wrap(mixed $value): array
    {
        if (null === $value) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    /**
     * Get the min value of a given key.
     */
    public static function min(callable|string|null $callback, array $array): mixed
    {
        $callback = self::valueRetriever($callback);

        $array = self::map(function ($value) use ($callback) {
            return $callback($value);
        }, $array);

        $array = self::where(function ($value) {
            return !is_null($value);
        }, $array);

        return self::reduce(function ($result, $value) {
            return is_null($result) || $value < $result ? $value : $result;
        }, $array);
    }

    /**
     * Get the max value of a given key.
     */
    public static function max(callable|string|null $callback, array $array): mixed
    {
        $callback = self::valueRetriever($callback);

        $array = self::where(function ($value) {
            return !is_null($value);
        }, $array);

        return self::reduce(function ($result, $value) use ($callback) {
            $value = $callback($value);

            return is_null($result) || $value > $result ? $value : $result;
        }, $array);
    }

    /**
     * Get the sum of the given values.
     */
    public static function sum(callable|string|null $callback, array $array): float|int
    {
        $callback = is_null($callback)
            ? self::identity()
            : self::valueRetriever($callback);

        /** @var float|int $reduce */
        $reduce = self::reduce(function ($result, $item) use ($callback) {
            return $result + $callback($item);
        }, $array, 0);

        return $reduce;
    }

    /**
     * Get a value retrieving callback.
     */
    protected static function valueRetriever(callable|array|string|null $value): callable
    {
        if (self::useAsCallable($value)) {
            /** @var callable $value * */
            return $value;
        }

        /** @var string|null $value * */
        return function ($item) use ($value) {
            return Utils::dataGet($item, $value);
        };
    }

    /**
     * Determine if the given value is callable, but not a string.
     */
    protected static function useAsCallable(mixed $value): bool
    {
        return !is_string($value) && is_callable($value);
    }

    /**
     * Get an operator checker callback.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected static function operatorForWhere(string $key, string $operator = null, mixed $value = null): callable
    {
        if (func_num_args() === 1) {
            $value = true;

            $operator = '=';
        }

        if (func_num_args() === 2) {
            $value = $operator;

            $operator = '=';
        }

        return function ($item) use ($key, $operator, $value) {
            $retrieved = Utils::dataGet($item, $key);

            $strings = array_filter([$retrieved, $value], function ($value) {
                return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
            });

            if (count($strings) < 2 && count(array_filter([$retrieved, $value], 'is_object')) == 1) {
                return in_array($operator, ['!=', '<>', '!==']);
            }

            switch ($operator) {
                default:
                case '=':
                case '==':
                    return $retrieved == $value;
                case '!=':
                case '<>':
                    return $retrieved != $value;
                case '<':
                    return $retrieved < $value;
                case '>':
                    return $retrieved > $value;
                case '<=':
                    return $retrieved <= $value;
                case '>=':
                    return $retrieved >= $value;
                case '===':
                    return $retrieved === $value;
                case '!==':
                    return $retrieved !== $value;
            }
        };
    }

    /**
     * Reduce the collection to a single value.
     */
    public static function reduce(callable $callback, array $array, mixed $initial = null): mixed
    {
        $result = $initial;

        foreach ($array as $key => $value) {
            $result = $callback($result, $value, $key);
        }

        return $result;
    }

    /**
     * Make a function that returns what's passed to it.
     */
    protected static function identity(): callable
    {
        return function ($value) {
            return $value;
        };
    }

    /**
     * Key an associative array by a field or using a callback.
     */
    public static function keyBy(array $array, callable|string $keyBy): array
    {
        $keyBy = self::valueRetriever($keyBy);

        $results = [];

        foreach ($array as $key => $item) {
            $resolvedKey = $keyBy($item, $key);

            if (is_object($resolvedKey)) {
                if (!$resolvedKey instanceof Stringable) {
                    throw new RuntimeException('Can not resolve object key, method __toString does not exists');
                }

                $resolvedKey = $resolvedKey->__toString();
            }

            $results[$resolvedKey] = $item;
        }

        return $results;
    }

    /**
     * Group an associative array by a field or using a callback.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function groupBy(array $array, array|callable|string $groupBy, bool $preserveKeys = false): array
    {
        if (!self::useAsCallable($groupBy) && is_array($groupBy)) {
            $nextGroups = $groupBy;

            $groupBy = array_shift($nextGroups);
        }

        $groupBy = self::valueRetriever($groupBy);

        $results = [];

        foreach ($array as $key => $value) {
            $groupKeys = $groupBy($value, $key);

            if (!is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }

            foreach ($groupKeys as $groupKey) {
                if (!is_string($groupKey) && !is_integer($groupKey)) {
                    settype($groupKey, 'string');
                }

                if (!array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = [];
                }

                if (true === $preserveKeys) {
                    $results[$groupKey][$key] = $value;
                    continue;
                }

                $results[$groupKey][] = $value;
            }
        }

        if (!empty($nextGroups)) {
            return self::map(function (array $item) use ($nextGroups, $preserveKeys) {
                return self::groupBy($item, $nextGroups, $preserveKeys);
            }, $results);
        }

        return $results;
    }

    /**
     * Remove duplicated elements from an array
     */
    public static function unique(array $array): array
    {
        return array_values(array_map("unserialize", array_unique(array_map("serialize", $array))));
    }

    /**
     * Convert iterable to array
     */
    public static function iteratorToArray(iterable $array): array
    {
        if ($array instanceof Traversable) {
            return iterator_to_array($array);
        }

        return (array) $array;
    }

    public static function i18n(array $array, string $language, string $fallbackLanguage = 'es'): mixed
    {
        if (ArrUtils::has($array, $language)) {
            return ArrUtils::get($array, $language);
        }

        if (ArrUtils::has($array, $fallbackLanguage)) {
            return ArrUtils::get($array, $fallbackLanguage);
        }

        return ArrUtils::first($array);
    }

    public static function oddToKeys(array $array): array
    {
        $array = array_values($array);

        return array_combine(
            array_filter($array, function ($key) {
                return $key % 2 == 0;
            }, ARRAY_FILTER_USE_KEY),
            array_filter($array, function ($key) {
                return $key % 2 != 0;
            }, ARRAY_FILTER_USE_KEY)
        );
    }

    public static function setArrayCounter(
        array $array,
        string $counterKey,
        array $matching,
        int|float $setValue,
    ): array {
        $exists = false;

        foreach ($array as &$arrayElement) {
            $match = all(function(mixed $value, string|int $index) use ($arrayElement) {
                return $arrayElement[$index] === $value;
            }, $matching);

            if (false === $match) {
                continue;
            }

            $exists = true;

            $arrayElement[$counterKey] = $setValue;
        }

        if (false === $exists) {
            $array[] = array_merge([
                $counterKey => $setValue,
            ], $matching);

            return $array;
        }

        return $array;
    }

    public static function incrementArrayCounter(
        array $array,
        string $counterKey,
        array $matching,
        int|float $increment = 1,
    ): array {
        $exists = false;

        foreach ($array as &$arrayElement) {
            $match = all(function(mixed $value, string|int $index) use ($arrayElement) {
                return $arrayElement[$index] === $value;
            }, $matching);

            if (false === $match) {
                continue;
            }

            $exists = true;

            $arrayElement[$counterKey] += $increment;
        }

        if (false === $exists) {
            $array[] = array_merge([
                $counterKey => $increment,
            ], $matching);

            return $array;
        }

        return $array;
    }

    public static function decrementArrayCounter(
        array $array,
        string $counterKey,
        array $matching,
        int|float $decrement = 1,
    ): array {
        $exists = false;

        foreach ($array as &$arrayElement) {
            $match = all(function(mixed $value, string|int $index) use ($arrayElement) {
                return $arrayElement[$index] === $value;
            }, $matching);

            if (false === $match) {
                continue;
            }

            $exists = true;

            $arrayElement[$counterKey] -= $decrement;
        }

        if (false === $exists) {
            $array[] = array_merge([
                $counterKey => -1 * $decrement,
            ], $matching);

            return $array;
        }

        return $array;
    }

    public static function fillArrayCounter(
        array $array,
        string $counterKey,
        string $key,
        string ...$values,
    ): array {
        return ArrUtils::map(function (string $value) use ($array, $counterKey, $key) {
            $indexKey = array_search($value, array_column($array, $key));

            if (false === $indexKey) {
                return [
                    $key        => $value,
                    $counterKey => 0,
                ];
            }

            return $array[$indexKey];
        }, $values);
    }
}

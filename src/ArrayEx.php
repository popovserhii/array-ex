<?php
/**
 * The wrapper for specific PHP array functions
 *
 * @category Agere
 * @package Agere_ArrayEx
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 13.01.2016 20:33
 */
namespace Agere\ArrayEx;

class ArrayEx {

	private $array;

	public function __construct(array $array = []) {
		$this->array = $array;
	}

	/**
	 * Create array object
	 *
	 * @param array $array
	 * @return ArrayEx
	 */
	static public function create(array $array = []) {
		return new self($array);
	}

	public function getArray() {
		return $this->array;
	}

	/**
	 * Multidimensional array check
	 *
	 * @param mixed $value
	 * @param string $key Could be string name
	 * @return bool
	 */
	public function in($value, $key = '') {
		return count(array_filter($this->array, function ($var) use ($key, $value) {
			if (!$key) {
				return in_array($value, $var);
			} elseif (isset($var[$key])) {
				return $var[$key] === $value;
			}

			return false;
		})) !== 0;
	}

	/**
	 * Insert element at specific Index of Array
	 *
	 * @param int|string $position
	 * @param mixed $insert
	 * @return array
	 * @link http://stackoverflow.com/a/18781630/1335142
	 */
	public function insert($position, $insert) {
		if (is_int($position)) {
			array_splice($this->array, $position, 0, $insert);
		} else {
			$pos = array_search($position, array_keys($this->array));
			$this->array = array_merge(array_slice($this->array, 0, $pos), $insert, array_slice($this->array, $pos));
		}

		return $this->array;
	}

	/**
	 * @param array $a
	 * @param array $b
	 *
	 * @return array
	 * @link http://stackoverflow.com/a/15296892
	 */
	public function merge(&$a, $b) {
		foreach ($b as $k => $v) {
			if (is_array($v)) {
				if (isset($a[$k])) {
					if ($this->isDeep($v)) {
						$a[$k] = $this->merge($a[$k], $v);
					} else {
						$a[$k] = array_merge($a[$k], $v);
					}
				} else {
					$a[$k] = $v;
				}
			} else {
				$a[$k] = $v;
			}
		}

		//return $a;
	}

	/**
	 * @param array $array
	 *
	 * @return bool
	 */
	public function isDeep($array) {
		foreach ($array as $elm) {
			if (is_array($elm)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get as close as possible the number of key
	 *
	 * @param $number
	 * @param string $get min | equally | max
	 * @return array|mixed
	 * @throws \Exception
	 * @link http://phpforum.ru/import.phtml?showtopic=62484&hl=&st=0#entry1902334
	 */
	public function findMinMaxKey($number, $get = '') {
		$mass = $this->getArray();
		asort($mass);
		$result = array(
			'min'     => array(),
			'max'     => array(),
			'equally' => array(),
		);
		foreach ($mass as $massKey => $massValue) {
			if ($massValue < $number) {
				$result['min'][] = $massKey;
			}
			if ($massValue == $number) {
				$result['equally'][] = $massKey;
			}
			if ($massValue > $number) {
				$result['max'][] = $massKey;
			}
		}
		if ($get) {
			if ($get == 'equally') {
				$result = array_shift($result['equally']);
			} elseif ($get == 'max') {
				$result = array_shift($result['max']);
			} elseif ($get == 'min') {
				$result = array_pop($result['min']);
			} else {
				throw new \Exception('Not found specifier "' . $get . '"');
			}
		}

		return $result;
	}

	/**
	 * Sort a 2 dimensional array based on 1 or more indexes.
	 *
	 * msort() can be used to sort a rowset like array on one or more
	 * 'headers' (keys in the 2th array).
	 *
	 * @param string|array $key The index(es) to sort the array on.
	 * @param int $sort_flags The optional parameter to modify the sorting
	 *                                 behavior. This parameter does not work when
	 *                                 supplying an array in the $key parameter.
	 *
	 * @return array The sorted array.
	 */
	function mSort($key, $sort_flags = SORT_REGULAR) {
		if (is_array($this->array) && $this->array) {
			if (!empty($key)) {
				$mapping = array();
				foreach ($this->array as $k => $v) {
					$sortKey = '';
					if (!is_array($key)) {
						$sortKey = $v[$key];
					} else {
						// @TODO This should be fixed, now it will be sorted as string
						foreach ($key as $keyKey) {
							$sortKey .= $v[$keyKey];
						}
						$sort_flags = SORT_STRING;
					}
					$mapping[$k] = $sortKey;
				}
				asort($mapping, $sort_flags);
				$sorted = array();
				foreach ($mapping as $k => $v) {
					$sorted[] = $this->array[$k];
				}

				return $sorted;
			}
		}

		return $this->array;
	}

}
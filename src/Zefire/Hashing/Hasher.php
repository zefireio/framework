<?php

namespace Zefire\Hashing;

class Hasher
{
	/**
     * Hashes a value.
     *
     * @param  string $data
     * @param  string $algorithm
     * @return string
     */
	public function make($data, $algorithm = 'sha256')
	{
		return hash($algorithm, $data);
	}
	/**
     * Hashes a value with salt.
     *
     * @param  string $data
     * @param  string $salt
     * @param  string $algorithm
     * @return string
     */
	public function makeSalted($data, $salt, $algorithm = 'sha256')
	{
		return hash_hmac($algorithm, $data, $salt);
	}
}
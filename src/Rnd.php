<?php
/**
 * Rnd.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright pgaultier
 * @package vb
 */

namespace vb;

/**
 * Backport of vb 5/6 randomizer
 *
 *$vbRandomizer = new Rnd();
 *
 * $initial = 1000;
 * // init randomizer
 * $vbRandomizer->rnd(-1);
 * $vbRandomizer->randomize($initial);
 * // get a random number
 * $rndValue = $vbRandomizer->rnd();
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright pgaultier
 * @package vb
 */
class Rnd
{
    /**
     * @var int
     */
    private $val;

    /**
     * Rnd constructor.
     * @param int $seed 0x50000 is default MS VB seed
     */
    public function __construct($seed = 0x50000)
    {
        $this->val = $seed;
    }

    /**
     * Generate a random number
     *
     * @param float $number
     * @return float
     */
    public function rnd($number = 1.0)
    {
        $a = 0x43fd43fd;
        $c = 0xc39ec3;
        if ($number != 0.0) {
            if ($number < 0.0) {
                $bytes = substr(pack("d", $number), 4); // Obtient les 4 octets de poids fort
                $num = unpack("V", $bytes)[1]; // Convertit les 4 octets en un entier non signé
                $this->val = ($num + ($num >> 24)) & 0xffffff;
            }
            $this->val = (($this->val * $a) + $c) & 0xffffff;
        }
        $p = pow(2, 24);
        $p = 16777216;
        return $this->val / $p;
    }

    /**
     * Randomize the seed
     *
     * @param float $number
     * @param bool $reset
     */
    public function randomize($number,$reset=false)
    {
        if($reset) {
            $this->val = 0x50000;
        }
        $bytes = substr(pack("d", $number), 4); // Obtient les 4 octets de poids fort
        $num = unpack("V", $bytes)[1]; // Convertit les 4 octets en un entier non signé

        $num = (($num & 0xffff) ^ ($num >> 16));
        $this->val = ($this->val & 0xff0000ff) | ($num << 8);
    }
}


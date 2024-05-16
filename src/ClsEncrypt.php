<?php
/**
 * ClsEncrypt.php
 *
 * PHP version 8.0+
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright pgaultier
 * @package vb
 */

namespace vb;

/**
 * Backport of vb 5/6 31bit cypher
 * Original VB code created by Michael Ciurescu (CVMichael from vbforums.com)
 * https://www.vbforums.com/showthread.php?231798-VB-31-Bit-Encryption-function
 *
 * @author Philippe Gaultier <pgaultier@gmail.com>
 * @copyright pgaultier
 * @package vb
 */
class ClsEncrypt
{

    /**
     * Encrypt/Decrypt a string with a password (level 2)
     * base64 encoding, hex encoding are not handled in the method
     *
     * @param string $strValue string to encrypt/decrypt
     * @param string $password password used
     * @param int $seedPasses number of seed passes (default 3)
     * @param int $dataPasses number of data passes (default 2)
     * @return string
     */
    public function RndCryptLevel2($strValue, $password, $seedPasses = 3, $dataPasses = 2) {
        $passwordAscii = mb_convert_encoding($password, 'ISO-8859-1', 'UTF-8');
        $passwordBytes = unpack('C*', $passwordAscii);
        $passwordBytes = array_values($passwordBytes);
        $passwordCount = count($passwordBytes);

        $strValueBytes = unpack('C*', $strValue);
        $strValueBytes = array_values($strValueBytes);
        $strValueCount = count($strValueBytes) - 1;

        $strOutputBytes = array_fill(0, $strValueCount + 1, 0);

        $vbRandomizer = new Rnd();

        $rounds = $strValueCount + 1 + ($passwordCount * $seedPasses);
        // init randomizer
        $vbRandomizer->rnd(-1);
        $vbRandomizer->randomize($rounds);

        $initialSeed = 0;
        $nbRounds = $rounds;
        for ($k = 1; $k <= $nbRounds; $k++) {
            $byte = $passwordBytes[($k % $passwordCount)];
            $exp = 1 + 2.7526486955 * $vbRandomizer->rnd();
            $rounds = (int)round(pow($byte, $exp));
            $initialSeed = (($initialSeed & 0x3FFFFFFF) + $rounds) | (int)round(1000 * $vbRandomizer->rnd());
        }

        $vbRandomizer->rnd(-1);
        $vbRandomizer->randomize($initialSeed);

        for ($k=0; $k<=$strValueCount;$k++) {
            $xor1 = (int)round(255.49 * $vbRandomizer->rnd());
            $xor2 = $strValueBytes[$k];
            $xor3 = $initialSeed & 0xFF;
            $strOutputBytes[$k] = $xor1 ^ $xor2 ^ $xor3;
            $base = (255 * $vbRandomizer->rnd());
            $exp = 1 + 2.7526486955 * $vbRandomizer->rnd();
            $rounds = (int)round(pow($base, $exp));
            $initialSeed = (int)(intdiv($initialSeed, 2) + $rounds);
        }

        for ($q = 1; $q < $dataPasses; $q++) {
            for ($k = 0; $k <= $strValueCount; $k++) {
                $xor1 = (int)round(255.49 * $vbRandomizer->rnd());
                $xor2 = $strOutputBytes[$k];
                $xor3 = ($initialSeed & 0xFF);
                $strOutputBytes[$k] = $xor1 ^ $xor2 ^ $xor3;
                $base = (255 * $vbRandomizer->rnd());
                $exp = 1 + 2.7526486955 * $vbRandomizer->rnd();
                $p = pow($base, $exp);
                $rounds = (int)round($p);
                $initialSeed = (int)(intdiv($initialSeed, 2) + $rounds);
            }
        }
        $output = pack("C*", ...$strOutputBytes);
        $output = mb_convert_encoding($output, 'UTF-8', 'ISO-8859-1');
        return $output;
    }

    /**
     * Encrypt/Decrypt a string with a password
     *
     * @param string $strValue string to encrypt/decrypt
     * @param string $password password used
     * @return string
     */
    public function RndCrypt($strValue, $password)
    {
        $passwordAscii = mb_convert_encoding($password, 'ISO-8859-1', 'UTF-8');
        $passwordBytes = unpack('C*', $passwordAscii);
        $passwordBytes = array_values($passwordBytes);
        $passwordCount = count($passwordBytes);

        $strValueBytes = unpack('C*', $strValue);
        $strValueBytes = array_values($strValueBytes);
        $strValueCount = count($strValueBytes) - 1;

        $vbRandomizer = new Rnd();
        $vbRandomizer->rnd(-1);
        $vbRandomizer->randomize($passwordCount);

        $sk = 0;
        for($i = 0; $i < $passwordCount; $i++) {
            $rnd = $vbRandomizer->rnd();
            $xor1 = $i % 256;
            $xor2 = $passwordBytes[$i];
            $xor3 = (int)round(256 * $rnd);
            $sk += (($xor1 ^ $xor2) ^ $xor3);
        }

        $vbRandomizer->rnd(-1);
        $vbRandomizer->randomize($sk);

        $strOutputBytes = array_fill(0, $strValueCount + 1, 0);
        for($i = 0; $i <= $strValueCount; $i++) {
            $rnd = $vbRandomizer->rnd();
            $xor1 = (int)round(256 * $rnd);
            $strOutputBytes[$i] = $xor1 ^ $strValueBytes[$i];
        }
        $output = pack("C*", ...$strOutputBytes);
        $output = mb_convert_encoding($output, 'UTF-8', 'ISO-8859-1');
        return $output;
    }


}
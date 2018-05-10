<?php

namespace Minicup\Misc;


class ColorUtils
{
    /**
     * Converts an RGB point into HSV
     *
     * @param int $r 0-255
     * @param int $g 0-255
     * @param int $b 0-255
     * @return array
     */
    public static function rgbToHsv(int $r, int $g, int $b): array
    {
        $rPrime = $r / 255;
        $gPrime = $g / 255;
        $bPrime = $b / 255;

        $max = max([$rPrime, $gPrime, $bPrime]);
        $min = min([$rPrime, $gPrime, $bPrime]);

        $delta = $max - $min;

        // Calculate H
        if ($delta == 0) {
            $h = 0;
        } else {
            if ($max === $rPrime) {
                $h = 60 * ((($gPrime - $bPrime) / $delta) % 6);
            } else if ($max === $gPrime) {
                $h = 60 * ((($bPrime - $rPrime) / $delta) + 2);
            } else if ($max === $bPrime) {
                $h = 60 * ((($rPrime - $gPrime) / $delta) + 4);
            } else {
                $h = 0;
            }
        }

        // Calculate S
        if ($max == 0) {
            $s = 0;
        } else {
            $s = $delta / $max;
        }

        // Calculate V
        $v = $max;

        return [$h, (int)($s * 100), (int)($v * 100)];
    }

    public static function hsvToRgb(int $iH, int $iS, int $iV): array
    {
        if ($iH < 0) $iH = 0;   // Hue:
        if ($iH > 360) $iH = 360; //   0-360
        if ($iS < 0) $iS = 0;   // Saturation:
        if ($iS > 100) $iS = 100; //   0-100
        if ($iV < 0) $iV = 0;   // Lightness:
        if ($iV > 100) $iV = 100; //   0-100
        $dS = $iS / 100.0; // Saturation: 0.0-1.0
        $dV = $iV / 100.0; // Lightness:  0.0-1.0
        $dC = $dV * $dS;   // Chroma:     0.0-1.0
        $dH = $iH / 60.0;  // H-Prime:    0.0-6.0
        $dT = $dH;       // Temp variable
        while ($dT >= 2.0) $dT -= 2.0; // php modulus does not work with float
        $dX = $dC * (1 - abs($dT - 1));     // as used in the Wikipedia link
        switch (floor($dH)) {
            case 0:
                $dR = $dC;
                $dG = $dX;
                $dB = 0.0;
                break;
            case 1:
                $dR = $dX;
                $dG = $dC;
                $dB = 0.0;
                break;
            case 2:
                $dR = 0.0;
                $dG = $dC;
                $dB = $dX;
                break;
            case 3:
                $dR = 0.0;
                $dG = $dX;
                $dB = $dC;
                break;
            case 4:
                $dR = $dX;
                $dG = 0.0;
                $dB = $dC;
                break;
            case 5:
                $dR = $dC;
                $dG = 0.0;
                $dB = $dX;
                break;
            default:
                $dR = 0.0;
                $dG = 0.0;
                $dB = 0.0;
                break;
        }
        $dM = $dV - $dC;
        $dR += $dM;
        $dG += $dM;
        $dB += $dM;
        $dR *= 255;
        $dG *= 255;
        $dB *= 255;
        return [round($dR), round($dG), round($dB)];
    }
}
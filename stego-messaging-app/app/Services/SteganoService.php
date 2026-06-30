<?php

namespace App\Services;

/**
 * SteganoService — Advanced Multi-Channel RGB Cycling LSB
 */
class SteganoService
{
    private const CH_RED   = 0;
    private const CH_GREEN = 1;
    private const CH_BLUE  = 2;

    /**
     * Embeds $message into the image and writes a lossless PNG.
     */
    public function embed(string $imagePath, string $message, string $outputPath): string
    {
        $info = getimagesize($imagePath);
        if (!$info) {
            throw new \Exception("Invalid image file at: {$imagePath}");
        }

        switch ($info['mime']) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($imagePath);
                break;

            case 'image/png':
                $src = imagecreatefrompng($imagePath);
                break;

            default:
                throw new \Exception("Unsupported image type: {$info['mime']}");
        }

        $width  = imagesx($src);
        $height = imagesy($src);

        // ── FIX: THE UNCONDITIONAL CANVAS PURGE ──────────────────────────────────
        // We ALWAYS copy the pixels onto a completely fresh, brand-new canvas.
        // This strips away embedded PNG Gamma (gAMA) and color profile (iCCP) chunks 
        // that cause GD to alter LSB values during the file save stage.
        $cleanCanvas = imagecreatetruecolor($width, $height);
        imagealphablending($cleanCanvas, false);
        imagesavealpha($cleanCanvas, true);
        imagecopy($cleanCanvas, $src, 0, 0, 0, 0, $width, $height);
        imagedestroy($src);
        $src = $cleanCanvas;
        // ────────────────────────────────────────────────────────────────────────

        $binaryMessage = $this->textToBinary($message . '|END|');
        $messageLength = strlen($binaryMessage);

        if ($messageLength > ($width * $height)) {
            imagedestroy($src);
            throw new \Exception("Image is too small to carry this payload.");
        }

        $bitIndex = 0;
        for ($y = 0; $y < $height && $bitIndex < $messageLength; $y++) {
            for ($x = 0; $x < $width && $bitIndex < $messageLength; $x++) {

                $rgb = imagecolorat($src, $x, $y);

                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >>  8) & 0xFF;
                $b =  $rgb        & 0xFF;
                $a = ($rgb >> 24) & 0x7F;

                $bit = (int) $binaryMessage[$bitIndex];

                switch ($bitIndex % 3) {
                    case self::CH_RED:
                        $r = ($r & 0xFE) | $bit;
                        break;
                    case self::CH_GREEN:
                        $g = ($g & 0xFE) | $bit;
                        break;
                    case self::CH_BLUE:
                        $b = ($b & 0xFE) | $bit;
                        break;
                }

                // Write raw color integer directly to bypass internal color allocation checks
                $newColor = ($a << 24) | ($r << 16) | ($g << 8) | $b;
                imagesetpixel($src, $x, $y, $newColor);

                $bitIndex++;
            }
        }

        imagepng($src, $outputPath, 9); // Compression 0 ensures uncompressed bitstreams
        imagedestroy($src);

        return $outputPath;
    }

    /**
     * Extracts a hidden message using symmetric traversal.
     */
    public function extract(string $imagePath): ?string
    {
        if (!file_exists($imagePath)) {
            return null;
        }

        $img    = imagecreatefrompng($imagePath);
        $width  = imagesx($img);
        $height = imagesy($img);

        // Force a fresh canvas layout for precise symmetric extraction matches
        $cleanCanvas = imagecreatetruecolor($width, $height);
        imagealphablending($cleanCanvas, false);
        imagesavealpha($cleanCanvas, true);
        imagecopy($cleanCanvas, $img, 0, 0, 0, 0, $width, $height);
        imagedestroy($img);
        $img = $cleanCanvas;

        $binaryMessage = '';
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {

                $rgb = imagecolorat($img, $x, $y);

                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >>  8) & 0xFF;
                $b =  $rgb        & 0xFF;

                switch (strlen($binaryMessage) % 3) {
                    case self::CH_RED:
                        $binaryMessage .= ($r & 1);
                        break;
                    case self::CH_GREEN:
                        $binaryMessage .= ($g & 1);
                        break;
                    case self::CH_BLUE:
                        $binaryMessage .= ($b & 1);
                        break;
                }

                if (strlen($binaryMessage) % 8 === 0) {
                    if (strlen($binaryMessage) > 40000) {
                        break 2; // Defensive safety brake
                    }

                    $currentText = $this->binaryToText($binaryMessage);
                    if (str_contains($currentText, '|END|')) {
                        imagedestroy($img);
                        return explode('|END|', $currentText)[0];
                    }
                }
            }
        }

        imagedestroy($img);
        return null;
    }

    private function textToBinary(string $text): string
    {
        $binary = '';
        foreach (str_split($text) as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        return $binary;
    }

    private function binaryToText(string $binary): string
    {
        $text = '';
        foreach (str_split($binary, 8) as $chunk) {
            $text .= chr(bindec($chunk));
        }
        return $text;
    }
}
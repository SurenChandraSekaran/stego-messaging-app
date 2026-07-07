<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\SteganoService;

class SteganoServiceTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_embeds_and_extracts_text_payload_losslessly_using_lsb()
    {
        $stegoService = new SteganoService();

        // Dynamically build a physically real temporary PNG file
        $imagePath = tempnam(sys_get_temp_dir(), 'cover_') . '.png';
        $img = imagecreatetruecolor(50, 50);
        imagepng($img, $imagePath);
        imagedestroy($img);

        $outputPath = tempnam(sys_get_temp_dir(), 'stego_out_') . '.png';
        $secretPayload = "Secret_FYP_Data_2026";

        // Execute embedding logic
        $processedImagePath = $stegoService->embed($imagePath, $secretPayload, $outputPath);
        $extractedPayload = $stegoService->extract($processedImagePath);

        // Verify exact bit match
        $this->assertEquals($secretPayload, $extractedPayload);

        // Clean up memory and file space
        if (file_exists($imagePath)) @unlink($imagePath);
        if (file_exists($outputPath)) @unlink($outputPath);
    }
}
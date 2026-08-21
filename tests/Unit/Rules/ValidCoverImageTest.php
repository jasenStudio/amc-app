<?php

namespace Tests\Unit\Rules;

use App\Rules\ValidCoverImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidCoverImageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        App::setLocale('en');
    }

    public function test_accepts_image_within_ratio_and_size_bounds(): void
    {
        $file = $this->makeUploadedPng(1600, 900); // ratio 1.777 (16:9)

        $errors = $this->validate($file);

        $this->assertEmpty($errors);
    }

    public function test_accepts_image_at_lower_ratio_bound(): void
    {
        $file = $this->makeUploadedPng(1600, 1000); // ratio 1.6

        $errors = $this->validate($file);

        $this->assertEmpty($errors);
    }

    public function test_accepts_image_at_upper_ratio_bound(): void
    {
        $file = $this->makeUploadedPng(2100, 1000); // ratio 2.1

        $errors = $this->validate($file);

        $this->assertEmpty($errors);
    }

    public function test_rejects_image_below_minimum_width(): void
    {
        $file = $this->makeUploadedPng(800, 450); // ratio 16:9 but width < 1200

        $errors = $this->validate($file);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('width', $errors[0]);
    }

    public function test_rejects_image_below_minimum_height(): void
    {
        $file = $this->makeUploadedPng(1200, 400); // width ok, height < 675

        $errors = $this->validate($file);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('height', $errors[0]);
    }

    public function test_rejects_image_with_ratio_too_low(): void
    {
        $file = $this->makeUploadedPng(1600, 1200); // ratio 1.33 (4:3)

        $errors = $this->validate($file);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('aspect ratio', $errors[0]);
    }

    public function test_rejects_image_with_ratio_too_high(): void
    {
        $file = $this->makeUploadedPng(2400, 800); // ratio 3.0

        $errors = $this->validate($file);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('aspect ratio', $errors[0]);
    }

    public function test_rejects_non_file_value(): void
    {
        $errors = $this->validate('not-a-file');

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('valid file', $errors[0]);
    }

    public function test_rejects_invalid_image_data(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'bad_');
        file_put_contents($tmp, 'not-an-image');

        $file = new UploadedFile($tmp, 'bad.png', 'image/png', null, true);

        $errors = $this->validate($file);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('dimensions', $errors[0]);

        @unlink($tmp);
    }

    public function test_custom_constraints_are_applied(): void
    {
        $rule = new ValidCoverImage(
            minWidth: 500,
            minHeight: 300,
            minRatio: 1.0,
            maxRatio: 2.0,
        );

        $file = $this->makeUploadedPng(600, 400); // ratio 1.5, within custom bounds

        $errors = $this->validate($file, $rule);

        $this->assertEmpty($errors);
    }

    private function validate(mixed $file, ?ValidCoverImage $rule = null): array
    {
        $rule ??= new ValidCoverImage;

        $validator = Validator::make(
            ['upload' => $file],
            ['upload' => [$rule]],
        );

        return $validator->errors()->get('upload');
    }

    private function makeUploadedPng(int $width, int $height): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'png_').'.png';
        $im = imagecreatetruecolor($width, $height);
        imagefill($im, 0, 0, imagecolorallocate($im, 100, 100, 100));
        imagepng($im, $path);
        imagedestroy($im);

        return new UploadedFile($path, 'cover.png', 'image/png', null, true);
    }
}

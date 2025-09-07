<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHelper
{
  public static function compressAndStore($image, $folder = 'blogs', $quality = 50): string
  {
    $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
    $imagePath = "$folder/$imageName";

    \Log::info('Compressing image...', ['imageName' => $imageName]);

    $manager = new ImageManager(new Driver());

    try {
      $imageInstance = $manager->read($image->getRealPath());

      $encodedImage = $imageInstance
        ->toJpeg(quality: $quality)
        ->toString();

      Storage::disk('public')->put($imagePath, $encodedImage);

      \Log::info('Image stored successfully.', ['path' => $imagePath]);

      return $imagePath;
    } catch (\Throwable $e) {
      \Log::error('Image compression/storage failed:', [
        'error' => $e->getMessage(),
      ]);
      return '';
    }
  }
}

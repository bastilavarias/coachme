<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ImageStorageService
{
    public function create($payload)
    {
        $image = $payload['image'] ?? null;
        if (isset($image)) {
            $user = User::findOrFail($payload['user_id']);
            $path = Storage::disk('public')->putFile('images', $image);
            $size = $image->getSize();
            $user->image()->create([
                'name' => substr(
                    $path,
                    strpos($path, 'images/') + strlen('images/')
                ),
                'original_name' => $image->getClientOriginalName(),
                'extension' => '.' . $image->getClientOriginalExtension(),
                'size' => $size,
                'path' => $path,
            ]);
        }
    }

    public function replace($payload)
    {
        $image = $payload['image'] ?? null;
        info($image);
        if (isset($image)) {
            $user = User::findOrFail($payload['user_id']);
            if (!empty($user->image)) {
                $image = $user
                    ->image()
                    ->latest()
                    ->first();
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
            $this->create($payload);
        }
    }
}

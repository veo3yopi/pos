<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('media:move-public {--delete}', function () {
    $targetDisk = 'public';
    $items = Media::query()
        ->where('disk', '!=', $targetDisk)
        ->orWhereNull('disk')
        ->get();

    if ($items->isEmpty()) {
        $this->info('No media records to move.');
        return;
    }

    foreach ($items as $media) {
        $fromDisk = $media->disk ?: config('media-library.disk_name', $targetDisk);
        if ($fromDisk === $targetDisk) {
            continue;
        }

        $paths = [$media->getPathRelativeToRoot()];
        $conversions = $media->getGeneratedConversions();
        $conversionNames = $conversions instanceof \Illuminate\Support\Collection
            ? $conversions->keys()->all()
            : array_keys($conversions ?? []);
        foreach ($conversionNames as $conversionName) {
            $paths[] = $media->getPathRelativeToRoot($conversionName);
        }

        foreach ($paths as $path) {
            if (! Storage::disk($fromDisk)->exists($path)) {
                continue;
            }

            if (! Storage::disk($targetDisk)->exists($path)) {
                Storage::disk($targetDisk)->put($path, Storage::disk($fromDisk)->get($path));
            }

            if ($this->option('delete')) {
                Storage::disk($fromDisk)->delete($path);
            }
        }

        $media->disk = $targetDisk;
        $media->conversions_disk = $targetDisk;
        $media->save();
    }

    $this->info('Media moved to public disk.');
})->purpose('Copy media files to public disk and update records');

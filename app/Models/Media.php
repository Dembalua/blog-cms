<?php

namespace App\Models;

use Awcodes\Curator\Glide\GlideBuilder;
use Awcodes\Curator\Models\Media as CuratorMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Media extends CuratorMedia
{
    public function ogUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->hasCuration('og')) {
                    $curation = $this->getCuration('og');

                    return Storage::disk($curation['disk'])->url($curation['path']);
                }

                return $this->url;
            }
        );
    }

    public function cardUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->hasCuration('card')) {
                    $curation = $this->getCuration('card');

                    return Storage::disk($curation['disk'])->url($curation['path']);
                }

                return $this->medium_url;
            }
        );
    }

    public function heroUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->hasCuration('hero')) {
                    $curation = $this->getCuration('hero');

                    return Storage::disk($curation['disk'])->url($curation['path']);
                }

                return GlideBuilder::make()->width(1920)->height(480)->format('webp')->fit('crop')->toUrl($this->path);
            }
        );
    }

    public function homepageHeroUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->hasCuration('homepage_hero')) {
                    $curation = $this->getCuration('homepage_hero');

                    return Storage::disk($curation['disk'])->url($curation['path']);
                }

                return GlideBuilder::make()->width(1100)->height(700)->format('webp')->fit('crop')->toUrl($this->path);
            }
        );
    }
}

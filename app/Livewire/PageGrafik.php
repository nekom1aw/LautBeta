<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class PageGrafik extends Component
{
    public array $items = [];

    public function mount(): void
    {
        $loc = app()->getLocale();

        $rows = DB::table('literacy')
            ->select([
                'id',
                'title_en',
                'title_id',
                'description_en',
                'description_id',
                'slug',
                'tanggal_publikasi',
                'image',
                'image_id',
                'image_en',
                'publikasi',
                'status',
            ])
            ->where('type', 'grafik')
            ->where('publikasi', 'publish')
            ->where('status', 'on')
            ->orderByDesc('tanggal_publikasi')
            ->orderByDesc('id')
            ->get();

        $this->items = $rows->map(function ($r) use ($loc) {
            $title = $loc === 'id' ? ($r->title_id ?: $r->title_en) : ($r->title_en ?: $r->title_id);
            $desc  = $loc === 'id' ? ($r->description_id ?: $r->description_en) : ($r->description_en ?: $r->description_id);
            $img   = $loc === 'id' ? ($r->image_id ?: $r->image) : ($r->image_en ?: $r->image);

            return [
                'id' => $r->id,
                'slug' => $r->slug,
                'title' => $title,
                'description' => $desc,
                'tanggal_publikasi' => $r->tanggal_publikasi,
                'image_url' => !empty($img) ? Storage::url($img) : null,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.page-grafik', ['items' => $this->items]);
    }
}

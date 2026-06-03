<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class PageLiteracyDetail extends Component
{
    public array $item = [];

    public function mount(): void
    {
        $id = (int) request()->route('id');
        $type = (string) request()->route('type');
        $locale = app()->getLocale();

        if (!in_array($type, ['grafik', 'journal'], true)) {
            abort(404);
        }

        $row = DB::table('literacy')
            ->select([
                'id',
                'title_id',
                'title_en',
                'description_id',
                'description_en',
                'content_id',
                'content_en',
                'image',
                'image_id',
                'image_en',
                'tanggal_publikasi',
                'slug',
                'type',
                'publikasi',
                'status',
            ])
            ->where('id', $id)
            ->where('type', $type)
            ->where('publikasi', 'publish')
            ->where('status', 'on')
            ->first();

        if (!$row) {
            abort(404);
        }

        $title = $locale === 'id'
            ? ($row->title_id ?? $row->title_en)
            : ($row->title_en ?? $row->title_id);

        $desc = $locale === 'id'
            ? ($row->description_id ?? $row->description_en)
            : ($row->description_en ?? $row->description_id);

        $cont = $locale === 'id'
            ? ($row->content_id ?? $row->content_en)
            : ($row->content_en ?? $row->content_id);

        $imgPath = $locale === 'id'
            ? ($row->image_id ?: $row->image)
            : ($row->image_en ?: $row->image);

        $canonicalSlug = $row->slug ?: Str::slug((string) $title);

        $this->item = [
            'id' => $row->id,
            'slug' => $canonicalSlug,
            'title' => $title ?: 'Untitled',
            'description' => (string) $desc,
            'content' => (string) $cont,
            'image_url' => $imgPath ? Storage::url($imgPath) : asset('img/placeholder-16x9.png'),
            'tanggal' => $row->tanggal_publikasi,
            'type' => $row->type,
        ];
    }

    public function render()
    {
        return view('livewire.page-literacy-detail');
    }
}

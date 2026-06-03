<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PreviewLiteracy extends Component
{
    public array $item = [];

    public function mount(): void
    {
        $id = (int) request()->route('id');

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
                'publikasi',
                'status',
                'type',
            ])
            ->where('id', $id)
            ->first();

        if (!$row) {
            abort(404);
        }

        $locale = app()->getLocale();

        $title = $locale === 'id'
            ? ($row->title_id ?: $row->title_en)
            : ($row->title_en ?: $row->title_id);

        $description = $locale === 'id'
            ? ($row->description_id ?: $row->description_en)
            : ($row->description_en ?: $row->description_id);

        $content = $locale === 'id'
            ? ($row->content_id ?: $row->content_en)
            : ($row->content_en ?: $row->content_id);

        $imgPath = $locale === 'id'
            ? ($row->image_id ?: $row->image)
            : ($row->image_en ?: $row->image);

        $this->item = [
            'id'                => $row->id,
            'title'             => $title,
            'description'       => $description,
            'content'           => $content,
            'image_url'         => $imgPath ? Storage::url($imgPath) : null,
            'tanggal_publikasi' => $row->tanggal_publikasi,
            'tahun'             => $row->tanggal_publikasi
                ? Carbon::parse($row->tanggal_publikasi)->format('Y')
                : null,
            'publikasi'         => $row->publikasi,
            'type'              => $row->type,
        ];
    }

    public function render()
    {
        return view('livewire.cms.preview-literacy');
    }
}

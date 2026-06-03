<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Str;

class AddLiteracy extends Component
{
    use WithFileUploads;

    private const TABLE = 'literacy';

    public string $lang = 'en';
    public string $type = 'grafik';

    public string $title_en = '';
    public string $title_id = '';
    public string $description_en = '';
    public string $description_id = '';
    public string $content_en = '';
    public string $content_id = '';
    public ?string $tanggal_publikasi = null;
    public string $publikasi = 'draft';
    public string $status = 'on';

    public $image_id = null;
    public $image_en = null;
    public ?string $imagePreviewId = null;
    public ?string $imagePreviewEn = null;

    protected function rules(): array
    {
        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_id' => ['required', 'string', 'max:255'],
            'description_en' => ['required', 'string'],
            'description_id' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'content_id' => ['required', 'string'],
            'type' => ['required', 'in:journal,grafik'],
            'tanggal_publikasi' => ['required', 'date'],
            'publikasi' => ['required', 'in:draft,publish'],
            'image_id' => ['required', File::image()->max(5 * 1024)],
            'image_en' => ['required', File::image()->max(5 * 1024)],
        ];
    }

    public function save()
    {
        $this->validate();

        $slug = Str::slug($this->title_id);
        if ($slug === '') {
            $slug = 'literacy-' . now()->timestamp;
        }

        $imageIdPath = $this->image_id->store("literacy/{$this->type}/id", 'public');
        $imageEnPath = $this->image_en->store("literacy/{$this->type}/en", 'public');

        DB::table(self::TABLE)->insert([
            'title_en' => $this->title_en,
            'title_id' => $this->title_id,
            'description_en' => $this->description_en,
            'description_id' => $this->description_id,
            'content_en' => $this->content_en,
            'content_id' => $this->content_id,
            'image' => $imageIdPath,
            'image_id' => $imageIdPath,
            'image_en' => $imageEnPath,
            'tanggal_publikasi' => $this->tanggal_publikasi,
            'publikasi' => $this->publikasi,
            'status' => $this->status,
            'type' => $this->type,
            'slug' => $slug,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->imagePreviewId = Storage::url($imageIdPath);
        $this->imagePreviewEn = Storage::url($imageEnPath);

        return redirect()
            ->route('cms.page.index.literacy', ['locale' => app()->getLocale()])
            ->with('success', 'Literacy berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.cms.add-literacy');
    }
}

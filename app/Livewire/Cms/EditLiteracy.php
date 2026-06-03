<?php

namespace App\Livewire\Cms;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class EditLiteracy extends Component
{
    use WithFileUploads;

    public int|string $id = 0;
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

    public function mount($id): void
    {
        $this->id = (int) $id;

        $data = DB::table('literacy')->find($this->id);
        if (!$data) {
            abort(404);
        }

        $this->type = $data->type;
        $this->title_en = $data->title_en ?? '';
        $this->title_id = $data->title_id ?? '';
        $this->description_en = $data->description_en ?? '';
        $this->description_id = $data->description_id ?? '';
        $this->content_en = $data->content_en ?? '';
        $this->content_id = $data->content_id ?? '';
        $this->tanggal_publikasi = $data->tanggal_publikasi;
        $this->publikasi = $data->publikasi;
        $this->status = $data->status;

        $this->imagePreviewId = !empty($data->image_id) ? Storage::url($data->image_id) : ($data->image ? Storage::url($data->image) : null);
        $this->imagePreviewEn = !empty($data->image_en) ? Storage::url($data->image_en) : ($data->image ? Storage::url($data->image) : null);
    }

    protected function rules(): array
    {
        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_id' => ['required', 'string', 'max:255'],
            'description_en' => ['required', 'string'],
            'description_id' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'content_id' => ['required', 'string'],
            'tanggal_publikasi' => ['required', 'date'],
            'publikasi' => ['required', 'in:draft,publish'],
            'status' => ['required', 'in:on,off'],
            'image_id' => ['nullable', File::image()->max(5 * 1024)],
            'image_en' => ['nullable', File::image()->max(5 * 1024)],
        ];
    }

    public function update(): void
    {
        $this->validate();

        $current = DB::table('literacy')->find((int) $this->id);
        if (!$current) {
            abort(404);
        }

        $data = [
            'title_en' => $this->title_en,
            'title_id' => $this->title_id,
            'description_en' => $this->description_en,
            'description_id' => $this->description_id,
            'content_en' => $this->content_en,
            'content_id' => $this->content_id,
            'tanggal_publikasi' => $this->tanggal_publikasi,
            'publikasi' => $this->publikasi,
            'status' => $this->status,
            'updated_at' => now(),
        ];

        if ($this->image_id) {
            $newPath = $this->image_id->store("literacy/{$this->type}/id", 'public');
            $data['image_id'] = $newPath;
            $data['image'] = $newPath;
            $this->imagePreviewId = Storage::url($newPath);
        }

        if ($this->image_en) {
            $newPath = $this->image_en->store("literacy/{$this->type}/en", 'public');
            $data['image_en'] = $newPath;
            $this->imagePreviewEn = Storage::url($newPath);
        }

        DB::table('literacy')->where('id', (int) $this->id)->update($data);

        session()->flash('success', 'Literacy berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.cms.edit-literacy');
    }
}

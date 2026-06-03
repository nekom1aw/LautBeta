<?php

namespace App\Livewire\Cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditInfografik extends Component
{
    use WithFileUploads;

    public int $id;

    public string $title_id = '';
    public string $title_en = '';
    public string $description_id = '';
    public string $description_en = '';
    public string $publikasi = 'draft';
    public ?string $tanggal_publikasi = null;

    public array $existingImages = [];
    public array $removeImageIds = [];
    public array $newImages = [];

    public function mount($id): void
    {
        $this->id = (int) $id;

        $row = DB::table('infografik')->where('id', $this->id)->first();
        if (!$row) {
            abort(404);
        }

        $this->title_id = (string) ($row->title_id ?? '');
        $this->title_en = (string) ($row->title_en ?? '');
        $this->description_id = (string) ($row->description_id ?? '');
        $this->description_en = (string) ($row->description_en ?? '');
        $this->publikasi = (string) ($row->publikasi ?? 'draft');
        $this->tanggal_publikasi = $row->tanggal_publikasi ? (string) $row->tanggal_publikasi : null;

        $this->loadExistingImages();
    }

    private function loadExistingImages(): void
    {
        $this->existingImages = DB::table('infografik_images')
            ->where('infografik_id', $this->id)
            ->orderBy('sort')
            ->orderBy('id')
            ->get(['id', 'image', 'sort'])
            ->map(fn($img) => [
                'id' => (int) $img->id,
                'image' => $img->image,
                'url' => asset('storage/' . ltrim($img->image, '/')),
                'sort' => (int) $img->sort,
            ])
            ->toArray();
    }

    public function markRemoveExisting(int $imageId): void
    {
        if (!in_array($imageId, $this->removeImageIds, true)) {
            $this->removeImageIds[] = $imageId;
        }

        $this->existingImages = array_values(array_filter(
            $this->existingImages,
            fn($img) => (int) $img['id'] !== $imageId
        ));
    }

    public function removeNewImage(int $index): void
    {
        if (isset($this->newImages[$index])) {
            array_splice($this->newImages, $index, 1);
        }
    }

    public function save()
    {
        $this->validate([
            'title_id' => 'required|max:255',
            'newImages.*' => 'image|max:2048',
            'publikasi' => 'required|in:draft,publish',
            'tanggal_publikasi' => 'nullable|date',
        ]);

        DB::table('infografik')->where('id', $this->id)->update([
            'title_id' => $this->title_id,
            'title_en' => $this->title_en,
            'description_id' => $this->description_id,
            'description_en' => $this->description_en,
            'slug' => Str::slug($this->title_id ?: Str::random(8)),
            'publikasi' => $this->publikasi,
            'tanggal_publikasi' => $this->tanggal_publikasi,
            'updated_at' => now(),
        ]);

        if (!empty($this->removeImageIds)) {
            $toDelete = DB::table('infografik_images')
                ->where('infografik_id', $this->id)
                ->whereIn('id', $this->removeImageIds)
                ->get(['id', 'image']);

            foreach ($toDelete as $img) {
                if ($img->image && Storage::disk('public')->exists($img->image)) {
                    Storage::disk('public')->delete($img->image);
                }
            }

            DB::table('infografik_images')
                ->where('infografik_id', $this->id)
                ->whereIn('id', $this->removeImageIds)
                ->delete();
        }

        $nextSort = (int) DB::table('infografik_images')
            ->where('infografik_id', $this->id)
            ->max('sort');
        $nextSort = $nextSort >= 0 ? $nextSort + 1 : 0;

        foreach ($this->newImages as $img) {
            $path = $img->store('infografik/gallery', 'public');

            DB::table('infografik_images')->insert([
                'infografik_id' => $this->id,
                'image' => $path,
                'sort' => $nextSort,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $nextSort++;
        }

        session()->flash('success', 'Infografik berhasil diperbarui!');

        return redirect()->route('cms.page.index.infografik', [
            'locale' => app()->getLocale(),
        ]);
    }

    public function render()
    {
        return view('livewire.cms.edit-infografik');
    }
}

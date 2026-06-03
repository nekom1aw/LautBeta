<div class="max-w-4xl mx-auto p-6 space-y-6 bg-white shadow-md" x-data="{ lang: 'id' }">
    <div class="flex items-center justify-between">
        <div class="flex gap-2">
            <button @click="lang='id'" :class="lang=='id' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-3 py-1 rounded">Indonesia</button>
            <button @click="lang='en'" :class="lang=='en' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-3 py-1 rounded">English</button>
        </div>
        <a href="{{ route('cms.page.index.infografik', ['locale' => app()->getLocale()]) }}" class="text-sm text-slate-600 hover:underline">Kembali</a>
    </div>

    @if (session('success'))
    <div class="p-3 rounded bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif

    <div>
        <label class="font-semibold">Judul</label>
        <input type="text" wire:model="title_id" x-show="lang=='id'" class="w-full border p-2">
        <input type="text" wire:model="title_en" x-show="lang=='en'" class="w-full border p-2">
        @error('title_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div wire:ignore x-show="lang=='id'"
        x-init="
            if (tinymce.get('desc_editor_id_edit')) tinymce.get('desc_editor_id_edit').remove();
            tinymce.init({
                selector:'#desc_editor_id_edit',
                height:300,
                base_url:'/tinymce',
                suffix:'.min',
                license_key:'gpl',
                menubar:false,
                plugins:'advlist anchor autolink link lists image table code preview',
                toolbar:'undo redo | bold italic underline | bullist numlist | alignleft aligncenter alignright',
                setup:(ed)=>{
                    ed.on('init',()=> ed.setContent(@this.get('description_id') || ''));
                    ed.on('change keyup undo redo',()=>{@this.set('description_id', ed.getContent())});
                }
            });
        ">
        <textarea id="desc_editor_id_edit"></textarea>
    </div>

    <div wire:ignore x-show="lang=='en'"
        x-init="
            if (tinymce.get('desc_editor_en_edit')) tinymce.get('desc_editor_en_edit').remove();
            tinymce.init({
                selector:'#desc_editor_en_edit',
                height:300,
                base_url:'/tinymce',
                suffix:'.min',
                license_key:'gpl',
                menubar:false,
                plugins:'advlist anchor autolink link lists image table code preview',
                toolbar:'undo redo | bold italic underline | bullist numlist | alignleft aligncenter alignright',
                setup:(ed)=>{
                    ed.on('init',()=> ed.setContent(@this.get('description_en') || ''));
                    ed.on('change keyup undo redo',()=>{@this.set('description_en', ed.getContent())});
                }
            });
        ">
        <textarea id="desc_editor_en_edit"></textarea>
    </div>

    <div class="space-y-3">
        <p class="font-semibold">Gambar Saat Ini</p>
        <div class="flex flex-wrap gap-3">
            @forelse($existingImages as $img)
            <div class="relative w-32 aspect-[4/5] bg-gray-200 overflow-hidden rounded">
                <img src="{{ $img['url'] }}" class="w-full h-full object-cover">
                <button type="button"
                    wire:click="markRemoveExisting({{ $img['id'] }})"
                    class="absolute top-1 right-1 bg-red-600 text-white text-xs px-1 rounded">X</button>
            </div>
            @empty
            <p class="text-sm text-slate-500">Tidak ada gambar.</p>
            @endforelse
        </div>
    </div>

    <div class="space-y-2">
        <p class="font-semibold">Tambah Gambar Baru</p>
        <input type="file" wire:model="newImages" multiple class="border p-2 w-72 mb-1">
        @error('newImages.*') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

        @if(!empty($newImages))
        <div class="flex flex-wrap gap-3 mt-2">
            @foreach($newImages as $i => $img)
            <div class="relative w-32 aspect-[4/5] bg-gray-200 overflow-hidden rounded">
                <img src="{{ $img->temporaryUrl() }}" class="w-full h-full object-cover">
                <button type="button"
                    wire:click="removeNewImage({{ $i }})"
                    class="absolute top-1 right-1 bg-red-600 text-white text-xs px-1 rounded">X</button>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div>
        <label class="font-semibold">Status</label>
        <select wire:model="publikasi" class="border p-2 w-full">
            <option value="draft">Draft</option>
            <option value="publish">Publish</option>
        </select>
    </div>

    <div>
        <label class="font-semibold">Tanggal Publikasi</label>
        <input type="date" wire:model="tanggal_publikasi" class="border p-2 w-full">
    </div>

    <button wire:click="save" class="px-5 py-2.5 bg-blue-600 text-white shadow hover:bg-blue-700 rounded">
        Update
    </button>
</div>

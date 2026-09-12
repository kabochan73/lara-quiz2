@props(['categories', 'selected' => null])

{{-- 問題編集フォームで使うセクション選択プルダウン(セクションは必須)。
     カテゴリをoptgroupにして、その下に属するセクションを並べる。 --}}
<select id="section_id" name="section_id" required>
    @foreach ($categories as $category)
        <optgroup label="{{ $category->name }}">
            @foreach ($category->sections as $section)
                <option value="{{ $section->id }}" @selected((string) $selected === (string) $section->id)>
                    {{ $section->name }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>

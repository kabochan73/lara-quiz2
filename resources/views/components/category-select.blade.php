@props(['categories', 'selected' => null])

{{-- 問題作成・編集フォームで共通して使うカテゴリ選択プルダウン。
     親カテゴリをoptgroupにして、その下に子カテゴリを並べる。
     親カテゴリ自体も選択肢に含める(子カテゴリを持たない/大分類だけで十分な場合もあるため)。 --}}
<select id="category_id" name="category_id">
    <option value="">-- 未分類 --</option>
    @foreach ($categories as $parent)
        <optgroup label="{{ $parent->name }}">
            <option value="{{ $parent->id }}" @selected((string) $selected === (string) $parent->id)>
                {{ $parent->name }}(親カテゴリ自体)
            </option>
            @foreach ($parent->children as $child)
                <option value="{{ $child->id }}" @selected((string) $selected === (string) $child->id)>
                    　{{ $child->name }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>

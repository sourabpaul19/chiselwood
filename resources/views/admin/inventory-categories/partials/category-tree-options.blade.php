@foreach($categories as $cat)

    {{-- Skip self --}}
    @if(isset($exclude) && $exclude == $cat->id)
        @continue
    @endif

    <option value="{{ $cat->id }}"
        @selected($selected == $cat->id)>
        {{ str_repeat('— ', $level) }} {{ $cat->name }}
    </option>

    {{-- Children --}}
    @if($cat->childrenRecursive->count())
        @include('admin.inventory-categories.partials.category-tree-options', [
            'categories' => $cat->childrenRecursive,
            'selected'   => $selected,
            'exclude'    => $exclude,
            'level'      => $level + 1
        ])
    @endif

@endforeach

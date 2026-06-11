<div class="category-section">

    <button
        class="category-chip active"
        data-category=""
    >
        All
    </button>

    @foreach($categories as $category)

        <button
            class="category-chip"
            data-category="{{ $category->id }}"
        >
            {{ $category->name }}
        </button>

    @endforeach

</div>

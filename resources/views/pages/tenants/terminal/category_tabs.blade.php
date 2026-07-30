<div class="category-section  p-3 pmt-2 pb-0">

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
